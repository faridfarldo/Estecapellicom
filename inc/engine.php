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

if ( ! function_exists( 'estecapelli_prepare_page_section_for_render' ) ) {
	/**
	 * Keep legacy TrichoLab translations visually aligned with the English page.
	 *
	 * Some translated records still store the process as the old "stepbook"
	 * layout. The English page uses the compact three-card steps timeline. This
	 * render-only compatibility layer preserves all translated copy and all
	 * three steps while avoiding a destructive database rewrite.
	 *
	 * @param array $section ACF flexible-content row.
	 * @param int   $post_id Current post ID.
	 * @return array
	 */
	function estecapelli_prepare_page_section_for_render( array $section, $post_id ) {
		// Before/after galleries are language-neutral: the composite images live
		// only on the default-language treatment. Translated posts keep an
		// independent (ACFML "Copy Once") gallery whose row count can drift from the
		// source when images are added or removed in the default language — leaving
		// stale rows that render as duplicated or wrong photos. Pull the gallery
		// items straight from the source-language post on translations so the two
		// can never diverge. Translated labels (title/eyebrow/lead) are untouched.
		if ( 'gallery' === ( $section['acf_fc_layout'] ?? '' ) && function_exists( 'estecapelli_collect_gallery_items' ) ) {
			$default_lang = apply_filters( 'wpml_default_language', null );
			$current_lang = apply_filters( 'wpml_current_language', null );
			if ( $default_lang && $current_lang && $default_lang !== $current_lang ) {
				$source_id = (int) apply_filters( 'wpml_object_id', $post_id, get_post_type( $post_id ), false, $default_lang );
				if ( $source_id && $source_id !== (int) $post_id ) {
					$section['items'] = estecapelli_collect_gallery_items( get_field( 'page_sections', $source_id ) );
				}
			}
		}

		$is_tricholab = 'tricholab' === get_post_field( 'post_name', $post_id );
		$is_stepbook  = 'stepbook' === ( $section['acf_fc_layout'] ?? '' );

		if ( ! $is_tricholab || ! $is_stepbook ) {
			return $section;
		}

		$section['acf_fc_layout'] = 'steps';

		if ( ! empty( $section['items'] ) && is_array( $section['items'] ) ) {
			foreach ( $section['items'] as &$item ) {
				if ( empty( $item['time'] ) && ! empty( $item['eyebrow'] ) ) {
					$item['time'] = $item['eyebrow'];
				}
			}
			unset( $item );
		}

		return $section;
	}
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
			$section = estecapelli_prepare_page_section_for_render( $section, $post_id );
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
