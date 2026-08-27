<?php
/**
 * Unified blog translation + SEO importer.
 *
 * One importer that brings every non-English blog article into WordPress and
 * layers Rank Math SEO metadata (meta description + focus keyword) onto every
 * language, English included. It supersedes the per-language approach so all
 * articles can be imported together from a single button.
 *
 * Sources of truth:
 *   - Slugs  : estecapelli_indexed_blog_slugs()  (frozen live-URL contract)
 *   - Bodies : /inc/data/blog/{lang}/{english-slug}.html  (authentic, cleaned)
 *   - Titles + SEO : estecapelli_blog_i18n_meta()
 *
 * Each translation is force-linked to its English original through the shared
 * WPML helpers in inc/wpml-slug-fix.php, exactly like the treatment/page/doctor
 * importers. English posts already exist (blog-seed.php); this importer only
 * writes their SEO meta.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/data/blog-i18n-meta.php';

if ( ! defined( 'ESTECAPELLI_BLOG_I18N_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_BLOG_I18N_IMPORT_VERSION', '2026-07-22.1' );
}

/** Translated languages this importer handles (English is meta-only). */
function estecapelli_blog_i18n_languages() {
	return array( 'fr', 'tr', 'it', 'es', 'pl', 'pt', 'ro' );
}

/** Localized metadata entry for one article + language, or null. */
function estecapelli_blog_i18n_entry( $english_slug, $lang ) {
	$meta = estecapelli_blog_i18n_meta();
	return $meta[ $english_slug ][ $lang ] ?? null;
}

/** Frozen localized slug for one article + language, or '' if none. */
function estecapelli_blog_i18n_slug( $english_slug, $lang ) {
	$slugs = estecapelli_indexed_blog_slugs();
	return (string) ( $slugs[ $english_slug ][ $lang ] ?? '' );
}

/** Read the version-controlled translated body, or WP_Error. */
function estecapelli_blog_i18n_body( $lang, $english_slug ) {
	$file = get_template_directory() . '/inc/data/blog/' . $lang . '/' . $english_slug . '.html';
	if ( ! is_readable( $file ) ) {
		return new WP_Error( 'blog_i18n_missing_body', sprintf( 'Missing %s blog body: %s', strtoupper( $lang ), basename( $file ) ) );
	}
	$html = (string) file_get_contents( $file );
	if ( '' === trim( $html ) ) {
		return new WP_Error( 'blog_i18n_empty_body', sprintf( 'Empty %s blog body: %s', strtoupper( $lang ), basename( $file ) ) );
	}
	return wp_kses_post( $html );
}

/** Read one field from WPML language details regardless of array/object shape. */
function estecapelli_blog_i18n_detail( $details, $key ) {
	if ( is_array( $details ) ) {
		return $details[ $key ] ?? null;
	}
	if ( is_object( $details ) ) {
		return $details->$key ?? null;
	}
	return null;
}

/** Find a published post id by raw slug, bypassing WPML language filtering. */
function estecapelli_blog_i18n_raw_post_id( $slug, $exclude_id = 0 ) {
	global $wpdb;
	if ( $exclude_id ) {
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				 WHERE post_name = %s AND post_type = 'post' AND post_status <> 'trash' AND ID <> %d
				 ORDER BY ID ASC LIMIT 1",
				$slug,
				(int) $exclude_id
			)
		);
	}
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = 'post' AND post_status <> 'trash'
			 ORDER BY ID ASC LIMIT 1",
			$slug
		)
	);
}

/** Write Rank Math meta description + focus keyword for one post. */
function estecapelli_blog_i18n_apply_seo( $post_id, $english_slug, $lang ) {
	$entry = estecapelli_blog_i18n_entry( $english_slug, $lang );
	if ( ! $entry ) {
		return;
	}
	if ( ! empty( $entry['description'] ) ) {
		update_post_meta( $post_id, 'rank_math_description', $entry['description'] );
	}
	if ( ! empty( $entry['focus_keyword'] ) ) {
		update_post_meta( $post_id, 'rank_math_focus_keyword', $entry['focus_keyword'] );
	}
}

/**
 * Layer Rank Math SEO meta onto every published English article.
 *
 * @return int Number of English posts updated.
 */
function estecapelli_blog_i18n_apply_en_seo() {
	$updated = 0;
	foreach ( estecapelli_blog_i18n_meta() as $english_slug => $langs ) {
		if ( empty( $langs['en'] ) ) {
			continue;
		}
		$post = get_page_by_path( $english_slug, OBJECT, 'post' );
		if ( ! $post ) {
			continue;
		}
		estecapelli_blog_i18n_apply_seo( (int) $post->ID, $english_slug, 'en' );
		$updated++;
	}
	return $updated;
}

