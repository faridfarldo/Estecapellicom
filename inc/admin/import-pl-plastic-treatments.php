<?php
/**
 * Polish importer for the eight Plastic Surgery treatments.
 *
 * The English treatment seed remains the source of truth for ACF structure and
 * media. Version-controlled Polish overlays replace only visitor-facing copy,
 * while WPML links each page to its published English source. Runs one page per
 * request (ACFML can exceed the execution limit when several flexible-content
 * translations are saved at once).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_PL_PLASTIC_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_PL_PLASTIC_IMPORT_VERSION', '2026-07-16.1' );
}

/**
 * Polish Plastic Surgery import manifest.
 *
 * @return array<string,string> English source slug => canonical Polish slug.
 */
function estecapelli_pl_plastic_manifest() {
	return array(
		'rhinoplasty'                                             => 'rynoplastyka',
		'breast-aesthetics-breast-surgery'                        => 'estetyka-piersi-chirurgia-piersi',
		'bbl'                                                     => 'bbl',
		'liposuction'                                             => 'liposukcja',
		'face-and-neck-lift-surgery'                             => 'chirurgia-liftingu-twarzy-i-szyi',
		'abdominoplasty-tummy-tuck'                              => 'plastyka-brzucha',
		'gynecomastia'                                           => 'ginekomastia',
		'obesity-surgeries-bariatric-surgery-and-gastric-balloon' => 'operacje-otylosci-chirurgia-bariatryczna-i-balon-zoladkowy',
	);
}

/**
 * Return one English seed record, restricted to Plastic Surgery.
 *
 * @param string $source_slug English treatment slug.
 * @return array<string,mixed>|WP_Error
 */
function estecapelli_pl_plastic_source_seed( $source_slug ) {
	foreach ( estecapelli_treatments_seed() as $treatment ) {
		if ( $source_slug === ( $treatment['slug'] ?? '' ) ) {
			if ( 'Plastic Surgery' !== ( $treatment['category'] ?? '' ) ) {
				return new WP_Error( 'pl_plastic_wrong_category', sprintf( '%s is not in the Plastic Surgery category.', $source_slug ) );
			}
			return $treatment;
		}
	}

	return new WP_Error( 'pl_plastic_missing_source_seed', sprintf( 'English seed not found for %s.', $source_slug ) );
}

/**
 * Load and strictly validate all Polish Plastic Surgery overlays.
 *
 * @return array<int,array<string,mixed>>|WP_Error
 */
function estecapelli_pl_plastic_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/pl/plastic-surgery';
	$indexed   = function_exists( 'estecapelli_indexed_treatment_slugs' ) ? estecapelli_indexed_treatment_slugs() : array();
	$loaded    = array();

	foreach ( estecapelli_pl_plastic_manifest() as $source_slug => $polish_slug ) {
		if ( ( $indexed[ $source_slug ]['pl'] ?? '' ) !== $polish_slug ) {
			return new WP_Error( 'pl_plastic_indexed_slug_mismatch', sprintf( 'The Polish slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'pl_plastic_missing_file', sprintf( 'Missing Polish translation file: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'pl_plastic_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}

		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$polish_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'pl_plastic_invalid_translation', sprintf( 'Incomplete or mismatched Polish translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_pl_plastic_source_seed( $source_slug );
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
 * Import one Polish Plastic Surgery treatment page.
 *
 * @param string $source_slug English treatment slug from the manifest.
 * @return array<string,int>|WP_Error Source slug => Polish post ID.
 */
function estecapelli_run_pl_plastic_import( $source_slug ) {
	$source_slug = (string) $source_slug;
	if ( ! isset( estecapelli_pl_plastic_manifest()[ $source_slug ] ) ) {
		return new WP_Error( 'pl_plastic_invalid_source', sprintf( 'Unknown Polish Plastic Surgery source: %s.', $source_slug ) );
	}

	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'pl_plastic_acf_missing', 'ACF is required for the Polish Plastic Surgery import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'pl_plastic_wpml_missing', 'WPML is required for the Polish Plastic Surgery import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['pl'] ) ) {
		return new WP_Error( 'pl_plastic_polish_inactive', 'Polish must be active in WPML before importing the treatment translations.' );
	}

	$translations = estecapelli_pl_plastic_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$polish_term_id = estecapelli_it_hair_category(
		array(
			'source_slug'          => 'plastic-surgery',
			'target_slug'          => 'chirurgia-plastyczna',
			'target_name'          => 'Chirurgia plastyczna',
			'target_language'      => 'pl',
			'target_language_name' => 'Polish',
			'label'                => 'Plastic Surgery',
		)
	);
	if ( is_wp_error( $polish_term_id ) ) {
		return $polish_term_id;
	}

	$imported = array();
	foreach ( $translations as $translation ) {
		if ( $source_slug !== $translation['source_slug'] ) {
			continue;
		}

		$result = estecapelli_it_hair_import_one(
			$translation,
			$polish_term_id,
			'estecapelli_pl_plastic_source_seed',
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
