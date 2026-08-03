<?php
/**
 * GA4 measurement layer — Consent Mode v2, page context and the dataLayer.
 *
 * The theme owns the *facts*; Google Tag Manager owns the *tags*. Everything
 * measurable is pushed to `window.dataLayer` as a named event with explicit
 * parameters, so a GTM tag never has to guess a CSS selector or scrape the DOM.
 * When a button moves or a class is renamed, tracking keeps working.
 *
 * Ordering inside <head> is load-bearing and is guaranteed by printing all of
 * this from estecapelli_gtm_head(), immediately before the container loader:
 *
 *   1. consent defaults  — must run before ANY Google tag, or the first hit
 *                          leaves the browser with unconsented storage.
 *   2. page context      — a plain (event-less) push, so the variables already
 *                          exist when GTM fires Initialisation / Container Load
 *                          and the GA4 config tag reads them on the very first
 *                          page_view.
 *   3. the GTM snippet.
 *
 * Language note: every canonical value below (page_key, treatment_category,
 * treatment_name…) is resolved back to its ENGLISH original through WPML. A
 * report row must aggregate one page across all seven languages — otherwise
 * "Sapphire FUE" is split into seven unrelated rows and nothing is comparable.
 * The visitor-facing language travels separately as `page_language`.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cookie holding the visitor's consent choice.
 *
 * Read by assets/js/consent.js (to decide whether to show the banner) and by
 * the inline bootstrap below (to replay the choice before GTM loads).
 */
if ( ! defined( 'ESTECAPELLI_CONSENT_COOKIE' ) ) {
	define( 'ESTECAPELLI_CONSENT_COOKIE', 'estecapelli_consent' );
}

/**
 * Bump to re-ask every visitor for consent — required when the set of cookies
 * or purposes materially changes. Stored choices at an older version are
 * ignored and the banner shows again.
 */
if ( ! defined( 'ESTECAPELLI_CONSENT_VERSION' ) ) {
	define( 'ESTECAPELLI_CONSENT_VERSION', '1' );
}

/** How long a consent choice is honoured before we ask again (6 months). */
if ( ! defined( 'ESTECAPELLI_CONSENT_LIFETIME' ) ) {
	define( 'ESTECAPELLI_CONSENT_LIFETIME', 15552000 );
}

/**
 * Whether measurement should run for this request.
 *
 * Tied to the GTM container: with no container there is nothing to feed, and
 * the dataLayer would only grow unread.
 *
 * @return bool
 */
function estecapelli_analytics_enabled() {
	return (bool) apply_filters( 'estecapelli_analytics_enabled', (bool) estecapelli_gtm_id() );
}

/* -------------------------------------------------------------------------
 * Page context
 * ---------------------------------------------------------------------- */

/**
 * Resolve a translated post to its English original.
 *
 * Returns the given ID unchanged when WPML is inactive or no source exists, so
 * every caller stays safe on a single-language install.
 *
 * @param int    $post_id Post ID in any language.
 * @param string $type    WPML element type — the post type or taxonomy name.
 * @return int
 */
function estecapelli_analytics_source_id( $post_id, $type = 'page' ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) {
		return 0;
	}

	$default = apply_filters( 'wpml_default_language', null );
	if ( ! $default ) {
		return $post_id;
	}

	$source = (int) apply_filters( 'wpml_object_id', $post_id, $type, true, $default );
	return $source ?: $post_id;
}

/**
 * A stable, language-independent identifier for the current page.
 *
 * This is the single most valuable dimension on a seven-language site: it is
 * what lets one row in GA4 mean "the contact page" rather than "/fr/contact"
 * and "/it/contatti" and "/pl/kontakt" as three unrelated pages.
 *
 * @return string English slug, or a synthetic key for non-singular views.
 */
function estecapelli_analytics_page_key() {
	// Resolved two or three times per request (context, page type, lead result)
	// and each miss costs a WPML translation lookup.
	static $cached = null;
	if ( null !== $cached ) {
		return $cached;
	}
	$cached = estecapelli_analytics_resolve_page_key();
	return $cached;
}

