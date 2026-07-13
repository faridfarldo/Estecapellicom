<?php
/**
 * Canonical URLs for the pre-WPML phase.
 *
 * Without WPML installed, every page is reachable at BOTH its bare WordPress
 * permalink (/hair-transplant/tricholab/) and its /en/ shim URL
 * (/en/hair-transplant/tricholab/) — both return 200. Google's sitemap only
 * lists the /en/ URLs, but with no canonical tag the bare duplicates can get
 * indexed too ("Duplicate without user-selected canonical" in Search Console).
 *
 * WordPress core's own rel_canonical emits the BARE permalink, which is the
 * wrong signal. So we replace it with a canonical that always points at the
 * /en/ version — the URL that is actually indexed.
 *
 * Entirely inert once WPML is active: WPML then owns canonical + hreflang, and
 * it 301-redirects the bare URLs to /en/ so this shim is no longer needed.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// WPML active = production language routing owns canonicals. Do nothing.
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
	return;
}

// Core's canonical points at the bare permalink — drop it and emit our own.
remove_action( 'wp_head', 'rel_canonical' );
add_action( 'wp_head', 'estecapelli_en_canonical', 10 );

/**
 * Print a canonical <link> pointing at the /en/ form of the current URL.
 */
function estecapelli_en_canonical() {

	if ( is_front_page() ) {
		$canonical = home_url( '/en/' );
	} elseif ( is_singular() ) {
		$permalink = get_permalink();
		if ( ! $permalink ) {
			return;
		}
		$canonical = estecapelli_ensure_en_prefix( $permalink );
	} else {
		// Archives / search / 404 — leave canonical unset (not indexed targets).
		return;
	}

	echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
}

/**
 * Guarantee a single /en/ language prefix on a full site URL.
 *
 * Treatments, doctors and blog posts already carry /en/ in their permalink;
 * regular pages do not. Strip any existing leading en/ then add exactly one,
 * so the prefix is never doubled.
 */
function estecapelli_ensure_en_prefix( $url ) {
	$home = home_url( '/' );
	$path = ltrim( str_replace( $home, '', $url ), '/' );
	$path = preg_replace( '#^en/#', '', $path );

	if ( '' === $path ) {
		return $home . 'en/';
	}
	return $home . 'en/' . $path;
}
