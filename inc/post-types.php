<?php
/**
 * Custom post types + taxonomies for Estecapelli.
 *
 * Treatment is the canonical service entity — every hair, plastic, dental and
 * medical procedure is a `treatment` post, categorised via the
 * `treatment_category` taxonomy (Hair Transplant, Plastic Surgery, etc.).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'estecapelli_register_treatment_cpt', 0 );
function estecapelli_register_treatment_cpt() {

	register_post_type(
		'treatment',
		array(
			'labels' => array(
				'name'                  => __( 'Treatments', 'estecapelli' ),
				'singular_name'         => __( 'Treatment', 'estecapelli' ),
				'menu_name'             => __( 'Treatments', 'estecapelli' ),
				'add_new'               => __( 'Add Treatment', 'estecapelli' ),
				'add_new_item'          => __( 'Add New Treatment', 'estecapelli' ),
				'new_item'              => __( 'New Treatment', 'estecapelli' ),
				'edit_item'             => __( 'Edit Treatment', 'estecapelli' ),
				'view_item'             => __( 'View Treatment', 'estecapelli' ),
				'all_items'             => __( 'All Treatments', 'estecapelli' ),
				'search_items'          => __( 'Search Treatments', 'estecapelli' ),
				'featured_image'        => __( 'Cover image', 'estecapelli' ),
				'set_featured_image'    => __( 'Set cover image', 'estecapelli' ),
				'remove_featured_image' => __( 'Remove cover image', 'estecapelli' ),
			),
			'public'              => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-heart',
			'menu_position'       => 20,
			'has_archive'         => false,
			// Live URL structure is /{lang}/{category}/{service}. WPML adds the
			// language prefix and strips it from incoming requests, so the rewrite
			// slug must NOT bake /en/ in — otherwise the rule never matches once
			// WPML has removed the prefix. The %treatment_category% tag keeps its
			// default regex (no restriction — a restricted regex breaks the rule's
			// capture groups); the /en/ page shim resolves any real page that would
			// otherwise be caught by this greedy two-segment rule.
			'rewrite'             => array( 'slug' => '%treatment_category%', 'with_front' => false ),
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'show_in_nav_menus'   => true,
		)
	);

	register_post_type(
		'result',
		array(
			'labels' => array(
				'name'                  => __( 'Before & After', 'estecapelli' ),
				'singular_name'         => __( 'Result', 'estecapelli' ),
				'menu_name'             => __( 'Before & After', 'estecapelli' ),
				'add_new'               => __( 'Add Result', 'estecapelli' ),
				'add_new_item'          => __( 'Add New Result', 'estecapelli' ),
				'new_item'              => __( 'New Result', 'estecapelli' ),
				'edit_item'             => __( 'Edit Result', 'estecapelli' ),
				'view_item'             => __( 'View Result', 'estecapelli' ),
				'all_items'             => __( 'All Results', 'estecapelli' ),
				'search_items'          => __( 'Search Results', 'estecapelli' ),
			),
			// Managed in admin, surfaced only through the carousel + gallery
			// page — no thin single-result pages on the front end.
			'public'              => false,
			'show_ui'             => true,
			// Hidden from the admin sidebar: the before/after content is authored
			// through each treatment's "gallery" section, so this empty CPT was
			// only cluttering the menu. The post type stays fully registered so the
			// treatment-page carousel, its ACF group and the WPML fix keep working.
			'show_in_menu'        => false,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-images-alt2',
			'menu_position'       => 21,
			'has_archive'         => false,
			'rewrite'             => false,
			'supports'            => array( 'title', 'page-attributes' ),
		)
	);

	register_post_type(
		'doctor',
		array(
			'labels' => array(
				'name'                  => __( 'Doctors', 'estecapelli' ),
				'singular_name'         => __( 'Doctor', 'estecapelli' ),
				'menu_name'             => __( 'Doctors', 'estecapelli' ),
				'add_new'               => __( 'Add Doctor', 'estecapelli' ),
				'add_new_item'          => __( 'Add New Doctor', 'estecapelli' ),
				'new_item'              => __( 'New Doctor', 'estecapelli' ),
				'edit_item'             => __( 'Edit Doctor', 'estecapelli' ),
				'view_item'             => __( 'View Doctor', 'estecapelli' ),
				'all_items'             => __( 'All Doctors', 'estecapelli' ),
				'search_items'          => __( 'Search Doctors', 'estecapelli' ),
				'not_found'             => __( 'No doctors yet. Click “Add Doctor” to create one.', 'estecapelli' ),
				'not_found_in_trash'    => __( 'No doctors in the trash.', 'estecapelli' ),
			),
			'public'              => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-businessperson',
			'menu_position'       => 22,
			'has_archive'         => false,
			// Profiles live at /{lang}/about-us/our-doctors/{slug} — same path the
			// old nested pages used, so links and SEO are preserved. WPML adds the
			// language prefix (and strips it from requests), so it is NOT baked in.
			'rewrite'             => array( 'slug' => 'about-us/our-doctors', 'with_front' => false ),
			// Name = post title, ordering via page-attributes (menu_order). Photo,
			// position, bio and credentials are ACF fields (see acf-field-groups.php)
			// so the editor only ever sees a short, friendly form.
			'supports'            => array( 'title', 'page-attributes' ),
			'show_in_nav_menus'   => true,
		)
	);

	// The `doctor` CPT shares its base path with the real "Our Doctors" page
	// (about-us/our-doctors). WordPress resolves /about-us/our-doctors/{slug}
	// as a child of that page first and 404s before the CPT rule ever runs, so
	// register an explicit top-priority rule that maps the trailing slug
	// straight to the doctor profile. The optional `en/` prefix covers both the
	// WPML production URLs and the local /en/ routing shim.
	add_rewrite_rule(
		'^(?:en/)?about-us/our-doctors/([^/]+)/?$',
		'index.php?doctor=$matches[1]',
		'top'
	);

	register_taxonomy(
		'treatment_category',
		array( 'treatment', 'result' ),
		array(
			'labels' => array(
				'name'              => __( 'Treatment Categories', 'estecapelli' ),
				'singular_name'     => __( 'Treatment Category', 'estecapelli' ),
				'menu_name'         => __( 'Categories', 'estecapelli' ),
				'all_items'         => __( 'All Categories', 'estecapelli' ),
				'edit_item'         => __( 'Edit Category', 'estecapelli' ),
				'add_new_item'      => __( 'Add New Category', 'estecapelli' ),
				'new_item_name'     => __( 'New Category Name', 'estecapelli' ),
				'search_items'      => __( 'Search Categories', 'estecapelli' ),
				'parent_item'       => __( 'Parent Category', 'estecapelli' ),
				'parent_item_colon' => __( 'Parent Category:', 'estecapelli' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'treatment-category', 'with_front' => false ),
		)
	);
}

/**
 * Resolve the %treatment_category% tag in treatment permalinks to the
 * post's category term slug, producing /en/{category}/{service}/.
 *
 * Falls back to the first category's slug; if a treatment has no category
 * assigned we default to 'hair-transplant' so the URL never breaks.
 */
add_filter( 'post_type_link', 'estecapelli_treatment_permalink', 10, 2 );
function estecapelli_treatment_permalink( $post_link, $post ) {
	if ( 'treatment' !== $post->post_type || false === strpos( $post_link, '%treatment_category%' ) ) {
		return $post_link;
	}

	$category = 'hair-transplant';
	$terms    = get_the_terms( $post->ID, 'treatment_category' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		// Prefer the deepest (most specific) term if the taxonomy is nested.
		$term     = end( $terms );
		$category = $term->slug;
	}

	return str_replace( '%treatment_category%', $category, $post_link );
}
