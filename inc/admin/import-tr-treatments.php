<?php
/**
 * Turkish importer for Hair Transplant and Plastic Surgery treatments.
 *
 * Each request imports or repairs one treatment so large ACF flexible-content
 * payloads stay below WPML/ACFML execution limits. English owns structure and
 * media; Turkish JSON overlays replace every visitor-facing text field.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_TR_TREATMENTS_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_TR_TREATMENTS_IMPORT_VERSION', '2026-07-17.1' );
}

/** Import configuration and exact indexed slugs for both Turkish groups. */
function estecapelli_tr_treatment_groups() {
	return array(
		'hair-transplant' => array(
			'label'           => 'Hair Transplant',
			'directory'       => 'hair-transplant',
			'source_seed'     => 'estecapelli_it_hair_source_seed',
			'source_category' => 'hair-transplant',
			'target_category' => 'sac-ekimi',
			'target_name'     => 'Saç Ekimi',
			'manifest'        => array(
				'exosome-fue-hair-transplant'  => 'exosome-fue-sac-ekimi',
				'female-hair-transplant'       => 'kadin-sac-ekimi',
				'hair-mesotherapy'             => 'sac-mezoterapisi',
				'sapphire-fue-hair-transplant' => 'safir-fue-sac-ekimi',
				'dhi-hair-transplant'          => 'dhi-sac-ekimi',
				'vita-treatment'               => 'vita-tedavisi',
				'eyebrow-transplant'           => 'kas-ekimi',
				'beard-transplant'             => 'sakal-ekimi',
			),
		),
		'plastic-surgery' => array(
			'label'           => 'Plastic Surgery',
			'directory'       => 'plastic-surgery',
			'source_seed'     => 'estecapelli_it_plastic_source_seed',
			'source_category' => 'plastic-surgery',
			'target_category' => 'estetik-cerrahi',
			'target_name'     => 'Estetik Cerrahi',
			'manifest'        => array(
				'rhinoplasty'                                             => 'burun-estetigi',
				'breast-aesthetics-breast-surgery'                        => 'meme-estetigi-gogus-estetigi',
				'bbl'                                                     => 'bbl',
				'liposuction'                                             => 'liposuction',
				'face-and-neck-lift-surgery'                              => 'yuz-ve-boyun-germe-ameliyati',
				'abdominoplasty-tummy-tuck'                               => 'karin-germe-ameliyati',
				'gynecomastia'                                            => 'jinekomasti',
				'obesity-surgeries-bariatric-surgery-and-gastric-balloon' => 'obezite-ameliyatlari-bariatrik-cerrahi-ve-mide-balonu',
			),
		),
	);
}

