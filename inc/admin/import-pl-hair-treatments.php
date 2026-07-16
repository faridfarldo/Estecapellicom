<?php
/**
 * Polish importer for the Hair Transplant treatments.
 *
 * The English treatment seed remains the source of truth for ACF structure
 * and media. Version-controlled Polish overlays replace only visitor-facing
 * copy, while WPML links each page to its published English source.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_PL_HAIR_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_PL_HAIR_IMPORT_VERSION', '2026-07-16.2' );
}

/**
 * Polish Hair Transplant import manifest.
 *
 * @return array<string,string> English source slug => canonical Polish slug.
 */
function estecapelli_pl_hair_manifest() {
	return array(
		'exosome-fue-hair-transplant'  => 'przeszczep-wlosow-exosome-fue',
		'female-hair-transplant'       => 'przeszczep-wlosow-u-kobiet',
		'hair-mesotherapy'             => 'mezoterapia-wlosow',
		'sapphire-fue-hair-transplant' => 'przeszczep-wlosow-metoda-sapphire-fue',
		'dhi-hair-transplant'          => 'przeszczep-wlosow-dhi',
		'vita-treatment'               => 'leczenie-vita',
		'eyebrow-transplant'           => 'przeszczep-brwi',
		'beard-transplant'             => 'przeszczep-brody',
	);
}

/**
 * Load and validate every Polish translation overlay in the manifest.
 *
 * @return array<int,array<string,mixed>>|WP_Error
 */
function estecapelli_pl_hair_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/pl/hair-transplant';
	$indexed   = function_exists( 'estecapelli_indexed_treatment_slugs' ) ? estecapelli_indexed_treatment_slugs() : array();
	$loaded    = array();

	foreach ( estecapelli_pl_hair_manifest() as $source_slug => $polish_slug ) {
		if ( ( $indexed[ $source_slug ]['pl'] ?? '' ) !== $polish_slug ) {
			return new WP_Error( 'pl_hair_indexed_slug_mismatch', sprintf( 'The Polish slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'pl_hair_missing_file', sprintf( 'Missing Polish translation file: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'pl_hair_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}

		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$polish_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'pl_hair_invalid_translation', sprintf( 'Incomplete or mismatched Polish translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_it_hair_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}

		$coverage = estecapelli_it_hair_validate_coverage( $seed['sections'], $translation['sections'], 'page_sections', 'Polish' );
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

/**
 * Import all eight Polish Hair Transplant treatment pages.
 *
 * @return array<string,int>|WP_Error Source slug => Polish post ID.
 */
function estecapelli_run_pl_hair_import() {
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'pl_hair_acf_missing', 'ACF is required for the Polish Hair Transplant import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'pl_hair_wpml_missing', 'WPML is required for the Polish Hair Transplant import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['pl'] ) ) {
		return new WP_Error( 'pl_hair_polish_inactive', 'Polish must be active in WPML before importing the treatment translations.' );
	}

	$translations = estecapelli_pl_hair_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$polish_term_id = estecapelli_it_hair_category(
		array(
			'source_slug'          => 'hair-transplant',
			'target_slug'          => 'przeszczep-wlosow',
			'target_name'          => 'Przeszczep włosów',
			'target_language'      => 'pl',
			'target_language_name' => 'Polish',
			'label'                => 'Hair Transplant',
		)
	);
	if ( is_wp_error( $polish_term_id ) ) {
		return $polish_term_id;
	}

	$imported = array();
	foreach ( $translations as $translation ) {
		$result = estecapelli_it_hair_import_one(
			$translation,
			$polish_term_id,
			'estecapelli_it_hair_source_seed',
			array(
				'language_code' => 'pl',
				'language_name' => 'Polish',
			)
		);
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

add_action( 'admin_init', 'estecapelli_maybe_import_pl_hair_treatments', 83 );
/**
 * Run once after deployment; failed runs remain retryable.
 */
function estecapelli_maybe_import_pl_hair_treatments() {
	if (
		get_option( 'estecapelli_pl_hair_import_version' ) === ESTECAPELLI_PL_HAIR_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_pl_hair_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_pl_hair_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_pl_hair_import_version', ESTECAPELLI_PL_HAIR_IMPORT_VERSION, false );
	delete_option( 'estecapelli_pl_hair_import_error' );
	set_transient( 'estecapelli_pl_hair_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_pl_hair_import_notice' );
/**
 * Show the automatic importer result to administrators.
 */
function estecapelli_pl_hair_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_pl_hair_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_pl_hair_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'Polish Hair Transplant translations imported successfully: %d treatments.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_pl_hair_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'Polish Hair Transplant import could not finish.' ),
			esc_html( $error )
		);
	}
}
