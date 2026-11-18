<?php
/**
 * Team member swap — replace one consultant card on the live Our Team pages.
 *
 * The team grid renders from each language's page content in the database, so
 * changing the page seed alone never reaches the live site. This finds every
 * team-member row on any page whose name is exactly the old name and writes
 * the new name, clearing the photo (uploaded image and bundled URL) so the card
 * shows the new person's initial until her portrait is added.
 *
 * Only rows matching the old name are touched; everything else on the pages —
 * other members, their languages, the section copy — is left as it is. Runs
 * once per version on the first admin page load, with nothing left to do on
 * any later run, so a re-run is harmless.
 *
 * Name and photo URL are ACFML "Copy" fields, so writing the same values in
 * every language matches what a save of the English page would copy anyway.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_TEAM_SWAP_VERSION' ) ) {
	define( 'ESTECAPELLI_TEAM_SWAP_VERSION', '2026-11-18.1' );
}

/** Old name => new name. Polish consultant Natalia is replaced by Sandra. */
function estecapelli_team_swap_map() {
	return array( 'Natalia' => 'Sandra' );
}

/**
 * Apply the swap. Returns the number of member rows changed.
 */
function estecapelli_run_team_swap() {
	global $wpdb;
	$changed = 0;

	foreach ( estecapelli_team_swap_map() as $old_name => $new_name ) {
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT pm.post_id, pm.meta_key FROM {$wpdb->postmeta} pm
				 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				 WHERE pm.meta_key LIKE %s AND pm.meta_value = %s
				   AND p.post_type = 'page' AND p.post_status <> 'trash'",
				$wpdb->esc_like( 'page_sections_' ) . '%' . $wpdb->esc_like( '_members_' ) . '%' . $wpdb->esc_like( '_name' ),
				$old_name
			)
		);

		foreach ( (array) $rows as $row ) {
			// page_sections_{i}_members_{j}_name — and not a nested languages row.
			if ( ! preg_match( '/^(page_sections_\d+_members_\d+)_name$/', $row->meta_key, $m ) ) {
				continue;
			}
			$prefix  = $m[1];
			$post_id = (int) $row->post_id;

			update_post_meta( $post_id, $prefix . '_name', $new_name );
			update_post_meta( $post_id, $prefix . '_photo_url', '' );
			update_post_meta( $post_id, $prefix . '_photo', '' );
			clean_post_cache( $post_id );
			++$changed;
		}
	}

	return $changed;
}

add_action( 'admin_init', 'estecapelli_maybe_run_team_swap', 97 );
/** Run once per version after deployment. */
function estecapelli_maybe_run_team_swap() {
	if (
		get_option( 'estecapelli_team_swap_version' ) === ESTECAPELLI_TEAM_SWAP_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$changed = estecapelli_run_team_swap();
	update_option( 'estecapelli_team_swap_version', ESTECAPELLI_TEAM_SWAP_VERSION, false );
	set_transient( 'estecapelli_team_swap_result', $changed, 10 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_team_swap_notice' );
/** Report the run once. */
function estecapelli_team_swap_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$changed = get_transient( 'estecapelli_team_swap_result' );
	if ( false === $changed ) {
		return;
	}
	delete_transient( 'estecapelli_team_swap_result' );

	printf(
		'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
		$changed ? 'success' : 'warning',
		esc_html( sprintf( 'Our Team: Natalia replaced by Sandra on %d page rows.', (int) $changed ) )
	);
}
