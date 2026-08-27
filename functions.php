<?php
/**
 * Estecapelli theme functions.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_VERSION' ) ) {
	define( 'ESTECAPELLI_VERSION', '1.30.0' );
}

if ( ! defined( 'ESTECAPELLI_WHATSAPP' ) ) {
	define( 'ESTECAPELLI_WHATSAPP', '905415410041' );
}

if ( ! defined( 'ESTECAPELLI_PATIENT_COUNT' ) ) {
	define( 'ESTECAPELLI_PATIENT_COUNT', '15,000' );
}

/**
 * Google Tag Manager container. Override in wp-config.php to point a staging
 * copy at a different container, or set it to '' to disable tracking entirely.
 */
if ( ! defined( 'ESTECAPELLI_GTM_ID' ) ) {
	define( 'ESTECAPELLI_GTM_ID', 'GTM-WVPFK35' );
}

if ( ! defined( 'ESTECAPELLI_COUNTRY_COUNT' ) ) {
	define( 'ESTECAPELLI_COUNTRY_COUNT', '40' );
}

/**
 * Production content safety lock.
 *
 * WordPress is the authoritative content store. Legacy seed/import utilities
 * must not register menus, admin-post handlers or automatic migrations unless
 * an operator deliberately opts in before the theme loads (for example in a
 * staging-only wp-config.php). The production-safe default is locked.
 */
if ( ! defined( 'ESTECAPELLI_ENABLE_CONTENT_MUTATIONS' ) ) {
	define( 'ESTECAPELLI_ENABLE_CONTENT_MUTATIONS', false );
}

/** Whether legacy importers and one-time content writers may run. */
function estecapelli_content_mutations_enabled() {
	return true === ESTECAPELLI_ENABLE_CONTENT_MUTATIONS;
}

require get_template_directory() . '/inc/db-error-display.php';
require get_template_directory() . '/inc/indexed-urls.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/svg-support.php';
require get_template_directory() . '/inc/class-estecapelli-walker-nav-menu.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/acfml-field-prefs.php';
require get_template_directory() . '/inc/acf-field-groups.php';
require get_template_directory() . '/inc/blog-seed.php';
require get_template_directory() . '/inc/engine.php';
require get_template_directory() . '/inc/acfml-layout-guard.php';
require get_template_directory() . '/inc/wpml-native-editor.php';
require get_template_directory() . '/inc/before-after.php';
require get_template_directory() . '/inc/toc.php';
require get_template_directory() . '/inc/redirects.php';
require get_template_directory() . '/inc/tricholab-sync.php';
require get_template_directory() . '/inc/canonical.php';
require get_template_directory() . '/inc/seo.php';
require get_template_directory() . '/inc/wpml-fix.php';
require get_template_directory() . '/inc/fr-string-fallbacks.php';
require get_template_directory() . '/inc/fr-content-revisions.php';
require get_template_directory() . '/inc/tr-string-fallbacks.php';
require get_template_directory() . '/inc/tr-content-revisions.php';
require get_template_directory() . '/inc/tr-site-revisions.php';
require get_template_directory() . '/inc/it-navigation.php';
require get_template_directory() . '/inc/it-string-fallbacks.php';
require get_template_directory() . '/inc/it-content-revisions.php';
require get_template_directory() . '/inc/pl-string-fallbacks.php';
require get_template_directory() . '/inc/pl-content-revisions.php';
require get_template_directory() . '/inc/es-string-fallbacks.php';
require get_template_directory() . '/inc/pt-string-fallbacks.php';
require get_template_directory() . '/inc/ro-string-fallbacks.php';
require get_template_directory() . '/inc/legal-string-fallbacks.php';
require get_template_directory() . '/inc/home-hero-translations.php';
require get_template_directory() . '/inc/nav-translations.php';
require get_template_directory() . '/inc/wpml-slug-fix.php';
require get_template_directory() . '/inc/local-en-routing.php';
require get_template_directory() . '/inc/leads.php';
require get_template_directory() . '/inc/lead-guard.php';
require get_template_directory() . '/inc/hair-analysis.php';
// After leads.php: the measurement layer reads its language + result helpers.
require get_template_directory() . '/inc/analytics.php';
require get_template_directory() . '/inc/consent-string-fallbacks.php';
// Read-only reports, so they stay available even when content mutations are
// locked. Each carries its own opt-in constant for the actions it offers.
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin/wpml-page-diagnostic.php';
	require get_template_directory() . '/inc/admin/translation-lock-diagnostic.php';
	require get_template_directory() . '/inc/admin/translation-save-recorder.php';
}

