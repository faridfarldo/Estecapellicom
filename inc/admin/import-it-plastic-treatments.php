<?php
/**
 * One-time Italian importer for the eight Plastic Surgery treatments.
 *
 * Version-controlled JSON overlays replace visitor-facing copy only. The
 * English seed remains the source of truth for ACF structure and media, while
 * WPML links every Italian record to its English original.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_IT_PLASTIC_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_IT_PLASTIC_IMPORT_VERSION', '2026-07-16.2' );
}

/** @return array<string,string> English source slug => indexed Italian slug. */
function estecapelli_it_plastic_manifest() {
	return array(
		'rhinoplasty'                                             => 'rinoplastica',
		'breast-aesthetics-breast-surgery'                        => 'estetica-del-seno-chirurgia-del-seno',
		'bbl'                                                      => 'bbl',
		'liposuction'                                              => 'liposuzione',
		'face-and-neck-lift-surgery'                              => 'chirurgia-di-lifting-del-viso-e-del-collo',
		'abdominoplasty-tummy-tuck'                               => 'addominoplastica',
		'gynecomastia'                                            => 'ginecomastia',
		'obesity-surgeries-bariatric-surgery-and-gastric-balloon' => 'chirurgie-dell-obesita-chirurgia-bariatrica-e-palloncino-gastrico',
	);
}

/**
 * Return one English seed record, restricted to Plastic Surgery.
 *
 * @param string $source_slug English treatment slug.
 * @return array<string,mixed>|WP_Error
 */
function estecapelli_it_plastic_source_seed( $source_slug ) {
	foreach ( estecapelli_treatments_seed() as $treatment ) {
		if ( $source_slug === ( $treatment['slug'] ?? '' ) ) {
			if ( 'Plastic Surgery' !== ( $treatment['category'] ?? '' ) ) {
				return new WP_Error( 'it_plastic_wrong_category', sprintf( '%s is not in the Plastic Surgery category.', $source_slug ) );
			}
			return $treatment;
		}
	}

	return new WP_Error( 'it_plastic_missing_source_seed', sprintf( 'English seed not found for %s.', $source_slug ) );
}

/**
 * Load and strictly validate all Italian Plastic Surgery overlays.
 *
 * @return array<int,array<string,mixed>>|WP_Error
 */
function estecapelli_it_plastic_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/it/plastic-surgery';
	$indexed   = function_exists( 'estecapelli_indexed_treatment_slugs' ) ? estecapelli_indexed_treatment_slugs() : array();
	$loaded    = array();

	foreach ( estecapelli_it_plastic_manifest() as $source_slug => $italian_slug ) {
		if ( ( $indexed[ $source_slug ]['it'] ?? '' ) !== $italian_slug ) {
			return new WP_Error( 'it_plastic_indexed_slug_mismatch', sprintf( 'The Italian slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'it_plastic_missing_file', sprintf( 'Missing Italian translation file: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'it_plastic_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}

		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$italian_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'it_plastic_invalid_translation', sprintf( 'Incomplete or mismatched Italian translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_it_plastic_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		$coverage = estecapelli_it_hair_validate_coverage( $seed['sections'], $translation['sections'] );
		if ( is_wp_error( $coverage ) ) {
			return new WP_Error( $coverage->get_error_code(), sprintf( '%s: %s', basename( $file ), $coverage->get_error_message() ) );
		}
		$structure = estecapelli_it_hair_overlay( $seed['sections'], $translation['sections'] );
		if ( is_wp_error( $structure ) ) {
			return new WP_Error( $structure->get_error_code(), sprintf( '%s: %s', basename( $file ), $structure->get_error_message() ) );
		}

		$loaded[] = $translation;
	}

	return $loaded;
}

/** Return the canonical Italian Plastic Surgery category. */
function estecapelli_it_plastic_category() {
	return estecapelli_it_hair_category(
		array(
			'source_slug' => 'plastic-surgery',
			'target_slug' => 'chirurgia-plastica',
			'target_name' => 'Chirurgia plastica',
			'label'       => 'Plastic Surgery',
		)
	);
}

/**
 * Run the Italian Plastic Surgery import.
 *
 * @param string $source_slug Import only this English source; empty imports all.
 * @return array<string,int>|WP_Error Source slug => Italian post ID.
 */
function estecapelli_run_it_plastic_import( $source_slug = '' ) {
	$source_slug = (string) $source_slug;
	if ( '' !== $source_slug && ! isset( estecapelli_it_plastic_manifest()[ $source_slug ] ) ) {
		return new WP_Error( 'it_plastic_invalid_source', sprintf( 'Unknown Italian Plastic Surgery source: %s.', $source_slug ) );
	}

	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'it_plastic_acf_missing', 'ACF is required for the Italian Plastic Surgery import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'it_plastic_wpml_missing', 'WPML is required for the Italian Plastic Surgery import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['it'] ) ) {
		return new WP_Error( 'it_plastic_italian_inactive', 'Italian must be active in WPML before importing the treatment translations.' );
	}

	$translations = estecapelli_it_plastic_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}
	$italian_term_id = estecapelli_it_plastic_category();
	if ( is_wp_error( $italian_term_id ) ) {
		return $italian_term_id;
	}

	$imported = array();
	foreach ( $translations as $translation ) {
		if ( '' !== $source_slug && $source_slug !== $translation['source_slug'] ) {
			continue;
		}

		$result = estecapelli_it_hair_import_one( $translation, $italian_term_id, 'estecapelli_it_plastic_source_seed' );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( $result->get_error_code(), sprintf( '%s: %s', $translation['source_slug'], $result->get_error_message() ) );
		}
		$imported[ $translation['source_slug'] ] = $result;
	}

	return $imported;
}

add_action( 'admin_init', 'estecapelli_maybe_import_it_plastic_treatments', 92 );
/** Run once after deployment. Failed runs remain retryable. */
function estecapelli_maybe_import_it_plastic_treatments() {
	if (
		get_option( 'estecapelli_it_plastic_import_version' ) === ESTECAPELLI_IT_PLASTIC_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_it_plastic_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_it_plastic_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_it_plastic_import_version', ESTECAPELLI_IT_PLASTIC_IMPORT_VERSION, false );
	delete_option( 'estecapelli_it_plastic_import_error' );
	set_transient( 'estecapelli_it_plastic_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_it_plastic_import_notice' );
/** Show an actionable import result to administrators. */
function estecapelli_it_plastic_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_it_plastic_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_it_plastic_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'Italian Plastic Surgery translations imported successfully: %d treatments.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_it_plastic_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'Italian Plastic Surgery import could not finish.' ),
			esc_html( $error )
		);
	}
}
