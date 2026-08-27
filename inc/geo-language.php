<?php
/**
 * Send a visitor to the site in their own language, without costing us the
 * six non-English indexes and without fighting the page cache.
 *
 * Two constraints shape everything below.
 *
 * 1. Google crawls this site almost entirely from US IP addresses. If /it/,
 *    /pl/ or /ro/ URLs answered a US IP with a redirect to English, Googlebot
 *    would see the translated pages disappear, and the ~590 indexed URLs
 *    across eight languages are the whole asset here. So a redirect happens on
 *    the HOMEPAGE ONLY. Every other page offers a dismissible link instead,
 *    which is a pattern Google is explicitly fine with.
 *
 * 2. WP Rocket serves cached pages from advanced-cache.php, long before
 *    template_redirect or wp_footer ever run. Anything visitor-specific that
 *    is decided in PHP and printed into the page is therefore either dead (the
 *    hook never fires) or actively wrong (the first visitor's answer is baked
 *    into the cache file and served to everyone after them). The same trap is
 *    already documented on the hair widget's nonce route.
 *
 * So the split is: the CACHED HTML carries nothing about the visitor, and one
 * uncached REST route answers "who is this and where should they go". The page
 * asks it, then either redirects itself (homepage) or injects the bar. Full
 * page caching stays on, and nothing needs excluding.
 *
 * Detection prefers the browser's own Accept-Language over the IP country: a
 * Romanian living in Germany wants Romanian, and only their browser knows
 * that. The country is the fallback, read from Cloudflare's CF-IPCountry
 * header, which is already in front of this site — no third-party API, no
 * latency, no cost. It needs "IP Geolocation" switched on under
 * Cloudflare → Network.
 *
 * Crawlers are answered by the route with "no opinion", so a JS-rendering
 * Googlebot has nothing to act on. That check lives at the route, not in the
 * page, precisely because the page is cached and the route is not.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Cookie holding the visitor's own language choice. Detection never overrides it. */
const ESTECAPELLI_GEO_COOKIE = 'este_lang';

/** Cookie remembering that the suggestion bar was dismissed. */
const ESTECAPELLI_GEO_BAR_COOKIE = 'este_lang_bar';

/** How long both cookies live. */
const ESTECAPELLI_GEO_COOKIE_LIFETIME = YEAR_IN_SECONDS;

/**
 * Country => language, for countries where the answer is not ambiguous.
 *
 * Deliberately absent: Belgium, Switzerland, Canada and the other genuinely
 * multilingual countries. Guessing one of their languages from the border is
 * worse than letting Accept-Language decide, and if the browser has no opinion
 * either, English is the honest answer.
 *
 * @return array<string,string>
 */
function estecapelli_geo_country_languages() {
	static $map = null;
	if ( null !== $map ) {
		return $map;
	}

	$by_language = array(
		'ro' => array( 'RO', 'MD' ),
		'tr' => array( 'TR' ),
		'pl' => array( 'PL' ),
		'it' => array( 'IT', 'SM', 'VA' ),
		// Our Portuguese is the European variant. Brazil is still served far
		// better by it than by English, so it is included knowingly.
		'pt' => array( 'PT', 'BR', 'AO', 'MZ', 'CV', 'GW', 'ST' ),
		'es' => array(
			'ES', 'MX', 'AR', 'CO', 'CL', 'PE', 'VE', 'EC', 'GT', 'CU', 'BO',
			'DO', 'HN', 'PY', 'SV', 'NI', 'CR', 'PA', 'UY', 'PR', 'GQ',
		),
		'fr' => array(
			'FR', 'MC', 'LU', 'SN', 'CI', 'ML', 'BF', 'NE', 'TG', 'BJ', 'GA',
			'CG', 'CD', 'CM', 'MG', 'MA', 'DZ', 'TN',
		),
	);

	$map = array();
	foreach ( $by_language as $language => $countries ) {
		foreach ( $countries as $country ) {
			$map[ $country ] = $language;
		}
	}

	return $map;
}

/**
 * Two-letter country for this request, or '' when nothing states one.
 *
 * Cloudflare sends XX for anonymising proxies and T1 for Tor, both of which
 * mean "no usable country" rather than a country named XX.
 */
function estecapelli_geo_country() {
	foreach ( array( 'HTTP_CF_IPCOUNTRY', 'HTTP_X_APPENGINE_COUNTRY', 'HTTP_X_COUNTRY_CODE' ) as $header ) {
		if ( empty( $_SERVER[ $header ] ) ) {
			continue;
		}
		$code = strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', (string) wp_unslash( $_SERVER[ $header ] ) ), 0, 2 ) );
		if ( 2 === strlen( $code ) && ! in_array( $code, array( 'XX', 'T1' ), true ) ) {
			return $code;
		}
	}

	return '';
}