/**
 * Import (or repair) one translated blog post and its WPML relationship.
 *
 * @param string $lang         Indexed language code (fr/tr/it/es/pl/pt).
 * @param string $english_slug English source slug.
 * @return int|WP_Error Translated post ID.
 */
function estecapelli_blog_i18n_import_one( $lang, $english_slug ) {
	$target_slug  = estecapelli_blog_i18n_slug( $english_slug, $lang );
	$entry        = estecapelli_blog_i18n_entry( $english_slug, $lang );
	if ( '' === $target_slug || ! $entry ) {
		return new WP_Error( 'blog_i18n_unknown', sprintf( 'No %s translation is defined for %s.', strtoupper( $lang ), $english_slug ) );
	}
	$target_title = (string) $entry['title'];

	// Public/indexed code ($lang: pt) drives slugs, bodies and SEO; the WPML
	// engine may store Portuguese under the built-in "pt-pt" code, so every
	// relationship call must use the resolved WPML code instead.
	$wpml_lang = estecapelli_wpml_language_code( $lang );

	$body = estecapelli_blog_i18n_body( $lang, $english_slug );
	if ( is_wp_error( $body ) ) {
		return $body;
	}

	$source_id = estecapelli_source_post_id( $english_slug, 'post' );
	if ( ! $source_id ) {
		return new WP_Error( 'blog_i18n_missing_source', sprintf( 'Published English post not found: %s.', $english_slug ) );
	}
	$source_post = get_post( $source_id );
	if ( ! $source_post ) {
		return new WP_Error( 'blog_i18n_invalid_source', sprintf( 'English post could not be loaded: %s.', $english_slug ) );
	}

	$element_type   = apply_filters( 'wpml_element_type', 'post' );
	$source_details = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => $source_id,
			'element_type' => 'post',
		)
	);
	$trid            = (int) estecapelli_blog_i18n_detail( $source_details, 'trid' );
	$source_language = (string) estecapelli_blog_i18n_detail( $source_details, 'language_code' );
	if ( ! $trid || 'en' !== $source_language ) {
		return new WP_Error( 'blog_i18n_unlinked_source', sprintf( 'WPML language details are missing for %s.', $english_slug ) );
	}

	$target_id = estecapelli_blog_i18n_raw_post_id( $target_slug, $source_id );
	if ( ! $target_id ) {
		$target_id = estecapelli_wpml_group_element_id_raw( $trid, $element_type, $wpml_lang );
	}
	if ( $target_id ) {
		$raw_target = get_post( $target_id );
		if ( ! $raw_target || 'post' !== $raw_target->post_type || 'trash' === $raw_target->post_status || $target_id === $source_id ) {
			estecapelli_wpml_delete_relationship_raw( $target_id, $element_type, $trid, $wpml_lang );
			$target_id = 0;
		}
	}
	if ( ! $target_id ) {
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'post', false, $wpml_lang );
	}
	if ( $target_id === $source_id ) {
		$target_id = 0;
	}
	if ( ! $target_id ) {
		$target_id = estecapelli_blog_i18n_raw_post_id( $target_slug, $source_id );
	}

	if ( $target_id ) {
		delete_post_meta( $target_id, '_icl_lang_duplicate_of' );
	}

	$post_args = array(
		'post_type'    => 'post',
		'post_status'  => 'publish',
		'post_title'   => $target_title,
		'post_name'    => $target_slug,
		'post_content' => $body,
		'post_author'  => (int) $source_post->post_author,
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
			'language_code'        => $wpml_lang,
			'source_language_code' => $source_language,
			'check_duplicates'     => false,
		)
	);
	delete_post_meta( $target_id, '_icl_lang_duplicate_of' );

	$forced = estecapelli_wpml_replace_language_slot_raw( $target_id, $element_type, $trid, $wpml_lang, $source_language );
	if ( ! $forced ) {
		$reason = estecapelli_wpml_last_slot_error();
		return new WP_Error(
			'blog_i18n_force_link_failed',
			sprintf(
				'The %s WPML relationship could not be rebuilt for %s (English #%d, %s #%d, trid %d)%s',
				strtoupper( $lang ),
				$english_slug,
				(int) $source_id,
				strtoupper( $lang ),
				(int) $target_id,
				(int) $trid,
				$reason ? ' — ' . $reason : '.'
			)
		);
	}

	$linked_target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'post', false, $wpml_lang );
	if ( (int) $target_id !== $linked_target_id && ! estecapelli_wpml_element_matches_raw( $target_id, $element_type, $trid, $wpml_lang ) ) {
		$repaired = estecapelli_wpml_repair_relationship_raw( $target_id, $element_type, $trid, $wpml_lang, $source_language );
		if ( ! $repaired ) {
			return new WP_Error( 'blog_i18n_link_failed', sprintf( 'WPML did not link the %s post for %s.', strtoupper( $lang ), $english_slug ) );
		}
	}

	// Re-apply the canonical title after WPML linking.
	$updated = wp_update_post(
		array(
			'ID'         => (int) $target_id,
			'post_title' => $target_title,
		),
		true
	);
	if ( is_wp_error( $updated ) ) {
		return $updated;
	}

	// Force the exact indexed slug directly. wp_update_post() runs post_name
	// through sanitize_title(), which strips the leading dash a few indexed
	// Spanish slugs legitimately carry (e.g. "-es-doloroso-el-trasplante-capilar"),
	// so the stored slug would never match the frozen live-URL contract and the
	// indexed router would 404 that path. Write it raw — but only after
	// confirming no *other* post already owns that exact slug.
	$target_post = get_post( $target_id );
	if ( ! $target_post ) {
		return new WP_Error( 'blog_i18n_missing_target', sprintf( 'The imported %s post could not be reloaded for %s.', strtoupper( $lang ), $english_slug ) );
	}
	if ( $target_slug !== $target_post->post_name ) {
		$conflict = estecapelli_blog_i18n_raw_post_id( $target_slug, (int) $target_id );
		if ( $conflict ) {
			return new WP_Error( 'blog_i18n_slug_conflict', sprintf( 'The required %s slug is already in use by another post: %s.', strtoupper( $lang ), $target_slug ) );
		}
		global $wpdb;
		$wpdb->update( $wpdb->posts, array( 'post_name' => $target_slug ), array( 'ID' => (int) $target_id ) );
		clean_post_cache( (int) $target_id );
	}

	// Mirror the English post's categories, mapped to their translated equivalents.
	$source_terms = wp_get_post_terms( $source_id, 'category', array( 'fields' => 'ids' ) );
	if ( ! is_wp_error( $source_terms ) && $source_terms ) {
		$mapped = array();
		foreach ( $source_terms as $term_id ) {
			$t = (int) apply_filters( 'wpml_object_id', (int) $term_id, 'category', false, $wpml_lang );
			if ( $t ) {
				$mapped[] = $t;
			}
		}
		if ( $mapped ) {
			add_filter( 'wpml_disable_term_adjust_id', '__return_true' );
			wp_set_post_terms( $target_id, $mapped, 'category', false );
			remove_filter( 'wpml_disable_term_adjust_id', '__return_true' );
		}
	}

	// Share the English featured image when the translation has none of its own.
	$thumbnail_id = get_post_thumbnail_id( $source_id );
	if ( $thumbnail_id && ! get_post_thumbnail_id( $target_id ) ) {
		set_post_thumbnail( $target_id, $thumbnail_id );
	}

	estecapelli_blog_i18n_apply_seo( (int) $target_id, $english_slug, $lang );

	return (int) $target_id;
}

