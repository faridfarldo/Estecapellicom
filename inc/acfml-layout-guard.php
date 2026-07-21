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
		$GLOBALS['estecapelli_source_translation_snapshots'] = array(
			'source_id'    => (int) $post_id,
			'translations' => $snapshots,
		);
	}
}
add_action( 'pre_post_update', 'estecapelli_snapshot_translations_before_source_acf_save', 0, 2 );

/**
 * Map raw ACF value meta keys to their registered field keys.
 *
 * @param array $rows Raw ACF meta snapshot.
 * @return array<string,string> Public meta key => ACF field key.
 */
function estecapelli_acf_meta_reference_map( array $rows ) {
	$references = array();
	foreach ( $rows as $row ) {
		$meta_key   = isset( $row['meta_key'] ) ? (string) $row['meta_key'] : '';
		$field_key  = isset( $row['meta_value'] ) ? (string) $row['meta_value'] : '';
		if ( '_' === substr( $meta_key, 0, 1 ) && 0 === strpos( $field_key, 'field_' ) ) {
			$references[ substr( $meta_key, 1 ) ] = $field_key;
		}
	}
	return $references;
}

/**
 * Whether one registered ACF field is intentionally shared from English.
 *
 * @param string $field_key ACF field key.
 * @return bool
 */
function estecapelli_acf_field_is_shared_copy( $field_key ) {
	static $preferences = array();
	$field_key = (string) $field_key;
	if ( isset( $preferences[ $field_key ] ) ) {
		return 1 === $preferences[ $field_key ];
	}

	$preference = 0;
	$field      = function_exists( 'acf_get_field' ) ? acf_get_field( $field_key ) : null;
	if ( is_array( $field ) ) {
		if ( isset( $field['wpml_cf_preferences'] ) ) {
			$preference = (int) $field['wpml_cf_preferences'];
		} elseif ( function_exists( 'estecapelli_acfml_preference_for_field' ) ) {
			$preference = (int) estecapelli_acfml_preference_for_field( $field );
		}
	}

	$preferences[ $field_key ] = $preference;
	return 1 === $preference;
}

/**
 * Find raw-meta prefixes for a registered ACF repeater field.
 *
 * ACF stores a hidden reference beside the repeater count, for example
 * `_page_sections_1_members` => `field_team_members`. The public part of that
 * meta key is the prefix shared by every descendant row.
 *
 * @param array  $rows      Raw ACF meta snapshot.
 * @param string $field_key Repeater field key.
 * @return array<int,string>
 */
function estecapelli_acf_repeater_prefixes( array $rows, $field_key ) {
	$prefixes = array();
	foreach ( $rows as $row ) {
		$meta_key   = isset( $row['meta_key'] ) ? (string) $row['meta_key'] : '';
		$meta_value = isset( $row['meta_value'] ) ? (string) $row['meta_value'] : '';
		if ( '_' === substr( $meta_key, 0, 1 ) && $field_key === $meta_value ) {
			$prefixes[] = substr( $meta_key, 1 );
		}
	}
	return array_values( array_unique( $prefixes ) );
}

/** Normalize one stable repeater-row identity value. */
function estecapelli_acf_normalize_identity( $value ) {
	$value = trim( wp_strip_all_tags( (string) $value ) );
	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $value, 'UTF-8' ) : strtolower( $value );
}

/**
 * Split one raw repeater subtree into indexed rows and stable identities.
 *
 * @param array  $rows                Raw ACF meta snapshot.
 * @param string $prefix              Public repeater meta prefix.
 * @param array  $identity_field_keys Ordered identity field keys.
 * @return array<int,array{index:int,rows:array,identities:array}>
 */
