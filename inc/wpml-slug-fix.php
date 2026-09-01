<?php
/**
 * Force WPML-translated slugs to match the live indexed URLs (tools/url-map.md).
 *
 * WPML/PTC generates its own translated slugs when it creates a translation
 * (e.g. it made the French Dental category "soins-dentaires" and Hollywood Smile
 * "sourire-hollywood"). But the live, Google-indexed French URLs are
 * "traitement-dentaire" and "sourire-hollywoodien" — so WPML's guesses 404.
 *
 * The golden rule of this migration is that every language URL must match the
 * live site exactly. Rather than hand-correcting slugs in the WPML UI (slow and
 * error-prone across 7 languages), we set them from the url-map here, in code.
 * The map is the single source of truth.
 *
 * This runs as a light sweep on admin load. It "settles" (stops running) once
 * every mapped translation already has the right slug, and automatically wakes
 * up again the moment a new translation is saved — so as you translate pages in
 * WPML, their slugs self-correct with no re-deploy. Bump SIGNATURE whenever the
 * tables below change (e.g. when a new language is added) to force a re-sweep.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Signature of the current slug tables. Bump when rows/languages are added so
 * the sweep re-runs against the new data.
 */
define( 'ESTECAPELLI_SLUG_FIX_SIG', 'v5-indexed-ro' );

/**
 * treatment_category base slugs, keyed by the English (source) slug.
 * Value is a per-language map of the exact live slug.
 *
 * @return array
 */
function estecapelli_slug_map_categories() {
	$maps = estecapelli_indexed_category_slugs();
	foreach ( $maps as &$languages ) {
		unset( $languages['en'] );
	}
	unset( $languages );
	return $maps;
}

/**
 * treatment post slugs, keyed by the English (source) slug. Slugs that are
 * intentionally identical across languages (tricholab, bbl,
 * plastic-surgery-overview) are omitted — WPML keeps them as-is.
 *
 * @return array
 */
function estecapelli_slug_map_treatments() {
	$maps = estecapelli_indexed_treatment_slugs();
	foreach ( $maps as &$languages ) {
		unset( $languages['en'] );
	}
	unset( $languages );
	return $maps;
}

/** Exact translated page leaf slugs, keyed by the English page path. */
function estecapelli_slug_map_pages() {
	$maps = array();
	foreach ( estecapelli_indexed_route_contract() as $key => $routes ) {
		if ( preg_match( '#^/en/(?:home|hair-transplant|plastic-surgery|dental-treatment|before-after|about-us|blog|contact)(?:/(?:our-doctors|our-team))?$#', $key ) ) {
			foreach ( $routes as $lang => $route ) {
				if ( 'en' !== $lang ) {
					$maps[ substr( $key, 4 ) ][ $lang ] = basename( $route );
				}
			}
		}
	}
	return $maps;
}

/** Exact translated blog slugs, keyed by the English source slug. */
function estecapelli_slug_map_posts() {
	$maps = estecapelli_indexed_blog_slugs();
	foreach ( $maps as &$languages ) {
		unset( $languages['en'] );
	}
	unset( $languages );
	return $maps;
}

/**
 * Light sweep on admin load. Corrects any mapped translation whose slug is
 * wrong, then settles until new translation work invalidates it.
 */
if ( estecapelli_content_mutations_enabled() ) {
	add_action( 'admin_init', 'estecapelli_wpml_slug_sweep' );
}
function estecapelli_wpml_slug_sweep() {
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
		return; // WPML not active.
	}
	if ( get_option( 'estecapelli_slug_fix_settled' ) === ESTECAPELLI_SLUG_FIX_SIG ) {
		return; // nothing changed since the last clean sweep.
	}

	$changed  = estecapelli_slug_fix_terms( estecapelli_slug_map_categories(), 'treatment_category' );
	$changed += estecapelli_slug_fix_posts( estecapelli_slug_map_treatments(), 'treatment' );
	$changed += estecapelli_slug_fix_posts( estecapelli_slug_map_posts(), 'post' );
	$changed += estecapelli_slug_fix_pages( estecapelli_slug_map_pages() );

	if ( $changed ) {
		flush_rewrite_rules( false ); // re-run next load to confirm it's settled.
	} else {
		update_option( 'estecapelli_slug_fix_settled', ESTECAPELLI_SLUG_FIX_SIG );
	}
}

