<?php
/**
 * Section engine — renders ACF page_sections for any post type that opts in.
 *
 * Used by single-treatment.php and page.php. Returns true if sections were
 * rendered, false if the post has no sections (caller falls back to its
 * own default markup).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'estecapelli_render_page_sections' ) ) {
	function estecapelli_render_page_sections( $post_id = null ) {

		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		$post_id  = $post_id ?: get_the_ID();
		$sections = get_field( 'page_sections', $post_id );

		if ( empty( $sections ) || ! is_array( $sections ) ) {
			return false;
		}

		foreach ( $sections as $section ) {
			$layout = $section['acf_fc_layout'] ?? '';
			if ( ! $layout ) {
				continue;
			}

			// locate_template + sanitize_file_name guards against arbitrary paths.
			$template = locate_template( 'template-parts/sections/' . sanitize_file_name( $layout ) . '.php' );
			if ( ! $template ) {
				continue;
			}

			set_query_var( 'section', $section );
			load_template( $template, false );
		}

		return true;
	}
}
