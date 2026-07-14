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

/**
 * De-duplicate TrichoLab.
 *
 * TrichoLab is intentionally a Page under Hair Transplant (ID matches the
 * indexed /en/hair-transplant/tricholab URL). An older, leftover `treatment`
 * post with the same slug still answers the bare /hair-transplant/tricholab URL,
 * creating a duplicate. Rather than delete data, we 301 that treatment view to
 * the canonical page. Runs before the legacy handler and is NOT gated on 404,
 * because the stale treatment resolves with a 200.
 */
add_action( 'template_redirect', 'estecapelli_dedupe_tricholab', 0 );
function estecapelli_dedupe_tricholab() {
	if ( is_admin() || ! is_singular( 'treatment' ) ) {
		return;
	}
	if ( 'tricholab' === get_post_field( 'post_name', get_queried_object_id() ) ) {
		wp_safe_redirect( home_url( '/en/hair-transplant/tricholab/' ), 301 );
		exit;
	}
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

	// The full requested path (minus query), used to guard against self-redirects.
	$current_path = trim( (string) $path, '/' );

	// Strip leading slash and any /en/ (WPML) prefix.
	$path = ltrim( $path, '/' );
	$path = preg_replace( '#^en(/|$)#', '', $path );
	$path = trim( $path, '/' );

	// Empty after stripping = redirect to the English homepage — but never when we
	// are already on /en/ (that would loop if /en/ itself 404s during setup).
	if ( '' === $path ) {
		if ( 'en' === $current_path || '' === $current_path ) {
			return;
		}
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
			// Never redirect a URL to itself.
			if ( trim( $target, '/' ) === $current_path ) {
				continue;
			}
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

		// ---- Doctors moved from nested pages to the `doctor` post type. The
		//      surgeons and Medical Director keep their exact indexed parents. ----
		array( 'from' => '#^about-us/medical-director/mehmet-hanifi-kutlar/?$#i', 'to' => '/en/about-us/medical-director/mehmet-hanifi-kutlar' ),

		// ---- Previous build shipped treatments at /treatments/{slug}/.
		//      Canonical is now /en/{category}/{service}. Every treatment we
		//      shipped was hair-transplant, so map those across; VITA also
		//      changed slug (vita → vita-treatment). ----
		array( 'from' => '#^treatments/vita/?$#i',    'to' => '/en/hair-transplant/vita-treatment' ),
		array( 'from' => '#^treatments/([^/]+)/?$#i', 'to' => '/en/hair-transplant/$1' ),

		// ---- TrichoLab lives at /en/hair-transplant/tricholab (the indexed
		//      live URL). Catch any old standalone /en/tricholab link. ----
		array( 'from' => '#^tricholab/?$#i', 'to' => '/en/hair-transplant/tricholab' ),

		// ---- "Overview" pages: the live site had a thin overview page under
		//      each category; the new theme's section landing already covers
		//      that ground, so send the indexed overview URLs to the landing. ----
		array( 'from' => '#^hair-transplant/hair-transplant-overview/?$#i', 'to' => '/en/hair-transplant' ),
		array( 'from' => '#^plastic-surgery/plastic-surgery-overview/?$#i',  'to' => '/en/plastic-surgery' ),
		array( 'from' => '#^dental-treatment/dental-treatment-overview/?$#i', 'to' => '/en/dental-treatment' ),

		// ---- The DHI page had a nested "techniques comparison" child page.
		//      Not rebuilt as a standalone page — fold it back into DHI. ----
		array( 'from' => '#^hair-transplant/dhi-hair-transplant/.+$#i', 'to' => '/en/hair-transplant/dhi-hair-transplant' ),

		// ---- Before/After: the 40 individual result pages are no longer thin
		//      standalone pages (they surface through the gallery). Send every
		//      indexed /en/before-after/{item} to the gallery. The gallery page
		//      itself (/en/before-after) resolves normally and never reaches this
		//      handler (is_404 gate). ----
		array( 'from' => '#^before-after/.+$#i', 'to' => '/en/before-after' ),

	);
}
