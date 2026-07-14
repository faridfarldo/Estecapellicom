<?php
/**
 * One-time French importer for the eight Plastic Surgery treatments.
 *
 * Version-controlled JSON overlays replace visitor-facing copy only. The
 * English seed remains the source of truth for ACF structure, media and
 * relationships, and WPML links each French record to its English original.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_FR_PLASTIC_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_FR_PLASTIC_IMPORT_VERSION', '2026-07-14.1' );
}

/**
 * English source slug => exact Google-indexed French leaf slug.
 *
 * @return array<string,string>
 */
function estecapelli_fr_plastic_manifest() {
	return array(
		'rhinoplasty'                                             => 'rhinoplastie',
		'breast-aesthetics-breast-surgery'                        => 'esthetique-mammaire-chirurgie-mammaire',
		'bbl'                                                      => 'bbl',
		'liposuction'                                              => 'liposuccion',
		'face-and-neck-lift-surgery'                              => 'chirurgie-de-lifting-du-visage-et-du-cou',
		'abdominoplasty-tummy-tuck'                               => 'abdominoplastie',
		'gynecomastia'                                            => 'gynecomastie',
		'obesity-surgeries-bariatric-surgery-and-gastric-balloon' => 'chirurgies-de-l-obesite-chirurgie-bariatrique-et-ballon-gastrique',
	);
}

/**
 * Return one English seed record, restricted to Plastic Surgery.
 *
 * @param string $source_slug English treatment slug.
 * @return array<string,mixed>|WP_Error
 */
function estecapelli_fr_plastic_source_seed( $source_slug ) {
	foreach ( estecapelli_treatments_seed() as $treatment ) {
		if ( $source_slug === ( $treatment['slug'] ?? '' ) ) {
			if ( 'Plastic Surgery' !== ( $treatment['category'] ?? '' ) ) {
				return new WP_Error( 'fr_plastic_wrong_category', sprintf( '%s is not in the Plastic Surgery category.', $source_slug ) );
			}
			return $treatment;
		}
	}

	return new WP_Error( 'fr_plastic_missing_source_seed', sprintf( 'English seed not found for %s.', $source_slug ) );
}

/**
 * Load and strictly validate all French Plastic Surgery overlays.
 *
 * @return array<int,array<string,mixed>>|WP_Error
 */
function estecapelli_fr_plastic_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/fr/plastic-surgery';
	$indexed   = function_exists( 'estecapelli_indexed_treatment_slugs' ) ? estecapelli_indexed_treatment_slugs() : array();
	$loaded    = array();

	foreach ( estecapelli_fr_plastic_manifest() as $source_slug => $french_slug ) {
		if ( ( $indexed[ $source_slug ]['fr'] ?? '' ) !== $french_slug ) {
			return new WP_Error( 'fr_plastic_indexed_slug_mismatch', sprintf( 'The French slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'fr_plastic_missing_file', sprintf( 'Missing French translation file: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'fr_plastic_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}

		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$french_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'fr_plastic_invalid_translation', sprintf( 'Incomplete or mismatched French translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_fr_plastic_source_seed( $source_slug );
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

/**
 * Run the complete French Plastic Surgery import.
 *
 * @return array<string,int>|WP_Error Source slug => French post ID.
 */
function estecapelli_run_fr_plastic_import() {
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'fr_plastic_acf_missing', 'ACF is required for the French Plastic Surgery import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'fr_plastic_wpml_missing', 'WPML is required for the French Plastic Surgery import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['fr'] ) ) {
		return new WP_Error( 'fr_plastic_french_inactive', 'French must be active in WPML before importing the treatment translations.' );
	}

	$translations = estecapelli_fr_plastic_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$french_term_id = estecapelli_fr_hair_category(
		array(
			'source_slug' => 'plastic-surgery',
			'target_slug' => 'chirurgie-plastique',
			'target_name' => 'Chirurgie plastique',
			'label'       => 'Plastic Surgery',
		)
	);
	if ( is_wp_error( $french_term_id ) ) {
		return $french_term_id;
	}

	$imported = array();
	foreach ( $translations as $translation ) {
		$result = estecapelli_fr_hair_import_one( $translation, $french_term_id, 'estecapelli_fr_plastic_source_seed' );
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

add_action( 'admin_init', 'estecapelli_maybe_import_fr_plastic_treatments', 90 );
/**
 * Run once after deployment. Failed runs remain retryable.
 */
function estecapelli_maybe_import_fr_plastic_treatments() {
	if (
		get_option( 'estecapelli_fr_plastic_import_version' ) === ESTECAPELLI_FR_PLASTIC_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_fr_plastic_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_fr_plastic_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_fr_plastic_import_version', ESTECAPELLI_FR_PLASTIC_IMPORT_VERSION, false );
	delete_option( 'estecapelli_fr_plastic_import_error' );
	set_transient( 'estecapelli_fr_plastic_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_fr_plastic_import_notice' );
/**
 * Show an actionable import result to administrators.
 */
function estecapelli_fr_plastic_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_fr_plastic_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_fr_plastic_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'French Plastic Surgery translations imported successfully: %d treatments.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_fr_plastic_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'French Plastic Surgery import could not finish.' ),
			esc_html( $error )
		);
	}
}
