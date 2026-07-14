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
define( 'ESTECAPELLI_SLUG_FIX_SIG', 'v2-fr' );

/**
 * treatment_category base slugs, keyed by the English (source) slug.
 * Value is a per-language map of the exact live slug.
 *
 * @return array
 */
function estecapelli_slug_map_categories() {
	return array(
		'hair-transplant'  => array( 'fr' => 'greffe-de-cheveux' ),
		'plastic-surgery'  => array( 'fr' => 'chirurgie-plastique' ),
		'dental-treatment' => array( 'fr' => 'traitement-dentaire' ),
	);
}

/**
 * treatment post slugs, keyed by the English (source) slug. Slugs that are
 * intentionally identical across languages (tricholab, bbl,
 * plastic-surgery-overview) are omitted — WPML keeps them as-is.
 *
 * @return array
 */
function estecapelli_slug_map_treatments() {
	return array(
		// Hair transplant.
		'hair-transplant-overview'                                => array( 'fr' => 'apercu-de-la-greffe-de-cheveux' ),
		'sapphire-fue-hair-transplant'                            => array( 'fr' => 'greffe-de-cheveux-fue-sapphire' ),
		'dhi-hair-transplant'                                     => array( 'fr' => 'greffe-de-cheveux-dhi' ),
		'exosome-fue-hair-transplant'                             => array( 'fr' => 'greffe-capillaire-exosome-fue' ),
		'vita-treatment'                                          => array( 'fr' => 'traitement-vita' ),
		'female-hair-transplant'                                  => array( 'fr' => 'greffe-de-cheveux-feminine' ),
		'eyebrow-transplant'                                      => array( 'fr' => 'transplantation-de-sourcils' ),
		'beard-transplant'                                        => array( 'fr' => 'transplantation-de-barbe' ),
		'hair-mesotherapy'                                        => array( 'fr' => 'mesotherapie-capillaire' ),
		'pre-hair-transplant-period'                              => array( 'fr' => 'periode-pre-transplantation-capillaire' ),
		'post-hair-transplant-period'                             => array( 'fr' => 'periode-post-greffe-de-cheveux' ),
		'hair-transplant-techniques-comparison2'                  => array( 'fr' => 'comparaison-des-techniques-de-greffe-de-cheveux-2' ),
		// Plastic surgery.
		'rhinoplasty'                                             => array( 'fr' => 'rhinoplastie' ),
		'breast-aesthetics-breast-surgery'                        => array( 'fr' => 'esthetique-mammaire-chirurgie-mammaire' ),
		'liposuction'                                             => array( 'fr' => 'liposuccion' ),
		'face-and-neck-lift-surgery'                              => array( 'fr' => 'chirurgie-de-lifting-du-visage-et-du-cou' ),
		'abdominoplasty-tummy-tuck'                               => array( 'fr' => 'abdominoplastie' ),
		'gynecomastia'                                            => array( 'fr' => 'gynecomastie' ),
		'obesity-surgeries-bariatric-surgery-and-gastric-balloon' => array( 'fr' => 'chirurgies-de-l-obesite-chirurgie-bariatrique-et-ballon-gastrique' ),
		// Dental.
		'dental-treatment-overview'                               => array( 'fr' => 'apercu-du-traitement-dentaire' ),
		'dental-implant'                                          => array( 'fr' => 'implant-dentaire' ),
		'hollywood-smile'                                         => array( 'fr' => 'sourire-hollywoodien' ),
	);
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
			remove_action( 'save_post_treatment', 'estecapelli_slug_fix_wake' );
			$res = wp_update_post(
				array(
					'ID'        => (int) $tr_id,
					'post_name' => $slug,
				),
				true
			);
			add_action( 'save_post_treatment', 'estecapelli_slug_fix_wake' );
			if ( ! is_wp_error( $res ) ) {
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
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT t.term_id FROM {$wpdb->terms} t
			 INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id
			 WHERE t.slug = %s AND tt.taxonomy = %s LIMIT 1",
			$slug,
			$taxonomy
		)
	);
}

/**
 * The English (source) post id, found by raw post_name lookup.
 */
function estecapelli_source_post_id( $slug, $post_type ) {
	global $wpdb;
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = %s AND post_status = 'publish' LIMIT 1",
			$slug,
			$post_type
		)
	);
}
