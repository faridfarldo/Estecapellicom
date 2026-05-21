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
	define( 'ESTECAPELLI_VERSION', '1.12.0' );
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
		ESTECAPELLI_VERSION
	);

	wp_enqueue_script(
		'estecapelli-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		ESTECAPELLI_VERSION,
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
