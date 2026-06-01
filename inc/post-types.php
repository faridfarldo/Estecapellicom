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
			// Live URL structure is /en/{category}/{service}. The %treatment_category%
			// tag is a real rewrite tag (registered because treatment_category has
			// rewrite enabled) so WordPress builds matching rules; the displayed
			// permalink is resolved in estecapelli_treatment_permalink().
			'rewrite'             => array( 'slug' => 'en/%treatment_category%', 'with_front' => false ),
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'show_in_nav_menus'   => true,
		)
	);

	register_taxonomy(
		'treatment_category',
		'treatment',
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