/**
 * Polish page repair. Its own constant, not ESTECAPELLI_ENABLE_CONTENT_MUTATIONS,
 * so repairing a translation never puts the bulk importers — "Re-import ALL
 * Pages" among them — one misclick away. Enable it in wp-config.php only for as
 * long as the repair takes.
 */
if ( is_admin() && defined( 'ESTECAPELLI_ENABLE_PL_PAGE_REPAIR' ) && true === ESTECAPELLI_ENABLE_PL_PAGE_REPAIR ) {
	require get_template_directory() . '/inc/admin/repair-pl-pages.php';
}

if ( is_admin() && estecapelli_content_mutations_enabled() ) {
	require get_template_directory() . '/inc/admin/import-database-guard.php';
	require get_template_directory() . '/inc/admin/import-treatments.php';
	require get_template_directory() . '/inc/admin/import-fr-hair-treatments.php';
	require get_template_directory() . '/inc/admin/import-it-hair-treatments.php';
	require get_template_directory() . '/inc/admin/import-pl-hair-treatments.php';
	require get_template_directory() . '/inc/admin/import-es-hair-treatments.php';
	require get_template_directory() . '/inc/admin/import-pt-hair-treatments.php';
	require get_template_directory() . '/inc/admin/import-ro-hair-treatments.php';
	require get_template_directory() . '/inc/admin/import-pl-plastic-treatments.php';
	require get_template_directory() . '/inc/admin/import-it-hair-pages.php';
	require get_template_directory() . '/inc/admin/import-pl-dental-treatments.php';
	require get_template_directory() . '/inc/admin/import-pl-pages.php';
	require get_template_directory() . '/inc/admin/import-it-pages.php';
	require get_template_directory() . '/inc/admin/import-fr-plastic-treatments.php';
	require get_template_directory() . '/inc/admin/import-it-plastic-treatments.php';
	require get_template_directory() . '/inc/admin/import-es-plastic-treatments.php';
	require get_template_directory() . '/inc/admin/import-pt-plastic-treatments.php';
	require get_template_directory() . '/inc/admin/import-ro-plastic-treatments.php';
	require get_template_directory() . '/inc/admin/import-fr-dental-treatments.php';
	require get_template_directory() . '/inc/admin/import-it-dental-treatments.php';
	require get_template_directory() . '/inc/admin/import-es-dental-treatments.php';
	require get_template_directory() . '/inc/admin/import-pt-dental-treatments.php';
	require get_template_directory() . '/inc/admin/import-ro-dental-treatments.php';
	// Loaded after the Italian Hair, Plastic and Dental import engines it shares.
	require get_template_directory() . '/inc/admin/import-tr-treatments.php';
	require get_template_directory() . '/inc/admin/import-it-doctors.php';
	// Loaded after the shared Italian page, template-page and doctor engines.
	require get_template_directory() . '/inc/admin/import-tr-pages.php';
	// Loaded after import-it-doctors.php, whose engine it shares.
	require get_template_directory() . '/inc/admin/import-pl-doctors.php';
	require get_template_directory() . '/inc/admin/import-es-pages.php';
	require get_template_directory() . '/inc/admin/import-pt-pages.php';
	require get_template_directory() . '/inc/admin/import-ro-pages.php';
	// Loaded after import-it-doctors.php, whose engine it shares.
	require get_template_directory() . '/inc/admin/import-fr-doctors.php';
	require get_template_directory() . '/inc/admin/import-fr-pages.php';
	require get_template_directory() . '/inc/admin/import-fr-blog.php';
	// Unified blog translation + Rank Math SEO importer (all languages, one run).
	require get_template_directory() . '/inc/admin/import-blog-translations.php';
	// Loaded last because it reuses the shared page and WPML repair helpers.
	require get_template_directory() . '/inc/admin/import-legal-pages.php';
}

if ( ! function_exists( 'estecapelli_setup' ) ) {
	function estecapelli_setup() {
		load_theme_textdomain( 'estecapelli', get_template_directory() . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'custom-logo', array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		) );

		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		) );

		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );

		register_nav_menus( array(
			'primary' => __( 'Primary Menu', 'estecapelli' ),
			'footer'  => __( 'Footer Menu', 'estecapelli' ),
		) );
	}
}
add_action( 'after_setup_theme', 'estecapelli_setup' );

