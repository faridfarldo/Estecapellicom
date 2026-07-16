<?php
/**
 * French importer for blog posts (post_type = post).
 *
 * Mirrors the page/treatment translation importers, but a blog post carries its
 * body in post_content (read from a version-controlled HTML file) rather than
 * ACF sections. The English post stays the source of truth; each French post is
 * force-linked to its English original via the shared WPML engine. The existing
 * slug sweep (estecapelli_slug_map_posts) settles the canonical French slug.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_FR_BLOG_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_FR_BLOG_IMPORT_VERSION', '2026-07-16.1' );
}

/**
 * English post slug => French slug + French title.
 *
 * Bodies live in /inc/data/blog/fr/{english-slug}.html. The "complete expert
 * guide" is intentionally excluded — the indexed URL contract defines no French
 * slug for it.
 *
 * @return array<string,array{slug:string,title:string}>
 */
function estecapelli_fr_blog_manifest() {
	return array(
		'unshaven-hair-transplant-for-women' => array(
			'slug'  => 'greffe-de-cheveux-sans-rasage-chez-la-femme',
			'title' => 'Greffe de cheveux sans rasage chez la femme',
		),
		'unshaven-hair-transplant' => array(
			'slug'  => 'greffe-de-cheveux-sans-rasage',
			'title' => 'Greffe de cheveux sans rasage',
		),
		'can-diabetic-patients-undergo-a-hair-transplant' => array(
			'slug'  => 'les-patients-diabetiques-peuvent-ils-subir-une-greffe-de-cheveux',
			'title' => 'Les patients diabétiques peuvent-ils subir une greffe de cheveux ?',
		),
		'is-hair-transplant-a-painful-procedure' => array(
			'slug'  => 'la-greffe-de-cheveux-est-elle-une-procedure-douloureuse',
			'title' => 'La greffe de cheveux est-elle une procédure douloureuse ?',
		),
		'will-my-hair-fall-out-again-after-a-hair-transplant' => array(
			'slug'  => 'mes-cheveux-vont-ils-retomber-apres-une-greffe-de-cheveux',
			'title' => 'Mes cheveux vont-ils retomber après une greffe de cheveux ?',
		),
		'hair-transplant-with-the-fue-vita-technique' => array(
			'slug'  => 'greffe-de-cheveux-avec-la-technique-fue-vita',
			'title' => 'Greffe de cheveux avec la technique FUE Vita',
		),
		'hair-transplant-for-hiv-positive-patients' => array(
			'slug'  => 'greffe-de-cheveux-pour-les-patients-seropositifs-vih',
			'title' => 'Greffe de cheveux pour les patients séropositifs (VIH)',
		),
		'hair-transplant-for-hiv-positive-patients-in-turkey' => array(
			'slug'  => 'greffe-de-cheveux-pour-patients-seropositifs-en-turquie',
			'title' => 'Greffe de cheveux pour patients séropositifs en Turquie',
		),
	);
}

/**
 * Read and lightly sanitise one French blog body from its HTML file.
 *
 * @param string $source_slug English post slug.
 * @return string|WP_Error
 */
function estecapelli_fr_blog_body( $source_slug ) {
	$file = get_template_directory() . '/inc/data/blog/fr/' . $source_slug . '.html';
	if ( ! is_readable( $file ) ) {
		return new WP_Error( 'fr_blog_missing_body', sprintf( 'Missing French blog body: %s', basename( $file ) ) );
	}
	$html = (string) file_get_contents( $file );
	if ( '' === trim( $html ) ) {
		return new WP_Error( 'fr_blog_empty_body', sprintf( 'Empty French blog body: %s', basename( $file ) ) );
	}
	return wp_kses_post( $html );
}

/**
 * Find a post id by raw slug, bypassing WPML language filtering.
 *
 * @param string $slug       Post slug.
 * @param int    $exclude_id Optional id to exclude.
 * @return int
 */