function estecapelli_acf_identity_repeater_rows( array $rows, $prefix, array $identity_field_keys ) {
	$references = estecapelli_acf_meta_reference_map( $rows );
	$pattern    = '/^' . preg_quote( $prefix, '/' ) . '_(\d+)_/';
	$indexed    = array();

	foreach ( $rows as $row ) {
		$meta_key   = isset( $row['meta_key'] ) ? (string) $row['meta_key'] : '';
		$public_key = '_' === substr( $meta_key, 0, 1 ) ? substr( $meta_key, 1 ) : $meta_key;
		if ( ! preg_match( $pattern, $public_key, $matches ) ) {
			continue;
		}

		$index = (int) $matches[1];
		if ( ! isset( $indexed[ $index ] ) ) {
			$indexed[ $index ] = array(
				'index'      => $index,
				'rows'       => array(),
				'identities' => array(),
			);
		}
		$indexed[ $index ]['rows'][] = $row;

		if ( $meta_key === $public_key && isset( $references[ $public_key ] ) && in_array( $references[ $public_key ], $identity_field_keys, true ) ) {
			$identity = estecapelli_acf_normalize_identity( $row['meta_value'] ?? '' );
			if ( '' !== $identity ) {
				$indexed[ $index ]['identities'][ $references[ $public_key ] ] = $identity;
			}
		}
	}

	ksort( $indexed, SORT_NUMERIC );
	return array_values( $indexed );
}

/** Re-key one raw repeater row from its old index/prefix to a new position. */
function estecapelli_acf_rekey_repeater_row( array $row, $old_prefix, $old_index, $new_prefix, $new_index ) {
	$meta_key = isset( $row['meta_key'] ) ? (string) $row['meta_key'] : '';
	$hidden   = '_' === substr( $meta_key, 0, 1 );
	$public   = $hidden ? substr( $meta_key, 1 ) : $meta_key;
	$old_root = $old_prefix . '_' . (int) $old_index . '_';
	if ( 0 === strpos( $public, $old_root ) ) {
		$public          = $new_prefix . '_' . (int) $new_index . '_' . substr( $public, strlen( $old_root ) );
		$row['meta_key'] = ( $hidden ? '_' : '' ) . $public;
	}
	return $row;
}

/**
 * Align one translated repeater with English by stable row identity.
 *
 * Deleted rows disappear, reordered rows follow English, and every translated
 * child subtree moves with its real person instead of remaining at a numeric
 * position. A brand-new source row is copied as an initial untranslated row.
 *
 * @param array  $translation        Translation's pre-save raw ACF rows.
 * @param array  $source             English post's newly saved raw ACF rows.
 * @param string $translation_prefix Translation repeater prefix.
 * @param string $source_prefix      English repeater prefix.
 * @param array  $identity_fields    Ordered stable identity field keys.
 * @return array
 */
function estecapelli_align_identity_repeater( array $translation, array $source, $translation_prefix, $source_prefix, array $identity_fields ) {
	$translation_rows = estecapelli_acf_identity_repeater_rows( $translation, $translation_prefix, $identity_fields );
	$source_rows      = estecapelli_acf_identity_repeater_rows( $source, $source_prefix, $identity_fields );
	$used             = array();
	$rebuilt          = array();

	foreach ( $source_rows as $new_index => $source_row ) {
		$match_index = null;
		foreach ( $identity_fields as $identity_field ) {
			$source_identity = $source_row['identities'][ $identity_field ] ?? '';
			if ( '' === $source_identity ) {
				continue;
			}
			foreach ( $translation_rows as $candidate_index => $candidate ) {
				if ( isset( $used[ $candidate_index ] ) ) {
					continue;
				}
				if ( $source_identity === ( $candidate['identities'][ $identity_field ] ?? '' ) ) {
					$match_index = $candidate_index;
					break 2;
				}
			}
		}

		$base        = null !== $match_index ? $translation_rows[ $match_index ] : $source_row;
		$base_prefix = null !== $match_index ? $translation_prefix : $source_prefix;
		if ( null !== $match_index ) {
			$used[ $match_index ] = true;
		}
		foreach ( $base['rows'] as $row ) {
			$rebuilt[] = estecapelli_acf_rekey_repeater_row( $row, $base_prefix, $base['index'], $translation_prefix, $new_index );
		}
	}

	$pattern       = '/^_?' . preg_quote( $translation_prefix, '/' ) . '_\d+_/';
	$result        = array();
	$count_updated = false;
	foreach ( $translation as $row ) {
		$meta_key = isset( $row['meta_key'] ) ? (string) $row['meta_key'] : '';
		if ( preg_match( $pattern, $meta_key ) ) {
			continue;
		}
		if ( $translation_prefix === $meta_key ) {
			$row['meta_value'] = (string) count( $source_rows );
			$count_updated     = true;
		}
		$result[] = $row;
	}

	if ( ! $count_updated ) {
		$result[] = array(
			'meta_key'   => $translation_prefix,
			'meta_value' => (string) count( $source_rows ),
		);
	}
	return array_merge( $result, $rebuilt );
}

