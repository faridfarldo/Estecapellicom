<?php
/**
 * WPML: assign the default language to existing custom-post-type content.
 *
 * When WPML is activated, it only shows posts that carry a language record in
 * its translations table. Pages/posts get one automatically, but the theme's
 * custom types (treatment, doctor, result) were created before WPML and have
 * none — so WPML filters them out of every query and their single pages fall
 * back to the homepage.
 *
 * This one-time migration writes the default-language record onto each of those
 * posts via WPML's official API, so they become visible again. It skips any post
 * that already has a language, and runs only once (guarded by an option flag).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_init', 'estecapelli_wpml_assign_cpt_language', 20 );
function estecapelli_wpml_assign_cpt_language() {

	// Only relevant with WPML active.
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
		return;
	}
	if ( get_option( 'estecapelli_wpml_cpt_lang_v1' ) ) {
		return;
	}

	$default = apply_filters( 'wpml_default_language', null );
	if ( ! $default ) {
		return; // WPML not ready yet — try again on the next admin load.
	}

	foreach ( array( 'treatment', 'doctor', 'result' ) as $post_type ) {
		$ids = get_posts(
			array(
				'post_type'        => $post_type,
				'post_status'      => 'any',
				'numberposts'      => -1,
				'fields'           => 'ids',
				'suppress_filters' => true, // bypass WPML's own language filter.
			)
		);

		foreach ( $ids as $post_id ) {
			$current = apply_filters(
				'wpml_element_language_code',
				null,
				array(
					'element_id'   => $post_id,
					'element_type' => 'post_' . $post_type,
				)
			);
			if ( $current ) {
				continue; // already tagged with a language.
			}

			do_action(
				'wpml_set_element_language_details',
				array(
					'element_id'           => $post_id,
					'element_type'         => 'post_' . $post_type,
					'trid'                 => false,
					'language_code'        => $default,
					'source_language_code' => null,
				)
			);
		}
	}

	update_option( 'estecapelli_wpml_cpt_lang_v1', 1 );
}