/**
 * Strip a dangling separator from the <title>, e.g. "Estecapelli -".
 *
 * This happens when the Site Tagline is empty but the title template still ends
 * with a separator + tagline (common with SEO plugins like Rank Math/Yoast, and
 * possible with a blank tagline on the core title-tag). We clean it in three
 * places so the fix holds no matter which is generating the title.
 */
function estecapelli_trim_title_separator( $title ) {
	// Drop a trailing separator (-, –, —, |, ·, •, ~, :) plus surrounding space.
	return preg_replace( '/\s*[-–—|·•~:]+\s*$/u', '', (string) $title );
}
add_filter( 'rank_math/frontend/title', 'estecapelli_trim_title_separator', 99 ); // Rank Math
add_filter( 'wpseo_title', 'estecapelli_trim_title_separator', 99 );               // Yoast (harmless if absent)

// Core title-tag path: drop any part that is blank or only a separator so the
// join can't leave a trailing dash.
add_filter( 'document_title_parts', function ( $parts ) {
	foreach ( $parts as $key => $value ) {
		if ( '' === trim( wp_strip_all_tags( (string) $value ), " \t\n\r\0\x0B-–—|·•~:" ) ) {
			unset( $parts[ $key ] );
		}
	}
	return $parts;
} );

/**
 * Flush rewrite rules once per theme version.
 *
 * The `treatment` CPT registers a `/treatments/%slug%/` permalink. Those
 * pretty URLs only resolve after rewrite rules are flushed; otherwise the
 * site falls back to the ugly `?treatment=slug` query form. Because the
 * theme is deployed by `git pull` (not a real theme switch), we gate the
 * flush on ESTECAPELLI_VERSION: bump the version on deploy and rules are
 * regenerated exactly once, with no per-request cost afterwards.
 *
 * Runs after the CPT is registered (init priority 0) so its rules exist.
 *
 * Note: this only produces pretty URLs when the site's permalink structure
 * is set to something other than "Plain" (Settings → Permalinks).
 *
 * IMPORTANT: we pass `false` for a SOFT flush. A hard flush (the default)
 * rewrites the site's .htaccess between the `# BEGIN WordPress` markers, which
 * WIPES any HTTPS-forcing rules placed inside that block (e.g. added by
 * "Really Simple SSL" or by hand) — that made SSL break on every deploy, since
 * every deploy bumps ESTECAPELLI_VERSION and triggers this. A soft flush only
 * regenerates the `rewrite_rules` option in the DB (all that the treatment CPT
 * needs) and never touches .htaccess.
 */
function estecapelli_maybe_flush_rewrite_rules() {
	if ( get_option( 'estecapelli_rewrite_version' ) !== ESTECAPELLI_VERSION ) {
		flush_rewrite_rules( false ); // soft flush — do NOT rewrite .htaccess
		update_option( 'estecapelli_rewrite_version', ESTECAPELLI_VERSION );
	}
}
add_action( 'init', 'estecapelli_maybe_flush_rewrite_rules', 99 );

/**
 * Cache-busting asset version: file modification time when available,
 * falling back to the theme version. Ensures browsers fetch fresh CSS/JS
 * whenever the file actually changes.
 *
 * @param string $relative_path Path relative to the theme root, e.g. '/assets/css/main.css'.
 * @return string
 */
function estecapelli_asset_ver( $relative_path ) {
	$file = get_template_directory() . $relative_path;
	return file_exists( $file ) ? (string) filemtime( $file ) : ESTECAPELLI_VERSION;
}

/**
 * Output a favicon built from the theme logo when no Site Icon is set in the
 * Customizer. Once an editor uploads a Site Icon (Appearance → Customize →
 * Site Identity), WordPress prints its own (better, multi-size) tags and this
 * fallback steps aside to avoid duplicates.
 */
add_action( 'wp_head', 'estecapelli_fallback_favicon', 4 );
function estecapelli_fallback_favicon() {
	if ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
		return;
	}
	$path = '/assets/images/logo.png';
	if ( ! file_exists( get_template_directory() . $path ) ) {
		return;
	}
	$url = add_query_arg( 'v', estecapelli_asset_ver( $path ), get_template_directory_uri() . $path );
	printf( '<link rel="icon" type="image/png" href="%s" />' . "\n", esc_url( $url ) );
	printf( '<link rel="apple-touch-icon" href="%s" />' . "\n", esc_url( $url ) );
}

