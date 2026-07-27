<?php
/**
 * Render-time guarantees for reviewed French content that lives in WordPress.
 *
 * The production content lock intentionally prevents importers from rewriting
 * existing posts during a deploy. These narrowly scoped fallbacks therefore
 * expose the approved French copy without mutating the database, and stop an
 * old ACF/blog record from undoing the reviewed public content.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Whether the current visitor-facing language is French. */
function estecapelli_fr_revision_is_french() {
	$language = (string) apply_filters( 'wpml_current_language', null );
	if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
		$language = estecapelli_indexed_language_code( $language );
	}

	return 'fr' === strtolower( $language );
}

/**
 * Apply the three approved Gynecomastia corrections to one rendered ACF row.
 *
 * Matching by layout and French eyebrow keeps this independent from row-index
 * drift while limiting the override to the exact sections under review.
 *
 * @param array $section ACF flexible-content row.
 * @return array
 */
function estecapelli_fr_revision_gynecomastia_section( array $section ) {
	$layout  = (string) ( $section['acf_fc_layout'] ?? '' );
	$eyebrow = trim( wp_strip_all_tags( (string) ( $section['eyebrow'] ?? '' ) ) );

	if ( 'intro' === $layout && 'La pathologie' === $eyebrow ) {
		$section['body'] = '<p>La gynécomastie désigne l’augmentation du volume de la poitrine chez l’homme, liée à un développement anormal de la glande mammaire. Ce phénomène s’explique généralement par un déséquilibre entre les hormones masculines et féminines (testostérone et œstrogènes), mais peut aussi être accentué par une prise de poids ou certains facteurs de santé.</p><p>Souvent source de complexes ou de gêne au quotidien, cette affection mérite une prise en charge experte et bienveillante. Pour les équipes d’Estecapelli, restaurer l’harmonie de votre torse est une priorité. Nous concevons un plan de traitement personnalisé afin de vous aider à retrouver une silhouette athlétique dans laquelle vous vous sentirez pleinement à l’aise.</p>';
	}

	if ( 'faq' === $layout ) {
		$section['title'] = 'Gynécomastie – FAQ';
	}

	if ( 'intro' === $layout && 'Tarifs' === $eyebrow ) {
		$section['title'] = 'Tarif de la chirurgie de la gynécomastie';
	}

	return $section;
}

/**
 * Apply reviewed French ACF copy only to the Gynecomastia treatment.
 *
 * @param array $section ACF flexible-content row.
 * @param int   $post_id Current post ID.
 * @return array
 */
function estecapelli_fr_revision_prepare_section( array $section, $post_id ) {
	if ( ! estecapelli_fr_revision_is_french() || ! function_exists( 'estecapelli_indexed_post_route_key' ) ) {
		return $section;
	}

	if ( '/en/plastic-surgery/gynecomastia' !== estecapelli_indexed_post_route_key( $post_id ) ) {
		return $section;
	}

	return estecapelli_fr_revision_gynecomastia_section( $section );
}

/**
 * Return the reviewed French unshaven-transplant article bundled with the theme.
 *
 * @return string
 */
function estecapelli_fr_revision_unshaven_article() {
	static $content = null;

	if ( null === $content ) {
		$file    = get_template_directory() . '/inc/data/blog/fr/unshaven-hair-transplant.html';
		$content = is_readable( $file ) ? (string) file_get_contents( $file ) : '';
	}

	return $content;
}

/**
 * Render the reviewed article even when its old imported DB post remains.
 *
 * @param string $content Current post content.
 * @return string
 */
function estecapelli_fr_revision_blog_content( $content ) {
	if ( is_admin() || ! is_singular( 'post' ) || ! estecapelli_fr_revision_is_french() ) {
		return $content;
	}

	$post_id = get_the_ID();
	$route   = function_exists( 'estecapelli_indexed_post_route_key' )
		? estecapelli_indexed_post_route_key( $post_id )
		: '';
	$slug    = (string) get_post_field( 'post_name', $post_id );

	if ( '/en/blog/unshaven-hair-transplant' !== $route && 'greffe-de-cheveux-sans-rasage' !== $slug ) {
		return $content;
	}

	$reviewed = estecapelli_fr_revision_unshaven_article();
	return '' !== $reviewed ? $reviewed : $content;
}
add_filter( 'the_content', 'estecapelli_fr_revision_blog_content', 8 );
