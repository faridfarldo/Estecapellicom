<?php
/**
 * Single Doctor.
 *
 * A doctor is its own post type, so the editor never builds this page by hand —
 * it is assembled here from the short Doctor Profile form (name = title, plus
 * position, photo, bio and credentials). We reuse the existing `doctor` section
 * renderer so the profile matches the rest of the site, then append the real
 * before/after results tied to that doctor's treatments are out of scope here.
 *
 * @package Estecapelli
 */

get_header();

while ( have_posts() ) :
	the_post();

	$name        = get_the_title();
	$position    = function_exists( 'get_field' ) ? (string) get_field( 'position' ) : '';
	$photo       = function_exists( 'get_field' ) ? get_field( 'photo' ) : array();
	$bio         = function_exists( 'get_field' ) ? (string) get_field( 'bio' ) : '';
	$credentials = function_exists( 'get_field' ) ? get_field( 'credentials' ) : array();

	// Build the profile section payload the shared renderer expects, then load
	// the same template the page builder uses. This keeps one source of truth
	// for the profile markup and styling.
	$section = array(
		'eyebrow'     => __( 'Résumé', 'estecapelli' ),
		'name'        => $name,
		'role'        => $position,
		'photo'       => is_array( $photo ) ? $photo : array(),
		'body'        => $bio,
		'credentials' => is_array( $credentials ) ? $credentials : array(),
		'cta'         => array(
			'label' => __( 'Book a Free Consultation', 'estecapelli' ),
			'url'   => home_url( '/en/contact' ),
		),
	);

	$template = locate_template( 'template-parts/sections/doctor.php' );
	if ( $template ) {
		set_query_var( 'section', $section );
		load_template( $template, false );
	}

endwhile;

get_footer();
