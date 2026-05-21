<?php
/**
 * Seed data for the page importer.
 *
 * Each entry creates a regular WordPress page (post_type = page) with the
 * correct slug, parent hierarchy, and an ACF page_sections scaffold the
 * editor can customise. Parents must be listed before their children so
 * post_parent resolution succeeds during import.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'estecapelli_pages_seed' ) ) {
	function estecapelli_pages_seed() {

		$whatsapp = function_exists( 'estecapelli_whatsapp_url' ) ? estecapelli_whatsapp_url() : '#';

		/**
		 * Helper: build a minimal hero scaffold so each page renders something
		 * intelligible before the editor designs it properly.
		 */
		$hero = function ( $eyebrow, $title, $lead ) use ( $whatsapp ) {
			return array(
				'acf_fc_layout' => 'hero',
				'eyebrow'       => $eyebrow,
				'title'         => $title,
				'lead'          => $lead,
				'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/contact/' ) ),
				'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
				'media_type'    => 'image',
				'image'         => '',
				'video_id'      => '',
			);
		};

		return array(

			// ======================== About branch ========================
			array(
				'slug'   => 'about-us',
				'title'  => 'About Us',
				'parent' => null,
				'sections' => array(
					$hero( __( 'About Estecapelli', 'estecapelli' ), __( 'Aesthetic excellence, backed by medical trust.', 'estecapelli' ), __( "Estecapelli is an Istanbul-based clinic specialised in hair restoration, plastic surgery, dental treatment and non-surgical aesthetics. We combine board-certified surgeons, hospital-grade facilities and a patient-first philosophy to deliver results our patients trust us for.", 'estecapelli' ) ),
				),
			),
			array(
				'slug'   => 'our-team',
				'title'  => 'Our Team',
				'parent' => 'about-us',
				'sections' => array(
					$hero( __( 'Our Team', 'estecapelli' ), __( 'The people behind every transformation.', 'estecapelli' ), __( 'From your first message to the final follow-up, our multilingual medical, consulting and patient-care team is with you across every step of your journey.', 'estecapelli' ) ),
				),
			),
			array(
				'slug'   => 'our-doctors',
				'title'  => 'Our Doctors',
				'parent' => 'about-us',
				'sections' => array(
					$hero( __( 'Our Doctors', 'estecapelli' ), __( 'Surgeons who lead every procedure.', 'estecapelli' ), __( 'Meet the board-certified surgeons performing every operation at Estecapelli — each one personally selected for their experience, results and patient care.', 'estecapelli' ) ),
				),
			),
			array(
				'slug'    => 'op-dr-hasan-celik',
				'title'   => 'Op. Dr. Hasan Çelik',
				'parent'  => 'our-doctors',
				'sections' => array(
					$hero( __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ), __( 'Op. Dr. Hasan Çelik', 'estecapelli' ), __( 'Board-certified plastic and reconstructive surgeon at Estecapelli.', 'estecapelli' ) ),
				),
			),
			array(
				'slug'    => 'op-dr-mehmet-palali',
				'title'   => 'Op. Dr. Mehmet Palalı',
				'parent'  => 'our-doctors',
				'sections' => array(
					$hero( __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ), __( 'Op. Dr. Mehmet Palalı', 'estecapelli' ), __( 'Board-certified plastic and reconstructive surgeon at Estecapelli.', 'estecapelli' ) ),
				),
			),
			array(
				'slug'    => 'op-dr-necdet-derici',
				'title'   => 'Op. Dr. Necdet Derici',
				'parent'  => 'our-doctors',
				'sections' => array(
					$hero( __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ), __( 'Op. Dr. Necdet Derici', 'estecapelli' ), __( 'Board-certified plastic and reconstructive surgeon at Estecapelli.', 'estecapelli' ) ),
				),
			),
			array(
				'slug'    => 'op-dr-ali-durmus',
				'title'   => 'Op. Dr. Ali Durmuş',
				'parent'  => 'our-doctors',
				'sections' => array(
					$hero( __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ), __( 'Op. Dr. Ali Durmuş', 'estecapelli' ), __( 'Board-certified plastic and reconstructive surgeon at Estecapelli.', 'estecapelli' ) ),
				),
			),
			array(
				'slug'   => 'medical-director',
				'title'  => 'Medical Director',
				'parent' => 'about-us',
				'sections' => array(
					$hero( __( 'Medical Direction', 'estecapelli' ), __( 'Led by our medical director.', 'estecapelli' ), __( 'Every clinical decision at Estecapelli is overseen by our medical director, ensuring consistent standards across hair restoration, plastic surgery, dental and non-surgical care.', 'estecapelli' ) ),
				),
			),
			array(
				'slug'    => 'mehmet-hanifi-kutlar',
				'title'   => 'Dr. Mehmet Hanifi Kutlar',
				'parent'  => 'medical-director',
				'sections' => array(
					$hero( __( 'Medical Director & Co-founder', 'estecapelli' ), __( 'Dr. Mehmet Hanifi Kutlar', 'estecapelli' ), __( '15+ years in aesthetic medicine, leading the clinical standards behind every Estecapelli treatment.', 'estecapelli' ) ),
				),
			),

			// ======================== Category landings ========================
			array(
				'slug'   => 'hair-transplant',
				'title'  => 'Hair Transplant',
				'parent' => null,
				'sections' => array(
					$hero( __( 'Hair Transplant', 'estecapelli' ), __( 'Hair restoration, engineered for natural density.', 'estecapelli' ), __( 'From sapphire-blade FUE to our patented exosome and VITA protocols, every Estecapelli hair-transplant plan is built around your unique scalp, donor area and goals.', 'estecapelli' ) ),
				),
			),
			array(
				'slug'   => 'plastic-surgery',
				'title'  => 'Plastic Surgery',
				'parent' => null,
				'sections' => array(
					$hero( __( 'Plastic Surgery', 'estecapelli' ), __( 'Aesthetic surgery, planned around you.', 'estecapelli' ), __( 'Rhinoplasty, BBL, breast aesthetics, face & neck lift and more — performed by board-certified surgeons in hospital-grade facilities.', 'estecapelli' ) ),
				),
			),
			array(
				'slug'   => 'dental-treatment',
				'title'  => 'Dental Treatment',
				'parent' => null,
				'sections' => array(
					$hero( __( 'Dental Treatment', 'estecapelli' ), __( 'Dental care with a personal touch.', 'estecapelli' ), __( 'From single-tooth dental implants to a full Hollywood smile makeover, every plan is built around the look you want and the bite you need.', 'estecapelli' ) ),
				),
			),

			// ======================== Gallery ========================
			array(
				'slug'   => 'before-after',
				'title'  => 'Before & After',
				'parent' => null,
				'sections' => array(
					$hero( __( 'Before & After', 'estecapelli' ), __( 'Real results, from real patients.', 'estecapelli' ), __( 'Browse before-and-after composites from our hair-transplant, plastic-surgery and dental treatments. Every image represents a patient who trusted us with their transformation.', 'estecapelli' ) ),
				),
			),

			// ======================== Contact ========================
			array(
				'slug'   => 'contact',
				'title'  => 'Contact',
				'parent' => null,
				'sections' => array(
					$hero( __( 'Get in Touch', 'estecapelli' ), __( "We're here to answer your questions.", 'estecapelli' ), __( 'Reach our team by WhatsApp, phone or email — or schedule a free online consultation with one of our medical consultants.', 'estecapelli' ) ),
				),
			),

		);
	}
}
