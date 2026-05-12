<?php
/**
 * Template tags — reusable presentational helpers.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'estecapelli_primary_menu_fallback' ) ) {
	/**
	 * Fallback nav links shown when no menu is assigned to the "primary" location.
	 * Lets the site display a usable header out-of-the-box before WP admin setup.
	 */
	function estecapelli_primary_menu_fallback() {
		$links = array(
			home_url( '/treatments/hair-transplant/' )  => __( 'Hair Transplant', 'estecapelli' ),
			home_url( '/treatments/plastic-surgery/' )  => __( 'Plastic Surgery', 'estecapelli' ),
			home_url( '/treatments/dental/' )           => __( 'Dental', 'estecapelli' ),
			home_url( '/before-after/' )                => __( 'Before & After', 'estecapelli' ),
			home_url( '/about/' )                       => __( 'About', 'estecapelli' ),
			home_url( '/blog/' )                        => __( 'Blog', 'estecapelli' ),
			home_url( '/contact/' )                     => __( 'Contact', 'estecapelli' ),
		);
		echo '<ul class="site-nav__list">';
		foreach ( $links as $url => $label ) {
			printf(
				'<li><a href="%1$s">%2$s</a></li>',
				esc_url( $url ),
				esc_html( $label )
			);
		}
		echo '</ul>';
	}
}

if ( ! function_exists( 'estecapelli_brand_mark' ) ) {
	/**
	 * Render the brand mark (logo image if present, else styled text wordmark).
	 *
	 * Picks the first available file from /assets/images/ in this order:
	 *   logo.svg, logo-horizontal.svg, logo.png, logo-horizontal.png
	 *
	 * Falls back to a CSS-styled "ESTE CAPELLI" wordmark when no file exists.
	 *
	 * @param string $context 'header' or 'footer' — used for CSS class scoping.
	 */
	function estecapelli_brand_mark( $context = 'header' ) {
		$candidates = array( 'logo.svg', 'logo-horizontal.svg', 'logo.png', 'logo-horizontal.png' );
		$base_dir   = get_template_directory() . '/assets/images/';
		$base_uri   = get_template_directory_uri() . '/assets/images/';
		$logo_url   = '';

		foreach ( $candidates as $file ) {
			if ( file_exists( $base_dir . $file ) ) {
				$logo_url = $base_uri . $file;
				break;
			}
		}

		$class_prefix = ( 'footer' === $context ) ? 'site-footer' : 'site-header';

		if ( $logo_url ) {
			printf(
				'<img class="%1$s__logo" src="%2$s" alt="%3$s" width="240" height="44" />',
				esc_attr( $class_prefix ),
				esc_url( $logo_url ),
				esc_attr( get_bloginfo( 'name' ) )
			);
			return;
		}

		printf(
			'<span class="%1$s__logo-text"><span>Este</span><span>capelli</span></span>',
			esc_attr( $class_prefix )
		);
	}
}
