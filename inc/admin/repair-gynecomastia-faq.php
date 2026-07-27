<?php
/**
 * One-time repair — refill the empty FAQ on the translated Gynecomastia pages.
 *
 * Five translated Gynecomastia treatments (fr, it, es, pl, pt) render an empty
 * FAQ because their faq repeater rows are missing (or a previous raw-meta patch
 * left broken rows that ACF cannot assemble). This tool injects the seven
 * translated question/answer rows from each language's own JSON into the faq
 * repeater at the faq layout's real index in the section map.
 *
 * The decision to fill is based on what ACF actually ASSEMBLES (get_field), not
 * on the raw meta, so a page that shows no FAQ is repaired even if broken raw
 * rows are left behind. It only touches the faq repeater — no gallery, no other
 * section, no layout map — and is safe to run more than once.
 *
 * Manual, admin-only and nonce-guarded — it never runs on its own.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Languages whose Gynecomastia FAQ may need refilling. */
function estecapelli_gyno_faq_languages() {
	return array( 'fr', 'it', 'es', 'pl', 'pt' );
}

/** The seven translated Q&A rows from a language's Gynecomastia JSON. */
function estecapelli_gyno_faq_json_items( $lang ) {
	$file = get_template_directory() . '/inc/data/translations/' . $lang . '/plastic-surgery/gynecomastia.json';
	if ( ! is_readable( $file ) ) {
		return null;
	}
	$data = json_decode( (string) file_get_contents( $file ), true );
	if ( ! is_array( $data ) ) {
		return null;
	}
	foreach ( (array) ( $data['sections'] ?? array() ) as $section ) {
		if ( 'faq' !== ( $section['acf_fc_layout'] ?? '' ) ) {
			continue;
		}
		$items = array();
		foreach ( (array) ( $section['items'] ?? array() ) as $item ) {
			if ( isset( $item['question'], $item['answer'] ) && '' !== trim( (string) $item['question'] ) ) {
				$items[] = array(
					'question' => (string) $item['question'],
					'answer'   => (string) $item['answer'],
				);
			}
		}
		return $items;
	}
	return null;
}

/** Resolve the translated Gynecomastia treatment post ID for a language. */
function estecapelli_gyno_faq_target_id( $lang ) {
	$source_id = function_exists( 'estecapelli_source_post_id' )
		? (int) estecapelli_source_post_id( 'gynecomastia', 'treatment' )
		: 0;
	if ( ! $source_id ) {
		return 0;
	}
	$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, $lang );
	return ( $target_id && $target_id !== $source_id ) ? $target_id : 0;
}

/** Index of the `faq` layout inside a post's section map, or null. */
function estecapelli_gyno_faq_layout_index( $target_id ) {
	$map = get_post_meta( $target_id, 'page_sections', true );
	if ( ! is_array( $map ) ) {
		return null;
	}
	$i = array_search( 'faq', $map, true );
	return ( false === $i ) ? null : (int) $i;
}

/** Number of faq rows ACF actually assembles for a post (what the page sees). */
function estecapelli_gyno_faq_rendered_count( $target_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return -1;
	}
	$sections = get_field( 'page_sections', $target_id );
	if ( ! is_array( $sections ) ) {
		return -1;
	}
	foreach ( $sections as $row ) {
		if ( 'faq' === ( $row['acf_fc_layout'] ?? '' ) ) {
			$n = 0;
			foreach ( (array) ( $row['items'] ?? array() ) as $it ) {
				if ( is_array( $it ) && '' !== trim( (string) ( $it['question'] ?? '' ) ) ) {
					$n++;
				}
			}
			return $n;
		}
	}
	return 0;
}

/** Raw-meta count of faq questions actually stored at the faq index. */
function estecapelli_gyno_faq_raw_count( $target_id, $faq_index ) {
	if ( null === $faq_index ) {
		return -1;
	}
	$n = 0;
	for ( $r = 0; $r < 20; $r++ ) {
		$q = (string) get_post_meta( $target_id, "page_sections_{$faq_index}_items_{$r}_question", true );
		if ( '' !== trim( $q ) ) {
			$n++;
		}
	}
	return $n;
}

/** Drop ACF's in-request value cache so a fresh get_field re-reads the DB. */
function estecapelli_gyno_faq_flush_cache( $target_id ) {
	clean_post_cache( $target_id );
	wp_cache_delete( $target_id, 'post_meta' );
	if ( function_exists( 'acf_flush_value_cache' ) ) {
		acf_flush_value_cache();
	}
}

/**
 * Fill / repair the FAQ for one language. Returns a human-readable status.
 *
 * Fills only when ACF currently assembles zero faq items (the page shows no
 * FAQ). Writes only the faq repeater meta at the faq layout index, overwriting
 * any broken leftover rows with the exact ACF shape and field-key references.
 */
