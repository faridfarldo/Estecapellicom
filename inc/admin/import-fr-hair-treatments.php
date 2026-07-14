<?php
/**
 * One-time French importer for the eight Hair Transplant treatments.
 *
 * The translated copy lives in version-controlled JSON overlays. The English
 * treatment seed remains the source of truth for ACF structure and media, while
 * only visitor-facing copy is replaced. Existing WPML translations are updated
 * in place; missing ones are created and linked to their English originals.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_FR_HAIR_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_FR_HAIR_IMPORT_VERSION', '2026-07-14.5' );
}

/**
 * The complete and deliberately narrow import manifest.
 *
 * @return array<string,string> English source slug => required French slug.
 */
function estecapelli_fr_hair_manifest() {
	return array(
		'exosome-fue-hair-transplant' => 'greffe-capillaire-exosome-fue',
		'female-hair-transplant'      => 'greffe-de-cheveux-feminine',
		'hair-mesotherapy'            => 'mesotherapie-capillaire',
		'sapphire-fue-hair-transplant' => 'greffe-de-cheveux-fue-sapphire',
		'dhi-hair-transplant'         => 'greffe-de-cheveux-dhi',
		'vita-treatment'              => 'traitement-vita',
		'eyebrow-transplant'          => 'transplantation-de-sourcils',
		'beard-transplant'            => 'transplantation-de-barbe',
	);
}

/**
 * Ensure every non-empty visitor-facing seed value has a French counterpart.
 *
 * These names correspond to text, textarea and WYSIWYG ACF fields. Structural
 * selectors, media, URLs and relationship values intentionally stay in English
 * source data and are copied by ACFML.
 *
 * @param array  $source      English seed value.
 * @param array  $translation French overlay value.
 * @param string $path        Diagnostic path.
 * @return true|WP_Error
 */
function estecapelli_fr_hair_validate_coverage( array $source, array $translation, $path = 'page_sections' ) {
	$text_fields = array(
		'eyebrow',
		'title',
		'lead',
		'body',
		'footer',
		'label',
		'value',
		'question',
		'answer',
		'submit_label',
		'caption',
		'time',
		'position',
		'role',
		'name',
	);

	foreach ( $source as $key => $value ) {
		$current_path = $path . '/' . $key;

		if ( is_array( $value ) ) {
			$translated_value = isset( $translation[ $key ] ) && is_array( $translation[ $key ] ) ? $translation[ $key ] : array();
			$result           = estecapelli_fr_hair_validate_coverage( $value, $translated_value, $current_path );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			continue;
		}

		if ( 'acf_fc_layout' === $key ) {
			if ( ! isset( $translation[ $key ] ) || $translation[ $key ] !== $value ) {
				return new WP_Error( 'fr_hair_layout_missing', sprintf( 'Missing or mismatched ACF layout at %s.', $path ) );
			}
			continue;
		}

		if ( in_array( $key, $text_fields, true ) && '' !== trim( wp_strip_all_tags( (string) $value ) ) ) {
			if ( ! array_key_exists( $key, $translation ) || '' === trim( wp_strip_all_tags( (string) $translation[ $key ] ) ) ) {
				return new WP_Error( 'fr_hair_copy_missing', sprintf( 'French copy is missing at %s.', $current_path ) );
			}
		}
	}

	return true;
}

/**
 * Load and validate all eight French translation overlays.
 *
 * @return array<int,array<string,mixed>>|WP_Error
 */
