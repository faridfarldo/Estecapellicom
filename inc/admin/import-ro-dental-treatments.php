<?php
/**
 * Romanian importer for the two Dental Treatment services.
 *
 * Imports one treatment per request to stay below ACFML execution limits.
 * English owns structure and media; Romanian overlays replace all visitor copy.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Romanian is stored in WPML under the same "ro" code its public routes use. */
function estecapelli_ro_dental_wpml_language() {
	return estecapelli_wpml_language_code( 'ro' );
}

if ( ! defined( 'ESTECAPELLI_RO_DENTAL_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_RO_DENTAL_IMPORT_VERSION', '2026-07-17.1' );
}

/** English source slug => exact indexed Romanian slug. */
function estecapelli_ro_dental_manifest() {
	return array(
		'dental-implant'  => 'implant-dentar',
		'hollywood-smile' => 'zambet-de-hollywood',
	);
}

/** Load and strictly validate every Romanian Dental Treatment overlay. */
function estecapelli_ro_dental_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/ro/dental-treatment';
	$indexed   = estecapelli_indexed_treatment_slugs();
	$loaded    = array();

	foreach ( estecapelli_ro_dental_manifest() as $source_slug => $romanian_slug ) {
		if ( ( $indexed[ $source_slug ]['ro'] ?? '' ) !== $romanian_slug ) {
			return new WP_Error( 'ro_dental_indexed_slug_mismatch', sprintf( 'The Romanian slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'ro_dental_missing_file', sprintf( 'Missing Romanian translation file: %s.', basename( $file ) ) );
		}
		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'ro_dental_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$romanian_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'ro_dental_invalid_translation', sprintf( 'Incomplete or mismatched Romanian translation: %s.', basename( $file ) ) );
		}

		$seed = estecapelli_it_dental_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		$coverage = estecapelli_it_hair_validate_coverage( $seed['sections'], $translation['sections'], 'page_sections', 'Romanian' );
		if ( is_wp_error( $coverage ) ) {
			return new WP_Error( $coverage->get_error_code(), sprintf( '%s: %s', basename( $file ), $coverage->get_error_message() ) );
		}
		$structure = estecapelli_it_hair_overlay( $seed['sections'], $translation['sections'] );
		if ( is_wp_error( $structure ) ) {
			return new WP_Error( $structure->get_error_code(), sprintf( '%s: %s', basename( $file ), $structure->get_error_message() ) );
		}

		$loaded[ $source_slug ] = $translation;
	}

	return $loaded;
}

/** Import or repair one Romanian Dental Treatment service. */
function estecapelli_run_ro_dental_import( $source_slug ) {
	$source_slug = (string) $source_slug;
	if ( ! isset( estecapelli_ro_dental_manifest()[ $source_slug ] ) ) {
		return new WP_Error( 'ro_dental_invalid_source', sprintf( 'Unknown Romanian Dental Treatment source: %s.', $source_slug ) );
	}
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'ro_dental_acf_missing', 'ACF is required for the Romanian Dental Treatment import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'ro_dental_wpml_missing', 'WPML is required for the Romanian Dental Treatment import.' );
	}

	$wpml_language = estecapelli_ro_dental_wpml_language();
	$languages     = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $languages ) || ! isset( $languages[ $wpml_language ] ) ) {
		return new WP_Error( 'ro_dental_romanian_inactive', sprintf( 'Romanian (%s) must be active in WPML before importing treatment translations.', $wpml_language ) );
	}

	$translations = estecapelli_ro_dental_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$romanian_term_id = estecapelli_it_hair_category(
		array(
			'source_slug'          => 'dental-treatment',
			'target_slug'          => 'tratament-dentar',
			'target_name'          => 'Tratament dentar',
			'target_language'      => $wpml_language,
			'target_language_name' => 'Romanian',
			'label'                => 'Dental Treatment',
		)
	);
	if ( is_wp_error( $romanian_term_id ) ) {
		return $romanian_term_id;
	}

	$result = estecapelli_it_hair_import_one(
		$translations[ $source_slug ],
		$romanian_term_id,
		'estecapelli_it_dental_source_seed',
		array(
			'language_code' => $wpml_language,
			'language_name' => 'Romanian',
		)
	);
	if ( is_wp_error( $result ) ) {
		return new WP_Error( $result->get_error_code(), sprintf( '%s: %s', $source_slug, $result->get_error_message() ) );
	}

	return (int) $result;
}

