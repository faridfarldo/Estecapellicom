<?php
/**
 * One-time French importer for the Dental Treatment procedures.
 *
 * Version-controlled JSON overlays replace visitor-facing copy only. The
 * English seed remains the source of truth for ACF structure, media and
 * relationships, and WPML links each French record to its English original.
 * Mirrors the Plastic Surgery importer and reuses the shared hair-import engine.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_FR_DENTAL_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_FR_DENTAL_IMPORT_VERSION', '2026-07-16.1' );
}

/**
 * English source slug => exact Google-indexed French leaf slug.
 *
 * @return array<string,string>
 */
function estecapelli_fr_dental_manifest() {
	return array(
		'dental-implant'  => 'implant-dentaire',
		'hollywood-smile' => 'sourire-hollywoodien',
	);
}

/**
 * Return one English seed record, restricted to Dental Treatment.
 *
 * @param string $source_slug English treatment slug.
 * @return array<string,mixed>|WP_Error
 */
function estecapelli_fr_dental_source_seed( $source_slug ) {
	foreach ( estecapelli_treatments_seed() as $treatment ) {
		if ( $source_slug === ( $treatment['slug'] ?? '' ) ) {
			if ( 'Dental Treatment' !== ( $treatment['category'] ?? '' ) ) {
				return new WP_Error( 'fr_dental_wrong_category', sprintf( '%s is not in the Dental Treatment category.', $source_slug ) );
			}
			return $treatment;
		}
	}

	return new WP_Error( 'fr_dental_missing_source_seed', sprintf( 'English seed not found for %s.', $source_slug ) );
}

/**
 * Load and strictly validate all French Dental Treatment overlays.
 *
 * @return array<int,array<string,mixed>>|WP_Error
 */
function estecapelli_fr_dental_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/fr/dental-treatment';
	$indexed   = function_exists( 'estecapelli_indexed_treatment_slugs' ) ? estecapelli_indexed_treatment_slugs() : array();
	$loaded    = array();

	foreach ( estecapelli_fr_dental_manifest() as $source_slug => $french_slug ) {
		if ( ( $indexed[ $source_slug ]['fr'] ?? '' ) !== $french_slug ) {
			return new WP_Error( 'fr_dental_indexed_slug_mismatch', sprintf( 'The French slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'fr_dental_missing_file', sprintf( 'Missing French translation file: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'fr_dental_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}

		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$french_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'fr_dental_invalid_translation', sprintf( 'Incomplete or mismatched French translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_fr_dental_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		$coverage = estecapelli_fr_hair_validate_coverage( $seed['sections'], $translation['sections'] );
		if ( is_wp_error( $coverage ) ) {
			return new WP_Error( $coverage->get_error_code(), sprintf( '%s: %s', basename( $file ), $coverage->get_error_message() ) );
		}
		$structure = estecapelli_fr_hair_overlay( $seed['sections'], $translation['sections'] );
		if ( is_wp_error( $structure ) ) {
			return new WP_Error( $structure->get_error_code(), sprintf( '%s: %s', basename( $file ), $structure->get_error_message() ) );
		}

		$loaded[] = $translation;
	}

	return $loaded;
}

/** Return the canonical French Dental Treatment category. */
function estecapelli_fr_dental_category() {
	return estecapelli_fr_hair_category(
		array(
			'source_slug' => 'dental-treatment',
			'target_slug' => 'traitement-dentaire',
			'target_name' => 'Traitement dentaire',
			'label'       => 'Dental Treatment',
		)
	);
}

/**
 * Force-import one French Dental treatment from its validated overlay.
 *
 * @param string $source_slug English source slug.
 * @return int|WP_Error French post ID.
 */
function estecapelli_import_one_fr_dental_treatment( $source_slug ) {
	if ( ! isset( estecapelli_fr_dental_manifest()[ $source_slug ] ) ) {
		return new WP_Error( 'fr_dental_unknown_treatment', 'Unknown French Dental treatment.' );
	}

	$translations = estecapelli_fr_dental_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}
	$french_term_id = estecapelli_fr_dental_category();
	if ( is_wp_error( $french_term_id ) ) {
		return $french_term_id;
	}

	foreach ( $translations as $translation ) {
		if ( $source_slug === $translation['source_slug'] ) {
			return estecapelli_fr_hair_import_one( $translation, $french_term_id, 'estecapelli_fr_dental_source_seed', true );
		}
	}

	return new WP_Error( 'fr_dental_translation_missing', 'The requested French translation overlay was not found.' );
}

/**
 * Run the complete French Dental Treatment import.
 *
 * @return array<string,int>|WP_Error Source slug => French post ID.
 */
function estecapelli_run_fr_dental_import() {
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'fr_dental_acf_missing', 'ACF is required for the French Dental import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'fr_dental_wpml_missing', 'WPML is required for the French Dental import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['fr'] ) ) {
		return new WP_Error( 'fr_dental_french_inactive', 'French must be active in WPML before importing the treatment translations.' );
	}

	$translations = estecapelli_fr_dental_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$french_term_id = estecapelli_fr_dental_category();
	if ( is_wp_error( $french_term_id ) ) {
		return $french_term_id;
	}

	$imported = array();
	foreach ( $translations as $translation ) {
		$result = estecapelli_fr_hair_import_one( $translation, $french_term_id, 'estecapelli_fr_dental_source_seed', true );
		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				$result->get_error_code(),
				sprintf( '%s: %s', $translation['source_slug'], $result->get_error_message() )
			);
		}
		$imported[ $translation['source_slug'] ] = $result;
	}

	return $imported;
}

