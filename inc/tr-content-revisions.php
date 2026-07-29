<?php
/**
 * Render-time guarantees for the reviewed Turkish hair-transplant content.
 *
 * The canonical Turkish JSON files retain the approved copy for any deliberate
 * future import. This narrowly scoped overlay also keeps that copy visible when
 * production still contains an older ACF record. It never writes to WordPress.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Whether the visitor-facing language is Turkish. */
function estecapelli_tr_revision_is_turkish() {
	$language = (string) apply_filters( 'wpml_current_language', null );
	if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
		$language = estecapelli_indexed_language_code( $language );
	}

	return 'tr' === strtolower( $language );
}

/**
 * Exact routes and fields approved in the Turkish revision document.
 *
 * A row key is "layout:zero-based ordinal". Only listed fields are overlaid;
 * media and every unreviewed field continue to come from WordPress.
 *
 * @return array<string,array{file:string,rows:array}>
 */
function estecapelli_tr_revision_manifest() {
	return array(
		'/en/about-us' => array(
			'file' => 'pages/about-us.json',
			'rows' => array(
				'intro:0' => array( 'fields' => array( 'body' ) ),
				'intro:1' => array( 'fields' => array( 'title' ) ),
				'intro:2' => array( 'fields' => array( 'body' ) ),
			),
		),
		'/en/about-us/our-team' => array(
			'file' => 'pages/our-team.json',
			'rows' => array(
				'hero:0' => array( 'fields' => array( 'title', 'lead' ) ),
			),
		),
		'/en/dental-treatment/hollywood-smile' => array(
			'file' => 'dental-treatment/hollywood-smile.json',
			'rows' => array(
				'stepbook:0' => array(
					'items' => array(
						4 => array( 'body' ),
					),
				),
				'faq:0' => array(
					'items' => array(
						0 => array( 'question', 'answer' ),
						1 => array( 'answer' ),
						5 => array( 'answer' ),
					),
				),
			),
		),
		'/en/hair-transplant/sapphire-fue-hair-transplant' => array(
			'file' => 'hair-transplant/sapphire-fue-hair-transplant.json',
			'rows' => array(
				'intro:0'    => array( 'fields' => array( 'body' ) ),
				'stepbook:0' => array(
					'items' => array(
						0 => array( 'body' ),
						1 => array( 'title', 'body' ),
						2 => array( 'body' ),
						3 => array( 'body' ),
						4 => array( 'title', 'body' ),
					),
				),
			),
		),
		'/en/hair-transplant/dhi-hair-transplant' => array(
			'file' => 'hair-transplant/dhi-hair-transplant.json',
			'rows' => array(
				'hero:0'     => array( 'fields' => array( 'title', 'lead' ) ),
				'intro:0'    => array( 'fields' => array( 'body' ) ),
				'stepbook:0' => array( 'items' => array( 3 => array( 'body' ) ) ),
			),
		),
		'/en/hair-transplant/exosome-fue-hair-transplant' => array(
			'file' => 'hair-transplant/exosome-fue-hair-transplant.json',
			'rows' => array(
				'intro:0'    => array( 'fields' => array( 'title', 'body' ) ),
				'stepbook:0' => array(
					'items' => array(
						0 => array( 'body' ),
						1 => array( 'title', 'body' ),
						3 => array( 'title', 'body' ),
						4 => array( 'body' ),
					),
				),
			),
		),
		'/en/hair-transplant/vita-treatment' => array(
			'file' => 'hair-transplant/vita-treatment.json',
			'rows' => array(
				'hero:0'  => array( 'fields' => array( 'title', 'lead' ) ),
				'intro:0' => array( 'fields' => array( 'body' ) ),
			),
		),
		'/en/hair-transplant/female-hair-transplant' => array(
			'file' => 'hair-transplant/female-hair-transplant.json',
			'rows' => array(
				'hero:0'     => array( 'fields' => array( 'title', 'lead' ) ),
				'stepbook:0' => array(
					'items' => array(
						0 => array( 'title', 'body' ),
						1 => array( 'title', 'body' ),
						3 => array( 'title', 'body' ),
					),
				),
			),
		),
		'/en/hair-transplant/hair-mesotherapy' => array(
			'file' => 'hair-transplant/hair-mesotherapy.json',
			'rows' => array( 'hero:0' => array( 'fields' => array( 'lead' ) ) ),
		),
		'/en/hair-transplant/beard-transplant' => array(
			'file' => 'hair-transplant/beard-transplant.json',
			'rows' => array(
				'hero:0' => array( 'fields' => array( 'lead' ) ),
				'faq:0'  => array(
					'items' => array_fill( 0, 6, array( 'question', 'answer' ) ),
				),
			),
		),
		'/en/hair-transplant/eyebrow-transplant' => array(
			'file' => 'hair-transplant/eyebrow-transplant.json',
			'rows' => array(
				'hero:0'     => array( 'fields' => array( 'lead' ) ),
				'intro:1'    => array( 'fields' => array( 'body' ) ),
				'stepbook:0' => array(
					'items' => array(
						2 => array( 'title', 'body' ),
						3 => array( 'body' ),
					),
				),
				'faq:0'      => array(
					'items' => array_fill( 0, 6, array( 'question', 'answer' ) ),
				),
			),
		),
		'/en/hair-transplant/pre-hair-transplant-period' => array(
			'file' => 'pages/pre-hair-transplant-period.json',
			'rows' => array(
				'intro:0'    => array( 'fields' => array( 'body' ) ),
				'stepbook:0' => array(
					'items' => array(
						1 => array( 'title', 'body' ),
						2 => array( 'title', 'body' ),
						4 => array( 'body' ),
						5 => array( 'title', 'body' ),
					),
				),
				'faq:0'      => array(
					'items' => array(
						0 => array( 'question', 'answer' ),
						1 => array( 'question', 'answer' ),
						3 => array( 'question', 'answer' ),
					),
				),
			),
		),
		'/en/hair-transplant/post-hair-transplant-period' => array(
			'file' => 'pages/post-hair-transplant-period.json',
			'rows' => array(
				'intro:0'    => array( 'fields' => array( 'eyebrow', 'title', 'body' ) ),
				'stepbook:0' => array(
					'items' => array(
						0 => array( 'body' ),
						1 => array( 'body' ),
						2 => array( 'body' ),
						3 => array( 'title', 'body' ),
						4 => array( 'title', 'body' ),
						5 => array( 'title', 'body' ),
						6 => array( 'title', 'body' ),
						7 => array( 'eyebrow', 'body' ),
						8 => array( 'eyebrow', 'title', 'body' ),
					),
				),
			),
		),
	);
}