/** Find a published page id by raw slug, bypassing WPML language filtering. */
function estecapelli_blog_i18n_raw_page_id( $slug, $exclude_id = 0 ) {
	global $wpdb;
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

/**
 * Ensure the blog landing page exists in one language and is WPML-linked.
 *
 * The blog landing is a section-less `page` at slug "blog" routed to
 * page-blog.php. Without a translated copy at the same slug, /{lang}/blog 404s.
 * The English original is the source of truth; only the WPML link + slug matter.
 *
 * @param string $lang Indexed language code (fr/tr/it/es/pl/pt).
 * @return int|WP_Error Translated blog page ID.
 */
function estecapelli_blog_i18n_ensure_landing_page( $lang ) {
	$wpml_lang    = estecapelli_wpml_language_code( $lang );
	$target_slug  = 'blog';
	$target_title = 'Blog';

	$source_id = estecapelli_source_post_id( 'blog', 'page' );
	if ( ! $source_id ) {
		return new WP_Error( 'blog_i18n_missing_landing_source', 'Published English blog landing page not found.' );
	}
	$source_post = get_post( $source_id );
	if ( ! $source_post ) {
		return new WP_Error( 'blog_i18n_invalid_landing_source', 'English blog landing page could not be loaded.' );
	}

	$element_type   = apply_filters( 'wpml_element_type', 'page' );
	$source_details = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => $source_id,
			'element_type' => 'page',
		)
	);
	$trid            = (int) estecapelli_blog_i18n_detail( $source_details, 'trid' );
	$source_language = (string) estecapelli_blog_i18n_detail( $source_details, 'language_code' );
	if ( ! $trid || 'en' !== $source_language ) {
		return new WP_Error( 'blog_i18n_landing_unlinked', 'WPML language details are missing for the blog landing page.' );
	}

	// Resolve the existing translation by WPML relationship first — the "blog"
	// slug is shared across languages, so a raw slug lookup could grab another
	// language's landing page. Only fall back to a raw lookup last.
	$target_id = estecapelli_wpml_group_element_id_raw( $trid, $element_type, $wpml_lang );
	if ( ! $target_id ) {
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, $wpml_lang );
	}
	if ( $target_id === $source_id ) {
		$target_id = 0;
	}
	if ( $target_id ) {
		$raw_target = get_post( $target_id );
		if ( ! $raw_target || 'page' !== $raw_target->post_type || 'trash' === $raw_target->post_status ) {
			estecapelli_wpml_delete_relationship_raw( $target_id, $element_type, $trid, $wpml_lang );
			$target_id = 0;
		}
	}

	if ( $target_id ) {
		delete_post_meta( $target_id, '_icl_lang_duplicate_of' );
	}

	$post_args = array(
		'post_type'    => 'page',
		'post_title'   => $target_title,
		'post_name'    => $target_slug,
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

	// Mirror any assigned page template (page-blog.php also applies by slug).
	$page_template = get_page_template_slug( $source_id );
	if ( $page_template ) {
		update_post_meta( (int) $target_id, '_wp_page_template', $page_template );
	}

	do_action(
		'wpml_set_element_language_details',
		array(
			'element_id'           => (int) $target_id,
			'element_type'         => $element_type,
			'trid'                 => $trid,
			'language_code'        => $wpml_lang,
			'source_language_code' => $source_language,
			'check_duplicates'     => false,
		)
	);
	delete_post_meta( $target_id, '_icl_lang_duplicate_of' );

	$forced = estecapelli_wpml_replace_language_slot_raw( $target_id, $element_type, $trid, $wpml_lang, $source_language );
	if ( ! $forced ) {
		$reason = estecapelli_wpml_last_slot_error();
		return new WP_Error(
			'blog_i18n_landing_force_link_failed',
			sprintf( 'The %s WPML relationship could not be rebuilt for the blog landing page%s', strtoupper( $lang ), $reason ? ' — ' . $reason : '.' )
		);
	}

	$linked_target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, $wpml_lang );
	if ( (int) $target_id !== $linked_target_id && ! estecapelli_wpml_element_matches_raw( $target_id, $element_type, $trid, $wpml_lang ) ) {
		$repaired = estecapelli_wpml_repair_relationship_raw( $target_id, $element_type, $trid, $wpml_lang, $source_language );
		if ( ! $repaired ) {
			return new WP_Error( 'blog_i18n_landing_link_failed', sprintf( 'WPML did not link the %s blog landing page.', strtoupper( $lang ) ) );
		}
	}

	// Re-apply the canonical slug/title; force it raw if WordPress deduped it
	// (the "blog" slug is shared across every language).
	wp_update_post(
		array(
			'ID'          => (int) $target_id,
			'post_title'  => $target_title,
			'post_name'   => $target_slug,
			'post_parent' => 0,
		),
		true
	);
	$target_post = get_post( $target_id );
	if ( ! $target_post ) {
		return new WP_Error( 'blog_i18n_landing_missing', sprintf( 'The %s blog landing page could not be reloaded.', strtoupper( $lang ) ) );
	}
	if ( $target_slug !== $target_post->post_name ) {
		global $wpdb;
		$wpdb->update( $wpdb->posts, array( 'post_name' => $target_slug ), array( 'ID' => (int) $target_id ) );
		clean_post_cache( (int) $target_id );
	}

	return (int) $target_id;
}

