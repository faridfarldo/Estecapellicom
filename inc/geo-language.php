<?php
/**
 * Send a visitor to the site in their own language, without costing us the
 * six non-English indexes.
 *
 * The rule that shapes every decision below: Google crawls this site almost
 * entirely from US IP addresses. If /it/, /pl/ or /ro/ URLs answered a US IP
 * with a redirect to English, Googlebot would see the translated pages
 * disappear, and the ~590 indexed URLs across seven languages are the whole
 * asset here. So:
 *
 * - A redirect happens on the HOMEPAGE ONLY, and only from the default
 *   language's root. Someone who arrives on /it/trapianto-di-capelli/ from a
 *   search result stays exactly where they landed.
 * - Every other page offers a dismissible SUGGESTION bar instead — a link, not
 *   a redirect, which Google is explicitly fine with.
 * - Crawlers are never redirected and never shown the bar.
 * - A manual language choice is remembered and permanently outranks detection.
 *
 * Detection prefers the browser's own Accept-Language over the IP country: a
 * Romanian living in Germany wants Romanian, and only their browser knows that.
 * The country is the fallback, read from Cloudflare's CF-IPCountry header,
 * which is already in front of this site — no API call, no latency, no cost.
 * It needs "IP Geolocation" switched on under Cloudflare → Network.
 *
 * CACHING NOTE: the homepage response now varies by visitor. It is sent with
 * Cache-Control: private, no-store, and WordPress HTML is not cached by
 * Cloudflare by default. If a page cache or Cloudflare APO is ever enabled,
 * the homepage MUST be excluded or every visitor gets the first visitor's
 * language.
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
	static $target = null;
	if ( null !== $target ) {
		return $target;
	}

	$target = estecapelli_geo_browser_language();
	if ( '' === $target ) {
		$country = estecapelli_geo_country();
		$map     = estecapelli_geo_country_languages();
		$target  = $country && isset( $map[ $country ] ) ? $map[ $country ] : '';
	}

	if ( $target && ! in_array( $target, estecapelli_indexed_languages(), true ) ) {
		$target = '';
	}

	return $target;
}

/** The visitor's remembered choice, or '' if they have not made one. */
function estecapelli_geo_stored_language() {
	if ( empty( $_COOKIE[ ESTECAPELLI_GEO_COOKIE ] ) ) {
		return '';
	}
	$stored = sanitize_key( (string) wp_unslash( $_COOKIE[ ESTECAPELLI_GEO_COOKIE ] ) );

	return in_array( $stored, estecapelli_indexed_languages(), true ) ? $stored : '';
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

/** Whether language detection may act on this request at all. */
function estecapelli_geo_may_run() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return false;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return false;
	}
	if ( is_user_logged_in() ) {
		return false; // Editors previewing a language must never be moved.
	}
	if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'GET' !== strtoupper( (string) wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) {
		return false;
	}

	return ! estecapelli_geo_is_crawler();
}

/** Remember a language server-side so the next request skips detection. */
function estecapelli_geo_remember( $language ) {
	if ( headers_sent() ) {
		return;
	}
	setcookie(
		ESTECAPELLI_GEO_COOKIE,
		$language,
		array(
			'expires'  => time() + ESTECAPELLI_GEO_COOKIE_LIFETIME,
			'path'     => '/',
			'secure'   => is_ssl(),
			'httponly' => false, // The switcher's JS writes the same cookie.
			'samesite' => 'Lax',
		)
	);
}

add_action( 'template_redirect', 'estecapelli_geo_redirect_home', 5 );
/**
 * Move a first-time visitor from the default-language homepage to their own.
 *
 * Only from the English root: a visitor already on /it/ or /ro/ has a language
 * in the URL, and second-guessing that is how you send someone who deliberately
 * clicked "English" back to Italian on every visit.
 */