add_action( 'admin_post_estecapelli_import_fr_dental_treatment', 'estecapelli_handle_fr_dental_manual_import' );
/** Process an individual manual French Dental import button. */
function estecapelli_handle_fr_dental_manual_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import treatments.', 'estecapelli' ) );
	}

	$source_slug = isset( $_GET['source'] ) ? sanitize_title( wp_unslash( $_GET['source'] ) ) : '';
	check_admin_referer( 'estecapelli_import_fr_dental_' . $source_slug );
	$result = estecapelli_import_one_fr_dental_treatment( $source_slug );
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_fr_dental_import_error', $source_slug . ': ' . $result->get_error_message(), false );
	} else {
		$batch = estecapelli_run_fr_dental_import();
		if ( is_wp_error( $batch ) ) {
			update_option( 'estecapelli_fr_dental_import_error', $batch->get_error_message(), false );
		} else {
			update_option( 'estecapelli_fr_dental_import_version', ESTECAPELLI_FR_DENTAL_IMPORT_VERSION, false );
			delete_option( 'estecapelli_fr_dental_import_error' );
			set_transient( 'estecapelli_fr_dental_import_success', count( $batch ), 5 * MINUTE_IN_SECONDS );
		}
	}

	wp_safe_redirect(
		add_query_arg( 'page', 'estecapelli-treatment-importer', admin_url( 'tools.php' ) )
	);
	exit;
}

add_action( 'admin_init', 'estecapelli_maybe_import_fr_dental_treatments', 90 );
/**
 * Run once after deployment. Failed runs remain retryable.
 */
function estecapelli_maybe_import_fr_dental_treatments() {
	if (
		get_option( 'estecapelli_fr_dental_import_version' ) === ESTECAPELLI_FR_DENTAL_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_fr_dental_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_fr_dental_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_fr_dental_import_version', ESTECAPELLI_FR_DENTAL_IMPORT_VERSION, false );
	delete_option( 'estecapelli_fr_dental_import_error' );
	set_transient( 'estecapelli_fr_dental_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_fr_dental_import_notice' );
/**
 * Show an actionable import result to administrators.
 */
function estecapelli_fr_dental_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_fr_dental_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_fr_dental_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'French Dental translations imported successfully: %d treatments.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_fr_dental_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'French Dental import could not finish.' ),
			esc_html( $error )
		);
	}
}