/**
 * Highest-priority Accept-Language tag we actually publish, or ''.
 *
 * Only the primary subtag matters to us: pt-BR and pt-PT are both "pt" here,
 * because there is one Portuguese translation to offer either way.
 */
function estecapelli_geo_browser_language() {
	if ( empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
		return '';
	}

	$header    = (string) wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
	$available = estecapelli_indexed_languages();
	$ranked    = array();

	foreach ( explode( ',', $header ) as $index => $chunk ) {
		$parts = explode( ';', trim( $chunk ) );
		$tag   = strtolower( trim( $parts[0] ) );
		if ( '' === $tag ) {
			continue;
		}
		$quality = 1.0;
		if ( isset( $parts[1] ) && preg_match( '/q\s*=\s*([0-9.]+)/', $parts[1], $m ) ) {
			$quality = (float) $m[1];
		}
		$primary = sanitize_key( strtok( str_replace( '_', '-', $tag ), '-' ) );
		if ( ! in_array( $primary, $available, true ) ) {
			continue;
		}
		// Ties keep header order, which is the visitor's own ordering.
		$ranked[] = array( $quality, -$index, $primary );
	}

	if ( ! $ranked ) {
		return '';
	}

	usort(
		$ranked,
		static function ( $a, $b ) {
			return $b[0] <=> $a[0] ?: $b[1] <=> $a[1];
		}
	);

	return $ranked[0][2];
}

/**
 * The language this visitor should be offered.
 *
 * Browser first, country second. Returns '' when neither says anything we
 * publish — the caller then leaves the visitor on English.
 */
function estecapelli_geo_target_language() {
	$target = estecapelli_geo_browser_language();
	if ( '' === $target ) {
		$country = estecapelli_geo_country();
		$map     = estecapelli_geo_country_languages();
		$target  = $country && isset( $map[ $country ] ) ? $map[ $country ] : '';
	}

	return in_array( $target, estecapelli_indexed_languages(), true ) ? $target : '';
}

/**
 * Whether this request is a robot.
 *
 * Kept as broad substring matching on purpose: a crawler we fail to recognise
 * is exactly the failure this whole file exists to avoid, and the cost of a
 * false positive is only that one human sees no suggestion bar.
 */
