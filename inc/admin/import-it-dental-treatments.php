<?php
/**
 * Italian importer for the two Dental Treatment procedures.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_IT_DENTAL_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_IT_DENTAL_IMPORT_VERSION', '2026-07-16.1' );
}

/** English source slug => exact indexed Italian leaf slug. */
function estecapelli_it_dental_manifest() {
	return array(
		'dental-implant'  => 'impianto-dentale',
		'hollywood-smile' => 'sorriso-hollywoodiano',
	);
}

/** Return one English seed record, restricted to Dental Treatment. */
function estecapelli_it_dental_source_seed( $source_slug ) {
	foreach ( estecapelli_treatments_seed() as $treatment ) {
		if ( $source_slug === ( $treatment['slug'] ?? '' ) ) {
			if ( 'Dental Treatment' !== ( $treatment['category'] ?? '' ) ) {
				return new WP_Error( 'it_dental_wrong_category', sprintf( '%s is not in the Dental Treatment category.', $source_slug ) );
			}
			return $treatment;
		}
	}

	return new WP_Error( 'it_dental_missing_source_seed', sprintf( 'English seed not found for %s.', $source_slug ) );
}

/** Load and strictly validate all Italian Dental Treatment overlays. */
function estecapelli_it_dental_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/it/dental-treatment';
	$indexed   = estecapelli_indexed_treatment_slugs();
	$loaded    = array();

	foreach ( estecapelli_it_dental_manifest() as $source_slug => $italian_slug ) {
		if ( ( $indexed[ $source_slug ]['it'] ?? '' ) !== $italian_slug ) {
			return new WP_Error( 'it_dental_indexed_slug_mismatch', sprintf( 'The Italian slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'it_dental_missing_file', sprintf( 'Missing Italian Dental Treatment translation: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'it_dental_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$italian_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'it_dental_invalid_translation', sprintf( 'Incomplete or mismatched Italian Dental Treatment translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_it_dental_source_seed( $source_slug );
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

/** Return the canonical Italian Dental Treatment category. */
function estecapelli_it_dental_category() {
	return estecapelli_it_hair_category(
		array(
			'source_slug' => 'dental-treatment',
			'target_slug' => 'trattamento-dentale',
			'target_name' => 'Trattamento dentale',
			'label'       => 'Dental Treatment',
		)
	);
}

/** Run the complete Italian Dental Treatment import. */
function estecapelli_run_it_dental_import() {
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'it_dental_acf_missing', 'ACF is required for the Italian Dental Treatment import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'it_dental_wpml_missing', 'WPML is required for the Italian Dental Treatment import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['it'] ) ) {
		return new WP_Error( 'it_dental_italian_inactive', 'Italian must be active in WPML before importing the treatment translations.' );
	}

	$translations = estecapelli_it_dental_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}
	$italian_term_id = estecapelli_it_dental_category();
	if ( is_wp_error( $italian_term_id ) ) {
		return $italian_term_id;
	}

	$imported = array();
	foreach ( $translations as $translation ) {
		$result = estecapelli_it_hair_import_one( $translation, $italian_term_id, 'estecapelli_it_dental_source_seed' );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( $result->get_error_code(), sprintf( '%s: %s', $translation['source_slug'], $result->get_error_message() ) );
		}
		$imported[ $translation['source_slug'] ] = $result;
	}

	return $imported;
}

add_action( 'admin_init', 'estecapelli_maybe_import_it_dental_treatments', 93 );
/** Run once after deployment; failed runs remain retryable. */
function estecapelli_maybe_import_it_dental_treatments() {
	if (
		get_option( 'estecapelli_it_dental_import_version' ) === ESTECAPELLI_IT_DENTAL_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_it_dental_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_it_dental_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_it_dental_import_version', ESTECAPELLI_IT_DENTAL_IMPORT_VERSION, false );
	delete_option( 'estecapelli_it_dental_import_error' );
	set_transient( 'estecapelli_it_dental_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_it_dental_import_notice' );
/** Show the Italian Dental Treatment import result. */
function estecapelli_it_dental_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_it_dental_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_it_dental_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'Italian Dental Treatment translations imported successfully: %d treatments.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_it_dental_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'Italian Dental Treatment import could not finish.' ),
			esc_html( $error )
		);
	}
}
