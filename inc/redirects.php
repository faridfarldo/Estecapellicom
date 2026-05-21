<?php
/**
 * Legacy URL redirects (estecapelli.com → new theme).
 *
 * Fires on template_redirect only when WordPress would otherwise 404.
 * Strips the `/en/` WPML prefix from the incoming request, matches the
 * remainder against an ordered list of regex rules, and 301-redirects
 * to the new URL via home_url() so the WPML language context is
 * preserved.
 *
 * Why is_404() only? It keeps the rules from accidentally hijacking
 * URLs that already resolve correctly on the new theme.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', 'estecapelli_handle_legacy_redirects', 1 );
function estecapelli_handle_legacy_redirects() {

	if ( is_admin() || ! is_404() ) {
		return;
	}

	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	if ( ! $request ) {
		return;
	}

	// Drop the query string for matching.
	$path = (string) strtok( $request, '?' );

	// Strip leading slash and any /en/ (WPML) prefix.
	$path = ltrim( $path, '/' );
	$path = preg_replace( '#^en(/|$)#', '', $path );
	$path = trim( $path, '/' );

	// Empty after stripping = redirect to the (language-aware) homepage.
	if ( '' === $path ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}

	foreach ( estecapelli_legacy_redirect_rules() as $rule ) {
		if ( preg_match( $rule['from'], $path, $matches ) ) {
			$target = preg_replace_callback(
				'#\$(\d+)#',
				function ( $m ) use ( $matches ) {
					$idx = (int) $m[1];
					return $matches[ $idx ] ?? '';
				},
				$rule['to']
			);
			wp_safe_redirect( home_url( $target ), 301 );
			exit;
		}
	}
}

/**
 * Ordered redirect rules — first match wins. Patterns match the path
 * AFTER the /en/ prefix has been stripped and surrounding slashes
 * trimmed. Targets are passed through home_url() so WPML prepends the
 * current language prefix automatically.
 */
function estecapelli_legacy_redirect_rules() {

	return array(

		// ---- Explicit static pages ----
		array( 'from' => '#^home/?$#i',    'to' => '/' ),

		// ---- Category overview pages ----
		array( 'from' => '#^hair-transplant/hair-transplant-overview/?$#i',     'to' => '/hair-transplant/' ),
		array( 'from' => '#^plastic-surgery/plastic-surgery-overview/?$#i',     'to' => '/plastic-surgery/' ),
		array( 'from' => '#^dental-treatment/dental-treatment-overview/?$#i',   'to' => '/dental-treatment/' ),

		// ---- Removed sub-pages → parent treatment ----
		array( 'from' => '#^hair-transplant/dhi-hair-transplant/[^/]+/?$#i',    'to' => '/treatments/dhi-hair-transplant/' ),

		// ---- Treatments — flatten /{category}/{slug} → /treatments/{slug}/ ----
		array( 'from' => '#^(hair-transplant|plastic-surgery|dental-treatment)/([^/]+)/?$#i', 'to' => '/treatments/$2/' ),

		// ---- Before-after items → single gallery page ----
		array( 'from' => '#^before-after/.+$#i',    'to' => '/before-after/' ),

		// ---- About sub-pages — preserve hierarchy, only ensure trailing slash ----
		array( 'from' => '#^(about-us/.+?)/?$#i',   'to' => '/$1/' ),

		// ---- Blog posts — preserve slug under /blog/ ----
		array( 'from' => '#^blog/(.+?)/?$#i',       'to' => '/blog/$1/' ),

		// ---- Top-level pages that already match the new structure ----
		array( 'from' => '#^(about-us|hair-transplant|plastic-surgery|dental-treatment|before-after|blog|contact)/?$#i', 'to' => '/$1/' ),

	);
}
