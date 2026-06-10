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
	define( 'ESTECAPELLI_VERSION', '1.20.0' );
}

if ( ! defined( 'ESTECAPELLI_WHATSAPP' ) ) {
	define( 'ESTECAPELLI_WHATSAPP', '905431488888' );
}

if ( ! defined( 'ESTECAPELLI_PATIENT_COUNT' ) ) {
	define( 'ESTECAPELLI_PATIENT_COUNT', '15,000' );
}

if ( ! defined( 'ESTECAPELLI_COUNTRY_COUNT' ) ) {
	define( 'ESTECAPELLI_COUNTRY_COUNT', '40' );
}

require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/class-estecapelli-walker-nav-menu.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/acf-field-groups.php';
require get_template_directory() . '/inc/engine.php';
require get_template_directory() . '/inc/before-after.php';
require get_template_directory() . '/inc/redirects.php';
require get_template_directory() . '/inc/local-en-routing.php';
require get_template_directory() . '/inc/leads.php';
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin/import-treatments.php';
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
 */
function estecapelli_maybe_flush_rewrite_rules() {
	if ( get_option( 'estecapelli_rewrite_version' ) !== ESTECAPELLI_VERSION ) {
		flush_rewrite_rules();
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