/**
 * Wake the sweep whenever a treatment or its category is saved — this is how a
 * freshly-created WPML translation gets its slug corrected on the next load.
 */
if ( estecapelli_content_mutations_enabled() ) {
	add_action( 'save_post_treatment', 'estecapelli_slug_fix_wake' );
	add_action( 'save_post_page', 'estecapelli_slug_fix_wake' );
	add_action( 'save_post_post', 'estecapelli_slug_fix_wake' );
	add_action( 'created_treatment_category', 'estecapelli_slug_fix_wake' );
	add_action( 'edited_treatment_category', 'estecapelli_slug_fix_wake' );
}
function estecapelli_slug_fix_wake() {
	delete_option( 'estecapelli_slug_fix_settled' );
}

/**
 * Point WPML term translations at their correct live slugs.
 *
 * @return int Number of terms updated.
 */
function estecapelli_slug_fix_terms( $map, $taxonomy ) {
	$count = 0;
	foreach ( $map as $en_slug => $by_lang ) {
		$en_id = estecapelli_source_term_id( $en_slug, $taxonomy );
		if ( ! $en_id ) {
			continue;
		}
		foreach ( $by_lang as $lang => $slug ) {
			$wpml_lang = estecapelli_wpml_language_code( $lang );
			$tr_id     = apply_filters( 'wpml_object_id', $en_id, $taxonomy, false, $wpml_lang );
			if ( ! $tr_id || (int) $tr_id === (int) $en_id ) {
				continue; // no translation in this language yet.
			}
			$term = get_term( (int) $tr_id, $taxonomy );
			if ( ! $term || is_wp_error( $term ) || $term->slug === $slug ) {
				continue;
			}
			$res = wp_update_term( (int) $tr_id, $taxonomy, array( 'slug' => $slug ) );
			if ( ! is_wp_error( $res ) ) {
				$count++;
			}
		}
	}
	return $count;
}

/**
 * Point WPML post translations at their correct live slugs.
 *
 * @return int Number of posts updated.
 */
function estecapelli_slug_fix_posts( $map, $post_type ) {
	$count = 0;
	foreach ( $map as $en_slug => $by_lang ) {
		$en_id = estecapelli_source_post_id( $en_slug, $post_type );
		if ( ! $en_id ) {
			continue;
		}
		foreach ( $by_lang as $lang => $slug ) {
			$wpml_lang = estecapelli_wpml_language_code( $lang );
			$tr_id     = apply_filters( 'wpml_object_id', $en_id, $post_type, false, $wpml_lang );
			if ( ! $tr_id || (int) $tr_id === (int) $en_id ) {
				continue;
			}
			if ( get_post_field( 'post_name', (int) $tr_id ) === $slug ) {
				continue;
			}
			// Avoid re-entrancy through our own save_post hook.
			$save_hook = 'save_post_' . $post_type;
			remove_action( $save_hook, 'estecapelli_slug_fix_wake' );
			$res = wp_update_post(
				array(
					'ID'        => (int) $tr_id,
					'post_name' => $slug,
				),
				true
			);
			add_action( $save_hook, 'estecapelli_slug_fix_wake' );
			if ( ! is_wp_error( $res ) ) {
				$count++;
			}
		}
	}
	return $count;
}

/**
 * Correct translated page slugs and attach child pages to translated parents.
 *
 * @return int Number of pages updated.
 */
