<?php
/**
 * Add ACFML preferences to PHP-registered ACF fields.
 *
 * ACFML synchronises local PHP fields from the arrays passed to
 * acf_add_local_field_group(). The preferences therefore need to be present at
 * registration time; adding them later with acf/load_field is too late for the
 * local-field sync tool.
 *
 * The site uses the same section structure in every language:
 *
 *   - Flexible Content, Repeater, Group and all non-text fields are copied.
 *   - Visitor-facing Text, Textarea and WYSIWYG values are translated.
 *   - Text fields which contain technical identifiers are explicitly copied.
 *
 * `wpml_cf_preferences` uses WPML's numeric values: 1 = Copy, 2 = Translate.
 * ACF safely ignores this extra field property when ACFML is not active.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attach ACFML preferences to every field in a local field group.
 *
 * @param array $group ACF local field-group definition.
 * @return array
 */
function estecapelli_acfml_prepare_field_group( $group ) {
	if ( ! empty( $group['fields'] ) && is_array( $group['fields'] ) ) {
		$group['fields'] = estecapelli_acfml_prepare_fields( $group['fields'] );
	}

	return $group;
}

/**
 * Recursively attach preferences to fields, layout sub-fields and nested rows.
 *
 * Existing explicit preferences win, so an exceptional field can always be
 * configured directly beside its definition.
 *
 * @param array $fields ACF field definitions.
 * @return array
 */
function estecapelli_acfml_prepare_fields( $fields ) {
	foreach ( $fields as $index => $field ) {
		if ( ! is_array( $field ) ) {
			continue;
		}

		if ( ! isset( $field['wpml_cf_preferences'] ) ) {
			$field['wpml_cf_preferences'] = estecapelli_acfml_preference_for_field( $field );
		}

		if ( ! empty( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
			$field['sub_fields'] = estecapelli_acfml_prepare_fields( $field['sub_fields'] );
		}

		if ( ! empty( $field['layouts'] ) && is_array( $field['layouts'] ) ) {
			foreach ( $field['layouts'] as $layout_key => $layout ) {
				if ( ! empty( $layout['sub_fields'] ) && is_array( $layout['sub_fields'] ) ) {
					$layout['sub_fields'] = estecapelli_acfml_prepare_fields( $layout['sub_fields'] );
				}
				$field['layouts'][ $layout_key ] = $layout;
			}
		}

		$fields[ $index ] = $field;
	}

	return $fields;
}

/**
 * Choose the ACFML preference for one field.
 *
 * Text-like fields are translated by default, except for an explicit list of
 * structural values (asset URLs, video IDs, slugs, codes, names and counts).
 * Every other field type is copied so layout selectors, row counts, media IDs,
 * choices and relationships remain identical in every language.
 *
 * @param array $field ACF field definition.
 * @return int 1 (Copy) or 2 (Translate).
 */
function estecapelli_acfml_preference_for_field( $field ) {
	$copy_text_fields = array(
		'field_hero_image_url',
		'field_hero_video_id',
		'field_cand_image_url',
		'field_gal_grafts',
		'field_team_m_photo_url',
		'field_team_m_name',
		'field_team_lang_country',
		'field_docs_m_name',
		'field_rel_category',
		'field_intro_image_url',
		'field_intro_video_url',
		'field_doctor_resume_photo_url',
	);

	$key = isset( $field['key'] ) ? (string) $field['key'] : '';
	if ( in_array( $key, $copy_text_fields, true ) ) {
		return 1;
	}

	$type = isset( $field['type'] ) ? (string) $field['type'] : '';
	if ( in_array( $type, array( 'text', 'textarea', 'wysiwyg' ), true ) ) {
		return 2;
	}

	return 1;
}