function estecapelli_fr_hair_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/fr/hair-transplant';
	$loaded    = array();

	foreach ( estecapelli_fr_hair_manifest() as $source_slug => $french_slug ) {
		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'fr_hair_missing_file', sprintf( 'Missing French translation file: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'fr_hair_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}

		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$french_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'fr_hair_invalid_translation', sprintf( 'Incomplete or mismatched French translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_fr_hair_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		$coverage = estecapelli_fr_hair_validate_coverage( $seed['sections'], $translation['sections'] );
		if ( is_wp_error( $coverage ) ) {
			return new WP_Error( $coverage->get_error_code(), sprintf( '%s: %s', basename( $file ), $coverage->get_error_message() ) );
		}
		$structure = estecapelli_fr_hair_overlay( $seed['sections'], $translation['sections'] );
		if ( is_wp_error( $structure ) ) {
			return new WP_Error( $structure->get_error_code(), sprintf( '%s: %s', basename( $file ), $structure->get_error_message() ) );
		}

		$loaded[] = $translation;
	}

	return $loaded;
}

/**
 * Return one English seed record, restricted to the Hair Transplant category.
 *
 * @param string $source_slug English treatment slug.
 * @return array<string,mixed>|WP_Error
 */
function estecapelli_fr_hair_source_seed( $source_slug ) {
	foreach ( estecapelli_treatments_seed() as $treatment ) {
		if ( $source_slug === ( $treatment['slug'] ?? '' ) ) {
			if ( 'Hair Transplant' !== ( $treatment['category'] ?? '' ) ) {
				return new WP_Error( 'fr_hair_wrong_category', sprintf( '%s is not in the Hair Transplant category.', $source_slug ) );
			}
			return $treatment;
		}
	}

	return new WP_Error( 'fr_hair_missing_source_seed', sprintf( 'English seed not found for %s.', $source_slug ) );
}

/**
 * Overlay translated leaves without changing ACF structure or media values.
 *
 * @param array  $base    English ACF value.
 * @param array  $overlay French translated leaves.
 * @param string $path    Diagnostic path.
 * @return array|WP_Error
 */
function estecapelli_fr_hair_overlay( array $base, array $overlay, $path = 'page_sections' ) {
	foreach ( $overlay as $key => $value ) {
		$current_path = $path . '/' . $key;

		if ( ! array_key_exists( $key, $base ) ) {
			return new WP_Error( 'fr_hair_unknown_field', sprintf( 'Translation field does not exist in English data: %s', $current_path ) );
		}

		if ( 'acf_fc_layout' === $key && $base[ $key ] !== $value ) {
			return new WP_Error( 'fr_hair_layout_mismatch', sprintf( 'ACF layout mismatch at %s.', $path ) );
		}

		if ( is_array( $value ) ) {
			if ( ! is_array( $base[ $key ] ) ) {
				return new WP_Error( 'fr_hair_type_mismatch', sprintf( 'Translation structure mismatch at %s.', $current_path ) );
			}
			$merged = estecapelli_fr_hair_overlay( $base[ $key ], $value, $current_path );
			if ( is_wp_error( $merged ) ) {
				return $merged;
			}
			$base[ $key ] = $merged;
		} else {
			if ( is_array( $base[ $key ] ) ) {
				return new WP_Error( 'fr_hair_type_mismatch', sprintf( 'Translation value mismatch at %s.', $current_path ) );
			}
			$base[ $key ] = $value;
		}
	}

	return $base;
}

/**
 * Point seeded internal English links at the French language directory.
 *
 * @param mixed $value ACF value.
 * @return mixed
 */
function estecapelli_fr_hair_localize_urls( $value ) {
	if ( ! is_array( $value ) ) {
		return $value;
	}

	$home = untrailingslashit( (string) get_option( 'home' ) );
	foreach ( $value as $key => $item ) {
		if ( 'url' === $key && is_string( $item ) ) {
			if ( $home && 0 === strpos( $item, $home . '/' ) ) {
				$relative = substr( $item, strlen( $home ) );
				$original = $relative;
				do {
					$previous = $relative;
					$relative = preg_replace( '#^/(?:en|fr)(?=/|$)#', '', $relative );
				} while ( $relative !== $previous );
				if ( $relative !== $original ) {
					if ( '/before-after' === untrailingslashit( $relative ) ) {
						$relative = '/avant-apres';
					}
					$value[ $key ] = $home . '/fr' . ( $relative ?: '/' );
				}
			} elseif ( 0 === strpos( $item, '/en/' ) ) {
				$relative      = '/' . substr( $item, 4 );
				$relative      = '/before-after' === untrailingslashit( $relative ) ? '/avant-apres' : $relative;
				$value[ $key ] = '/fr' . $relative;
			}
		} elseif ( is_array( $item ) ) {
			$value[ $key ] = estecapelli_fr_hair_localize_urls( $item );
		}
	}

	return $value;
}

