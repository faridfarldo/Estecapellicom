<?php
/**
 * French importer for regular WordPress pages.
 *
 * Mirrors the treatment translation importer: version-controlled JSON overlays
 * replace visitor-facing copy only, the English page seed stays the source of
 * truth for ACF structure and media, and WPML links each French page to its
 * English original. Reuses the shared overlay/validation/WPML engine.
 *
 * Page hierarchy is preserved: a French child page is parented under the French
 * translation of its English parent. The existing slug sweep
 * (estecapelli_slug_fix_pages) settles the canonical French leaf slug afterwards.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_FR_PAGES_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_FR_PAGES_IMPORT_VERSION', '2026-07-20.1' );
}

/**
 * English page slug => exact Google-indexed French leaf slug.
 *
 * Deliberately ordered parents-before-children so post_parent resolves to an
 * already-linked French parent during a single sequential run.
 *
 * @return array<string,string>
 */
function estecapelli_fr_pages_manifest() {
	return array(
		// Field-of-care landing pages (nav parents).
		'hair-transplant'             => 'greffe-de-cheveux',
		'plastic-surgery'             => 'chirurgie-plastique',
		'dental-treatment'            => 'traitement-dentaire',
		// Hair-transplant care & technology (children of hair-transplant).
		'tricholab'                   => 'tricholab',
		'pre-hair-transplant-period'  => 'periode-pre-transplantation-capillaire',
		'post-hair-transplant-period' => 'periode-post-greffe-de-cheveux',
		// About tree.
		'about-us'                    => 'a-propos-de-nous',
		'our-team'                    => 'notre-equipe',
		'our-doctors'                 => 'nos-medecins',
		'medical-director'            => 'directeur-medical',
		// Standalone pages. (The blog landing has no ACF sections — it renders the
		// post archive — so it is translated with the blog round, not here.)
		'before-after'                => 'avant-apres',
		'contact'                     => 'contact',
	);
}

/**
 * Return one English page seed record by slug.
 *
 * @param string $source_slug English page slug.
 * @return array<string,mixed>|WP_Error
 */
function estecapelli_fr_pages_source_seed( $source_slug ) {
	if ( ! function_exists( 'estecapelli_pages_seed' ) ) {
		return new WP_Error( 'fr_pages_no_seed', 'The page seed is unavailable.' );
	}
	foreach ( estecapelli_pages_seed() as $page ) {
		if ( $source_slug === ( $page['slug'] ?? '' ) ) {
			return $page;
		}
	}

	return new WP_Error( 'fr_pages_missing_source_seed', sprintf( 'English page seed not found for %s.', $source_slug ) );
}

/**
 * Find a page id by raw slug, bypassing WPML language filtering.
 *
 * @param string $slug       Page slug (post_name).
 * @param int    $exclude_id Optional id to exclude.
 * @return int
 */
