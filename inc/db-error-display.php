<?php
/**
 * Keep database errors out of responses that must stay machine readable.
 *
 * wpdb::print_error() writes with printf() rather than through PHP's error
 * handler, so display_errors never silences it and WP_DEBUG_LOG never records
 * it. The only switch is wpdb's own show_errors flag, decided once at
 * construction from WP_DEBUG && WP_DEBUG_DISPLAY — anything that turns it back
 * on afterwards puts raw SQL text inside the next response.
 *
 * That single failing query then breaks the request in whichever way hurts most:
 * inside a REST body the block editor reports "The response is not a valid JSON
 * response" and refuses to save, and inside an admin page it arrives before
 * wp_redirect() so WordPress reports "headers already sent" and the redirect is
 * lost. Neither reveals the query that actually failed.
 *
 * Core already decided REST output must not carry printed errors — wp_debug_mode()
 * forces display_errors off for REST_REQUEST. wpdb simply escapes that rule by
 * not using the error handler, so the same intent is applied to it here.
 *
 * Nothing is hidden that was not already meant to be hidden, and failures still
 * surface: wpdb records every one in $wpdb->last_error and $EZSQL_ERROR.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Stop wpdb printing into the response being built. */
function estecapelli_hide_db_error_output() {
	global $wpdb;

	if ( isset( $wpdb ) && is_object( $wpdb ) && method_exists( $wpdb, 'hide_errors' ) ) {
		$wpdb->hide_errors();
	}
}

/*
 * A REST body must be JSON whatever the site's debug settings say, so this is
 * unconditional and matches what core already does for every other error source.
 */
add_action( 'rest_api_init', 'estecapelli_hide_db_error_output', 0 );

/*
 * In wp-admin the site's own preference still decides. A printed error there is
 * merely ugly on a screen that renders, but fatal to one that redirects, so
 * honour WP_DEBUG_DISPLAY rather than overriding it.
 */
if ( is_admin() && defined( 'WP_DEBUG_DISPLAY' ) && ! WP_DEBUG_DISPLAY ) {
	estecapelli_hide_db_error_output();
}