/**
 * Uncached body of estecapelli_analytics_page_key().
 *
 * @return string
 */
function estecapelli_analytics_resolve_page_key() {
	if ( is_404() ) {
		return 'not-found';
	}
	if ( is_search() ) {
		return 'search';
	}
	if ( is_front_page() ) {
		return 'home';
	}

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$type    = (string) get_post_type( $post_id );
		$source  = estecapelli_analytics_source_id( $post_id, $type );
		$slug    = (string) get_post_field( 'post_name', $source );
		return $slug ?: (string) $post_id;
	}

	$term = get_queried_object();
	if ( $term instanceof WP_Term ) {
		$source = estecapelli_analytics_source_id( $term->term_id, $term->taxonomy );
		$mapped = get_term( $source, $term->taxonomy );
		return ( $mapped instanceof WP_Term ) ? $mapped->slug : $term->slug;
	}

	if ( is_home() ) {
		return 'blog';
	}

	return 'other';
}

/**
 * Broad template classification, used to segment behaviour by page role.
 *
 * @return string
 */
function estecapelli_analytics_page_type() {
	if ( is_404() ) {
		return 'not_found';
	}
	if ( is_search() ) {
		return 'search';
	}
	if ( is_front_page() ) {
		return 'home';
	}
	if ( is_singular( 'treatment' ) ) {
		return 'treatment';
	}
	if ( is_singular( 'doctor' ) ) {
		return 'doctor';
	}
	if ( is_singular( 'post' ) ) {
		return 'blog_post';
	}
	if ( is_tax( 'treatment_category' ) ) {
		return 'treatment_category';
	}
	if ( is_home() || is_category() || is_tag() || is_archive() ) {
		return 'blog_index';
	}

	if ( is_page() ) {
		// Keyed by the English slug, so a translated page classifies identically.
		$key   = estecapelli_analytics_page_key();
		$pages = array(
			'contact'         => 'contact',
			'blog'            => 'blog_index',
			'before-after'    => 'before_after',
			'our-doctors'     => 'doctors',
			'privacy-policy'  => 'legal',
			'cookie-policy'   => 'legal',
			'kvkk-disclosure' => 'legal',
			'terms'           => 'legal',
		);
		if ( isset( $pages[ $key ] ) ) {
			return $pages[ $key ];
		}
		return 'page';
	}

	return 'other';
}

/**
 * GA4 content group for the current page.
 *
 * Sent as the reserved `content_group` parameter, which populates GA4's own
 * "Content group" dimension without any custom-definition registration.
 *
 * @param string $page_type Result of estecapelli_analytics_page_type().
 * @return string
 */
function estecapelli_analytics_content_group( $page_type ) {
	$groups = array(
		'home'               => 'Home',
		'treatment'          => 'Treatments',
		'treatment_category' => 'Treatments',
		'doctor'             => 'Doctors',
		'doctors'            => 'Doctors',
		'blog_post'          => 'Blog',
		'blog_index'         => 'Blog',
		'contact'            => 'Contact',
		'before_after'       => 'Results',
		'legal'              => 'Legal',
	);

	return $groups[ $page_type ] ?? 'Other';
}

/**
 * The English treatment-category slug for the current page, or ''.
 *
 * Answers "does hair, dental or plastic traffic convert better" — the question
 * that actually decides where budget goes.
 *
 * @return string
 */
function estecapelli_analytics_treatment_category() {
	$term = null;

	if ( is_tax( 'treatment_category' ) ) {
		$queried = get_queried_object();
		$term    = ( $queried instanceof WP_Term ) ? $queried : null;
	} elseif ( is_singular( array( 'treatment', 'result' ) ) ) {
		$terms = get_the_terms( get_queried_object_id(), 'treatment_category' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$term = $terms[0];
		}
	}

	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$source = estecapelli_analytics_source_id( $term->term_id, 'treatment_category' );
	$mapped = get_term( $source, 'treatment_category' );

	return ( $mapped instanceof WP_Term ) ? $mapped->slug : $term->slug;
}

