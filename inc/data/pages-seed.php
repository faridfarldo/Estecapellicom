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
				'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
				'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
				'media_type'    => 'image',
				'image'         => '',
				'video_id'      => '',
			);
		};

		// Doctor profiles are no longer pages — each surgeon is a `doctor` post
		// (see inc/data/doctors-seed.php and the Doctors menu). The "Our Doctors"
		// roster grid pulls them in automatically.

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
					array(
						'acf_fc_layout' => 'team',
						'eyebrow'       => __( 'Clinical Care', 'estecapelli' ),
						'title'         => __( 'Medical Team', 'estecapelli' ),
						'lead'          => __( 'The nurses and technicians who care for you before, during and after every procedure.', 'estecapelli' ),
						'variant'       => 'medical',
						'members'       => array(
							array( 'photo' => '', 'name' => __( 'Aylin Y.', 'estecapelli' ), 'role' => __( 'Registered Nurse', 'estecapelli' ),    'languages' => array() ),
							array( 'photo' => '', 'name' => __( 'Selin K.', 'estecapelli' ), 'role' => __( 'Operating Room Nurse', 'estecapelli' ), 'languages' => array() ),
							array( 'photo' => '', 'name' => __( 'Burak T.', 'estecapelli' ), 'role' => __( 'Surgical Technician', 'estecapelli' ),   'languages' => array() ),
							array( 'photo' => '', 'name' => __( 'Derya A.', 'estecapelli' ), 'role' => __( 'Anaesthesia Nurse', 'estecapelli' ),     'languages' => array() ),
							array( 'photo' => '', 'name' => __( 'Emre S.', 'estecapelli' ),  'role' => __( 'Patient Care Nurse', 'estecapelli' ),    'languages' => array() ),
							array( 'photo' => '', 'name' => __( 'Zeynep M.', 'estecapelli' ),'role' => __( 'Recovery Nurse', 'estecapelli' ),        'languages' => array() ),
						),
					),
					array(
						'acf_fc_layout' => 'team',
						'eyebrow'       => __( 'Your First Point of Contact', 'estecapelli' ),
						'title'         => __( 'Medical Consultants', 'estecapelli' ),
						'lead'          => __( 'Multilingual consultants who guide your journey in your own language — from first question to final result.', 'estecapelli' ),
						'variant'       => 'consultant',
						'members'       => array(
							array( 'photo' => '', 'name' => __( 'Sara', 'estecapelli' ),   'role' => '', 'languages' => array(
								array( 'country' => 'gb', 'label' => __( 'English', 'estecapelli' ) ),
								array( 'country' => 'sa', 'label' => __( 'Arabic', 'estecapelli' ) ),
								array( 'country' => 'tr', 'label' => __( 'Turkish', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'name' => __( 'Daniel', 'estecapelli' ), 'role' => '', 'languages' => array(
								array( 'country' => 'gb', 'label' => __( 'English', 'estecapelli' ) ),
								array( 'country' => 'de', 'label' => __( 'German', 'estecapelli' ) ),
								array( 'country' => 'fr', 'label' => __( 'French', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'name' => __( 'Maria', 'estecapelli' ),  'role' => '', 'languages' => array(
								array( 'country' => 'es', 'label' => __( 'Spanish', 'estecapelli' ) ),
								array( 'country' => 'gb', 'label' => __( 'English', 'estecapelli' ) ),
								array( 'country' => 'it', 'label' => __( 'Italian', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'name' => __( 'Olga', 'estecapelli' ),   'role' => '', 'languages' => array(
								array( 'country' => 'ru', 'label' => __( 'Russian', 'estecapelli' ) ),
								array( 'country' => 'gb', 'label' => __( 'English', 'estecapelli' ) ),
								array( 'country' => 'tr', 'label' => __( 'Turkish', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'name' => __( 'Leyla', 'estecapelli' ),  'role' => '', 'languages' => array(
								array( 'country' => 'tr', 'label' => __( 'Turkish', 'estecapelli' ) ),
								array( 'country' => 'sa', 'label' => __( 'Arabic', 'estecapelli' ) ),
								array( 'country' => 'gb', 'label' => __( 'English', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'name' => __( 'Sophie', 'estecapelli' ), 'role' => '', 'languages' => array(
								array( 'country' => 'fr', 'label' => __( 'French', 'estecapelli' ) ),
								array( 'country' => 'gb', 'label' => __( 'English', 'estecapelli' ) ),
								array( 'country' => 'de', 'label' => __( 'German', 'estecapelli' ) ),
							) ),
						),
					),
				),
			),
			array(
				'slug'   => 'our-doctors',
				'title'  => 'Our Doctors',
				'parent' => 'about-us',
				'sections' => array(
					$hero( __( 'Our Doctors', 'estecapelli' ), __( 'Surgeons who lead every procedure.', 'estecapelli' ), __( 'Meet the board-certified surgeons performing every operation at Estecapelli — each one personally selected for their experience, results and patient care.', 'estecapelli' ) ),
					array(
						'acf_fc_layout' => 'doctors',
						'eyebrow'       => __( 'Board-Certified Surgeons', 'estecapelli' ),
						'title'         => __( 'Meet the surgeons', 'estecapelli' ),
						'lead'          => __( 'Each operation is led personally by one of our specialist surgeons. Open any résumé to read their training, credentials and areas of expertise.', 'estecapelli' ),
						'members'       => array(
							array(
								'photo'      => '',
								'name'       => __( 'Dr. Mehmet Hanifi Kutlar', 'estecapelli' ),
								'position'   => __( 'Medical Director & Co-founder', 'estecapelli' ),
								'resume_url' => home_url( '/en/about-us/medical-director/mehmet-hanifi-kutlar' ),
							),
							array(
								'photo'      => '',
								'name'       => __( 'Op. Dr. Hasan Çelik', 'estecapelli' ),
								'position'   => __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ),
								'resume_url' => home_url( '/en/about-us/our-doctors/op-dr-hasan-celik' ),
							),
							array(
								'photo'      => '',
								'name'       => __( 'Op. Dr. Mehmet Palalı', 'estecapelli' ),
								'position'   => __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ),
								'resume_url' => home_url( '/en/about-us/our-doctors/op-dr-mehmet-palali' ),
							),
							array(
								'photo'      => '',
								'name'       => __( 'Op. Dr. Necdet Derici', 'estecapelli' ),
								'position'   => __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ),
								'resume_url' => home_url( '/en/about-us/our-doctors/op-dr-necdet-derici' ),
							),
							array(
								'photo'      => '',
								'name'       => __( 'Op. Dr. Ali Durmuş', 'estecapelli' ),
								'position'   => __( 'Plastic & Reconstructive Surgeon', 'estecapelli' ),
								'resume_url' => home_url( '/en/about-us/our-doctors/op-dr-ali-durmus' ),
							),
						),
					),
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

			// ======================== Category landings ========================
			array(
				'slug'   => 'hair-transplant',
				'title'  => 'Hair Transplant',
				'parent' => null,
				'sections' => array(
					$hero( __( 'Hair Transplant', 'estecapelli' ), __( 'Hair restoration, engineered for natural density.', 'estecapelli' ), __( 'From sapphire-blade FUE to our patented exosome and VITA protocols, every Estecapelli hair-transplant plan is built around your unique scalp, donor area and goals.', 'estecapelli' ) ),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Where to Begin', 'estecapelli' ),
						'title'         => __( 'A Hair Transplant Planned Entirely Around You', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Hair loss is rarely the same from one person to the next, so neither is the right solution. At Estecapelli, every plan starts with a detailed assessment of your scalp, donor area, hair characteristics and the density you want to achieve — never a one-size-fits-all package. From this we recommend the technique, the graft count and the approach that will give you the most natural result.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Our surgeons work exclusively in hospital-grade facilities using the gold-standard FUE and DHI methods, enhanced by our own Sapphire, Exosome and VITA protocols. Whether you are restoring a receding hairline, adding density, or shaping eyebrows and a beard, the goal is always the same: results that look like they were never anything but your own.', 'estecapelli' ) . '</p>',
						'image'          => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'quick_stats',
						'eyebrow'       => __( 'Why Patients Choose Estecapelli', 'estecapelli' ),
						'stats'         => array(
							array( 'icon' => 'sparkles',     'value' => __( '6 techniques', 'estecapelli' ), 'label' => __( 'FUE, DHI, Sapphire, Exosome, VITA & more', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'value' => __( 'Local', 'estecapelli' ),        'label' => __( 'Anaesthesia — awake and comfortable', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'value' => __( '~3 days', 'estecapelli' ),       'label' => __( 'Typical stay in Turkey', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'value' => __( 'Permanent', 'estecapelli' ),     'label' => __( 'Grafts resistant to future loss', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Our Treatments', 'estecapelli' ),
						'title'         => __( 'Explore Hair Transplant Treatments', 'estecapelli' ),
						'count'         => 12,
						'category'      => 'hair-transplant',
						'manual'        => array(),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Hair Transplant — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the natural density and hairlines our patients achieve. Every result is designed around the individual growth pattern for a look that is completely natural.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Find Out Which Hair Transplant Is Right for You', 'estecapelli' ),
						'lead'          => __( 'Send us a few photos and our medical team will reply with the recommended technique, a graft estimate and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free graft assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Hair Transplant — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Which hair transplant technique is best for me?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'There is no single best technique — the right choice depends on your degree of hair loss, the quality and size of your donor area, the area to be covered and your lifestyle. FUE and DHI are both gold-standard methods, while our Sapphire, Exosome and VITA protocols add specific advantages for healing, density or graft survival. After reviewing your photos, our surgeons recommend the method that will give you the most natural, lasting result for your case.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Am I a good candidate for a hair transplant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most adults with stable hair loss and a healthy donor area at the back and sides of the scalp are suitable, including men with male-pattern baldness and many women with thinning hair. Good general health matters more than age. A free consultation with photos lets our team confirm your suitability and estimate the number of grafts you would need.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is the procedure painful, and what anaesthesia is used?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The procedure is performed under local anaesthesia, so you remain awake but feel no pain during the session. Most patients describe only mild discomfort when the anaesthetic is first applied. A session typically lasts six to eight hours depending on the number of grafts, and you return to your hotel the same day.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long should I stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most patients plan for around three days in Turkey. This covers the consultation and procedure day, a first wash and a personal aftercare briefing with our team before you fly home. We help coordinate your hotel and airport transfers so the whole trip is as smooth as possible.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see results, and are they permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Transplanted hairs usually shed in the first few weeks, which is completely normal. New growth begins around months three to four, noticeable density develops by six to nine months, and the final result matures at about twelve months. Because the grafts are taken from areas genetically resistant to hair loss, they continue to grow permanently — you can cut, wash and style your hair as usual.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will the result look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The most important factors for a natural look are hairline design and the angle, direction and density of each implanted follicle. Our surgeons design your hairline with you in advance and implant grafts one by one to match your natural growth pattern, so the result blends seamlessly with your existing hair and looks like it was always there.', 'estecapelli' ) . '</p>' ),
						),
					),
				),
			),
			array(
				'slug'   => 'plastic-surgery',
				'title'  => 'Plastic Surgery',
				'parent' => null,
				'sections' => array(
					$hero( __( 'Plastic Surgery', 'estecapelli' ), __( 'Aesthetic surgery, planned around you.', 'estecapelli' ), __( 'Rhinoplasty, BBL, breast aesthetics, face & neck lift and more — performed by board-certified surgeons in hospital-grade facilities.', 'estecapelli' ) ),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Our Approach', 'estecapelli' ),
						'title'         => __( 'Aesthetic Surgery, Safely and Naturally Done', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Plastic surgery is a deeply personal decision, and the right result is one that looks like you — balanced, natural and in proportion — not overdone. At Estecapelli, every procedure is carried out by board-certified plastic surgeons in fully accredited, hospital-grade facilities, with the anaesthesia, monitoring and post-operative care you would expect from a leading clinic.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'From rhinoplasty and facial rejuvenation to body contouring, breast aesthetics and bariatric surgery, your journey begins with an honest consultation. We discuss what is realistically achievable, plan the procedure around your anatomy and goals, and guide you through recovery with a dedicated patient-care team that speaks your language.', 'estecapelli' ) . '</p>',
						'image'          => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'quick_stats',
						'eyebrow'       => __( 'Why Patients Choose Estecapelli', 'estecapelli' ),
						'stats'         => array(
							array( 'icon' => 'shield-check', 'value' => __( 'Board-certified', 'estecapelli' ), 'label' => __( 'Experienced plastic surgeons', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'value' => __( 'Hospital-grade', 'estecapelli' ),  'label' => __( 'Accredited, fully equipped facilities', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'value' => __( 'End-to-end', 'estecapelli' ),      'label' => __( 'Care from first message to recovery', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'value' => __( 'All-inclusive', 'estecapelli' ),   'label' => __( 'Transparent, no-obligation quotes', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Our Treatments', 'estecapelli' ),
						'title'         => __( 'Explore Plastic Surgery Procedures', 'estecapelli' ),
						'count'         => 12,
						'category'      => 'plastic-surgery',
						'manual'        => array(),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Plastic Surgery — Before & After', 'estecapelli' ),
						'lead'          => __( 'Browse before-and-after results from real patients. Every transformation is planned around the individual for a natural, balanced outcome.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Plan Your Procedure with Our Surgeons', 'estecapelli' ),
						'lead'          => __( 'Tell us what you would like to achieve and our medical team will reply with a personalised plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'shield-check', 'label' => __( 'Assessment by board-certified surgeons', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Realistic, honest expectations from the start', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Plastic Surgery — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'How do I know which procedure is right for me?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It starts with a consultation. We listen to what bothers you and what you would like to change, assess your anatomy, and explain which procedure — or combination — will achieve a natural, balanced result. We are equally clear about what is not advisable, because the right plan is the one that is safe and realistic for you, not simply the one you ask for.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the surgeons qualified and the facilities safe?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. All procedures are performed by board-certified plastic surgeons in accredited, hospital-grade facilities with full anaesthesia and monitoring. Your safety is assessed before surgery with the necessary tests and a review of your medical history, and you are cared for throughout your stay by qualified medical staff.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What type of anaesthesia is used?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most plastic surgery procedures are performed under general anaesthesia administered by a specialist anaesthetist, while some smaller treatments can be done under sedation or local anaesthesia. The right option is confirmed during your pre-operative assessment based on the procedure and your health.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is the recovery and how long should I stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Recovery depends on the procedure. Many patients stay in Turkey for roughly five to ten days so we can monitor early healing and remove any sutures or drains before they travel. We give you a clear, written recovery timeline and remain available for follow-up questions after you return home.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will there be visible scars?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Some surgical procedures leave scars, but our surgeons place incisions in discreet, naturally hidden locations wherever possible and use careful closure techniques to keep them as fine as possible. Scars typically fade significantly over the following months, and we provide guidance on aftercare to help them heal well.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Many results, such as rhinoplasty or breast and body contouring, are long-lasting. However, natural ageing, significant weight changes and lifestyle still affect your body over time. Maintaining a stable weight and a healthy routine helps preserve your result for as long as possible, and we advise you on this during your consultation.', 'estecapelli' ) . '</p>' ),
						),
					),
				),
			),
			array(
				'slug'   => 'dental-treatment',
				'title'  => 'Dental Treatment',
				'parent' => null,
				'sections' => array(
					$hero( __( 'Dental Treatment', 'estecapelli' ), __( 'Dental care with a personal touch.', 'estecapelli' ), __( 'From single-tooth dental implants to a full Hollywood smile makeover, every plan is built around the look you want and the bite you need.', 'estecapelli' ) ),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Our Approach', 'estecapelli' ),
						'title'         => __( 'A Healthy Bite and a Smile You Love', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A great smile is about more than appearance — it depends on healthy teeth, gums and a bite that works. At Estecapelli, treatment begins with a full oral assessment so that aesthetics and function are planned together, whether you need to replace a missing tooth or completely redesign your smile.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Our dental team combines modern digital planning with high-quality materials to deliver natural-looking, durable results. From dental implants that restore the strength of a natural tooth to a tailored Hollywood Smile, every plan is built around the look you want and the bite you need — and explained clearly before any treatment begins.', 'estecapelli' ) . '</p>',
						'image'          => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'quick_stats',
						'eyebrow'       => __( 'Why Patients Choose Estecapelli', 'estecapelli' ),
						'stats'         => array(
							array( 'icon' => 'sparkles',     'value' => __( 'Digital', 'estecapelli' ),       'label' => __( 'Smile design planned before you start', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'value' => __( 'Premium', 'estecapelli' ),       'label' => __( 'High-quality, long-lasting materials', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'value' => __( 'Natural', 'estecapelli' ),       'label' => __( 'Results matched to your face and bite', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'value' => __( 'All-inclusive', 'estecapelli' ),  'label' => __( 'Transparent, no-obligation quotes', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Our Treatments', 'estecapelli' ),
						'title'         => __( 'Explore Dental Treatments', 'estecapelli' ),
						'count'         => 12,
						'category'      => 'dental-treatment',
						'manual'        => array(),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Dental Treatment — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the smiles our patients leave with. Every result is designed to look natural and to suit the individual face, lips and bite.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Design Your New Smile', 'estecapelli' ),
						'lead'          => __( 'Send us a photo of your smile and our dental team will reply with a personalised treatment plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free smile assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Dental Treatment — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'What is the difference between dental implants and a Hollywood Smile?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A dental implant replaces a missing tooth from the root up, restoring full chewing strength with a titanium post and a crown. A Hollywood Smile is a cosmetic makeover that redesigns the appearance of your visible teeth using veneers or crowns for a brighter, more even look. Many patients combine the two — implants to restore missing teeth and veneers to perfect the smile.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does dental treatment take, and how many trips are needed?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A Hollywood Smile with veneers or crowns can usually be completed in a single trip of about five to seven days. Dental implants involve a healing period after the implant is placed, so they often require two visits a few months apart, or a planning consultation followed by the final restoration. We map out your exact schedule before you travel.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is dental treatment painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Treatment is carried out under local anaesthesia, so you should not feel pain during the procedure. Mild sensitivity or tenderness afterwards is normal and easily managed with simple pain relief. Our team makes sure you are comfortable at every stage and explains exactly what to expect.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will my new teeth look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. We use digital smile design to plan the shape, size and shade of your teeth in proportion to your lips, gums and face, so the result suits you rather than looking artificial. You can review and adjust the plan before any permanent work is done, ensuring you are happy with the final look.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long do the results last?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Dental implants are designed to last for decades — often a lifetime — with good oral hygiene and regular check-ups. High-quality veneers and crowns typically last many years before they may need replacing. Looking after your teeth and gums and attending routine dental visits is the best way to protect your investment.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Am I a suitable candidate?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most people are suitable, but the right treatment depends on the health of your teeth and gums and the amount of natural bone available for implants. A consultation with photos — and, where needed, an X-ray taken on arrival — allows our dental team to confirm the best plan for you and flag anything that needs treating first.', 'estecapelli' ) . '</p>' ),
						),
					),
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

			// ======================== Blog ========================
			array(
				'slug'   => 'blog',
				'title'  => 'Blog',
				'parent' => null,
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
