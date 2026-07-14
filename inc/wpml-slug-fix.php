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
define( 'ESTECAPELLI_SLUG_FIX_SIG', 'v3-indexed-7-languages' );

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
		if ( preg_match( '#^/en/(?:home|hair-transplant|plastic-surgery|dental-treatment|before-after|about-us|blog|contact)(?:/(?:our-doctors|medical-director|our-team))?$#', $key ) ) {
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
add_action( 'admin_init', 'estecapelli_wpml_slug_sweep' );
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
add_action( 'save_post_treatment', 'estecapelli_slug_fix_wake' );
add_action( 'save_post_page', 'estecapelli_slug_fix_wake' );
add_action( 'save_post_post', 'estecapelli_slug_fix_wake' );
add_action( 'created_treatment_category', 'estecapelli_slug_fix_wake' );
add_action( 'edited_treatment_category', 'estecapelli_slug_fix_wake' );
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
			$tr_id = apply_filters( 'wpml_object_id', $en_id, $taxonomy, false, $lang );
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
			$tr_id = apply_filters( 'wpml_object_id', $en_id, $post_type, false, $lang );
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
			$target_id = (int) apply_filters( 'wpml_object_id', $source->ID, 'page', false, $lang );
			if ( ! $target_id || $target_id === (int) $source->ID ) {
				continue;
			}
			$target_parent = 0;
			if ( $parent ) {
				$translated_parent = (int) apply_filters( 'wpml_object_id', $parent->ID, 'page', false, $lang );
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
 * The English (source) post id, found by raw post_name lookup.
 */
function estecapelli_source_post_id( $slug, $post_type ) {
	global $wpdb;
	$candidate = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = %s AND post_status = 'publish' LIMIT 1",
			$slug,
			$post_type
		)
	);
	$source = $candidate ? (int) apply_filters( 'wpml_object_id', $candidate, $post_type, true, 'en' ) : 0;
	return $source ?: $candidate;
}
