<?php
/**
 * Add ACFML preferences to PHP-registered ACF fields.
 *
 * ACFML synchronises local PHP fields from the arrays passed to
 * acf_add_local_field_group(). The preferences therefore need to be present at
 * registration time; adding them later with acf/load_field is too late for the
 * local-field sync tool.
 *
 * The site is translated with the native WordPress/ACF editor. Its ACFML
 * preferences therefore separate shared assets from editable language content:
 *
 *   - Flexible Content, Repeater and Group containers are copied once, so a
 *     translation can be edited without its rows being restored from English.
 *   - Visitor-facing Text, Textarea and WYSIWYG values are translated.
 *   - Media and technical identifiers stay copied from the source language.
 *
 * `wpml_cf_preferences` uses WPML's numeric values: 1 = Copy, 2 = Translate,
 * 3 = Copy Once. ACF safely ignores this property when ACFML is not active.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * ACFML enables repeater/Flexible Content row synchronisation by default. That
 * behaviour is useful when every language must have identical rows, but this
 * site's importers intentionally write complete per-language page builders.
 * Leaving sync enabled makes a later native-editor Save restore the old source
 * rows over the freshly imported translation.
 */
if ( ! defined( 'ACFML_REPEATER_SYNC_DEFAULT' ) ) {
	define( 'ACFML_REPEATER_SYNC_DEFAULT', false );
}

/**
 * Keep ACFML's per-post row-position synchronisation disabled.
 *
 * Copy fields still propagate normally. Only the dangerous operation that
 * inserts, removes or shifts repeater/Flexible Content positions is blocked;
 * those structural changes are applied deliberately through the importers.
 * Returning an empty map also overrides a stale `true` choice already stored
 * by ACFML for an existing post.
 *
 * @return array
 */
function estecapelli_acfml_disable_repeater_position_sync() {
	return array();
}
add_filter( 'pre_option_acfml_synchronise_repeater_fields', 'estecapelli_acfml_disable_repeater_position_sync', PHP_INT_MAX );

/** Prevent ACFML from persisting row-position sync as enabled again. */
function estecapelli_acfml_reject_repeater_position_sync_update() {
	return array();
}
add_filter( 'pre_update_option_acfml_synchronise_repeater_fields', 'estecapelli_acfml_reject_repeater_position_sync_update', PHP_INT_MAX );

/**
 * Attach ACFML preferences to every field in a local field group.
 *
 * @param array $group ACF local field-group definition.
 * @return array
 */
function estecapelli_acfml_prepare_field_group( $group ) {
	/* Expert mode respects the explicit per-field preferences below. */
	if ( ! isset( $group['acfml_field_group_mode'] ) ) {
		$group['acfml_field_group_mode'] = 'advanced';
	}

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
 * Container fields use Copy Once: the source structure seeds a new translation,
 * then its imported rows remain independent. Other non-text values stay copied
 * so media IDs, choices and relationships remain identical in every language.
 *
 * @param array $field ACF field definition.
 * @return int 1 (Copy), 2 (Translate) or 3 (Copy Once).
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
	if ( in_array( $type, array( 'flexible_content', 'repeater', 'group', 'clone', 'accordion', 'tab' ), true ) ) {
		return 3;
	}

	if ( in_array( $type, array( 'text', 'textarea', 'wysiwyg' ), true ) ) {
		return 2;
	}

	return 1;
}

/**
 * Remove ACFML's old per-post repeater-sync choices once.
 *
 * The constant above changes the default for new posts. Existing posts can
 * still have the former `true` value persisted in this ACFML option, so they
 * need a one-time reset before the new default can take effect.
 *
 * @return void
 */
function estecapelli_acfml_disable_existing_repeater_sync() {
	$migration = 'estecapelli_acfml_repeater_sync_disabled_v1';
	if ( '1' === get_option( $migration, '' ) ) {
		return;
	}

	delete_option( 'acfml_synchronise_repeater_fields' );
	update_option( $migration, '1', false );
}
if ( estecapelli_content_mutations_enabled() ) {
	add_action( 'admin_init', 'estecapelli_acfml_disable_existing_repeater_sync', 0 );
}