/**
 * Run the complete unified import: English SEO + every translated article.
 *
 * @return array{en:int,imported:array<string,int>}|WP_Error
 */
function estecapelli_run_blog_i18n_import() {
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'blog_i18n_wpml_missing', 'WPML is required to import the blog translations.' );
	}

	$active = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active ) ) {
		$active = array();
	}

	$en_updated = estecapelli_blog_i18n_apply_en_seo();
	$imported   = array();

	foreach ( estecapelli_blog_i18n_languages() as $lang ) {
		// Portuguese may be active under the built-in "pt-pt" code, so test the
		// resolved WPML code, not the public one.
		$wpml_lang = estecapelli_wpml_language_code( $lang );
		if ( ! isset( $active[ $lang ] ) && ! isset( $active[ $wpml_lang ] ) ) {
			continue; // Skip languages that are not active in WPML yet.
		}

		// The blog landing page must exist in this language first so /{lang}/blog resolves.
		$landing = estecapelli_blog_i18n_ensure_landing_page( $lang );
		if ( is_wp_error( $landing ) ) {
			return new WP_Error(
				$landing->get_error_code(),
				sprintf( '%s blog landing: %s', $lang, $landing->get_error_message() )
			);
		}
		$imported[ $lang . '/__landing__' ] = $landing;

		foreach ( estecapelli_indexed_blog_slugs() as $english_slug => $langs ) {
			if ( empty( $langs[ $lang ] ) ) {
				continue; // No indexed copy of this article in this language.
			}
			$result = estecapelli_blog_i18n_import_one( $lang, $english_slug );
			if ( is_wp_error( $result ) ) {
				return new WP_Error(
					$result->get_error_code(),
					sprintf( '%s/%s: %s', $lang, $english_slug, $result->get_error_message() )
				);
			}
			$imported[ $lang . '/' . $english_slug ] = $result;
		}
	}

	return array( 'en' => $en_updated, 'imported' => $imported );
}