function estecapelli_fr_blog_raw_post_id( $slug, $exclude_id = 0 ) {
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

/**
 * Force-import one French blog post.
 *
 * @param string $source_slug English post slug.
 * @return int|WP_Error French post ID.
 */
function estecapelli_fr_blog_import_one( $source_slug ) {
	$manifest = estecapelli_fr_blog_manifest();
	if ( ! isset( $manifest[ $source_slug ] ) ) {
		return new WP_Error( 'fr_blog_unknown', sprintf( 'Unknown French blog post: %s.', $source_slug ) );
	}
	$french_slug  = $manifest[ $source_slug ]['slug'];
	$french_title = $manifest[ $source_slug ]['title'];

	$body = estecapelli_fr_blog_body( $source_slug );
	if ( is_wp_error( $body ) ) {
		return $body;
	}

	$source_id = estecapelli_source_post_id( $source_slug, 'post' );
	if ( ! $source_id ) {
		return new WP_Error( 'fr_blog_missing_source_post', sprintf( 'Published English post not found: %s.', $source_slug ) );
	}
	$source_post = get_post( $source_id );
	if ( ! $source_post ) {
		return new WP_Error( 'fr_blog_invalid_source_post', sprintf( 'English post could not be loaded: %s.', $source_slug ) );
	}

	$element_type    = apply_filters( 'wpml_element_type', 'post' );
	$source_details  = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => $source_id,
			'element_type' => 'post',
		)
	);
	$trid            = (int) estecapelli_fr_hair_detail( $source_details, 'trid' );
	$source_language = (string) estecapelli_fr_hair_detail( $source_details, 'language_code' );
	if ( ! $trid || 'en' !== $source_language ) {
		return new WP_Error( 'fr_blog_unlinked_source_post', sprintf( 'WPML language details are missing for %s.', $source_slug ) );
	}

	$target_id = estecapelli_fr_blog_raw_post_id( $french_slug, $source_id );
	if ( ! $target_id ) {
		$target_id = estecapelli_wpml_group_element_id_raw( $trid, $element_type, 'fr' );
	}
	if ( $target_id ) {
		$raw_target = get_post( $target_id );
		if ( ! $raw_target || 'post' !== $raw_target->post_type || 'trash' === $raw_target->post_status || $target_id === $source_id ) {
			estecapelli_wpml_delete_relationship_raw( $target_id, $element_type, $trid, 'fr' );
			$target_id = 0;
		}
	}
	if ( ! $target_id ) {
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'post', false, 'fr' );
	}
	if ( $target_id === $source_id ) {
		$target_id = 0;
	}
	if ( ! $target_id ) {
		$target_id = estecapelli_fr_blog_raw_post_id( $french_slug, $source_id );
	}

	if ( $target_id ) {
		delete_post_meta( $target_id, '_icl_lang_duplicate_of' );
	}

	$post_args = array(
		'post_type'    => 'post',
		'post_status'  => 'publish',
		'post_title'   => $french_title,
		'post_name'    => $french_slug,
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
			'fr_blog_force_link_failed',
			sprintf(
				'The French WPML relationship could not be rebuilt for %s (English post #%d, French post #%d, trid %d)%s',
				$source_slug,
				(int) $source_id,
				(int) $target_id,
				(int) $trid,
				$reason ? ' — ' . $reason : '.'
			)
		);
	}

	$linked_target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'post', false, 'fr' );
	if ( (int) $target_id !== $linked_target_id && ! estecapelli_wpml_element_matches_raw( $target_id, $element_type, $trid, 'fr' ) ) {
		$repaired = estecapelli_wpml_repair_relationship_raw( $target_id, $element_type, $trid, 'fr', $source_language );
		if ( ! $repaired ) {
			return new WP_Error( 'fr_blog_link_failed', sprintf( 'WPML did not link the French post for %s.', $source_slug ) );
		}
	}

	// Re-apply the canonical French slug/title after WPML linking.
	$target_id = wp_update_post(
		array(
			'ID'         => (int) $target_id,
			'post_title' => $french_title,
			'post_name'  => $french_slug,
		),
		true
	);
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}
	$target_post = get_post( $target_id );
	if ( ! $target_post || $french_slug !== $target_post->post_name ) {
		return new WP_Error( 'fr_blog_slug_conflict', sprintf( 'The required French slug is already in use: %s.', $french_slug ) );
	}

	// Mirror the English post's categories, mapped to their French equivalents.
	$source_terms = wp_get_post_terms( $source_id, 'category', array( 'fields' => 'ids' ) );
	if ( ! is_wp_error( $source_terms ) && $source_terms ) {
		$french_terms = array();
		foreach ( $source_terms as $term_id ) {
			$fr_term = (int) apply_filters( 'wpml_object_id', (int) $term_id, 'category', false, 'fr' );
			if ( $fr_term ) {
				$french_terms[] = $fr_term;
			}
		}
		if ( $french_terms ) {
			add_filter( 'wpml_disable_term_adjust_id', '__return_true' );
			wp_set_post_terms( $target_id, $french_terms, 'category', false );
			remove_filter( 'wpml_disable_term_adjust_id', '__return_true' );
		}
	}

	$thumbnail_id = get_post_thumbnail_id( $source_id );
	if ( $thumbnail_id ) {
		set_post_thumbnail( $target_id, $thumbnail_id );
	}

	return (int) $target_id;
}