/**
 * Reduce formatted ACF image arrays to attachment IDs before update_field().
 *
 * @param mixed $value ACF value.
 * @return mixed
 */
function estecapelli_fr_hair_normalize_media( $value ) {
	if ( ! is_array( $value ) ) {
		return $value;
	}

	if ( isset( $value['url'] ) && ( isset( $value['ID'] ) || isset( $value['id'] ) ) ) {
		return (int) ( $value['ID'] ?? $value['id'] );
	}

	foreach ( $value as $key => $item ) {
		$value[ $key ] = estecapelli_fr_hair_normalize_media( $item );
	}

	return $value;
}

/**
 * Verify that every non-empty source media value survived the ACF write.
 *
 * Attachment IDs can be mapped to language-specific attachment records by
 * WPML, so this verifies presence and shape rather than requiring the saved
 * numeric ID to be identical to the English one.
 *
 * @param mixed  $expected Media-bearing ACF value sent to update_field().
 * @param mixed  $saved    Formatted ACF value read back from the French post.
 * @param string $path     Diagnostic path.
 * @return true|WP_Error
 */
function estecapelli_fr_hair_validate_media( $expected, $saved, $path = 'page_sections' ) {
	$media_keys = array( 'image', 'icon_file', 'image_url', 'video_id', 'video_url', 'media_type', 'slider_images' );

	if ( ! is_array( $expected ) ) {
		return true;
	}

	foreach ( $expected as $key => $value ) {
		$current_path = $path . '/' . $key;
		if ( in_array( (string) $key, $media_keys, true ) ) {
			$saved_media = is_array( $saved ) && array_key_exists( $key, $saved ) ? $saved[ $key ] : null;
			if ( ! empty( $value ) && empty( $saved_media ) ) {
				return new WP_Error( 'fr_hair_media_not_saved', sprintf( 'Media was not saved at %s.', $current_path ) );
			}
			if ( 'slider_images' === $key && is_array( $value ) && is_array( $saved_media ) && count( $saved_media ) < count( $value ) ) {
				return new WP_Error( 'fr_hair_media_not_saved', sprintf( 'Not all slider images were saved at %s.', $current_path ) );
			}
			continue;
		}

		if ( is_array( $value ) ) {
			$saved_value = isset( $saved[ $key ] ) && is_array( $saved[ $key ] ) ? $saved[ $key ] : array();
			$result      = estecapelli_fr_hair_validate_media( $value, $saved_value, $current_path );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}
	}

	return true;
}

/**
 * Read a property from either WPML's object or array response.
 *
 * @param mixed  $details WPML language details.
 * @param string $key     Property name.
 * @return mixed|null
 */
function estecapelli_fr_hair_detail( $details, $key ) {
	if ( is_object( $details ) && isset( $details->{$key} ) ) {
		return $details->{$key};
	}
	if ( is_array( $details ) && isset( $details[ $key ] ) ) {
		return $details[ $key ];
	}
	return null;
}

/**
 * Find an untranslated post by its exact slug without WPML query filtering.
 *
 * @param string $slug       French post slug.
 * @param int    $exclude_id Post ID that must never be reused.
 * @return int
 */
