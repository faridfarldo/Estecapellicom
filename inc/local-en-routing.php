<?php
/**
 * /en/ routing for the default (English) language.
 *
 * WPML serves the non-default languages from their own directories (/fr/, /it/…)
 * reliably, but its "Use directory for default language" routing does NOT engage
 * on this install: /en/ URLs 404 even though WPML generates them (hreflang,
 * internal links). Rather than fight that flaky feature, the theme owns the /en/
 * prefix for English — it intercepts /en/{path} at the request stage (before the
 * rewrite rules decide the query) and resolves it to the matching page, blog
 * post, treatment or doctor.
 *
 * Works whether or not WPML is active. It only ever touches paths under /en/, so
 * WPML's /fr/, /it/, … handling is untouched. No rewrite flush, no .htaccess.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve /en/{path} to the right content before rewrite rules run.
 *
 * Priority 20 so it runs after WPML's own request handling and has the final say
 * on the query vars for /en/ URLs.
 */
add_filter( 'request', 'estecapelli_en_request', 20 );
function estecapelli_en_request( $query_vars ) {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path = trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );

	if ( 'en' !== $path && 0 !== strpos( $path, 'en/' ) ) {
		return $query_vars; // Not an /en/ URL — leave it for WP/WPML.
	}

	$rest = trim( substr( $path, 2 ), '/' );
	if ( '' === $rest ) {
		return array(); // bare /en/ — serve the front page.
	}

	// Preview links carry the post ID and a post-specific nonce. Resolve that ID
	// before the public slug lookups below, which intentionally accept published
	// content only. This keeps drafts private while allowing their real /en/ URL
	// to render in WordPress Preview.
	$preview_id = estecapelli_en_preview_post_id();
	if ( $preview_id ) {
		$preview_post = get_post( $preview_id );
		if ( $preview_post && in_array( $preview_post->post_type, array( 'post', 'page', 'treatment', 'doctor' ), true ) ) {
			unset( $query_vars['name'], $query_vars['pagename'], $query_vars['attachment'], $query_vars['attachment_id'] );
			$query_vars['preview'] = 'true';
			if ( 'page' === $preview_post->post_type ) {
				unset( $query_vars['p'], $query_vars['post_type'] );
				$query_vars['page_id'] = $preview_id;
			} else {
				unset( $query_vars['page_id'] );
				$query_vars['p']         = $preview_id;
				$query_vars['post_type'] = $preview_post->post_type;
			}
			return $query_vars;
		}
	}

	// 1) A real page — covers top-level and nested pages (contact, about-us,
	//    about-us/our-team, hair-transplant, hair-transplant/tricholab, …).
	$page = get_page_by_path( $rest );
	if ( $page ) {
		return array( 'page_id' => $page->ID );
	}

	// 2) Blog post at blog/{slug}.
	if ( 0 === strpos( $rest, 'blog/' ) ) {
		$slug = trim( substr( $rest, 5 ), '/' );
		if ( '' !== $slug ) {
			$id = estecapelli_post_id_by_slug( $slug, 'post' );
			if ( $id ) {
				return array( 'p' => $id );
			}
		}
	}

	$parts = explode( '/', $rest );

	// 3) Doctor profile at about-us/our-doctors/{slug}.
	if ( 3 === count( $parts ) && 'about-us' === $parts[0] && 'our-doctors' === $parts[1] ) {
		$id = estecapelli_post_id_by_slug( $parts[2], 'doctor' );
		if ( $id ) {
			return array( 'p' => $id, 'post_type' => 'doctor' );
		}
	}

	// 4) Treatment at {category}/{service} (e.g. hair-transplant/sapphire-fue-…).
	if ( 2 === count( $parts ) ) {
		$id = estecapelli_post_id_by_slug( $parts[1], 'treatment' );
		if ( $id ) {
			return array( 'p' => $id, 'post_type' => 'treatment' );
		}
	}

	// Nothing matched — let it fall through (e.g. before-after/{item}, which the
	// legacy redirect handler sends to the gallery).
	return $query_vars;
}

/**
 * Return the post ID from a valid WordPress preview request.
 *
 * WordPress creates the nonce with the `post_preview_{ID}` action. Requiring the
 * same nonce and edit capability prevents an /en/ preview URL from becoming a
 * way to read drafts without permission.
 *
 * @return int Valid preview post ID, or 0.
 */
