<?php
/**
 * Seed data for the treatment importer.
 *
 * Each entry maps a treatment to a ready-to-publish ACF page_sections
 * payload. The importer (inc/admin/import-treatments.php) consumes this
 * array, creates or updates the matching treatment posts, and writes
 * the sections into ACF.
 *
 * Content drafted from the master content sheet in Google Drive
 * (sheet id 1-Cp_FAjmLMD3ScqcOJCthB-4sfBl1Zqjh9wlcO644f0).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'estecapelli_treatments_seed' ) ) {
	function estecapelli_treatments_seed() {

		$whatsapp = function_exists( 'estecapelli_whatsapp_url' ) ? estecapelli_whatsapp_url() : '#';

		return array(

			// ============================================================
			// Exosome FUE
			// ============================================================
			array(
				'slug'     => 'exosome-fue-hair-transplant',
				'title'    => 'Exosome FUE Hair Transplant',
				'category' => 'Hair Transplant',
				'sections' => array(

					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Patented · Estecapelli Exclusive', 'estecapelli' ),
						'title'         => __( 'Exosome FUE Hair Transplant', 'estecapelli' ),
						'lead'          => __( "Estecapelli's patented therapy integrates regenerative exosomes to enhance hair-transplant outcomes. Supported by cell-regenerating exosomes, this technique helps preserve hair follicles for up to 72 hours, supporting stronger graft survival and improved healing.", 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image',
						'image'         => '',
						'video_id'      => '',
					),

					array(
						'acf_fc_layout' => 'quick_stats',
						'eyebrow'       => __( 'Quick Facts', 'estecapelli' ),
						'stats'         => array(
							array( 'icon' => 'sparkles',     'value' => '98%',         'label' => __( 'Follicle Survival', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'value' => 'Local',       'label' => __( 'Anaesthesia', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'value' => '6–8 hrs',     'label' => __( 'Procedure Time', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'value' => __( '7–10 days', 'estecapelli' ), 'label' => __( 'Recovery Time', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Method', 'estecapelli' ),
						'title'         => __( 'What is Exosome FUE Hair Transplantation?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'At Estecapelli, we offer patented Exosome FUE hair transplantation, an advanced treatment designed to support the regeneration and vitality of hair follicles. The specially formulated solution used in this method is derived from mesenchymal stem cells, containing numerous bioactive components that promote tissue repair and activate dormant hair follicles for healthy, lasting hair growth.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Our Exosome FUE solution contains millions of exosome cells that help hair follicles maintain their vitality both during the pre-transplantation period and while adapting to their new locations. By minimising external factors that could compromise graft quality, this technology significantly increases the success rate of hair transplantation.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Exosomes are natural vesicles that enhance intercellular communication, accelerate the healing process and regulate cellular functions. Approved by TÜBİTAK (The Scientific and Technological Research Council of Turkey), this method not only reduces hair loss but also strengthens the growth of transplanted follicles. Thanks to the anti-inflammatory properties of exosomes, redness and scalp sensitivity resolve more quickly, while increased cell renewal and improved blood circulation optimise follicle nourishment.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'This advanced therapy not only accelerates short-term healing but also ensures long-term, natural and lasting results, making it a state-of-the-art solution in hair restoration.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'Exosome FUE Hair Transplantation Stages', 'estecapelli' ),
						'lead'          => __( 'Cutting-edge hair restoration that combines the precision of FUE with the regenerative power of exosome technology. Swipe through the stages below.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'hair',         'eyebrow' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Harvesting Hair Follicles', 'estecapelli' ),         'body' => '<p>' . esc_html__( 'Healthy grafts are individually extracted from the safe donor area using ultra-fine micromotors.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'atom',         'eyebrow' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Strengthening in Exosome Solution', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'The harvested grafts are placed in a specially formulated exosome solution that nourishes and prepares them for transplantation.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'target',       'eyebrow' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Preparing the Transplantation Area', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'The recipient area is prepared with precise channels using sapphire-tipped instruments for the most natural angle and direction.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'check-circle', 'eyebrow' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Transplantation of the Follicles', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'The exosome-strengthened follicles are implanted one by one for natural density and direction.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'hands-heart',  'eyebrow' => __( 'Stage 5', 'estecapelli' ), 'title' => __( 'Healing Process', 'estecapelli' ),                  'body' => '<p>' . esc_html__( 'Patients return home the same day; our team guides every step of the recovery.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Why It Matters', 'estecapelli' ),
						'title'         => __( 'The Most Important Advantages of Exosome FUE', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( "Exosome FUE is the future of hair restoration — the precision of FUE meets cutting-edge exosome therapy. The technique boosts follicle survival to up to 98% over 72 hours, accelerating healing, supporting stronger growth, and delivering naturally lasting results.", 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Exosome FUE Healing Process: Post-Procedure Expectations', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The first week after surgery typically involves mild redness and scabbing. New growth becomes visible from month three, with full results at month twelve. Our dedicated post-operative support team guides you every step of the way.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Exosome FUE — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the natural density and hairline our patients achieve with Exosome FUE. Every result is planned around your own growth pattern for a result that looks completely natural.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Exosome FUE — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'What is Exosome FUE and how does it differ from classic FUE?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Exosome FUE follows the same gold-standard FUE technique — individual follicles are extracted and implanted one by one — but adds exosome therapy to support healing and growth. Exosomes are tiny cell-derived vesicles rich in growth factors that signal the follicles and surrounding tissue to regenerate. The result is the proven reliability of FUE combined with a biological boost to graft recovery.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Am I a good candidate for Exosome FUE?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most adults with stable hair loss and a healthy donor area at the back and sides of the scalp are suitable, including men with male-pattern baldness and many women with thinning. It is also a strong option for patients with weaker hair quality or those wanting to maximise graft survival. A consultation with photos lets our team confirm your suitability and estimate the grafts needed.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What does the procedure involve and is it painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The procedure is performed under local anaesthesia, so you are awake but comfortable and feel no pain during the session, which typically lasts six to eight hours depending on the number of grafts. Follicles are extracted from the donor area, the exosome solution is applied to support the grafts, and they are implanted along a natural-looking hairline designed with you beforehand. You go home or back to your hotel the same day.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What is recovery like and how long should I stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most patients stay in Turkey for around three days, including a first wash and aftercare briefing before flying home. Mild redness, swelling and small scabs are normal in the first week and settle quickly; the scabs fall away within about ten days. Many people return to desk-based work within a few days, avoiding strenuous activity, sun and swimming for the first couple of weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see results, and is the shedding phase normal?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes — the transplanted hairs often shed in the first few weeks, which is completely normal and expected. New growth begins around months three to four, noticeable density develops by six to nine months, and the final, fully matured result appears at about twelve months. Exosome support can help the new hair come through stronger and a little sooner than with classic FUE.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent and natural-looking?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The transplanted follicles are taken from areas genetically resistant to hair loss, so they continue to grow permanently like the rest of your hair. With a carefully designed hairline and single-follicle implantation, the result looks completely natural — you can cut, style and wash it normally. Existing non-transplanted hair can still thin over time, so your surgeon may advise supportive care to protect the overall look.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready to Restore Your Hair with Exosome FUE?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised Exosome FUE plan, a graft estimate and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
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
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Explore Further', 'estecapelli' ),
						'title'         => __( 'Related Treatments', 'estecapelli' ),
						'count'         => 3,
						'manual'        => array(),
					),
				),
			),

			// ============================================================
			// Female Hair Transplant
			// ============================================================
			array(
				'slug'     => 'female-hair-transplant',
				'title'    => 'Female Hair Transplant',
				'category' => 'Hair Transplant',
				'sections' => array(

					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Specially Designed for Women', 'estecapelli' ),
						'title'         => __( 'Female Hair Transplant', 'estecapelli' ),
						'lead'          => __( 'Designed exclusively for women, offering dense and natural-looking results without shaving. Each graft is delicately placed to blend seamlessly with your existing hair, preserving your style throughout the entire process.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image',
						'image'         => '',
						'video_id'      => '',
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Approach', 'estecapelli' ),
						'title'         => __( 'What Is Female Hair Transplantation?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( "Female hair transplant is a specialised procedure designed to address hair loss and thinning in women, delivering permanent and natural-looking results. Unlike male hair transplants, the technique is carefully adapted to suit women's unique hair structure, growth patterns and hairline aesthetics.", 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'At Estecapelli, every treatment plan is fully personalised, ensuring results that blend seamlessly with your natural hair. Our specialists guide you through every step of the journey, from the initial consultation to post-operative care, making sure your recovery is smooth and your results are everything you hoped for.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why It Happens', 'estecapelli' ),
						'title'         => __( 'Causes of Hair Loss in Women', 'estecapelli' ),
						'body'          => __( 'Understanding the root cause of hair loss is the first and most important step toward finding the right solution. The most common causes include:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'atom',         'label' => __( 'Hormonal changes (pregnancy, postpartum, menopause)', 'estecapelli' ) ),
							array( 'icon' => 'dna',          'label' => __( 'Genetic predisposition and family history', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Stress and emotional factors', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Nutritional deficiencies (iron, biotin, zinc, B-vitamins)', 'estecapelli' ) ),
							array( 'icon' => 'globe',        'label' => __( 'Environmental factors (pollution, UV, chemicals)', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Health conditions (thyroid disorders, PCOS, alopecia)', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Best Technique', 'estecapelli' ),
						'title'         => __( 'Best Hair Transplant Techniques for Women', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'At Estecapelli, the technique we most strongly recommend for women is DHI hair transplantation. DHI is frequently preferred by our female patients because it allows hair follicles to be implanted directly without the need to shave existing hair, making it an ideal choice for women who want to maintain their appearance throughout the entire process.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'DHI advantages:', 'estecapelli' ) . '</strong> ' . esc_html__( 'precise angle and direction control, denser implantation, no shaving required, minimal scarring, faster healing, lower risk of damage to existing follicles.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'Female Hair Transplant Stages', 'estecapelli' ),
						'lead'          => __( 'Carefully managed from the first consultation to final growth, every stage is tailored to a woman’s hair structure and aesthetic goals. Swipe through the stages below.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'clipboard',    'eyebrow' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Consultation & Planning', 'estecapelli' ),  'body' => '<p>' . esc_html__( 'Your hair loss is analysed in detail and the underlying cause — hormonal, genetic or nutritional — is identified so the plan addresses the real problem. We assess the strength of your donor area, evaluate your expectations and build a fully personalised treatment plan, including a realistic graft estimate and the technique best suited to your hair.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'sparkles',     'eyebrow' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Natural Hairline Design', 'estecapelli' ),  'body' => '<p>' . esc_html__( 'An aesthetic, feminine hairline is drawn in harmony with your facial proportions and natural growth direction. For women the goal is usually to restore density and frame the face rather than rebuild a receded line, so the design protects your existing style and keeps the result completely undetectable.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'target',       'eyebrow' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Hair Follicle Extraction', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Healthy grafts are harvested one by one from the safe donor area at the back of the scalp using ultra-fine micromotors. With the DHI technique this is done without shaving your existing hair, so you keep your length throughout — the donor area heals quickly and leaves no linear scar.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'check-circle', 'eyebrow' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Graft Implantation', 'estecapelli' ),       'body' => '<p>' . esc_html__( 'Using DHI implanter pens, the follicles are placed directly into the thinning areas at the correct angle, depth and direction for seamless, natural density. Direct implantation gives precise control and lets grafts be added between your existing hairs without damaging them.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'The Difference', 'estecapelli' ),
						'title'         => __( 'Advantages of Female Hair Transplant', 'estecapelli' ),
						'body'          => __( 'With an expert approach tailored specifically for women, the procedure offers many meaningful advantages:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'check-circle', 'label' => __( 'Natural and permanent results', 'estecapelli' ) ),
							array( 'icon' => 'hair',         'label' => __( 'Increased hair density in thinning areas', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Minimum scarring, maximum aesthetics', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Fast recovery, back to daily life within days', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Shaving-free with the DHI technique', 'estecapelli' ) ),
							array( 'icon' => 'clipboard',    'label' => __( 'Personalised hairline design', 'estecapelli' ) ),
							array( 'icon' => 'face',         'label' => __( 'Rejuvenating effect — a fresher, younger look', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Post-Procedure Recovery Process', 'estecapelli' ),
						'body'          => '<p><strong>' . esc_html__( 'First week:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Mild swelling and scabbing may occur and typically subside within a short time.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( '1 to 2 months:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Shock loss may occur — completely normal and expected.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( '6 to 12 months:', 'estecapelli' ) . '</strong> ' . esc_html__( 'New hair grows in with full density and natural appearance.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Alternative Solutions', 'estecapelli' ),
						'title'         => __( 'Non-Surgical Hair Treatments for Women', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'We also offer various treatment options for women experiencing hair loss who do not prefer surgical procedures:', 'estecapelli' ) . '</p><ul>'
							. '<li><strong>PRP</strong> — ' . esc_html__( 'natural regenerative treatment that nourishes and strengthens hair follicles.', 'estecapelli' ) . '</li>'
							. '<li><strong>' . esc_html__( 'Mesotherapy', 'estecapelli' ) . '</strong> — ' . esc_html__( 'micro-injections of vitamins and minerals directly into the scalp.', 'estecapelli' ) . '</li>'
							. '<li><strong>' . esc_html__( 'Stem Cell Therapy', 'estecapelli' ) . '</strong> — ' . esc_html__( 'revitalises dormant hair follicles using regenerative cells.', 'estecapelli' ) . '</li>'
							. '<li><strong>' . esc_html__( 'Exosome Injection', 'estecapelli' ) . '</strong> — ' . esc_html__( 'next-generation hair restoration that triggers the scalp\'s natural repair process.', 'estecapelli' ) . '</li>'
							. '</ul>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => __( 'Speak to a Consultant', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Female Hair Transplant — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Am I a good candidate for a female hair transplant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most women with stable hair loss and a healthy donor area at the back of the scalp are suitable. It works best for thinning along the part line, a widening hairline or sparse temples. Because women more often have diffuse thinning, the single most important step is identifying the cause of your hair loss first — a consultation with clear photos lets our team confirm your suitability and estimate the grafts needed.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will my head be shaved? Can I keep my long hair?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'In most cases, no. We primarily use the DHI technique, which allows follicles to be implanted directly without shaving your existing hair, so you keep your length and style throughout the whole process. Only a small, hidden section of the donor area may be trimmed, staying completely covered by the hair above it.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'My hair loss may be hormonal — postpartum, menopause, PCOS or thyroid. Can I still have a transplant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Hormonal and medical hair loss often needs to be stabilised first, because transplanting into actively shedding hair can give disappointing results. We assess the underlying cause and may recommend supportive treatments such as PRP, mesotherapy or medical management before or instead of surgery. Once your hair loss is stable, a transplant becomes a reliable, permanent option.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Could a transplant damage my existing hair?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'With the precise, direct DHI method, grafts are carefully placed between your existing follicles to avoid harming them. A temporary "shock loss" of some surrounding hairs can occur in the first weeks — this is normal and the hair regrows. Choosing an experienced team is the key factor in protecting your natural hair.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see results?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The transplanted hairs usually shed within the first few weeks, which is completely normal. New growth begins around months three to four, noticeable density develops by six to nine months, and the final, fully matured result appears at about twelve months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent and natural-looking?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The follicles are taken from areas genetically resistant to hair loss, so they keep growing permanently like the rest of your hair. With a feminine hairline design and single-follicle implantation along the natural growth direction, the result is completely natural and undetectable — you can cut, colour and style it as usual.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is it painful, and how long is recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The procedure is performed under local anaesthesia, so you stay awake but comfortable and feel no pain during the session. Mild redness and small scabs are normal in the first week and settle quickly; most patients return to daily life within a few days, avoiding strenuous activity, sun and swimming for the first couple of weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can I have a transplant while pregnant or breastfeeding?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'We do not perform hair transplants during pregnancy or breastfeeding, as a precaution around anaesthesia and medication. Postpartum shedding is also usually temporary and often recovers on its own, so we recommend waiting until your hormones and hair have settled before considering surgery.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Female Hair Transplant — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the restored density and natural hairlines our female patients achieve. Every result is planned around your own hair structure and growth pattern, so the outcome blends seamlessly with your existing hair and looks completely natural.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready to Restore Your Hair?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised, shaving-free female hair transplant plan, a graft estimate and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free graft assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-shave DHI planned around your existing hair', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),

					array(
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Explore Further', 'estecapelli' ),
						'title'         => __( 'Related Treatments', 'estecapelli' ),
						'count'         => 3,
						'manual'        => array(),
					),
				),
			),

			// ============================================================
			// Hair Mesotherapy
			// ============================================================
			array(
				'slug'     => 'hair-mesotherapy',
				'title'    => 'Hair Mesotherapy',
				'category' => 'Hair Transplant',
				'sections' => array(

					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Non-Surgical Hair Treatment', 'estecapelli' ),
						'title'         => __( 'Hair Mesotherapy', 'estecapelli' ),
						'lead'          => __( 'Revitalise your hair follicles with a customised vitamin, mineral, and amino-acid mixture delivered directly to the scalp through painless micro-injections.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image',
						'image'         => '',
						'video_id'      => '',
					),

					array(
						'acf_fc_layout' => 'quick_stats',
						'eyebrow'       => __( 'Quick Facts', 'estecapelli' ),
						'stats'         => array(
							array( 'icon' => 'medical-plus', 'value' => '4–6',         'label' => __( 'Sessions', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'value' => '4–6 weeks',   'label' => __( 'Interval', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'value' => 'Painless',    'label' => __( 'Procedure', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'value' => 'No downtime', 'label' => __( 'Recovery', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Approach', 'estecapelli' ),
						'title'         => __( "Hair Mesotherapy: Estecapelli's Personalised Treatment Approach", 'estecapelli' ),
						'body'          => '<p>' . esc_html__( "Hair mesotherapy is a fast and effective way to combat hair loss and boost hair health. The treatment delivers a custom vitamin, mineral, and amino-acid mixture directly to the hair follicles, providing essential nutrients where they're needed most.", 'estecapelli' ) . '</p><p>' . esc_html__( 'At Estecapelli, the process is carefully tailored: the scalp and follicles are analysed, the mixture is prepared based on individual needs, and it is administered via painless microinjections. Sessions are short and comfortable, and you can return to your daily routine immediately.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'How is Hair Mesotherapy Performed?', 'estecapelli' ),
						'lead'          => __( "Estecapelli's step-by-step application process, from scalp analysis to a personalised session program. Swipe through the steps below.", 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'target',       'eyebrow' => __( 'Step 1', 'estecapelli' ), 'title' => __( 'Analysis', 'estecapelli' ),                            'body' => '<p>' . esc_html__( 'The hair and scalp are carefully evaluated to understand the structure of the hair follicles, overall scalp condition and the cause of hair loss. At Estecapelli, this is done using TrichoLab analysis, which lets our specialists examine follicle length, quality and density — the basis for a precise, personalised treatment plan rather than a one-size-fits-all cocktail.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'atom',         'eyebrow' => __( 'Step 2', 'estecapelli' ), 'title' => __( 'Preparation of the Mesotherapy Mixture', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'A customised mesotherapy solution is prepared for each patient, containing a carefully balanced combination of vitamins, minerals, amino acids and growth factors essential for healthy hair. The mixture is tailored to your specific needs — to support follicle strength, stimulate growth and improve overall hair quality.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'medical-plus', 'eyebrow' => __( 'Step 3', 'estecapelli' ), 'title' => __( 'Application', 'estecapelli' ),                         'body' => '<p>' . esc_html__( 'The customised mixture is injected directly into the scalp using fine-tipped needles, delivering nutrients straight to the hair follicles. For those with sensitive scalps, a topical anaesthetic cream can be applied. The procedure is quick, comfortable and minimally invasive, so you can return to daily activities immediately.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'calendar',     'eyebrow' => __( 'Step 4', 'estecapelli' ), 'title' => __( 'Treatment Program', 'estecapelli' ),                   'body' => '<p>' . esc_html__( 'A treatment program of typically 4–6 sessions is applied at intervals of 4–6 weeks. The exact schedule is adjusted to your level of hair loss and how your hair responds, with periodic maintenance sessions recommended afterwards to preserve the results.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why Choose It', 'estecapelli' ),
						'title'         => __( 'Advantages of Hair Mesotherapy', 'estecapelli' ),
						'body'          => __( 'A supportive, non-invasive treatment that fits into your daily life:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'hair',         'label' => __( 'Strengthens hair follicles and promotes new growth', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Reduces hair loss with no downtime', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Personalised vitamin and mineral cocktail', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Painless, comfortable sessions', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Compatible with hair-transplant after-care', 'estecapelli' ) ),
							array( 'icon' => 'star',         'label' => __( 'Quick visible results over a few sessions', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Hair Mesotherapy — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Am I a good candidate for hair mesotherapy?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Mesotherapy suits men and women in the early to moderate stages of hair loss — thinning, weak or shedding hair, including androgenetic alopecia and telogen effluvium. It is also a strong support treatment after a hair transplant. It is not a substitute for surgery in advanced baldness, so the cause should first be assessed in detail by a specialist.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How many sessions will I need and when will I see results?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A typical course is four to six sessions spaced four to six weeks apart. Many patients notice reduced shedding after the first two or three sessions, with improved thickness and new growth building over the full program. The exact number is personalised to the severity of your hair loss and how your hair responds.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is hair mesotherapy painful, and is there any downtime?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is generally painless — you may feel only a slight pricking sensation, and a topical numbing cream can be used for sensitive scalps. There is no downtime: sessions are short and you can return to your daily routine, including work, straight afterwards.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What is actually in the mesotherapy mixture?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The solution is a tailored cocktail of vitamins, minerals, amino acids and growth factors that nourish the follicles directly. Rather than a fixed formula, the blend is adjusted to your scalp analysis so it targets the specific deficiencies and weaknesses found in your hair.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can it be combined with other treatments?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. It pairs well with PRP and with medical treatments such as minoxidil, and it is often used to support and strengthen results after DHI, FUE or Sapphire FUE transplantation. Our specialists design the best combination for your goals.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are there any side effects?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is a safe, minimally invasive procedure. Rarely, temporary redness, mild swelling or itching may appear at the injection sites and settle on their own within a short time.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long do the results last?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Results can last a long time, but mesotherapy supports rather than permanently cures hair loss. If the underlying factors persist, periodic maintenance sessions are recommended to preserve the density and keep your hair healthy.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Hair Mesotherapy — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the improvement in density and hair quality our patients achieve over a course of mesotherapy. Results build gradually across sessions as the follicles are nourished and strengthened.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready to Strengthen Your Hair?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised mesotherapy plan, the recommended number of sessions and a no-obligation quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free scalp assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Personalised vitamin and mineral cocktail', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),

					array(
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Explore Further', 'estecapelli' ),
						'title'         => __( 'Related Treatments', 'estecapelli' ),
						'count'         => 3,
						'manual'        => array(),
					),
				),
			),

			// ============================================================
			// Sapphire FUE Hair Transplant
			// ============================================================
			array(
				'slug'     => 'sapphire-fue-hair-transplant',
				'title'    => 'Sapphire FUE Hair Transplant',
				'category' => 'Hair Transplant',
				'sections' => array(

					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Advanced FUE Technique', 'estecapelli' ),
						'title'         => __( 'Sapphire FUE Hair Transplant', 'estecapelli' ),
						'lead'          => __( 'One of the most widely preferred hair restoration methods today. Sapphire FUE replaces traditional steel blades with sapphire-tipped instruments, creating ultra-precise channels for denser, more natural-looking results and faster healing.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image',
						'image'         => array( 'url' => get_template_directory_uri() . '/assets/images/treatments/graft-extraction.webp', 'alt' => __( 'Follicle extraction with an ultra-fine micromotor', 'estecapelli' ), 'width' => 1448, 'height' => 1086 ),
						'video_id'      => '',
					),

					array(
						'acf_fc_layout' => 'quick_stats',
						'eyebrow'       => __( 'Quick Facts', 'estecapelli' ),
						'stats'         => array(
							array( 'icon' => 'shield-check', 'value' => 'Local',     'label' => __( 'Anaesthesia', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'value' => '6–8 hrs',   'label' => __( 'Procedure Time', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'value' => '1 session', 'label' => __( 'Sessions', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'value' => 'Minimal',   'label' => __( 'Scarring', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Method', 'estecapelli' ),
						'title'         => __( 'What is Sapphire FUE Hair Transplantation?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'As technology has advanced, so have hair transplantation techniques. Sapphire FUE is a modern refinement of the classic FUE method in which the channels that receive the grafts are opened with blades tipped with a sapphire gemstone rather than steel. The smoother, sharper sapphire edge creates finer incisions, allowing grafts to be placed closer together at the correct angle and direction for a fuller, completely natural result.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'How is Sapphire FUE Applied at Estecapelli?', 'estecapelli' ),
						'lead'          => __( 'Every procedure is built on careful planning and precise execution at each stage. Swipe through the stages below.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'clipboard',    'eyebrow' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Consultation & Planning', 'estecapelli' ),   'body' => '<p>' . esc_html__( 'A comprehensive consultation and TrichoLab scalp analysis create the most accurate treatment plan based on your unique hair structure and goals. Your donor area is assessed, the number of grafts estimated and a natural hairline designed before anything else begins.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'shield-check', 'eyebrow' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Local Anaesthesia', 'estecapelli' ),       'body' => '<p>' . esc_html__( 'Local anaesthesia is applied to the donor and recipient areas so the entire session is comfortable and pain-free. You stay awake but relaxed throughout, and can listen to music or watch something during the procedure.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'target',       'eyebrow' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Graft Collection', 'estecapelli' ),         'body' => '<p>' . esc_html__( 'Healthy follicles are harvested one by one from the safe donor area at the back and sides of the scalp using ultra-fine micromotors. Each graft is carefully sorted and preserved to keep it strong and viable until it is implanted.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'sparkles',     'eyebrow' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Sapphire Channel Opening', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Channels are created with sapphire-tipped blades — the most decisive stage for natural density, angle and direction. The smoother, sharper sapphire edge makes finer incisions, so grafts sit closer together with less tissue trauma and faster healing.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'check-circle', 'eyebrow' => __( 'Stage 5', 'estecapelli' ), 'title' => __( 'Follicle Implantation', 'estecapelli' ),     'body' => '<p>' . esc_html__( 'The harvested follicles are implanted one by one into the pre-opened channels at the correct depth, angle and direction. This precise placement is what delivers a full, completely natural-looking result that grows in seamlessly with your own hair.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why Estecapelli', 'estecapelli' ),
						'title'         => __( 'Why Estecapelli & Turkey for Sapphire FUE', 'estecapelli' ),
						'body'          => __( 'Sapphire technology, scientific planning and experienced teams come together to deliver results that last a lifetime:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'check-circle', 'label' => __( 'Permanent and reliable, natural-looking results', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'High precision with sapphire-tipped blades', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Less tissue trauma and faster healing', 'estecapelli' ) ),
							array( 'icon' => 'hair',         'label' => __( 'High density and a natural appearance', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Gentle, suitable for sensitive scalps', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Reduced risk of infection thanks to sapphire’s antibacterial properties', 'estecapelli' ) ),
							array( 'icon' => 'atom',         'label' => __( 'Personalised planning with TrichoLab scientific analysis', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Sapphire vs FUE', 'estecapelli' ),
						'title'         => __( 'What are the Differences Between Sapphire FUE and FUE?', 'estecapelli' ),
						'body'          => '<p><strong>' . esc_html__( 'Sharp and Smooth Channels.', 'estecapelli' ) . '</strong> ' . esc_html__( 'While traditional FUE uses steel blades, Sapphire FUE creates ultra-precise channels with sapphire-tipped blades that stay sharp longer. This allows for smaller, cleaner incisions, minimising tissue trauma and bleeding. The precise placement of grafts ensures natural hair growth and density, while also promoting a faster, more comfortable recovery.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Natural Hairline.', 'estecapelli' ) . '</strong> ' . esc_html__( 'The Sapphire FUE technique is more effective than traditional methods in achieving a natural appearance. Sapphire blades create channels that consider the natural growth direction of hair follicles, giving the patient a more natural hairline. Additionally, maximum density is achieved, resulting in a fuller appearance.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Less Scarring.', 'estecapelli' ) . '</strong> ' . esc_html__( 'Sapphire FUE leaves less scarring compared to traditional techniques. Because sapphire blades maintain their sharpness, the incisions are smaller, resulting in minimal scarring after the healing process. These marks disappear quickly.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Candidates', 'estecapelli' ),
						'title'         => __( 'Who Can Undergo a Hair Transplant?', 'estecapelli' ),
						'body'          => '<p><strong>' . esc_html__( 'Age.', 'estecapelli' ) . '</strong> ' . esc_html__( 'There is no strict age limit for hair transplantation, but candidates are generally over 25, when hair loss patterns become more predictable. That said, hair loss can be advanced even at 22–24, so the extent of a patient’s hair loss is just as important as age when planning a transplant.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Donor Area.', 'estecapelli' ) . '</strong> ' . esc_html__( 'A successful hair transplant requires healthy hair follicles that can be safely harvested. The primary donor area is usually the back and sides of the scalp, where hair is genetically resistant to thinning. In some cases, when additional grafts are needed, chest or beard hair can also be used. The quality and density of the donor area determine how many grafts can be transplanted.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Type of Hair Loss.', 'estecapelli' ) . '</strong> ' . esc_html__( 'The cause of your hair loss is an important factor in determining your suitability. The best candidates are typically those with male or female pattern baldness, as this type usually affects only certain areas of the scalp rather than all of it, so the donor area is likely to remain unaffected. Other types, such as alopecia, require a more specialised evaluation.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Health.', 'estecapelli' ) . '</strong> ' . esc_html__( 'For an effective hair transplant, you should not have any medical conditions that could affect the procedure or healing process. Examples include:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Uncontrolled diabetes, which can slow wound healing', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Severe heart or liver problems, which may complicate anaesthesia', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Active scalp infections or skin diseases in the donor or recipient areas', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Autoimmune disorders that can interfere with hair growth', 'estecapelli' ) . '</li></ul>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Sapphire FUE Recovery Process: What to Expect', 'estecapelli' ),
						'body'          => '<p><strong>' . esc_html__( 'First days and weeks:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Mild redness and swelling are normal and subside quickly.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Days 7–14:', 'estecapelli' ) . '</strong> ' . esc_html__( 'The scab-shedding phase is a natural part of healing; small scabs loosen around day 7–10 and fully come off with gentle washing between days 10 and 14.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Months 1–3:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Temporary shock shedding may occur as follicles adapt — completely expected and harmless to the follicles.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Months 3–12:', 'estecapelli' ) . '</strong> ' . esc_html__( 'New growth begins around month 3–4; density gradually increases and by 12 months the results are fully visible — thicker, natural-looking and permanent hair, supported by our follow-up team every step of the way.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Sapphire FUE — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'What is the difference between Sapphire FUE and standard FUE?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Both use the same follicle-by-follicle FUE method; the difference is the blade. Standard FUE opens the recipient channels with steel blades, while Sapphire FUE uses sapphire-tipped blades that stay sharper and create smoother, smaller incisions. That means grafts can be placed closer together for higher density, with less tissue trauma, a more natural hairline, minimal scarring and faster healing.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Am I a good candidate for Sapphire FUE?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The best candidates are generally over 25 with male or female pattern hair loss, a healthy donor area at the back and sides of the scalp, and no medical condition that would affect the procedure or healing. Because sapphire blades are gentle, it also suits sensitive scalps. A free consultation with photos confirms your suitability and estimates the grafts you need.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does it require shaving, and how long should I stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Sapphire FUE usually involves shaving the donor and recipient areas to give the surgeon full precision, though partial-shave options can be discussed at your consultation. Most patients stay in Turkey for around three days, including a first wash and aftercare briefing before flying home.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is Sapphire FUE painful, and what is recovery like?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The procedure is performed under local anaesthesia, so you feel no pain during the session, which typically lasts six to eight hours. Mild redness and swelling settle within a few days, and small scabs shed with gentle washing between days ten and fourteen. Most people return to desk-based work within a few days, avoiding strenuous activity, sun and swimming for the first couple of weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see the final result, and is shedding normal?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The transplanted hairs often shed in the first weeks — this "shock shedding" is completely normal and expected. New growth begins around months three to four, noticeable density develops by six to nine months, and the final, fully matured result appears at about twelve months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent and natural-looking?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The follicles are taken from areas genetically resistant to hair loss, so they keep growing permanently. The sapphire channels follow your natural growth direction for a completely natural hairline and density — you can cut, wash and style the hair normally. Existing non-transplanted hair can still thin over time, so supportive care may be advised to protect the overall look.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Sapphire FUE — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the density and natural hairlines our patients achieve with Sapphire FUE. Every result is planned around your own growth pattern, so the outcome blends seamlessly with your existing hair and looks completely natural.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready to Restore Your Hair with Sapphire FUE?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised Sapphire FUE plan, a graft estimate and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
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
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Explore Further', 'estecapelli' ),
						'title'         => __( 'Related Treatments', 'estecapelli' ),
						'count'         => 3,
						'manual'        => array(),
					),
				),
			),

			// ============================================================
			// DHI Hair Transplant
			// ============================================================
			array(
				'slug'     => 'dhi-hair-transplant',
				'title'    => 'DHI Hair Transplant',
				'category' => 'Hair Transplant',
				'sections' => array(

					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Direct Hair Implantation', 'estecapelli' ),
						'title'         => __( 'DHI Hair Transplant', 'estecapelli' ),
						'lead'          => __( 'A modern, highly precise technique in which follicles are implanted directly into the recipient area with a specialised Choi pen — no pre-opened channels. The result is denser placement, minimal trauma and, with the right plan, no need to shave existing hair.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image',
						'image'         => array( 'url' => get_template_directory_uri() . '/assets/images/treatments/scalp-implant.webp', 'alt' => __( 'Direct hair implantation with a Choi pen', 'estecapelli' ), 'width' => 1448, 'height' => 1086 ),
						'video_id'      => '',
					),

					array(
						'acf_fc_layout' => 'quick_stats',
						'eyebrow'       => __( 'Quick Facts', 'estecapelli' ),
						'stats'         => array(
							array( 'icon' => 'shield-check', 'value' => 'Local',       'label' => __( 'Anaesthesia', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'value' => 'Choi pen',    'label' => __( 'Implantation', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'value' => 'No channels', 'label' => __( 'Technique', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'value' => 'Fast',        'label' => __( 'Recovery', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Method', 'estecapelli' ),
						'title'         => __( 'What is DHI Hair Transplantation?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'DHI — Direct Hair Implantation — is a modern, highly effective hair transplant technique. Harvested follicles are loaded into a specialised Choi implanter pen and placed directly into the recipient area without first opening separate channels. This gives the surgeon precise control over the angle, direction and depth of every graft, enabling dense placement with minimal handling and a fast recovery.', 'estecapelli' ) . '</p>',
						'image'         => array( 'url' => get_template_directory_uri() . '/assets/images/treatments/scalp-implant-macro.webp', 'alt' => __( 'Close-up of direct hair implantation', 'estecapelli' ), 'width' => 1254, 'height' => 1254 ),
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'How is DHI Performed at Estecapelli?', 'estecapelli' ),
						'lead'          => __( 'A precise, four-stage process managed end to end by our medical team. Swipe through the stages below.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'clipboard',    'eyebrow' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Consultation & Planning', 'estecapelli' ),   'body' => '<p>' . esc_html__( 'Your hair structure, donor area and goals are analysed with AI-supported TrichoLab to design a fully personalised plan. The number of grafts is estimated and a natural hairline mapped out, so every detail is agreed before the procedure begins.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'shield-check', 'eyebrow' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Local Anaesthesia', 'estecapelli' ),       'body' => '<p>' . esc_html__( 'Local anaesthesia is applied to the donor and recipient areas for a comfortable, pain-free procedure. You stay awake but relaxed throughout, and can listen to music or watch something during the session.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'target',       'eyebrow' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Graft Collection', 'estecapelli' ),         'body' => '<p>' . esc_html__( 'Healthy follicles are individually extracted from the safe donor area using ultra-fine micromotors and immediately loaded into the Choi implanter pens, minimising the time grafts spend outside the scalp to keep them strong and viable.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'check-circle', 'eyebrow' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Implantation with Choi Pen', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Grafts are implanted directly with the Choi pen at the precise angle, depth and direction — no separate channels are opened first. This direct placement allows very high density between existing hairs and, with the right plan, often without shaving, for a completely natural result.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why Estecapelli', 'estecapelli' ),
						'title'         => __( 'Why Estecapelli is the Best Choice for DHI', 'estecapelli' ),
						'body'          => __( 'Detailed planning and the precision of the Choi pen deliver dense, natural and lasting results:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'check-circle', 'label' => __( 'Permanent and reliable results from meticulous planning', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'High precision — direct implantation, no pre-made channels', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Less trauma and faster healing', 'estecapelli' ) ),
							array( 'icon' => 'hair',         'label' => __( 'High density and a natural appearance', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Suitable for sensitive skin', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Reduced risk of infection with no open incisions', 'estecapelli' ) ),
							array( 'icon' => 'atom',         'label' => __( 'Personalised planning with scientific TrichoLab analysis', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Candidates', 'estecapelli' ),
						'title'         => __( 'Who Can Undergo a Hair Transplant?', 'estecapelli' ),
						'body'          => '<p><strong>' . esc_html__( 'Age.', 'estecapelli' ) . '</strong> ' . esc_html__( 'There is no strict age limit for hair transplantation, but candidates are generally over 25, when hair loss patterns become more predictable. That said, hair loss can be advanced even at 22–24, so the extent of a patient’s hair loss is just as important as age when planning a transplant.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Donor Area.', 'estecapelli' ) . '</strong> ' . esc_html__( 'A successful hair transplant requires healthy hair follicles that can be safely harvested. The primary donor area is usually the back and sides of the scalp, where hair is genetically resistant to thinning. The quality and density of the donor area determine how many grafts can be transplanted.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Type of Hair Loss.', 'estecapelli' ) . '</strong> ' . esc_html__( 'The best candidates are typically those with male or female pattern baldness, as this type usually affects only certain areas of the scalp, so the donor area is likely to remain unaffected. Other types, such as alopecia, require a more specialised evaluation.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Health.', 'estecapelli' ) . '</strong> ' . esc_html__( 'For an effective hair transplant, you should not have any medical conditions that could affect the procedure or healing, such as uncontrolled diabetes, severe heart or liver problems, active scalp infections, or autoimmune disorders. A thorough medical evaluation before the procedure ensures it is safe and effective.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'DHI Recovery Process: What to Expect', 'estecapelli' ),
						'body'          => '<p><strong>' . esc_html__( 'First days and weeks:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Mild redness and swelling are normal and resolve quickly.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Scab shedding:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Small scabs naturally shed as part of healing.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Months 1–4:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Temporary shock shedding occurs as follicles enter a new growth cycle.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Months 3–12:', 'estecapelli' ) . '</strong> ' . esc_html__( 'New hair grows in with full, natural density, supported by our follow-up team.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'DHI — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'What is the difference between DHI and FUE?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Both harvest follicles the same way; the difference is implantation. With FUE the recipient channels are opened first and grafts placed afterwards, while DHI uses a Choi implanter pen to open the site and place the follicle in one direct motion. This gives the surgeon precise control over angle, depth and density, allows very dense placement, and — with the right plan — often avoids shaving your existing hair.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is DHI really shaving-free? Can I keep my hair?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'DHI is the technique best suited to unshaven and targeted procedures. For smaller areas and density work, your existing hair can often be fully preserved; for larger sessions a partial or hidden trim may be needed. Your consultant will confirm exactly what is possible for your case from your photos.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Am I a good candidate for DHI?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'DHI is an excellent choice for early to moderate hair loss, diffuse thinning, smaller or targeted areas, hairline refinement and anyone wanting ultra-precise, dense placement without shaving. The best candidates are generally over 25 with a healthy donor area and stable hair loss; a free consultation with photos confirms your suitability.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is DHI painful, and what is recovery like?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The procedure is carried out under local anaesthesia, so no pain is felt during treatment. Because DHI is minimally invasive with no open channels, recovery is fast — mild redness and small scabs settle within days and most patients return to daily life quickly, avoiding strenuous activity, sun and swimming for the first couple of weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see results, and is shedding normal?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The transplanted hairs often shed in the first weeks — this "shock shedding" is completely normal. New growth begins around months three to four, noticeable density develops by six to nine months, and the final, fully matured result appears at about twelve months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent and natural-looking?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The follicles are taken from areas genetically resistant to hair loss, so they keep growing permanently. The direct, angle-controlled implantation gives a completely natural hairline and density — you can cut, wash and style the hair normally. Existing non-transplanted hair can still thin over time, so supportive care may be advised to protect the overall look.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'DHI — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the density and natural hairlines our patients achieve with DHI. Every result is planned around your own growth pattern, so the outcome blends seamlessly with your existing hair and looks completely natural.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready to Restore Your Hair with DHI?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised DHI plan, a graft estimate and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free graft assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Precise, often shave-free direct implantation', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),

					array(
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Explore Further', 'estecapelli' ),
						'title'         => __( 'Related Treatments', 'estecapelli' ),
						'count'         => 3,
						'manual'        => array(),
					),
				),
			),

			// ============================================================
			// VITA Treatment (VITA Protocol)
			// ============================================================
			array(
				'slug'     => 'vita-treatment',
				'title'    => 'VITA Treatment',
				'category' => 'Hair Transplant',
				'sections' => array(

					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Estecapelli Signature Method', 'estecapelli' ),
						'title'         => __( 'VITA Treatment', 'estecapelli' ),
						'lead'          => __( 'Estecapelli’s signature protocol revitalises the scalp, nourishes hair follicles and accelerates healing. A vitamin-derived serum protects graft vitality before and during transplantation to maximise survival and growth.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image',
						'image'         => '',
						'video_id'      => '',
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Method', 'estecapelli' ),
						'title'         => __( 'What is the VITA Protocol?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The VITA Protocol is a two-phase treatment, specifically designed by Estecapelli medical experts, to increase graft survival rates and enhance surgical outcomes during the hair transplant process. It can be used in compatibility with both Sapphire FUE and DHI hair transplant techniques. This protocol combines highly enriched components — including amino acids, vitamins, growth factors and ATP — to nourish hair follicles.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'In the first phase, a serum solution is applied to the scalp via microchannels opened with a dermaroller massage tool, allowing grafts to receive the nutrients they need more effectively. In the second phase, cold air vapour is applied to the grafts after they are harvested and during the waiting period in VITA serum, combined with a saline solution to increase their survival rate.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Science', 'estecapelli' ),
						'title'         => __( 'Estecapelli’s Signature Technique — VITA Power Derived from Vitamins', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The period during which hair follicles remain outside the body is a critical factor that directly affects the procedure’s success. Once grafts are extracted, their access to blood circulation and vital nutrients is interrupted, leading to gradual weakening.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'The VITA Protocol intervenes at these sensitive stages of the operation by providing essential support to the grafts. Thanks to a specially formulated vitamin serum solution and cold air vapour application, it significantly contributes to preserving graft viability in both FUE and DHI techniques.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why It Matters', 'estecapelli' ),
						'title'         => __( 'Advantages of the VITA Protocol', 'estecapelli' ),
						'body'          => __( 'The vitamins, minerals, growth factors and ATP in the VITA serum strengthen follicles and support stronger development:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'shield-check', 'label' => __( 'Protects graft vitality outside the body', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Higher graft survival and retention', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Nourishes follicles with vitamins, minerals and ATP', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Accelerates the healing process', 'estecapelli' ) ),
							array( 'icon' => 'atom',         'label' => __( 'Combines with both FUE and DHI procedures', 'estecapelli' ) ),
							array( 'icon' => 'hair',         'label' => __( 'Ideal for weak follicles, fine hair or limited donor areas', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'How is the VITA Protocol Applied?', 'estecapelli' ),
						'lead'          => __( 'A two-stage protocol that integrates seamlessly into your Sapphire FUE or DHI transplant. Swipe through the stages below.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'medical-plus', 'eyebrow' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Microneedling & Nutrient Serum', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Microchannels are created with a dermaroller massage tool, and a highly concentrated serum of vitamins, amino acids, growth factors and minerals is applied to boost blood circulation. The donor area is ideally prepared for smooth, well-fed harvesting, while the recipient area is nourished with a concentrated vitamin complex that the scalp absorbs far more effectively thanks to the micro-channels.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'atom',         'eyebrow' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Cold-Vapour Serum on Grafts', 'estecapelli' ),  'body' => '<p>' . esc_html__( 'The same nourishing serum is mixed with saline in the graft-harvesting tray and applied to the harvested grafts using a cold-vapour method. This is the most critical window — while follicles are outside the body — so protecting them here prevents deterioration, maximises survival and increases their post-transplantation regrowth potential.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'VITA Treatment — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Is VITA a standalone treatment?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'No — VITA is a protocol that enhances a hair transplant rather than a treatment on its own. It is applied during your Sapphire FUE or DHI procedure to nourish the scalp and protect the grafts, improving their survival and your overall result.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Why does graft survival matter so much?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The time follicles spend outside the body is one of the biggest factors in a transplant’s success. Once harvested, grafts lose access to blood supply and nutrients and begin to weaken. By feeding and cold-protecting them during this window, VITA helps more grafts survive and grow — which means denser, fuller results.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Who benefits most from VITA?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is valuable for almost any transplant, but especially for patients with weak follicles, fine hair, or a limited donor area where every single graft needs to count. Your surgeon will advise whether adding VITA is worthwhile for your specific case.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does VITA work with both FUE and DHI?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The VITA Protocol is fully compatible with both Sapphire FUE and DHI techniques, integrating seamlessly into either procedure without changing how your transplant is performed.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does VITA change recovery or cause side effects?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'By nourishing the scalp and grafts, the protocol supports a faster, healthier healing process. It uses vitamins, minerals, amino acids and growth factors naturally suited to the body, so it adds no meaningful downtime or side effects beyond those of the transplant itself.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'VITA-Supported Transplants — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the density and natural results our patients achieve when the VITA Protocol supports their Sapphire FUE or DHI transplant — stronger graft survival translating into a fuller, healthier head of hair.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Want VITA Added to Your Transplant?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised transplant plan, explain how the VITA Protocol can boost your graft survival, and send a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free graft assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Maximised graft survival with the VITA Protocol', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),

					array(
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Explore Further', 'estecapelli' ),
						'title'         => __( 'Related Treatments', 'estecapelli' ),
						'count'         => 3,
						'manual'        => array(),
					),
				),
			),

			// ============================================================
			// Eyebrow Transplant
			// ============================================================
			array(
				'slug'     => 'eyebrow-transplant',
				'title'    => 'Eyebrow Transplant',
				'category' => 'Hair Transplant',
				'sections' => array(

					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Precise Microsurgery', 'estecapelli' ),
						'title'         => __( 'Eyebrow Transplant', 'estecapelli' ),
						'lead'          => __( 'A precise microsurgical procedure that restores eyebrows that have thinned, fallen out or lost their shape. Each single follicle is placed at the right angle and direction for a permanent, completely natural brow.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image',
						'image'         => array( 'url' => get_template_directory_uri() . '/assets/images/treatments/eyebrow-transplant.webp', 'alt' => __( 'Eyebrow transplant performed with a Choi implanter pen', 'estecapelli' ), 'width' => 1448, 'height' => 1086 ),
						'video_id'      => '',
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'What is Eyebrow Transplantation?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Eyebrow transplantation is a precise microsurgical procedure designed to restore eyebrows that have thinned, fallen out or lost their natural shape due to aging, trauma, medical conditions or over-plucking. During the procedure, individual hair follicles are carefully harvested from the nape (back of the scalp) and transplanted into the eyebrow area. Each follicle is placed at the exact angle and direction to mimic natural eyebrow growth.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'The main goal is to create full, natural-looking eyebrows that complement your facial features. Once healed, the transplanted hairs grow permanently, providing a low-maintenance, aesthetic result that doesn’t rely on makeup.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why It Is Preferred', 'estecapelli' ),
						'title'         => __( 'Why is Eyebrow Transplantation Preferred?', 'estecapelli' ),
						'body'          => __( 'Eyebrows are one of the most defining elements of facial expression. Eyebrow transplantation provides a permanent and effective solution in the following situations:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Years of incorrect eyebrow shaping', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Sudden hair loss', 'estecapelli' ) ),
							array( 'icon' => 'atom',         'label' => __( 'Hormonal changes', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Stress or nutritional deficiencies', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Gaps caused by injury, burns or trauma', 'estecapelli' ) ),
							array( 'icon' => 'face',         'label' => __( 'Deformations caused by permanent makeup or microblading', 'estecapelli' ) ),
							array( 'icon' => 'dna',          'label' => __( 'Genetically sparse eyebrows', 'estecapelli' ) ),
						),
						'footer'        => __( 'With the advancement of techniques, eyebrow transplantation has become one of the most preferred procedures in recent years due to its natural and aesthetic results.', 'estecapelli' ),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'How is Eyebrow Transplantation Performed?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The eyebrow transplant procedure follows principles similar to hair transplantation; however, it requires a much higher level of aesthetic precision.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'Step by Step', 'estecapelli' ),
						'title'         => __( 'The Eyebrow Transplant Stages', 'estecapelli' ),
						'lead'          => __( 'Designed around your facial proportions for a natural, lasting result. Swipe through the stages below.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'sparkles',     'eyebrow' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Eyebrow Design', 'estecapelli' ),       'body' => '<p>' . esc_html__( 'A natural eyebrow shape is drawn by taking your facial proportions, muscle structure and personal preferences into account. This is the most critical part of the procedure — the design determines the entire result, so it is agreed with you in detail before any follicles are placed.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'target',       'eyebrow' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Graft Harvesting', 'estecapelli' ),     'body' => '<p>' . esc_html__( 'Hair follicles are carefully extracted from the donor area at the back of the scalp using a specialised micromotor. Since the donor area naturally contains a mixture of single-, double- and multi-hair follicular units, the extracted grafts are meticulously sorted under magnification. During this microscopic graft-sorting process, single-hair follicles are separated and selected for implantation, as they provide the soft, delicate and natural-looking appearance required for eyebrow transplantation.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'check-circle', 'eyebrow' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'DHI Implantation', 'estecapelli' ),     'body' => '<p>' . esc_html__( 'The DHI method is preferred for eyebrows: using special Choi pens, the channel is opened and the follicle implanted in one motion. This gives precise control over the very shallow angle and direction each brow hair needs to lie flat and grow naturally.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'calendar',     'eyebrow' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Procedure Duration', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'The procedure takes approximately two to three hours and is performed under local anaesthesia, so it is comfortable and pain-free. It is carried out as a day procedure, and you can return to your hotel or home the same day.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After the Procedure', 'estecapelli' ),
						'title'         => __( 'What is the Post-Eyebrow Transplantation Process Like?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The first day after the procedure, mild redness and sensitivity are normal. Within two weeks, mild scabbing occurs in the area and falls off. During the same period, the transplanted hairs may temporarily shed; this process is called shock loss.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'From the third month onward, new eyebrows begin to grow. It takes approximately 9 to 12 months for the eyebrows to fully settle and achieve a natural appearance. Since eyebrow transplantation uses fewer grafts compared to hair transplantation, the recovery process is much faster and more comfortable.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Best Technique', 'estecapelli' ),
						'title'         => __( 'Which Method Should Be Used for Eyebrow Transplantation?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'For eyebrow transplantation, both the DHI (Direct Hair Implantation) and Sapphire FUE techniques can be used, depending on the patient’s eyebrow condition, donor characteristics and the desired aesthetic outcome.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'The DHI method uses specialised Choi implanter pens, allowing each graft to be implanted immediately after placement in a single step. This provides excellent control over angle, direction and depth — particularly useful for the very fine, hair-by-hair alignment required for natural-looking eyebrows.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'The Sapphire FUE technique, on the other hand, involves first creating recipient channels with sapphire blades before placing the grafts, letting the surgeon carefully design the overall shape, symmetry and density distribution of the eyebrow area with high precision.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'In practice, both techniques are effective: DHI is often preferred for its direct control of hair direction at a micro level, while Sapphire FUE can be advantageous for structured design and precise planning of the eyebrow framework. The choice is always made based on individual anatomy and the most natural aesthetic result.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Eyebrow Transplant — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Will the result look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Using only single follicles, implanted hair by hair at the correct shallow angle and direction with the DHI method, creates a brow that looks and behaves completely naturally — the shape is designed to suit your face, not a one-size template.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will I have to trim my eyebrows? They grow like scalp hair, right?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Because the follicles come from the nape of your scalp, they keep their original growth behaviour and will grow longer than normal brow hairs. This is completely normal — you simply trim them every couple of weeks. Over time they often adapt and slow down, and the upkeep is minimal.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does the procedure take, and is it painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It takes around two to three hours and is performed under local anaesthesia, so you feel no pain during the session. It is a comfortable day procedure, and because far fewer grafts are needed than a scalp transplant, recovery is quick.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see the final result?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The transplanted hairs usually shed within the first weeks — this shock loss is normal. New brow hairs begin to grow from around month three, and it takes roughly nine to twelve months for the eyebrows to fully settle into their final, natural appearance.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Am I a good candidate?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Eyebrow transplantation suits anyone with brows that have thinned, scarred or lost shape from over-plucking, ageing, hormonal changes, injury or permanent makeup, as long as there are healthy donor follicles at the nape. A consultation with clear photos confirms your suitability and the design.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The transplanted brow follicles are taken from a permanent donor area, so after the initial shedding phase they grow in for a lasting, low-maintenance result that does not rely on makeup or microblading.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Eyebrow Transplant — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the natural, fuller brows our patients achieve. Every eyebrow is designed around your facial proportions and implanted hair by hair, so the result frames your face and looks completely natural.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready for Fuller, Natural Eyebrows?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised eyebrow transplant plan, a brow design suited to your face and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free brow assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Single-follicle DHI for a completely natural look', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),

					array(
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Explore Further', 'estecapelli' ),
						'title'         => __( 'Related Treatments', 'estecapelli' ),
						'count'         => 3,
						'manual'        => array(),
					),
				),
			),

			// ============================================================
			// Beard Transplant
			// ============================================================
			array(
				'slug'     => 'beard-transplant',
				'title'    => 'Beard Transplant',
				'category' => 'Hair Transplant',
				'sections' => array(

					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Natural & Permanent', 'estecapelli' ),
						'title'         => __( 'Beard Transplant', 'estecapelli' ),
						'lead'          => __( 'A natural and permanent beard look with Estecapelli. Beard transplantation restores fullness to sparse, uneven or patchy areas by transplanting your own follicles — grown in alignment with your facial features.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image',
						'image'         => array( 'url' => get_template_directory_uri() . '/assets/images/treatments/beard-transplant.webp', 'alt' => __( 'Beard transplant procedure at Estecapelli', 'estecapelli' ), 'width' => 1254, 'height' => 1254 ),
						'video_id'      => '',
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'What is Beard Transplantation?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The beard is one of the most defining aesthetic features for men, projecting strength, confidence and character. A full, well-groomed beard not only enhances self-confidence but also sculpts and defines facial structure.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Yet for many men, achieving that ideal beard is not straightforward. Genetic factors, hormonal imbalances, trauma or scarring can lead to thinning, patchiness or uneven growth. Beard transplantation offers a permanent, natural-looking solution to these challenges.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'At Estecapelli, our specialists bring deep expertise to every beard transplant procedure. Using the same proven principles as hair transplantation, our team applies advanced techniques to deliver results that are both natural in appearance and permanent — giving you the beard you have always wanted, for life.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'How It Works', 'estecapelli' ),
						'title'         => __( 'How Does a Beard Transplant Work?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A beard transplant is a surgical procedure that restores fullness to the beard by transplanting hair follicles into sparse, uneven or hairless areas. At Estecapelli, our expert team creates a personalised beard design tailored to each individual’s facial features and expectations. The procedure is performed under local anaesthesia and is generally painless throughout. Recovery time varies from person to person, but most patients return to their social lives relatively quickly, and following the aftercare guidelines provided by our team helps speed up healing and ensures the best possible results.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Depending on the beard area and the result you want, the grafts can be placed using either the Sapphire FUE technique or the DHI (Choi pen) method — your specialist recommends the most suitable approach during your consultation.', 'estecapelli' ) . '</p>',
						'image'         => array( 'url' => get_template_directory_uri() . '/assets/images/treatments/beard-placement.webp', 'alt' => __( 'Placing beard grafts one by one', 'estecapelli' ), 'width' => 870, 'height' => 870 ),
						'image_position' => 'left',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'Stages of Beard Transplantation', 'estecapelli' ),
						'lead'          => __( 'Careful harvesting and precise placement for a natural beard line. Swipe through the stages below.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'target',       'eyebrow' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Graft Harvesting', 'estecapelli' ),  'body' => '<p>' . esc_html__( 'The FUE method is used to harvest follicles from the safe donor area at the back and sides of the head, where hair is genetically resistant to loss. Each follicle is extracted one by one with ultra-fine micromotors to minimise damage and preserve the natural look of the donor area.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'sparkles',     'eyebrow' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Channel Opening', 'estecapelli' ),   'body' => '<p>' . esc_html__( 'The direction, depth and angle of the channels are the most decisive factors for a natural result. Our experts create micro-channels that follow the natural growth pattern of a beard — flatter and more downward than scalp hair — so each follicle sits seamlessly within the surrounding hair and defines a clean beard line.', 'estecapelli' ) . '</p>' ),
							array( 'icon' => 'check-circle', 'eyebrow' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Graft Implantation', 'estecapelli' ), 'body' => '<p>' . esc_html__( 'Finally, the harvested grafts are placed into the prepared channels one by one, each at the correct angle and depth. This precise placement fills patchy or sparse areas evenly for a fuller, well-defined beard that looks and feels completely natural and grows in with your existing hair.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Advantages and Possible Risks of Beard Transplantation', 'estecapelli' ),
						'body'          => '<p><strong>' . esc_html__( 'Advantages.', 'estecapelli' ) . '</strong> ' . esc_html__( 'One of the greatest advantages of beard transplantation is that the results are both permanent and natural-looking. The beard grows in alignment with its surrounding hair, making it virtually indistinguishable from natural growth. Beyond aesthetics, a fuller and well-defined beard can have a meaningful impact on self-confidence and overall appearance.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Possible Risks.', 'estecapelli' ) . '</strong> ' . esc_html__( 'As with any medical procedure, beard transplantation carries some minor, temporary side effects. These typically include redness, swelling and mild sensitivity in the treated area in the days following the procedure, all of which subside on their own with proper aftercare. Serious complications are rare, particularly when the procedure is performed by an experienced team. At Estecapelli, our specialists take every precaution to ensure a safe procedure and a smooth recovery.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Beard Transplant — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Am I a good candidate for a beard transplant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'If you have patchy, sparse or uneven beard growth, gaps from scarring or injury, or simply struggle to grow a full beard, you are likely a good candidate — provided you have a healthy donor area at the back and sides of the scalp. A consultation with clear photos confirms your suitability and estimates the grafts you need.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Where do the grafts come from, and will the beard look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Healthy follicles are harvested with the FUE method from the safe donor area at the back and sides of the head. Careful control of the angle, depth and direction of each graft — following the flatter, downward growth pattern of a beard — ensures the result blends seamlessly with your existing hair and looks completely natural.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can I shave and shape it normally afterwards?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Once fully healed, your transplanted beard behaves like natural beard hair — you can shave it, trim it, grow it out and style it however you like. Because the follicles are permanent, the fullness remains even as your look changes over time.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is it painful, and what is recovery like?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The procedure is performed under local anaesthesia, so it is generally painless. Mild redness, swelling and small scabs in the treated area are normal for the first week and settle with proper aftercare; most patients return to their social life within one to two weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see results, and is shedding normal?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The transplanted hairs usually shed in the first few weeks — this shock loss is completely normal. New beard growth begins around months three to six, and the full, final appearance forms over six to twelve months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is a beard transplant permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The follicles are taken from a permanent donor area, so once they settle they grow for life. Performed with the right technique by an experienced team, a beard transplant delivers natural, long-lasting results.', 'estecapelli' ) . '</p>' ),
						),
					),

					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Beard Transplant — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the fuller, well-defined beards our patients achieve. Every beard line is designed around your facial features and implanted hair by hair, so patchy areas fill in evenly and the result looks completely natural.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),

					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready for a Fuller, Natural Beard?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised beard transplant plan, a beard design suited to your face and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free graft assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Natural beard line designed around your face', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),

					array(
						'acf_fc_layout' => 'related',
						'eyebrow'       => __( 'Explore Further', 'estecapelli' ),
						'title'         => __( 'Related Treatments', 'estecapelli' ),
						'count'         => 3,
						'manual'        => array(),
					),
				),
			),

			// ============================================================
			// Rhinoplasty (Plastic Surgery)
			// ============================================================
			array(
				'slug'     => 'rhinoplasty',
				'title'    => 'Rhinoplasty',
				'category' => 'Plastic Surgery',
				'sections' => array(
					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Nose Aesthetics', 'estecapelli' ),
						'title'         => __( 'Rhinoplasty', 'estecapelli' ),
						'lead'          => __( 'A surgical procedure that reshapes the nose — improving its form, size and function. Performed for both aesthetic refinement and breathing concerns.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image', 'image' => '', 'video_id' => '',
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'What is Rhinoplasty?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Rhinoplasty is a surgical procedure designed to reshape the nose by improving its form, size and function. It can be performed for both aesthetic purposes and to correct breathing problems. Because the nose is the most prominent feature at the center of the face, even subtle changes can make a noticeable difference in facial harmony and self-confidence.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'At Estecapelli, every rhinoplasty procedure is carefully planned on an individual basis. Our specialists evaluate the overall facial structure and consider facial proportions and balance to achieve natural-looking results. All surgeries are performed by experienced ear, nose and throat (ENT) specialists and plastic surgeons, ensuring both aesthetic refinement and functional improvement.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why It Is Done', 'estecapelli' ),
						'title'         => __( 'Why is Rhinoplasty Done?', 'estecapelli' ),
						'body'          => __( 'People choose rhinoplasty for a variety of reasons, related to both aesthetic appearance and functional concerns. Some of the most common reasons include:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'face',         'label' => __( 'A nose that appears too large or too small in proportion to the face', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'A noticeable hump on the nasal bridge', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'A drooping, wide or poorly defined nasal tip', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Asymmetrical nostrils', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Deformities caused by previous injury or trauma', 'estecapelli' ) ),
							array( 'icon' => 'dna',          'label' => __( 'Dissatisfaction with the natural, genetically inherited shape of the nose', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Breathing difficulties caused by structural issues such as a deviated septum or internal nasal curvature', 'estecapelli' ) ),
						),
						'footer'        => __( 'At Estecapelli, the aim of rhinoplasty is to create a natural-looking nose that complements the overall facial features while also improving breathing function when necessary.', 'estecapelli' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Candidates', 'estecapelli' ),
						'title'         => __( 'Who Can Have Rhinoplasty?', 'estecapelli' ),
						'body'          => __( 'Rhinoplasty may be a suitable option for individuals who wish to improve both the appearance and function of their nose. Ideal candidates typically include:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'check-circle', 'label' => __( 'Adults whose facial growth and physical development are complete', 'estecapelli' ) ),
							array( 'icon' => 'face',         'label' => __( 'Individuals who feel dissatisfied with the appearance or shape of their nose', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'People who experience breathing difficulties due to structural nasal issues', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Those with nasal deformities caused by genetics, injury or previous trauma', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Individuals seeking a more balanced, natural and harmonious facial appearance', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Techniques', 'estecapelli' ),
						'title'         => __( 'Types of Rhinoplasty', 'estecapelli' ),
						'body'          => '<ul><li><strong>' . esc_html__( 'Open Rhinoplasty', 'estecapelli' ) . '</strong> — ' . esc_html__( 'a surgical technique in which the nasal structure is fully exposed through a small incision made under the nostrils. It is the most ideal method for major structural changes, significant deviations and cases requiring revision surgery.', 'estecapelli' ) . '</li>'
							. '<li><strong>' . esc_html__( 'Closed Rhinoplasty', 'estecapelli' ) . '</strong> — ' . esc_html__( 'a surgical technique in which all incisions are made inside the nose, leaving no visible external scars. It offers a faster recovery process and minimal tissue trauma. However, it may not be suitable for every nose; eligibility is determined through a medical examination.', 'estecapelli' ) . '</li>'
							. '<li><strong>' . esc_html__( 'Tipplasty (Nose Tip Surgery)', 'estecapelli' ) . '</strong> — ' . esc_html__( 'an aesthetic procedure performed solely on the nasal tip. Without intervening in the nasal bridge or bone structure, the tip of the nose is reshaped. The procedure takes less time and offers a faster recovery period.', 'estecapelli' ) . '</li>'
							. '<li><strong>' . esc_html__( 'Septum Deviation Surgery', 'estecapelli' ) . '</strong> — ' . esc_html__( 'the correction of cartilage and bone curvature in the midline of the nose. The primary goal is to eliminate breathing problems. The external appearance of the nose is not altered; only nasal function is improved.', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Before Surgery', 'estecapelli' ),
						'title'         => __( 'The Pre-Operative Process', 'estecapelli' ),
						'body'          => __( 'The pre-operative stage is essential for a successful rhinoplasty. At Estecapelli, each patient undergoes a careful evaluation to ensure both safety and the best possible results. Before surgery, the following steps are carried out:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'clipboard',    'label' => __( 'Review of the patient’s medical history and current health conditions', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Discussion of aesthetic expectations and desired results', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Examination of the internal and external nasal structure', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Blood tests and anaesthesia assessment', 'estecapelli' ) ),
							array( 'icon' => 'image',        'label' => __( 'Medical photographs for analysis and surgical planning', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Temporary discontinuation of blood-thinning medications', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Avoiding alcohol and smoking before surgery to support proper healing', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Operation', 'estecapelli' ),
						'title'         => __( 'How is Rhinoplasty Performed?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Operation steps:', 'estecapelli' ) . '</p>'
							. '<ol><li>' . esc_html__( 'Administration of anaesthesia', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Making incisions inside or outside the nose', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Reshaping the cartilage and bone structure', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Opening the airways if necessary', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Aesthetic adjustment of the nasal tip', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Closing the incisions and applying a splint', 'estecapelli' ) . '</li></ol>'
							. '<p>' . esc_html__( 'Most patients are discharged the same day or the next day after the operation.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'The Recovery Process After Rhinoplasty', 'estecapelli' ),
						'lead'          => __( 'A general guide to the rhinoplasty recovery timeline.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'time' => __( 'First 24 Hours', 'estecapelli' ), 'title' => __( 'Mild Swelling', 'estecapelli' ),   'body' => __( 'Mild pain and swelling may occur.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'First Week', 'estecapelli' ),     'title' => __( 'Bruising Fades', 'estecapelli' ),  'body' => __( 'Most bruising begins to subside.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Day 7', 'estecapelli' ),          'title' => __( 'Splint Removed', 'estecapelli' ),  'body' => __( 'The splint is usually removed.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( '1 Month', 'estecapelli' ),        'title' => __( 'Major Healing', 'estecapelli' ),   'body' => __( 'Significant healing takes place.', 'estecapelli' ) ),
							array( 'icon' => 'star',         'time' => __( '6–12 Months', 'estecapelli' ),    'title' => __( 'Final Shape', 'estecapelli' ),     'body' => __( 'The final shape of the nose gradually settles and fully matures.', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Risks of Rhinoplasty', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'As with any surgical procedure, rhinoplasty carries some potential risks, such as mild swelling, temporary bleeding, infection or healing-related issues. However, these complications are uncommon and can be significantly minimised when the procedure is performed by experienced surgeons using proper techniques.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Rhinoplasty — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the refined, balanced noses our patients achieve — always in harmony with the rest of the face. Every result is planned around your individual features for a natural look.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Rhinoplasty — FAQ', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Am I a good candidate for rhinoplasty?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Good candidates are adults whose facial growth is complete — generally from the late teens onwards — who are in good general health and bothered by the shape, size or proportion of the nose, or by a breathing difficulty. Realistic expectations matter: the goal is a nose that suits your face, not a copy of someone else’s. During consultation your surgeon assesses your nasal structure, skin type and breathing to confirm suitability and plan the result with you.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What is the difference between open and closed rhinoplasty?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'In closed rhinoplasty all incisions are made inside the nostrils, so there is no external scar; it suits cases needing more limited reshaping. Open rhinoplasty adds a tiny incision across the columella (the strip of skin between the nostrils), giving the surgeon fuller visibility for more complex or revision work. The small open-technique scar is well hidden and usually unnoticeable once healed. Your surgeon recommends the approach that gives the most reliable result for your nose.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What anaesthesia is used and how long does surgery take?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Rhinoplasty is performed under general anaesthesia, so you feel nothing during the operation, which usually takes about two to three hours depending on the technique and whether functional correction is included. You can normally return home or to your hotel the same day or after one night, following a routine check.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery and the stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most patients stay in Turkey for around seven days, until the splint is removed and a follow-up check is done before flying home. A cast or splint is worn for the first week, and swelling and bruising around the eyes fade over one to two weeks. Most people return to work and light routine within ten to fourteen days, while the nose continues to refine.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see the final result?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'You will notice a clear difference once the splint comes off, but the nose settles gradually as the deep swelling resolves. Most of the shape is apparent within a few months, while the final, fully refined result — especially at the tip — emerges over roughly six to twelve months. Your surgeon guides you through each stage of healing.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will rhinoplasty affect my breathing?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It should not make breathing worse — and it often improves it. Functional rhinoplasty can be combined with the aesthetic procedure to correct issues such as a deviated septum or narrow airways, so you can address appearance and breathing in a single operation. Tell your surgeon about any nasal congestion or breathing difficulty so it can be assessed and treated.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent and natural-looking?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Rhinoplasty gives permanent results, and in experienced hands the nose looks natural and balanced with the rest of your face rather than “operated on”. Minor changes can continue over the first year as healing completes. A small percentage of patients choose a minor revision later, which your surgeon will discuss honestly if it is ever relevant.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'Rhinoplasty Prices', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Rhinoplasty pricing varies depending on the technique used, the scope of the procedure and your individual needs and anatomy. A personalised quote is provided following a detailed consultation, ensuring full transparency before any decisions are made.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready for a Nose That Suits Your Face?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised rhinoplasty plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

			// ============================================================
			// BBL — Brazilian Butt Lift (Plastic Surgery)
			// ============================================================
			array(
				'slug'     => 'bbl',
				'title'    => 'BBL (Brazilian Butt Lift)',
				'category' => 'Plastic Surgery',
				'sections' => array(
					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Body Contouring', 'estecapelli' ),
						'title'         => __( 'Brazilian Butt Lift (BBL)', 'estecapelli' ),
						'lead'          => __( 'A popular body-contouring procedure that enhances the shape, volume and lift of the buttocks for a fuller, more balanced silhouette — using your own fat.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image', 'image' => '', 'video_id' => '',
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'What is a Brazilian Butt Lift?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Brazilian Butt Lift (BBL) is a popular body-contouring procedure designed to enhance the shape, volume and lift of the buttocks, creating a fuller and more sculpted appearance.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'The most common technique involves transferring the patient’s own fat to the buttocks. Excess fat is first removed from areas such as the waist, abdomen or hips through liposuction and then carefully injected into the buttocks to improve shape and projection.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'This modern approach not only enhances the buttocks but also slims surrounding areas, helping to create a more balanced and feminine body contour.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Candidates', 'estecapelli' ),
						'title'         => __( 'Who is BBL Suitable For?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Brazilian Butt Lift (BBL) is a body-contouring procedure suitable for both women and men who wish to enhance the shape and volume of their buttocks.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Ideal candidates are generally individuals in good overall health who feel their buttocks lack volume, shape or projection, and who wish to achieve a more balanced waist–hip proportion. Patients with excess fat in areas such as the waist, hips or abdomen are often especially suitable for fat transfer BBL.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'For individuals who do not have enough fat tissue for transfer, buttock augmentation with silicone implants may also be considered as an alternative option.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'What It Improves', 'estecapelli' ),
						'title'         => __( 'What Problems Does BBL Solve?', 'estecapelli' ),
						'body'          => __( 'Brazilian Butt Lift (BBL) can improve a range of aesthetic and proportion-related concerns, including:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'target',      'label' => __( 'Flat or shapeless buttocks', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',    'label' => __( 'Lack of waist definition', 'estecapelli' ) ),
							array( 'icon' => 'star',        'label' => __( 'Underdeveloped hip curves', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart', 'label' => __( 'Volume loss or sagging after weight changes', 'estecapelli' ) ),
							array( 'icon' => 'check-circle','label' => __( 'Imbalance between the upper and lower body', 'estecapelli' ) ),
						),
						'footer'        => __( 'At Estecapelli, the focus is not just on enhancing the buttocks, but on creating a balanced and harmonious body contour by considering the waist, hips and abdomen as a whole.', 'estecapelli' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Fat Transfer BBL', 'estecapelli' ),
						'title'         => __( 'Brazilian Butt Lift with Fat Transfer', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The most popular approach for Brazilian Butt Lift (BBL) uses the patient’s own fat and involves two main steps:', 'estecapelli' ) . '</p>'
							. '<ol><li><strong>' . esc_html__( 'Liposuction (Fat Removal)', 'estecapelli' ) . '</strong> — ' . esc_html__( 'excess fat is gently removed from areas such as the abdomen, waist, hips, back or legs. This step also helps refine the waistline, enhancing an “hourglass” silhouette.', 'estecapelli' ) . '</li>'
							. '<li><strong>' . esc_html__( 'Fat Transfer (Buttock Injection)', 'estecapelli' ) . '</strong> — ' . esc_html__( 'the harvested fat is purified and then carefully injected into the upper and outer buttocks, following natural anatomical contours. The goal is to create a rounded, lifted and balanced buttock shape.', 'estecapelli' ) . '</li></ol>'
							. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
							. '<ul><li>' . esc_html__( 'Uses your own fat, with no foreign material', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Slims the waist and abdomen while enhancing the buttocks', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Minimal, well-hidden scars', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Natural look and feel', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Alternative', 'estecapelli' ),
						'title'         => __( 'Buttock Augmentation with Silicone Implants', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Silicone buttock implants are an alternative for patients who do not have enough fat for a fat transfer BBL. In this procedure:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'A small incision is made near the tailbone', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'A specially designed silicone implant is placed either under or above the gluteal muscle', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The implants are made from materials safe for long-term use in the body', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'This method provides an effective way to enhance buttock volume and shape, especially for patients who are very lean or have limited fat reserves.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Operation', 'estecapelli' ),
						'title'         => __( 'How is BBL Surgery Performed?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'At Estecapelli, the Brazilian Butt Lift is performed under general anaesthesia in fully equipped operating room conditions. The average surgery time may vary between 2–4 hours depending on the technique used and combined procedures. The process is summarised as follows:', 'estecapelli' ) . '</p>'
							. '<ol><li>' . esc_html__( 'Pre-examination and body analysis', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Determination of buttock design and target volume', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Planning of liposuction areas', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Preoperative required examinations and blood tests', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Operation performed under general anaesthesia', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Fat transfer or placement of silicone implant', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Postoperative corset application and creation of a follow-up plan', 'estecapelli' ) . '</li></ol>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery Process & Sitting Rules After BBL', 'estecapelli' ),
						'body'          => '<ul><li>' . esc_html__( 'Avoid sitting directly on the buttocks for the first 2–3 weeks', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Pressure should be transferred to the thighs using special BBL pillows', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Sleeping on the stomach or side is preferred during the first days', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The compression garment should be used for the period recommended by your surgeon (usually 4–6 weeks)', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Heavy sports and intense exercise are not recommended during the first 4 weeks', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'In a BBL performed with fat transfer, this period is critical for the transferred fat cells to establish a new blood supply and become permanent.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Results', 'estecapelli' ),
						'title'         => __( 'When Do BBL Results Appear and How Long Do They Last?', 'estecapelli' ),
						'body'          => '<ul><li>' . esc_html__( 'Fat cells that become permanent stay for years unless major weight changes occur', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'With silicone implants the volume is more predictable and more defined; results become clearer after the swelling subsides', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'More stable and true results appear after 3–6 months', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'About 20–30% of fat cells may be absorbed naturally by the body in the first 3 months', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Safety', 'estecapelli' ),
						'title'         => __( 'Is BBL Safe?', 'estecapelli' ),
						'body'          => __( 'BBL has become one of the fastest-growing body-contouring procedures worldwide. While its popularity has risen, safety concerns have emerged in the past, often due to incorrect techniques or inexperienced practitioners. To ensure a safe BBL procedure:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'shield-check', 'label' => __( 'Fat is injected only into the subcutaneous (under-the-skin) layer', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Muscle or deep tissue injections are strictly avoided', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Fat volumes are kept within safe limits', 'estecapelli' ) ),
							array( 'icon' => 'star',         'label' => __( 'Procedures are performed by experienced plastic surgeons', 'estecapelli' ) ),
							array( 'icon' => 'building',     'label' => __( 'Surgeries take place in fully equipped operating rooms or hospital settings', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'BBL — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the fuller, rounder and more lifted shape our patients achieve. Every BBL is tailored to the individual body for a natural, balanced silhouette.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'BBL — FAQ', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Who is a good candidate for a BBL?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Good candidates are adults in good general health who want more buttock volume, a rounder shape or a better waist-to-hip balance, and who have enough body fat elsewhere to harvest for the transfer. Very slim patients may have too little donor fat for a meaningful result. A stable weight and realistic expectations give the best outcome, and your surgeon will confirm your suitability at consultation.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How is a Brazilian Butt Lift actually performed?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A BBL is a two-stage operation done in one session. First, liposuction removes fat from areas such as the abdomen, waist, flanks or back — which also sculpts the waistline. The fat is then purified and carefully re-injected into the buttocks at different depths to build a natural, rounded shape. Because it uses your own tissue, there is no implant in a standard BBL.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What anaesthesia is used and how long does surgery take?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A BBL is performed under general anaesthesia, so you feel nothing during the procedure, which usually takes two to four hours depending on the number of liposuction areas and the volume transferred. You stay under close monitoring afterwards and are typically discharged with detailed aftercare instructions.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery, and when can I sit normally?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Initial recovery takes about two to three weeks, with fuller recovery over six to eight weeks. To protect the transferred fat, avoid sitting directly on your buttocks for the first two to three weeks — a special BBL cushion lets you sit safely — and sleep on your front or side. Light walking is encouraged early to aid circulation, while a compression garment is worn to support healing of the liposuction areas.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long should I stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most international patients stay in Turkey for around seven to ten days, which allows for the procedure, initial healing and a follow-up check before flying home. Because long-haul sitting is discouraged early on, your patient-care team will advise on the safest timing for your return flight and on using a cushion during travel.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent and natural-looking?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Part of the transferred fat is naturally reabsorbed in the first months, while the fat that establishes a blood supply stays permanently — which is why surgeons account for this when planning volume. Final results usually become clear within three to six months once swelling settles. Because a BBL uses your own fat and shapes the surrounding areas at the same time, the result looks and feels very natural, and a stable weight helps maintain it.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'BBL Prices', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'BBL pricing varies depending on the technique used (fat transfer or implants), the scope of the procedure, any combined treatments and your individual needs and anatomy. A personalised quote is provided following a detailed consultation, ensuring full transparency before any decisions are made.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready for a Fuller, More Balanced Shape?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised BBL plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

			// ============================================================
			// Breast Aesthetics (Plastic Surgery)
			// ============================================================
			array(
				'slug'     => 'breast-aesthetics-breast-surgery',
				'title'    => 'Breast Aesthetics',
				'category' => 'Plastic Surgery',
				'sections' => array(
					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Breast Surgery', 'estecapelli' ),
						'title'         => __( 'Breast Aesthetics', 'estecapelli' ),
						'lead'          => __( 'Achieve breasts that are harmonious with your body — full and natural-looking. Our personalised planning ensures aesthetic, balanced results tailored to your unique figure and goals.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image', 'image' => '', 'video_id' => '',
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'What is Breast Aesthetics (Breast Surgery)?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Breast aesthetics is the general term for aesthetic surgical procedures performed to improve the size, shape and position of the breasts in harmony with the face and overall body proportions. The most commonly performed procedures include breast augmentation, breast reduction and breast lift surgery.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'At Estecapelli, breast aesthetics is about more than simply enlarging or reducing the breasts. It is about creating a balanced, natural silhouette that complements the entire body. Every treatment plan is carefully personalised to each patient’s unique anatomy and aesthetic goals. Our ultimate aim is to help patients feel better both physically and emotionally, achieving a body form they feel truly confident and comfortable in.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Options', 'estecapelli' ),
						'title'         => __( 'Types of Breast Surgery', 'estecapelli' ),
						'lead'          => __( 'The right procedure depends on your anatomy, your goals and the look you want. Swipe through the main options below.', 'estecapelli' ),
						'items'         => array(
							array(
								'icon'    => 'sparkles',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Breast Augmentation', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Breast augmentation increases breast volume and improves shape using silicone implants or, for suitable patients, fat transfer. It is ideal for genetically small breasts or volume loss after pregnancy, breastfeeding or weight change.', 'estecapelli' ) . '</p>'
									. '<ul><li>' . esc_html__( 'Adds fullness and balance to the figure', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Implant shape and size chosen with you', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Discreet incision and natural-looking result', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'target',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Breast Reduction', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Breast reduction removes excess breast tissue, fat and skin to create a lighter, firmer and more proportionate breast. It relieves the neck, back and shoulder strain often caused by overly large breasts.', 'estecapelli' ) . '</p>'
									. '<ul><li>' . esc_html__( 'Eases physical discomfort and posture problems', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Reshapes and lifts at the same time', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'A more balanced, comfortable silhouette', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'hands-heart',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Breast Lift (Mastopexy)', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'A breast lift raises and reshapes sagging breasts by removing excess skin and tightening the surrounding tissue, restoring a firmer, more youthful position without necessarily changing the size. It can be combined with an implant for added volume.', 'estecapelli' ) . '</p>'
									. '<ul><li>' . esc_html__( 'Corrects sagging after pregnancy, ageing or weight loss', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Repositions the nipple for a natural look', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Can be combined with augmentation for volume', 'estecapelli' ) . '</li></ul>',
							),
						),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'When It Helps', 'estecapelli' ),
						'title'         => __( 'When is Breast Augmentation Performed?', 'estecapelli' ),
						'body'          => __( 'Breast augmentation surgery is an ideal solution in the following cases:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'dna',         'label' => __( 'Genetically small breast structure', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',    'label' => __( 'Breasts that appear small relative to overall body proportions', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart', 'label' => __( 'Loss of volume and shape due to aging, weight loss, pregnancy or breastfeeding', 'estecapelli' ) ),
							array( 'icon' => 'target',      'label' => __( 'A noticeable size difference between the two breasts', 'estecapelli' ) ),
							array( 'icon' => 'star',        'label' => __( 'Asymmetry or unevenness that affects confidence and clothing fit', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus','label' => __( 'Volume restoration following breast cancer surgery or reconstruction', 'estecapelli' ) ),
						),
						'footer'        => __( 'At Estecapelli, the goal of breast augmentation is not only to increase volume but to create a natural-looking breast form that suits the patient’s body proportions, posture and personal expectations.', 'estecapelli' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'How It Works', 'estecapelli' ),
						'title'         => __( 'How is Breast Augmentation Performed?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Breast augmentation surgery is performed under general anaesthesia in a fully equipped operating room and typically takes between 1.5 and 2 hours to complete. During the procedure, implants are carefully placed to achieve the most natural and proportionate result possible. Patients are generally discharged the day after surgery, following a routine post-operative check-up.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Implant Selection', 'estecapelli' ),
						'title'         => __( 'Choosing a Silicone Implant: Round or Teardrop?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Breast implants vary in shape, volume, surface texture and filling content. Choosing the right implant is one of the most important steps in achieving a natural and proportionate result, and the ideal option differs from patient to patient based on body structure, existing breast tissue and personal aesthetic goals. At Estecapelli, implant selection is made together with the patient through detailed consultation and careful evaluation.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Surgical Detail', 'estecapelli' ),
						'title'         => __( 'Incision Site and Implant Placement', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'In breast augmentation surgery, the incision can be made at one of three entry points:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Underarm', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Under the breast', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Around the nipple', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'The choice of incision site is made together with the patient during consultation, with priority given to minimising visible scarring and protecting the milk ducts.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Implants can be placed in one of two tissue layers:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Above the muscle (under the breast tissue)', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Below the muscle', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'Submuscular placement is frequently preferred as it tends to offer a more natural appearance, better long-term tissue support and a lower risk of capsular contracture. The most suitable option is determined through detailed examination and personalised planning.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why Turkey & Estecapelli', 'estecapelli' ),
						'title'         => __( 'Breast Aesthetics in Turkey and the Advantages of Estecapelli', 'estecapelli' ),
						'body'          => __( 'Turkey is one of the most preferred destinations worldwide for breast aesthetics. The main reasons include:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'star',     'label' => __( 'Highly experienced surgeons with a strong track record of successful cases', 'estecapelli' ) ),
							array( 'icon' => 'building',  'label' => __( 'Clinical infrastructure that meets international standards', 'estecapelli' ) ),
							array( 'icon' => 'tag',       'label' => __( 'High-quality care at significantly more affordable costs compared to Europe and North America', 'estecapelli' ) ),
						),
						'footer'        => __( 'At Estecapelli, these advantages are combined with a personalised approach, ensuring every patient receives the attention, expertise and results they deserve.', 'estecapelli' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Before Surgery', 'estecapelli' ),
						'title'         => __( 'Breast Augmentation Surgery: Preoperative Process', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The most critical part of the entire process is establishing a clear and mutual understanding between the surgeon and the patient. This includes an open discussion of aesthetic expectations, implant selection and the anticipated post-operative appearance. At Estecapelli, every patient undergoes a thorough preoperative consultation to ensure they feel fully informed, prepared and confident before proceeding with surgery.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'The Recovery Process', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Achieving a fully settled, natural and near-final appearance typically requires a period of 3 to 6 months. The recovery process after breast augmentation surgery is gradual, and patients are guided closely at every stage to ensure comfort, safety and the best possible outcome.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Breast Aesthetics — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the natural, balanced results our patients achieve — fuller, lifted or more proportionate, always in harmony with the body. Every result is personalised, with incisions placed as discreetly as possible.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Breast Aesthetics — FAQ', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Which breast procedure is right for me — augmentation, reduction or a lift?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It depends on your concern. Augmentation adds volume and fullness with an implant or fat transfer; reduction removes excess tissue to relieve discomfort and improve proportion; a lift (mastopexy) raises and reshapes sagging breasts without necessarily changing the size. Many patients combine a lift with augmentation. After examining you and discussing your goals, your surgeon recommends the option — or combination — that best suits your anatomy.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Should I choose silicone implants or fat transfer?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Silicone implants are the most common choice and give a predictable, lasting increase in size and projection. Fat transfer, which uses liposuction to harvest your own fat, suits patients who want a modest, very natural enhancement and have enough donor fat. Implants offer a larger volume change, while fat transfer avoids a foreign material but may partly reabsorb. Your surgeon will explain which fits your body and expectations.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Round or teardrop implants, and over or under the muscle?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Round implants tend to give more upper-pole fullness, while teardrop (anatomical) implants follow a more sloped, natural profile — both can look beautiful and natural in the right candidate. Placement under the muscle is often preferred for a more natural appearance, better long-term tissue support and a lower risk of capsular contracture. The final choice of shape, size, profile and pocket is made together with you based on your frame, existing tissue and the look you want.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What anaesthesia is used, how long is surgery, and is it painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Breast surgery is performed under general anaesthesia, so you feel nothing during the operation, which usually takes one to two hours. Afterwards you can expect tightness and moderate soreness for the first few days, well controlled with prescribed medication. A supportive surgical bra and gentle movement help you feel more comfortable as you heal.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is the recovery and stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most patients stay in Turkey for around five to seven days, including a follow-up check before flying home. Initial recovery takes about one to two weeks, when many return to desk-based work, and a supportive bra is worn for several weeks. A fully settled, near-final appearance typically develops over three to six months as swelling resolves and the breasts soften into place.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Where are the scars, and will they be visible?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The incision is placed as discreetly as possible — under the breast fold, around the areola or through the underarm for augmentation, with reduction and lift scars hidden along natural contours. Scars are most noticeable early on and then flatten and fade considerably over the following months with proper care. Your surgeon chooses the approach that minimises visible scarring and, where relevant, protects the milk ducts.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are breast implants safe, and can I still breastfeed?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Modern, CE-marked and FDA-approved implants have an excellent long-term safety record and are used worldwide. Most women can still breastfeed after augmentation, particularly when the incision and implant placement are planned to protect the milk ducts — let your surgeon know if future breastfeeding is important to you. Implants do not need routine replacement at a fixed date, but you should attend long-term follow-up as advised.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent and natural-looking?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Results are long-lasting, and with the right implant choice, sizing and technique the breasts look and feel very natural. Over the years, ageing, pregnancy, breastfeeding and weight changes can still affect breast shape, and a revision is occasionally chosen later. Maintaining a stable weight and wearing good support helps preserve your result for as long as possible.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'Breast Aesthetics Prices', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Breast aesthetics pricing — whether for augmentation, reduction or lift — varies depending on several factors. These include the type of implant selected, the scope of the procedure, any additional treatments such as a lift, asymmetry correction or combined surgeries, and the patient’s individual needs and anatomy. A personalised quote is provided following a detailed consultation, ensuring full transparency before any decisions are made.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready for a Naturally Balanced Silhouette?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised breast surgery plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

			// ============================================================
			// Abdominoplasty / Tummy Tuck (Plastic Surgery)
			// ============================================================
			array(
				'slug'     => 'abdominoplasty-tummy-tuck',
				'title'    => 'Abdominoplasty (Tummy Tuck)',
				'category' => 'Plastic Surgery',
				'sections' => array(
					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Body Contouring', 'estecapelli' ),
						'title'         => __( 'Abdominoplasty (Tummy Tuck)', 'estecapelli' ),
						'lead'          => __( 'Achieve a firmer, flatter abdomen. We deliver smooth, aesthetic results tailored to your body and goals by correcting sagging, looseness and muscle separation.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image', 'image' => '', 'video_id' => '',
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'What is Abdominoplasty?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Abdominoplasty is a surgical body-contouring procedure that corrects sagging, looseness and deformities in the abdominal area, resulting in a firmer, flatter and more aesthetic appearance. Performed by Estecapelli’s expert team, it is one of the most effective procedures for reshaping the abdomen in both women and men.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Over time, the abdominal area can loosen due to weight changes, aging, pregnancy, genetic factors or reduced skin elasticity. Tummy tuck surgery addresses these concerns directly, restoring a younger, fitter and more balanced appearance to the midsection.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Candidates', 'estecapelli' ),
						'title'         => __( 'Who Is Suitable for Abdominoplasty?', 'estecapelli' ),
						'body'          => __( 'Adult women and men who are dissatisfied with the appearance of their abdominal area and experience one or more of the following conditions may be suitable candidates for this surgery:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'target',       'label' => __( 'Sagging following significant weight gain or loss', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Loose or excess skin after pregnancy', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Separation of the abdominal muscles (diastasis recti)', 'estecapelli' ) ),
							array( 'icon' => 'dna',          'label' => __( 'Genetically loose or weak abdominal structure', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Excess skin remaining after liposuction', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Abdominal concerns that cannot be resolved through exercise alone', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Options', 'estecapelli' ),
						'title'         => __( 'Types of Tummy Tuck', 'estecapelli' ),
						'lead'          => __( 'The right technique depends on your anatomy, the degree of sagging and your goals. Swipe through the main options below.', 'estecapelli' ),
						'items'         => array(
							array(
								'icon'    => 'target',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Full Abdominoplasty', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Full abdominoplasty is preferred for individuals with advanced sagging and muscle separation throughout the abdominal area. The procedure addresses the abdomen comprehensively through the following steps:', 'estecapelli' ) . '</p>'
									. '<ul><li>' . esc_html__( 'The entire abdominal area is tightened', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'The navel is repositioned for a natural appearance', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Excess skin is removed', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'The underlying muscles are tightened and repaired', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'sparkles',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Mini Tummy Tuck', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'A mini tummy tuck is suitable for individuals with mild sagging limited to the area below the navel. Compared to a full abdominoplasty, it is a less invasive option with several notable benefits:', 'estecapelli' ) . '</p>'
									. '<ul><li>' . esc_html__( 'Smaller incision', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Faster recovery time', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'No repositioning of the navel', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'medical-plus',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Liposuction + Abdominoplasty Combination', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'This combination is the ideal approach when both excess fat and skin sagging are present in the abdominal area. Liposuction is first performed to remove unwanted fat, followed by the removal of excess skin for a smoother and more contoured result.', 'estecapelli' ) . '</p>',
							),
							array(
								'icon'    => 'hands-heart',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Mommy Makeover', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'A mommy makeover combines multiple procedures into a single surgery for women looking to restore their body after childbirth. It typically includes abdominoplasty, breast lift or augmentation, and liposuction, allowing patients to achieve a full transformation with just one recovery period.', 'estecapelli' ) . '</p>',
							),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Operation', 'estecapelli' ),
						'title'         => __( 'How Is Abdominoplasty Performed?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Abdominoplasty is performed under general anaesthesia in a sterile operating room and typically lasts between 1.5 and 4 hours. The procedure follows these steps:', 'estecapelli' ) . '</p>'
							. '<ol><li>' . esc_html__( 'Marking of sagging skin and tissues', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Tightening of the abdominal muscles', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Removal of excess skin', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Aesthetic reshaping of the belly button', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Closure of incision areas with aesthetic sutures', 'estecapelli' ) . '</li></ol>'
							. '<p>' . esc_html__( 'The incision scar is carefully placed along the bikini line and gradually fades over time.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery Process After Abdominoplasty', 'estecapelli' ),
						'lead'          => __( 'While recovery varies from person to person, the general process is as follows.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'time' => __( 'Day 1', 'estecapelli' ),       'title' => __( 'Tightness', 'estecapelli' ),         'body' => __( 'A feeling of tightness in the abdomen is normal. The patient stays one night in hospital.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'Week 1', 'estecapelli' ),      'title' => __( 'Corset & Light Activity', 'estecapelli' ), 'body' => __( 'A gradual return to daily activities begins. The corset should be worn regularly as advised.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Weeks 2–6', 'estecapelli' ),   'title' => __( 'Back to Routine', 'estecapelli' ),   'body' => __( 'Swelling continues to decrease gradually and light walks can be extended as comfort improves. Physical activity is slowly reintroduced under surgeon guidance, and most patients return to their normal daily routine within this period.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( '3 Months', 'estecapelli' ),    'title' => __( 'Settling', 'estecapelli' ),          'body' => __( 'The abdominal area begins to take on a more natural and settled appearance, and scars continue to fade and soften over time.', 'estecapelli' ) ),
							array( 'icon' => 'star',         'time' => __( '6–12 Months', 'estecapelli' ), 'title' => __( 'Final Result', 'estecapelli' ),      'body' => __( 'The final result of the surgery becomes fully apparent and the abdominal contour is at its most refined and natural state.', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Benefits', 'estecapelli' ),
						'title'         => __( 'Advantages of Tummy Tuck Surgery', 'estecapelli' ),
						'body'          => __( 'A tummy tuck offers a range of aesthetic and confidence-related benefits:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'check-circle', 'label' => __( 'A flatter and firmer abdominal appearance', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Removal of most stretch marks in the treated area', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'A slimmer, more defined waistline', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Correction of post-pregnancy changes to the abdomen', 'estecapelli' ) ),
							array( 'icon' => 'star',         'label' => __( 'Longer-lasting, natural-looking results', 'estecapelli' ) ),
							array( 'icon' => 'face',         'label' => __( 'A significant boost in self-confidence and body image', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Tummy Tuck — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the flatter, firmer and more contoured abdomen our patients achieve. Every result is tailored to the individual body, with the scar discreetly placed along the bikini line.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Tummy Tuck — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Does a tummy tuck make you lose weight?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Not really — a tummy tuck is a body-contouring operation, not a weight-loss method. It removes loose, excess skin and tightens the abdominal muscles, which can make you look noticeably slimmer and more toned, but the actual change on the scale is usually modest. It works best once you are close to a stable, healthy weight.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Which type of tummy tuck is right for me?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It depends on how much loose skin and muscle laxity you have. A mini tummy tuck suits mild sagging below the navel, a full abdominoplasty addresses the whole abdomen with muscle repair, a liposuction combination is ideal when excess fat is also present, and a mommy makeover bundles several procedures after childbirth. Your surgeon recommends the best option after examining you.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is the surgery painful, and what anaesthesia is used?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The operation is performed under general anaesthesia, so you feel nothing during surgery. Afterwards you can expect tightness and moderate discomfort for the first few days, well controlled with prescribed medication. A supportive corset and gentle movement help you feel more comfortable as you heal.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is the recovery and stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most patients stay in Turkey for around five to seven days, including a follow-up check before flying home. Initial recovery takes about two to three weeks, and a compression corset is worn for several weeks. Full recovery, including return to strenuous exercise, takes roughly six to eight weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will there be a visible scar?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes, a tummy tuck does leave a scar, but it is carefully positioned low along the bikini line so it stays hidden under underwear and swimwear. With proper care the scar flattens and fades considerably over the following months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The results are long-lasting, especially the muscle repair and removal of excess skin. Maintaining a stable weight preserves your result best — significant weight gain or a future pregnancy can stretch the area again, so a tummy tuck is ideally done once you are not planning further pregnancies.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When can I exercise again?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Gentle walking is encouraged within a few days to support circulation and healing. Light activity resumes over the first few weeks, while strenuous exercise, core workouts and heavy lifting should wait until around six to eight weeks, once your surgeon confirms you are fully healed.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'Tummy Tuck Surgery Prices in Turkey', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Turkey is a globally preferred destination for tummy tuck surgery, and for good reason:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Highly experienced surgeons with proven results', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Well-equipped hospital infrastructure', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'International quality standards', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'More affordable pricing compared to Europe and North America', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Comprehensive patient care and support services', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'Prices vary depending on the technique used, the surgeon’s experience, any additional procedures and the overall scope of the operation.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready for a Flatter, Firmer Abdomen?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised tummy tuck plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

			// ============================================================
			// Liposuction (Plastic Surgery)
			// ============================================================
			array(
				'slug'     => 'liposuction',
				'title'    => 'Liposuction',
				'category' => 'Plastic Surgery',
				'sections' => array(
					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Body Contouring', 'estecapelli' ),
						'title'         => __( 'Liposuction', 'estecapelli' ),
						'lead'          => __( 'A modern body-contouring procedure that reduces localized, stubborn fat deposits and reshapes your aesthetic contours.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image', 'image' => '', 'video_id' => '',
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'What is Liposuction?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Liposuction is a modern body-contouring procedure designed to reduce localised fat deposits and reshape aesthetic contours. During the procedure, fat cells are surgically removed, permanently decreasing their number in the treated area. Because of this, liposuction provides an effective and reliable solution with results that can last for many years.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'The liposuction techniques used at Estecapelli go beyond simple body slimming and help achieve a more proportionate, aesthetic appearance.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Treatment Areas', 'estecapelli' ),
						'title'         => __( 'Which Areas Can Be Shaped with Liposuction?', 'estecapelli' ),
						'body'          => __( 'Liposuction can be performed on various areas of the body where fat tends to accumulate and is resistant to other methods. The most common areas treated include:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'target',       'label' => __( 'Abdomen and stomach', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Waist and flanks', 'estecapelli' ) ),
							array( 'icon' => 'star',         'label' => __( 'Hips and saddlebags', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Inner and outer thighs', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Back', 'estecapelli' ) ),
							array( 'icon' => 'face',         'label' => __( 'Arms and underarms', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Chest (to treat gynecomastia in men)', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Double chin and under the jaw', 'estecapelli' ) ),
						),
						'footer'        => __( 'This procedure is not a method for weight loss. Instead, it effectively removes localised fat deposits that do not disappear despite overall weight loss.', 'estecapelli' ),
					),
					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'Techniques', 'estecapelli' ),
						'title'         => __( 'Types of Liposuction', 'estecapelli' ),
						'lead'          => __( 'At Estecapelli, different liposuction techniques are used to reach your body-contouring goals. The right method is chosen based on your fat distribution, skin elasticity and desired result. Swipe through the main options below.', 'estecapelli' ),
						'items'         => array(
							array(
								'icon'    => 'sparkles',
								'eyebrow' => __( 'Technique', 'estecapelli' ),
								'title'   => __( 'Vaser Liposuction', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Vaser Liposuction is an advanced technique that uses ultrasound energy to liquefy fat cells before removal, allowing for precise and gentle body contouring.', 'estecapelli' ) . '</p>'
									. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
									. '<ul><li>' . esc_html__( 'Targets fat precisely', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Minimally invasive with less tissue trauma', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Smooth, natural-looking results', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Faster recovery compared to traditional liposuction', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'target',
								'eyebrow' => __( 'Technique', 'estecapelli' ),
								'title'   => __( 'Laser Liposuction', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Laser Liposuction uses intense laser energy to break down fat cells, while simultaneously promoting skin tightening for smoother contours.', 'estecapelli' ) . '</p>'
									. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
									. '<ul><li>' . esc_html__( 'Melts fat precisely', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Supports skin tightening', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Minimally invasive with quick recovery', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Smooth and natural-looking results', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'medical-plus',
								'eyebrow' => __( 'Technique', 'estecapelli' ),
								'title'   => __( 'Tumescent Liposuction', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Tumescent Liposuction involves injecting a special solution into the targeted area before fat removal to minimise bleeding and bruising. Today, it is often combined with other liposuction techniques for enhanced results.', 'estecapelli' ) . '</p>',
							),
							array(
								'icon'    => 'check-circle',
								'eyebrow' => __( 'Technique', 'estecapelli' ),
								'title'   => __( 'PAL (Power Assisted Liposuction)', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'PAL (Power Assisted Liposuction) uses vibration-assisted cannulas to break down fat more easily and efficiently, making the procedure faster and smoother.', 'estecapelli' ) . '</p>'
									. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
									. '<ul><li>' . esc_html__( 'Easier and faster fat removal', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Less surgeon fatigue, more precise', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Minimally invasive with smoother results', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Can be combined with other techniques', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'hands-heart',
								'eyebrow' => __( 'Technique', 'estecapelli' ),
								'title'   => __( 'Classic Liposuction', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Traditional Liposuction is a conventional fat removal technique performed manually without devices. It remains a preferred option in certain cases.', 'estecapelli' ) . '</p>',
							),
						),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Candidates', 'estecapelli' ),
						'title'         => __( 'Who Can Have Liposuction?', 'estecapelli' ),
						'body'          => __( 'If you meet any of these criteria, you might be a candidate for liposuction:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'check-circle', 'label' => __( 'Adults who have finished their growth phase', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'People with stubborn fat areas that do not improve with diet and exercise', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Those seeking to define their body contours', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Individuals without medical conditions that prevent surgery', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'People with good skin elasticity', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Important', 'estecapelli' ),
						'title'         => __( 'When Liposuction Is Not Recommended', 'estecapelli' ),
						'body'          => '<ul><li>' . esc_html__( 'Pregnancy and breastfeeding period', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Serious chronic illnesses', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Bleeding disorders', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Severe obesity', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Before Surgery', 'estecapelli' ),
						'title'         => __( 'Preparation Before Liposuction', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The preoperative process at Estecapelli is meticulously managed to ensure safe surgical planning.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Operation', 'estecapelli' ),
						'title'         => __( 'How is Liposuction Surgery Performed?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Liposuction is performed under either local or general anaesthesia. The technique selected depends on the amount and characteristics of the fat to be removed, as well as the size of the treated area.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'General steps:', 'estecapelli' ) . '</strong></p>'
							. '<ol><li>' . esc_html__( 'Make small incisions', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Break down fat using the appropriate technology (Vaser, laser, etc.)', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Remove fat cells with cannulas', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Contour and shape the area', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Close incisions with minimal scarring', 'estecapelli' ) . '</li></ol>'
							. '<p>' . esc_html__( 'Duration: 30 minutes to 3 hours, depending on the size of the procedure.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery Process After Liposuction', 'estecapelli' ),
						'lead'          => __( 'At Estecapelli, we prioritise comfort by carefully monitoring the post-liposuction recovery process.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'time' => __( 'First Days', 'estecapelli' ),       'title' => __( 'Compression Garment', 'estecapelli' ), 'body' => __( 'Mild pain, bruising and swelling might happen. Start using a compression garment.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( '1st Week', 'estecapelli' ),         'title' => __( 'Back to Daily Life', 'estecapelli' ),  'body' => __( 'The patient may resume daily activities. Walking and light movements are encouraged.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( '2–3 Weeks', 'estecapelli' ),        'title' => __( 'Swelling Diminishes', 'estecapelli' ), 'body' => __( 'Swelling significantly diminishes. Gradually returning to sports activities is possible.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( '6 Weeks – 3 Months', 'estecapelli' ),'title' => __( 'Contours Take Shape', 'estecapelli' ),'body' => __( 'Body contours start to take shape.', 'estecapelli' ) ),
							array( 'icon' => 'star',         'time' => __( '3–6 Months', 'estecapelli' ),       'title' => __( 'Final Results', 'estecapelli' ),       'body' => __( 'Final results are revealed.', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Aftercare', 'estecapelli' ),
						'title'         => __( 'Things to Consider After Liposuction', 'estecapelli' ),
						'body'          => __( 'To support healing and protect your results, keep the following in mind:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'shield-check', 'label' => __( 'Wear the recommended compression garment regularly', 'estecapelli' ) ),
							array( 'icon' => 'x-circle',     'label' => __( 'Avoid intense sports for some time', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Stay away from hot baths, saunas or similar environments', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Drink plenty of water and keep a balanced diet', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Liposuction — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the smoother, more defined contours our patients achieve. Every result is tailored to the individual body, with fat removed precisely for a natural, proportionate shape.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Liposuction — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Is liposuction a weight-loss method?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'No — liposuction is a body-contouring procedure, not a way to lose weight. It removes stubborn, localised fat deposits that resist diet and exercise to refine your shape, but the change on the scale is usually small. It works best once you are close to a stable, healthy weight, and is not a treatment for obesity.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Which technique is right for me — Vaser, laser, PAL, tumescent or classic?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The best method depends on the area treated, the amount and firmness of the fat, and your skin elasticity. Vaser uses ultrasound for precise, gentle contouring of defined areas, laser adds some skin tightening, PAL speeds up removal of larger volumes, and tumescent technique is often combined with the others to reduce bleeding and bruising. After examining you, your surgeon recommends the single technique — or combination — that will give the smoothest, most natural result.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Which areas of the body can be treated?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Liposuction can treat most areas where stubborn fat collects — the abdomen, waist, flanks (love handles), hips, thighs, knees, arms, back, bra line and the chin or neck. Several areas can often be addressed in a single session, and the procedure is frequently combined with other operations such as a tummy tuck or BBL. Your surgeon will confirm which areas are suitable during your consultation.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What anaesthesia is used, and is the procedure painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Depending on the number and size of the areas, liposuction is performed under either local or general anaesthesia, so you are comfortable throughout. The operation typically lasts from about thirty minutes to three hours. Afterwards you can expect soreness, bruising and swelling for the first days to weeks, well managed with prescribed medication and a compression garment.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is the recovery and stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most patients stay in Turkey for a few days, including a follow-up check before flying home. Initial recovery takes around one to two weeks, when many return to desk-based work, and a compression garment is worn for several weeks to support healing and smooth the contour. Final results emerge gradually as swelling settles, typically becoming clear over three to six months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent and natural-looking?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The fat cells removed during liposuction are gone permanently, so the treated areas stay slimmer long term. Significant future weight gain can, however, enlarge the remaining fat cells elsewhere, so a stable weight preserves your result best. Performed with the right technique by an experienced surgeon, the contour looks smooth, balanced and natural.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When can I exercise again?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Gentle walking is encouraged within a few days to support circulation and healing. Light activity resumes over the first couple of weeks, while strenuous exercise, heavy lifting and high-impact workouts should wait until around four to six weeks, once your surgeon confirms you are fully healed.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'Liposuction Prices', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Liposuction pricing depends on several factors:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Amount of fat to be removed', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Number of areas to be treated', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Technique used (Vaser, laser, classic, etc.)', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Duration of the surgery', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Patient’s expectations', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'The most accurate price is determined after an online consultation with Estecapelli specialists.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready to Define Your Contours?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised liposuction plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

			// ============================================================
			// Face & Neck Lift Surgery (Plastic Surgery)
			// ============================================================
			array(
				'slug'     => 'face-and-neck-lift-surgery',
				'title'    => 'Face & Neck Lift Surgery',
				'category' => 'Plastic Surgery',
				'sections' => array(
					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Facial Rejuvenation', 'estecapelli' ),
						'title'         => __( 'Face & Neck Lift Surgery', 'estecapelli' ),
						'lead'          => __( 'As we age, facial tissues sag and the jawline and neck lose definition. A face and neck lift restores a firmer, more youthful and natural contour.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image', 'image' => '', 'video_id' => '',
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Facial Rejuvenation', 'estecapelli' ),
						'title'         => __( 'Face and Neck Lift Surgery', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'As we age, facial tissues sag downward due to gravity, causing the cheeks and jawline to lose definition and the neck to loosen, leading to a double chin. Face and neck lift surgery reverses these changes, reshaping the facial contour.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Face and neck lift surgery is one of the most effective rejuvenation methods that addresses facial sagging caused by aging, weight loss or reduced skin elasticity. Thanks to modern facial rejuvenation techniques performed by Estecapelli’s expert surgeons, you can confidently achieve a firmer, younger and natural-looking face.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Face Lift', 'estecapelli' ),
						'title'         => __( 'What is a Face Lift Surgery?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A facelift is a surgical procedure that lifts sagging facial tissues, removes excess skin and provides a younger appearance. During the surgery:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'The cheeks and jawline are tightened', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The lower part of the face is firmed', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Accumulated skin on the neck and under the chin is removed', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The skin is repositioned according to the facial anatomy', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'Since the incisions are usually made around the ears and along the hairline, the scars are barely noticeable after healing.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Neck Lift', 'estecapelli' ),
						'title'         => __( 'What is a Neck Lift Surgery?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A neck lift is a rejuvenation procedure that corrects sagging, banding, a double chin and excess skin in the neck. It is often performed in conjunction with facelift surgery because the facial skin that loosens over time tends to accumulate in the neck. With a neck lift:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'The double chin is tightened', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The neckline becomes more defined', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'A longer and younger-looking neck is achieved', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Candidates', 'estecapelli' ),
						'title'         => __( 'Who is Suitable for Face and Neck Lift Surgery?', 'estecapelli' ),
						'body'          => __( 'You may be a suitable candidate for face and neck lift surgery if one or more of the following conditions apply to you:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'face',         'label' => __( 'Noticeable sagging in the cheeks and jawline', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Accumulation of skin in the double chin area', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Neck wrinkles and looseness', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Prominent aging lines around the mouth', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Decreased skin elasticity', 'estecapelli' ) ),
							array( 'icon' => 'dna',          'label' => __( 'Genetically early-aging facial structure', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Options', 'estecapelli' ),
						'title'         => __( 'Types of Face & Neck Lift', 'estecapelli' ),
						'lead'          => __( 'At Estecapelli, different facelift and neck lift techniques are tailored to each person’s anatomy, expectations and needs. Swipe through the options below.', 'estecapelli' ),
						'items'         => array(
							array(
								'icon'    => 'sparkles',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Mini Face Lift', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'A Mini Facelift is a minimally invasive procedure designed for younger patients with mild to moderate facial sagging. It focuses on tightening the lower face and jawline, providing a more refreshed and youthful appearance with smaller incisions and a quicker recovery compared to a traditional facelift.', 'estecapelli' ) . '</p>'
									. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
									. '<ul><li>' . esc_html__( 'Minimally invasive procedure', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Smaller incisions and minimal scarring', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Faster recovery time', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Natural-looking results', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Improves jawline and mild sagging', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'face',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Full Face Lift', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'A Full Facelift is a comprehensive facial rejuvenation procedure designed for patients with moderate to advanced signs of aging. It targets deeper facial sagging by tightening the skin and underlying tissues of the mid-face, jawline and neck, creating a smoother and more youthful appearance.', 'estecapelli' ) . '</p>'
									. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
									. '<ul><li>' . esc_html__( 'Treats moderate to severe facial sagging', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Improves jawline and neck contours', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Long-lasting results', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Comprehensive facial rejuvenation', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Natural and balanced appearance', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'dna',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Deep Plane Face Lift', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'A Deep Plane Facelift is an advanced facelift technique designed to treat moderate to severe facial aging. This method lifts the deeper facial tissues rather than only tightening the skin, allowing for more natural and longer-lasting rejuvenation, especially in the mid-face, nasolabial folds, jawline and neck.', 'estecapelli' ) . '</p>'
									. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
									. '<ul><li>' . esc_html__( 'Lifts deeper facial tissues for natural results', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Improves mid-face volume and nasolabial folds', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Enhances jawline and neck contour', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Long-lasting rejuvenation', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'More natural facial movement and expression', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'target',
								'eyebrow' => __( 'Type', 'estecapelli' ),
								'title'   => __( 'Neck Lift', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'A Neck Lift is a procedure designed to improve loose skin, excess fat and muscle banding in the neck area. It helps create a firmer, smoother and more defined neck and jawline, restoring a more youthful profile.', 'estecapelli' ) . '</p>'
									. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
									. '<ul><li>' . esc_html__( 'Tightens loose neck skin', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Improves jawline definition', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Reduces neck bands and excess fat', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Creates a smoother, youthful neck contour', 'estecapelli' ) . '</li></ul>',
							),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Operation', 'estecapelli' ),
						'title'         => __( 'How is a Face and Neck Lift Surgery Performed?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The operation is performed under general anaesthesia in a fully equipped hospital environment. It usually takes 2–4 hours. Procedure steps:', 'estecapelli' ) . '</p>'
							. '<ol><li>' . esc_html__( 'Minimal incisions are made around the ears and along the hairline', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Muscles and tissues under the skin are lifted upwards', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Excess skin is removed', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Tissues are shaped according to the natural facial anatomy', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Incisions are closed with aesthetic sutures', 'estecapelli' ) . '</li></ol>'
							. '<p>' . esc_html__( 'With properly performed facelift surgery, facial expressions do not change; only a younger, more dynamic appearance is achieved.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery Process After Face and Neck Lift Surgery', 'estecapelli' ),
						'lead'          => __( 'The recovery process varies from person to person, but the general process is as follows.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'time' => __( 'First Days', 'estecapelli' ), 'title' => __( 'Swelling & Bruising', 'estecapelli' ), 'body' => __( 'Mild pain, swelling and bruising are normal. Cold compresses are applied. You may need to stay in hospital for one night.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'Week 1', 'estecapelli' ),     'title' => __( 'Back to Daily Life', 'estecapelli' ), 'body' => __( 'Swelling significantly decreases and return to daily activities is possible.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( '2–3 Weeks', 'estecapelli' ),  'title' => __( 'Bruising Fades', 'estecapelli' ),     'body' => __( 'Swelling and bruising continue to fade and most normal activities can be resumed.', 'estecapelli' ) ),
							array( 'icon' => 'star',         'time' => __( '1–3 Months', 'estecapelli' ), 'title' => __( 'Final Contour', 'estecapelli' ),      'body' => __( 'Final results become visible and facial contours are fully defined.', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Aftercare', 'estecapelli' ),
						'title'         => __( 'Things to Consider', 'estecapelli' ),
						'body'          => __( 'To support healing and protect your results, keep the following in mind:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'shield-check', 'label' => __( 'Protect your face from the sun', 'estecapelli' ) ),
							array( 'icon' => 'x-circle',     'label' => __( 'Avoid smoking and alcohol', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Limit intense exercise and bending forward', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Do not pick at scabs', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Use skin care products recommended by your doctor', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Face & Neck Lift — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the natural rejuvenation our patients achieve — a firmer jawline, smoother neck and refreshed, younger-looking face. Every result is planned individually.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Face & Neck Lift — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'What is a face and neck lift?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is a surgical rejuvenation procedure that lifts sagging facial and neck tissues, removes excess skin and redefines the jawline and neckline. The result is a firmer, smoother and more youthful appearance — without changing your natural expressions or identity.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What is the difference between a mini, full and deep plane facelift?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A mini facelift treats mild sagging through smaller incisions with a faster recovery, ideal for younger patients. A full facelift addresses moderate to advanced ageing across the mid-face, jawline and neck. A deep plane facelift lifts the deeper tissue layers rather than just the skin, giving the most natural and longest-lasting result. Your surgeon recommends the right option after assessing your face.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can a facelift and neck lift be done together?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes, and they very often are. Because the face and neck age as one aesthetic unit and loosened facial skin tends to gather in the neck, combining the two delivers a more balanced, harmonious result in a single recovery period.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Am I a good candidate, and is there an age limit?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Good candidates are generally healthy non-smokers (or those willing to stop around surgery) who have visible sagging of the cheeks, jawline or neck and realistic expectations. There is no strict age limit — skin quality and overall health matter more than age. A consultation confirms the best approach for you.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is the surgery painful, and what anaesthesia is used?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The operation is performed under general anaesthesia, so you feel nothing during surgery. Afterwards most patients report tightness and mild discomfort rather than significant pain, which is easily controlled with prescribed medication.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is the recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most patients return to daily life within ten to fourteen days, once the main swelling and bruising have settled. Tissues continue to refine over the following weeks, with the final contour usually visible between one and three months after surgery.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will there be visible scars?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Incisions are carefully placed around the ears and within the hairline so they are well hidden. As they heal they fade and become difficult to notice, especially once the hair and natural skin creases conceal them.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A face and neck lift produces long-lasting results that typically last many years. The natural ageing process continues gradually afterwards, but your face will always look younger than it would have without the procedure. A healthy lifestyle and good skin care help preserve the result.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'Face and Neck Lift Surgery Prices in Turkey', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Türkiye is a popular destination for medical tourism worldwide, especially for face and neck lift surgeries. The operation costs depend on:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'The surgeon’s experience', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The technique used', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The extent of the face and neck lift', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The clinic’s capabilities and equipment', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'Costs may vary accordingly. A personalised quote is provided following a detailed consultation.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready to Refresh Your Look?', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised face & neck lift plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

			// ============================================================
			// Obesity Surgeries — Bariatric & Gastric Balloon (Plastic Surgery)
			// ============================================================
			array(
				'slug'     => 'obesity-surgeries-bariatric-surgery-and-gastric-balloon',
				'title'    => 'Obesity Surgeries (Bariatric)',
				'category' => 'Plastic Surgery',
				'sections' => array(
					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Weight Loss', 'estecapelli' ),
						'title'         => __( 'Obesity Surgeries — Bariatric & Gastric Balloon', 'estecapelli' ),
						'lead'          => __( 'Permanent and healthy weight loss with Estecapelli. Surgical and non-surgical options tailored to your health profile and goals.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image', 'image' => '', 'video_id' => '',
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Overview', 'estecapelli' ),
						'title'         => __( 'Permanent and Healthy Weight Loss with Estecapelli', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Obesity has become one of the fastest-growing health problems worldwide. Defined as having a body mass index over 30, obesity affects not only physical appearance but also leads to serious conditions such as cardiovascular diseases, diabetes, sleep apnea, hypertension and joint disorders.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Estecapelli provides professional solutions for sustainable weight loss and a healthier life through internationally standardised bariatric surgery methods and endoscopic gastric balloon treatments to combat obesity.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'What is Bariatric Surgery?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Bariatric surgery is a general term for surgical procedures used to treat severe obesity, aiming to promote weight loss and reduce obesity-related health risks. These surgeries work by altering how the digestive system functions:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'To achieve satiety with less food', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'To reduce nutrient absorption', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'To modify metabolism', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'Bariatric surgery is one of the most effective methods for achieving lasting weight loss in appropriate patients.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Candidates', 'estecapelli' ),
						'title'         => __( 'Who Can Undergo Bariatric Surgery?', 'estecapelli' ),
						'body'          => __( 'Individuals who meet one or more of the following criteria may be suitable candidates for bariatric surgery:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'target',       'label' => __( 'Body mass index (BMI) of 40 or higher', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'BMI between 35–40 with conditions such as diabetes, hypertension or sleep apnea', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Those who cannot achieve results despite weight loss efforts', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Individuals experiencing movement limitations due to obesity', 'estecapelli' ) ),
							array( 'icon' => 'star',         'label' => __( 'Individuals whose quality of life is reduced because of excess weight', 'estecapelli' ) ),
						),
						'footer'        => __( 'At Estecapelli, each patient is evaluated through a multidisciplinary approach, and the most appropriate treatment plan is created in consultation with internal medicine, endocrinology and cardiology.', 'estecapelli' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Before Surgery', 'estecapelli' ),
						'title'         => __( 'Preoperative Requirements for Bariatric Surgery', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The preoperative process is essential for the success of the surgery. At Estecapelli, pre-surgery preparation involves the following steps:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Detailed physical examination', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Blood tests, ECG and stress test if needed', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Endoscopic evaluation', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Review of medical history and medication use', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Abstaining from alcohol and smoking for at least 6 months', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Nutritional and psychological assessments', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'After all results are reviewed, a customised surgical plan is developed for the patient.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Methods', 'estecapelli' ),
						'title'         => __( 'Bariatric Surgery Methods', 'estecapelli' ),
						'body'          => __( 'The most common methods include:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'medical-plus', 'label' => __( 'Gastric Sleeve (Sleeve Gastrectomy)', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Gastric Bypass', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Gastric Balloon (Endoscopic Procedure – Non-Surgical)', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Gastric Band', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Post-Bariatric Surgery Care and Recovery Guidelines', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'After a successful operation, permanent weight loss is possible with consistent lifestyle habits. Estecapelli’s expert team provides patients with a detailed roadmap during the post-operative journey:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Follow the nutrition plan carefully', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Consume liquids during the first few weeks', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Gradually switch to a protein-rich diet', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Avoid sugary and processed foods', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Take vitamin and mineral supplements regularly', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Begin with light exercises and gradually establish a consistent workout routine', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Drink plenty of water', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Stay away from alcohol and caffeine during the initial months', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'The post-bariatric surgery process requires both physical and psychological support. Estecapelli offers post-operative follow-up and counselling to all patients.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Non-Surgical Option', 'estecapelli' ),
						'title'         => __( 'What is a Gastric Balloon?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The gastric balloon is an inflatable medical device inserted into the stomach through endoscopy, eliminating the need for surgery. By reducing the stomach’s capacity, the balloon helps individuals feel full with less food, promoting weight loss.', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'The procedure takes 20–30 minutes', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Same-day discharge is possible', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Options for 6- or 12-month balloons are available', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'The gastric balloon is an effective weight management method for patients who prefer to avoid surgery or are not suitable candidates for surgical procedures.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Eligibility', 'estecapelli' ),
						'title'         => __( 'Who is Eligible for a Gastric Balloon?', 'estecapelli' ),
						'body'          => __( 'A gastric balloon can be recommended for people with the following conditions:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'check-circle', 'label' => __( 'Individuals with a BMI of 30–35 who prefer to avoid surgery', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Those needing a supportive first step in weight loss', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'People with health issues that make surgery unsuitable', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Individuals who cannot lose weight after childbirth and have a moderate amount of excess weight', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Important', 'estecapelli' ),
						'title'         => __( 'When a Gastric Balloon Should Not Be Used', 'estecapelli' ),
						'body'          => '<ul><li>' . esc_html__( 'Gastric ulcer, gastritis and hiatal hernia', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Substance abuse', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Severe eating disorders', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Pregnancy and breastfeeding period', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Why Turkey', 'estecapelli' ),
						'title'         => __( 'Obesity Surgery in Turkey', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Turkey is among the most popular countries worldwide for bariatric surgery. The main reasons include:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Surgeons with international experience', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Technologically advanced hospitals', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'High success rates', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'More affordable prices compared to Europe and the USA', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'Estecapelli offers the following services to patients coming from both Turkey and abroad:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Personalised treatment plans', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Comfortable hospital and hotel options', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Post-operative follow-up programs', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Weight Loss — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the life-changing transformations our patients achieve with bariatric surgery and gastric balloon treatment. Every journey is supported by our team from start to finish.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Bariatric Surgery — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Who is bariatric surgery suitable for?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is generally suitable for adults with a body mass index (BMI) of 40 or above, or a BMI of 35+ when obesity-related conditions such as type 2 diabetes, hypertension or sleep apnea are present. Candidates who have struggled to lose weight through diet and exercise alone are assessed individually through our multidisciplinary team before any decision is made.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What procedures do you offer?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'We offer the full range of options: gastric sleeve (sleeve gastrectomy), gastric bypass and gastric band surgery, as well as the non-surgical endoscopic gastric balloon. The right choice depends on your weight, health profile and goals, which we confirm during your assessment.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What is the difference between gastric sleeve, bypass and balloon?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A gastric sleeve permanently removes part of the stomach so you feel full sooner. A gastric bypass also reroutes part of the intestine to reduce calorie absorption, often giving greater results for higher BMIs. A gastric balloon is a temporary, non-surgical device placed for 6 to 12 months — ideal as a kick-start or for those who prefer to avoid surgery.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How much weight will I lose?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'With surgery, patients typically lose around 60 to 80 percent of their excess weight within the first 12 to 18 months. Gastric balloon results are more modest. Long-term success in every case depends on following the recommended nutrition and lifestyle plan after the procedure.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is bariatric surgery safe, and will there be large scars?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Performed laparoscopically (keyhole) by experienced surgeons in fully equipped hospitals, modern bariatric surgery is both safe and effective. Because only a few small incisions are used, scarring is minimal and recovery is faster than with open surgery.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery and how long should I stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most patients stay in Turkey for around five to seven days, including hospital time and a follow-up check before flying home. Return to daily life usually takes one to two weeks, with full recovery in around four to six weeks. The gastric balloon requires only a short stay.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does it improve other health conditions?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Significant weight loss often brings marked improvement — and sometimes resolution — of conditions such as type 2 diabetes, high blood pressure, sleep apnea, joint pain and fatty liver disease, alongside a major boost to quality of life.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will I have to change my lifestyle permanently?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes — lasting results depend on lasting habits. After your procedure you will follow a staged nutrition plan, take recommended vitamins and gradually build a regular exercise routine. Our team provides post-operative follow-up and counselling to help you maintain your results for life.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'Obesity Surgery Prices', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Prices vary based on factors such as the chosen method, the patient’s health, the preferred balloon type and the surgical technique. The most accurate prices are determined after an assessment with Estecapelli specialists.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Start Your Weight-Loss Journey', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised weight-loss plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free assessment of the right option for you', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

			// ============================================================
			// Gynecomastia (Plastic Surgery)
			// ============================================================
			array(
				'slug'     => 'gynecomastia',
				'title'    => 'Gynecomastia',
				'category' => 'Plastic Surgery',
				'sections' => array(
					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Male Chest Aesthetics', 'estecapelli' ),
						'title'         => __( 'Gynecomastia', 'estecapelli' ),
						'lead'          => __( 'A surgical solution for enlarged male breast tissue — restoring a flatter, firmer and more masculine chest contour with natural-looking results.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image', 'image' => '', 'video_id' => '',
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Condition', 'estecapelli' ),
						'title'         => __( 'What Is Gynecomastia?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Gynecomastia is a condition in which male breast tissue grows beyond its normal size, causing the chest to take on a feminine appearance. While men naturally have breast tissue and mammary glands, testosterone typically keeps this tissue small and inactive. When hormonal imbalances, weight gain, certain medications or underlying health conditions disrupt this balance, the tissue enlarges and gynecomastia develops.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'This condition goes beyond aesthetics — it can significantly impact a man’s psychological well-being, self-confidence and social life. At Estecapelli, gynecomastia is approached from both a medical and aesthetic standpoint, ensuring that each patient receives a personalised and permanent solution.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Causes', 'estecapelli' ),
						'title'         => __( 'What Causes Gynecomastia?', 'estecapelli' ),
						'body'          => __( 'Gynecomastia can develop through a variety of mechanisms. The most common causes include:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'atom',         'label' => __( 'Imbalance between estrogen (female hormone) and testosterone (male hormone)', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Fat accumulation in the chest area due to excessive weight gain', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Tissue and skin sagging caused by repeated cycles of weight gain and loss', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Hormonal side effects of certain medications', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Liver, kidney and endocrine system diseases', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'label' => __( 'Age-related hormonal changes', 'estecapelli' ) ),
							array( 'icon' => 'x-circle',     'label' => __( 'Use of anabolic steroids for bodybuilding purposes', 'estecapelli' ) ),
							array( 'icon' => 'dna',          'label' => __( 'A significant decrease in testosterone levels', 'estecapelli' ) ),
						),
						'footer'        => __( 'In many cases, a combination of these factors contributes to the condition, making a thorough medical evaluation essential before determining the appropriate treatment approach.', 'estecapelli' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Treatment', 'estecapelli' ),
						'title'         => __( 'Gynecomastia Treatment: Non-Surgical and Surgical Approaches', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The most suitable treatment method is determined based on the type and degree of breast enlargement, as well as the underlying cause. A thorough evaluation ensures that each patient receives a personalised approach for safe and effective results.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'In mild cases', 'estecapelli' ) . '</strong> ' . esc_html__( 'with minimal fat accumulation and skin sagging: removal of fatty tissue through liposuction or its variants — such as Vaser liposuction — may be sufficient to achieve the desired contour.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'In advanced cases', 'estecapelli' ) . '</strong> ' . esc_html__( 'with significant breast tissue enlargement and skin sagging: surgical removal of glandular tissue combined with excess skin excision is required for a flat and natural-looking result.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'At Estecapelli, each patient is evaluated individually. A detailed analysis is performed to determine whether the excess tissue consists of fatty tissue, firm glandular breast tissue or a combination of both — ensuring the most appropriate and effective treatment plan is selected.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Operation', 'estecapelli' ),
						'title'         => __( 'How Is Gynecomastia Surgery Performed?', 'estecapelli' ),
						'lead'          => __( 'Gynecomastia surgery is performed under general anaesthesia in a fully equipped operating room. The procedure is tailored to each patient’s specific condition, and its content varies depending on the type and degree of gynecomastia present.', 'estecapelli' ),
						'items'         => array(
							array(
								'icon'    => 'target',
								'eyebrow' => __( 'Case Type', 'estecapelli' ),
								'title'   => __( 'Cases With Excess Fatty Tissue Only', 'estecapelli' ),
								'body'    => '<ul><li>' . esc_html__( 'Liposuction or Vaser Liposuction is the preferred technique', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Fatty tissue is removed using cannulas inserted through very small incisions in the chest area', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'When skin elasticity is sufficient, a flat and natural chest contour is achieved without the need for additional skin incisions', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'medical-plus',
								'eyebrow' => __( 'Case Type', 'estecapelli' ),
								'title'   => __( 'Cases With Both Breast Tissue Enlargement and Skin Sagging', 'estecapelli' ),
								'body'    => '<ul><li>' . esc_html__( 'Glandular breast tissue is surgically removed', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Excess skin is excised and the chest is reshaped for a firmer, flatter contour', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Inverted T or circular incisions are used when necessary', 'estecapelli' ) . '</li></ul>'
									. '<p>' . esc_html__( 'The average duration of the surgery is approximately 1.5–2 hours.', 'estecapelli' ) . '</p>'
									. '<p>' . esc_html__( 'Liposuction-based procedures leave minimal scarring. In surgeries involving tissue and skin removal, incisions are strategically placed in the most discreet areas possible and fade significantly over time with proper care.', 'estecapelli' ) . '</p>',
							),
						),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery Process After Gynecomastia Surgery', 'estecapelli' ),
						'lead'          => __( 'The postoperative period is generally comfortable and well-tolerated. Mild pain and a feeling of tightness on the first day are completely normal and expected.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'time' => __( 'First 24 Hours', 'estecapelli' ),   'title' => __( 'Hospital Stay', 'estecapelli' ),       'body' => __( 'The patient typically stays in hospital for one night, with pain well-managed through prescribed medication.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'First Week', 'estecapelli' ),       'title' => __( 'Compression Garment', 'estecapelli' ),  'body' => __( 'Swelling, bruising and sensitivity in the chest area are expected. Wearing a compression garment during this period is essential for proper healing and shaping.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Weeks 2–3', 'estecapelli' ),        'title' => __( 'Edema Subsides', 'estecapelli' ),       'body' => __( 'Edema largely subsides and movement becomes more comfortable. Light daily activities can gradually be resumed.', 'estecapelli' ) ),
							array( 'icon' => 'star',         'time' => __( '1 Month and Beyond', 'estecapelli' ),'title' => __( 'Defined Chest', 'estecapelli' ),       'body' => __( 'The chest begins to take on a more natural and defined appearance. Returning to sports, weightlifting and chest-intensive exercises requires doctor approval to ensure a safe and full recovery.', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Gynecomastia — Before & After', 'estecapelli' ),
						'lead'          => __( 'See the flatter, firmer and more masculine chest contour our patients achieve. Every result is planned individually for a natural look and discreet scarring.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Gynecomastia — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'What is gynecomastia?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Gynecomastia is the enlargement of breast tissue in men, giving the chest a fuller, more feminine appearance. It can be caused by a hormonal imbalance between estrogen and testosterone, weight gain, certain medications, anabolic steroid use or underlying health conditions. It is very common and, while harmless medically, can affect confidence — which is why many men choose treatment.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How is gynecomastia treated?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Treatment depends on whether the excess is fatty tissue, firm glandular tissue or a combination. Milder cases are corrected with liposuction (often Vaser), while cases with significant glandular tissue or loose skin require surgical removal of the gland and, where needed, skin tightening. Your surgeon confirms the right approach after examining you.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is the surgery painful, and what anaesthesia is used?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The procedure is performed under general anaesthesia, so you feel nothing during surgery. Afterwards most patients describe mild soreness and tightness rather than significant pain, which is well controlled with prescribed medication and eases within a few days.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is the recovery and stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most patients stay in Turkey for around three to five days, including a follow-up check. You can return to daily life within one to two weeks, while a compression garment is worn for several weeks to support shaping. Strenuous exercise and chest workouts are resumed after about a month, with your surgeon’s approval.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will there be visible scars?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Liposuction-only procedures leave tiny, barely noticeable marks. When glandular tissue or skin is removed, incisions are placed as discreetly as possible — often around the edge of the areola — and fade significantly over time with proper care.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent, and can it come back?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The removed glandular tissue does not grow back, so results are long-lasting. Recurrence is uncommon, but significant weight gain, steroid use or a new hormonal imbalance can cause changes — maintaining a stable weight and healthy lifestyle keeps your results looking their best.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does the chest look natural afterwards?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Performed with the right technique, gynecomastia surgery creates a flat, firm and naturally masculine chest contour. The aim is always a result that looks like it was never operated on, in harmony with your body.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'Gynecomastia Surgery Prices', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Gynecomastia surgery prices may vary depending on several factors, including:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'The technique used (liposuction only, surgical gland removal, or a combined approach)', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The severity or degree of gynecomastia', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The patient’s individual needs and whether additional procedures are required', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The overall surgical plan and treatment approach', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'For this reason, the most accurate price can only be determined after an in-person examination or an online pre-consultation with Estecapelli specialists.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Restore Your Confidence', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised gynecomastia treatment plan and a no-obligation, all-inclusive quote — usually within a few hours. Your enquiry is completely confidential.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free, discreet assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

			// ============================================================
			// Dental Implant (Dental Treatment)
			// ============================================================
			array(
				'slug'     => 'dental-implant',
				'title'    => 'Dental Implant',
				'category' => 'Dental Treatment',
				'sections' => array(
					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Permanent Tooth Replacement', 'estecapelli' ),
						'title'         => __( 'Dental Implant', 'estecapelli' ),
						'lead'          => __( 'Restore missing teeth with permanent, natural-looking solutions. Biocompatible titanium roots deliver strong, aesthetic and long-lasting results.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image', 'image' => '', 'video_id' => '',
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Treatment', 'estecapelli' ),
						'title'         => __( 'What is a Dental Implant?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A dental implant is a modern treatment method that restores natural tooth function and aesthetic appearance by placing biocompatible titanium roots into the jawbone to replace missing teeth. Implants take over the role of the lost tooth root and, together with the prosthetic tooth placed on top, offer the closest solution to a natural tooth in terms of both appearance and function.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why It Matters', 'estecapelli' ),
						'title'         => __( 'Why Choose Dental Implants?', 'estecapelli' ),
						'body'          => __( 'Tooth loss is more than just an aesthetic concern. Impaired chewing function can negatively impact the digestive system, jawbone resorption may develop over time, and neighbouring teeth can gradually shift and deteriorate. Dental implants are the most ideal treatment solution for the following reasons:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'check-circle', 'label' => __( 'Permanently fills the gap left by missing teeth', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Protects the jawbone and prevents bone loss', 'estecapelli' ) ),
							array( 'icon' => 'tooth',        'label' => __( 'Preserves surrounding healthy teeth without causing damage', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Restores chewing strength to a level closest to natural teeth', 'estecapelli' ) ),
							array( 'icon' => 'face',         'label' => __( 'Enhances facial aesthetics and overall appearance', 'estecapelli' ) ),
							array( 'icon' => 'star',         'label' => __( 'Offers a lifelong, durable solution', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'How is a Dental Implant Applied?', 'estecapelli' ),
						'lead'          => __( 'Implant treatment follows a structured process designed to deliver safe, long-lasting and natural-looking results. The main stages of the treatment are as follows.', 'estecapelli' ),
						'items'         => array(
							array(
								'icon'    => 'target',
								'eyebrow' => __( 'Step 1', 'estecapelli' ),
								'title'   => __( 'Clinical Examination & Imaging', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'The jawbone is thoroughly analysed using panoramic X-rays and digital tomography. Bone density, implant size and the treatment area are carefully evaluated to create a personalised implant plan, and the patient’s overall oral health and medical history are reviewed.', 'estecapelli' ) . '</p>',
							),
							array(
								'icon'    => 'clipboard',
								'eyebrow' => __( 'Step 2', 'estecapelli' ),
								'title'   => __( 'Treatment Planning', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'A personalised treatment plan is prepared based on the number of missing teeth, jaw structure and overall oral health. If preliminary procedures such as bone grafting or tooth extraction are required, these are incorporated into the plan.', 'estecapelli' ) . '</p>',
							),
							array(
								'icon'    => 'medical-plus',
								'eyebrow' => __( 'Step 3', 'estecapelli' ),
								'title'   => __( 'Implant Placement', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Under local anaesthesia, the titanium implant root is carefully placed into the jawbone, typically taking 15 to 30 minutes per implant. Mild soreness or swelling in the following days is a normal part of healing.', 'estecapelli' ) . '</p>',
							),
							array(
								'icon'    => 'shield-check',
								'eyebrow' => __( 'Step 4', 'estecapelli' ),
								'title'   => __( 'Osseointegration', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'A healing period of approximately 2 to 3 months allows the titanium root to fully integrate with the surrounding bone, creating a strong and stable foundation for the prosthetic tooth.', 'estecapelli' ) . '</p>',
							),
							array(
								'icon'    => 'check-circle',
								'eyebrow' => __( 'Step 5', 'estecapelli' ),
								'title'   => __( 'Prosthetic Structure', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Once osseointegration is complete, a custom-made prosthetic tooth is attached. Porcelain, zirconium or hybrid prostheses may be used, designed to match the colour, shape and size of the surrounding natural teeth.', 'estecapelli' ) . '</p>',
							),
						),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Benefits', 'estecapelli' ),
						'title'         => __( 'Advantages of Dental Implants', 'estecapelli' ),
						'body'          => __( 'Implants are the gold standard for replacing missing teeth:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'tooth',        'label' => __( 'Provides a natural tooth appearance and feel', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Ensures maximum chewing performance', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No procedure is performed on adjacent healthy teeth', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Preserves oral health in the long term', 'estecapelli' ) ),
							array( 'icon' => 'face',         'label' => __( 'Supports facial aesthetics and prevents the risk of facial collapse', 'estecapelli' ) ),
							array( 'icon' => 'star',         'label' => __( 'Offers lifelong use with proper care and maintenance', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Why Turkey', 'estecapelli' ),
						'title'         => __( 'Why Are Dental Implants Preferred in Turkey?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Turkey has become one of the most preferred destinations worldwide for implant treatment, thanks to its internationally accredited clinics, experienced surgeons and cost-effective treatment options. Our Istanbul clinic offers patients travelling for implant treatment a complete health tourism experience, including VIP transfers, accommodation support and a comprehensive personalised treatment plan.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => __( 'Plan Your Visit', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),
					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Dental Implants — Before & After', 'estecapelli' ),
						'lead'          => __( 'See how a completed smile is restored with implant treatment. Every case is planned individually for a natural look, comfortable bite and lasting result.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Dental Implants — FAQ', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Who is a good candidate for a dental implant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most adults in good general health who have lost one or more teeth and have enough healthy jawbone are suitable candidates. Conditions such as uncontrolled diabetes, advanced gum disease or significant bone loss don’t necessarily rule out treatment, but they are assessed and managed first. A short examination with X-rays or a 3D scan lets our dentists confirm your suitability and outline the right plan.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does the whole treatment take?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'From start to finish, implant treatment usually spans two to four months. The implant itself is placed in a short appointment, but it then needs roughly two to three months to fuse with the bone (osseointegration) before the final tooth is fitted. In selected cases with good bone quality, same-day or immediate-load implants can shorten this considerably.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is the procedure painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The placement is carried out under local anaesthesia, so you won’t feel pain during the procedure itself. Afterwards it is normal to have mild swelling or soreness for a few days, which is easily managed with simple painkillers and aftercare. Most patients compare the recovery to a routine tooth extraction.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What if I don’t have enough jawbone?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Insufficient bone is common after long-standing tooth loss and is rarely a barrier. Procedures such as bone grafting or a sinus lift can rebuild the bone volume needed to support an implant securely. If this is required, it is planned in advance and may add some healing time before the implant is placed.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can implants replace several or all of my teeth?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. A single implant can replace one tooth, while a few implants can support a bridge for several missing teeth. For a full arch, treatments such as All-on-4 or All-on-6 use just four to six implants to anchor a complete fixed set of teeth — a stable, comfortable alternative to removable dentures.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Do implants look and feel natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'They do. The titanium root sits hidden in the bone, and the custom prosthetic tooth on top is matched to the colour, shape and size of your natural teeth. Because the implant is firmly anchored, it lets you bite, chew and speak just like a real tooth — without the movement associated with dentures.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long do dental implants last?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The implant root is designed to be a lifelong solution and, with good care, can last for decades. The visible crown on top may eventually show wear after many years of use and can be renewed without replacing the implant. Daily hygiene, healthy gums and regular check-ups are the keys to long-term success.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does smoking or age affect implant treatment?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'There is no upper age limit — what matters is bone and gum health rather than age. Smoking, however, slows healing and raises the risk of implant failure, so cutting down or stopping around the time of treatment significantly improves the outcome. Your dentist will give tailored advice during your assessment.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How do I care for my implant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Caring for an implant is just like caring for natural teeth: brush twice a day, clean between the teeth with floss or an interdental brush, and attend routine dental check-ups. Good daily hygiene keeps the surrounding gum healthy and protects the long-term stability of the implant.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Get Your Implant Treatment Plan', 'estecapelli' ),
						'lead'          => __( 'Share a few details and our team will reply with a personalised dental implant plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free treatment assessment from your photos or X-ray', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

			// ============================================================
			// Hollywood Smile (Dental Treatment)
			// ============================================================
			array(
				'slug'     => 'hollywood-smile',
				'title'    => 'Hollywood Smile',
				'category' => 'Dental Treatment',
				'sections' => array(
					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'Smile Design', 'estecapelli' ),
						'title'         => __( 'Hollywood Smile', 'estecapelli' ),
						'lead'          => __( 'A brighter, more symmetrical and truly striking smile — designed to transform not just your teeth, but the harmony of teeth, gums and lips.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image', 'image' => '', 'video_id' => '',
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Treatment', 'estecapelli' ),
						'title'         => __( 'What is a Hollywood Smile?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Hollywood Smile is a comprehensive smile design treatment that harmonises the relationship between teeth, gums and lips to create a smile that is symmetrical, natural and perfectly suited to the individual’s face. Rather than simply altering the appearance of the teeth, it takes a holistic approach, considering gum health, facial structure and aesthetic proportions as a whole.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'At Estecapelli, every Hollywood Smile procedure is tailored to the individual using digital design technologies and the principles of aesthetic dentistry. The result is a smile that is not only visually striking but also healthy, balanced and built to last.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Candidates', 'estecapelli' ),
						'title'         => __( 'Who Is Hollywood Smile Suitable For?', 'estecapelli' ),
						'body'          => __( 'Hollywood Smile is an ideal solution for individuals experiencing any of the following:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Tooth discoloration and staining', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Crowding, gaps or irregular alignment', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Worn, broken or short teeth', 'estecapelli' ) ),
							array( 'icon' => 'face',         'label' => __( 'Excessive gum display (gummy smile)', 'estecapelli' ) ),
							array( 'icon' => 'tooth',        'label' => __( 'Missing teeth', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'A smile that lacks harmony with the facial and lip structure', 'estecapelli' ) ),
							array( 'icon' => 'star',         'label' => __( 'Anyone looking for a whiter, more symmetrical and more attractive smile', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'stepbook',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'How Is a Hollywood Smile Applied?', 'estecapelli' ),
						'lead'          => __( 'Hollywood Smile is a highly personalised treatment, and the procedure is tailored to the unique needs of each patient. While the specific steps may vary, the process generally follows these key stages.', 'estecapelli' ),
						'items'         => array(
							array(
								'icon'    => 'target',
								'eyebrow' => __( 'Step 1', 'estecapelli' ),
								'title'   => __( 'Digital Smile Analysis', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'The patient’s facial shape, lip structure, gum line and tooth form are thoroughly analysed. Using this data, a fully personalised smile design is created digitally, allowing both the specialist and the patient to visualise the expected outcome before any treatment begins.', 'estecapelli' ) . '</p>',
							),
							array(
								'icon'    => 'medical-plus',
								'eyebrow' => __( 'Step 2', 'estecapelli' ),
								'title'   => __( 'Oral and Dental Health Preparation', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Before any aesthetic work begins, the foundation of a healthy oral structure must be established. Depending on the patient’s needs, this may include:', 'estecapelli' ) . '</p>'
									. '<ul><li>' . esc_html__( 'Treatment of any existing cavities', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Professional dental scaling', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Treatment of gum disease', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Root canal treatment where necessary', 'estecapelli' ) . '</li></ul>'
									. '<p>' . esc_html__( 'Aesthetic procedures are only initiated once full oral health has been confirmed, ensuring lasting and stable results.', 'estecapelli' ) . '</p>',
							),
							array(
								'icon'    => 'tooth',
								'eyebrow' => __( 'Step 3', 'estecapelli' ),
								'title'   => __( 'Restoring Missing Teeth', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Where missing teeth are present, implant treatment is carried out prior to the aesthetic procedure. Once the implant has fully integrated with the bone, aesthetic crowns are placed to complete the smile design seamlessly.', 'estecapelli' ) . '</p>',
							),
							array(
								'icon'    => 'face',
								'eyebrow' => __( 'Step 4', 'estecapelli' ),
								'title'   => __( 'Gum Aesthetics (Gummy Smile Treatment)', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'Where gum levels are asymmetrical or excessively visible, laser gum contouring is performed to achieve a balanced and proportionate gum line. This step plays a critical role in the overall smile design, as even the most refined dental work can fall short without a well-defined and symmetrical gum structure.', 'estecapelli' ) . '</p>',
							),
							array(
								'icon'    => 'sparkles',
								'eyebrow' => __( 'Step 5', 'estecapelli' ),
								'title'   => __( 'Teeth Renewal', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'The most suitable materials are applied to aesthetically reshape and transform the teeth. The most commonly used options in Hollywood Smile treatment are:', 'estecapelli' ) . '</p>'
									. '<p><strong>' . esc_html__( 'Porcelain Laminate Veneer', 'estecapelli' ) . '</strong></p>'
									. '<ul><li>' . esc_html__( 'Closest aesthetic appearance to natural teeth', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'High light translucency for a lifelike finish', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Requires minimal tooth reduction', 'estecapelli' ) . '</li></ul>'
									. '<p><strong>' . esc_html__( 'Zirconia Crowns', 'estecapelli' ) . '</strong></p>'
									. '<ul><li>' . esc_html__( 'Long-lasting due to their highly durable structure', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Suitable for use in cases of missing teeth', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Delivers a white and aesthetically pleasing result', 'estecapelli' ) . '</li></ul>'
									. '<p><strong>' . esc_html__( 'Composite Bonding', 'estecapelli' ) . '</strong></p>'
									. '<ul><li>' . esc_html__( 'Ideal for quickly correcting minor imperfections', 'estecapelli' ) . '</li>'
									. '<li>' . esc_html__( 'Can be applied without damaging the natural tooth structure', 'estecapelli' ) . '</li></ul>',
							),
							array(
								'icon'    => 'star',
								'eyebrow' => __( 'Step 6', 'estecapelli' ),
								'title'   => __( 'Teeth Whitening and Colour Matching', 'estecapelli' ),
								'body'    => '<p>' . esc_html__( 'In the final stage, the shade of all teeth is carefully harmonised to create a consistent and balanced appearance. This ensures the overall result looks natural, cohesive and aesthetically refined across the entire smile.', 'estecapelli' ) . '</p>',
							),
						),
					),
					array(
						'acf_fc_layout' => 'gallery',
						'eyebrow'       => __( 'Real Results', 'estecapelli' ),
						'title'         => __( 'Hollywood Smile — Before & After', 'estecapelli' ),
						'lead'          => __( 'Real smile-design transformations from our patients. Every result is tailored to the individual’s face for a natural, balanced and confident smile.', 'estecapelli' ),
						'items'         => array(), // Upload before/after composite images in the editor — recommended ratio 16:10.
						'cta'           => array( 'label' => __( 'View the full gallery', 'estecapelli' ), 'url' => home_url( '/en/before-after' ) ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Hollywood Smile — FAQ', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'What is the difference between veneers, crowns and a Hollywood Smile?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A Hollywood Smile is not a single product but a complete smile-design plan. Porcelain laminate veneers are ultra-thin shells bonded to the front of the teeth and require minimal reshaping; zirconium crowns cover the whole tooth and are used when a tooth is heavily worn, broken or root-canal treated. Your dentist combines whichever options suit each tooth — together with whitening or gum contouring where needed — so the final result looks balanced across the entire smile.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How much of my natural tooth is removed, and is it reversible?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It depends on the material. Porcelain laminate veneers need only a very thin layer of enamel removed — sometimes almost none — while crowns require more reshaping. Because some enamel is permanently reduced, the treatment is generally considered non-reversible, which is exactly why a careful digital smile analysis and a clear plan are agreed before any work begins.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will my smile look natural or too “fake white”?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A well-designed Hollywood Smile should look natural for your face. Shade, tooth shape and proportions are chosen from a digital smile design based on your facial features, age and skin tone — so you can have a bright, even smile without the flat, overly white “block of teeth” look. The shade is agreed with you in advance.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does the treatment take and how many days should I stay in Turkey?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most Hollywood Smile treatments are completed in roughly five to seven days, across two to three appointments — the first for preparation and temporaries, the following ones for fitting and fine-tuning the final restorations. We recommend planning your stay accordingly and keeping a day at the end for any small adjustments.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is the procedure painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The procedure itself is performed under local anaesthesia, so you should not feel pain during treatment. Some patients notice mild sensitivity to hot or cold for a few days afterwards, which is temporary and easily managed with simple aftercare and, if needed, over-the-counter pain relief.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long do the results last?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'With good oral hygiene and regular check-ups, porcelain laminate veneers and zirconium crowns typically last around ten to fifteen years, and often longer. Their lifespan depends mainly on daily care, gum health and avoiding habits such as biting hard objects or grinding your teeth.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How do I care for them, and will they stain?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'You care for them just like natural teeth: brushing twice a day, flossing and regular dental visits. High-quality porcelain and zirconium are highly resistant to staining and do not discolour the way natural enamel does, though good habits keep the surrounding gums and teeth healthy and the overall smile bright.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can a Hollywood Smile fix crooked or gapped teeth without braces?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'In many cases of mild crowding, gaps or slightly rotated teeth, veneers and crowns can create a straight, even appearance without orthodontics. For more significant misalignment your dentist may recommend orthodontic treatment first, or a combined plan, so the final result is both aesthetic and healthy.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What happens if a veneer or crown chips or comes off?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Modern veneers and crowns are strong, but in the rare event that one chips or debonds, a single unit can usually be repaired or replaced without redoing the whole smile. Avoiding very hard foods and wearing a night guard if you grind your teeth greatly reduces this risk.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Who is a good candidate for a Hollywood Smile?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Good candidates are adults with healthy gums and sufficient tooth structure who want to improve discoloration, staining, gaps, worn or asymmetrical teeth. Any active issues such as decay or gum disease are treated first. A short assessment — ideally with photos or a video consultation — lets our dentists confirm suitability and outline the right plan for you.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'form',
						'eyebrow'       => __( 'Free Consultation', 'estecapelli' ),
						'title'         => __( 'Ready for Your New Smile?', 'estecapelli' ),
						'lead'          => __( 'Send us a few details and our team will reply with a personalised Hollywood Smile plan and a no-obligation, all-inclusive quote — usually within a few hours.', 'estecapelli' ),
						'points'        => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Free digital smile assessment from your photos', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No-obligation, transparent all-inclusive quote', 'estecapelli' ) ),
							array( 'icon' => 'headset',      'label' => __( 'Fast reply from our dedicated patient-care team', 'estecapelli' ) ),
							array( 'icon' => 'languages',    'label' => __( 'Multilingual support at every step', 'estecapelli' ) ),
						),
						'submit_label'  => __( 'Request a Free Consultation', 'estecapelli' ),
						'show_whatsapp' => true,
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

		);
	}
}
