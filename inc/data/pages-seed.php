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

		// -------------------------------------------------------------------
		// Legal pages. Plain WYSIWYG pages (no ACF sections), rendered through
		// the page.php prose fallback. The text below is a starting template
		// tailored to a medical clinic in Türkiye serving international
		// patients — HAVE IT REVIEWED BY A QUALIFIED LAWYER and replace every
		// [bracketed] placeholder before relying on it.
		// -------------------------------------------------------------------
		$privacy_content =
			  '<p><em>' . esc_html__( 'Last updated: June 2026', 'estecapelli' ) . '</em></p>'
			. '<p>' . esc_html__( 'This Privacy Policy explains how Estecapelli collects, uses, stores and protects your personal data when you visit our website, contact us, or receive a consultation or treatment with us. Estecapelli is based in Istanbul, Türkiye and cares for patients from around the world. We are committed to handling your data lawfully, transparently and securely.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '1. Who we are', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'Estecapelli is a hair restoration, plastic surgery, dental and aesthetic clinic operating in Istanbul, Türkiye. For the purposes of data protection law, the data controller is [registered legal entity name], [registered address]. You can reach us about any privacy matter using the details in the Contact us section below.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '2. The information we collect', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'Depending on how you interact with us, we may collect:', 'estecapelli' ) . '</p>'
			. '<ul>'
			. '<li>' . esc_html__( 'Contact details — your name, phone number, email address, country and preferred language.', 'estecapelli' ) . '</li>'
			. '<li>' . esc_html__( 'Health information you choose to share — photographs of the treatment area, medical history, current medications and other details you send so our medical team can assess your suitability and prepare a treatment plan.', 'estecapelli' ) . '</li>'
			. '<li>' . esc_html__( 'Booking and travel details — appointment dates and the information needed to coordinate your visit.', 'estecapelli' ) . '</li>'
			. '<li>' . esc_html__( 'Technical data — IP address, browser type, device information and cookie data collected automatically when you use our website.', 'estecapelli' ) . '</li>'
			. '</ul>'

			. '<h2>' . esc_html__( '3. How we use your information', 'estecapelli' ) . '</h2>'
			. '<ul>'
			. '<li>' . esc_html__( 'To respond to your enquiries and provide a personalised assessment, treatment plan and quote.', 'estecapelli' ) . '</li>'
			. '<li>' . esc_html__( 'To plan, deliver and follow up on your treatment and aftercare.', 'estecapelli' ) . '</li>'
			. '<li>' . esc_html__( 'To coordinate appointments, travel and accommodation related to your visit.', 'estecapelli' ) . '</li>'
			. '<li>' . esc_html__( 'To improve our website and services, and — only where you have agreed — to send you relevant information.', 'estecapelli' ) . '</li>'
			. '<li>' . esc_html__( 'To meet our legal, medical and regulatory obligations.', 'estecapelli' ) . '</li>'
			. '</ul>'

			. '<h2>' . esc_html__( '4. Legal basis for processing', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'We process your personal data under the Turkish Personal Data Protection Law (KVKK) and, where it applies, the EU/UK General Data Protection Regulation (GDPR). Our legal bases are your consent, the performance of a contract or steps taken at your request, our legitimate interests in running the clinic, and compliance with legal obligations. Health information is a special category of data: we process it only with your explicit consent or where otherwise permitted for the provision of healthcare.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '5. Sharing your information', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'We do not sell your personal data. We share it only as needed with: our medical and patient-care team; the surgeons, hospitals and clinics involved in your treatment; and trusted service providers (for example, secure hosting, communication and travel coordination) acting on our instructions. We may also disclose data where required by law.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '6. International transfers', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'Because we treat patients from many countries, your data may be transferred to and processed in Türkiye and other countries. Where we transfer data internationally, we take appropriate safeguards to protect it in line with applicable data protection law.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '7. How long we keep your data', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'We keep your personal data only for as long as necessary for the purposes described above, including any medical, legal or regulatory retention periods that apply to healthcare records. When data is no longer needed, we securely delete or anonymise it.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '8. Your rights', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'Subject to applicable law, you have the right to access your data; to have inaccurate data corrected; to request deletion; to restrict or object to certain processing; to data portability; and to withdraw consent at any time without affecting processing already carried out. You also have the right to complain to the relevant data protection authority. To exercise any of these rights, contact us using the details below.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '9. Cookies', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'Our website uses cookies and similar technologies to make the site work, to remember your preferences and to understand how the site is used. You can control cookies through your browser settings; disabling some cookies may affect how the site functions.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '10. Security', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'We use appropriate technical and organisational measures to protect your personal data against loss, misuse and unauthorised access. No method of transmission or storage is completely secure, but we work to safeguard your information at all times.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '11. Children', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'Our services are intended for adults. We do not knowingly collect data from children without the consent of a parent or legal guardian.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '12. Changes to this policy', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'We may update this Privacy Policy from time to time. The latest version will always be available on this page, with the date it was last updated shown at the top.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '13. Contact us', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'If you have any questions about this policy or how we handle your data, contact us at', 'estecapelli' ) . ' <a href="mailto:info@estecapelli.com">info@estecapelli.com</a> ' . esc_html__( 'or via our Contact page.', 'estecapelli' ) . '</p>';

		$terms_content =
			  '<p><em>' . esc_html__( 'Last updated: June 2026', 'estecapelli' ) . '</em></p>'
			. '<p>' . esc_html__( 'These Terms govern your use of the Estecapelli website and the consultations, quotes and information we provide through it. By using this website or contacting us, you agree to these Terms. If you do not agree, please do not use the site.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '1. About these terms', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'This website is operated by Estecapelli, a clinic based in Istanbul, Türkiye. The operator is [registered legal entity name], [registered address]. We may update these Terms from time to time; the current version always applies.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '2. Not medical advice', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'The content on this website is provided for general information only and is not medical advice. It is not a substitute for a professional consultation, diagnosis or examination. Any treatment recommendation can only be confirmed after a personal assessment by our medical team. Always seek the advice of a qualified healthcare professional regarding your individual circumstances.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '3. Consultations and quotes', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'Online assessments, graft estimates and price quotes provided before your visit are preliminary and based on the information and photographs you give us. They are indicative only and may be adjusted after an in-person examination. A quote does not constitute a binding offer until confirmed in writing.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '4. Bookings and payments', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'Booking, deposit, payment and cancellation terms are provided to you separately before any treatment is confirmed [insert your booking, deposit and cancellation policy here]. Please make sure you have read and understood them before making a payment.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '5. Results and individual variation', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'Medical and aesthetic outcomes vary from person to person and cannot be guaranteed. Before-and-after images on this website show real individual results and are not a promise of the outcome you will achieve. Your result depends on factors including your physiology, the treatment plan and following the recommended aftercare.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '6. Intellectual property', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'All content on this website — including text, images, logos, before-and-after photographs and design — is owned by or licensed to Estecapelli and is protected by intellectual property law. You may not copy, reproduce or distribute it without our written permission.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '7. Information and photos you send us', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'When you send us information or photographs, you confirm they are accurate and that you have the right to share them. We handle this information in line with our Privacy Policy. Please do not send information that is not yours to share.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '8. Third-party links', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'Our website may contain links to third-party sites and services. We are not responsible for their content or privacy practices, and a link does not imply our endorsement.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '9. Limitation of liability', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'To the fullest extent permitted by law, Estecapelli is not liable for any loss arising from reliance on the general information on this website. Nothing in these Terms limits any liability that cannot be limited under applicable law.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '10. Governing law', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'These Terms are governed by the laws of the Republic of Türkiye, and the courts of Istanbul shall have jurisdiction, without prejudice to any mandatory consumer protection rights you may have in your country of residence.', 'estecapelli' ) . '</p>'

			. '<h2>' . esc_html__( '11. Contact us', 'estecapelli' ) . '</h2>'
			. '<p>' . esc_html__( 'For any questions about these Terms, contact us at', 'estecapelli' ) . ' <a href="mailto:info@estecapelli.com">info@estecapelli.com</a> ' . esc_html__( 'or via our Contact page.', 'estecapelli' ) . '</p>';

		return array(

			// ======================== TrichoLab (AI hair analysis) ========================
			array(
				'slug'   => 'tricholab',
				'title'  => 'TrichoLab',
				'parent' => null,
				'sections' => array(
					$hero( __( 'AI-Powered Hair Analysis', 'estecapelli' ), __( 'TrichoLab', 'estecapelli' ), __( 'An advanced AI-powered hair analysis system that examines the hair and scalp through high-resolution trichoscopic imaging — turning your scalp into precise data that drives a fully personalised transplant plan.', 'estecapelli' ) ),

					array(
						'acf_fc_layout'  => 'intro',
						'eyebrow'        => __( 'The Technology', 'estecapelli' ),
						'title'          => __( 'What is TrichoLab?', 'estecapelli' ),
						'body'           => '<p>' . esc_html__( 'TrichoLab is an advanced AI-powered hair analysis system that examines the hair and scalp through high-resolution trichoscopic imaging. By measuring critical parameters with millimetric precision, it provides objective, data-driven insights that guide our specialists in planning the most effective treatment for each individual.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'The system evaluates a comprehensive range of factors, including hair follicle density, graft potential, hair shaft thickness, donor area capacity, hair loss mapping and miniaturisation rate. This level of detail allows our team to design a personalised treatment plan based on accurate, real-time data rather than estimation.', 'estecapelli' ) . '</p>',
						'image'          => '',
						'image_position' => 'right',
						'cta'            => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'Hair Analysis with TrichoLab at Estecapelli', 'estecapelli' ),
						'lead'          => __( 'From high-resolution imaging to a precise, personalised plan. Swipe through the steps below.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'target',    'eyebrow' => __( 'Step 1', 'estecapelli' ), 'title' => __( 'Digital Trichoscopic Scanning', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'The scalp is examined with high-resolution micro-imaging, capturing detailed visuals of the hair follicles.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'atom',      'eyebrow' => __( 'Step 2', 'estecapelli' ), 'title' => __( 'AI-Powered Data Analysis', 'estecapelli' ),     'body' => '<p>' . esc_html__( 'Advanced algorithms turn the imaging data into precise numerical values for density and follicular distribution.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'clipboard', 'eyebrow' => __( 'Step 3', 'estecapelli' ), 'title' => __( 'Personalised Treatment Plan', 'estecapelli' ),   'body' => '<p>' . esc_html__( 'The data forms the basis of a fully personalised plan, including the exact number of grafts required.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why It Matters', 'estecapelli' ),
						'title'         => __( 'The Importance of TrichoLab in Transplant Planning', 'estecapelli' ),
						'body'          => __( 'Scientific analysis protects your donor area and ensures natural, symmetrical results:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'atom',         'label' => __( 'Scientific assessment of donor area capacity — no overharvesting', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Accurate graft calculation, eliminating under- and over-planning', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Natural, symmetrical design based on your existing hair', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Long-term protection of the donor area', 'estecapelli' ) ),
							array( 'icon' => 'star',         'label' => __( 'Available in only a limited number of clinics in Turkey', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout'  => 'intro',
						'eyebrow'        => __( 'Why Estecapelli', 'estecapelli' ),
						'title'          => __( 'Why TrichoLab at Estecapelli?', 'estecapelli' ),
						'body'           => '<p>' . esc_html__( 'TrichoLab is an advanced analysis system available in only a limited number of clinics across Turkey, and at Estecapelli it sits at the core of everything we do, giving our patients access to a level of analysis that few clinics can offer.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Every evaluation is objective, measurable and fully transparent. A truly personalised transplant strategy is developed for each patient, built on accurate data rather than guesswork, protecting the donor area and maximising long-term results.', 'estecapelli' ) . '</p>',
						'image'          => '',
						'image_position' => 'left',
						'cta'            => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'TrichoLab — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Is TrichoLab analysis painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Not at all. It is a non-invasive imaging process that simply scans the scalp — no needles, no discomfort.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Why does TrichoLab matter for my transplant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It calculates the exact number of grafts you need and protects your donor area, so the plan is accurate, natural and sustainable.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What exactly is TrichoLab analysis?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'TrichoLab is an advanced hair analysis system that examines the hair and scalp with high-resolution imaging and AI-supported software, measuring hair density, hair strand thickness, donor area capacity and shedding pattern.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does TrichoLab determine the exact number of grafts?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'TrichoLab strengthens planning by supporting the graft requirement with scientific data. The final decision is made together with clinical factors such as expert assessment and hairline design.', 'estecapelli' ) . '</p>' ),
						),
					),
				),
			),

			// ======================== Pre Hair Transplant ========================
			array(
				'slug'   => 'pre-hair-transplant-period',
				'title'  => 'Pre Hair Transplant Period',
				'parent' => 'hair-transplant',
				'sections' => array(
					$hero( __( 'Before Your Procedure', 'estecapelli' ), __( 'Preparing for Your Hair Transplant', 'estecapelli' ), __( 'The right preparation is one of the biggest factors in a successful hair transplant. From a thorough medical evaluation to small day-of details, here is everything you need to do before your procedure at Estecapelli.', 'estecapelli' ) ),

					array(
						'acf_fc_layout'  => 'intro',
						'eyebrow'        => __( 'The Preparation Phase', 'estecapelli' ),
						'title'          => __( 'Why the Pre-Transplant Period Matters', 'estecapelli' ),
						'body'           => '<p>' . esc_html__( 'The pre-hair-transplant period covers all the essential steps taken before the procedure begins. It includes a thorough medical evaluation, scalp analysis and determining the most suitable technique for you.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'The donor area is assessed and the hairline design is carefully planned to achieve natural-looking results. Proper preparation during this period plays a key role in the overall success of the transplant.', 'estecapelli' ) . '</p>',
						'image'          => '',
						'image_position' => 'right',
						'cta'            => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'Your Pre-Op Checklist', 'estecapelli' ),
						'title'         => __( 'How to Prepare for Your Hair Transplant', 'estecapelli' ),
						'lead'          => __( 'Follow these steps in the days leading up to your procedure. Swipe through each one below.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'eyebrow' => __( 'Step 1', 'estecapelli' ), 'title' => __( 'Your Health Status & Medications', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Share any existing conditions — high blood pressure, diabetes, heart disease, hormonal disorders or other chronic illnesses — and list every medication you take regularly. Never stop a medication, including blood thinners such as Aspirin, without your doctor’s approval.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'check-circle', 'eyebrow' => __( 'Step 2', 'estecapelli' ), 'title' => __( 'Alcohol, Smoking & Substances', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Stop drinking alcohol at least 24 hours before the procedure and quit smoking at least one day before — ideally for a week afterwards too. Smoking reduces blood flow to the scalp and lowers graft survival. Avoid all recreational drugs.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'clipboard', 'eyebrow' => __( 'Step 3', 'estecapelli' ), 'title' => __( 'Tests & Examination', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Blood tests screen for infection risk, anaemia, blood-sugar levels and clotting factors. A TrichoLab AI analysis measures your hair density and donor-area capacity, and your hairline is designed in line with your facial proportions.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'sparkles', 'eyebrow' => __( 'Step 4', 'estecapelli' ), 'title' => __( 'Nutrition & Sleep', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Have a light breakfast on the day of the operation — never arrive on an empty stomach — and avoid heavy or greasy food. A good night’s sleep beforehand strengthens your immune system and supports a faster, smoother recovery.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'calendar', 'eyebrow' => __( 'Step 5', 'estecapelli' ), 'title' => __( 'Day-of-Operation Preparation', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Wear comfortable, front-opening clothing such as a button-up or wide-necked top so nothing touches the grafts when you change. Remove all jewellery and accessories, keep your scalp free of gel, wax, spray or shampoo residue, and limit caffeine.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'target', 'eyebrow' => __( 'Step 6', 'estecapelli' ), 'title' => __( 'What to Avoid Beforehand', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Avoid Aspirin and blood thinners (without doctor approval), energy drinks, and blood-thinning herbal teas such as green tea, ginseng and ginkgo. These can increase bleeding during the procedure and affect the result.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'star', 'eyebrow' => __( 'Step 7', 'estecapelli' ), 'title' => __( 'Estecapelli Recommendations', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Increase your water intake for better skin hydration and graft survival, take any supplements we recommend for B6, B12, Vitamin D or zinc deficiencies, and try to keep your stress levels low — all of which support a successful outcome.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Before Your Transplant — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Should I stop my regular medication before the procedure?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Never stop any medication on your own. Tell us everything you take and we will advise you. Blood thinners such as Aspirin are only paused before the procedure under medical supervision.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can I eat on the day of the operation?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes — have a light breakfast. Arriving on an empty stomach can disrupt your blood-pressure balance, but heavy or greasy food should be avoided.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long before the procedure should I stop smoking?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Stop smoking at least one day before, and ideally avoid it for a week after as well. Smoking reduces blood flow to the scalp and can lower graft survival.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What should I wear on the day?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Comfortable, front-opening clothing such as a button-up shirt or a wide-necked top, so you never have to pull anything over the freshly transplanted area.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Have a Question Before Your Procedure?', 'estecapelli' ),
						'lead'          => __( 'Send us your details and our medical team will guide you through every step of your preparation — and answer any question you have before the day.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'shield-check', 'label' => __( 'Personalised pre-op guidance from our medical team', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from your dedicated patient-care contact', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
				),
			),

			// ======================== Post Hair Transplant ========================
			array(
				'slug'   => 'post-hair-transplant-period',
				'title'  => 'Post Hair Transplant Period',
				'parent' => 'hair-transplant',
				'sections' => array(
					$hero( __( 'After Your Procedure', 'estecapelli' ), __( 'Your Recovery, Step by Step', 'estecapelli' ), __( 'What you do after your hair transplant matters just as much as the procedure itself. Here is exactly what to expect — and what to do — from the first 24 hours to the day your final result is complete.', 'estecapelli' ) ),

					array(
						'acf_fc_layout'  => 'intro',
						'eyebrow'        => __( 'The Recovery Journey', 'estecapelli' ),
						'title'          => __( 'Things to Do & Things to Watch For', 'estecapelli' ),
						'body'           => '<p>' . esc_html__( 'Your hair transplant recovery follows a clear, predictable timeline. Knowing what happens at each stage — and following your aftercare plan closely — protects the grafts and gives you the fullest, most natural result.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'This is the Estecapelli post-operation guide: the things to do and the things to pay attention to, stage by stage, from the day of your procedure through to full results.', 'estecapelli' ) . '</p>',
						'image'          => '',
						'image_position' => 'left',
						'cta'            => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'Recovery Timeline', 'estecapelli' ),
						'title'         => __( 'Your Hair Transplant, Day by Day', 'estecapelli' ),
						'lead'          => __( 'From the first 24 hours to the completion of your results. Swipe through each stage below.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'eyebrow' => __( 'First 24 Hours', 'estecapelli' ), 'title' => __( 'The First 24 Hours', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Mild redness, swelling and sensitivity around the transplanted and donor areas are completely normal. Keep your head elevated, sleep on your back, do not touch or scratch the area, and take the medication we provide exactly as directed.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'check-circle', 'eyebrow' => __( 'Day 1', 'estecapelli' ), 'title' => __( 'Dressing & First Wash', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'You return to the clinic so your dressing can be checked and your first hair wash performed by our team. We show you the gentle technique you will then continue at home without disturbing the grafts.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'calendar', 'eyebrow' => __( 'Days 2–7', 'estecapelli' ), 'title' => __( 'Daily Washing Routine', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Continue the daily washing routine exactly as demonstrated, using the lotion and special shampoo provided. Swelling settles and small scabs begin to form around each graft — this is part of normal healing.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'sparkles', 'eyebrow' => __( 'Day 7', 'estecapelli' ), 'title' => __( 'Grafts Secured', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'By around the seventh day the grafts are secure and most swelling has resolved. You can gradually return to your normal routine while still protecting the scalp from knocks and direct sun.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'target', 'eyebrow' => __( 'Days 9–12', 'estecapelli' ), 'title' => __( 'Scab Shedding Phase', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'The scab shedding phase is a natural part of healing, occurring around the 7th to 10th day. With gentle washing the small scabs loosen and fall away on their own — never pick or scratch at them.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'atom', 'eyebrow' => __( 'Day 30', 'estecapelli' ), 'title' => __( 'Shock Shedding', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'During the first one to three months some transplanted hairs temporarily shed — a normal phenomenon called shock loss, caused by the follicles entering a brief resting phase before regrowing stronger.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'hair', 'eyebrow' => __( 'Months 2–4', 'estecapelli' ), 'title' => __( 'New Growth Begins', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'The resting follicles reactivate and new hair growth typically begins around the third to fourth month. Early hairs may look fine and sparse at first before they thicken over the following weeks.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'star', 'eyebrow' => __( 'Months 4–12', 'estecapelli' ), 'title' => __( 'Density Builds', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Density steadily increases as more follicles enter the growth phase. The transplanted hair thickens, gains its natural texture and begins to blend seamlessly with your existing hair.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'check-circle', 'eyebrow' => __( 'Months 12–18', 'estecapelli' ), 'title' => __( 'Completion of Results', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Your final result matures, with full density and a completely natural look. The transplanted hair is permanent and can be cut, washed and styled exactly like the rest of your hair.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Aftercare Essentials', 'estecapelli' ),
						'title'         => __( 'What to Do During Recovery', 'estecapelli' ),
						'body'          => __( 'Following your personalised aftercare plan closely is the single best thing you can do for your result:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'check-circle', 'label' => __( 'Use the special shampoo and lotion exactly as instructed', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'label' => __( 'Keep up your monthly PRP sessions', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Take the prescribed medications and vitamins', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Continue any recommended mesotherapy treatments', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Follow your individualised aftercare plan throughout', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout'  => 'intro',
						'eyebrow'        => __( 'Supportive Treatments', 'estecapelli' ),
						'title'          => __( 'Treatments We Recommend to Boost Your Result', 'estecapelli' ),
						'body'           => '<p>' . esc_html__( 'To strengthen the new follicles and accelerate healthy growth, Estecapelli may recommend supportive treatments alongside your recovery:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Exosome injection — to support follicle health and growth', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'PRP (Platelet-Rich Plasma) — to stimulate the scalp and strengthen hair', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Mesotherapy — to nourish the follicles with vitamins and minerals', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Stem cell therapy — to encourage stronger, healthier regrowth', 'estecapelli' ) . '</li></ul>',
						'image'          => '',
						'image_position' => 'right',
						'cta'            => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'After Your Transplant — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'When can I wash my hair?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Your first wash is done at the clinic on Day 1, where our team shows you the gentle technique. From then on you continue the daily washing routine at home with the lotion and shampoo we provide.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When do the scabs fall off?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The scab shedding phase happens around the 7th to 10th day. With gentle washing the scabs loosen and fall away on their own — it is important never to pick or scratch at them.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is it normal to lose hair after the transplant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Between the first and third months some transplanted hairs shed temporarily — this is called shock loss and is completely normal. The follicles then regrow stronger from around the third to fourth month.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see my final result?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'New growth begins around months three to four, noticeable density develops between months four and twelve, and your final result is complete at roughly twelve to eighteen months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can I smoke or drink alcohol during recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'We recommend avoiding smoking for at least one week after the procedure and limiting alcohol, as both reduce blood flow to the scalp and can affect graft survival and healing.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'We’re Here for You', 'estecapelli' ),
						'title'         => __( 'Questions About Your Recovery?', 'estecapelli' ),
						'lead'          => __( 'Our patient-care team supports you long after you fly home. Send your details — or a photo — and we will guide you through every stage of your recovery.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'headset',      'label' => __( 'Dedicated post-operative support team', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Send a photo for a quick progress check', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Contact Patient Care', 'estecapelli' ),
						'show_whatsapp' => true,
					),
				),
			),

			// ======================== About branch ========================
			array(
				'slug'   => 'about-us',
				'title'  => 'About Us',
				'parent' => null,
				'sections' => array(
					$hero( __( 'About Estecapelli', 'estecapelli' ), __( 'Aesthetic excellence, backed by medical trust.', 'estecapelli' ), __( 'Estecapelli is an Istanbul-based clinic specialised in hair restoration, plastic surgery, dental treatment and non-surgical aesthetics. We combine board-certified surgeons, hospital-grade facilities and a patient-first philosophy to deliver results our patients trust us for.', 'estecapelli' ) ),

					// By the numbers.
					array(
						'acf_fc_layout' => 'quick_stats',
						'eyebrow'       => __( 'Trusted Worldwide', 'estecapelli' ),
						'stats'         => array(
							array( 'icon' => 'calendar', 'value' => '15+',      'label' => __( 'Years of Experience', 'estecapelli' ) ),
							array( 'icon' => 'hair',     'value' => '+50,000',  'label' => __( 'Hair Transplants', 'estecapelli' ) ),
							array( 'icon' => 'globe',    'value' => '+40',      'label' => __( 'Countries Served', 'estecapelli' ) ),
							array( 'icon' => 'headset',  'value' => '24/7',     'label' => __( 'Patient Support', 'estecapelli' ) ),
						),
					),

					// Who we are.
					array(
						'acf_fc_layout'  => 'intro',
						'eyebrow'        => __( 'Who We Are', 'estecapelli' ),
						'title'          => __( 'A global leader in hair restoration', 'estecapelli' ),
						'body'           => '<p>' . esc_html__( 'Estecapelli is a global leader in hair restoration surgery, trusted by thousands of patients worldwide who seek excellence, safety, and truly transformative results.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Our clinic combines advanced medical technology with the expertise of a highly skilled and internationally experienced team. Through our patented techniques and patient-focused approach, we deliver natural-looking, long-lasting outcomes that go beyond restoring hair — helping our patients regain confidence, a youthful appearance, and a renewed sense of well-being.', 'estecapelli' ) . '</p>',
						'image'          => '',
						'image_position' => 'right',
						'cta'            => array( 'label' => '', 'url' => '' ),
					),

					// Why Türkiye / value.
					array(
						'acf_fc_layout'  => 'intro',
						'eyebrow'        => __( 'Why Türkiye', 'estecapelli' ),
						'title'          => __( 'World-class results, without the inflated price', 'estecapelli' ),
						'body'           => '<p>' . esc_html__( 'Why pay more in the UK, USA, or Europe? At Estecapelli we combine medical excellence, internationally accredited techniques and competitive pricing to deliver world-class hair-transplant results at a fraction of the cost.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Our state-of-the-art facilities in Türkiye offer the same quality of care you would expect from top clinics worldwide — without the inflated prices. With experienced surgeons, personalised treatment plans and comprehensive aftercare, you receive exceptional value that never compromises on quality.', 'estecapelli' ) . '</p>',
						'image'          => '',
						'image_position' => 'left',
						'cta'            => array( 'label' => '', 'url' => '' ),
					),

					// Our story.
					array(
						'acf_fc_layout'  => 'intro',
						'eyebrow'        => __( 'Our Story', 'estecapelli' ),
						'title'          => __( 'Proven in Türkiye, trusted worldwide', 'estecapelli' ),
						'body'           => '<p>' . esc_html__( 'Our story began in Istanbul, Türkiye, where thousands of patients from around the world have experienced life-changing hair transformations.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'The success we have achieved through our unique techniques and patient-centred approach has brought the Estecapelli experience to Santiago, Chile — and today, with the same passion for excellence, personalised care and natural results, we bring Estecapelli quality to many more locations around the world.', 'estecapelli' ) . '</p>',
						'image'          => '',
						'image_position' => 'right',
						'cta'            => array( 'label' => __( 'Meet Our Doctors', 'estecapelli' ), 'url' => home_url( '/en/about-us/our-doctors' ) ),
					),
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
						'eyebrow'       => __( 'Your First Point of Contact', 'estecapelli' ),
						'title'         => __( 'Medical Consultants', 'estecapelli' ),
						'lead'          => __( 'Multilingual consultants who guide your journey in your own language — from first question to final result.', 'estecapelli' ),
						'variant'       => 'consultant',
						'members'       => array(
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/marcus.webp',  'name' => __( 'Marcus', 'estecapelli' ),  'role' => '', 'languages' => array(
								array( 'country' => 'gb', 'label' => __( 'English', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/matthew.webp', 'name' => __( 'Matthew', 'estecapelli' ), 'role' => '', 'languages' => array(
								array( 'country' => 'gb', 'label' => __( 'English', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/rachel.webp',  'name' => __( 'Rachel', 'estecapelli' ),  'role' => '', 'languages' => array(
								array( 'country' => 'gb', 'label' => __( 'English', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/roxane.webp',  'name' => __( 'Roxane', 'estecapelli' ),  'role' => '', 'languages' => array(
								array( 'country' => 'fr', 'label' => __( 'French', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/sana.webp',    'name' => __( 'Sana', 'estecapelli' ),    'role' => '', 'languages' => array(
								array( 'country' => 'fr', 'label' => __( 'French', 'estecapelli' ) ),
								array( 'country' => 'sa', 'label' => __( 'Arabic', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/melissa.webp', 'name' => __( 'Melissa', 'estecapelli' ), 'role' => '', 'languages' => array(
								array( 'country' => 'it', 'label' => __( 'Italian', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/natalia.webp', 'name' => __( 'Natalia', 'estecapelli' ), 'role' => '', 'languages' => array(
								array( 'country' => 'pl', 'label' => __( 'Polish', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/cynthia.webp', 'name' => __( 'Cynthia', 'estecapelli' ), 'role' => '', 'languages' => array(
								array( 'country' => 'pt', 'label' => __( 'Portuguese', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/damla.webp',   'name' => __( 'Damla', 'estecapelli' ),   'role' => '', 'languages' => array(
								array( 'country' => 'tr', 'label' => __( 'Turkish', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/samet.webp',   'name' => __( 'Samet', 'estecapelli' ),   'role' => '', 'languages' => array(
								array( 'country' => 'tr', 'label' => __( 'Turkish', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/hamza.webp',   'name' => __( 'Hamza', 'estecapelli' ),   'role' => '', 'languages' => array(
								array( 'country' => 'pk', 'label' => __( 'Urdu', 'estecapelli' ) ),
							) ),
							array( 'photo' => '', 'photo_url' => get_template_directory_uri() . '/assets/images/team/wali.webp',    'name' => __( 'Wali', 'estecapelli' ),    'role' => '', 'languages' => array(
								array( 'country' => 'pk', 'label' => __( 'Urdu', 'estecapelli' ) ),
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

			// ======================== Legal ========================
			array(
				'slug'    => 'privacy-policy',
				'title'   => 'Privacy Policy',
				'parent'  => null,
				'content' => $privacy_content,
			),
			array(
				'slug'    => 'terms',
				'title'   => 'Terms',
				'parent'  => null,
				'content' => $terms_content,
			),

		);
	}
}
