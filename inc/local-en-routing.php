<?php
/**
 * Local /en/ routing shim (development only).
 *
 * Production runs WPML, which serves every page under the `/en/` language
 * prefix and strips trailing slashes. All internal links are hard-coded to
 * those live URLs (e.g. /en/before-after). Locally WPML is NOT installed, so
 * regular pages live at /{slug}/ and the /en/{slug} links would 404.
 *
 * Treatments already carry /en/ in their CPT rewrite slug
 * (en/%treatment_category%), so they resolve locally. The catch: that same
 * rewrite struct generates a greedy `en/{anything}` category rule that would
 * hijack /en/{page} before page resolution runs. So instead of fighting the
 * rule order, we intercept at do_parse_request (which fires before any rule
 * matching): if the /en/ path is a real page, resolve it directly; otherwise
 * fall through so the treatment rules handle it.
 *
 * Entirely inert when WPML is active (production owns /en/ routing).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// WPML active = production: it owns /en/ routing, do nothing.
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
	return;
}

/**
 * Resolve /en/{page-path} to the matching page, before rewrite rules run.
 *
 * Returns false (short-circuiting WP's own parsing) only when the stripped
 * path is a real page; treatments and everything else fall through unchanged.
 */
add_filter( 'do_parse_request', 'estecapelli_local_en_parse', 10, 2 );
function estecapelli_local_en_parse( $continue, $wp ) {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path = trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );

	if ( 'en' !== $path && 0 !== strpos( $path, 'en/' ) ) {
		return $continue;
	}

	$rest = trim( substr( $path, 2 ), '/' );
	if ( '' === $rest ) {
		// Bare /en/ — serve the front page without a canonical redirect loop.
		$wp->query_vars   = array();
		$wp->request      = $path;
		$wp->matched_rule = '';
		return false;
	}

	$page = get_page_by_path( $rest );
	if ( $page ) {
		$wp->query_vars  = array( 'pagename' => $rest );
		$wp->request     = $path;
		$wp->matched_rule = '';
		return false; // we resolved it; skip WP's rule matching.
	}

	return $continue; // not a page → let the treatment CPT rules handle it.
}

/**
 * Keep the browser on the /en/ URL: without this, WordPress would 301
 * /en/before-after to its canonical /before-after/.
 */
add_filter( 'redirect_canonical', 'estecapelli_local_en_no_canonical', 10, 2 );
function estecapelli_local_en_no_canonical( $redirect_url, $requested_url ) {
	if ( preg_match( '#^https?://[^/]+/en(/|$)#', $requested_url ) ) {
		return false;
	}
	return $redirect_url;
}
