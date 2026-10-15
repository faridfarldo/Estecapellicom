<?php
/**
 * Blog cross-language repair — four posts, by ID, one button.
 *
 * Two articles carry a Spanish slug with a leading dash ("-es-doloroso-…",
 * "-se-volvera-…", what is left of the opening "¿"). WordPress stores those
 * slugs without the dash, so the blog importer's exact-slug lookup for the
 * Spanish copy missed, fell back to WPML, and was handed the Turkish post. It
 * wrote the Spanish title, body and Rank Math meta over the Turkish one. Blog
 * Repair later put the slugs and language slots back, but never the content.
 * The Spanish posts themselves kept their Spanish body but ended up with the
 * Italian meta description the same way.
 *
 * Live, 2026-10-15:
 *   TR #7195  /tr/blog/sac-ekimi-agrili-bir-islem-midir                — Spanish title/body/meta
 *   TR #7444  /tr/blog/sac-ekimi-sonrasinda-saclarim-tekrar-dokulur-mu — Spanish title/body/meta
 *   ES #7157  /es/blog/-es-doloroso-el-trasplante-capilar              — Italian meta description
 *   ES #7160  /es/blog/-se-volvera-a-caer-mi-cabello-…                 — Italian meta description
 *
 * Deliberately narrow:
 *   - No auto-run. Nothing happens until the button is pressed.
 *   - Only these four IDs. Each is written only if its post type, WPML
 *     language and slug still match what is expected; otherwise it is skipped.
 *   - Title and body go straight to the posts table, so no save_post hook
 *     (WPML, Rank Math, the slug sweep) fires and nothing else is touched.
 *     Slugs, language links, categories and images are never written.
 *   - Content comes from the version-controlled files the importer uses.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/data/blog-i18n-meta.php';

/**
 * The four posts and what each one should hold.
 *
 * 'body' => true rewrites title + body + SEO; false rewrites SEO only.
 *
 * @return array<int,array<string,mixed>>
 */
function estecapelli_blog_crossed_targets() {
	return array(
		7195 => array( 'lang' => 'tr', 'article' => 'is-hair-transplant-a-painful-procedure', 'body' => true ),
		7444 => array( 'lang' => 'tr', 'article' => 'will-my-hair-fall-out-again-after-a-hair-transplant', 'body' => true ),
		7157 => array( 'lang' => 'es', 'article' => 'is-hair-transplant-a-painful-procedure', 'body' => false ),
		7160 => array( 'lang' => 'es', 'article' => 'will-my-hair-fall-out-again-after-a-hair-transplant', 'body' => false ),
	);
}

/** The WPML language a post is stored under, read raw ('' if none). */
function estecapelli_blog_crossed_language( $post_id ) {
	global $wpdb;
	return (string) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT language_code FROM {$wpdb->prefix}icl_translations
			 WHERE element_id = %d AND element_type = 'post_post'
			 ORDER BY translation_id ASC LIMIT 1",
			(int) $post_id
		)
	);
}

/**
 * Check one target against the contract and collect what would be written.
 *
 * @return array{ok:bool,reason:string,post:?WP_Post,title:string,body:string,entry:array}
 */
function estecapelli_blog_crossed_check( $post_id, array $target ) {
	$result = array( 'ok' => false, 'reason' => '', 'post' => null, 'title' => '', 'body' => '', 'entry' => array() );

	$post = get_post( (int) $post_id );
	if ( ! $post || 'post' !== $post->post_type || 'trash' === $post->post_status ) {
		$result['reason'] = 'post not found, not a blog post, or in the bin';
		return $result;
	}
	$result['post'] = $post;

	$language = estecapelli_blog_crossed_language( $post_id );
	if ( $language !== $target['lang'] ) {
		$result['reason'] = sprintf( 'WPML language is "%s", expected "%s"', $language, $target['lang'] );
		return $result;
	}

	$slugs    = estecapelli_indexed_blog_slugs();
	$expected = (string) ( $slugs[ $target['article'] ][ $target['lang'] ] ?? '' );
	if ( '' === $expected || ! in_array( $post->post_name, array( $expected, ltrim( $expected, '-' ) ), true ) ) {
		$result['reason'] = sprintf( 'slug is "%s", expected "%s"', $post->post_name, $expected );
		return $result;
	}

	$meta  = estecapelli_blog_i18n_meta();
	$entry = $meta[ $target['article'] ][ $target['lang'] ] ?? null;
	if ( ! $entry || empty( $entry['title'] ) ) {
		$result['reason'] = 'no title/SEO entry in blog-i18n-meta.php';
		return $result;
	}
	$result['entry'] = $entry;
	$result['title'] = (string) $entry['title'];

	if ( $target['body'] ) {
		$file = get_template_directory() . '/inc/data/blog/' . $target['lang'] . '/' . $target['article'] . '.html';
		$html = is_readable( $file ) ? trim( (string) file_get_contents( $file ) ) : '';
		if ( '' === $html ) {
			$result['reason'] = 'body file missing or empty: ' . basename( $file );
			return $result;
		}
		$result['body'] = wp_kses_post( $html );
	}

	$result['ok'] = true;
	return $result;
}

