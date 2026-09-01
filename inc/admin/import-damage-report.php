<?php
/**
 * Import damage report — where else did the importers leave a mess?
 *
 * The doctors and the blog were each found by accident, one screenshot at a
 * time, after the same two faults had been quietly running for months: a failed
 * import left its post behind and was retried on every admin page load, and
 * claiming a WPML language slot deletes the previous occupant's row without
 * touching the post. Nothing about either fault was specific to doctors or to
 * posts, so this asks the same questions of everything at once.
 *
 * What it looks for, per post type and per taxonomy:
 *
 *   - more items sharing a slug than there are languages holding it, which is
 *     what a duplicate looks like on a site where one slug per language is
 *     normal and expected;
 *   - orphans, with no row in wp_icl_translations at all — these are the
 *     dangerous ones. An orphan has no language, so it drops out of every
 *     language filter in wp-admin (including "All languages") while still being
 *     served, without its language prefix, and still being put in the sitemap;
 *   - rows filed under a language code the site does not have, 'all' above all,
 *     which WPML reads as "show this in every language";
 *   - translation groups where one language slot is claimed twice.
 *
 * Read-only: every statement is a SELECT. It reports, it never repairs — the
 * repair for anything found here is a decision, not a button.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Post types worth auditing: content, not plumbing. */
function estecapelli_damage_post_types() {
	$types = get_post_types( array( 'public' => true ), 'names' );
	unset( $types['attachment'] );
	return array_values( $types );
}

/** Taxonomies worth auditing. */
function estecapelli_damage_taxonomies() {
	$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );
	unset( $taxonomies['post_format'] );
	return array_values( $taxonomies );
}

