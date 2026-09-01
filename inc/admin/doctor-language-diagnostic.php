<?php
/**
 * Doctor language diagnostic — read-only report under Tools → Doctor Languages.
 *
 * Built for one concrete bug: on /tr/ and /ro/ the roster grid on the doctors
 * landing page prints the FRENCH position under every doctor, while the same
 * doctor's own profile page prints the correct Turkish one. Same post type,
 * same request language, two different answers — so the report shows, side by
 * side, the three things that could explain it:
 *
 *   1. every `doctor` row in the database and the language WPML has it under,
 *   2. what the raw `position` meta holds versus what get_field() returns
 *      (ACFML filters the second, so a mismatch localises the fault), and
 *   3. exactly which posts the roster query returns in each language, run the
 *      way template-parts/sections/doctors.php runs it.
 *
 * It changes NOTHING. Every query is a SELECT, and the language switching in
 * section 2 is per-request state that WPML drops when the request ends; the
 * report restores it anyway.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The English positions come from the seed, which is otherwise only pulled in
// by the importers — and those load behind the content-mutation switch this
// read-only report deliberately sits in front of.
require_once get_template_directory() . '/inc/data/doctors-seed.php';

/** Languages the report walks, in column order. */
function estecapelli_doctor_diag_languages() {
	return array( 'en', 'fr', 'it', 'es', 'pt', 'pl', 'tr', 'ro' );
}

/**
 * Every `doctor` row in the database, unfiltered by language or query vars.
 *
 * @return array<int,object>
 */
function estecapelli_doctor_diag_all_posts() {
	global $wpdb;
	return (array) $wpdb->get_results(
		"SELECT ID, post_title, post_name, post_status, menu_order
		 FROM {$wpdb->posts}
		 WHERE post_type = 'doctor' AND post_status <> 'auto-draft'
		 ORDER BY menu_order ASC, ID ASC"
	);
}

/** WPML trid / language / source language for one doctor post, via the filter. */
function estecapelli_doctor_diag_details( $post_id ) {
	$details = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => (int) $post_id,
			'element_type' => 'doctor',
		)
	);
	$read = static function ( $key ) use ( $details ) {
		if ( is_object( $details ) ) {
			return $details->$key ?? '';
		}
		if ( is_array( $details ) ) {
			return $details[ $key ] ?? '';
		}
		return '';
	};

	return array(
		'trid'            => (int) $read( 'trid' ),
		'language_code'   => (string) $read( 'language_code' ),
		'source_language' => (string) $read( 'source_language_code' ),
	);
}

/**
 * The raw icl_translations rows for a doctor post, read straight from the table.
 *
 * WPML's own filter can answer for a post that has no row at all, so the table
 * is asked directly — an orphan (no row) is one of the likelier causes here and
 * has to be distinguishable from a row that genuinely says 'fr'.
 *
 * @return array{language:string,trid:int,source:string,rows:int}
 */
function estecapelli_doctor_diag_raw_row( $post_id ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';
	$rows  = (array) $wpdb->get_results(
		$wpdb->prepare(
			"SELECT trid, language_code, source_language_code
			 FROM {$table}
			 WHERE element_id = %d AND element_type = 'post_doctor'",
			(int) $post_id
		)
	);
	if ( ! $rows ) {
		return array(
			'language' => '',
			'trid'     => 0,
			'source'   => '',
			'rows'     => 0,
		);
	}

	$first = $rows[0];
	return array(
		'language' => (string) $first->language_code,
		'trid'     => (int) $first->trid,
		'source'   => (string) $first->source_language_code,
		'rows'     => count( $rows ),
	);
}

/**
 * Every `position` string the theme knows, mapped to the language it belongs to.
 *
 * Lets the report name the language of a value it finds rather than leaving it
 * to the reader's eye — which is how French copy first got reported as Spanish.
 *
 * @return array<string,string> position string => language code
 */