/**
 * Everything GA4 should know about this page before the first hit leaves.
 *
 * Pushed WITHOUT an `event` key: this is state, not an occurrence. GTM reads it
 * through Data Layer Variables, and because the push happens above the
 * container snippet the values are already present for the first page_view.
 *
 * @return array<string,mixed>
 */
function estecapelli_analytics_page_context() {
	$page_type = estecapelli_analytics_page_type();
	$language  = estecapelli_request_language_code();
	if ( ! $language ) {
		$language = estecapelli_indexed_language_code();
	}

	$context = array(
		'page_type'     => $page_type,
		'page_key'      => estecapelli_analytics_page_key(),
		'page_language' => $language,
		'content_group' => estecapelli_analytics_content_group( $page_type ),
		// Staff browsing the live site would otherwise pollute every funnel.
		// GTM blocks on this rather than the theme dropping the container, so
		// an administrator can still verify tracking in Preview mode.
		'user_type'     => is_user_logged_in() ? 'staff' : 'visitor',
	);

	$treatment_category = estecapelli_analytics_treatment_category();
	if ( $treatment_category ) {
		$context['treatment_category'] = $treatment_category;
	}

	if ( is_singular( 'treatment' ) ) {
		$source                    = estecapelli_analytics_source_id( get_queried_object_id(), 'treatment' );
		$context['treatment_name'] = wp_strip_all_tags( (string) get_the_title( $source ) );
	}

	if ( is_singular( 'doctor' ) ) {
		$source                 = estecapelli_analytics_source_id( get_queried_object_id(), 'doctor' );
		$context['doctor_name'] = wp_strip_all_tags( (string) get_the_title( $source ) );
	}

	if ( is_singular( 'post' ) ) {
		$categories = get_the_category( get_queried_object_id() );
		if ( $categories && ! is_wp_error( $categories ) ) {
			$source                   = estecapelli_analytics_source_id( $categories[0]->term_id, 'category' );
			$mapped                   = get_term( $source, 'category' );
			$context['blog_category'] = ( $mapped instanceof WP_Term ) ? $mapped->slug : $categories[0]->slug;
		}
	}

	if ( is_search() ) {
		$context['search_term'] = (string) get_search_query();
	}

	if ( is_404() ) {
		// The requested path is the whole point of a 404 report; GA4's own
		// page_location would only record where the visitor landed.
		$context['not_found_path'] = isset( $_SERVER['REQUEST_URI'] )
			? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
			: '';
	}

	return (array) apply_filters( 'estecapelli_analytics_page_context', $context );
}

/* -------------------------------------------------------------------------
 * Classic (non-AJAX) form results
 * ---------------------------------------------------------------------- */

/**
 * The outcome of a Post/Redirect/Get lead submission, or null.
 *
 * The popup, the footer and the Hair Lab submit over AJAX and report themselves
 * from JavaScript. The contact page and the in-page treatment form still POST
 * and redirect (see estecapelli_handle_lead), so their result exists only as
 * `?sent=1` or `?lead_error=<code>` on the next page load — invisible to any
 * click listener. Without this, the two forms most likely to be used by a
 * high-intent visitor would report zero conversions.
 *
 * The submitting form's identity is lost across the redirect, so it is
 * reconstructed from what does survive: the footer marker, then the page.
 *
 * @return array<string,string>|null
 */
