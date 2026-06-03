<?php
/**
 * Before & After results — queries and render helpers.
 *
 * A `result` post holds one before image, one after image, a linked
 * `service` (treatment), an optional method label and graft count. Results
 * surface in two places, both reading from this single source:
 *
 *   1. A carousel auto-appended to the bottom of every treatment page,
 *      showing only that service's results.
 *   2. The Before & After page (page-before-after.php), grouping every
 *      result by its service's category (main group) then by service
 *      (technique).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Results linked to a single treatment, ordered by menu order then newest.
 *
 * @param int $treatment_id Treatment post ID.
 * @return WP_Post[] Result posts.
 */
function estecapelli_results_for_treatment( $treatment_id ) {
	$treatment_id = (int) $treatment_id;
	if ( ! $treatment_id ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'result',
			'post_status'            => 'publish',
			'posts_per_page'         => 24,
			'orderby'                => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'meta_query'             => array(
				array(
					'key'     => 'service',
					'value'   => $treatment_id,
					'compare' => '=',
				),
			),
		)
	);

	return $query->posts;
}

/**
 * Normalise a result post into the shape the card template expects.
 *
 * @param int|WP_Post $result Result post or ID.
 * @return array|null Card data, or null when images are missing.
 */
function estecapelli_result_card_data( $result ) {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	$id     = is_object( $result ) ? $result->ID : (int) $result;
	$before = get_field( 'before_image', $id );
	$after  = get_field( 'after_image', $id );

	if ( empty( $before['url'] ) || empty( $after['url'] ) ) {
		return null;
	}

	$service_id = (int) get_field( 'service', $id );
	$method     = trim( (string) get_field( 'method', $id ) );
	if ( '' === $method && $service_id ) {
		$method = get_the_title( $service_id );
	}

	$grafts = get_field( 'grafts', $id );
	$grafts = ( '' !== $grafts && null !== $grafts ) ? (int) $grafts : 0;

	return array(
		'before' => $before,
		'after'  => $after,
		'method' => $method,
		'grafts' => $grafts,
	);
}

/**
 * Render the before/after carousel for a treatment. Prints nothing when the
 * treatment has no results.
 *
 * @param int $treatment_id Treatment post ID.
 */
function estecapelli_render_before_after_carousel( $treatment_id ) {
	$results = estecapelli_results_for_treatment( $treatment_id );
	if ( empty( $results ) ) {
		return;
	}

	set_query_var( 'ba_results', $results );
	set_query_var( 'ba_title', get_the_title( $treatment_id ) );
	load_template( locate_template( 'template-parts/before-after-carousel.php' ), false );
}

/**
 * All results grouped by category (main group) then service (technique).
 *
 * Hair Transplant is forced first; remaining categories follow term order.
 *
 * @return array List of groups: [ 'term' => WP_Term, 'services' => [ 'service' => WP_Post, 'results' => WP_Post[] ] ].
 */
function estecapelli_results_grouped() {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'result',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'no_found_rows'  => true,
		)
	);

	if ( empty( $query->posts ) ) {
		return array();
	}

	// Bucket: cat_term_id => [ 'term' => WP_Term, 'services' => [ service_id => [...] ] ].
	$buckets = array();

	foreach ( $query->posts as $post ) {
		$service_id = (int) get_field( 'service', $post->ID );
		if ( ! $service_id ) {
			continue;
		}

		$terms = get_the_terms( $service_id, 'treatment_category' );
		if ( ! $terms || is_wp_error( $terms ) ) {
			continue;
		}
		// Deepest (most specific) category wins, matching the permalink rule.
		$term = end( $terms );

		if ( ! isset( $buckets[ $term->term_id ] ) ) {
			$buckets[ $term->term_id ] = array( 'term' => $term, 'services' => array() );
		}
		if ( ! isset( $buckets[ $term->term_id ]['services'][ $service_id ] ) ) {
			$buckets[ $term->term_id ]['services'][ $service_id ] = array(
				'service' => get_post( $service_id ),
				'results' => array(),
			);
		}
		$buckets[ $term->term_id ]['services'][ $service_id ]['results'][] = $post;
	}

	// Order categories: hair-transplant first, then alphabetically by name.
	uasort(
		$buckets,
		function ( $a, $b ) {
			$a_hair = ( 'hair-transplant' === $a['term']->slug ) ? 0 : 1;
			$b_hair = ( 'hair-transplant' === $b['term']->slug ) ? 0 : 1;
			if ( $a_hair !== $b_hair ) {
				return $a_hair - $b_hair;
			}
			return strcmp( $a['term']->name, $b['term']->name );
		}
	);

	return array_values( $buckets );
}