function estecapelli_geo_redirect_home() {
	if ( ! is_front_page() || ! estecapelli_geo_may_run() ) {
		return;
	}
	if ( estecapelli_geo_stored_language() ) {
		return; // They have chosen; detection is over for good.
	}

	$current = estecapelli_indexed_language_code();
	if ( 'en' !== $current ) {
		return;
	}

	$target = estecapelli_geo_target_language();
	if ( '' === $target || 'en' === $target ) {
		return;
	}

	$url = estecapelli_language_root_url( $target );
	if ( ! $url ) {
		return;
	}

	// Remember it first, so this is a one-time event even if they come back to
	// the bare root later, and so the redirect cannot loop.
	estecapelli_geo_remember( $target );

	// This response is visitor-specific and must never be cached or made
	// permanent. 302, never 301.
	if ( ! headers_sent() ) {
		header( 'Cache-Control: private, no-store, max-age=0' );
	}
	wp_safe_redirect( $url, 302 );
	exit;
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
 * The exact translated URL of the page being viewed, or '' if there is none.
 *
 * Built from the frozen URL contract rather than assembled by hand, so the bar
 * either points at a real translated page or is not shown at all. Offering a
 * link that 404s is worse than offering nothing.
 */
function estecapelli_geo_translated_url( $language ) {
	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$key     = estecapelli_indexed_route_key( $request );
	if ( ! $key || ! estecapelli_indexed_route_path( $key, $language ) ) {
		return '';
	}

	return estecapelli_indexed_url( $key, $language );
}

add_action( 'wp_footer', 'estecapelli_geo_render_suggestion_bar', 30 );
/** Offer the visitor's language on any page we are deliberately not redirecting. */
function estecapelli_geo_render_suggestion_bar() {
	if ( is_front_page() || is_404() || ! estecapelli_geo_may_run() ) {
		return;
	}
	if ( ! empty( $_COOKIE[ ESTECAPELLI_GEO_BAR_COOKIE ] ) ) {
		return;
	}

	// A stored choice is a decision, not a hint: never second-guess it.
	if ( estecapelli_geo_stored_language() ) {
		return;
	}

	$target  = estecapelli_geo_target_language();
	$current = estecapelli_indexed_language_code();
	if ( '' === $target || $target === $current ) {
		return;
	}

	$strings = estecapelli_geo_bar_strings();
	if ( ! isset( $strings[ $target ] ) ) {
		return;
	}

	$url = estecapelli_geo_translated_url( $target );
	if ( '' === $url ) {
		return;
	}
	?>
	<div class="lang-suggest" data-lang-suggest data-lang="<?php echo esc_attr( $target ); ?>" role="region" aria-label="<?php echo esc_attr( $strings[ $target ]['message'] ); ?>">
		<p class="lang-suggest__text" lang="<?php echo esc_attr( $target ); ?>"><?php echo esc_html( $strings[ $target ]['message'] ); ?></p>
		<a class="lang-suggest__action" href="<?php echo esc_url( $url ); ?>" lang="<?php echo esc_attr( $target ); ?>" hreflang="<?php echo esc_attr( $target ); ?>"><?php echo esc_html( $strings[ $target ]['action'] ); ?></a>
		<button type="button" class="lang-suggest__close" data-lang-suggest-close aria-label="<?php echo esc_attr( $strings[ $target ]['dismiss'] ); ?>">&times;</button>
	</div>
	<?php
}

add_action( 'wp_footer', 'estecapelli_geo_inline_script', 31 );
/**
 * Record a manual language choice, and let the bar be dismissed.
 *
 * The switcher's links are plain anchors in the header and footer, so a click
 * listener is enough — no URL parameter, and nothing to strip back out of the
 * indexed URLs afterwards.
 */
function estecapelli_geo_inline_script() {
	if ( ! estecapelli_geo_may_run() ) {
		return;
	}
	$cookie     = ESTECAPELLI_GEO_COOKIE;
	$bar_cookie = ESTECAPELLI_GEO_BAR_COOKIE;
	$lifetime   = (int) ESTECAPELLI_GEO_COOKIE_LIFETIME;
	?>
	<script>
	(function () {
		var SECURE = location.protocol === 'https:' ? '; secure' : '';
		function remember(name, value) {
			try {
				document.cookie = name + '=' + encodeURIComponent(value) +
					'; max-age=<?php echo esc_js( $lifetime ); ?>; path=/; samesite=lax' + SECURE;
			} catch (e) {}
		}

		// Any language-switcher link is a deliberate choice. hreflang only ever
		// appears on those two switchers, in the header and the footer.
		document.addEventListener('click', function (event) {
			var link = event.target && event.target.closest ? event.target.closest('a[hreflang]') : null;
			if (link) {
				remember('<?php echo esc_js( $cookie ); ?>', link.getAttribute('hreflang'));
			}
		}, true);

		var bar = document.querySelector('[data-lang-suggest]');
		if (!bar) { return; }
		var close = bar.querySelector('[data-lang-suggest-close]');
		if (close) {
			close.addEventListener('click', function () {
				remember('<?php echo esc_js( $bar_cookie ); ?>', '1');
				bar.parentNode && bar.parentNode.removeChild(bar);
			});
		}
	}());
	</script>
	<?php
}