function estecapelli_analytics_lead_result() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only
	// reporting of a value this theme itself put in the URL; nothing is changed.
	$sent  = isset( $_GET['sent'] ) ? sanitize_text_field( wp_unslash( $_GET['sent'] ) ) : '';
	$error = isset( $_GET['lead_error'] ) ? sanitize_key( wp_unslash( $_GET['lead_error'] ) ) : '';
	$form  = isset( $_GET['lead_form'] ) ? sanitize_key( wp_unslash( $_GET['lead_form'] ) ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	if ( '1' !== $sent && '' === $error ) {
		return null;
	}

	if ( 'footer' === $form ) {
		// Only reachable with JavaScript off; the AJAX path never redirects.
		$location = 'footer_nojs';
	} elseif ( 'contact' === estecapelli_analytics_page_type() ) {
		$location = 'contact_page';
	} else {
		$location = 'treatment_section';
	}

	return array(
		'status'   => $error ? 'error' : 'success',
		'location' => $location,
		'message'  => $error,
		'language' => estecapelli_lead_language_code(),
	);
}

/* -------------------------------------------------------------------------
 * Consent Mode v2
 * ---------------------------------------------------------------------- */

/**
 * Google Consent Mode v2 default state.
 *
 * Denied by default everywhere, not only in the EEA. Five of the seven public
 * languages are EU markets and the Turkish one is covered by KVKK, so a
 * region-scoped default would be exception-shaped rather than rule-shaped. It
 * also keeps the clinic defensible: nothing is stored before a visitor agrees.
 *
 * `wait_for_update` holds Google tags briefly so a returning visitor's stored
 * choice (replayed just below) is applied before the first hit is sent, rather
 * than that hit going out cookieless and being modelled unnecessarily.
 *
 * To relax this to EEA-only later, filter it:
 *
 *   add_filter( 'estecapelli_consent_defaults', function ( $d ) {
 *       $d['region'] = array( 'AT', 'BE', … , 'GB', 'CH' );
 *       return $d;
 *   } );
 *
 * @return array<string,mixed>
 */
function estecapelli_analytics_consent_defaults() {
	return (array) apply_filters(
		'estecapelli_consent_defaults',
		array(
			'ad_storage'             => 'denied',
			'ad_user_data'           => 'denied',
			'ad_personalization'     => 'denied',
			'analytics_storage'      => 'denied',
			'functionality_storage'  => 'granted',
			'personalization_storage' => 'denied',
			// Never gated: this covers anti-fraud and the Turnstile check that
			// protects every lead form.
			'security_storage'       => 'granted',
			'wait_for_update'        => 500,
		)
	);
}

/* -------------------------------------------------------------------------
 * The <head> bootstrap
 * ---------------------------------------------------------------------- */

/**
 * Print consent defaults + page context, immediately above the GTM snippet.
 *
 * Called from estecapelli_gtm_head() rather than hooked to wp_head, because
 * "before the container loads" is a hard requirement and hook ordering is not
 * a strong enough guarantee to bet the whole measurement setup on.
 */