/** Load and strictly validate every overlay in one Turkish treatment group. */
function estecapelli_tr_load_treatment_translations( $group_key ) {
	$groups = estecapelli_tr_treatment_groups();
	if ( ! isset( $groups[ $group_key ] ) ) {
		return new WP_Error( 'tr_treatment_invalid_group', sprintf( 'Unknown Turkish treatment group: %s.', $group_key ) );
	}

	$group     = $groups[ $group_key ];
	$directory = get_template_directory() . '/inc/data/translations/tr/' . $group['directory'];
	$indexed   = estecapelli_indexed_treatment_slugs();
	$loaded    = array();

	foreach ( $group['manifest'] as $source_slug => $turkish_slug ) {
		if ( ( $indexed[ $source_slug ]['tr'] ?? '' ) !== $turkish_slug ) {
			return new WP_Error( 'tr_treatment_indexed_slug_mismatch', sprintf( 'The Turkish slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'tr_treatment_missing_file', sprintf( 'Missing Turkish translation file: %s.', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'tr_treatment_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$turkish_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'tr_treatment_invalid_translation', sprintf( 'Incomplete or mismatched Turkish translation: %s.', basename( $file ) ) );
		}

		$seed = call_user_func( $group['source_seed'], $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		$coverage = estecapelli_it_hair_validate_coverage( $seed['sections'], $translation['sections'], 'page_sections', 'Turkish' );
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

/** Import or repair one Turkish treatment. */
function estecapelli_run_tr_treatment_import( $group_key, $source_slug ) {
	$groups = estecapelli_tr_treatment_groups();
	if ( ! isset( $groups[ $group_key ] ) || ! isset( $groups[ $group_key ]['manifest'][ $source_slug ] ) ) {
		return new WP_Error( 'tr_treatment_invalid_source', sprintf( 'Unknown Turkish treatment source: %s/%s.', $group_key, $source_slug ) );
	}
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'tr_treatment_acf_missing', 'ACF is required for the Turkish treatment import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'tr_treatment_wpml_missing', 'WPML is required for the Turkish treatment import.' );
	}

	$languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $languages ) || ! isset( $languages['tr'] ) ) {
		return new WP_Error( 'tr_treatment_turkish_inactive', 'Turkish (tr) must be active in WPML before importing treatment translations.' );
	}

	$translations = estecapelli_tr_load_treatment_translations( $group_key );
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$group   = $groups[ $group_key ];
	$term_id = estecapelli_it_hair_category(
		array(
			'source_slug'          => $group['source_category'],
			'target_slug'          => $group['target_category'],
			'target_name'          => $group['target_name'],
			'target_language'      => 'tr',
			'target_language_name' => 'Turkish',
			'label'                => $group['label'],
		)
	);
	if ( is_wp_error( $term_id ) ) {
		return $term_id;
	}

	$result = estecapelli_it_hair_import_one(
		$translations[ $source_slug ],
		$term_id,
		$group['source_seed'],
		array(
			'language_code' => 'tr',
			'language_name' => 'Turkish',
		)
	);
	if ( is_wp_error( $result ) ) {
		return new WP_Error( $result->get_error_code(), sprintf( '%s: %s', $source_slug, $result->get_error_message() ) );
	}

	return (int) $result;
}

add_action( 'admin_menu', 'estecapelli_register_tr_treatment_importer' );
/** Register the combined Turkish treatment importer. */
function estecapelli_register_tr_treatment_importer() {
	add_management_page(
		__( 'Turkish Treatment Importer', 'estecapelli' ),
		__( 'Turkish Treatments', 'estecapelli' ),
		'manage_options',
		'estecapelli-turkish-treatment-importer',
		'estecapelli_render_tr_treatment_importer'
	);
}

add_action( 'admin_post_estecapelli_import_tr_treatment', 'estecapelli_handle_tr_treatment_import' );
/** Process one Turkish treatment and return to the importer. */
function estecapelli_handle_tr_treatment_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import content.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_import_tr_treatment' );

	$group_key   = isset( $_POST['group'] ) ? sanitize_key( wp_unslash( $_POST['group'] ) ) : '';
	$source_slug = isset( $_POST['source'] ) ? sanitize_title( wp_unslash( $_POST['source'] ) ) : '';
	$result      = estecapelli_run_tr_treatment_import( $group_key, $source_slug );
	if ( is_wp_error( $result ) ) {
		set_transient( 'estecapelli_tr_treatment_import_error', $group_key . '/' . $source_slug . ': ' . $result->get_error_message(), 5 * MINUTE_IN_SECONDS );
	} else {
		set_transient( 'estecapelli_tr_treatment_import_success', $group_key . '/' . $source_slug, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-turkish-treatment-importer', admin_url( 'tools.php' ) ) );
	exit;
}

/** Render one Hair Transplant or Plastic Surgery table. */
function estecapelli_render_tr_treatment_group( $group_key, array $group ) {
	?>
	<h2><?php echo esc_html( $group['label'] ); ?></h2>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="estecapelli_import_tr_treatment">
		<input type="hidden" name="group" value="<?php echo esc_attr( $group_key ); ?>">
		<?php wp_nonce_field( 'estecapelli_import_tr_treatment' ); ?>
		<table class="widefat striped" style="max-width:1080px;margin:0 0 2rem;">
			<thead><tr><th><?php esc_html_e( 'English source', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Turkish slug', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Status', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th></tr></thead>
			<tbody>
				<?php foreach ( $group['manifest'] as $source_slug => $turkish_slug ) :
					$source_id = estecapelli_source_post_id( $source_slug, 'treatment' );
					$linked_id = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, 'tr' ) : 0;
					$candidate = $source_id ? estecapelli_it_hair_adoptable_post_id( $turkish_slug, $source_id, 'tr' ) : 0;
					$target_id = ( $linked_id && $linked_id !== $source_id ) ? $linked_id : $candidate;
					?>
					<tr>
						<td><code><?php echo esc_html( $source_slug ); ?></code></td>
						<td><code><?php echo esc_html( $turkish_slug ); ?></code></td>
						<td>
							<?php if ( $linked_id && $linked_id !== $source_id ) : ?>
								<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
							<?php elseif ( $candidate ) : ?>
								<span style="color:#b26200;"><?php esc_html_e( 'Exists - link needs repair', 'estecapelli' ); ?></span>
							<?php else : ?>
								<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
							<?php endif; ?>
							<?php if ( $target_id ) : ?>
								— <a href="<?php echo esc_url( get_edit_post_link( $target_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
							<?php endif; ?>
						</td>
						<td><button type="submit" name="source" value="<?php echo esc_attr( $source_slug ); ?>" class="button button-primary"><?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?></button></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</form>
	<?php
}

/** Render the one-treatment-per-request Turkish importer screen. */
function estecapelli_render_tr_treatment_importer() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success = get_transient( 'estecapelli_tr_treatment_import_success' );
	$error   = get_transient( 'estecapelli_tr_treatment_import_error' );
	delete_transient( 'estecapelli_tr_treatment_import_success' );
	delete_transient( 'estecapelli_tr_treatment_import_error' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Turkish Treatment Importer', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Imports one complete Turkish treatment per request. Structure and media come from English; every visitor-facing text field is translated.', 'estecapelli' ); ?></p>
		<p class="description"><?php esc_html_e( 'Routes are validated against the indexed /tr/sac-ekimi/ and /tr/estetik-cerrahi/ URL contracts before every import.', 'estecapelli' ); ?></p>
		<?php if ( $success ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sprintf( 'Turkish treatment imported or repaired: %s.', $success ) ); ?></p></div>
		<?php elseif ( $error ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'Turkish import could not finish.', 'estecapelli' ); ?></strong> <?php echo esc_html( $error ); ?></p></div>
		<?php endif; ?>

		<?php foreach ( estecapelli_tr_treatment_groups() as $group_key => $group ) : ?>
			<?php estecapelli_render_tr_treatment_group( $group_key, $group ); ?>
		<?php endforeach; ?>
	</div>
	<?php
}
