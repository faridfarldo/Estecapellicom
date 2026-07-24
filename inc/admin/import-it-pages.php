<?php
/**
 * Italian importer for the remaining non-article content pages.
 *
 * Version-controlled overlays replace visitor-facing copy only. The English
 * seed remains authoritative for ACF structure and media, while the shared
 * Italian page engine repairs the WPML relationship and indexed hierarchy.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_IT_PAGES_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_IT_PAGES_IMPORT_VERSION', '2026-07-24.1' );
}

/** English page slug => exact indexed Italian leaf slug. */
function estecapelli_it_pages_manifest() {
	return array(
		'plastic-surgery'  => 'chirurgia-plastica',
		'dental-treatment' => 'trattamento-dentale',
		'about-us'         => 'chi-siamo',
		'our-team'         => 'il-nostro-team',
		'our-doctors'      => 'i-nostri-medici',
		'medical-director' => 'direttore-medico',
		'before-after'     => 'prima-dopo',
		'contact'          => 'contatto',
	);
}

/** Template-rendered pages with no ACF page_sections overlay. */
function estecapelli_it_template_pages_manifest() {
	return array(
		'blog' => array(
			'slug'  => 'blog',
			'title' => 'Blog',
		),
	);
}

/** Return the indexed English route key for a regular page seed. */
function estecapelli_it_pages_route_key( $source_slug ) {
	if ( in_array( $source_slug, array( 'our-team', 'our-doctors', 'medical-director' ), true ) ) {
		return '/en/about-us/' . $source_slug;
	}

	return '/en/' . $source_slug;
}

/** Load and strictly validate every Italian page overlay. */
function estecapelli_it_pages_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/it/pages';
	$loaded    = array();

	foreach ( estecapelli_it_pages_manifest() as $source_slug => $italian_slug ) {
		$route = estecapelli_indexed_route_path( estecapelli_it_pages_route_key( $source_slug ), 'it' );
		if ( ! $route || basename( $route ) !== $italian_slug ) {
			return new WP_Error( 'it_pages_indexed_slug_mismatch', sprintf( 'The Italian slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'it_pages_missing_file', sprintf( 'Missing Italian page translation file: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'it_pages_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$italian_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'it_pages_invalid_translation', sprintf( 'Incomplete or mismatched Italian page translation: %s', basename( $file ) ) );
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

/** Find an Italian page by a shared multilingual slug, excluding the source. */
function estecapelli_it_template_page_raw_target_id( $slug, $source_id, $language_code = 'it' ) {
	global $wpdb;
	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = 'page' AND post_status <> 'trash' AND ID <> %d
			 ORDER BY ID ASC",
			$slug,
			(int) $source_id
		)
	);

	foreach ( $ids as $id ) {
		$details = apply_filters(
			'wpml_element_language_details',
			null,
			array(
				'element_id'   => (int) $id,
				'element_type' => 'page',
			)
		);
		if ( $language_code === (string) estecapelli_it_hair_detail( $details, 'language_code' ) ) {
			return (int) $id;
		}
	}

	return 0;
}

/** Import or repair a template-rendered Italian page without touching posts. */
function estecapelli_it_template_page_import_one( $source_slug, array $translation, $language_code = 'it', $language_name = 'Italian' ) {
	$route = estecapelli_indexed_route_path( '/en/' . $source_slug, $language_code );
	if ( ! $route || basename( $route ) !== $translation['slug'] ) {
		return new WP_Error( 'it_template_pages_indexed_slug_mismatch', sprintf( 'The %s template-page slug does not match the indexed URL contract: %s.', $language_name, $source_slug ) );
	}

	$source_id = estecapelli_source_post_id( $source_slug, 'page' );
	if ( ! $source_id ) {
		return new WP_Error( 'it_template_pages_missing_source_post', sprintf( 'Published English page not found: %s.', $source_slug ) );
	}
	$source_post = get_post( $source_id );
	if ( ! $source_post ) {
		return new WP_Error( 'it_template_pages_invalid_source_post', sprintf( 'English page could not be loaded: %s.', $source_slug ) );
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
		return new WP_Error( 'it_template_pages_unlinked_source_post', sprintf( 'WPML language details are missing for %s.', $source_slug ) );
	}

	// Prefer the canonical target slug so a stale WPML slot occupant cannot be
	// overwritten with this page's content.
	$target_id = estecapelli_it_template_page_raw_target_id( $translation['slug'], $source_id, $language_code );
	if ( ! $target_id ) {
		$target_id = estecapelli_wpml_group_element_id_raw( $trid, $element_type, $language_code );
	}
	if ( ! $target_id ) {
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, $language_code );
	}
	if ( $target_id === $source_id ) {
		$target_id = 0;
	}
	if ( $target_id ) {
		$raw_target = get_post( $target_id );
		if ( ! $raw_target || 'page' !== $raw_target->post_type || 'trash' === $raw_target->post_status || $target_id === $source_id ) {
			estecapelli_wpml_delete_relationship_raw( $target_id, $element_type, $trid, $language_code );
			$target_id = 0;
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
		'post_parent'  => 0,
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
			'language_code'        => $language_code,
			'source_language_code' => $source_language,
			'check_duplicates'     => false,
		)
	);
	delete_post_meta( $target_id, '_icl_lang_duplicate_of' );

	if ( ! estecapelli_wpml_replace_language_slot_raw( $target_id, $element_type, $trid, $language_code, $source_language ) ) {
		$reason = estecapelli_wpml_last_slot_error();
		return new WP_Error(
			'it_template_pages_force_link_failed',
			sprintf( 'The %s WPML relationship could not be rebuilt for %s%s', $language_name, $source_slug, $reason ? ' — ' . $reason : '.' )
		);
	}

	$target_id = wp_update_post(
		array(
			'ID'         => (int) $target_id,
			'post_title' => $translation['title'],
			'post_name'  => $translation['slug'],
		),
		true
	);
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}
	$slug_result = estecapelli_force_multilingual_post_slug( $target_id, $translation['slug'], 'page', $language_code );
	if ( is_wp_error( $slug_result ) ) {
		return $slug_result;
	}
	$target_post = get_post( $target_id );
	if ( ! $target_post || $translation['slug'] !== $target_post->post_name ) {
		return new WP_Error( 'it_template_pages_slug_conflict', sprintf( 'The required %s page slug is already in use: %s.', $language_name, $translation['slug'] ) );
	}

	$thumbnail_id = get_post_thumbnail_id( $source_id );
	if ( $thumbnail_id && ! get_post_thumbnail_id( $target_id ) ) {
		set_post_thumbnail( $target_id, $thumbnail_id );
	}

	return (int) $target_id;
}

