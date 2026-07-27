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
	 * @param array      $section        ACF flexible-content row.
	 * @param int        $post_id        Current post ID.
	 * @param array|null $source_section Same-index row from the default-language
	 *                                   post, when the current post is a translation.
	 * @return array
	 */
	function estecapelli_prepare_page_section_for_render( array $section, $post_id, $source_section = null ) {
		// Shared uploaded section images (intro, candidate, hero, …) are attached
		// only to the default-language post via the ACF image field; the per-language
		// JSON carries no image for them, so a translation renders the section with an
		// empty image slot. When the translation has no image of its own — and no
		// localized text graphic — borrow the shared image from the source row at the
		// same index. A translation that ships its own localized_image_url or image
		// keeps it. Pure display, no database write.
		if ( is_array( $source_section )
			&& ( $section['acf_fc_layout'] ?? '' ) === ( $source_section['acf_fc_layout'] ?? '' ) ) {
			$has_localized = ! empty( $section['localized_image_url'] );
			$has_image     = ! empty( $section['image'] ) && ! empty( $section['image']['url'] );
			$has_image_url = ! empty( $section['image_url'] );
			if ( ! $has_localized && ! $has_image && ! $has_image_url ) {
				if ( ! empty( $source_section['image'] ) && is_array( $source_section['image'] ) && ! empty( $source_section['image']['url'] ) ) {
					$section['image'] = $source_section['image'];
				} elseif ( ! empty( $source_section['image_url'] ) ) {
					$section['image_url'] = $source_section['image_url'];
				}
			}
		}

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

		// When this post is a translation, load the default-language builder once so
		// each row can borrow a shared uploaded image the JSON translation omits.
		// Rows are grouped by layout in source order: a translation row then finds
		// its counterpart as "the Nth row of this layout", which survives unrelated
		// sections being inserted or removed at a different index in either language
		// (the live section maps can drift from the JSON per language).
		$source_by_layout = array();
		$default_lang = apply_filters( 'wpml_default_language', null );
		$current_lang = apply_filters( 'wpml_current_language', null );
		if ( $default_lang && $current_lang && $default_lang !== $current_lang ) {
			$source_id = (int) apply_filters( 'wpml_object_id', $post_id, get_post_type( $post_id ), false, $default_lang );
			if ( $source_id && $source_id !== (int) $post_id ) {
				$maybe_source = get_field( 'page_sections', $source_id );
				if ( is_array( $maybe_source ) ) {
					foreach ( $maybe_source as $src_row ) {
						$src_layout = is_array( $src_row ) ? ( $src_row['acf_fc_layout'] ?? '' ) : '';
						if ( '' !== $src_layout ) {
							$source_by_layout[ $src_layout ][] = $src_row;
						}
					}
				}
			}
		}
		$layout_seen = array();

		foreach ( $sections as $section ) {
			$this_layout = is_array( $section ) ? ( $section['acf_fc_layout'] ?? '' ) : '';
			$ordinal     = isset( $layout_seen[ $this_layout ] ) ? $layout_seen[ $this_layout ] : 0;
			if ( '' !== $this_layout ) {
				$layout_seen[ $this_layout ] = $ordinal + 1;
			}
			$source_section = ( '' !== $this_layout && isset( $source_by_layout[ $this_layout ][ $ordinal ] ) )
				? $source_by_layout[ $this_layout ][ $ordinal ]
				: null;
			$section = estecapelli_prepare_page_section_for_render( $section, $post_id, $source_section );
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