/** Apply the repair to every target that passes its check. */
function estecapelli_blog_crossed_run() {
	global $wpdb;
	$lines = array();

	foreach ( estecapelli_blog_crossed_targets() as $post_id => $target ) {
		$check = estecapelli_blog_crossed_check( $post_id, $target );
		$label = sprintf( '#%d %s', $post_id, strtoupper( $target['lang'] ) );
		if ( ! $check['ok'] ) {
			$lines[] = $label . ': skipped — ' . $check['reason'];
			continue;
		}

		if ( $target['body'] ) {
			$saved = $wpdb->update(
				$wpdb->posts,
				array(
					'post_title'        => $check['title'],
					'post_content'      => $check['body'],
					'post_modified'     => current_time( 'mysql' ),
					'post_modified_gmt' => current_time( 'mysql', true ),
				),
				array( 'ID' => (int) $post_id ),
				array( '%s', '%s', '%s', '%s' ),
				array( '%d' )
			);
			if ( false === $saved ) {
				$lines[] = $label . ': failed — ' . ( $wpdb->last_error ?: 'database error' );
				continue;
			}
		}

		if ( ! empty( $check['entry']['description'] ) ) {
			update_post_meta( (int) $post_id, 'rank_math_description', $check['entry']['description'] );
		}
		if ( ! empty( $check['entry']['focus_keyword'] ) ) {
			update_post_meta( (int) $post_id, 'rank_math_focus_keyword', $check['entry']['focus_keyword'] );
		}
		clean_post_cache( (int) $post_id );

		$lines[] = $label . ( $target['body'] ? ': title, body and SEO restored' : ': SEO restored' );
	}

	return implode( "\n", $lines );
}

add_action( 'admin_menu', 'estecapelli_register_blog_crossed_repair' );
/** Tools → Blog Language Fix. */
function estecapelli_register_blog_crossed_repair() {
	add_management_page(
		__( 'Blog Language Fix', 'estecapelli' ),
		__( 'Blog Language Fix', 'estecapelli' ),
		'manage_options',
		'estecapelli-blog-crossed',
		'estecapelli_render_blog_crossed_repair'
	);
}

add_action( 'admin_post_estecapelli_blog_crossed', 'estecapelli_handle_blog_crossed_repair' );
/** The button. The only way this file writes anything. */
function estecapelli_handle_blog_crossed_repair() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_blog_crossed' );

	set_transient( 'estecapelli_blog_crossed_notice', estecapelli_blog_crossed_run(), 5 * MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'tools.php?page=estecapelli-blog-crossed' ) );
	exit;
}

/** Read-only report of the four posts, plus the button. */
function estecapelli_render_blog_crossed_repair() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$notice = (string) get_transient( 'estecapelli_blog_crossed_notice' );
	if ( '' !== $notice ) {
		delete_transient( 'estecapelli_blog_crossed_notice' );
	}

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Blog Language Fix', 'estecapelli' ) . '</h1>';
	if ( '' !== $notice ) {
		echo '<div class="notice notice-success"><pre style="white-space:pre-wrap;">' . esc_html( $notice ) . '</pre></div>';
	}
	echo '<p class="description" style="max-width:760px;">' . esc_html__( 'Restores the two Turkish articles that show Spanish text, and the Italian meta description on their two Spanish counterparts. Only the four posts below are written; nothing runs until the button is pressed. Slugs, language links, categories and images are left as they are.', 'estecapelli' ) . '</p>';

	echo '<table class="widefat striped" style="max-width:1000px;"><thead><tr>';
	foreach ( array( 'Post', 'Lang', 'Current title', 'Will become', 'Status' ) as $heading ) {
		echo '<th>' . esc_html( $heading ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	foreach ( estecapelli_blog_crossed_targets() as $post_id => $target ) {
		$check = estecapelli_blog_crossed_check( $post_id, $target );
		echo '<tr>';
		echo '<td>#' . (int) $post_id . ( $check['post'] ? ' — <a href="' . esc_url( get_permalink( $check['post'] ) ) . '" target="_blank">view</a>' : '' ) . '</td>';
		echo '<td><code>' . esc_html( strtoupper( $target['lang'] ) ) . '</code></td>';
		echo '<td>' . esc_html( $check['post'] ? $check['post']->post_title : '—' ) . '</td>';
		echo '<td>' . esc_html( $target['body'] ? $check['title'] : __( '(meta description + focus keyword only)', 'estecapelli' ) ) . '</td>';
		echo '<td>' . ( $check['ok']
			? '<span style="color:#0d8551;">' . esc_html__( 'Ready', 'estecapelli' ) . '</span>'
			: '<span style="color:#b32d2e;">' . esc_html__( 'Will be skipped:', 'estecapelli' ) . ' ' . esc_html( $check['reason'] ) . '</span>' ) . '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';

	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="margin-top:1.5rem;">';
	echo '<input type="hidden" name="action" value="estecapelli_blog_crossed">';
	wp_nonce_field( 'estecapelli_blog_crossed' );
	submit_button( __( 'Fix these four posts', 'estecapelli' ), 'primary', 'submit', false );
	echo '</form></div>';
}
