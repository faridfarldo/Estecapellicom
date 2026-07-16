<?php
/**
 * Polish importer for the two Dental Treatment procedures.
 *
 * Each action imports one treatment only, avoiding the ACFML execution-time
 * failure caused by saving several flexible-content translations in one request.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** English source slug => exact live Polish leaf slug. */
function estecapelli_pl_dental_manifest() {
	return array(
		'dental-implant'  => 'implant-zebowy',
		'hollywood-smile' => 'hollywoodzki-usmiech',
	);
}

/** Return one English seed record, restricted to Dental Treatment. */
function estecapelli_pl_dental_source_seed( $source_slug ) {
	foreach ( estecapelli_treatments_seed() as $treatment ) {
		if ( $source_slug === ( $treatment['slug'] ?? '' ) ) {
			if ( 'Dental Treatment' !== ( $treatment['category'] ?? '' ) ) {
				return new WP_Error( 'pl_dental_wrong_category', sprintf( '%s is not in the Dental Treatment category.', $source_slug ) );
			}
			return $treatment;
		}
	}

	return new WP_Error( 'pl_dental_missing_source_seed', sprintf( 'English seed not found for %s.', $source_slug ) );
}

/** Load and strictly validate every Polish Dental Treatment overlay. */
function estecapelli_pl_dental_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/pl/dental-treatment';
	$indexed   = estecapelli_indexed_treatment_slugs();
	$loaded    = array();

	foreach ( estecapelli_pl_dental_manifest() as $source_slug => $polish_slug ) {
		if ( ( $indexed[ $source_slug ]['pl'] ?? '' ) !== $polish_slug ) {
			return new WP_Error( 'pl_dental_indexed_slug_mismatch', sprintf( 'The Polish slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'pl_dental_missing_file', sprintf( 'Missing Polish Dental Treatment translation: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'pl_dental_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$polish_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'pl_dental_invalid_translation', sprintf( 'Incomplete or mismatched Polish Dental Treatment translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_pl_dental_source_seed( $source_slug );
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

		$loaded[ $source_slug ] = $translation;
	}

	return $loaded;
}

/** Import or repair one Polish Dental Treatment procedure. */
function estecapelli_run_pl_dental_import( $source_slug ) {
	$source_slug = (string) $source_slug;
	if ( ! isset( estecapelli_pl_dental_manifest()[ $source_slug ] ) ) {
		return new WP_Error( 'pl_dental_invalid_source', sprintf( 'Unknown Polish Dental Treatment source: %s.', $source_slug ) );
	}
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'pl_dental_acf_missing', 'ACF is required for the Polish Dental Treatment import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'pl_dental_wpml_missing', 'WPML is required for the Polish Dental Treatment import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['pl'] ) ) {
		return new WP_Error( 'pl_dental_polish_inactive', 'Polish must be active in WPML before importing the treatment translations.' );
	}

	$translations = estecapelli_pl_dental_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$polish_term_id = estecapelli_it_hair_category(
		array(
			'source_slug'          => 'dental-treatment',
			'target_slug'          => 'leczenie-stomatologiczne',
			'target_name'          => 'Leczenie stomatologiczne',
			'target_language'      => 'pl',
			'target_language_name' => 'Polish',
			'label'                => 'Dental Treatment',
		)
	);
	if ( is_wp_error( $polish_term_id ) ) {
		return $polish_term_id;
	}

	$translation = $translations[ $source_slug ];
	$result      = estecapelli_it_hair_import_one(
		$translation,
		$polish_term_id,
		'estecapelli_pl_dental_source_seed',
		array(
			'language_code' => 'pl',
			'language_name' => 'Polish',
		)
	);
	if ( is_wp_error( $result ) ) {
		return new WP_Error( $result->get_error_code(), sprintf( '%s: %s', $source_slug, $result->get_error_message() ) );
	}

	return array( $source_slug => $result );
}
