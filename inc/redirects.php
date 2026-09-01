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

/**
 * Renamed blog slugs (copy-paste bug inherited from the old live site).
 *
 * Three translated posts carried a slug copied from another language:
 *   - ES "diabetic"         was published at the Italian slug
 *     (/es/blog/i-pazienti-diabetici-...-trapianto-di-capelli)
 *   - IT "unshaven"         was published at the French slug
 *     (/it/blog/greffe-de-cheveux-sans-rasage)
 *   - PL "HIV+ in Turkey"   was published at the Spanish slug
 *     (/pl/blog/trasplante-capilar-para-pacientes-vih-positivos-en-turquia)
 * Each now has its correct localized slug (see estecapelli_indexed_blog_slugs),
 * so 301 the old language-scoped URLs to the new ones.
 *
 * The match is on the EXACT language-prefixed path because two of those slugs
 * are still the live URLs in their real languages — /it/blog/i-pazienti-...
 * (Italian) and /fr/blog/greffe-de-cheveux-sans-rasage (French) must never be
 * redirected.
 *
 * The second group is a deliberate editorial merge, not a bug. "HIV+ hair
 * transplant IN TURKEY" and "HIV+ hair transplant" were two articles competing
 * for one query, so the first was removed to stop them cannibalising each
 * other. Consolidating only works if the removed URLs pass their ranking on, so
 * all eight of its localized slugs 301 to the article that survived, spelled
 * out here rather than left to WordPress's own renamed-slug handling: the
 * contract entry they came from is gone, and a redirect that carries ranking
 * signal should not depend on post meta surviving a re-import. The Spanish slug
 * in this group WAS live Spanish content until that merge, which is why the
 * warning above no longer covers it.
 *
 * Runs at priority 0 (before redirect_canonical) and is NOT gated on is_404 so it
 * fires no matter how WPML resolves the stale slug.
 */
/*
 * Ahead of estecapelli_indexed_404_fallback(), which also answers on
 * template_redirect at priority 0 and, being registered from a file loaded
 * earlier, used to win every tie. That fallback works the destination out from
 * the route contract; everything below is a destination somebody chose. An
 * explicit rule has to outrank a computed one, or the rule is decoration —
 * /pt/sobre-nos/diretor-medico proved it by landing on the ENGLISH roster while
 * the line saying otherwise sat right here unused.
 */
