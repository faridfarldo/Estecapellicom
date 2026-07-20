<?php
/**
 * Add ACFML preferences to PHP-registered ACF fields.
 *
 * ACFML synchronises local PHP fields from the arrays passed to
 * acf_add_local_field_group(). The preferences therefore need to be present at
 * registration time; adding them later with acf/load_field is too late for the
 * local-field sync tool.
 *
 * Every ACF value here is written per language by the version-controlled
 * importers, so WPML/ACFML is told to leave all of them alone: each field is
 * registered with preference 0 ("Don't translate"). That keeps translated pages
 * as plain per-post meta the native editor can Save without WPML re-syncing them
 * from the original (which used to revert imported translations on Save).
 *
 * `wpml_cf_preferences` uses WPML's numeric values: 0 = Don't translate,
 * 1 = Copy, 2 = Translate. ACF safely ignores this property when ACFML is off.
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
	/*
	 * 0 = "Don't translate": WPML/ACFML ignores the field completely.
	 *
	 * Every ACF value on this site — page sections, doctor and treatment fields —
	 * is written per language by the version-controlled importers, and the
	 * Flexible Content structure is forced from the source post at read time by
	 * inc/acfml-layout-guard.php. WPML therefore has no useful job on these
	 * fields. When it DID manage them (Copy for structure, Translate for text) it
	 * re-synced each translation from the English original whenever the page was
	 * saved in the editor, silently reverting the imported translation — so an
	 * editor could never open a translated page, tweak a word or image and Save.
	 *
	 * Handing every field back to plain per-post meta makes a translated page
	 * behave like a normal page: the importer fills it, and manual edits Save and
	 * persist. The post-level translation relationship (URLs, language switcher)
	 * is unaffected — that is governed by WPML's post translation, not by these
	 * custom-field preferences.
	 */
	unset( $field );
	return 0;
}
