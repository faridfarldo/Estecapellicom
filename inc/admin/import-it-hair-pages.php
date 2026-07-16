<?php
/**
 * Italian importer for the Hair Transplant landing and care pages.
 *
 * The English page seed remains authoritative for ACF structure and media;
 * version-controlled overlays replace every visitor-facing string. WPML page
 * relationships and the exact indexed Italian hierarchy are repaired in place.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_IT_HAIR_PAGES_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_IT_HAIR_PAGES_IMPORT_VERSION', '2026-07-16.1' );
}

/**
 * English page slug => exact live Italian leaf slug.
 *
 * The parent is deliberately first so children can attach to its linked
 * Italian translation during the same sequential import.
 *
 * @return array<string,string>
 */
function estecapelli_it_hair_pages_manifest() {
	return array(
		'hair-transplant'             => 'trapianto-di-capelli',
		'tricholab'                   => 'tricholab',
		'pre-hair-transplant-period'  => 'periodo-pre-trapianto-di-capelli',
		'post-hair-transplant-period' => 'periodo-post-trapianto-di-capelli',
	);
}

/** Return one English page seed record by slug. */
function estecapelli_it_hair_pages_source_seed( $source_slug ) {
	if ( ! function_exists( 'estecapelli_pages_seed' ) ) {
		return new WP_Error( 'it_hair_pages_no_seed', 'The page seed is unavailable.' );
	}
	foreach ( estecapelli_pages_seed() as $page ) {
		if ( $source_slug === ( $page['slug'] ?? '' ) ) {
			return $page;
		}
	}

	return new WP_Error( 'it_hair_pages_missing_source_seed', sprintf( 'English page seed not found for %s.', $source_slug ) );
}

/** Exact indexed source key for one page in the manifest. */
function estecapelli_it_hair_pages_route_key( $source_slug ) {
	return 'hair-transplant' === $source_slug
		? '/en/hair-transplant'
		: '/en/hair-transplant/' . $source_slug;
}

/** Find a page ID by raw slug, bypassing WPML language filtering. */
function estecapelli_it_hair_page_raw_post_id( $slug, $exclude_id = 0 ) {
	global $wpdb;
	if ( $exclude_id ) {
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				 WHERE post_name = %s AND post_type = 'page' AND post_status <> 'trash' AND ID <> %d
				 ORDER BY ID ASC LIMIT 1",
				$slug,
				(int) $exclude_id
			)
		);
	}
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = 'page' AND post_status <> 'trash'
			 ORDER BY ID ASC LIMIT 1",
			$slug
		)
	);
}

