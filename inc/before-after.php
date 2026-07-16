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
/**
 * Fixed display order for hair-transplant services (services block + before/after
 * tabs). Lower = earlier; anything not listed sorts after, alphabetically.
 *
 * @param string $slug Treatment post slug.
 * @return int
 */
function estecapelli_treatment_order_rank( $slug ) {
	// Sorting is always applied WITHIN a single category, so these ranks only
	// ever compare same-category slugs — hair ranks never mix with plastic ones.
	$order = array(
		// Hair transplant.
		'exosome-fue-hair-transplant',
		'vita-treatment',
		'sapphire-fue-hair-transplant',
		'dhi-hair-transplant',
		'female-hair-transplant',
		'beard-transplant',
		// Plastic surgery — these four lead; the rest keep query order after them.
		'breast-aesthetics-breast-surgery', // Breast surgery
		'rhinoplasty',                      // Nose surgery
		'abdominoplasty-tummy-tuck',        // Tummy Tuck
		'liposuction',                      // Lipo
	);
	$i = array_search( $slug, $order, true );
	return ( false === $i ) ? 99 : $i;
}

/**
 * uasort() comparator: order a group's `services` by the fixed rank above.
 */
function estecapelli_sort_services_by_rank( array &$services ) {
	uasort(
		$services,
		function ( $a, $b ) {
			$ra = estecapelli_treatment_order_rank( $a['service']->post_name );
			$rb = estecapelli_treatment_order_rank( $b['service']->post_name );
			if ( $ra !== $rb ) {
				return $ra - $rb;
			}
			return strcmp( $a['service']->post_title, $b['service']->post_title );
		}
	);
}

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
	// Fixed priority for the main categories, then alphabetical for the rest.
	$cat_order = array( 'hair-transplant' => 0, 'plastic-surgery' => 1, 'dental-treatment' => 2 );
	uasort(
		$buckets,
		function ( $a, $b ) use ( $cat_order ) {
			$ai = $cat_order[ $a['term']->slug ] ?? 99;
			$bi = $cat_order[ $b['term']->slug ] ?? 99;
			if ( $ai !== $bi ) {
				return $ai - $bi;
			}
			return strcmp( $a['term']->name, $b['term']->name );
		}
	);

	foreach ( $buckets as $__key => $__group ) {
		estecapelli_sort_services_by_rank( $buckets[ $__key ]['services'] );
	}

	return array_values( $buckets );
}

/**
 * All before/after composites pulled from the "gallery" sections of every
 * treatment, grouped by category (main group) then service (technique).
 *
 * This is the source for the Before & After page: whatever an editor adds to a
 * treatment's gallery section automatically surfaces here, grouped by the
 * treatment's category. Hair Transplant is forced first.
 *
 * @return array List of groups: [ 'term' => WP_Term, 'services' => [ [ 'service' => WP_Post, 'items' => array[] ] ] ].
 */
/**
 * Collect every gallery image from a treatment's ACF sections.
 *
 * @param mixed $sections Value of the `page_sections` ACF field.
 * @return array<int,array<string,mixed>> Gallery items that carry an image URL.
 */
function estecapelli_collect_gallery_items( $sections ) {
	if ( empty( $sections ) || ! is_array( $sections ) ) {
		return array();
	}
	$items = array();
	foreach ( $sections as $section ) {
		if ( 'gallery' !== ( $section['acf_fc_layout'] ?? '' ) || empty( $section['items'] ) || ! is_array( $section['items'] ) ) {
			continue;
		}
		foreach ( $section['items'] as $item ) {
			if ( ! empty( $item['image']['url'] ) ) {
				$items[] = $item;
			}
		}
	}
	return $items;
}

