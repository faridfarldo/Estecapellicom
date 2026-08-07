<?php
/**
 * French importer for the indexed doctor profiles.
 *
 * Text is stored in version-controlled JSON overlays. Portraits and resume
 * images stay attached to the English source and are copied only when the
 * French field is empty. WPML linking is repaired without touching profiles
 * in any other language.
 *
 * The per-profile write is the shared implementation every other language
 * already uses. French previously carried a private copy that predated two
 * fixes made there: it adopted whichever element squatted the French WPML slot
 * instead of the profile actually tagged French — so a Spanish profile in that
 * slot was served on French pages — and it aborted on a cross-language slug
 * collision rather than forcing the slug.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_FR_DOCTORS_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_FR_DOCTORS_IMPORT_VERSION', '2026-08-07.1' );
}

/** Indexed English doctor slug => exact indexed French slug. */
function estecapelli_fr_doctors_manifest() {
	return array(
		'mehmet-hanifi-kutlar'  => 'mehmet-hanifi-kutlar',
		'prof-dr-binnur-ustun'  => 'prof-dr-binnur-ustun',
		'op-dr-hasan-celik'     => 'op-dr-hasan-celik',
		'op-dr-mehmet-palali'   => 'op-dr-mehmet-palali',
		'op-dr-necdet-derici'   => 'op-dr-necdet-derici',
		'op-dr-ali-durmus'      => 'op-dr-ali-durmus',
	);
}

