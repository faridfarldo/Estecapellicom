<?php
/**
 * Keep ACF flexible-content layout selectors canonical across languages.
 *
 * WPML/ACFML (or a legacy custom-field-translation preference) can translate
 * the INTERNAL `acf_fc_layout` value of our `page_sections` flexible content —
 * e.g. "stepbook" → "guide-etapes", "candidate" → "candidat", "gallery" →
 * "galerie". ACF then can't match the row to a defined layout and DROPS it, so
 * the translated page renders with missing sections (or empty).
 *
 * We repair the selector at read time: if a stored `*_acf_fc_layout` value is
 * not one of the theme's real layouts, we pull the SAME key from the post's
 * original-language counterpart, which always holds the English selector. There
 * is no hard-coded translation table — the source post is the source of truth —
 * so this works for every language and every future layout automatically.
 *
 * English (source) posts and already-canonical values are never touched.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The theme's real flexible-content layouts = the section templates that exist.
 * Cached for the request.
 *
 * @return array<string,bool> Map of layout name => true.
 */
function estecapelli_canonical_layouts() {
	static $layouts = null;
	if ( null === $layouts ) {
		$layouts = array();
		foreach ( (array) glob( get_template_directory() . '/template-parts/sections/*.php' ) as $file ) {
			$layouts[ basename( $file, '.php' ) ] = true;
		}
	}
	return $layouts;
}

add_filter( 'get_post_metadata', 'estecapelli_fix_fc_layout', 10, 4 );
/**
 * Restore a canonical layout selector when the stored one has been translated.
 *
 * Hooks the meta read so ACF sees the English layout name and keeps the row.
 */
function estecapelli_fix_fc_layout( $value, $object_id, $meta_key, $single ) {
	// Only flexible-content layout selectors (incl. nested ones).
	if ( ! is_string( $meta_key ) || '_acf_fc_layout' !== substr( $meta_key, -14 ) ) {
		return $value;
	}

	// Guard against recursion: our own get_post_meta() call below re-enters here.
	static $busy = false;
	if ( $busy ) {
		return $value;
	}

	$busy   = true;
	$stored = get_post_meta( $object_id, $meta_key, true );
	$busy   = false;

	if ( '' === $stored ) {
		return $value; // no value stored — let ACF/WP handle it.
	}

	$layouts = estecapelli_canonical_layouts();
	if ( isset( $layouts[ $stored ] ) ) {
		return $value; // already canonical — do not interfere.
	}

	// Non-canonical (translated) selector: recover it from the source post.
	$canonical = estecapelli_layout_from_source( $object_id, $meta_key );
	if ( '' !== $canonical && isset( $layouts[ $canonical ] ) ) {
		return $single ? $canonical : array( $canonical );
	}

	return $value;
}

/**
 * Read the same layout-selector key from the post's original-language version.
 *
 * @return string Canonical layout name, or '' when there is no source post.
 */
function estecapelli_layout_from_source( $post_id, $meta_key ) {
	$post_type = get_post_type( $post_id );
	if ( ! $post_type ) {
		return '';
	}

	$default   = apply_filters( 'wpml_default_language', null );
	$source_id = apply_filters( 'wpml_object_id', $post_id, $post_type, false, $default );

	// No WPML, or this IS the source post — nothing to recover.
	if ( ! $source_id || (int) $source_id === (int) $post_id ) {
		return '';
	}

	global $wpdb;
	return (string) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s LIMIT 1",
			(int) $source_id,
			$meta_key
		)
	);
}