/** Load and strictly validate all Italian Hair Transplant page overlays. */
function estecapelli_it_hair_pages_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/it/pages';
	$loaded    = array();

	foreach ( estecapelli_it_hair_pages_manifest() as $source_slug => $italian_slug ) {
		$route_key = estecapelli_it_hair_pages_route_key( $source_slug );
		$route     = estecapelli_indexed_route_path( $route_key, 'it' );
		if ( ! $route || basename( $route ) !== $italian_slug ) {
			return new WP_Error( 'it_hair_pages_indexed_slug_mismatch', sprintf( 'The Italian slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'it_hair_pages_missing_file', sprintf( 'Missing Italian page translation file: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'it_hair_pages_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$italian_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'it_hair_pages_invalid_translation', sprintf( 'Incomplete or mismatched Italian page translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_it_hair_pages_source_seed( $source_slug );
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

		$loaded[ $source_slug ] = $translation;
	}

	return $loaded;
}

/** Force-import one validated Italian page overlay. */
function estecapelli_it_hair_pages_import_one( array $translation ) {
	$source_slug = $translation['source_slug'];
	$seed        = estecapelli_it_hair_pages_source_seed( $source_slug );
	if ( is_wp_error( $seed ) ) {
		return $seed;
	}

	$source_id = estecapelli_source_post_id( $source_slug, 'page' );
	if ( ! $source_id ) {
		return new WP_Error( 'it_hair_pages_missing_source_post', sprintf( 'Published English page not found: %s.', $source_slug ) );
	}
	$source_post = get_post( $source_id );
	if ( ! $source_post ) {
		return new WP_Error( 'it_hair_pages_invalid_source_post', sprintf( 'English page could not be loaded: %s.', $source_slug ) );
	}

	$element_type    = apply_filters( 'wpml_element_type', 'page' );
	$source_details  = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => $source_id,
			'element_type' => 'page',
		)
	);
	$trid            = (int) estecapelli_it_hair_detail( $source_details, 'trid' );
	$source_language = (string) estecapelli_it_hair_detail( $source_details, 'language_code' );
	if ( ! $trid || 'en' !== $source_language ) {
		return new WP_Error( 'it_hair_pages_unlinked_source_post', sprintf( 'WPML language details are missing for %s.', $source_slug ) );
	}

	// Prefer the record already owning the exact live slug.
	$target_id = estecapelli_it_hair_page_raw_post_id( $translation['slug'], $source_id );
	if ( ! $target_id ) {
		$target_id = estecapelli_wpml_group_element_id_raw( $trid, $element_type, 'it' );
	}
	if ( $target_id ) {
		$raw_target = get_post( $target_id );
		if ( ! $raw_target || 'page' !== $raw_target->post_type || 'trash' === $raw_target->post_status || $target_id === $source_id ) {
			estecapelli_wpml_delete_relationship_raw( $target_id, $element_type, $trid, 'it' );
			$target_id = 0;
		}
	}
	if ( ! $target_id ) {
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, 'it' );
	}
	if ( $target_id === $source_id ) {
		$target_id = 0;
	}
	if ( ! $target_id ) {
		$target_id = estecapelli_it_hair_page_raw_post_id( $translation['slug'], $source_id );
	}

	$italian_parent = 0;
	if ( (int) $source_post->post_parent ) {
		$linked_parent = (int) apply_filters( 'wpml_object_id', (int) $source_post->post_parent, 'page', false, 'it' );
		if ( $linked_parent && $linked_parent !== (int) $source_post->post_parent ) {
			$italian_parent = $linked_parent;
		}
	}

	if ( $target_id ) {
		delete_post_meta( $target_id, '_icl_lang_duplicate_of' );
	}

	$post_args = array(
		'post_type'    => 'page',
		'post_title'   => $translation['title'],
		'post_name'    => $translation['slug'],
		'post_status'  => 'publish',
		'post_content' => '',
		'post_parent'  => $italian_parent,
		'menu_order'   => (int) $source_post->menu_order,
	);
	if ( $target_id ) {
		$post_args['ID'] = $target_id;
		$target_id       = wp_update_post( $post_args, true );
	} else {
		$target_id = wp_insert_post( $post_args, true );
	}
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}

	do_action(
		'wpml_set_element_language_details',
		array(
			'element_id'           => (int) $target_id,
			'element_type'         => $element_type,
			'trid'                 => $trid,
			'language_code'        => 'it',
			'source_language_code' => $source_language,
			'check_duplicates'      => false,
		)
	);
	delete_post_meta( $target_id, '_icl_lang_duplicate_of' );

	$forced = estecapelli_wpml_replace_language_slot_raw( $target_id, $element_type, $trid, 'it', $source_language );
	if ( ! $forced ) {
		$reason = estecapelli_wpml_last_slot_error();
		return new WP_Error(
			'it_hair_pages_force_link_failed',
			sprintf(
				'The Italian WPML relationship could not be rebuilt for %s (English page #%d, Italian page #%d, trid %d)%s',
				$source_slug,
				(int) $source_id,
				(int) $target_id,
				(int) $trid,
				$reason ? ' — ' . $reason : '.'
			)
		);
	}

	$linked_target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, 'it' );
	if ( (int) $target_id !== $linked_target_id && ! estecapelli_wpml_element_matches_raw( $target_id, $element_type, $trid, 'it' ) ) {
		$repaired = estecapelli_wpml_repair_relationship_raw( $target_id, $element_type, $trid, 'it', $source_language );
		if ( ! $repaired ) {
			return new WP_Error( 'it_hair_pages_link_failed', sprintf( 'WPML did not link the Italian page for %s.', $source_slug ) );
		}
	}

	$target_id = wp_update_post(
		array(
			'ID'          => (int) $target_id,
			'post_title'  => $translation['title'],
			'post_name'   => $translation['slug'],
			'post_parent' => $italian_parent,
		),
		true
	);
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}
	$target_post = get_post( $target_id );
	if ( ! $target_post || $translation['slug'] !== $target_post->post_name ) {
		return new WP_Error( 'it_hair_pages_slug_conflict', sprintf( 'The required Italian slug is already in use: %s.', $translation['slug'] ) );
	}

	$sections = estecapelli_merge_preserve_media( $seed['sections'], $source_id );
	$sections = estecapelli_it_hair_overlay( $sections, $translation['sections'] );
	if ( is_wp_error( $sections ) ) {
		return $sections;
	}
	$sections = estecapelli_it_hair_localize_urls( $sections );
	$sections = estecapelli_it_hair_normalize_media( $sections );
	update_field( 'page_sections', $sections, $target_id );

	$saved_sections = get_field( 'page_sections', $target_id );
	$saved_title    = is_array( $saved_sections ) ? ( $saved_sections[0]['title'] ?? '' ) : '';
	$expected_title = $translation['sections'][0]['title'] ?? '';
	if ( ! is_array( $saved_sections ) || ! $expected_title || $expected_title !== $saved_title ) {
		return new WP_Error( 'it_hair_pages_acf_not_saved', sprintf( 'The Italian ACF content was not saved for %s.', $source_slug ) );
	}
	$media_saved = estecapelli_it_hair_validate_media( $sections, $saved_sections );
	if ( is_wp_error( $media_saved ) ) {
		return $media_saved;
	}

	$thumbnail_id = get_post_thumbnail_id( $source_id );
	if ( $thumbnail_id ) {
		set_post_thumbnail( $target_id, $thumbnail_id );
	}

	return (int) $target_id;
}

