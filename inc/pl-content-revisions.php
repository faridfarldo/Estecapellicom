<?php
/**
 * Render-time Polish page content, read from the reviewed translation JSON.
 *
 * Several Polish pages are stored in WordPress holding TURKISH content — the
 * WPML wiring is correct (the Polish post exists, is registered as pl and is
 * linked into the English trid group), only the stored rows are wrong. The
 * importer that would rewrite them is deliberately locked on production and
 * runs across every page at once, which is not a risk worth taking for this.
 *
 * So nothing here writes to the database. On a Polish request, and only for a
 * page whose stored title does not match its reviewed Polish title, the title
 * and builder rows are swapped for the ones in
 * inc/data/translations/pl/pages/<slug>.json on the way to the renderer.
 *
 * That comparison is what keeps this self-limiting:
 *   - a page already showing correct Polish is left completely alone,
 *   - the override stops applying by itself the day the stored rows are fixed,
 *   - no other language and no other page is ever touched.
 *
 * Section images are unaffected: translated rows borrow uploaded media from the
 * English post at render time (estecapelli_render_page_sections), matched by
 * layout ordinal, which happens after this filter and reads the English post
 * independently.
 *
 * To disable entirely: remove the require in functions.php. To exempt one page:
 * add_filter( 'estecapelli_pl_revision_pages', … ) and drop its slug.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * English source slugs whose Polish translation may be repaired at render time.
 *
 * Keyed by the English slug because that is what identifies the page across
 * languages; the JSON file carries the Polish slug and title.
 *
 * @return string[]
 */
function estecapelli_pl_revision_pages() {
	return (array) apply_filters(
		'estecapelli_pl_revision_pages',
		array(
			'hair-transplant',
			'tricholab',
			'pre-hair-transplant-period',
			'post-hair-transplant-period',
			'plastic-surgery',
			'dental-treatment',
			'about-us',
			'our-team',
			'our-doctors',
			'before-after',
			'contact',
		)
	);
}

/** Whether this request should be served reviewed Polish page content. */
function estecapelli_pl_revision_active() {
	return ! is_admin()
		&& function_exists( 'estecapelli_is_polish_request' )
		&& estecapelli_is_polish_request();
}

/**
 * Reviewed Polish content for a page, or null when it does not apply.
 *
 * Returns null — leaving the stored content untouched — when the page is not
 * in scope, has no translation file, or already renders the reviewed title.
 *
 * @param int $post_id Page being rendered.
 * @return array{title:string,sections:array}|null
 */
function estecapelli_pl_revision_page( $post_id ) {
	static $cache = array();

	$post_id = (int) $post_id;
	if ( array_key_exists( $post_id, $cache ) ) {
		return $cache[ $post_id ];
	}
	$cache[ $post_id ] = null;

	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
		return null;
	}

	$source_slug = function_exists( 'estecapelli_wpml_source_post_slug' )
		? estecapelli_wpml_source_post_slug( $post )
		: $post->post_name;
	if ( ! in_array( $source_slug, estecapelli_pl_revision_pages(), true ) ) {
		return null;
	}

	$file = get_template_directory() . '/inc/data/translations/pl/pages/' . $source_slug . '.json';
	if ( ! is_readable( $file ) ) {
		return null;
	}

	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! is_array( $data ) || empty( $data['title'] ) || empty( $data['sections'] ) || ! is_array( $data['sections'] ) ) {
		return null;
	}

	// The stored title already being the reviewed one means the page is fine.
	// Comparing here, rather than assuming, is what stops this from overriding
	// a page that was never broken.
	if ( trim( (string) $post->post_title ) === trim( (string) $data['title'] ) ) {
		return null;
	}

	$cache[ $post_id ] = array(
		'title'    => (string) $data['title'],
		'sections' => $data['sections'],
	);

	return $cache[ $post_id ];
}

add_filter( 'estecapelli_page_sections', 'estecapelli_pl_revision_sections', 10, 2 );
/**
 * Swap in the reviewed Polish builder rows for a page still holding Turkish.
 *
 * @param mixed $sections Rows read from ACF.
 * @param int   $post_id  Page being rendered.
 * @return mixed
 */
function estecapelli_pl_revision_sections( $sections, $post_id ) {
	if ( ! estecapelli_pl_revision_active() ) {
		return $sections;
	}

	$revision = estecapelli_pl_revision_page( $post_id );

	return $revision ? $revision['sections'] : $sections;
}

add_filter( 'the_title', 'estecapelli_pl_revision_title', 10, 2 );
/**
 * Match the page title to the rows the visitor is actually being shown.
 *
 * Without this the before/after page keeps its Turkish <h1>, because that
 * template prints the post title rather than a hero row.
 *
 * @param string $title   Stored title.
 * @param int    $post_id Page the title belongs to.
 * @return string
 */
function estecapelli_pl_revision_title( $title, $post_id = 0 ) {
	if ( ! $post_id || ! estecapelli_pl_revision_active() ) {
		return $title;
	}

	$revision = estecapelli_pl_revision_page( $post_id );

	return $revision ? $revision['title'] : $title;
}
