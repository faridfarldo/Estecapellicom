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

if ( ! defined( 'ESTECAPELLI_COUNTRY_COUNT' ) ) {
	define( 'ESTECAPELLI_COUNTRY_COUNT', '40' );
}

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
require get_template_directory() . '/inc/before-after.php';
require get_template_directory() . '/inc/toc.php';
require get_template_directory() . '/inc/redirects.php';
require get_template_directory() . '/inc/tricholab-sync.php';
require get_template_directory() . '/inc/canonical.php';
require get_template_directory() . '/inc/seo.php';
require get_template_directory() . '/inc/wpml-fix.php';
require get_template_directory() . '/inc/fr-string-fallbacks.php';
require get_template_directory() . '/inc/it-navigation.php';
require get_template_directory() . '/inc/it-string-fallbacks.php';
require get_template_directory() . '/inc/wpml-slug-fix.php';
require get_template_directory() . '/inc/local-en-routing.php';
require get_template_directory() . '/inc/leads.php';
require get_template_directory() . '/inc/hair-analysis.php';
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin/import-treatments.php';
	require get_template_directory() . '/inc/admin/import-fr-hair-treatments.php';
	require get_template_directory() . '/inc/admin/import-it-hair-treatments.php';
	require get_template_directory() . '/inc/admin/import-pl-hair-treatments.php';
	require get_template_directory() . '/inc/admin/import-it-hair-pages.php';
	require get_template_directory() . '/inc/admin/import-it-pages.php';
	require get_template_directory() . '/inc/admin/import-fr-plastic-treatments.php';
	require get_template_directory() . '/inc/admin/import-it-plastic-treatments.php';
	require get_template_directory() . '/inc/admin/import-fr-dental-treatments.php';
	require get_template_directory() . '/inc/admin/import-it-dental-treatments.php';
	require get_template_directory() . '/inc/admin/import-it-doctors.php';
	require get_template_directory() . '/inc/admin/import-fr-doctors.php';
	require get_template_directory() . '/inc/admin/import-fr-pages.php';
	require get_template_directory() . '/inc/admin/import-fr-blog.php';
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

function estecapelli_enqueue_assets() {
	wp_enqueue_style(
		'estecapelli-fonts',
		'https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'estecapelli-style',
		get_stylesheet_uri(),
		array( 'estecapelli-fonts' ),
		ESTECAPELLI_VERSION
	);

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

	// Lead popup: AJAX endpoint + nonce, and the contact-page paths whose links
	// should open the popup instead of navigating. See assets/js/main.js.
	wp_localize_script(
		'estecapelli-main',
		'EstecapelliLead',
		array(
			'ajax'         => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'estecapelli_lead_ajax' ),
			'contactPaths' => array( '/en/contact', '/contact' ),
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
