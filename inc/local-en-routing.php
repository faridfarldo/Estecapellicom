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
			$post = get_page_by_path( $slug, OBJECT, 'post' );
			if ( $post ) {
				return array( 'p' => $post->ID );
			}
		}
	}

	$parts = explode( '/', $rest );

	// 3) Doctor profile at about-us/our-doctors/{slug}.
	if ( 3 === count( $parts ) && 'about-us' === $parts[0] && 'our-doctors' === $parts[1] ) {
		$doctor = get_page_by_path( $parts[2], OBJECT, 'doctor' );
		if ( $doctor ) {
			return array( 'doctor' => $parts[2] );
		}
	}

	// 4) Treatment at {category}/{service} (e.g. hair-transplant/sapphire-fue-…).
	if ( 2 === count( $parts ) ) {
		$treatment = get_page_by_path( $parts[1], OBJECT, 'treatment' );
		if ( $treatment ) {
			return array( 'treatment' => $parts[1] );
		}
	}

	// Nothing matched — let it fall through (e.g. before-after/{item}, which the
	// legacy redirect handler sends to the gallery).
	return $query_vars;
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