add_action( 'admin_menu', 'estecapelli_register_ro_dental_importer' );
/** Register the dedicated Romanian Dental Treatment importer. */
function estecapelli_register_ro_dental_importer() {
	add_management_page(
		__( 'Romanian Dental Treatment Importer', 'estecapelli' ),
		__( 'Romanian Dental Treatment', 'estecapelli' ),
		'manage_options',
		'estecapelli-romanian-dental-importer',
		'estecapelli_render_ro_dental_importer'
	);
}

add_action( 'admin_post_estecapelli_import_ro_dental', 'estecapelli_handle_ro_dental_import' );
/** Process one Romanian treatment and return to the importer. */
function estecapelli_handle_ro_dental_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import content.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_import_ro_dental' );

	$source_slug = isset( $_POST['source'] ) ? sanitize_title( wp_unslash( $_POST['source'] ) ) : '';
	$result      = estecapelli_run_ro_dental_import( $source_slug );
	if ( is_wp_error( $result ) ) {
		set_transient( 'estecapelli_ro_dental_import_error', $source_slug . ': ' . $result->get_error_message(), 5 * MINUTE_IN_SECONDS );
	} else {
		set_transient( 'estecapelli_ro_dental_import_success', $source_slug, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-romanian-dental-importer', admin_url( 'tools.php' ) ) );
	exit;
}

/** Render the one-treatment-per-request importer screen. */
function estecapelli_render_ro_dental_importer() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success       = get_transient( 'estecapelli_ro_dental_import_success' );
	$error         = get_transient( 'estecapelli_ro_dental_import_error' );
	$wpml_language = estecapelli_ro_dental_wpml_language();
	delete_transient( 'estecapelli_ro_dental_import_success' );
	delete_transient( 'estecapelli_ro_dental_import_error' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Romanian Dental Treatment Importer', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Imports one complete Romanian treatment per request. Structure and media come from English; every visitor-facing text field is translated.', 'estecapelli' ); ?></p>
		<p class="description"><?php echo esc_html( sprintf( 'Public routes use /ro/; this installation stores Romanian in WPML as "%s".', $wpml_language ) ); ?></p>
		<?php if ( $success ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sprintf( 'Romanian treatment imported or repaired: %s.', $success ) ); ?></p></div>
		<?php elseif ( $error ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'Romanian import could not finish.', 'estecapelli' ); ?></strong> <?php echo esc_html( $error ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="estecapelli_import_ro_dental">
			<?php wp_nonce_field( 'estecapelli_import_ro_dental' ); ?>
			<table class="widefat striped" style="max-width:980px;margin-top:1rem;">
				<thead><tr><th><?php esc_html_e( 'English source', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Romanian slug', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Status', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( estecapelli_ro_dental_manifest() as $source_slug => $romanian_slug ) :
						$source_id = estecapelli_source_post_id( $source_slug, 'treatment' );
						$linked_id = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, $wpml_language ) : 0;
						$candidate = $source_id ? estecapelli_it_hair_adoptable_post_id( $romanian_slug, $source_id, $wpml_language ) : 0;
						$target_id = ( $linked_id && $linked_id !== $source_id ) ? $linked_id : $candidate;
						?>
						<tr>
							<td><code><?php echo esc_html( $source_slug ); ?></code></td>
							<td><code><?php echo esc_html( $romanian_slug ); ?></code></td>
							<td>
								<?php if ( $linked_id && $linked_id !== $source_id ) : ?>
									<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
								<?php elseif ( $candidate ) : ?>
									<span style="color:#b26200;"><?php esc_html_e( 'Exists - link needs repair', 'estecapelli' ); ?></span>
								<?php else : ?>
									<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
								<?php endif; ?>
								<?php if ( $target_id ) : ?>
									&mdash; <a href="<?php echo esc_url( get_edit_post_link( $target_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
								<?php endif; ?>
							</td>
							<td><button type="submit" name="source" value="<?php echo esc_attr( $source_slug ); ?>" class="button button-primary"><?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?></button></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</form>
	</div>
	<?php
}
