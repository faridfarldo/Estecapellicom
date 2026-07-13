<?php
/**
 * Blog seeding — recreate the live blog articles as WordPress posts, in version
 * control, with the EXACT same slugs as estecapelli.com/en/blog/{slug} so the
 * URLs match.
 *
 * Each article's body lives in /inc/data/blog/{slug}.html. The importer is
 * idempotent: it skips any slug that already exists, so it never duplicates the
 * posts already live on the host — it only fills in what's missing (e.g. on a
 * fresh install). Featured images are added later by hand.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'estecapelli_blog_seed_articles' ) ) {
	/**
	 * The 9 live articles: slug (must match the live URL), title, category.
	 * Body HTML is read from /inc/data/blog/{slug}.html.
	 */
	function estecapelli_blog_seed_articles() {
		return array(
			array( 'slug' => 'hair-transplant-turkey-complete-expert-guide',        'title' => 'Hair Transplant Turkey : Complete Expert Guide',        'category' => 'Hair Transplant' ),
			array( 'slug' => 'unshaven-hair-transplant-for-women',                   'title' => 'Unshaven Hair Transplant for Women',                    'category' => 'Hair Transplant' ),
			array( 'slug' => 'unshaven-hair-transplant',                             'title' => 'Unshaven Hair Transplant',                              'category' => 'Hair Transplant' ),
			array( 'slug' => 'can-diabetic-patients-undergo-a-hair-transplant',      'title' => 'Can Diabetic Patients Undergo a Hair Transplant?',       'category' => 'Hair Transplant' ),
			array( 'slug' => 'is-hair-transplant-a-painful-procedure',               'title' => 'Is Hair Transplant a Painful Procedure?',               'category' => 'Hair Transplant' ),
			array( 'slug' => 'will-my-hair-fall-out-again-after-a-hair-transplant',  'title' => 'Will My Hair Fall Out Again After a Hair Transplant?',   'category' => 'Hair Transplant' ),
			array( 'slug' => 'hair-transplant-with-the-fue-vita-technique',          'title' => 'Hair Transplant with the FUE Vita Technique',           'category' => 'Hair Transplant' ),
			array( 'slug' => 'hair-transplant-for-hiv-positive-patients-in-turkey',  'title' => 'Hair Transplant for HIV Positive Patients in Turkey',   'category' => 'Hair Transplant' ),
			array( 'slug' => 'hair-transplant-for-hiv-positive-patients',            'title' => 'Hair Transplant for HIV-Positive Patients',             'category' => 'Hair Transplant' ),
		);
	}
}

/**
 * Blog posts resolve at /{lang}/blog/{slug} — the canonical live structure.
 *
 * WPML adds the language prefix (/en/, /fr/…), so the permalink base must be
 * /blog/%postname%/ and NOT bake /en/ in — otherwise WPML doubles it to
 * /en/en/blog/. (Before WPML this was /en/blog/%postname%/; the v3 flag re-runs
 * this once to switch the live site over now that WPML is active.)
 *
 * Runs once per flag. WordPress's own canonical redirect sends the old URLs on.
 */
add_action( 'admin_init', 'estecapelli_ensure_blog_permalink_base', 5 );
function estecapelli_ensure_blog_permalink_base() {
	if ( get_option( 'estecapelli_blog_permalink_base_v3' ) ) {
		return;
	}
	$current = (string) get_option( 'permalink_structure' );
	if ( '/blog/%postname%/' !== $current ) {
		global $wp_rewrite;
		if ( $wp_rewrite instanceof WP_Rewrite ) {
			$wp_rewrite->set_permalink_structure( '/blog/%postname%/' );
			$wp_rewrite->flush_rules( false );
		}
	}
	update_option( 'estecapelli_blog_permalink_base_v3', 1 );
}

add_action( 'admin_init', 'estecapelli_seed_blog_posts' );
function estecapelli_seed_blog_posts() {
	if ( get_option( 'estecapelli_blog_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'estecapelli_blog_seed_articles' ) ) {
		return;
	}

	$dir = get_template_directory() . '/inc/data/blog/';

	foreach ( estecapelli_blog_seed_articles() as $a ) {
		// Never duplicate a post that already exists at this slug (protects the
		// live site, which already has all 9).
		if ( get_page_by_path( $a['slug'], OBJECT, 'post' ) ) {
			continue;
		}

		$file = $dir . $a['slug'] . '.html';
		if ( ! file_exists( $file ) ) {
			continue; // body not bundled yet — skip until it is.
		}
		$content = file_get_contents( $file );
		if ( false === $content || '' === trim( $content ) ) {
			continue;
		}

		// Resolve (or create) the category term.
		$cat_ids = array();
		if ( ! empty( $a['category'] ) ) {
			$term = get_term_by( 'name', $a['category'], 'category' );
			if ( ! $term ) {
				$new = wp_insert_term( $a['category'], 'category' );
				if ( ! is_wp_error( $new ) ) {
					$cat_ids[] = (int) $new['term_id'];
				}
			} else {
				$cat_ids[] = (int) $term->term_id;
			}
		}

		wp_insert_post(
			array(
				'post_title'    => $a['title'],
				'post_name'     => $a['slug'], // exact slug → URL matches live.
				'post_content'  => $content,
				'post_status'   => 'publish',
				'post_type'     => 'post',
				'post_category' => $cat_ids,
			)
		);
	}

	update_option( 'estecapelli_blog_seeded', 1 );
}