function estecapelli_en_preview_post_id() {
	if ( empty( $_GET['preview'] ) || ! is_scalar( $_GET['preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return 0;
	}

	$preview = strtolower( sanitize_text_field( wp_unslash( $_GET['preview'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! in_array( $preview, array( '1', 'true' ), true ) ) {
		return 0;
	}

	$preview_id = 0;
	if ( isset( $_GET['preview_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$preview_id = absint( $_GET['preview_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	} elseif ( isset( $_GET['p'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$preview_id = absint( $_GET['p'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	if ( ! $preview_id || empty( $_GET['preview_nonce'] ) || ! is_scalar( $_GET['preview_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return 0;
	}

	$nonce = sanitize_text_field( wp_unslash( $_GET['preview_nonce'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! wp_verify_nonce( $nonce, 'post_preview_' . $preview_id ) || ! current_user_can( 'edit_post', $preview_id ) ) {
		return 0;
	}

	return $preview_id;
}

/**
 * Return the ID of a published post of this type with this slug, or 0.
 *
 * Queried straight against the database on purpose, then handed back to WP as an
 * ID (?p=ID). Slug-based lookups/queries are re-filtered by WPML's language in
 * the /en/ request context and come back empty for these custom types — but an
 * explicit post ID loads reliably, exactly as page_id does for pages.
 */
function estecapelli_post_id_by_slug( $slug, $post_type ) {
	global $wpdb;
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = %s AND post_status = 'publish' LIMIT 1",
			$slug,
			$post_type
		)
	);
}

/**
 * Keep the browser on the /en/ URL — without this, WordPress would 301
 * /en/contact to its bare canonical /contact/.
 */
add_filter( 'redirect_canonical', 'estecapelli_en_no_canonical', 10, 2 );
function estecapelli_en_no_canonical( $redirect_url, $requested_url ) {
	if ( preg_match( '#^https?://[^/]+/en(/|$)#', $requested_url ) ) {
		return false;
	}
	return $redirect_url;
}

/**
 * Redirect bare English blog URLs to the indexed /en/ contract.
 *
 * Public URLs use a permanent redirect. A valid draft preview uses a temporary
 * redirect so browsers and proxies never cache its nonce-bearing URL as a
 * permanent public location.
 */
add_action( 'template_redirect', 'estecapelli_redirect_bare_english_blog', -10 );
function estecapelli_redirect_bare_english_blog() {
	if ( is_admin() ) {
		return;
	}

	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path    = untrailingslashit( (string) wp_parse_url( $request, PHP_URL_PATH ) );
	if ( '/blog' !== $path && ! preg_match( '#^/blog/([^/]+)$#', $path, $match ) ) {
		return;
	}

	$preview_id = estecapelli_en_preview_post_id();
	if ( isset( $match[1] ) ) {
		$slug    = sanitize_title( rawurldecode( $match[1] ) );
		$post_id = estecapelli_post_id_by_slug( $slug, 'post' );
		if ( ! $post_id && ! $preview_id ) {
			return;
		}
		if ( $preview_id ) {
			$preview_post = get_post( $preview_id );
			if ( ! $preview_post || 'post' !== $preview_post->post_type || ( $preview_post->post_name && $slug !== $preview_post->post_name ) ) {
				return;
			}
		}

		// A bare URL must never steal a translated post from its own language.
		$language = $post_id
			? (string) apply_filters(
				'wpml_element_language_code',
				null,
				array( 'element_id' => $post_id, 'element_type' => 'post_post' )
			)
			: '';
		if ( $language && 'en' !== estecapelli_indexed_language_code( $language ) ) {
			return;
		}
	} elseif ( $preview_id ) {
		$preview_post = get_post( $preview_id );
		if ( ! $preview_post || 'page' !== $preview_post->post_type || 'blog' !== $preview_post->post_name ) {
			return;
		}
	}

	$query  = (string) wp_parse_url( $request, PHP_URL_QUERY );
	$target = estecapelli_unfiltered_home_url() . '/en' . $path;
	if ( $query ) {
		$target .= '?' . $query;
	}

	$is_preview_request = isset( $_GET['preview'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	wp_safe_redirect( $target, $is_preview_request ? 302 : 301 );
	exit;
}
