<?php
/**
 * Comments are switched off site-wide.
 *
 * The theme never renders a comment form, but WordPress still accepted
 * comments on every post whose comment_status was "open": spam bots post
 * straight to /wp-comments-post.php (or the REST API / XML-RPC pingbacks)
 * without ever seeing a form. This closes every one of those doors.
 *
 * The wp-admin Comments screen is left in place so existing spam can still
 * be reviewed and emptied.
 *
 * @package Estecapelli
 */

defined( 'ABSPATH' ) || exit;

// Every post reports comments/pings as closed. wp-comments-post.php checks
// this and rejects the submission ("comments are closed") before saving.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

// New posts default to closed too.
add_filter( 'option_default_comment_status', static fn() => 'closed' );
add_filter( 'option_default_ping_status', static fn() => 'closed' );

// Never show stored comments on the front end.
add_filter( 'comments_array', '__return_empty_array', 20 );

// Hard stop on the comment handler itself, before WordPress does any work.
add_action(
	'pre_comment_on_post',
	static function () {
		wp_die(
			esc_html__( 'Comments are closed.', 'estecapelli' ),
			'',
			array( 'response' => 403 )
		);
	}
);

// REST API: refuse to create comments.
add_filter(
	'rest_pre_insert_comment',
	static function () {
		return new WP_Error(
			'rest_comment_closed',
			__( 'Comments are closed.', 'estecapelli' ),
			array( 'status' => 403 )
		);
	}
);

// XML-RPC pingbacks / trackback discovery.
add_filter(
	'xmlrpc_methods',
	static function ( $methods ) {
		unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
		return $methods;
	}
);
add_filter(
	'wp_headers',
	static function ( $headers ) {
		unset( $headers['X-Pingback'] );
		return $headers;
	}
);

// No comment feeds.
add_filter( 'feed_links_show_comments_feed', '__return_false' );