function estecapelli_fr_hair_raw_post_id( $slug, $exclude_id = 0 ) {
	global $wpdb;
	if ( $exclude_id ) {
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				 WHERE post_name = %s AND post_type = 'treatment' AND post_status <> 'trash' AND ID <> %d
				 ORDER BY ID ASC LIMIT 1",
				$slug,
				(int) $exclude_id
			)
		);
	}
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = 'treatment' AND post_status <> 'trash'
			 ORDER BY ID ASC LIMIT 1",
			$slug
		)
	);
}

/**
 * Find every taxonomy term using a slug without WPML language filtering.
 *
 * @param string $slug     Term slug.
 * @param string $taxonomy Taxonomy name.
 * @return int[]
 */
function estecapelli_fr_hair_term_ids_by_slug( $slug, $taxonomy ) {
	global $wpdb;
	$term_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT t.term_id FROM {$wpdb->terms} t
			 INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id
			 WHERE t.slug = %s AND tt.taxonomy = %s ORDER BY t.term_id ASC",
			$slug,
			$taxonomy
		)
	);
	return array_map( 'intval', $term_ids );
}

/**
 * Create or repair a linked French treatment category.
 *
 * @param array<string,string> $settings Optional category contract.
 * @return int|WP_Error French term ID.
 */
function estecapelli_fr_hair_category( array $settings = array() ) {
	/*
	 * WPML normally adjusts term IDs to the admin's current language. That is
	 * useful in templates but unsafe while repairing an explicit French term:
	 * wp_update_term() can otherwise compare/update a different translated ID.
	 */
	add_filter( 'wpml_disable_term_adjust_id', '__return_true' );
	try {
		return estecapelli_fr_hair_category_unadjusted( $settings );
	} finally {
		remove_filter( 'wpml_disable_term_adjust_id', '__return_true' );
	}
}

/**
 * Internal category repair with WPML's automatic term-ID adjustment disabled.
 *
 * @param array<string,string> $settings Optional category contract.
 * @return int|WP_Error French term ID.
 */
