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
				'slug'             => 'mehmet-hanifi-kutlar',
				'name'             => 'Mehmet Hanifi Kutlar',
				'position'         => __( 'Hair Transplant Specialist & Co-Founder', 'estecapelli' ),
				'bio'              => __( 'Mehmet Hanifi Kutlar is a hair-transplant specialist and co-founder of Estecapelli. Born in Istanbul in 1988, he studied health sciences at Süleyman Demirel, Nişantaşı and Üsküdar Universities and has spent his career at the forefront of hair restoration — training many of the specialists now working in clinics around the world. Alongside Estecapelli he co-founded the medical-travel group Bench Turizm, building an operation that today spans seven countries and serves patients from more than 47 nations, with his work featured across international press.', 'estecapelli' ),
				'credentials'      => array(
					__( 'Co-founder — Estecapelli & Bench Turizm', 'estecapelli' ),
					__( 'Hair-transplant specialist — trainer of specialists worldwide', 'estecapelli' ),
					__( 'Health-sciences education — Süleyman Demirel, Nişantaşı & Üsküdar Universities', 'estecapelli' ),
					__( 'International reach — active in 7 countries, patients from 47+ nations', 'estecapelli' ),
					__( 'Research background — supported by TÜBİTAK', 'estecapelli' ),
				),
				'resume_photo_url' => get_template_directory_uri() . '/assets/images/doctors/kutlar-resume.webp',
				'menu_order'       => 0,
				// Legacy page lived under Medical Director, so its URL changes —
				// inc/redirects.php 301s the old path to the new profile.
				'old_page_path'    => 'about-us/medical-director/mehmet-hanifi-kutlar',
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
				'position'      => __( 'Ear, Nose & Throat (ENT) Specialist', 'estecapelli' ),
				'bio'           => __( 'Op. Dr. Mehmet Palalı completed his medical degree at İstanbul University, Cerrahpaşa Faculty of Medicine, earning the title of Medical Doctor, and went on to complete his specialist training at Ankara Numune Training and Research Hospital, qualifying as an Ear, Nose and Throat (ENT) specialist. Over more than a decade he has practised across leading Turkish hospitals — including Yozgat State Hospital and Medilife Bağcılar Hospital — and today cares for patients at Özel Güneşli Erdem Hospital in Bağcılar, İstanbul, pairing precise surgical technique with a calm, patient-first approach.', 'estecapelli' ),
				'credentials'   => array(
					__( 'ENT Specialist — Otorhinolaryngology (Kulak Burun Boğaz)', 'estecapelli' ),
					__( 'Medical degree — İstanbul University, Cerrahpaşa Faculty of Medicine (2007)', 'estecapelli' ),
					__( 'ENT specialist training — Ankara Numune Training & Research Hospital (2013)', 'estecapelli' ),
					__( '15+ years of clinical experience across leading Turkish hospitals', 'estecapelli' ),
					__( 'Special interests — snoring & sleep apnoea, chronic sinusitis, nasal obstruction, tonsil disorders', 'estecapelli' ),
				),
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
