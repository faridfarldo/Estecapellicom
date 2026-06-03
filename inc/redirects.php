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

	// Empty after stripping = redirect to the English homepage.
	if ( '' === $path ) {
		wp_safe_redirect( home_url( '/en/' ), 301 );
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

		// ---- Homepage ----
		array( 'from' => '#^home/?$#i', 'to' => '/en/' ),

		// ---- Previous build shipped treatments at /treatments/{slug}/.
		//      Canonical is now /en/{category}/{service}. Every treatment we
		//      shipped was hair-transplant, so map those across; VITA also
		//      changed slug (vita → vita-treatment). ----
		array( 'from' => '#^treatments/vita/?$#i',    'to' => '/en/hair-transplant/vita-treatment' ),
		array( 'from' => '#^treatments/([^/]+)/?$#i', 'to' => '/en/hair-transplant/$1' ),

	);
}