function estecapelli_fr_hair_category_unadjusted( array $settings = array() ) {
	$settings = array_merge(
		array(
			'source_slug' => 'hair-transplant',
			'target_slug' => 'greffe-de-cheveux',
			'target_name' => 'Greffe de cheveux',
			'label'       => 'Hair Transplant',
		),
		$settings
	);
	$source_slug = $settings['source_slug'];
	$target_slug = $settings['target_slug'];
	$target_name = $settings['target_name'];
	$label       = $settings['label'];
	$taxonomy       = 'treatment_category';
	$element_type   = apply_filters( 'wpml_element_type', $taxonomy );
	$source_term_id = estecapelli_source_term_id( $source_slug, $taxonomy );

	if ( ! $source_term_id ) {
		return new WP_Error( 'fr_hair_missing_source_term', sprintf( 'English %s category was not found.', $label ) );
	}

	$source_term = get_term( $source_term_id, $taxonomy );
	if ( ! $source_term || is_wp_error( $source_term ) ) {
		return new WP_Error( 'fr_hair_invalid_source_term', sprintf( 'English %s category could not be loaded.', $label ) );
	}

	$source_details = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => (int) $source_term->term_taxonomy_id,
			'element_type' => $taxonomy,
		)
	);
	$trid            = (int) estecapelli_fr_hair_detail( $source_details, 'trid' );
	$source_language = (string) estecapelli_fr_hair_detail( $source_details, 'language_code' );
	if ( ! $trid || 'en' !== $source_language ) {
		return new WP_Error( 'fr_hair_unlinked_source_term', sprintf( 'WPML language details are missing for the English %s category.', $label ) );
	}

	$linked_term_id     = (int) apply_filters( 'wpml_object_id', $source_term_id, $taxonomy, false, 'fr' );
	$canonical_term_ids = estecapelli_fr_hair_term_ids_by_slug( $target_slug, $taxonomy );
	$canonical_term_id  = 0;

	// Prefer the term WPML already links when it owns the canonical slug.
	if ( $linked_term_id && in_array( $linked_term_id, $canonical_term_ids, true ) ) {
		$canonical_term_id = $linked_term_id;
	} else {
		// Duplicate slugs can exist after an interrupted WPML import. Prefer the
		// candidate WPML already identifies as French rather than an arbitrary ID.
		foreach ( $canonical_term_ids as $candidate_id ) {
			$candidate = get_term( $candidate_id, $taxonomy );
			if ( ! $candidate || is_wp_error( $candidate ) ) {
				continue;
			}
			$candidate_details = apply_filters(
				'wpml_element_language_details',
				null,
				array(
					'element_id'   => (int) $candidate->term_taxonomy_id,
					'element_type' => $taxonomy,
				)
			);
			if ( 'fr' === (string) estecapelli_fr_hair_detail( $candidate_details, 'language_code' ) ) {
				$canonical_term_id = $candidate_id;
				break;
			}
		}
	}
	if ( ! $canonical_term_id && $canonical_term_ids ) {
		$canonical_term_id = reset( $canonical_term_ids );
	}
	$target_term_id = $canonical_term_id ?: $linked_term_id;

	/*
	 * Some installations already contain the canonical French term, while WPML
	 * points the English source at a second auto-created French term. Preserve
	 * both terms, detach only the incorrect translation relationship, and reuse
	 * the term that already owns the live canonical slug.
	 */
	if ( $canonical_term_id && $linked_term_id && $canonical_term_id !== $linked_term_id ) {
		$linked_term = get_term( $linked_term_id, $taxonomy );
		if ( ! $linked_term || is_wp_error( $linked_term ) ) {
			return new WP_Error( 'fr_hair_invalid_linked_term', sprintf( 'The duplicate French %s category could not be loaded.', $label ) );
		}

		do_action(
			'wpml_set_element_language_details',
			array(
				'element_id'           => (int) $linked_term->term_taxonomy_id,
				'element_type'         => $element_type,
				'trid'                 => false,
				'language_code'        => 'fr',
				'source_language_code' => null,
				'check_duplicates'      => false,
			)
		);
	}

	if ( ! $target_term_id ) {
		$created = wp_insert_term(
			$target_name,
			$taxonomy,
			array( 'slug' => $target_slug )
		);
		if ( is_wp_error( $created ) ) {
			return $created;
		}
		$target_term_id = (int) $created['term_id'];
		$canonical_term_ids[] = $target_term_id;
	}

	$target_term = get_term( $target_term_id, $taxonomy );
	if ( ! $target_term || is_wp_error( $target_term ) ) {
		return new WP_Error( 'fr_hair_invalid_target_term', sprintf( 'French %s category could not be loaded.', $label ) );
	}

	$target_details  = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => (int) $target_term->term_taxonomy_id,
			'element_type' => $taxonomy,
		)
	);
	$target_language = (string) estecapelli_fr_hair_detail( $target_details, 'language_code' );
	if ( $target_language && 'fr' !== $target_language ) {
		// The indexed slug is authoritative. Detach a stale language assignment
		// before linking this canonical term to the English source below.
		do_action(
			'wpml_set_element_language_details',
			array(
				'element_id'           => (int) $target_term->term_taxonomy_id,
				'element_type'         => $element_type,
				'trid'                 => false,
				'language_code'        => 'fr',
				'source_language_code' => null,
				'check_duplicates'      => false,
			)
		);
	}

	if ( in_array( $target_term_id, $canonical_term_ids, true ) ) {
		/*
		 * Do not ask wp_update_term() to re-apply an already-canonical slug. If
		 * WPML left duplicate rows with that slug, core correctly rejects the
		 * update. Updating the display name directly is safe and leaves both the
		 * term identity and URL untouched while we repair the WPML relationship.
		 */
		global $wpdb;
		$name_updated = $wpdb->update(
			$wpdb->terms,
			array( 'name' => $target_name ),
			array( 'term_id' => $target_term_id ),
			array( '%s' ),
			array( '%d' )
		);
		if ( false === $name_updated ) {
			return new WP_Error( 'fr_hair_term_name_update_failed', sprintf( 'The French %s category name could not be updated.', $label ) );
		}
		clean_term_cache( $target_term_id, $taxonomy );
	} else {
		$updated = wp_update_term(
			$target_term_id,
			$taxonomy,
			array(
				'name' => $target_name,
				'slug' => $target_slug,
			)
		);
		if ( is_wp_error( $updated ) ) {
			return $updated;
		}
	}

	// Reload after wp_update_term() in case WordPress refreshed the term object.
	$target_term = get_term( $target_term_id, $taxonomy );
	if ( ! $target_term || is_wp_error( $target_term ) ) {
		return new WP_Error( 'fr_hair_invalid_target_term', sprintf( 'French %s category could not be loaded.', $label ) );
	}

	do_action(
		'wpml_set_element_language_details',
		array(
			'element_id'           => (int) $target_term->term_taxonomy_id,
			'element_type'         => $element_type,
			'trid'                 => $trid,
			'language_code'        => 'fr',
			'source_language_code' => $source_language,
			'check_duplicates'      => false,
		)
	);

	$verified_term_id = (int) apply_filters( 'wpml_object_id', $source_term_id, $taxonomy, false, 'fr' );
	if (
		$target_term_id !== $verified_term_id &&
		! estecapelli_wpml_element_matches_raw( $target_term->term_taxonomy_id, $element_type, $trid, 'fr' )
	) {
		return new WP_Error( 'fr_hair_term_link_failed', sprintf( 'WPML did not link the French %s category to its English source.', $label ) );
	}

	return $target_term_id;
}