/** Whether WPML's translation table is actually there to be read. */
function estecapelli_damage_has_wpml_table() {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';
	return (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
}

/** The language codes this site legitimately uses, in WPML's own spelling. */
function estecapelli_damage_known_languages() {
	$known = array();
	foreach ( estecapelli_indexed_languages() as $language ) {
		$known[] = function_exists( 'estecapelli_wpml_language_code' )
			? (string) estecapelli_wpml_language_code( $language )
			: (string) $language;
	}
	return array_values( array_unique( $known ) );
}

/**
 * Slugs held by more items than there are languages holding them.
 *
 * One post per language sharing a slug is normal here — the doctor profiles are
 * built that way on purpose. More posts than languages is not.
 *
 * @param string $post_type Post type to audit.
 * @return array<int,array<string,mixed>>
 */
function estecapelli_damage_duplicate_slugs( $post_type ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';

	$rows = (array) $wpdb->get_results(
		$wpdb->prepare(
			"SELECT p.post_name,
			        COUNT(*) AS items,
			        COUNT(DISTINCT t.language_code) AS languages,
			        SUM( CASE WHEN t.translation_id IS NULL THEN 1 ELSE 0 END ) AS orphans,
			        GROUP_CONCAT( p.ID ORDER BY p.ID ASC ) AS ids
			 FROM {$wpdb->posts} p
			 LEFT JOIN {$table} t
			        ON t.element_id = p.ID
			       AND t.element_type = CONCAT( 'post_', p.post_type )
			 WHERE p.post_type = %s
			   AND p.post_status NOT IN ( 'trash', 'auto-draft', 'inherit' )
			   AND p.post_name <> ''
			 GROUP BY p.post_name
			 HAVING items > languages OR orphans > 0
			 ORDER BY items DESC, p.post_name ASC",
			$post_type
		)
	);

	return $rows;
}

/**
 * Items with no WPML row at all, per post type.
 *
 * @return array<string,array<int,object>>
 */
function estecapelli_damage_orphans( $post_type ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';

	return (array) $wpdb->get_results(
		$wpdb->prepare(
			"SELECT p.ID, p.post_name, p.post_title, p.post_status
			 FROM {$wpdb->posts} p
			 LEFT JOIN {$table} t
			        ON t.element_id = p.ID
			       AND t.element_type = CONCAT( 'post_', p.post_type )
			 WHERE p.post_type = %s
			   AND p.post_status NOT IN ( 'trash', 'auto-draft', 'inherit' )
			   AND t.translation_id IS NULL
			 ORDER BY p.ID ASC",
			$post_type
		)
	);
}

/**
 * Rows filed under a language the site does not have — 'all' being the one
 * that actually changes what visitors see.
 *
 * @return array<int,object>
 */
function estecapelli_damage_unknown_language_rows() {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';
	$known = estecapelli_damage_known_languages();
	if ( ! $known ) {
		return array();
	}

	$placeholders = implode( ',', array_fill( 0, count( $known ), '%s' ) );

	return (array) $wpdb->get_results(
		$wpdb->prepare(
			"SELECT element_type, language_code, COUNT(*) AS items,
			        GROUP_CONCAT( element_id ORDER BY element_id ASC ) AS ids
			 FROM {$table}
			 WHERE language_code NOT IN ( {$placeholders} )
			 GROUP BY element_type, language_code
			 ORDER BY items DESC",
			$known
		)
	);
}

/**
 * Translation groups where one language slot is claimed more than once.
 *
 * @return array<int,object>
 */
function estecapelli_damage_double_claimed_slots() {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';

	return (array) $wpdb->get_results(
		"SELECT trid, element_type, language_code, COUNT(*) AS items,
		        GROUP_CONCAT( element_id ORDER BY element_id ASC ) AS ids
		 FROM {$table}
		 GROUP BY trid, element_type, language_code
		 HAVING items > 1
		 ORDER BY items DESC
		 LIMIT 200"
	);
}

/**
 * How many items each post type has in each language.
 *
 * A language column that is taller than the others is the shape a duplicate
 * run leaves behind; a short one is content that never arrived.
 *
 * @return array<string,array<string,int>>
 */
function estecapelli_damage_language_matrix( $post_type ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';

	$rows = (array) $wpdb->get_results(
		$wpdb->prepare(
			"SELECT COALESCE( t.language_code, '(orphan)' ) AS language_code, COUNT(*) AS items
			 FROM {$wpdb->posts} p
			 LEFT JOIN {$table} t
			        ON t.element_id = p.ID
			       AND t.element_type = CONCAT( 'post_', p.post_type )
			 WHERE p.post_type = %s
			   AND p.post_status NOT IN ( 'trash', 'auto-draft', 'inherit' )
			 GROUP BY language_code
			 ORDER BY language_code ASC",
			$post_type
		)
	);

	$out = array();
	foreach ( $rows as $row ) {
		$out[ (string) $row->language_code ] = (int) $row->items;
	}
	return $out;
}

/**
 * Terms sharing a slug, and terms with no WPML row, per taxonomy.
 *
 * @return array<int,object>
 */
function estecapelli_damage_term_problems( $taxonomy ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';

	return (array) $wpdb->get_results(
		$wpdb->prepare(
			"SELECT t.term_id, t.name, t.slug, tt.count AS posts,
			        tr.language_code
			 FROM {$wpdb->terms} t
			 INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id
			 LEFT JOIN {$table} tr
			        ON tr.element_id = tt.term_taxonomy_id
			       AND tr.element_type = CONCAT( 'tax_', tt.taxonomy )
			 WHERE tt.taxonomy = %s
			 ORDER BY t.slug ASC",
			$taxonomy
		)
	);
}

add_action( 'admin_menu', 'estecapelli_register_damage_report' );
/** Put the report under Tools, with the other read-only diagnostics. */
function estecapelli_register_damage_report() {
	add_management_page(
		__( 'Estecapelli — Import Damage Report', 'estecapelli' ),
		__( 'Import Damage', 'estecapelli' ),
		'manage_options',
		'estecapelli-import-damage',
		'estecapelli_render_damage_report'
	);
}

/** Build the whole report as plain text, for pasting somewhere else. */
function estecapelli_damage_report_text() {
	$lines   = array();
	$lines[] = '# import damage report — ' . home_url( '/' );
	$lines[] = '# generated ' . gmdate( 'Y-m-d H:i' ) . ' UTC';
	$lines[] = '# languages: ' . implode( ', ', estecapelli_damage_known_languages() );

	foreach ( estecapelli_damage_post_types() as $post_type ) {
		$matrix  = estecapelli_damage_language_matrix( $post_type );
		$parts   = array();
		foreach ( $matrix as $language => $count ) {
			$parts[] = $language . '=' . $count;
		}
		$lines[] = '';
		$lines[] = '## ' . $post_type . '  ' . implode( '  ', $parts );

		$duplicates = estecapelli_damage_duplicate_slugs( $post_type );
		foreach ( $duplicates as $row ) {
			$lines[] = sprintf(
				"DUP\t%s\titems=%d\tlanguages=%d\torphans=%d\tids=%s",
				$row->post_name,
				(int) $row->items,
				(int) $row->languages,
				(int) $row->orphans,
				$row->ids
			);
		}
		if ( ! $duplicates ) {
			$lines[] = '(no duplicate or orphaned slugs)';
		}
	}

	$unknown = estecapelli_damage_unknown_language_rows();
	$lines[] = '';
	$lines[] = '## rows under a language this site does not have';
	if ( $unknown ) {
		foreach ( $unknown as $row ) {
			$lines[] = sprintf( "%s\t%s\titems=%d\tids=%s", $row->element_type, $row->language_code, (int) $row->items, $row->ids );
		}
	} else {
		$lines[] = '(none)';
	}

	$double  = estecapelli_damage_double_claimed_slots();
	$lines[] = '';
	$lines[] = '## language slots claimed more than once';
	if ( $double ) {
		foreach ( $double as $row ) {
			$lines[] = sprintf( "trid=%d\t%s\t%s\titems=%d\tids=%s", (int) $row->trid, $row->element_type, $row->language_code, (int) $row->items, $row->ids );
		}
	} else {
		$lines[] = '(none)';
	}

	foreach ( estecapelli_damage_taxonomies() as $taxonomy ) {
		$terms   = estecapelli_damage_term_problems( $taxonomy );
		$lines[] = '';
		$lines[] = '## taxonomy ' . $taxonomy . ' — ' . count( $terms ) . ' terms';
		foreach ( $terms as $term ) {
			$lines[] = sprintf(
				"%d\t%s\t%s\tposts=%d\tlang=%s",
				(int) $term->term_id,
				$term->slug,
				$term->name,
				(int) $term->posts,
				$term->language_code ? $term->language_code : 'ORPHAN'
			);
		}
	}

	return implode( "\n", $lines );
}

/** Render the report. */
function estecapelli_render_damage_report() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Import Damage Report', 'estecapelli' ) . '</h1>';
	echo '<p class="description">' . esc_html__( 'Read-only. Every statement is a SELECT — this page reports, it never repairs. It asks of every post type and taxonomy the questions that found the doctor and blog duplicates.', 'estecapelli' ) . '</p>';

	if ( ! estecapelli_damage_has_wpml_table() ) {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'WPML translation table not found — nothing to audit.', 'estecapelli' ) . '</p></div></div>';
		return;
	}

	echo '<h2>' . esc_html__( 'Copy this and send it over', 'estecapelli' ) . '</h2>';
	echo '<textarea readonly onclick="this.select();" rows="16" style="width:100%;font-family:monospace;font-size:11px;white-space:pre;">';
	echo esc_textarea( estecapelli_damage_report_text() );
	echo '</textarea>';

	foreach ( estecapelli_damage_post_types() as $post_type ) {
		$matrix     = estecapelli_damage_language_matrix( $post_type );
		$duplicates = estecapelli_damage_duplicate_slugs( $post_type );
		$orphans    = estecapelli_damage_orphans( $post_type );

		echo '<h2>' . esc_html( $post_type ) . '</h2>';

		echo '<p>';
		foreach ( $matrix as $language => $count ) {
			$bad = '(orphan)' === $language;
			echo '<span style="display:inline-block;margin-right:14px;' . ( $bad ? 'color:#b32d2e;font-weight:600;' : '' ) . '"><code>' . esc_html( $language ) . '</code> ' . (int) $count . '</span>';
		}
		echo '</p>';

		if ( ! $duplicates ) {
			echo '<p style="color:#1a7f37;">' . esc_html__( 'No duplicate or orphaned slugs.', 'estecapelli' ) . '</p>';
		} else {
			echo '<table class="widefat striped"><thead><tr>';
			echo '<th>' . esc_html__( 'Slug', 'estecapelli' ) . '</th>';
			echo '<th>' . esc_html__( 'Items', 'estecapelli' ) . '</th>';
			echo '<th>' . esc_html__( 'Languages', 'estecapelli' ) . '</th>';
			echo '<th>' . esc_html__( 'Orphans', 'estecapelli' ) . '</th>';
			echo '<th>' . esc_html__( 'IDs', 'estecapelli' ) . '</th>';
			echo '</tr></thead><tbody>';
			foreach ( $duplicates as $row ) {
				echo '<tr>';
				echo '<td><code>' . esc_html( $row->post_name ) . '</code></td>';
				echo '<td><strong>' . (int) $row->items . '</strong></td>';
				echo '<td>' . (int) $row->languages . '</td>';
				echo '<td>' . ( (int) $row->orphans ? '<strong style="color:#b32d2e;">' . (int) $row->orphans . '</strong>' : '0' ) . '</td>';
				echo '<td><small>' . esc_html( $row->ids ) . '</small></td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		}

		if ( $orphans ) {
			echo '<p class="description">' . esc_html__( 'Orphans in full — these carry no language, so no language filter in wp-admin will show them, and the only way to open one is the link here.', 'estecapelli' ) . '</p>';
			echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'ID', 'estecapelli' ) . '</th><th>' . esc_html__( 'Slug', 'estecapelli' ) . '</th><th>' . esc_html__( 'Title', 'estecapelli' ) . '</th><th>' . esc_html__( 'Status', 'estecapelli' ) . '</th><th>' . esc_html__( 'Open', 'estecapelli' ) . '</th></tr></thead><tbody>';
			foreach ( $orphans as $orphan ) {
				echo '<tr>';
				echo '<td>' . (int) $orphan->ID . '</td>';
				echo '<td><code>' . esc_html( $orphan->post_name ) . '</code></td>';
				echo '<td><small>' . esc_html( $orphan->post_title ) . '</small></td>';
				echo '<td>' . esc_html( $orphan->post_status ) . '</td>';
				echo '<td><a href="' . esc_url( (string) get_edit_post_link( $orphan->ID, 'raw' ) ) . '">' . esc_html__( 'Edit', 'estecapelli' ) . '</a></td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		}
	}

	$unknown = estecapelli_damage_unknown_language_rows();
	echo '<h2>' . esc_html__( 'Rows under a language this site does not have', 'estecapelli' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'An "all" row is the one that changes what visitors see: WPML reads it as "show this element in every language", so a single translation surfaces site-wide.', 'estecapelli' ) . '</p>';
	if ( ! $unknown ) {
		echo '<p style="color:#1a7f37;">' . esc_html__( 'None.', 'estecapelli' ) . '</p>';
	} else {
		echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Element type', 'estecapelli' ) . '</th><th>' . esc_html__( 'Language', 'estecapelli' ) . '</th><th>' . esc_html__( 'Items', 'estecapelli' ) . '</th><th>' . esc_html__( 'IDs', 'estecapelli' ) . '</th></tr></thead><tbody>';
		foreach ( $unknown as $row ) {
			echo '<tr><td><code>' . esc_html( $row->element_type ) . '</code></td><td><strong style="color:#b32d2e;">' . esc_html( $row->language_code ) . '</strong></td><td>' . (int) $row->items . '</td><td><small>' . esc_html( $row->ids ) . '</small></td></tr>';
		}
		echo '</tbody></table>';
	}

	$double = estecapelli_damage_double_claimed_slots();
	echo '<h2>' . esc_html__( 'Language slots claimed more than once', 'estecapelli' ) . '</h2>';
	if ( ! $double ) {
		echo '<p style="color:#1a7f37;">' . esc_html__( 'None.', 'estecapelli' ) . '</p>';
	} else {
		echo '<table class="widefat striped"><thead><tr><th>trid</th><th>' . esc_html__( 'Element type', 'estecapelli' ) . '</th><th>' . esc_html__( 'Language', 'estecapelli' ) . '</th><th>' . esc_html__( 'Items', 'estecapelli' ) . '</th><th>' . esc_html__( 'IDs', 'estecapelli' ) . '</th></tr></thead><tbody>';
		foreach ( $double as $row ) {
			echo '<tr><td>' . (int) $row->trid . '</td><td><code>' . esc_html( $row->element_type ) . '</code></td><td>' . esc_html( $row->language_code ) . '</td><td><strong style="color:#b32d2e;">' . (int) $row->items . '</strong></td><td><small>' . esc_html( $row->ids ) . '</small></td></tr>';
		}
		echo '</tbody></table>';
	}

	foreach ( estecapelli_damage_taxonomies() as $taxonomy ) {
		$terms = estecapelli_damage_term_problems( $taxonomy );
		echo '<h2>' . esc_html__( 'Taxonomy:', 'estecapelli' ) . ' ' . esc_html( $taxonomy ) . '</h2>';
		echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Term ID', 'estecapelli' ) . '</th><th>' . esc_html__( 'Slug', 'estecapelli' ) . '</th><th>' . esc_html__( 'Name', 'estecapelli' ) . '</th><th>' . esc_html__( 'Posts', 'estecapelli' ) . '</th><th>' . esc_html__( 'Language', 'estecapelli' ) . '</th></tr></thead><tbody>';
		foreach ( $terms as $term ) {
			$orphan = ! $term->language_code;
			echo '<tr>';
			echo '<td>' . (int) $term->term_id . '</td>';
			echo '<td><code>' . esc_html( $term->slug ) . '</code></td>';
			echo '<td>' . esc_html( $term->name ) . '</td>';
			echo '<td>' . (int) $term->posts . '</td>';
			echo '<td>' . ( $orphan ? '<strong style="color:#b32d2e;">ORPHAN</strong>' : '<code>' . esc_html( $term->language_code ) . '</code>' ) . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}

	echo '</div>';
}