function estecapelli_slug_fix_pages( $map ) {
	$count = 0;
	foreach ( $map as $english_path => $by_lang ) {
		$source = get_page_by_path( $english_path, OBJECT, 'page' );
		if ( ! $source ) {
			continue;
		}
		$source_id = (int) apply_filters( 'wpml_object_id', $source->ID, 'page', true, 'en' );
		$source    = get_post( $source_id ?: $source->ID );
		$parent_path = dirname( $english_path );
		$parent      = '.' !== $parent_path ? get_page_by_path( $parent_path, OBJECT, 'page' ) : null;
		if ( $parent ) {
			$parent_id = (int) apply_filters( 'wpml_object_id', $parent->ID, 'page', true, 'en' );
			$parent    = get_post( $parent_id ?: $parent->ID );
		}

		foreach ( $by_lang as $lang => $slug ) {
			$wpml_lang = estecapelli_wpml_language_code( $lang );
			$target_id = (int) apply_filters( 'wpml_object_id', $source->ID, 'page', false, $wpml_lang );
			if ( ! $target_id || $target_id === (int) $source->ID ) {
				continue;
			}
			$target_parent = 0;
			if ( $parent ) {
				$translated_parent = (int) apply_filters( 'wpml_object_id', $parent->ID, 'page', false, $wpml_lang );
				$target_parent     = ( $translated_parent && $translated_parent !== (int) $parent->ID )
					? $translated_parent
					: null;
			}
			$updates = array( 'ID' => $target_id );
			if ( get_post_field( 'post_name', $target_id ) !== $slug ) {
				$updates['post_name'] = $slug;
			}
			if ( null !== $target_parent && (int) wp_get_post_parent_id( $target_id ) !== $target_parent ) {
				$updates['post_parent'] = $target_parent;
			}
			if ( 1 === count( $updates ) ) {
				continue;
			}

			remove_action( 'save_post_page', 'estecapelli_slug_fix_wake' );
			$result = wp_update_post( $updates, true );
			add_action( 'save_post_page', 'estecapelli_slug_fix_wake' );
			if ( ! is_wp_error( $result ) ) {
				$count++;
			}
		}
	}
	return $count;
}

/**
 * The English (source) term id, found by raw slug lookup so WPML's language
 * filtering can't hand us a translated term instead.
 */
function estecapelli_source_term_id( $slug, $taxonomy ) {
	global $wpdb;
	$candidate = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT t.term_id FROM {$wpdb->terms} t
			 INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id
			 WHERE t.slug = %s AND tt.taxonomy = %s LIMIT 1",
			$slug,
			$taxonomy
		)
	);
	$source = $candidate ? (int) apply_filters( 'wpml_object_id', $candidate, $taxonomy, true, 'en' ) : 0;
	return $source ?: $candidate;
}

/**
 * English source slug of a term, regardless of the current language.
 *
 * Theme lookups (tab icons, field-of-care ordering) are keyed to the English
 * category slugs. A translated term carries its own localized slug, so we
 * resolve it back to its English original via WPML before matching. Falls back
 * to the term's own slug when WPML is inactive or no English link exists.
 *
 * @param WP_Term|int $term     Term object or id.
 * @param string      $taxonomy Taxonomy (required when $term is an id).
 * @return string English slug, or '' when the term cannot be resolved.
 */
function estecapelli_wpml_source_term_slug( $term, $taxonomy = '' ) {
	if ( ! $term instanceof WP_Term ) {
		$term = get_term( (int) $term, $taxonomy );
	}
	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$english_id = (int) apply_filters( 'wpml_object_id', $term->term_id, $term->taxonomy, false, 'en' );
	if ( $english_id && $english_id !== (int) $term->term_id ) {
		$english = get_term( $english_id, $term->taxonomy );
		if ( $english instanceof WP_Term ) {
			return $english->slug;
		}
	}
	return $term->slug;
}

/**
 * English source slug (post_name) of a post, regardless of the current language.
 *
 * Mirrors estecapelli_wpml_source_term_slug() for treatment card ordering, which
 * is keyed to the English post slugs. Falls back to the post's own slug.
 *
 * @param WP_Post|int $post Post object or id.
 * @return string English post_name, or '' when the post cannot be resolved.
 */
function estecapelli_wpml_source_post_slug( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	$english_id = (int) apply_filters( 'wpml_object_id', $post->ID, $post->post_type, false, 'en' );
	if ( $english_id && $english_id !== (int) $post->ID ) {
		$english = get_post( $english_id );
		if ( $english instanceof WP_Post ) {
			return $english->post_name;
		}
	}
	return $post->post_name;
}

/**
 * The English (source) post id, found by raw post_name lookup.
 *
 * A slug can be held by several posts at once, because WPML allows a translation
 * to keep its English slug (bbl does so in every language). Every candidate is
 * therefore resolved to English rather than trusting an arbitrary first row.
 */
function estecapelli_source_post_id( $slug, $post_type ) {
	global $wpdb;
	$candidate_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = %s AND post_status = 'publish'
			 ORDER BY ID ASC",
			$slug,
			$post_type
		)
	);
	$candidate_ids = array_map( 'intval', (array) $candidate_ids );

	foreach ( $candidate_ids as $candidate_id ) {
		$source = (int) apply_filters( 'wpml_object_id', $candidate_id, $post_type, false, 'en' );
		if ( $source ) {
			return $source;
		}
	}

	return $candidate_ids ? $candidate_ids[0] : 0;
}