function estecapelli_fr_page_raw_post_id( $slug, $exclude_id = 0 ) {
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

/**
 * Load and strictly validate all French page overlays.
 *
 * @return array<int,array<string,mixed>>|WP_Error
 */
function estecapelli_fr_pages_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/fr/pages';
	$indexed   = function_exists( 'estecapelli_slug_map_pages' ) ? estecapelli_slug_map_pages() : array();
	$loaded    = array();

	foreach ( estecapelli_fr_pages_manifest() as $source_slug => $french_slug ) {
		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'fr_pages_missing_file', sprintf( 'Missing French page translation file: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'fr_pages_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}

		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$french_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'fr_pages_invalid_translation', sprintf( 'Incomplete or mismatched French page translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_fr_pages_source_seed( $source_slug );
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

		$loaded[ $source_slug ] = $translation;
	}

	return $loaded;
}

/**
 * Force-import one French page from its validated overlay.
 *
 * @param array<string,mixed> $translation Validated overlay.
 * @return int|WP_Error French page ID.
 */
function estecapelli_fr_pages_import_one( array $translation ) {
	$source_slug = $translation['source_slug'];

	$seed = estecapelli_fr_pages_source_seed( $source_slug );
	if ( is_wp_error( $seed ) ) {
		return $seed;
	}

	$source_id = estecapelli_source_post_id( $source_slug, 'page' );
	if ( ! $source_id ) {
		return new WP_Error( 'fr_pages_missing_source_post', sprintf( 'Published English page not found: %s.', $source_slug ) );
	}
	$source_post = get_post( $source_id );
	if ( ! $source_post ) {
		return new WP_Error( 'fr_pages_invalid_source_post', sprintf( 'English page could not be loaded: %s.', $source_slug ) );
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
	$trid            = (int) estecapelli_fr_hair_detail( $source_details, 'trid' );
	$source_language = (string) estecapelli_fr_hair_detail( $source_details, 'language_code' );
	if ( ! $trid || 'en' !== $source_language ) {
		return new WP_Error( 'fr_pages_unlinked_source_post', sprintf( 'WPML language details are missing for %s.', $source_slug ) );
	}

	// Prefer the exact indexed French slug so a stale slot occupant cannot cause
	// a different French page to be overwritten.
	$target_id = estecapelli_fr_page_raw_post_id( $translation['slug'], $source_id );
	if ( ! $target_id ) {
		$target_id = estecapelli_wpml_group_element_id_raw( $trid, $element_type, 'fr' );
	}
	if ( $target_id ) {
		$raw_target = get_post( $target_id );
		if ( ! $raw_target || 'page' !== $raw_target->post_type || 'trash' === $raw_target->post_status || $target_id === $source_id ) {
			estecapelli_wpml_delete_relationship_raw( $target_id, $element_type, $trid, 'fr' );
			$target_id = 0;
		}
	}
	if ( ! $target_id ) {
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, 'fr' );
	}
	if ( $target_id === $source_id ) {
		$target_id = 0;
	}
	if ( ! $target_id ) {
		$target_id = estecapelli_fr_page_raw_post_id( $translation['slug'], $source_id );
	}

	// Resolve the French parent so the page keeps its hierarchy.
	$french_parent = 0;
	if ( (int) $source_post->post_parent ) {
		$linked_parent = (int) apply_filters( 'wpml_object_id', (int) $source_post->post_parent, 'page', false, 'fr' );
		if ( $linked_parent && $linked_parent !== (int) $source_post->post_parent ) {
			$french_parent = $linked_parent;
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
		'post_parent'  => $french_parent,
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
			'language_code'        => 'fr',
			'source_language_code' => $source_language,
			'check_duplicates'     => false,
		)
	);
	delete_post_meta( $target_id, '_icl_lang_duplicate_of' );

	$forced = estecapelli_wpml_replace_language_slot_raw( $target_id, $element_type, $trid, 'fr', $source_language );
	if ( ! $forced ) {
		$reason = estecapelli_wpml_last_slot_error();
		return new WP_Error(
			'fr_pages_force_link_failed',
			sprintf(
				'The French WPML relationship could not be rebuilt for %s (English page #%d, French page #%d, trid %d)%s',
				$source_slug,
				(int) $source_id,
				(int) $target_id,
				(int) $trid,
				$reason ? ' — ' . $reason : '.'
			)
		);
	}

	$linked_target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, 'fr' );
	if ( (int) $target_id !== $linked_target_id && ! estecapelli_wpml_element_matches_raw( $target_id, $element_type, $trid, 'fr' ) ) {
		$repaired = estecapelli_wpml_repair_relationship_raw( $target_id, $element_type, $trid, 'fr', $source_language );
		if ( ! $repaired ) {
			return new WP_Error( 'fr_pages_link_failed', sprintf( 'WPML did not link the French page for %s.', $source_slug ) );
		}
	}

	// Re-apply the canonical French slug/title after WPML linking.
	$target_id = wp_update_post(
		array(
			'ID'          => (int) $target_id,
			'post_title'  => $translation['title'],
			'post_name'   => $translation['slug'],
			'post_parent' => $french_parent,
		),
		true
	);
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}
	$target_post = get_post( $target_id );
	if ( ! $target_post || $translation['slug'] !== $target_post->post_name ) {
		return new WP_Error( 'fr_pages_slug_conflict', sprintf( 'The required French slug is already in use: %s.', $translation['slug'] ) );
	}

	$sections = estecapelli_merge_preserve_media( $seed['sections'], $source_id );
	$sections = estecapelli_fr_hair_overlay( $sections, $translation['sections'] );
	if ( is_wp_error( $sections ) ) {
		return $sections;
	}
	$sections = estecapelli_fr_hair_localize_urls( $sections );
	$sections = estecapelli_fr_hair_normalize_media( $sections );
	update_field( 'page_sections', $sections, $target_id );

	$saved_sections = get_field( 'page_sections', $target_id );
	$saved_title    = is_array( $saved_sections ) ? ( $saved_sections[0]['title'] ?? '' ) : '';
	$expected_title = $translation['sections'][0]['title'] ?? '';
	if ( ! is_array( $saved_sections ) || ! $expected_title || $expected_title !== $saved_title ) {
		return new WP_Error( 'fr_pages_acf_not_saved', sprintf( 'The French ACF content was not saved for %s.', $source_slug ) );
	}

	$thumbnail_id = get_post_thumbnail_id( $source_id );
	if ( $thumbnail_id ) {
		set_post_thumbnail( $target_id, $thumbnail_id );
	}

	return (int) $target_id;
}

