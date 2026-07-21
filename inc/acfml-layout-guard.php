<?php
/**
 * Keep ACF Flexible Content layout selectors canonical across languages.
 *
 * ACF normally stores the ordered layout names in the parent `page_sections`
 * meta value. A translated post's own canonical structure must remain the
 * source of truth; otherwise opening the native editor can load the English
 * page's older row list and save it over a newly imported translation. We only
 * recover structure from the original-language post when the translated value
 * is missing or contains invalid/translated layout names.
 *
 * The source post is only the repair fallback, so no translated layout-name
 * table is needed and future layouts are protected automatically.
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
 * Repair invalid Flexible Content structure from the original-language post.
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

	// A valid translated layout list is authoritative. Replacing it merely
	// because it differs from English makes the editor resurrect stale rows.
	if ( $is_parent_layout ) {
		if ( estecapelli_has_canonical_layout_list( $stored ) ) {
			return $value;
		}

		$source_layouts = estecapelli_meta_from_source( $object_id, $meta_key );
		if ( estecapelli_has_canonical_layout_list( $source_layouts ) ) {
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

/**
 * Capture a translated post's submitted ACF values after ACF accepted them.
 *
 * Some ACFML/WPML versions run a source-field synchronisation later in the
 * WordPress save lifecycle. The importer succeeds because it writes outside
 * that lifecycle, while a normal editor Save can consequently end with the old
 * source values. Keeping the already-validated submitted values lets us make
 * that editor save deterministic after every plugin save hook has completed.
 *
 * @param int|string $post_id ACF post ID.
 * @return void
 */
function estecapelli_capture_translated_acf_save( $post_id ) {
	$post_id = is_numeric( $post_id ) ? (int) $post_id : 0;
	if ( ! $post_id || empty( $_POST['acf'] ) || ! is_array( $_POST['acf'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	if ( ! in_array( $post_type, array( 'page', 'treatment', 'doctor' ), true ) ) {
		return;
	}

	$details = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => $post_id,
			'element_type' => $post_type,
		)
	);
	$language = is_object( $details ) ? ( $details->language_code ?? '' ) : ( is_array( $details ) ? ( $details['language_code'] ?? '' ) : '' );
	$default  = (string) apply_filters( 'wpml_default_language', null );
	if ( ! $language || ! $default || $language === $default ) {
		return;
	}

	$GLOBALS['estecapelli_translated_acf_save'] = array(
		'post_id' => $post_id,
		'values'  => $_POST['acf'], // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ACF validated and saved these values before this hook runs.
	);
}
add_action( 'acf/save_post', 'estecapelli_capture_translated_acf_save', PHP_INT_MAX );

/**
 * Re-apply the accepted translated ACF submission after WPML save callbacks.
 *
 * `acf_update_values()` is the same ACF API used by its normal form handler.
 * Calling it does not fire `save_post`, so it cannot recurse through WPML's
 * post-save synchronisation a second time.
 *
 * @return void
 */
function estecapelli_commit_translated_acf_save() {
	$snapshot = $GLOBALS['estecapelli_translated_acf_save'] ?? null;
	unset( $GLOBALS['estecapelli_translated_acf_save'] );

	if ( ! is_array( $snapshot ) || empty( $snapshot['post_id'] ) || empty( $snapshot['values'] ) || ! function_exists( 'acf_update_values' ) ) {
		return;
	}

	acf_update_values( $snapshot['values'], (int) $snapshot['post_id'] );
	clean_post_cache( (int) $snapshot['post_id'] );
}
add_action( 'shutdown', 'estecapelli_commit_translated_acf_save', PHP_INT_MAX );