/** Load one reviewed Turkish translation JSON file. */
function estecapelli_tr_revision_load_sections( $relative_file ) {
	static $cache = array();

	$relative_file = ltrim( (string) $relative_file, '/\\' );
	if ( isset( $cache[ $relative_file ] ) ) {
		return $cache[ $relative_file ];
	}

	$file = get_template_directory() . '/inc/data/translations/tr/' . $relative_file;
	if ( ! is_readable( $file ) ) {
		$cache[ $relative_file ] = array();
		return $cache[ $relative_file ];
	}

	$data = json_decode( (string) file_get_contents( $file ), true );
	$cache[ $relative_file ] = is_array( $data ) && isset( $data['sections'] ) && is_array( $data['sections'] )
		? $data['sections']
		: array();

	return $cache[ $relative_file ];
}

/** Find the Nth canonical row for a flexible-content layout. */
function estecapelli_tr_revision_canonical_row( array $sections, $layout, $ordinal ) {
	$seen = 0;
	foreach ( $sections as $section ) {
		if ( ! is_array( $section ) || $layout !== (string) ( $section['acf_fc_layout'] ?? '' ) ) {
			continue;
		}
		if ( $seen === (int) $ordinal ) {
			return $section;
		}
		++$seen;
	}

	return array();
}

/** Overlay only fields explicitly named by one manifest row rule. */
function estecapelli_tr_revision_apply_rule( array $section, array $canonical, array $rule ) {
	foreach ( (array) ( $rule['fields'] ?? array() ) as $field ) {
		if ( array_key_exists( $field, $canonical ) ) {
			$section[ $field ] = $canonical[ $field ];
		}
	}

	if ( empty( $rule['items'] ) || ! is_array( $rule['items'] ) || empty( $section['items'] ) || ! is_array( $section['items'] ) ) {
		return $section;
	}

	foreach ( $rule['items'] as $index => $fields ) {
		if ( ! isset( $section['items'][ $index ] ) || ! is_array( $section['items'][ $index ] ) || ! isset( $canonical['items'][ $index ] ) || ! is_array( $canonical['items'][ $index ] ) ) {
			continue;
		}
		foreach ( (array) $fields as $field ) {
			if ( array_key_exists( $field, $canonical['items'][ $index ] ) ) {
				$section['items'][ $index ][ $field ] = $canonical['items'][ $index ][ $field ];
			}
		}
	}

	return $section;
}

/** Correct the reviewed Lamine spelling in any Turkish builder text. */
function estecapelli_tr_revision_lamine_spelling( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = estecapelli_tr_revision_lamine_spelling( $item );
		}
		return $value;
	}

	if ( is_string( $value ) ) {
		return str_replace(
			array( 'Laminate', 'laminate', 'Lamina', 'lamina' ),
			array( 'Lamine', 'lamine', 'Lamine', 'lamine' ),
			$value
		);
	}

	return $value;
}

/** Apply an approved Turkish revision to one rendered ACF section. */
function estecapelli_tr_revision_prepare_section( array $section, $post_id, $layout_ordinal = 0 ) {
	if ( ! estecapelli_tr_revision_is_turkish() ) {
		return $section;
	}

	$section = estecapelli_tr_revision_lamine_spelling( $section );
	if ( ! function_exists( 'estecapelli_indexed_post_route_key' ) ) {
		return $section;
	}

	$route    = estecapelli_indexed_post_route_key( $post_id );
	$manifest = estecapelli_tr_revision_manifest();
	if ( ! isset( $manifest[ $route ] ) ) {
		return $section;
	}

	$layout = (string) ( $section['acf_fc_layout'] ?? '' );
	$key    = $layout . ':' . max( 0, (int) $layout_ordinal );
	$entry  = $manifest[ $route ];
	if ( '' === $layout || empty( $entry['rows'][ $key ] ) ) {
		return $section;
	}

	$sections  = estecapelli_tr_revision_load_sections( $entry['file'] );
	$canonical = estecapelli_tr_revision_canonical_row( $sections, $layout, $layout_ordinal );
	if ( empty( $canonical ) ) {
		return $section;
	}

	return estecapelli_tr_revision_apply_rule( $section, $canonical, $entry['rows'][ $key ] );
}