/**
 * Run the complete French page import.
 *
 * @return array<string,int>|WP_Error Source slug => French page ID.
 */
function estecapelli_run_fr_pages_import() {
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'fr_pages_acf_missing', 'ACF is required for the French page import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'fr_pages_wpml_missing', 'WPML is required for the French page import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['fr'] ) ) {
		return new WP_Error( 'fr_pages_french_inactive', 'French must be active in WPML before importing the page translations.' );
	}

	$translations = estecapelli_fr_pages_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$imported = array();
	foreach ( $translations as $source_slug => $translation ) {
		$result = estecapelli_fr_pages_import_one( $translation );
		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				$result->get_error_code(),
				sprintf( '%s: %s', $source_slug, $result->get_error_message() )
			);
		}
		$imported[ $source_slug ] = $result;
	}

	return $imported;
}

/**
 * Force-import one French page by slug (used by the manual button).
 *
 * @param string $source_slug English page slug.
 * @return int|WP_Error
 */
function estecapelli_import_one_fr_page( $source_slug ) {
	if ( ! isset( estecapelli_fr_pages_manifest()[ $source_slug ] ) ) {
		return new WP_Error( 'fr_pages_unknown', 'Unknown French page.' );
	}
	$translations = estecapelli_fr_pages_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}
	if ( ! isset( $translations[ $source_slug ] ) ) {
		return new WP_Error( 'fr_pages_translation_missing', 'The requested French page overlay was not found.' );
	}
	return estecapelli_fr_pages_import_one( $translations[ $source_slug ] );
}

add_action( 'admin_post_estecapelli_import_fr_page', 'estecapelli_handle_fr_page_manual_import' );
/** Process an individual manual French page import button. */
function estecapelli_handle_fr_page_manual_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import pages.', 'estecapelli' ) );
	}

	$source_slug = isset( $_GET['source'] ) ? sanitize_title( wp_unslash( $_GET['source'] ) ) : '';
	check_admin_referer( 'estecapelli_import_fr_page_' . $source_slug );
	$result = estecapelli_import_one_fr_page( $source_slug );
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_fr_pages_import_error', $source_slug . ': ' . $result->get_error_message(), false );
	} else {
		delete_option( 'estecapelli_fr_pages_import_error' );
		set_transient( 'estecapelli_fr_pages_import_success', 1, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect(
		add_query_arg( 'page', 'estecapelli-treatment-importer', admin_url( 'tools.php' ) )
	);
	exit;
}

add_action( 'admin_init', 'estecapelli_maybe_import_fr_pages', 90 );
/**
 * Run once after deployment. Failed runs remain retryable.
 */
function estecapelli_maybe_import_fr_pages() {
	if (
		get_option( 'estecapelli_fr_pages_import_version' ) === ESTECAPELLI_FR_PAGES_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_fr_pages_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_fr_pages_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_fr_pages_import_version', ESTECAPELLI_FR_PAGES_IMPORT_VERSION, false );
	delete_option( 'estecapelli_fr_pages_import_error' );
	set_transient( 'estecapelli_fr_pages_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_fr_pages_import_notice' );
/**
 * Show an actionable import result to administrators.
 */
function estecapelli_fr_pages_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_fr_pages_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_fr_pages_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'French page translations imported successfully: %d pages.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_fr_pages_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'French page import could not finish.' ),
			esc_html( $error )
		);
	}
}