add_action( 'admin_post_estecapelli_import_blog_i18n', 'estecapelli_handle_blog_i18n_manual_import' );
/** Process the manual "Import all blog translations + SEO" button. */
function estecapelli_handle_blog_i18n_manual_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import posts.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_import_blog_i18n' );

	$result = estecapelli_run_blog_i18n_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_blog_i18n_import_error', $result->get_error_message(), false );
	} else {
		delete_option( 'estecapelli_blog_i18n_import_error' );
		set_transient(
			'estecapelli_blog_i18n_import_success',
			sprintf( '%d translated posts, %d English SEO entries', count( $result['imported'] ), (int) $result['en'] ),
			5 * MINUTE_IN_SECONDS
		);
	}

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-treatment-importer', admin_url( 'tools.php' ) ) );
	exit;
}

add_action( 'admin_post_estecapelli_import_blog_i18n_one', 'estecapelli_handle_blog_i18n_single_import' );
/** Process a single-language, single-article Import / Repair button. */
function estecapelli_handle_blog_i18n_single_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import posts.', 'estecapelli' ) );
	}
	$lang = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : '';
	$slug = isset( $_GET['source'] ) ? sanitize_title( wp_unslash( $_GET['source'] ) ) : '';
	check_admin_referer( 'estecapelli_import_blog_i18n_one_' . $lang . '_' . $slug );

	$result = estecapelli_blog_i18n_import_one( $lang, $slug );
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_blog_i18n_import_error', $lang . '/' . $slug . ': ' . $result->get_error_message(), false );
	} else {
		delete_option( 'estecapelli_blog_i18n_import_error' );
		set_transient( 'estecapelli_blog_i18n_import_success', sprintf( '%s / %s imported', $lang, $slug ), 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-treatment-importer', admin_url( 'tools.php' ) ) );
	exit;
}

add_action( 'admin_init', 'estecapelli_maybe_import_blog_i18n', 92 );
/** Auto-run once per deployment version. Failed runs remain retryable. */
function estecapelli_maybe_import_blog_i18n() {
	if (
		get_option( 'estecapelli_blog_i18n_import_version' ) === ESTECAPELLI_BLOG_I18N_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_blog_i18n_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_blog_i18n_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_blog_i18n_import_version', ESTECAPELLI_BLOG_I18N_IMPORT_VERSION, false );
	delete_option( 'estecapelli_blog_i18n_import_error' );
	set_transient(
		'estecapelli_blog_i18n_import_success',
		sprintf( '%d translated posts, %d English SEO entries', count( $result['imported'] ), (int) $result['en'] ),
		5 * MINUTE_IN_SECONDS
	);
}

add_action( 'admin_notices', 'estecapelli_blog_i18n_import_notice' );
/** Report the import outcome to administrators. */
function estecapelli_blog_i18n_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success = get_transient( 'estecapelli_blog_i18n_import_success' );
	if ( false !== $success ) {
		delete_transient( 'estecapelli_blog_i18n_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s %s</p></div>',
			esc_html__( 'Blog translations + SEO imported:', 'estecapelli' ),
			esc_html( (string) $success )
		);
		return;
	}

	$error = get_option( 'estecapelli_blog_i18n_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html__( 'Blog translation import could not finish.', 'estecapelli' ),
			esc_html( $error )
		);
	}
}