/**
 * Create or update one French treatment and its ACF translation.
 *
 * @param array<string,mixed> $translation Translation overlay.
 * @param int                 $french_term_id French category term ID.
 * @param callable            $seed_loader English source-seed resolver.
 * @param bool                $force_relationship Replace a conflicting WPML slot directly.
 * @return int|WP_Error French post ID.
 */
function estecapelli_fr_hair_import_one( array $translation, $french_term_id, $seed_loader = 'estecapelli_fr_hair_source_seed', $force_relationship = false ) {
	$source_slug = $translation['source_slug'];
	if ( ! is_callable( $seed_loader ) ) {
		return new WP_Error( 'fr_hair_invalid_seed_loader', 'The French treatment source-seed resolver is invalid.' );
	}
	$seed = call_user_func( $seed_loader, $source_slug );
	if ( is_wp_error( $seed ) ) {
		return $seed;
	}

	$source_id = estecapelli_source_post_id( $source_slug, 'treatment' );
	if ( ! $source_id ) {
		return new WP_Error( 'fr_hair_missing_source_post', sprintf( 'Published English treatment not found: %s.', $source_slug ) );
	}

	$source_post = get_post( $source_id );
	if ( ! $source_post ) {
		return new WP_Error( 'fr_hair_invalid_source_post', sprintf( 'English treatment could not be loaded: %s.', $source_slug ) );
	}

	$element_type   = apply_filters( 'wpml_element_type', 'treatment' );
	$source_details = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => $source_id,
			'element_type' => 'treatment',
		)
	);
	$trid           = (int) estecapelli_fr_hair_detail( $source_details, 'trid' );
	$source_language = (string) estecapelli_fr_hair_detail( $source_details, 'language_code' );
	if ( ! $trid || 'en' !== $source_language ) {
		return new WP_Error( 'fr_hair_unlinked_source_post', sprintf( 'WPML language details are missing for %s.', $source_slug ) );
	}

	// Prefer the relationship that actually occupies the French slot in the
	// source TRID. A forced repair first prefers the exact indexed slug so a
	// stale slot occupant cannot cause a different French post to be overwritten.
	$target_id = $force_relationship ? estecapelli_fr_hair_raw_post_id( $translation['slug'], $source_id ) : 0;
	if ( ! $target_id ) {
		$target_id = estecapelli_wpml_group_element_id_raw( $trid, $element_type, 'fr' );
	}
	if ( $target_id ) {
		$raw_target = get_post( $target_id );
		if ( ! $raw_target || 'treatment' !== $raw_target->post_type || 'trash' === $raw_target->post_status || $target_id === $source_id ) {
			estecapelli_wpml_delete_relationship_raw( $target_id, $element_type, $trid, 'fr' );
			$target_id = 0;
		}
	}
	if ( ! $target_id ) {
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, 'fr' );
	}
	if ( $target_id === $source_id ) {
		$target_id = 0;
	}
	if ( ! $target_id ) {
		$target_id = estecapelli_fr_hair_raw_post_id( $translation['slug'], $source_id );
	}

	/*
	 * WPML duplicates deliberately mirror their source post. Existing French
	 * records may therefore accept our update and then immediately revert to
	 * English. Removing this one marker is WPML's "Translate independently"
	 * operation; the normal trid relationship remains intact.
	 */
	if ( $target_id ) {
		delete_post_meta( $target_id, '_icl_lang_duplicate_of' );
	}

	$post_args = array(
		'post_type'    => 'treatment',
		'post_title'   => $translation['title'],
		'post_name'    => $translation['slug'],
		'post_status'  => 'publish',
		'post_content' => '',
		'menu_order'   => (int) $source_post->menu_order,
	);

	if ( $target_id ) {
		$post_args['ID'] = $target_id;
		$target_id       = wp_update_post( $post_args, true );
	} else {
		$target_id = wp_insert_post( $post_args, true );
	}
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}

	do_action(
		'wpml_set_element_language_details',
		array(
			'element_id'           => (int) $target_id,
			'element_type'         => $element_type,
			'trid'                 => $trid,
			'language_code'        => 'fr',
			'source_language_code' => $source_language,
			'check_duplicates'      => false,
		)
	);
	delete_post_meta( $target_id, '_icl_lang_duplicate_of' );

	if ( $force_relationship ) {
		$forced = estecapelli_wpml_replace_language_slot_raw( $target_id, $element_type, $trid, 'fr', $source_language );
		if ( ! $forced ) {
			return new WP_Error( 'fr_hair_post_force_link_failed', sprintf( 'The French WPML relationship could not be rebuilt for %s.', $source_slug ) );
		}
	}

	$linked_target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, 'fr' );
	if ( (int) $target_id !== $linked_target_id && ! estecapelli_wpml_element_matches_raw( $target_id, $element_type, $trid, 'fr' ) ) {
		$repaired = estecapelli_wpml_repair_relationship_raw( $target_id, $element_type, $trid, 'fr', $source_language );
		if ( ! $repaired ) {
			return new WP_Error( 'fr_hair_post_link_failed', sprintf( 'WPML did not link the French translation for %s.', $source_slug ) );
		}
	}

	// Re-apply the canonical French slug after WPML has linked the translation.
	$target_id = wp_update_post(
		array(
			'ID'         => (int) $target_id,
			'post_title' => $translation['title'],
			'post_name'  => $translation['slug'],
		),
		true
	);
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}
	$target_post = get_post( $target_id );
	if ( ! $target_post || $translation['slug'] !== $target_post->post_name ) {
		return new WP_Error( 'fr_hair_slug_conflict', sprintf( 'The required French slug is already in use: %s.', $translation['slug'] ) );
	}
	if ( $translation['title'] !== $target_post->post_title ) {
		return new WP_Error( 'fr_hair_title_not_saved', sprintf( 'The French title was not saved for %s.', $source_slug ) );
	}

	// The target term ID is already French; prevent WPML from adjusting it back
	// to the English term while the admin request is running in English.
	add_filter( 'wpml_disable_term_adjust_id', '__return_true' );
	$term_result = wp_set_object_terms( $target_id, array( (int) $french_term_id ), 'treatment_category', false );
	remove_filter( 'wpml_disable_term_adjust_id', '__return_true' );
	if ( is_wp_error( $term_result ) ) {
		return $term_result;
	}

	$sections = estecapelli_merge_preserve_media( $seed['sections'], $source_id );
	$sections = estecapelli_fr_hair_overlay( $sections, $translation['sections'] );
	if ( is_wp_error( $sections ) ) {
		return $sections;
	}
	$sections = estecapelli_fr_hair_localize_urls( $sections );
	$sections = estecapelli_fr_hair_normalize_media( $sections );
	update_field( 'field_treatment_sections', $sections, $target_id );

	// Do not mark the migration complete unless ACF actually returns the French
	// copy. This catches duplicate-sync or ACFML preference problems immediately.
	$saved_sections = get_field( 'page_sections', $target_id );
	$saved_title    = is_array( $saved_sections ) ? ( $saved_sections[0]['title'] ?? '' ) : '';
	$expected_title = $translation['sections'][0]['title'] ?? '';
	if ( ! is_array( $saved_sections ) || ! $expected_title || $expected_title !== $saved_title ) {
		return new WP_Error( 'fr_hair_acf_not_saved', sprintf( 'The French ACF content was not saved for %s.', $source_slug ) );
	}
	$media_saved = estecapelli_fr_hair_validate_media( $sections, $saved_sections );
	if ( is_wp_error( $media_saved ) ) {
		return $media_saved;
	}

	$thumbnail_id = get_post_thumbnail_id( $source_id );
	if ( $thumbnail_id ) {
		set_post_thumbnail( $target_id, $thumbnail_id );
	}

	return (int) $target_id;
}