function estecapelli_gallery_grouped() {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$default_lang = apply_filters( 'wpml_default_language', null );
	$current_lang = apply_filters( 'wpml_current_language', null );
	$translate    = $default_lang && $current_lang && $default_lang !== $current_lang;

	// Before/after photos are uploaded on the original (default-language)
	// treatments and are language-neutral; translated treatments carry empty
	// galleries. Gather everything in the default language — the path known to be
	// populated — then localize the visible labels for the current request.
	if ( $translate ) {
		do_action( 'wpml_switch_language', $default_lang );
	}

	// Fetch every language's treatments (suppress_filters bypasses WPML's own
	// language filter, which is unreliable for a switched secondary query) and
	// keep only the default-language originals — the ones that actually hold the
	// gallery images. Their fields/terms are read below in the default-language
	// context, exactly like the working English page.
	$treatment_ids = get_posts(
		array(
			'post_type'        => 'treatment',
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'orderby'          => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => true,
		)
	);

	// Bucket: cat_term_id => [ 'term' => WP_Term, 'services' => [ service_id => [...] ] ].
	$buckets = array();

	foreach ( (array) $treatment_ids as $tid ) {
		if ( $default_lang ) {
			$post_lang = apply_filters(
				'wpml_element_language_code',
				null,
				array( 'element_id' => (int) $tid, 'element_type' => 'post_treatment' )
			);
			if ( $post_lang && $post_lang !== $default_lang ) {
				continue; // A translation — its gallery is empty; skip it.
			}
		}

		// Gather every image from all gallery sections on this treatment.
		$items = estecapelli_collect_gallery_items( get_field( 'page_sections', $tid ) );
		if ( empty( $items ) ) {
			continue;
		}

		$terms = get_the_terms( $tid, 'treatment_category' );
		if ( ! $terms || is_wp_error( $terms ) ) {
			continue;
		}
		// Deepest (most specific) category wins, matching the permalink rule.
		$term = end( $terms );

		if ( ! isset( $buckets[ $term->term_id ] ) ) {
			$buckets[ $term->term_id ] = array( 'term' => $term, 'services' => array() );
		}
		$buckets[ $term->term_id ]['services'][ $tid ] = array(
			'service' => get_post( $tid ),
			'items'   => $items,
		);
	}

	// Fixed priority for the main categories, then alphabetical for the rest.
	// Done in the default language so category slugs and rank meta are reliable.
	$cat_order = array( 'hair-transplant' => 0, 'plastic-surgery' => 1, 'dental-treatment' => 2 );
	uasort(
		$buckets,
		function ( $a, $b ) use ( $cat_order ) {
			$ai = $cat_order[ $a['term']->slug ] ?? 99;
			$bi = $cat_order[ $b['term']->slug ] ?? 99;
			if ( $ai !== $bi ) {
				return $ai - $bi;
			}
			return strcmp( $a['term']->name, $b['term']->name );
		}
	);

	foreach ( $buckets as $__key => $__group ) {
		estecapelli_sort_services_by_rank( $buckets[ $__key ]['services'] );
	}

	if ( $translate ) {
		do_action( 'wpml_switch_language', $current_lang );
		$buckets = estecapelli_localize_gallery_buckets( $buckets, $current_lang );
	}

	return array_values( $buckets );
}

/**
 * Localize gallery buckets for display: swap the category name and each service
 * post to the current language when a translation exists. The English category
 * slug is kept so tab IDs and the fixed category ordering stay stable; the
 * language-neutral before/after images are left untouched.
 *
 * @param array<int,array<string,mixed>> $buckets Buckets built in the default language.
 * @param string                         $lang    Target language code.
 * @return array<int,array<string,mixed>>
 */
function estecapelli_localize_gallery_buckets( array $buckets, $lang ) {
	foreach ( $buckets as $key => $group ) {
		$loc_term_id = (int) apply_filters( 'wpml_object_id', $group['term']->term_id, 'treatment_category', false, $lang );
		if ( $loc_term_id && $loc_term_id !== (int) $group['term']->term_id ) {
			$loc_term = get_term( $loc_term_id, 'treatment_category' );
			if ( $loc_term && ! is_wp_error( $loc_term ) ) {
				$display_term       = clone $group['term'];
				$display_term->name = $loc_term->name;
				$buckets[ $key ]['term'] = $display_term;
			}
		}

		foreach ( $group['services'] as $svc_id => $svc ) {
			$loc_id = (int) apply_filters( 'wpml_object_id', $svc_id, 'treatment', false, $lang );
			if ( $loc_id && $loc_id !== (int) $svc_id ) {
				$loc_post = get_post( $loc_id );
				if ( $loc_post ) {
					$buckets[ $key ]['services'][ $svc_id ]['service'] = $loc_post;
				}
			}
		}
	}

	return $buckets;
}