function estecapelli_gyno_faq_fill_one( $lang ) {
	$target_id = estecapelli_gyno_faq_target_id( $lang );
	if ( ! $target_id ) {
		return 'no linked translation';
	}
	$faq_index = estecapelli_gyno_faq_layout_index( $target_id );
	if ( null === $faq_index ) {
		return 'no faq layout in section map';
	}
	$rendered = estecapelli_gyno_faq_rendered_count( $target_id );
	if ( $rendered > 0 ) {
		return sprintf( 'already renders %d items — skipped', $rendered );
	}
	$items = estecapelli_gyno_faq_json_items( $lang );
	if ( ! is_array( $items ) || 7 !== count( $items ) ) {
		return 'JSON faq is missing or not 7 items — skipped';
	}

	$base = "page_sections_{$faq_index}_items";

	// Clear any stale rows beyond the seven we write, so a broken longer row set
	// left by an earlier attempt cannot linger.
	for ( $r = 0; $r < 20; $r++ ) {
		delete_post_meta( $target_id, "{$base}_{$r}_question" );
		delete_post_meta( $target_id, "_{$base}_{$r}_question" );
		delete_post_meta( $target_id, "{$base}_{$r}_answer" );
		delete_post_meta( $target_id, "_{$base}_{$r}_answer" );
	}

	update_post_meta( $target_id, $base, count( $items ) );
	update_post_meta( $target_id, "_{$base}", 'field_faq_items' );
	foreach ( $items as $r => $item ) {
		update_post_meta( $target_id, "{$base}_{$r}_question", $item['question'] );
		update_post_meta( $target_id, "_{$base}_{$r}_question", 'field_faq_q' );
		update_post_meta( $target_id, "{$base}_{$r}_answer", $item['answer'] );
		update_post_meta( $target_id, "_{$base}_{$r}_answer", 'field_faq_a' );
	}

	estecapelli_gyno_faq_flush_cache( $target_id );
	$after = estecapelli_gyno_faq_rendered_count( $target_id );
	return sprintf( 'wrote 7 rows — ACF now assembles %d items', $after );
}

/** Register the manual repair page under Tools. */
add_action( 'admin_menu', 'estecapelli_gyno_faq_register_page' );
function estecapelli_gyno_faq_register_page() {
	add_management_page(
		__( 'Repair Gynecomastia FAQ', 'estecapelli' ),
		__( 'Repair Gynecomastia FAQ', 'estecapelli' ),
		'manage_options',
		'estecapelli-gyno-faq-repair',
		'estecapelli_gyno_faq_render_page'
	);
}

/** Render the repair page: diagnostics table plus a manual fill button. */
function estecapelli_gyno_faq_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$results = array();
	if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['estecapelli_gyno_faq_run'] ) ) {
		check_admin_referer( 'estecapelli_gyno_faq_run' );
		foreach ( estecapelli_gyno_faq_languages() as $lang ) {
			$results[ $lang ] = estecapelli_gyno_faq_fill_one( $lang );
		}
	}

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Repair Gynecomastia FAQ', 'estecapelli' ) . '</h1>';
	echo '<p>' . esc_html__( 'Fills the FAQ on a translated Gynecomastia page only when ACF assembles zero questions there. Writes just the FAQ rows — nothing else — and is safe to run more than once.', 'estecapelli' ) . '</p>';

	echo '<table class="widefat striped" style="max-width:1000px;"><thead><tr>';
	foreach ( array( 'Language', 'Post ID', 'Section map', 'FAQ idx', 'ACF renders', 'Raw meta', 'JSON', 'Result' ) as $th ) {
		echo '<th>' . esc_html( $th ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	foreach ( estecapelli_gyno_faq_languages() as $lang ) {
		$tid  = estecapelli_gyno_faq_target_id( $lang );
		$map  = $tid ? get_post_meta( $tid, 'page_sections', true ) : null;
		$fi   = $tid ? estecapelli_gyno_faq_layout_index( $tid ) : null;
		$ren  = $tid ? estecapelli_gyno_faq_rendered_count( $tid ) : null;
		$raw  = ( $tid && null !== $fi ) ? estecapelli_gyno_faq_raw_count( $tid, $fi ) : null;
		$json = estecapelli_gyno_faq_json_items( $lang );
		$jc   = is_array( $json ) ? count( $json ) : null;
		$map_str = is_array( $map ) ? implode( ', ', array_map( 'strval', $map ) ) : '—';
		echo '<tr>';
		echo '<td>' . esc_html( strtoupper( $lang ) ) . '</td>';
		echo '<td>' . esc_html( $tid ? (string) $tid : '—' ) . '</td>';
		echo '<td style="font-size:11px;max-width:280px;">' . esc_html( $map_str ) . '</td>';
		echo '<td>' . esc_html( null === $fi ? '—' : (string) $fi ) . '</td>';
		echo '<td><strong>' . esc_html( null === $ren ? '—' : (string) $ren ) . '</strong></td>';
		echo '<td>' . esc_html( null === $raw ? '—' : (string) $raw ) . '</td>';
		echo '<td>' . esc_html( null === $jc ? '—' : (string) $jc ) . '</td>';
		echo '<td>' . esc_html( $results[ $lang ] ?? '' ) . '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
	echo '<p style="color:#666;font-size:12px;">' . esc_html__( '"ACF renders" is how many FAQ questions the live page actually shows. If it is 0 while JSON is 7, the row needs filling.', 'estecapelli' ) . '</p>';

	echo '<form method="post" style="margin-top:1.2em;">';
	wp_nonce_field( 'estecapelli_gyno_faq_run' );
	echo '<input type="hidden" name="estecapelli_gyno_faq_run" value="1" />';
	submit_button( __( 'Fill / repair missing FAQ items', 'estecapelli' ) );
	echo '</form>';
	echo '</div>';
}