/**
 * Self-hosted DM Sans.
 *
 * This used to be a <link> to fonts.googleapis.com, which put a third-party
 * origin on the critical path twice over: the browser had to fetch and parse
 * a stylesheet from googleapis.com before it even learned the font lived on
 * gstatic.com. Serving the two woff2 subsets ourselves removes both hops —
 * they come off our own connection, already open, and Cloudflare caches them
 * for a year like every other static asset.
 *
 * DM Sans is a variable font: all four weights are the same file. The faces
 * are declared per weight exactly as Google's own CSS did, so the browser
 * picks the same axis position it always has.
 */
function estecapelli_font_face_css() {
	$base = get_template_directory_uri() . '/assets/fonts/';
	$subsets = array(
		// Latin covers en/fr/it/es. latin-ext carries the Turkish and Polish
		// diacritics (ş ğ ł ą ę ż) — both are required on this site.
		'latin'     => 'U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD',
		'latin-ext' => 'U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF',
	);

	$css = '';
	foreach ( $subsets as $subset => $range ) {
		foreach ( array( 400, 500, 600, 700 ) as $weight ) {
			$css .= sprintf(
				'@font-face{font-family:"DM Sans";font-style:normal;font-weight:%d;font-display:swap;src:url(%s) format("woff2");unicode-range:%s}',
				$weight,
				esc_url( $base . 'dm-sans-' . $subset . '.woff2' ),
				$range
			);
		}
	}
	return $css;
}

/**
 * Preload the Latin subset. Text is the first thing painted on every page, and
 * font-display:swap means a late font arrival re-lays-out that text; starting
 * the fetch in the first round trip avoids the flash. latin-ext is left to
 * normal discovery — only tr/pl need it, and only for a handful of glyphs.
 */
function estecapelli_preload_font() {
	printf(
		"\t<link rel=\"preload\" as=\"font\" type=\"font/woff2\" href=\"%s\" crossorigin />\n",
		esc_url( get_template_directory_uri() . '/assets/fonts/dm-sans-latin.woff2' )
	);
}
add_action( 'wp_head', 'estecapelli_preload_font', 1 );

