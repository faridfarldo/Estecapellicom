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

/**
 * Read every raw ACF meta row belonging to one post.
 *
 * ACF stores a hidden reference (`_field_name` => `field_...`) beside each
 * value. Those references let us distinguish ACF data from WordPress/plugin
 * metadata without hard-coding every current and future page-builder field.
 *
 * @param int $post_id Post ID.
 * @return array<int,array{meta_key:string,meta_value:string}>
 */
function estecapelli_raw_acf_meta_snapshot( $post_id ) {
	global $wpdb;

	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d ORDER BY meta_id ASC",
			(int) $post_id
		)
	);
	if ( ! $rows ) {
		return array();
	}

	$acf_keys = array();
	foreach ( $rows as $row ) {
		$key = (string) $row->meta_key;
		if ( '_' === substr( $key, 0, 1 ) && 0 === strpos( (string) $row->meta_value, 'field_' ) ) {
			$acf_keys[ $key ]              = true;
			$acf_keys[ substr( $key, 1 ) ] = true;
		}
	}

	$snapshot = array();
	foreach ( $rows as $row ) {
		$key = (string) $row->meta_key;
		if ( isset( $acf_keys[ $key ] ) ) {
			$snapshot[] = array(
				'meta_key'   => $key,
				'meta_value' => (string) $row->meta_value,
			);
		}
	}

	return $snapshot;
}

/**
 * Snapshot all translations before an English ACF edit can trigger WPML Copy.
 *
 * `pre_post_update` runs before `save_post` and before ACFML's propagation
 * callbacks. This is intentionally limited to real editor submissions carrying
 * ACF values and to the post types managed by the version-controlled importers.
 *
 * @param int   $post_id Post being updated.
 * @param array $data    Sanitized post data (unused).
 * @return void
 */
function estecapelli_snapshot_translations_before_source_acf_save( $post_id, $data ) {
	unset( $data );

	if ( ! empty( $GLOBALS['estecapelli_source_translation_snapshots'] ) ) {
		return;
	}
	if ( empty( $_POST['acf'] ) || ! is_array( $_POST['acf'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
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
			'element_id'   => (int) $post_id,
			'element_type' => $post_type,
		)
	);
	$language = is_object( $details ) ? ( $details->language_code ?? '' ) : ( is_array( $details ) ? ( $details['language_code'] ?? '' ) : '' );
	$trid     = is_object( $details ) ? ( $details->trid ?? 0 ) : ( is_array( $details ) ? ( $details['trid'] ?? 0 ) : 0 );
	$default  = (string) apply_filters( 'wpml_default_language', null );
	if ( ! $trid || ! $language || ! $default || $language !== $default ) {
		return;
	}

	global $wpdb;
	$element_type = (string) apply_filters( 'wpml_element_type', $post_type );
	$table        = $wpdb->prefix . 'icl_translations';
	$translation_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT t.element_id
			 FROM {$table} t
			 INNER JOIN {$wpdb->posts} p ON p.ID = t.element_id
			 WHERE t.trid = %d AND t.element_type = %s AND t.element_id <> %d
			   AND p.post_type = %s AND p.post_status <> 'trash'
			 ORDER BY t.translation_id ASC",
			(int) $trid,
			$element_type,
			(int) $post_id,
			$post_type
		)
	);

	$snapshots = array();
	foreach ( array_map( 'intval', (array) $translation_ids ) as $translation_id ) {
		if ( $translation_id ) {
			$snapshots[ $translation_id ] = estecapelli_raw_acf_meta_snapshot( $translation_id );
		}
	}
	if ( $snapshots ) {
		$GLOBALS['estecapelli_source_translation_snapshots'] = $snapshots;
	}
}
add_action( 'pre_post_update', 'estecapelli_snapshot_translations_before_source_acf_save', 0, 2 );

/**
 * Replace one post's ACF rows with an exact pre-save snapshot.
 *
 * Direct postmeta writes are deliberate here: using update_field()/update_meta()
 * would re-enter the same ACFML Copy hooks whose side effects are being undone.
 * All unrelated post metadata remains untouched.
 *
 * @param int   $post_id  Translation post ID.
 * @param array $snapshot Raw ACF meta rows.
 * @return bool Whether the replacement completed.
 */
function estecapelli_restore_raw_acf_meta_snapshot( $post_id, array $snapshot ) {
	global $wpdb;

	$current = estecapelli_raw_acf_meta_snapshot( $post_id );
	$keys    = array();
	foreach ( array_merge( $current, $snapshot ) as $row ) {
		if ( ! empty( $row['meta_key'] ) ) {
			$keys[ (string) $row['meta_key'] ] = true;
		}
	}

	$wpdb->query( 'START TRANSACTION' );
	if ( $keys ) {
		$meta_keys    = array_keys( $keys );
		$placeholders = implode( ', ', array_fill( 0, count( $meta_keys ), '%s' ) );
		$deleted      = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key IN ({$placeholders})",
				array_merge( array( (int) $post_id ), $meta_keys )
			)
		);
		if ( false === $deleted ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}
	}

	foreach ( array_chunk( $snapshot, 100 ) as $chunk ) {
		$values = array();
		$args   = array();
		foreach ( $chunk as $row ) {
			$values[] = '(%d, %s, %s)';
			$args[]   = (int) $post_id;
			$args[]   = (string) $row['meta_key'];
			$args[]   = (string) $row['meta_value'];
		}
		$inserted = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES " . implode( ', ', $values ),
				$args
			)
		);
		if ( false === $inserted ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}
	}

	$wpdb->query( 'COMMIT' );
	clean_post_cache( (int) $post_id );
	return true;
}

/** Restore translated ACF data after every English/WPML save callback. */
function estecapelli_restore_translations_after_source_acf_save() {
	$snapshots = $GLOBALS['estecapelli_source_translation_snapshots'] ?? null;
	unset( $GLOBALS['estecapelli_source_translation_snapshots'] );
	if ( ! is_array( $snapshots ) ) {
		return;
	}

	foreach ( $snapshots as $translation_id => $snapshot ) {
		estecapelli_restore_raw_acf_meta_snapshot( (int) $translation_id, (array) $snapshot );
	}
}
add_action( 'shutdown', 'estecapelli_restore_translations_after_source_acf_save', PHP_INT_MAX );