/**
 * Verify a WPML relationship directly from its source-of-truth table.
 *
 * WPML's object-id filter can retain the pre-update value for the remainder of
 * an admin request. Importers use this raw check only after calling
 * wpml_set_element_language_details, so a valid repair is not reported as a
 * failure merely because a runtime cache is stale.
 */
function estecapelli_wpml_element_matches_raw( $element_id, $element_type, $trid, $language ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';
	$row   = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT trid, language_code FROM {$table} WHERE element_id = %d AND element_type = %s LIMIT 1",
			(int) $element_id,
			(string) $element_type
		)
	);

	return $row && (int) $row->trid === (int) $trid && (string) $row->language_code === (string) $language;
}

/**
 * Return the element that actually occupies a language slot in a WPML group.
 *
 * This bypasses WPML's in-request object cache, which can disagree with the
 * translation table after interrupted imports or relationship repairs.
 */
function estecapelli_wpml_group_element_id_raw( $trid, $element_type, $language ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';

	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT element_id FROM {$table}
			 WHERE trid = %d AND element_type = %s AND language_code = %s
			 ORDER BY translation_id ASC LIMIT 1",
			(int) $trid,
			(string) $element_type,
			(string) $language
		)
	);
}

/** Remove a stale relationship row that no longer points to a valid element. */
function estecapelli_wpml_delete_relationship_raw( $element_id, $element_type, $trid, $language ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';

	return false !== $wpdb->delete(
		$table,
		array(
			'element_id'    => (int) $element_id,
			'element_type'  => (string) $element_type,
			'trid'          => (int) $trid,
			'language_code' => (string) $language,
		),
		array( '%d', '%s', '%d', '%s' )
	);
}

/**
 * Last-resort relationship repair when WPML's public setter refuses an update.
 *
 * The caller must first select the existing element occupying this language
 * slot. We never overwrite a different element in the same group/language.
 */
function estecapelli_wpml_repair_relationship_raw( $element_id, $element_type, $trid, $language, $source_language ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';

	$conflict_id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT element_id FROM {$table}
			 WHERE trid = %d AND element_type = %s AND language_code = %s AND element_id <> %d
			 ORDER BY translation_id ASC LIMIT 1",
			(int) $trid,
			(string) $element_type,
			(string) $language,
			(int) $element_id
		)
	);
	if ( $conflict_id ) {
		return false;
	}

	$translation_id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT translation_id FROM {$table}
			 WHERE element_id = %d AND element_type = %s
			 ORDER BY translation_id ASC LIMIT 1",
			(int) $element_id,
			(string) $element_type
		)
	);

	$values = array(
		'trid'                 => (int) $trid,
		'language_code'        => (string) $language,
		'source_language_code' => (string) $source_language,
	);
	if ( $translation_id ) {
		$saved = $wpdb->update(
			$table,
			$values,
			array( 'translation_id' => $translation_id ),
			array( '%d', '%s', '%s' ),
			array( '%d' )
		);
	} else {
		$values['element_id']   = (int) $element_id;
		$values['element_type'] = (string) $element_type;
		$saved                  = $wpdb->insert(
			$table,
			$values,
			array( '%d', '%s', '%s', '%d', '%s' )
		);
	}

	return false !== $saved && estecapelli_wpml_element_matches_raw( $element_id, $element_type, $trid, $language );
}

/**
 * Return (and clear) the diagnostic for the last language-slot replacement.
 *
 * estecapelli_wpml_replace_language_slot_raw() returns only a bool, so when a
 * forced import fails the underlying reason (which SQL step failed, the DB error
 * and the group's row state) is stashed here for the caller to surface.
 *
 * @return string Human-readable diagnostic, or '' if the last call succeeded.
 */
function estecapelli_wpml_last_slot_error() {
	return (string) apply_filters( 'estecapelli_wpml_last_slot_error', '' );
}

/**
 * Replace one WPML group/language slot with a known canonical element.
 *
 * This is reserved for explicit, version-controlled imports. Both the target's
 * old relationship and the stale occupant of the requested slot are replaced
 * atomically, while rows belonging to other element types/languages are left
 * untouched.
 */
