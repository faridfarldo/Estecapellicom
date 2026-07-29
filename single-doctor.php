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

	// Résumé image: prefer a dedicated résumé photo (uploaded or theme-bundled
	// URL); otherwise reuse the roster thumbnail. Never changes the thumbnail.
	$resume_photo     = function_exists( 'get_field' ) ? get_field( 'resume_photo' ) : array();
	$resume_photo_url = function_exists( 'get_field' ) ? (string) get_field( 'resume_photo_url' ) : '';
	if ( is_array( $resume_photo ) && ! empty( $resume_photo['url'] ) ) {
		$photo = $resume_photo;
	} elseif ( '' !== $resume_photo_url ) {
		$photo = array( 'url' => $resume_photo_url, 'alt' => $name );
	}

	// Doctor photos are uploaded through ACF image fields that live on whichever
	// language the editor used — usually the default. A translated profile that
	// carries no photo of its own borrows the shared photo from the
	// default-language doctor, the same way page sections inherit shared media.
	// Same résumé/roster priority, applied to the source post. Display only.
	if ( ( ! is_array( $photo ) || empty( $photo['url'] ) ) && function_exists( 'get_field' ) ) {
		$default_lang = apply_filters( 'wpml_default_language', null );
		$current_lang = apply_filters( 'wpml_current_language', null );
		if ( $default_lang && $current_lang && $default_lang !== $current_lang ) {
			$source_id = (int) apply_filters( 'wpml_object_id', get_the_ID(), 'doctor', false, $default_lang );
			if ( $source_id && $source_id !== (int) get_the_ID() ) {
				$src_resume     = get_field( 'resume_photo', $source_id );
				$src_resume_url = (string) get_field( 'resume_photo_url', $source_id );
				$src_photo      = get_field( 'photo', $source_id );
				if ( is_array( $src_resume ) && ! empty( $src_resume['url'] ) ) {
					$photo = $src_resume;
				} elseif ( '' !== $src_resume_url ) {
					$photo = array( 'url' => $src_resume_url, 'alt' => $name );
				} elseif ( is_array( $src_photo ) && ! empty( $src_photo['url'] ) ) {
					$photo = $src_photo;
				}
			}
		}
	}

	if ( function_exists( 'estecapelli_tr_revision_doctor_profile' ) ) {
		$profile = estecapelli_tr_revision_doctor_profile(
			get_the_ID(),
			array(
				'name'        => $name,
				'position'    => $position,
				'bio'         => $bio,
				'credentials' => is_array( $credentials ) ? $credentials : array(),
			)
		);
		$name        = (string) ( $profile['name'] ?? $name );
		$position    = (string) ( $profile['position'] ?? $position );
		$bio         = (string) ( $profile['bio'] ?? $bio );
		$credentials = is_array( $profile['credentials'] ?? null ) ? $profile['credentials'] : $credentials;
	}

	$is_turkish_profile = function_exists( 'estecapelli_is_turkish_request' ) && estecapelli_is_turkish_request();

	// Build the profile section payload the shared renderer expects, then load
	// the same template the page builder uses. This keeps one source of truth
	// for the profile markup and styling.
	$section = array(
		'eyebrow'     => $is_turkish_profile ? '' : __( 'Résumé', 'estecapelli' ),
		'name'        => $name,
		'role'        => $position,
		'photo'       => is_array( $photo ) ? $photo : array(),
		'body'        => $bio,
		'credentials' => is_array( $credentials ) ? $credentials : array(),
		'cta'         => array(
			'label' => __( 'Book a Free Consultation', 'estecapelli' ),
			'url'   => estecapelli_indexed_url( '/en/contact' ),
		),
	);

	$template = locate_template( 'template-parts/sections/doctor.php' );
	if ( $template ) {
		set_query_var( 'section', $section );
		load_template( $template, false );
	}

endwhile;

get_footer();
