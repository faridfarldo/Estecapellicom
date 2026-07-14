<?php
/**
 * Keep ACF Flexible Content layout selectors canonical across languages.
 *
 * ACF normally stores the ordered layout names in the parent `page_sections`
 * meta value. We always use that structural value from the original-language
 * post when reading a translation. The legacy `*_acf_fc_layout` repair remains
 * for any rows written in that form by older imports or integrations.
 *
 * The source post is the source of truth, so no translated layout-name table is
 * needed and future layouts are protected automatically.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the theme's real Flexible Content layouts, cached for the request.
 *
 * @return array<string,bool> Map of layout name to true.
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
 * Restore Flexible Content structure from the original-language post.
 *
 * @param mixed  $value     Existing short-circuit value.
 * @param int    $object_id Post ID.
 * @param string $meta_key  Meta key.
 * @param bool   $single    Whether a single value was requested.
 * @return mixed
 */
function estecapelli_fix_fc_layout( $value, $object_id, $meta_key, $single ) {
	if ( ! is_string( $meta_key ) ) {
		return $value;
	}

	$is_parent_layout = 'page_sections' === $meta_key;
	$is_row_layout    = '_acf_fc_layout' === substr( $meta_key, -14 );

	// Only the parent layout list or legacy per-row layout selectors.
	if ( ! $is_parent_layout && ! $is_row_layout ) {
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

	// The parent value contains only ordered layout names. Returning the source
	// value cannot overwrite translated sub-fields, which use separate meta keys.
	if ( $is_parent_layout ) {
		$source_layouts = estecapelli_meta_from_source( $object_id, $meta_key );
		if ( estecapelli_has_canonical_layout_list( $source_layouts ) && $stored !== $source_layouts ) {
			return $single ? $source_layouts : array( $source_layouts );
		}

		return $value;
	}

	if ( '' === $stored ) {
		return $value;
	}

	$layouts = estecapelli_canonical_layouts();
	if ( isset( $layouts[ $stored ] ) ) {
		return $value;
	}

	$canonical = estecapelli_layout_from_source( $object_id, $meta_key );
	if ( '' !== $canonical && isset( $layouts[ $canonical ] ) ) {
		return $single ? $canonical : array( $canonical );
	}

	return $value;
}

/**
 * Read a legacy per-row layout selector from the original-language post.
 *
 * @param int    $post_id  Translation post ID.
 * @param string $meta_key Layout-selector meta key.
 * @return string Canonical layout name, or an empty string.
 */
function estecapelli_layout_from_source( $post_id, $meta_key ) {
	$source_value = estecapelli_meta_from_source( $post_id, $meta_key );
	return is_string( $source_value ) ? $source_value : '';
}

/**
 * Read an unfiltered meta value from the original-language post.
 *
 * @param int    $post_id  Translation post ID.
 * @param string $meta_key Meta key to read.
 * @return mixed|null Source value, or null when there is no source post/value.
 */
function estecapelli_meta_from_source( $post_id, $meta_key ) {
	$post_type = get_post_type( $post_id );
	if ( ! $post_type ) {
		return null;
	}

	$default   = apply_filters( 'wpml_default_language', null );
	$source_id = apply_filters( 'wpml_object_id', $post_id, $post_type, false, $default );

	// No WPML, or this is the source post: there is nothing to recover.
	if ( ! $source_id || (int) $source_id === (int) $post_id ) {
		return null;
	}

	global $wpdb;
	$raw_value = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s LIMIT 1",
			(int) $source_id,
			$meta_key
		)
	);

	return null === $raw_value ? null : maybe_unserialize( $raw_value );
}

/**
 * Confirm that a parent Flexible Content value contains only real layouts.
 *
 * @param mixed $layouts Candidate layout list.
 * @return bool
 */
function estecapelli_has_canonical_layout_list( $layouts ) {
	if ( ! is_array( $layouts ) ) {
		return false;
	}

	$canonical = estecapelli_canonical_layouts();
	foreach ( $layouts as $layout ) {
		if ( ! is_string( $layout ) || ! isset( $canonical[ $layout ] ) ) {
			return false;
		}
	}

	return true;
}