function estecapelli_geo_is_crawler() {
	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		return true; // No user agent at all is far more likely a script than a person.
	}

	$agent = strtolower( (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
	foreach ( array( 'bot', 'crawl', 'spider', 'slurp', 'archiver', 'preview', 'fetch', 'monitor', 'validator', 'lighthouse', 'headless' ) as $needle ) {
		if ( false !== strpos( $agent, $needle ) ) {
			return true;
		}
	}

	return false;
}

/**
 * The suggestion bar's copy, written in the language being offered.
 *
 * These cannot go through __(): gettext renders in the language of the CURRENT
 * page, and the whole point of this bar is to speak to someone in a language
 * the page they are reading is not written in.
 *
 * @return array<string,array{message:string,action:string,dismiss:string}>
 */
function estecapelli_geo_bar_strings() {
	return array(
		'en' => array(
			'message' => 'This page is also available in English.',
			'action'  => 'View in English',
			'dismiss' => 'Dismiss',
		),
		'ro' => array(
			'message' => 'Această pagină este disponibilă și în română.',
			'action'  => 'Vezi în română',
			'dismiss' => 'Închide',
		),
		'tr' => array(
			'message' => 'Bu sayfa Türkçe olarak da mevcut.',
			'action'  => 'Türkçe görüntüle',
			'dismiss' => 'Kapat',
		),
		'fr' => array(
			'message' => 'Cette page est également disponible en français.',
			'action'  => 'Voir en français',
			'dismiss' => 'Fermer',
		),
		'it' => array(
			'message' => 'Questa pagina è disponibile anche in italiano.',
			'action'  => 'Vedi in italiano',
			'dismiss' => 'Chiudi',
		),
		'es' => array(
			'message' => 'Esta página también está disponible en español.',
			'action'  => 'Ver en español',
			'dismiss' => 'Cerrar',
		),
		'pl' => array(
			'message' => 'Ta strona jest dostępna także po polsku.',
			'action'  => 'Zobacz po polsku',
			'dismiss' => 'Zamknij',
		),
		'pt' => array(
			'message' => 'Esta página também está disponível em português.',
			'action'  => 'Ver em português',
			'dismiss' => 'Fechar',
		),
	);
}

/**
 * Whether a path is a language root — the one place a redirect is allowed.
 *
 * Matches "/", "/en", "/en/" and the same for every indexed code, and nothing
 * deeper. Deriving this from the path rather than is_front_page() is what lets
 * the decision live at the REST route, where the cache cannot reach it.
 */
function estecapelli_geo_path_language( $path ) {
	$trimmed = trim( (string) wp_parse_url( (string) $path, PHP_URL_PATH ), '/' );
	if ( '' === $trimmed ) {
		return array( 'en', true );
	}

	$segments = explode( '/', $trimmed );
	$first    = sanitize_key( strtolower( $segments[0] ) );
	if ( 'pt-pt' === $first ) {
		$first = 'pt';
	}
	if ( ! in_array( $first, estecapelli_indexed_languages(), true ) ) {
		// No language directory: an English page under the bare permalink.
		return array( 'en', false );
	}

	return array( $first, 1 === count( $segments ) );
}

add_action( 'rest_api_init', 'estecapelli_geo_register_route' );
/** One uncached route, because the page that asks it is cached. */
function estecapelli_geo_register_route() {
	register_rest_route(
		'estecapelli/v1',
		'/language-hint',
		array(
			'methods'             => 'GET',
			'callback'            => 'estecapelli_geo_language_hint',
			'permission_callback' => '__return_true',
			'args'                => array(
				'path' => array(
					'required'          => true,
					'sanitize_callback' => static function ( $value ) {
						// Path only: a full URL here would let a caller ask us
						// to describe somewhere that is not this site.
						return (string) wp_parse_url( (string) $value, PHP_URL_PATH );
					},
				),
			),
		)
	);
}

/**
 * Answer "what language is this visitor, and where should they go from here".
 *
 * Returns mode "none" for anyone we must not act on — crawlers above all, but
 * also logged-in editors previewing a language and visitors who have already
 * chosen. The page treats "none" as "do nothing", so every one of those cases
 * fails safe.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response
 */
function estecapelli_geo_language_hint( WP_REST_Request $request ) {
	$payload = array( 'mode' => 'none' );

	$respond = static function ( $payload ) {
		$response = new WP_REST_Response( $payload, 200 );
		// Visitor-specific: no layer (browser, plugin, CDN) may keep this.
		$response->header( 'Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private' );
		$response->header( 'Vary', 'Accept-Language, CF-IPCountry, Cookie' );
		return $response;
	};

	if ( estecapelli_geo_is_crawler() || is_user_logged_in() ) {
		return $respond( $payload );
	}

	// A stored choice is a decision, not a hint: never second-guess it.
	if ( ! empty( $_COOKIE[ ESTECAPELLI_GEO_COOKIE ] ) ) {
		return $respond( $payload );
	}

	$target = estecapelli_geo_target_language();
	if ( '' === $target ) {
		return $respond( $payload );
	}

	list( $current, $is_root ) = estecapelli_geo_path_language( (string) $request->get_param( 'path' ) );
	if ( $target === $current ) {
		return $respond( $payload );
	}

	if ( $is_root ) {
		// Redirect only from the DEFAULT language's root. Someone already on
		// /it/ has a language in the URL, and second-guessing that is how you
		// send a person who deliberately clicked "English" back to Italian on
		// every single visit.
		if ( 'en' !== $current ) {
			return $respond( $payload );
		}
		$url = estecapelli_language_root_url( $target );
		if ( $url ) {
			$payload = array(
				'mode'     => 'redirect',
				'language' => $target,
				'url'      => $url,
			);
		}
		return $respond( $payload );
	}

	if ( ! empty( $_COOKIE[ ESTECAPELLI_GEO_BAR_COOKIE ] ) ) {
		return $respond( $payload );
	}

	// Built from the frozen URL contract rather than assembled by hand, so the
	// bar either points at a real translated page or is not offered at all.
	// A link that 404s is worse than no link.
	$key = estecapelli_indexed_route_key( (string) $request->get_param( 'path' ) );
	if ( ! $key || ! estecapelli_indexed_route_path( $key, $target ) ) {
		return $respond( $payload );
	}

	$strings = estecapelli_geo_bar_strings();
	if ( ! isset( $strings[ $target ] ) ) {
		return $respond( $payload );
	}

	return $respond(
		array(
			'mode'     => 'suggest',
			'language' => $target,
			'url'      => estecapelli_indexed_url( $key, $target ),
			'strings'  => $strings[ $target ],
		)
	);
}

add_action( 'wp_footer', 'estecapelli_geo_render_bar_skeleton', 30 );
/**
 * An empty, hidden bar that every visitor receives identically.
 *
 * Building the whole element in JS would be simpler, but WP Rocket's "Remove
 * Unused CSS" reads the cached HTML and strips any selector it cannot find
 * there — so a bar that only ever exists after fetch() would arrive unstyled.
 * Printing the skeleton keeps every class visible to that pass while carrying
 * nothing about who is reading, so the cache stays correct.
 */
function estecapelli_geo_render_bar_skeleton() {
	if ( is_admin() ) {
		return;
	}
	?>
	<div class="lang-suggest" data-lang-suggest hidden>
		<p class="lang-suggest__text" data-lang-suggest-text></p>
		<a class="lang-suggest__action" data-lang-suggest-action></a>
		<button type="button" class="lang-suggest__close" data-lang-suggest-close>&times;</button>
	</div>
	<?php
}

add_action( 'wp_head', 'estecapelli_geo_inline_script', 2 );
/**
 * The only script printed into the cached HTML — identical for every visitor,
 * which is exactly why it survives the cache.
 *
 * Runs in <head> so the homepage redirect fires before the page has painted
 * much. It is a location.replace(), so it leaves no entry in history and the
 * back button still works normally.
 *
 * data-cfasync="false" keeps Cloudflare Rocket Loader from deferring it. WP
 * Rocket's "Delay JavaScript execution" must be told the same — see
 * docs/GEO-LANGUAGE.md — or the redirect waits for the visitor to interact,
 * which is precisely when it is no longer wanted.
 */
function estecapelli_geo_inline_script() {
	if ( is_admin() ) {
		return;
	}

	$endpoint   = esc_url_raw( rest_url( 'estecapelli/v1/language-hint' ) );
	$cookie     = ESTECAPELLI_GEO_COOKIE;
	$bar_cookie = ESTECAPELLI_GEO_BAR_COOKIE;
	$lifetime   = (int) ESTECAPELLI_GEO_COOKIE_LIFETIME;
	?>
	<script id="estecapelli-language-hint" data-cfasync="false">
	(function () {
		var ENDPOINT = <?php echo wp_json_encode( $endpoint ); ?>;
		var LANG_COOKIE = <?php echo wp_json_encode( $cookie ); ?>;
		var BAR_COOKIE = <?php echo wp_json_encode( $bar_cookie ); ?>;
		var MAX_AGE = <?php echo (int) $lifetime; ?>;
		var SECURE = location.protocol === 'https:' ? '; secure' : '';

		function remember(name, value) {
			try {
				document.cookie = name + '=' + encodeURIComponent(value) +
					'; max-age=' + MAX_AGE + '; path=/; samesite=lax' + SECURE;
			} catch (e) {}
		}
		function hasCookie(name) {
			return document.cookie.indexOf(name + '=') !== -1;
		}

		// Any language-switcher click is a deliberate choice, and it outranks
		// detection permanently. hreflang appears only on the header and footer
		// switchers — and on the suggestion bar's own link, which is the same
		// kind of decision.
		document.addEventListener('click', function (event) {
			var link = event.target && event.target.closest ? event.target.closest('a[hreflang]') : null;
			if (link) { remember(LANG_COOKIE, link.getAttribute('hreflang')); }
		}, true);

		// Already chosen, or already dismissed and nothing else to do: never ask.
		if (hasCookie(LANG_COOKIE)) { return; }
		if (!window.fetch) { return; }

		function showBar(data) {
			var bar = document.querySelector('[data-lang-suggest]');
			if (!bar || !data.strings) { return; }

			var text = bar.querySelector('[data-lang-suggest-text]');
			var action = bar.querySelector('[data-lang-suggest-action]');
			var close = bar.querySelector('[data-lang-suggest-close]');
			if (!text || !action || !close) { return; }

			bar.setAttribute('role', 'region');
			bar.setAttribute('aria-label', data.strings.message);

			text.lang = data.language;
			text.textContent = data.strings.message;

			action.href = data.url;
			action.lang = data.language;
			action.setAttribute('hreflang', data.language);
			action.textContent = data.strings.action;

			close.setAttribute('aria-label', data.strings.dismiss);
			close.addEventListener('click', function () {
				remember(BAR_COOKIE, '1');
				bar.hidden = true;
			});

			bar.hidden = false;
		}

		fetch(ENDPOINT + '?path=' + encodeURIComponent(location.pathname), {
			credentials: 'same-origin',
			headers: { 'Accept': 'application/json' }
		}).then(function (r) {
			return r.ok ? r.json() : null;
		}).then(function (data) {
			if (!data || !data.mode || data.mode === 'none') { return; }

			if (data.mode === 'redirect' && data.url) {
				// Remember it first: this is a one-time event, and the cookie
				// is what guarantees the redirect cannot happen twice.
				remember(LANG_COOKIE, data.language);
				location.replace(data.url);
				return;
			}

			if (data.mode === 'suggest') {
				if (document.body) {
					showBar(data);
				} else {
					document.addEventListener('DOMContentLoaded', function () { showBar(data); });
				}
			}
		}).catch(function () {});
	}());
	</script>
	<?php
}