add_action( 'template_redirect', 'estecapelli_redirect_renamed_blog_slugs', -5 );
function estecapelli_redirect_renamed_blog_slugs() {
	if ( is_admin() ) {
		return;
	}
	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	if ( ! $request ) {
		return;
	}
	$path = trim( (string) strtok( $request, '?' ), '/' );

	$moved = array(
		'es/blog/i-pazienti-diabetici-possono-sottoporsi-a-un-trapianto-di-capelli' => '/es/blog/los-pacientes-diabeticos-pueden-someterse-a-un-trasplante-capilar/',
		'it/blog/greffe-de-cheveux-sans-rasage'                                     => '/it/blog/trapianto-di-capelli-senza-rasatura/',
		// Straight to the surviving article. This used to point at the Polish
		// "in Turkey" slug, which the merge below then redirected again — two
		// hops where one will do.
		'pl/blog/trasplante-capilar-para-pacientes-vih-positivos-en-turquia'        => '/pl/blog/przeszczep-wlosow-u-pacjentow-hiv-pozytywnych/',

		/*
		 * The Chief Physician page is retired. It was a one-section stub that
		 * duplicated what the doctors roster already says, and the only profile
		 * nested under it — mehmet-hanifi-kutlar, there because the old live
		 * site had indexed that path, not because of his title — now sits with
		 * every other profile under our-doctors.
		 *
		 * Sixteen indexed URLs move, so all sixteen are written out: the page
		 * itself goes to the roster, and the profile goes to its own new
		 * address rather than being folded into the roster with it.
		 */
		'en/about-us/medical-director/mehmet-hanifi-kutlar'    => '/en/about-us/our-doctors/mehmet-hanifi-kutlar/',
		'en/about-us/medical-director'                         => '/en/about-us/our-doctors/',
		'tr/hakkimizda/tibbi-direktor/mehmet-hanifi-kutlar'    => '/tr/hakkimizda/doktorlarimiz/mehmet-hanifi-kutlar/',
		'tr/hakkimizda/tibbi-direktor'                         => '/tr/hakkimizda/doktorlarimiz/',
		'fr/a-propos-de-nous/directeur-medical/mehmet-hanifi-kutlar' => '/fr/a-propos-de-nous/nos-medecins/mehmet-hanifi-kutlar/',
		'fr/a-propos-de-nous/directeur-medical'                => '/fr/a-propos-de-nous/nos-medecins/',
		'it/chi-siamo/direttore-medico/mehmet-hanifi-kutlar'   => '/it/chi-siamo/i-nostri-medici/mehmet-hanifi-kutlar/',
		'it/chi-siamo/direttore-medico'                        => '/it/chi-siamo/i-nostri-medici/',
		'es/sobre-nosotros/director-medico/mehmet-hanifi-kutlar' => '/es/sobre-nosotros/nuestros-doctores/mehmet-hanifi-kutlar/',
		'es/sobre-nosotros/director-medico'                    => '/es/sobre-nosotros/nuestros-doctores/',
		'pl/o-nas/dyrektor-medyczny/mehmet-hanifi-kutlar'      => '/pl/o-nas/nasi-lekarze/mehmet-hanifi-kutlar/',
		'pl/o-nas/dyrektor-medyczny'                           => '/pl/o-nas/nasi-lekarze/',
		'pt/sobre-nos/diretor-medico/mehmet-hanifi-kutlar'     => '/pt/sobre-nos/nossos-medicos/mehmet-hanifi-kutlar/',
		'pt/sobre-nos/diretor-medico'                          => '/pt/sobre-nos/nossos-medicos/',
		'ro/despre-noi/director-medical/mehmet-hanifi-kutlar'  => '/ro/despre-noi/medicii-nostri/mehmet-hanifi-kutlar/',
		'ro/despre-noi/director-medical'                       => '/ro/despre-noi/medicii-nostri/',

		// The "HIV+ in Turkey" merge, one line per language.
		'en/blog/hair-transplant-for-hiv-positive-patients-in-turkey'               => '/en/blog/hair-transplant-for-hiv-positive-patients/',
		'tr/blog/hiv-pozitif-hastalarina-turkiye-de-sac-ekimi'                      => '/tr/blog/hiv-pozitif-hastalara-sac-ekimi/',
		'fr/blog/greffe-de-cheveux-pour-patients-seropositifs-en-turquie'           => '/fr/blog/greffe-de-cheveux-pour-les-patients-seropositifs-vih/',
		'it/blog/trapianto-di-capelli-per-pazienti-hiv-positivi-in-turchia'         => '/it/blog/trapianto-di-capelli-per-pazienti-sieropositivi-hiv/',
		'es/blog/trasplante-capilar-para-pacientes-vih-positivos-en-turquia'        => '/es/blog/trasplante-capilar-para-pacientes-vih-positivos/',
		'pl/blog/przeszczep-wlosow-u-pacjentow-hiv-pozytywnych-w-turcji'            => '/pl/blog/przeszczep-wlosow-u-pacjentow-hiv-pozytywnych/',
		'pt/blog/transplante-capilar-para-pacientes-hiv-positivos-na-turquia'       => '/pt/blog/transplante-capilar-para-pacientes-hiv-positivos/',
		'ro/blog/transplant-de-par-pentru-pacientii-hiv-pozitivi-in-turcia'         => '/ro/blog/transplant-de-par-pentru-pacientii-hiv-pozitivi/',
	);

	if ( isset( $moved[ $path ] ) ) {
		wp_safe_redirect( home_url( $moved[ $path ] ), 301 );
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

		// ---- Doctors moved from nested pages to the `doctor` post type, and
		//      every profile now sits under our-doctors. ----
		array( 'from' => '#^about-us/medical-director/mehmet-hanifi-kutlar/?$#i', 'to' => '/en/about-us/our-doctors/mehmet-hanifi-kutlar' ),

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