function estecapelli_wpml_replace_language_slot_raw( $element_id, $element_type, $trid, $language, $source_language ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';

	// Reset the diagnostic for this attempt.
	estecapelli_wpml_set_slot_error( '' );

	$wpdb->query( 'START TRANSACTION' );

	$translation_id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT translation_id FROM {$table}
			 WHERE element_id = %d AND element_type = %s
			 ORDER BY translation_id ASC LIMIT 1",
			(int) $element_id,
			(string) $element_type
		)
	);
	// Clear the slot by its real unique key. WPML's `trid_lang` index is on
	// (trid, language_code) alone, so a conflicting occupant must be removed
	// regardless of its element_type — and a stale row with a NULL element_id
	// (which `element_id <> %d` silently skips) would otherwise survive and
	// collide with the write below. We exclude only our own translation row.
	$slot_deleted   = $translation_id
		? $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$table}
				 WHERE trid = %d AND language_code = %s AND translation_id <> %d",
				(int) $trid,
				(string) $language,
				(int) $translation_id
			)
		)
		: $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$table}
				 WHERE trid = %d AND language_code = %s",
				(int) $trid,
				(string) $language
			)
		);
	if ( false === $slot_deleted ) {
		estecapelli_wpml_set_slot_error(
			sprintf( 'clearing the %s slot in trid %d failed: %s', $language, (int) $trid, $wpdb->last_error ?: 'unknown DB error' )
		);
		$wpdb->query( 'ROLLBACK' );
		return false;
	}

	$values         = array(
		'trid'                 => (int) $trid,
		'language_code'        => (string) $language,
		'source_language_code' => (string) $source_language,
	);
	if ( $translation_id ) {
		$saved = $wpdb->update(
			$table,
			$values,
			array( 'translation_id' => $translation_id ),
			array( '%d', '%s', '%s' ),
			array( '%d' )
		);
	} else {
		$values['element_type'] = (string) $element_type;
		$values['element_id']   = (int) $element_id;
		$saved                  = $wpdb->insert(
			$table,
			$values,
			array( '%d', '%s', '%s', '%s', '%d' )
		);
	}

	if ( false === $saved ) {
		estecapelli_wpml_set_slot_error(
			sprintf(
				'%s row for element %d could not be written into trid %d: %s',
				$translation_id ? 'updating the' : 'inserting a',
				(int) $element_id,
				(int) $trid,
				$wpdb->last_error ?: 'unknown DB error'
			)
		);
		$wpdb->query( 'ROLLBACK' );
		return false;
	}

	$valid = estecapelli_wpml_element_matches_raw( $element_id, $element_type, $trid, $language );
	if ( ! $valid ) {
		estecapelli_wpml_set_slot_error(
			sprintf(
				'the write did not stick — element %d still does not occupy the %s slot in trid %d. Current group rows: %s',
				(int) $element_id,
				$language,
				(int) $trid,
				estecapelli_wpml_describe_group_raw( $trid, $element_type )
			)
		);
	}
	$wpdb->query( $valid ? 'COMMIT' : 'ROLLBACK' );
	return $valid;
}

/**
 * Store the diagnostic for the most recent language-slot replacement attempt.
 *
 * @param string $message Diagnostic text ('' clears it).
 */
function estecapelli_wpml_set_slot_error( $message ) {
	remove_all_filters( 'estecapelli_wpml_last_slot_error' );
	$message = (string) $message;
	add_filter(
		'estecapelli_wpml_last_slot_error',
		static function () use ( $message ) {
			return $message;
		}
	);
}

/**
 * Summarise the rows of one WPML translation group for diagnostics.
 *
 * @param int    $trid         Translation group id.
 * @param string $element_type WPML element type.
 * @return string One row per relationship: "lang=element_id(post_status)".
 */
function estecapelli_wpml_describe_group_raw( $trid, $element_type ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT t.language_code, t.element_id, p.post_status
			 FROM {$table} t
			 LEFT JOIN {$wpdb->posts} p ON p.ID = t.element_id
			 WHERE t.trid = %d AND t.element_type = %s
			 ORDER BY t.language_code",
			(int) $trid,
			(string) $element_type
		)
	);
	if ( ! $rows ) {
		return '(none)';
	}
	$parts = array();
	foreach ( $rows as $row ) {
		$parts[] = sprintf( '%s=%d(%s)', $row->language_code, (int) $row->element_id, $row->post_status ?: 'missing' );
	}
	return implode( ', ', $parts );
}