/** Load and validate every French doctor overlay. */
function estecapelli_fr_doctors_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/fr/doctors';
	$loaded    = array();

	foreach ( estecapelli_fr_doctors_manifest() as $source_slug => $french_slug ) {
		$route = estecapelli_indexed_route_path( estecapelli_it_doctor_route_key( $source_slug ), 'fr' );
		if ( ! $route || basename( $route ) !== $french_slug ) {
			return new WP_Error( 'fr_doctors_indexed_slug_mismatch', sprintf( 'The French doctor slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'fr_doctors_missing_file', sprintf( 'Missing French doctor translation: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'fr_doctors_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$french_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['name'] ) ||
			empty( $translation['position'] ) ||
			empty( $translation['bio'] ) ||
			empty( $translation['credentials'] ) ||
			! is_array( $translation['credentials'] )
		) {
			return new WP_Error( 'fr_doctors_invalid_translation', sprintf( 'Incomplete or mismatched French doctor translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_it_doctor_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		if ( count( $translation['credentials'] ) !== count( $seed['credentials'] ?? array() ) ) {
			return new WP_Error( 'fr_doctors_credentials_mismatch', sprintf( 'Credential count does not match the English source: %s.', $source_slug ) );
		}

		$loaded[ $source_slug ] = $translation;
	}

	return $loaded;
}

/**
 * Run the French doctor import.
 *
 * @param string $source_slug Import only this doctor; empty imports all.
 * @return array<string,int>|WP_Error
 */
function estecapelli_run_fr_doctors_import( $source_slug = '' ) {
	$source_slug = (string) $source_slug;
	if ( '' !== $source_slug && ! isset( estecapelli_fr_doctors_manifest()[ $source_slug ] ) ) {
		return new WP_Error( 'fr_doctors_invalid_source', sprintf( 'Unknown French doctor source: %s.', $source_slug ) );
	}

	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'fr_doctors_acf_missing', 'ACF is required for the French doctor import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'fr_doctors_wpml_missing', 'WPML is required for the French doctor import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['fr'] ) ) {
		return new WP_Error( 'fr_doctors_french_inactive', 'French must be active in WPML before importing doctor translations.' );
	}

	$translations = estecapelli_fr_doctors_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$imported = array();
	$failures = array();
	foreach ( $translations as $slug => $translation ) {
		if ( '' !== $source_slug && $source_slug !== $slug ) {
			continue;
		}

		// Keep going after a failure: stopping at the first one would leave every
		// later profile serving whichever language last occupied its WPML slot.
		$result = estecapelli_it_doctor_import_one( $translation, 'fr', 'French' );
		if ( is_wp_error( $result ) ) {
			$failures[] = sprintf( '%s: %s', $slug, $result->get_error_message() );
			continue;
		}
		$imported[ $slug ] = $result;
	}

	if ( $failures ) {
		return new WP_Error( 'fr_doctors_import_incomplete', implode( ' | ', $failures ) );
	}

	// An empty result is not a success: reporting one would hide a manifest whose
	// source slug never matched the requested import.
	if ( ! $imported ) {
		return new WP_Error(
			'fr_doctors_nothing_imported',
			sprintf( 'No French doctor overlay was applied for %s.', $source_slug ? $source_slug : 'any doctor' )
		);
	}

	return $imported;
}

add_action( 'admin_menu', 'estecapelli_register_fr_doctors_importer' );
/** Register the dedicated French doctor importer. */
function estecapelli_register_fr_doctors_importer() {
	add_management_page(
		__( 'French Doctors Importer', 'estecapelli' ),
		__( 'French Doctors', 'estecapelli' ),
		'manage_options',
		'estecapelli-french-doctors-importer',
		'estecapelli_render_fr_doctors_importer'
	);
}

add_action( 'admin_post_estecapelli_import_fr_doctor', 'estecapelli_handle_fr_doctor_import' );
/** Process one French doctor and return to the importer. */
function estecapelli_handle_fr_doctor_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import content.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_import_fr_doctor' );

	$source_slug = isset( $_POST['source'] ) ? sanitize_title( wp_unslash( $_POST['source'] ) ) : '';
	$result      = estecapelli_run_fr_doctors_import( $source_slug );
	if ( is_wp_error( $result ) ) {
		set_transient( 'estecapelli_fr_doctors_import_error', $source_slug . ': ' . $result->get_error_message(), 5 * MINUTE_IN_SECONDS );
	} else {
		set_transient( 'estecapelli_fr_doctors_import_success', $source_slug, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-french-doctors-importer', admin_url( 'tools.php' ) ) );
	exit;
}

/** Render the one-doctor-per-request importer screen. */
function estecapelli_render_fr_doctors_importer() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success = get_transient( 'estecapelli_fr_doctors_import_success' );
	$error   = get_transient( 'estecapelli_fr_doctors_import_error' );
	delete_transient( 'estecapelli_fr_doctors_import_success' );
	delete_transient( 'estecapelli_fr_doctors_import_error' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'French Doctors Importer', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Imports one French doctor profile per request. English owns the portrait and structure; the position, biography and credentials are translated.', 'estecapelli' ); ?></p>
		<?php if ( $success ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sprintf( 'French doctor imported or repaired: %s.', $success ) ); ?></p></div>
		<?php elseif ( $error ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'French doctor import could not finish.', 'estecapelli' ); ?></strong> <?php echo esc_html( $error ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="estecapelli_import_fr_doctor">
			<?php wp_nonce_field( 'estecapelli_import_fr_doctor' ); ?>
			<table class="widefat striped" style="max-width:980px;margin-top:1rem;">
				<thead><tr><th><?php esc_html_e( 'English source', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Status', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( estecapelli_fr_doctors_manifest() as $source_slug => $french_slug ) :
						$source_id = estecapelli_source_post_id( $source_slug, 'doctor' );
						$linked_id = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'doctor', false, 'fr' ) : 0;
						$exists    = ( $linked_id && $linked_id !== $source_id );
						?>
						<tr>
							<td><code><?php echo esc_html( $source_slug ); ?></code></td>
							<td>
								<?php if ( ! $source_id ) : ?>
									<span style="color:#b26200;"><?php esc_html_e( 'English profile missing — run the English doctor import first', 'estecapelli' ); ?></span>
								<?php elseif ( $exists ) : ?>
									<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
									— <a href="<?php echo esc_url( get_edit_post_link( $linked_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
								<?php else : ?>
									<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
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

add_action( 'admin_init', 'estecapelli_maybe_import_fr_doctors', 93 );
/** Run once after deployment; failed runs remain retryable. */
function estecapelli_maybe_import_fr_doctors() {
	if (
		get_option( 'estecapelli_fr_doctors_import_version' ) === ESTECAPELLI_FR_DOCTORS_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_fr_doctors_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_fr_doctors_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_fr_doctors_import_version', ESTECAPELLI_FR_DOCTORS_IMPORT_VERSION, false );
	delete_option( 'estecapelli_fr_doctors_import_error' );
	set_transient( 'estecapelli_fr_doctors_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_fr_doctors_import_notice' );
/** Show the French doctor-import result. */
function estecapelli_fr_doctors_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_fr_doctors_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_fr_doctors_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'French doctor translations imported successfully: %d profiles.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_fr_doctors_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'French doctor import could not finish.' ),
			esc_html( $error )
		);
	}
}
