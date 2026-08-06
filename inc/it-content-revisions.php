<?php
/**
 * Render-time guarantees for the reviewed Italian page copy.
 *
 * The canonical Italian JSON files carry the approved wording, but the
 * importers that would write it into WordPress are locked on production. These
 * corrections are phrase-level, so rather than swapping whole builder rows this
 * replaces the exact reviewed phrases on their own page, on Italian requests
 * only. Nothing is written to the database.
 *
 * The replacement is self-limiting: once an import does land the old phrase is
 * gone, no needle matches, and every row passes through untouched.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Approved Italian phrase corrections, keyed by English source route.
 *
 * Each entry maps the wording currently stored in WordPress to the reviewed
 * wording. Keys are deliberately long enough to be unique on their page.
 *
 * @return array<string,array<string,string>>
 */
function estecapelli_it_revision_manifest() {
	return array(
		'/en/about-us'         => array(
			// The technique is exclusive to Estecapelli, but claiming a patent
			// for it is not accurate.
			"La tecnica Exosome FUE è un'applicazione brevettata di Estecapelli." => 'La tecnica Exosome FUE è una tecnica esclusiva sviluppata da Estecapelli.',
			'eseguiti ogni anno nella sola Turchia' => 'eseguiti ogni anno solo in Turchia',
		),
		'/en/plastic-surgery'  => array(
			'conformi agli standard di una clinica di eccellenza' => 'conformi ai più elevati standard clinici',
		),
		'/en/dental-treatment' => array(
			'Una masticazione sana e un sorriso che ami' => 'Una masticazione sana e un sorriso da amare',
		),
	);
}

/** Whether the visitor-facing language is Italian. */
function estecapelli_it_revision_is_italian() {
	if ( function_exists( 'estecapelli_is_italian_request' ) ) {
		return estecapelli_is_italian_request();
	}

	$language = (string) apply_filters( 'wpml_current_language', null );
	if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
		$language = estecapelli_indexed_language_code( $language );
	}

	return 'it' === strtolower( $language );
}

/**
 * Expand one needle into the apostrophe forms the stored copy may use.
 *
 * The JSON source holds straight apostrophes, but a value that has been through
 * the editor or a texturising filter can hold the curly form instead. Matching
 * both keeps a correction from silently missing its target.
 *
 * @param string $needle Reviewed phrase as written in the JSON source.
 * @return string[]
 */
function estecapelli_it_revision_needle_variants( $needle ) {
	$variants = array( $needle );
	$curly    = str_replace( "'", '’', $needle );

	if ( $curly !== $needle ) {
		$variants[] = $curly;
	}

	return $variants;
}

/**
 * Apply one page's corrections to every string in a builder row.
 *
 * @param mixed                 $value        Field value, at any nesting depth.
 * @param array<string,string>  $replacements Reviewed phrases for this page.
 * @return mixed
 */
function estecapelli_it_revision_replace( $value, array $replacements ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = estecapelli_it_revision_replace( $item, $replacements );
		}

		return $value;
	}

	if ( ! is_string( $value ) || '' === $value ) {
		return $value;
	}

	foreach ( $replacements as $before => $after ) {
		$value = str_replace( estecapelli_it_revision_needle_variants( $before ), $after, $value );
	}

	return $value;
}

/**
 * Apply the approved Italian revision to one rendered ACF section.
 *
 * @param array $section ACF flexible-content row.
 * @param int   $post_id Page being rendered.
 * @return array
 */
function estecapelli_it_revision_prepare_section( array $section, $post_id ) {
	if ( ! estecapelli_it_revision_is_italian() || ! function_exists( 'estecapelli_indexed_post_route_key' ) ) {
		return $section;
	}

	$manifest = estecapelli_it_revision_manifest();
	$route    = estecapelli_indexed_post_route_key( $post_id );
	if ( '' === $route || empty( $manifest[ $route ] ) ) {
		return $section;
	}

	return estecapelli_it_revision_replace( $section, $manifest[ $route ] );
}