/**
 * Import or repair one Italian regular page by its English source slug.
 *
 * @param string $source_slug English page slug.
 * @return int|WP_Error
 */
function estecapelli_import_one_it_page( $source_slug ) {
	$source_slug = sanitize_title( $source_slug );
	$manifest    = estecapelli_it_pages_manifest();
	if ( isset( $manifest[ $source_slug ] ) ) {
		$translations = estecapelli_it_pages_load_translations();
		if ( is_wp_error( $translations ) ) {
			return $translations;
		}
		if ( ! isset( $translations[ $source_slug ] ) ) {
			return new WP_Error( 'it_page_translation_missing', 'The requested Italian page overlay was not found.' );
		}

		return estecapelli_it_hair_pages_import_one( $translations[ $source_slug ] );
	}

	$template_pages = estecapelli_it_template_pages_manifest();
	if ( isset( $template_pages[ $source_slug ] ) ) {
		return estecapelli_it_template_page_import_one( $source_slug, $template_pages[ $source_slug ] );
	}

	return new WP_Error( 'it_page_unknown', 'Unknown Italian page.' );
}

add_action( 'admin_post_estecapelli_import_it_page', 'estecapelli_handle_it_page_manual_import' );
/** Process one manual Italian page import and return to the shared importer. */
function estecapelli_handle_it_page_manual_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import pages.', 'estecapelli' ) );
	}

	$source_slug = isset( $_GET['source'] ) ? sanitize_title( wp_unslash( $_GET['source'] ) ) : '';
	check_admin_referer( 'estecapelli_import_it_page_' . $source_slug );
	$result = estecapelli_import_one_it_page( $source_slug );
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_it_pages_import_error', $source_slug . ': ' . $result->get_error_message(), false );
	} else {
		delete_option( 'estecapelli_it_pages_import_error' );
		set_transient( 'estecapelli_it_pages_import_success', 1, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect(
		add_query_arg( 'page', 'estecapelli-treatment-importer', admin_url( 'tools.php' ) )
	);
	exit;
}

/** Run the complete Italian non-blog page import. */
function estecapelli_run_it_pages_import() {
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'it_pages_acf_missing', 'ACF is required for the Italian page import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'it_pages_wpml_missing', 'WPML is required for the Italian page import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['it'] ) ) {
		return new WP_Error( 'it_pages_italian_inactive', 'Italian must be active in WPML before importing the page translations.' );
	}

	$translations = estecapelli_it_pages_load_translations();
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
	foreach ( estecapelli_it_template_pages_manifest() as $source_slug => $translation ) {
		$result = estecapelli_it_template_page_import_one( $source_slug, $translation );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( $result->get_error_code(), sprintf( '%s: %s', $source_slug, $result->get_error_message() ) );
		}
		$imported[ $source_slug ] = $result;
	}

	return $imported;
}

add_action( 'admin_init', 'estecapelli_maybe_import_it_pages', 84 );
/** Run once after deployment; failed runs remain retryable. */
function estecapelli_maybe_import_it_pages() {
	if (
		get_option( 'estecapelli_it_pages_import_version' ) === ESTECAPELLI_IT_PAGES_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_it_pages_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_it_pages_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_it_pages_import_version', ESTECAPELLI_IT_PAGES_IMPORT_VERSION, false );
	delete_option( 'estecapelli_it_pages_import_error' );
	set_transient( 'estecapelli_it_pages_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_it_pages_import_notice' );
/** Show the Italian page-import result to administrators. */
function estecapelli_it_pages_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_it_pages_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_it_pages_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html(
				sprintf(
					_n(
						'Italian page imported successfully: %d page.',
						'Italian pages imported successfully: %d pages.',
						(int) $success_count,
						'estecapelli'
					),
					(int) $success_count
				)
			)
		);
		return;
	}

	$error = get_option( 'estecapelli_it_pages_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'Italian page import could not finish.' ),
			esc_html( $error )
		);
	}
}