/**
 * Run the complete French blog import.
 *
 * @return array<string,int>|WP_Error Source slug => French post ID.
 */
function estecapelli_run_fr_blog_import() {
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'fr_blog_wpml_missing', 'WPML is required for the French blog import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['fr'] ) ) {
		return new WP_Error( 'fr_blog_french_inactive', 'French must be active in WPML before importing the blog translations.' );
	}

	$imported = array();
	foreach ( array_keys( estecapelli_fr_blog_manifest() ) as $source_slug ) {
		$result = estecapelli_fr_blog_import_one( $source_slug );
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

add_action( 'admin_post_estecapelli_import_fr_blog', 'estecapelli_handle_fr_blog_manual_import' );
/** Process an individual manual French blog import button. */
function estecapelli_handle_fr_blog_manual_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import posts.', 'estecapelli' ) );
	}

	$source_slug = isset( $_GET['source'] ) ? sanitize_title( wp_unslash( $_GET['source'] ) ) : '';
	check_admin_referer( 'estecapelli_import_fr_blog_' . $source_slug );
	$result = estecapelli_fr_blog_import_one( $source_slug );
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_fr_blog_import_error', $source_slug . ': ' . $result->get_error_message(), false );
	} else {
		delete_option( 'estecapelli_fr_blog_import_error' );
		set_transient( 'estecapelli_fr_blog_import_success', 1, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect(
		add_query_arg( 'page', 'estecapelli-treatment-importer', admin_url( 'tools.php' ) )
	);
	exit;
}

add_action( 'admin_init', 'estecapelli_maybe_import_fr_blog', 90 );
/**
 * Run once after deployment. Failed runs remain retryable.
 */
function estecapelli_maybe_import_fr_blog() {
	if (
		get_option( 'estecapelli_fr_blog_import_version' ) === ESTECAPELLI_FR_BLOG_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_fr_blog_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_fr_blog_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_fr_blog_import_version', ESTECAPELLI_FR_BLOG_IMPORT_VERSION, false );
	delete_option( 'estecapelli_fr_blog_import_error' );
	set_transient( 'estecapelli_fr_blog_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_fr_blog_import_notice' );
/**
 * Show an actionable import result to administrators.
 */
function estecapelli_fr_blog_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_fr_blog_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_fr_blog_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'French blog translations imported successfully: %d posts.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_fr_blog_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'French blog import could not finish.' ),
			esc_html( $error )
		);
	}
}