/**
 * Align repeaters whose translated child data must follow a stable entity.
 *
 * @param array $translation Translation's pre-save raw ACF rows.
 * @param array $source      English post's newly saved raw ACF rows.
 * @return array
 */
function estecapelli_align_identity_repeaters( array $translation, array $source ) {
	$configurations = array(
		'field_team_members' => array( 'field_team_m_photo_url', 'field_team_m_photo', 'field_team_m_name' ),
		'field_team_m_langs' => array( 'field_team_lang_country' ),
		'field_docs_members' => array( 'field_docs_m_photo', 'field_docs_m_name' ),
	);

	foreach ( $configurations as $repeater_field => $identity_fields ) {
		$translation_prefixes = estecapelli_acf_repeater_prefixes( $translation, $repeater_field );
		$source_prefixes      = estecapelli_acf_repeater_prefixes( $source, $repeater_field );
		foreach ( $translation_prefixes as $position => $translation_prefix ) {
			$source_prefix = in_array( $translation_prefix, $source_prefixes, true )
				? $translation_prefix
				: ( $source_prefixes[ $position ] ?? '' );
			if ( '' !== $source_prefix ) {
				$translation = estecapelli_align_identity_repeater( $translation, $source, $translation_prefix, $source_prefix, $identity_fields );
			}
		}
	}

	return $translation;
}

/**
 * Merge only shared Copy values from English into a translation snapshot.
 *
 * The translation owns every Translate and Copy Once value, including all
 * repeater/Flexible Content row counts and order. A source value is accepted
 * only when the same meta position already exists in the translation and its
 * registered ACF field preference is Copy. New/reordered rows therefore wait
 * for an explicit page re-import instead of corrupting translated row content.
 *
 * @param array $translation Translation's pre-save raw ACF rows.
 * @param array $source      English post's newly saved raw ACF rows.
 * @return array Selectively synchronized translation snapshot.
 */
function estecapelli_merge_shared_acf_values( array $translation, array $source ) {
	$references    = estecapelli_acf_meta_reference_map( $translation );
	$source_values = array();
	foreach ( $source as $row ) {
		$meta_key = isset( $row['meta_key'] ) ? (string) $row['meta_key'] : '';
		if ( isset( $references[ $meta_key ] ) && estecapelli_acf_field_is_shared_copy( $references[ $meta_key ] ) ) {
			$source_values[ $meta_key ][] = (string) $row['meta_value'];
		}
	}

	$positions = array();
	foreach ( $translation as $index => $row ) {
		$meta_key = isset( $row['meta_key'] ) ? (string) $row['meta_key'] : '';
		if ( ! isset( $source_values[ $meta_key ] ) || ! isset( $references[ $meta_key ] ) || ! estecapelli_acf_field_is_shared_copy( $references[ $meta_key ] ) ) {
			continue;
		}

		$position = $positions[ $meta_key ] ?? 0;
		if ( isset( $source_values[ $meta_key ][ $position ] ) ) {
			$translation[ $index ]['meta_value'] = $source_values[ $meta_key ][ $position ];
		}
		$positions[ $meta_key ] = $position + 1;
	}

	return $translation;
}

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
	$state = $GLOBALS['estecapelli_source_translation_snapshots'] ?? null;
	unset( $GLOBALS['estecapelli_source_translation_snapshots'] );
	if ( ! is_array( $state ) || empty( $state['source_id'] ) || empty( $state['translations'] ) || ! is_array( $state['translations'] ) ) {
		return;
	}

	$source = estecapelli_raw_acf_meta_snapshot( (int) $state['source_id'] );
	foreach ( $state['translations'] as $translation_id => $snapshot ) {
		$snapshot = estecapelli_align_identity_repeaters( (array) $snapshot, $source );
		$snapshot = estecapelli_merge_shared_acf_values( (array) $snapshot, $source );
		estecapelli_restore_raw_acf_meta_snapshot( (int) $translation_id, $snapshot );
	}
}
add_action( 'shutdown', 'estecapelli_restore_translations_after_source_acf_save', PHP_INT_MAX );
