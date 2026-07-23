<?php
/**
 * One-time: mirror the complete TrichoLab content onto the canonical Page.
 *
 * TrichoLab exists twice: the intended Page at /en/hair-transplant/tricholab,
 * and an older `treatment` post with the same slug that holds the full,
 * content-complete version (all section images). The Page was seeded with empty
 * image slots, so it looked bare next to the original.
 *
 * Rather than re-key every image by hand, we copy the treatment's ACF
 * `page_sections` (text + image references) onto the Page once. The images are
 * shared media-library attachments, so nothing is duplicated, and the treatment
 * itself is only read — never modified. The bare treatment URL already 301s to
 * the Page (see inc/redirects.php), so after this runs there is one complete
 * TrichoLab page and no duplicate.
 *
 * Runs once on admin load, guarded by an option. Bump the option key to re-run.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( estecapelli_content_mutations_enabled() ) {
	add_action( 'admin_init', 'estecapelli_tricholab_mirror_content' );
}
function estecapelli_tricholab_mirror_content() {
	if ( get_option( 'estecapelli_tricholab_mirror_v1' ) ) {
		return;
	}

	global $wpdb;

	$src = (int) $wpdb->get_var(
		"SELECT ID FROM {$wpdb->posts}
		 WHERE post_name = 'tricholab' AND post_type = 'treatment' AND post_status = 'publish'
		 ORDER BY ID ASC LIMIT 1"
	);
	$dest = (int) $wpdb->get_var(
		"SELECT ID FROM {$wpdb->posts}
		 WHERE post_name = 'tricholab' AND post_type = 'page' AND post_status = 'publish'
		 ORDER BY ID ASC LIMIT 1"
	);

	if ( ! $src || ! $dest || $src === $dest ) {
		return;
	}

	$value_like = $wpdb->esc_like( 'page_sections' ) . '%';   // page_sections, page_sections_0_image, …
	$key_like   = $wpdb->esc_like( '_page_sections' ) . '%';  // ACF field-key references

	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT meta_key, meta_value FROM {$wpdb->postmeta}
			 WHERE post_id = %d AND ( meta_key LIKE %s OR meta_key LIKE %s )",
			$src,
			$value_like,
			$key_like
		)
	);

	if ( empty( $rows ) ) {
		return; // Nothing to copy — leave the Page untouched, try again next load.
	}

	// Replace the Page's section meta with the treatment's complete set.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta}
			 WHERE post_id = %d AND ( meta_key LIKE %s OR meta_key LIKE %s )",
			$dest,
			$value_like,
			$key_like
		)
	);

	foreach ( $rows as $row ) {
		update_post_meta( $dest, $row->meta_key, maybe_unserialize( $row->meta_value ) );
	}

	clean_post_cache( $dest );
	update_option( 'estecapelli_tricholab_mirror_v1', time() );
}
