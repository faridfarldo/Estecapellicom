<?php
/**
 * Seed data for the doctor importer.
 *
 * Each entry becomes one `doctor` post (post_type = doctor) with the Doctor
 * Profile ACF fields filled in. `old_page_path` is the full path of the legacy
 * nested page this doctor replaces; the importer trashes that page after a
 * successful migration so the content lives in exactly one place.
 *
 * `menu_order` controls the ordering on the "Our Doctors" roster grid.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'estecapelli_doctors_seed' ) ) {
	function estecapelli_doctors_seed() {

		// Shared placeholder bio for the surgeons — the editor personalises each
		// one. The bracketed prompts mirror the old page scaffolds.
		$surgeon_bio = static function ( $name ) {
			return sprintf(
				/* translators: %s: doctor name */
				__( '%s is a board-certified plastic, reconstructive and aesthetic surgeon at Estecapelli. He completed his medical degree and specialist surgical training at [university / training hospital] and has built his practice around [main areas — e.g. rhinoplasty, body contouring, breast and facial aesthetics]. He leads each of his operations personally, from the first consultation through surgery to post-operative follow-up, pairing precise surgical technique with a calm, patient-first approach. [Add one or two sentences on his experience, fellowships or special interests.]', 'estecapelli' ),
				$name
			);
		};

		$surgeon_credentials = array(
			__( 'Board-certified — Plastic, Reconstructive & Aesthetic Surgery', 'estecapelli' ),
			__( 'Medical degree — [University, year]', 'estecapelli' ),
			__( 'Specialist training — [Training hospital / department]', 'estecapelli' ),
			__( 'Member — [Professional society]', 'estecapelli' ),
			__( 'Special interests — [e.g. rhinoplasty, body contouring]', 'estecapelli' ),
			__( 'Languages — [Turkish, English]', 'estecapelli' ),
		);

		return array(

			array(
				'slug'          => 'mehmet-hanifi-kutlar',
				'name'          => 'Dr. Mehmet Hanifi Kutlar',
				'position'      => __( 'Medical Director & Co-founder', 'estecapelli' ),
				'bio'           => __( 'Dr. Mehmet Hanifi Kutlar is the Medical Director and co-founder of Estecapelli, overseeing clinical standards across hair restoration, plastic surgery, dental and non-surgical aesthetics. With more than 15 years in aesthetic medicine, he shaped the clinic’s treatment protocols and surgical-quality framework, and personally guides the medical team that delivers them every day. [Add one or two sentences on his background, training and the founding story of Estecapelli.]', 'estecapelli' ),
				'credentials'   => array(
					__( 'Medical Director & Co-founder — Estecapelli', 'estecapelli' ),
					__( '15+ years in aesthetic medicine', 'estecapelli' ),
					__( 'Medical degree — [University, year]', 'estecapelli' ),
					__( 'Member — [Professional society]', 'estecapelli' ),
					__( 'Areas of focus — [e.g. hair restoration, aesthetic medicine]', 'estecapelli' ),
					__( 'Languages — [Turkish, English]', 'estecapelli' ),
				),
				'menu_order'    => 0,
				// Legacy page lived under Medical Director, so its URL changes —
				// inc/redirects.php 301s the old path to the new profile.
				'old_page_path' => 'about-us/medical-director/mehmet-hanifi-kutlar',
			),

			array(
				'slug'          => 'op-dr-hasan-celik',
				'name'          => 'Op. Dr. Hasan Çelik',
				'position'      => __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ),
				'bio'           => $surgeon_bio( __( 'Op. Dr. Hasan Çelik', 'estecapelli' ) ),
				'credentials'   => $surgeon_credentials,
				'menu_order'    => 1,
				'old_page_path' => 'about-us/our-doctors/op-dr-hasan-celik',
			),

			array(
				'slug'          => 'op-dr-mehmet-palali',
				'name'          => 'Op. Dr. Mehmet Palalı',
				'position'      => __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ),
				'bio'           => $surgeon_bio( __( 'Op. Dr. Mehmet Palalı', 'estecapelli' ) ),
				'credentials'   => $surgeon_credentials,
				'menu_order'    => 2,
				'old_page_path' => 'about-us/our-doctors/op-dr-mehmet-palali',
			),

			array(
				'slug'          => 'op-dr-necdet-derici',
				'name'          => 'Op. Dr. Necdet Derici',
				'position'      => __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ),
				'bio'           => $surgeon_bio( __( 'Op. Dr. Necdet Derici', 'estecapelli' ) ),
				'credentials'   => $surgeon_credentials,
				'menu_order'    => 3,
				'old_page_path' => 'about-us/our-doctors/op-dr-necdet-derici',
			),

			array(
				'slug'          => 'op-dr-ali-durmus',
				'name'          => 'Op. Dr. Ali Durmuş',
				'position'      => __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ),
				'bio'           => $surgeon_bio( __( 'Op. Dr. Ali Durmuş', 'estecapelli' ) ),
				'credentials'   => $surgeon_credentials,
				'menu_order'    => 4,
				'old_page_path' => 'about-us/our-doctors/op-dr-ali-durmus',
			),

		);
	}
}
