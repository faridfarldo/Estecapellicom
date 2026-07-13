<?php
/**
 * SEO adjustments layered on top of Rank Math.
 *
 * 1. Force the /en/ language prefix onto the canonical and Open Graph URLs for
 *    the default language. WPML's "directory for default language" does not
 *    engage on this install, so Rank Math emits the bare URL
 *    (/hair-transplant/…) instead of the indexed /en/ one. Any URL with no
 *    language directory is normalised to /en/; URLs already under a language
 *    directory (/fr/, /it/…) are left untouched.
 *
 * 2. Serve robots.txt (WordPress's virtual one 404s on this install) — allow
 *    crawling, block wp-admin, and advertise the sitemap.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a single /en/ prefix to a full URL that has no language directory yet.
 */
function estecapelli_seo_en_url( $url ) {
	if ( ! is_string( $url ) || '' === $url ) {
		return $url;
	}
	$parts = wp_parse_url( $url );
	if ( empty( $parts['host'] ) ) {
		return $url;
	}
	$path = isset( $parts['path'] ) ? ltrim( $parts['path'], '/' ) : '';

	// Already under a language directory — leave it exactly as WPML built it.
	if ( preg_match( '#^(en|tr|fr|it|es|pl|pt)(/|$)#', $path ) ) {
		return $url;
	}

	$scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : 'https';
	$out    = $scheme . '://' . $parts['host'] . '/en/' . $path;
	if ( ! empty( $parts['query'] ) ) {
		$out .= '?' . $parts['query'];
	}
	return $out;
}
add_filter( 'rank_math/frontend/canonical', 'estecapelli_seo_en_url' );
add_filter( 'rank_math/opengraph/url', 'estecapelli_seo_en_url' );

/**
 * Serve robots.txt directly (the virtual WordPress robots.txt 404s here).
 * Allow all, keep crawlers out of wp-admin, and advertise the sitemap.
 */
add_action( 'init', 'estecapelli_serve_robots_txt' );
function estecapelli_serve_robots_txt() {
	$path = isset( $_SERVER['REQUEST_URI'] )
		? (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH )
		: '';

	if ( '/robots.txt' !== $path ) {
		return;
	}

	if ( ! headers_sent() ) {
		header( 'Content-Type: text/plain; charset=utf-8' );
	}
	echo "User-agent: *\n";
	echo "Disallow: /wp-admin/\n";
	echo "Allow: /wp-admin/admin-ajax.php\n\n";
	echo 'Sitemap: ' . esc_url( home_url( '/sitemap_index.xml' ) ) . "\n";
	exit;
}