function estecapelli_doctor_diag_known_positions() {
	static $map = null;
	if ( null !== $map ) {
		return $map;
	}

	$map = array();
	foreach ( estecapelli_doctor_diag_expected_positions() as $by_language ) {
		foreach ( $by_language as $language => $position ) {
			if ( '' !== $position ) {
				$map[ $position ] = $language;
			}
		}
	}

	return $map;
}

/**
 * The position each doctor should carry in each language.
 *
 * English comes from the seed, everything else from the JSON overlays, so the
 * report compares live data against the same source the importers write from.
 *
 * @return array<string,array<string,string>> doctor slug => language => position
 */
function estecapelli_doctor_diag_expected_positions() {
	static $expected = null;
	if ( null !== $expected ) {
		return $expected;
	}

	$expected = array();
	if ( function_exists( 'estecapelli_doctors_seed' ) ) {
		foreach ( estecapelli_doctors_seed() as $doctor ) {
			if ( ! empty( $doctor['slug'] ) ) {
				$expected[ (string) $doctor['slug'] ]['en'] = (string) ( $doctor['position'] ?? '' );
			}
		}
	}

	foreach ( estecapelli_doctor_diag_languages() as $language ) {
		if ( 'en' === $language ) {
			continue;
		}
		$directory = get_template_directory() . '/inc/data/translations/' . $language . '/doctors';
		foreach ( (array) glob( $directory . '/*.json' ) as $file ) {
			$data = json_decode( (string) file_get_contents( $file ), true );
			if ( ! is_array( $data ) ) {
				continue;
			}
			$slug = (string) ( $data['source_slug'] ?? basename( $file, '.json' ) );
			$expected[ $slug ][ $language ] = (string) ( $data['position'] ?? '' );
		}
	}

	return $expected;
}

/** Name the language of one position value, '?' when unknown, '—' when empty. */
function estecapelli_doctor_diag_position_language( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '—';
	}
	$known = estecapelli_doctor_diag_known_positions();
	return $known[ $value ] ?? '?';
}

/**
 * Run the roster query exactly as the doctors section runs it, in one language.
 *
 * @param string $language Language code to switch WPML to.
 * @return array<int,array<string,mixed>>
 */
function estecapelli_doctor_diag_roster_for( $language ) {
	$previous = (string) apply_filters( 'wpml_current_language', null );

	// WPML's own code, not the indexed one: Portuguese is 'pt-pt' inside WPML
	// while the site publishes it at /pt/. Asking WPML to switch to 'pt' names a
	// language it does not have, and it answers with another language's posts —
	// which is why the pt roster below used to come back holding Turkish rows.
	do_action( 'wpml_switch_language', estecapelli_wpml_language_code( $language ) );

	// Identical arguments to template-parts/sections/doctors.php.
	$posts = get_posts(
		array(
			'post_type'        => 'doctor',
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'orderby'          => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'suppress_filters' => false,
		)
	);

	$out = array();
	foreach ( $posts as $doc ) {
		$raw   = estecapelli_doctor_diag_raw_row( $doc->ID );
		$out[] = array(
			'id'        => (int) $doc->ID,
			'title'     => get_the_title( $doc ),
			'row_lang'  => $raw['language'],
			'meta'      => (string) get_post_meta( $doc->ID, 'position', true ),
			'field'     => function_exists( 'get_field' ) ? (string) get_field( 'position', $doc->ID ) : '',
			'permalink' => (string) get_permalink( $doc ),
		);
	}

	do_action( 'wpml_switch_language', $previous ? $previous : 'en' );
	return $out;
}

add_action( 'admin_menu', 'estecapelli_register_doctor_language_diagnostic' );
/** Put the report under Tools, beside the other diagnostics. */
function estecapelli_register_doctor_language_diagnostic() {
	add_management_page(
		__( 'Estecapelli — Doctor Language Diagnostic', 'estecapelli' ),
		__( 'Doctor Languages', 'estecapelli' ),
		'manage_options',
		'estecapelli-doctor-language-diagnostic',
		'estecapelli_render_doctor_language_diagnostic'
	);
}