/**
 * Run the complete French Hair Transplant import.
 *
 * @return array<string,int>|WP_Error Source slug => French post ID.
 */
function estecapelli_run_fr_hair_import() {
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'fr_hair_acf_missing', 'ACF is required for the French Hair Transplant import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'fr_hair_wpml_missing', 'WPML is required for the French Hair Transplant import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['fr'] ) ) {
		return new WP_Error( 'fr_hair_french_inactive', 'French must be active in WPML before importing the treatment translations.' );
	}

	$translations = estecapelli_fr_hair_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$french_term_id = estecapelli_fr_hair_category();
	if ( is_wp_error( $french_term_id ) ) {
		return $french_term_id;
	}

	$imported = array();
	foreach ( $translations as $translation ) {
		$result = estecapelli_fr_hair_import_one( $translation, $french_term_id );
		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				$result->get_error_code(),
				sprintf( '%s: %s', $translation['source_slug'], $result->get_error_message() )
			);
		}
		$imported[ $translation['source_slug'] ] = $result;
	}

	return $imported;
}

add_action( 'admin_init', 'estecapelli_maybe_import_fr_hair_treatments', 80 );
/**
 * Run once after deployment. Failed runs remain retryable on the next admin hit.
 */
function estecapelli_maybe_import_fr_hair_treatments() {
	if (
		get_option( 'estecapelli_fr_hair_import_version' ) === ESTECAPELLI_FR_HAIR_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_fr_hair_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_fr_hair_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_fr_hair_import_version', ESTECAPELLI_FR_HAIR_IMPORT_VERSION, false );
	delete_option( 'estecapelli_fr_hair_import_error' );
	set_transient( 'estecapelli_fr_hair_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_fr_hair_import_notice' );
/**
 * Show an actionable result to administrators after the automatic migration.
 */
function estecapelli_fr_hair_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_fr_hair_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_fr_hair_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'French Hair Transplant translations imported successfully: %d treatments.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_fr_hair_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'French Hair Transplant import could not finish.' ),
			esc_html( $error )
		);
	}
}