function estecapelli_analytics_bootstrap() {
	if ( ! estecapelli_analytics_enabled() ) {
		return;
	}

	// JSON_HEX_TAG so a page title or treatment name containing "</script>"
	// cannot close the block it is being printed into. Still valid JSON.
	$flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;

	$defaults = wp_json_encode( estecapelli_analytics_consent_defaults(), $flags );
	$context  = wp_json_encode( estecapelli_analytics_page_context(), $flags );
	$cookie   = wp_json_encode( ESTECAPELLI_CONSENT_COOKIE, $flags );
	$version  = wp_json_encode( (string) ESTECAPELLI_CONSENT_VERSION, $flags );
	?>
<!-- Estecapelli measurement bootstrap (consent + page context) -->
<script data-nowprocket>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', <?php echo $defaults; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode output. ?>);
gtag('set', 'ads_data_redaction', true);
gtag('set', 'url_passthrough', true);
(function () {
	/* Replay a returning visitor's stored choice before any tag reads it. A
	   value from an older consent version is ignored, so the banner re-asks. */
	var name = <?php echo $cookie; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode output. ?>;
	var want = <?php echo $version; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode output. ?>;
	var hit  = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
	if (!hit) { return; }
	var parts = decodeURIComponent(hit[1]).split('|');
	if (parts[0] !== want) { return; }
	var stored = {};
	for (var i = 1; i < parts.length; i++) {
		var pair = parts[i].split(':');
		stored[pair[0]] = pair[1] === '1';
	}
	var analytics = stored.analytics ? 'granted' : 'denied';
	var marketing = stored.marketing ? 'granted' : 'denied';
	gtag('consent', 'update', {
		analytics_storage: analytics,
		ad_storage: marketing,
		ad_user_data: marketing,
		ad_personalization: marketing,
		personalization_storage: marketing
	});
	window.EstecapelliConsentState = { analytics: !!stored.analytics, marketing: !!stored.marketing, restored: true };
})();
dataLayer.push(<?php echo $context; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode output. ?>);
<?php
$lead_result = estecapelli_analytics_lead_result();
if ( $lead_result ) :
	?>
/* Result of a redirect-based form submit. analytics.js turns this into the
   event, and de-duplicates it so a page refresh does not count twice. */
window.EstecapelliLeadResult = <?php echo wp_json_encode( $lead_result, $flags ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode output. ?>;
<?php endif; ?>
</script>
<!-- End Estecapelli measurement bootstrap -->
	<?php
}

/* -------------------------------------------------------------------------
 * Front-end scripts
 * ---------------------------------------------------------------------- */

/**
 * Load the tracker and the consent banner controller.
 *
 * Both go in the footer but are excluded from WP Rocket's delayed JavaScript
 * (see estecapelli_analytics_skip_js_delay): delaying them until first
 * interaction would drop every event fired by that same first interaction, and
 * would leave the consent banner invisible until the visitor clicked something.
 */
function estecapelli_analytics_enqueue() {
	if ( ! estecapelli_analytics_enabled() ) {
		return;
	}

	wp_enqueue_script(
		'estecapelli-analytics',
		get_template_directory_uri() . '/assets/js/analytics.js',
		array(),
		estecapelli_asset_ver( '/assets/js/analytics.js' ),
		true
	);

	wp_enqueue_script(
		'estecapelli-consent',
		get_template_directory_uri() . '/assets/js/consent.js',
		array(),
		estecapelli_asset_ver( '/assets/js/consent.js' ),
		true
	);

	wp_localize_script(
		'estecapelli-consent',
		'EstecapelliConsent',
		array(
			'cookie'   => ESTECAPELLI_CONSENT_COOKIE,
			'version'  => (string) ESTECAPELLI_CONSENT_VERSION,
			'lifetime' => (int) ESTECAPELLI_CONSENT_LIFETIME,
			'secure'   => is_ssl(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'estecapelli_analytics_enqueue', 22 );

/**
 * Keep both scripts out of WP Rocket's Delay JavaScript Execution pass.
 *
 * @param string $tag    Script tag HTML.
 * @param string $handle Registered handle.
 * @return string
 */
function estecapelli_analytics_skip_js_delay( $tag, $handle ) {
	$critical = array( 'estecapelli-analytics', 'estecapelli-consent' );
	if ( ! in_array( $handle, $critical, true ) || false !== strpos( $tag, 'data-nowprocket' ) ) {
		return $tag;
	}
	return preg_replace( '/<script\b/', '<script data-nowprocket', $tag, 1 );
}
add_filter( 'script_loader_tag', 'estecapelli_analytics_skip_js_delay', 10, 2 );

/**
 * Render the consent banner in the footer.
 *
 * Always printed (hidden) rather than injected by JS, so the markup is in the
 * document for screen readers and needs no layout shift to appear.
 */
function estecapelli_analytics_render_consent_banner() {
	if ( ! estecapelli_analytics_enabled() ) {
		return;
	}
	get_template_part( 'template-parts/consent-banner' );
}
add_action( 'wp_footer', 'estecapelli_analytics_render_consent_banner', 5 );
