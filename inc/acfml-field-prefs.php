<?php
/**
 * Tell ACFML which ACF fields to translate.
 *
 * Our field groups are registered in PHP (inc/acf-field-groups.php). Without an
 * explicit translation preference, ACFML treats every custom field as "don't
 * translate", so the WPML editor shows "Page Sections 0" — no strings to
 * translate — and translated pages keep the English text.
 *
 * We set the preference per field TYPE at load time, so it applies to every
 * current and future field with no per-field bookkeeping:
 *
 *   - text / textarea / wysiwyg      → Translate (real copy).
 *   - group / repeater / flexible    → Translate, i.e. ACFML recurses into the
 *                                       sub-fields and translates each by its
 *                                       own type. ACFML is structure-aware, so
 *                                       it copies the flexible-content layout
 *                                       selector (hero, stepbook…) rather than
 *                                       translating it. inc/acfml-layout-guard
 *                                       is the belt-and-braces backup.
 *   - image / file / gallery         → Copy (same media in every language).
 *   - url / link                     → Copy (internal links are localised in
 *                                       the theme, not per-field).
 *
 * `wpml_cf_preferences` values are WPML's own constants: 1 = Copy,
 * 2 = Translate, 3 = Copy once.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'acf/load_field', 'estecapelli_acfml_field_pref' );
function estecapelli_acfml_field_pref( $field ) {
	if ( empty( $field['type'] ) || isset( $field['wpml_cf_preferences'] ) ) {
		return $field; // nothing to type, or an explicit choice already set.
	}

	switch ( $field['type'] ) {
		case 'text':
		case 'textarea':
		case 'wysiwyg':
		case 'group':
		case 'repeater':
		case 'flexible_content':
			$field['wpml_cf_preferences'] = 2; // Translate / recurse.
			break;

		case 'image':
		case 'file':
		case 'gallery':
		case 'url':
		case 'link':
			$field['wpml_cf_preferences'] = 1; // Copy.
			break;
	}

	return $field;
}
