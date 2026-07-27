<?php
/**
 * One-time repair — refill the empty FAQ on the translated Gynecomastia pages.
 *
 * Five translated Gynecomastia treatments (fr, it, es, pl, pt) render an empty
 * FAQ because their faq repeater rows were never written by the import. This
 * tool injects the seven translated question/answer rows from each language's
 * own JSON directly into the faq repeater — the value meta, its ACF field-key
 * reference meta and the row count — at the faq layout's REAL index in the
 * post's section map. It:
 *
 *   - only fills a faq that is currently empty (safe to run more than once);
 *   - touches nothing else — no gallery, no other section, no layout map;
 *   - writes exactly the ACF nested-repeater meta shape, using the confirmed
 *     field keys (field_faq_items / field_faq_q / field_faq_a).
 *
 * It is manual, admin-only and nonce-guarded — like Safe Content Updates it
 * never runs on its own.
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

/** Count faq rows that already carry a non-empty question. */
function estecapelli_gyno_faq_current_count( $target_id, $faq_index ) {
	$count = 0;
	for ( $r = 0; $r < 20; $r++ ) {
		$q = (string) get_post_meta( $target_id, "page_sections_{$faq_index}_items_{$r}_question", true );
		if ( '' !== trim( $q ) ) {
			$count++;
		}
	}
	return $count;
}

/**
 * Fill the empty FAQ for one language. Returns a human-readable status.
 *
 * Only writes when the faq is currently empty, and only the faq repeater meta.
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
	$current = estecapelli_gyno_faq_current_count( $target_id, $faq_index );
	if ( $current > 0 ) {
		return sprintf( 'already has %d items — skipped', $current );
	}
	$items = estecapelli_gyno_faq_json_items( $lang );
	if ( ! is_array( $items ) || 7 !== count( $items ) ) {
		return 'JSON faq is missing or not 7 items — skipped';
	}

	$base = "page_sections_{$faq_index}_items";
	update_post_meta( $target_id, $base, count( $items ) );
	update_post_meta( $target_id, "_{$base}", 'field_faq_items' );
	foreach ( $items as $r => $item ) {
		update_post_meta( $target_id, "{$base}_{$r}_question", $item['question'] );
		update_post_meta( $target_id, "_{$base}_{$r}_question", 'field_faq_q' );
		update_post_meta( $target_id, "{$base}_{$r}_answer", $item['answer'] );
		update_post_meta( $target_id, "_{$base}_{$r}_answer", 'field_faq_a' );
	}
	clean_post_cache( $target_id );

	$after = estecapelli_gyno_faq_current_count( $target_id, $faq_index );
	return sprintf( 'filled — now %d items', $after );
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

/** Render the repair page: a preview table plus a manual fill button. */
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
	echo '<p>' . esc_html__( 'Fills the FAQ questions/answers on the translated Gynecomastia pages only when their FAQ is empty. It writes just the FAQ rows — nothing else is touched — and is safe to run more than once.', 'estecapelli' ) . '</p>';

	echo '<table class="widefat striped" style="max-width:900px;"><thead><tr>';
	foreach ( array( 'Language', 'Post ID', 'FAQ index', 'Current items', 'JSON items', 'Result' ) as $th ) {
		echo '<th>' . esc_html( $th ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	foreach ( estecapelli_gyno_faq_languages() as $lang ) {
		$tid   = estecapelli_gyno_faq_target_id( $lang );
		$fi    = $tid ? estecapelli_gyno_faq_layout_index( $tid ) : null;
		$cur   = ( $tid && null !== $fi ) ? estecapelli_gyno_faq_current_count( $tid, $fi ) : null;
		$json  = estecapelli_gyno_faq_json_items( $lang );
		$jc    = is_array( $json ) ? count( $json ) : null;
		echo '<tr>';
		echo '<td>' . esc_html( strtoupper( $lang ) ) . '</td>';
		echo '<td>' . esc_html( $tid ? (string) $tid : '—' ) . '</td>';
		echo '<td>' . esc_html( null === $fi ? '—' : (string) $fi ) . '</td>';
		echo '<td>' . esc_html( null === $cur ? '—' : (string) $cur ) . '</td>';
		echo '<td>' . esc_html( null === $jc ? '—' : (string) $jc ) . '</td>';
		echo '<td>' . esc_html( $results[ $lang ] ?? '' ) . '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';

	echo '<form method="post" style="margin-top:1.5em;">';
	wp_nonce_field( 'estecapelli_gyno_faq_run' );
	echo '<input type="hidden" name="estecapelli_gyno_faq_run" value="1" />';
	submit_button( __( 'Fill missing FAQ items', 'estecapelli' ) );
	echo '</form>';
	echo '</div>';
}