function estecapelli_enqueue_assets() {
	wp_enqueue_style(
		'estecapelli-style',
		get_stylesheet_uri(),
		array(),
		ESTECAPELLI_VERSION
	);
	wp_add_inline_style( 'estecapelli-style', estecapelli_font_face_css() );

	wp_enqueue_style(
		'estecapelli-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'estecapelli-style' ),
		estecapelli_asset_ver( '/assets/css/main.css' )
	);

	wp_enqueue_script(
		'estecapelli-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		estecapelli_asset_ver( '/assets/js/main.js' ),
		true
	);

	// Lead popup: AJAX endpoint + nonce, and every localized contact-page path
	// whose CTA buttons should open the popup instead of navigating. Keep the
	// unprefixed legacy route as a fallback for older links and cached markup.
	$lead_contact_paths = array( '/contact' );
	foreach ( estecapelli_indexed_languages() as $language ) {
		$contact_path = estecapelli_indexed_route_path( '/en/contact', $language );
		if ( $contact_path ) {
			$lead_contact_paths[] = untrailingslashit( $contact_path );
		}
	}
	$lead_contact_paths = array_values( array_unique( $lead_contact_paths ) );

	wp_localize_script(
		'estecapelli-main',
		'EstecapelliLead',
		array(
			'ajax'         => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'estecapelli_lead_ajax' ),
			'contactPaths' => $lead_contact_paths,
			'i18n'         => array(
				'sending' => __( 'Sending…', 'estecapelli' ),
				'thanks'  => __( 'Thank you! Your request has been received — our team will contact you shortly.', 'estecapelli' ),
				'error'   => __( 'Something went wrong. Please try again or reach us on WhatsApp.', 'estecapelli' ),
			),
		)
	);

	// International phone input — auto-selects the country dial code from the
	// visitor's IP. Upgrades any input.js-intl-phone (footer, contact, form section).
	wp_enqueue_style(
		'intl-tel-input',
		'https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/css/intlTelInput.css',
		array(),
		'24.6.0'
	);
	wp_enqueue_script(
		'intl-tel-input',
		'https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/intlTelInput.min.js',
		array(),
		'24.6.0',
		true
	);
	wp_enqueue_script(
		'estecapelli-phone',
		get_template_directory_uri() . '/assets/js/phone-intl.js',
		array( 'intl-tel-input' ),
		estecapelli_asset_ver( '/assets/js/phone-intl.js' ),
		true
	);

	// Validation messages for the two front-end form controllers. Both scripts
	// carry English fallbacks inline, so these have to ship on every request or
	// a non-English visitor gets an English error under a translated field.
	wp_add_inline_script(
		'estecapelli-main',
		'window.EstecapelliLeadServerErrors=' . wp_json_encode(
			array(
				'Please enter your name.'            => __( 'Please enter your name.', 'estecapelli' ),
				'Name is required.'                  => __( 'Name is required.', 'estecapelli' ),
				'Please enter your phone number.'    => __( 'Please enter your phone number.', 'estecapelli' ),
				'Please enter a valid phone number.' => __( 'Please enter a valid phone number.', 'estecapelli' ),
				'Please enter a valid email address.' => __( 'Please enter a valid email address.', 'estecapelli' ),
				'Please refresh the page and submit the form again.' => __( 'Please refresh the page and submit the form again.', 'estecapelli' ),
				'Too many requests. Please wait a few minutes and try again.' => __( 'Too many requests. Please wait a few minutes and try again.', 'estecapelli' ),
			)
		) . ';',
		'before'
	);
	wp_add_inline_script(
		'estecapelli-phone',
		'window.EstecapelliPhone=' . wp_json_encode(
			array(
				'i18n' => array(
					'required'       => __( 'Please enter your phone number.', 'estecapelli' ),
					'invalid'        => __( 'Please enter a valid phone number.', 'estecapelli' ),
					'countryCode'    => __( 'Please select a valid country code.', 'estecapelli' ),
					'tooShort'       => __( 'This number is too short for the selected country.', 'estecapelli' ),
					'tooLong'        => __( 'This number is too long for the selected country.', 'estecapelli' ),
					'areaCode'       => __( 'Please enter the full phone number, including the area code.', 'estecapelli' ),
					'invalidCountry' => __( 'Please enter a valid phone number for the selected country.', 'estecapelli' ),
				),
			)
		) . ';',
		'before'
	);

	// Hair Analysis Lab widget styles — only on the front page where it mounts.
	if ( is_front_page() ) {
		wp_enqueue_style(
			'estecapelli-hair-widget',
			get_template_directory_uri() . '/assets/hair-widget/css/widget.css',
			array( 'estecapelli-main' ),
			estecapelli_asset_ver( '/assets/hair-widget/css/widget.css' )
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'estecapelli_enqueue_assets' );

/**
 * Take the phone-widget stylesheet off the critical path.
 *
 * intl-tel-input's CSS only styles the country dropdown on a phone field — the
 * footer form, the contact page and the lead popup. None of that is on screen
 * at first paint, yet as a plain <link> in the head it blocks rendering on a
 * third-party origin (DNS + TLS + download before anything shows). Loading it
 * as media="print" makes the browser fetch it without blocking, and the onload
 * handler promotes it to all media the moment it arrives. The <noscript> copy
 * keeps it working with JavaScript off.
 *
 * WP Rocket cannot do this for us: it leaves external stylesheets alone.
 */
function estecapelli_async_noncritical_styles( $tag, $handle ) {
	if ( 'intl-tel-input' !== $handle || is_admin() ) {
		return $tag;
	}
	$async = str_replace( "media='all'", "media='print' onload=\"this.media='all';this.onload=null\"", $tag );
	if ( $async === $tag ) {
		return $tag; // Markup was not what we expected — leave it render-blocking rather than break it.
	}
	return $async . '<noscript>' . $tag . '</noscript>' . "\n";
}
add_filter( 'style_loader_tag', 'estecapelli_async_noncritical_styles', 10, 2 );

function estecapelli_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer Column 1', 'estecapelli' ),
		'id'            => 'footer-1',
		'description'   => __( 'First footer widget column.', 'estecapelli' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget__title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Column 2', 'estecapelli' ),
		'id'            => 'footer-2',
		'description'   => __( 'Second footer widget column.', 'estecapelli' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget__title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Column 3', 'estecapelli' ),
		'id'            => 'footer-3',
		'description'   => __( 'Third footer widget column.', 'estecapelli' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget__title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Column 4', 'estecapelli' ),
		'id'            => 'footer-4',
		'description'   => __( 'Fourth footer widget column.', 'estecapelli' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget__title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'estecapelli_widgets_init' );