/** Render the report. */
function estecapelli_render_doctor_language_diagnostic() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$languages = estecapelli_doctor_diag_languages();

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Doctor Language Diagnostic', 'estecapelli' ) . '</h1>';
	echo '<p class="description">' . esc_html__( 'Read-only. Nothing on this page writes to the database — it only reports what is already there.', 'estecapelli' ) . '</p>';

	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'WPML is not active — nothing to diagnose.', 'estecapelli' ) . '</p></div></div>';
		return;
	}

	estecapelli_doctor_diag_render_copy_block( $languages );
	estecapelli_doctor_diag_render_all_posts();
	estecapelli_doctor_diag_render_rosters( $languages );
	estecapelli_doctor_diag_render_expected( $languages );

	echo '</div>';
}

/**
 * The whole report again as one block of plain text, ready to copy out.
 *
 * The tables below are wide enough that screenshotting them loses columns, and
 * the person reading this report is usually relaying it to someone who cannot
 * reach wp-admin. One tab-separated block travels intact through any chat box.
 *
 * @param array $languages Language codes, in column order.
 * @return void
 */
function estecapelli_doctor_diag_render_copy_block( array $languages ) {
	$lines   = array();
	$lines[] = '# doctor language diagnostic — ' . home_url( '/' );
	$lines[] = '# generated ' . gmdate( 'Y-m-d H:i' ) . ' UTC';
	$lines[] = '';
	$lines[] = '## posts (id | slug | status | wpml_row | trid | source | meta[lang] | get_field[lang])';

	foreach ( estecapelli_doctor_diag_all_posts() as $post ) {
		$raw   = estecapelli_doctor_diag_raw_row( $post->ID );
		$meta  = (string) get_post_meta( $post->ID, 'position', true );
		$field = function_exists( 'get_field' ) ? (string) get_field( 'position', $post->ID ) : '';

		$lines[] = implode(
			"\t",
			array(
				(int) $post->ID,
				$post->post_name,
				$post->post_status,
				$raw['rows'] ? $raw['language'] : 'NO-ROW',
				$raw['rows'] > 1 ? $raw['trid'] . ' (x' . $raw['rows'] . ' rows)' : (string) $raw['trid'],
				$raw['source'] ? $raw['source'] : '-',
				$meta . ' [' . estecapelli_doctor_diag_position_language( $meta ) . ']',
				$field . ' [' . estecapelli_doctor_diag_position_language( $field ) . ']',
			)
		);
	}

	foreach ( $languages as $language ) {
		$roster  = estecapelli_doctor_diag_roster_for( $language );
		$lines[] = '';
		$lines[] = '## roster ' . $language . ' — ' . count( $roster ) . ' returned';
		if ( ! $roster ) {
			$lines[] = '(empty — section falls back to the page JSON members repeater)';
			continue;
		}
		foreach ( $roster as $row ) {
			$lines[] = implode(
				"\t",
				array(
					(int) $row['id'],
					$row['row_lang'] ? $row['row_lang'] : 'NO-ROW',
					$row['field'] . ' [' . estecapelli_doctor_diag_position_language( $row['field'] ) . ']',
					$row['permalink'],
				)
			);
		}
	}

	echo '<h2>' . esc_html__( 'Copy this and send it over', 'estecapelli' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Everything below is in the tables further down — this box is just the same thing as plain text, so it can be selected and pasted in one go. Click inside it and press Ctrl+A then Ctrl+C.', 'estecapelli' ) . '</p>';
	echo '<textarea readonly onclick="this.select();" rows="14" style="width:100%;font-family:monospace;font-size:11px;white-space:pre;">';
	echo esc_textarea( implode( "\n", $lines ) );
	echo '</textarea>';
}

/** Section 1 — every doctor post, with its WPML row and its position values. */
function estecapelli_doctor_diag_render_all_posts() {
	$posts = estecapelli_doctor_diag_all_posts();

	echo '<h2>' . esc_html__( '1. Every doctor post in the database', 'estecapelli' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Unfiltered by language. "WPML row" is read straight from wp_icl_translations: an empty cell means the post is an orphan WPML does not know about. "position (meta)" is the stored value and "position (get_field)" is what the theme actually prints — if those two differ, the fault is in ACFML filtering rather than in the data.', 'estecapelli' ) . '</p>';

	echo '<table class="widefat striped"><thead><tr>';
	foreach ( array( 'ID', 'Title', 'Slug', 'Status', 'WPML row', 'trid', 'Source', 'position (meta)', 'position (get_field)', 'Verdict' ) as $heading ) {
		echo '<th>' . esc_html( $heading ) . '</th>';
	}
	echo '</tr></thead><tbody>';

	if ( ! $posts ) {
		echo '<tr><td colspan="10">' . esc_html__( 'No doctor posts found at all.', 'estecapelli' ) . '</td></tr>';
	}

	foreach ( $posts as $post ) {
		$raw        = estecapelli_doctor_diag_raw_row( $post->ID );
		$details    = estecapelli_doctor_diag_details( $post->ID );
		$meta       = (string) get_post_meta( $post->ID, 'position', true );
		$field      = function_exists( 'get_field' ) ? (string) get_field( 'position', $post->ID ) : '';
		$meta_lang  = estecapelli_doctor_diag_position_language( $meta );
		$field_lang = estecapelli_doctor_diag_position_language( $field );

		if ( ! $raw['rows'] ) {
			$verdict = estecapelli_doctor_diag_bad( __( 'ORPHAN — no WPML row', 'estecapelli' ) );
		} elseif ( $raw['rows'] > 1 ) {
			$verdict = estecapelli_doctor_diag_bad( __( 'DUPLICATE WPML rows', 'estecapelli' ) );
		} elseif ( $meta !== $field ) {
			$verdict = estecapelli_doctor_diag_bad( __( 'get_field OVERRIDES the stored value', 'estecapelli' ) );
		} elseif ( '—' === $field_lang ) {
			$verdict = estecapelli_doctor_diag_warn( __( 'position is empty', 'estecapelli' ) );
		} elseif ( '?' === $field_lang ) {
			$verdict = estecapelli_doctor_diag_warn( __( 'position not on record in any language', 'estecapelli' ) );
		} elseif ( $field_lang !== estecapelli_indexed_language_code( $raw['language'] ) ) {
			$verdict = estecapelli_doctor_diag_bad(
				sprintf(
					/* translators: 1: language of the text found, 2: language the post is registered under */
					__( 'WRONG LANGUAGE — %1$s text on a %2$s post', 'estecapelli' ),
					$field_lang,
					$raw['language']
				)
			);
		} else {
			$verdict = '<span style="color:#1a7f37;font-weight:600;">' . esc_html__( 'OK', 'estecapelli' ) . '</span>';
		}

		echo '<tr>';
		echo '<td>' . (int) $post->ID . '</td>';
		echo '<td>' . esc_html( $post->post_title ) . '</td>';
		echo '<td><code>' . esc_html( $post->post_name ) . '</code></td>';
		echo '<td>' . esc_html( $post->post_status ) . '</td>';
		echo '<td><code>' . esc_html( $raw['language'] ? $raw['language'] : '—' ) . '</code>';
		if ( $raw['language'] !== $details['language_code'] ) {
			echo '<br /><small>' . esc_html__( 'filter says:', 'estecapelli' ) . ' <code>' . esc_html( $details['language_code'] ? $details['language_code'] : '—' ) . '</code></small>';
		}
		echo '</td>';
		echo '<td>' . (int) $raw['trid'] . '</td>';
		echo '<td><code>' . esc_html( $raw['source'] ? $raw['source'] : '—' ) . '</code></td>';
		echo '<td>' . esc_html( $meta ) . '<br /><small>[' . esc_html( $meta_lang ) . ']</small></td>';
		echo '<td>' . esc_html( $field ) . '<br /><small>[' . esc_html( $field_lang ) . ']</small></td>';
		echo '<td>' . $verdict . '</td>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts above.
		echo '</tr>';
	}

	echo '</tbody></table>';
}

/** Section 2 — the roster query, replayed once per language. */
function estecapelli_doctor_diag_render_rosters( array $languages ) {
	echo '<h2>' . esc_html__( '2. What the roster grid returns, language by language', 'estecapelli' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'The same get_posts() call template-parts/sections/doctors.php makes, run once per language. This is the table that names the bug: if tr comes back holding post IDs whose WPML row says fr, the roster query is at fault; if it comes back with the right tr IDs but a French position, the field is.', 'estecapelli' ) . '</p>';

	foreach ( $languages as $language ) {
		$roster = estecapelli_doctor_diag_roster_for( $language );

		echo '<h3><code>' . esc_html( $language ) . '</code> — ' . (int) count( $roster ) . ' ' . esc_html__( 'doctors returned', 'estecapelli' ) . '</h3>';
		echo '<table class="widefat striped" style="margin-bottom:18px;"><thead><tr>';
		foreach ( array( 'ID', 'Title', 'WPML row', 'position (meta)', 'position (get_field) — what the page prints', 'Permalink' ) as $heading ) {
			echo '<th>' . esc_html( $heading ) . '</th>';
		}
		echo '</tr></thead><tbody>';

		if ( ! $roster ) {
			echo '<tr><td colspan="6">' . esc_html__( 'Nothing returned — the section falls back to the manual members repeater in the page JSON.', 'estecapelli' ) . '</td></tr>';
		}

		foreach ( $roster as $row ) {
			$row_lang   = $row['row_lang'];
			$field_lang = estecapelli_doctor_diag_position_language( $row['field'] );
			// Compared as indexed codes so WPML's 'pt-pt' is not read as a
			// Portuguese post sitting in the wrong language.
			$wrong_post = $row_lang && estecapelli_indexed_language_code( $row_lang ) !== $language;
			$wrong_text = '?' !== $field_lang && '—' !== $field_lang && $field_lang !== $language;

			echo '<tr' . ( ( $wrong_post || $wrong_text ) ? ' style="background:#fcf0f1;"' : '' ) . '>';
			echo '<td>' . (int) $row['id'] . '</td>';
			echo '<td>' . esc_html( $row['title'] ) . '</td>';
			echo '<td><code>' . esc_html( $row_lang ? $row_lang : '—' ) . '</code></td>';
			echo '<td>' . esc_html( $row['meta'] ) . '</td>';
			echo '<td><strong>' . esc_html( $row['field'] ) . '</strong> <small>[' . esc_html( $field_lang ) . ']</small></td>';
			echo '<td><small>' . esc_html( $row['permalink'] ) . '</small></td>';
			echo '</tr>';
		}

		echo '</tbody></table>';
	}
}

/** Section 3 — what each position should be, from the seed and the overlays. */
function estecapelli_doctor_diag_render_expected( array $languages ) {
	echo '<h2>' . esc_html__( '3. What the position should be', 'estecapelli' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Straight from the seed and the JSON overlays in the theme, for comparison with section 1.', 'estecapelli' ) . '</p>';

	echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Doctor', 'estecapelli' ) . '</th>';
	foreach ( $languages as $language ) {
		echo '<th><code>' . esc_html( $language ) . '</code></th>';
	}
	echo '</tr></thead><tbody>';

	foreach ( estecapelli_doctor_diag_expected_positions() as $slug => $by_language ) {
		echo '<tr><td><code>' . esc_html( $slug ) . '</code></td>';
		foreach ( $languages as $language ) {
			echo '<td><small>' . esc_html( isset( $by_language[ $language ] ) ? $by_language[ $language ] : '—' ) . '</small></td>';
		}
		echo '</tr>';
	}

	echo '</tbody></table>';
}

/** Red verdict cell. */
function estecapelli_doctor_diag_bad( $text ) {
	return '<span style="color:#b32d2e;font-weight:600;">' . esc_html( $text ) . '</span>';
}

/** Amber verdict cell. */
function estecapelli_doctor_diag_warn( $text ) {
	return '<span style="color:#8a6d00;">' . esc_html( $text ) . '</span>';
}