/** Run the complete Italian Hair Transplant page import. */
function estecapelli_run_it_hair_pages_import() {
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'it_hair_pages_acf_missing', 'ACF is required for the Italian page import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'it_hair_pages_wpml_missing', 'WPML is required for the Italian page import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['it'] ) ) {
		return new WP_Error( 'it_hair_pages_italian_inactive', 'Italian must be active in WPML before importing the page translations.' );
	}

	$translations = estecapelli_it_hair_pages_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$imported = array();
	foreach ( $translations as $source_slug => $translation ) {
		$result = estecapelli_it_hair_pages_import_one( $translation );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( $result->get_error_code(), sprintf( '%s: %s', $source_slug, $result->get_error_message() ) );
		}
		$imported[ $source_slug ] = $result;
	}

	return $imported;
}

add_action( 'admin_init', 'estecapelli_maybe_import_it_hair_pages', 83 );
/** Run once after deployment; failed runs remain retryable. */
function estecapelli_maybe_import_it_hair_pages() {
	if (
		get_option( 'estecapelli_it_hair_pages_import_version' ) === ESTECAPELLI_IT_HAIR_PAGES_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_it_hair_pages_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_it_hair_pages_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_it_hair_pages_import_version', ESTECAPELLI_IT_HAIR_PAGES_IMPORT_VERSION, false );
	delete_option( 'estecapelli_it_hair_pages_import_error' );
	set_transient( 'estecapelli_it_hair_pages_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_it_hair_pages_import_notice' );
/** Show an actionable import result to administrators. */
function estecapelli_it_hair_pages_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_it_hair_pages_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_it_hair_pages_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'Italian Hair Transplant pages imported successfully: %d pages.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_it_hair_pages_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'Italian Hair Transplant page import could not finish.' ),
			esc_html( $error )
		);
	}
}
