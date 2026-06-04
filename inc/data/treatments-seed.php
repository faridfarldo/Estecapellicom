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
							array( 'icon' => 'check-circle', 'value' => '1 session',   'label' => __( 'Sessions', 'estecapelli' ) ),
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
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'Exosome FUE Hair Transplantation Stages', 'estecapelli' ),
						'lead'          => __( 'Cutting-edge hair restoration that combines the precision of FUE with the regenerative power of exosome technology.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'hair',         'time' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Harvesting Hair Follicles', 'estecapelli' ),         'body' => __( 'Healthy grafts are individually extracted from the safe donor area using ultra-fine micromotors.', 'estecapelli' ) ),
							array( 'icon' => 'atom',         'time' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Strengthening in Exosome Solution', 'estecapelli' ), 'body' => __( 'The harvested grafts are placed in a specially formulated exosome solution that nourishes and prepares them for transplantation.', 'estecapelli' ) ),
							array( 'icon' => 'target',       'time' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Preparing the Transplantation Area', 'estecapelli' ), 'body' => __( 'The recipient area is prepared with precise channels using sapphire-tipped instruments for the most natural angle and direction.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Transplantation of the Follicles', 'estecapelli' ), 'body' => __( 'The exosome-strengthened follicles are implanted one by one for natural density and direction.', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'time' => __( 'Stage 5', 'estecapelli' ), 'title' => __( 'Healing Process', 'estecapelli' ),                  'body' => __( 'Patients return home the same day; our team guides every step of the recovery.', 'estecapelli' ) ),
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
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Exosome FUE — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'What is Exosome FUE hair transplantation?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Exosome FUE is an innovative, biotechnology-enhanced version of the classic FUE method. The exosomes it uses are micro-vesicles secreted by cells that stimulate follicle regeneration and support stronger, healthier hair growth.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is Exosome FUE more effective than classic FUE?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Compared with classic FUE it offers faster healing, a higher graft-survival rate and a denser result, because exosome technology strengthens the way the follicles adapt after transplantation.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Why choose Estecapelli for Exosome FUE?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Estecapelli is an international clinic with proven expertise in hair restoration. An experienced medical team, the latest technology and personalised treatment plans deliver natural, lasting Exosome FUE results.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see results?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'New growth usually appears within three to six months, with final, permanent results emerging at around twelve months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What is recovery like after Exosome FUE?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Recovery is faster than with classic methods. Mild swelling, redness and scabbing may appear in the first few days; these are normal and subside quickly when you follow the aftercare advice.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is Exosome FUE permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The transplanted follicles are resistant to shedding and continue to grow like natural hair, giving permanent results.', 'estecapelli' ) . '</p>' ),
						),
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
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'Female Hair Transplant Stages', 'estecapelli' ),
						'lead'          => __( 'The process is carried out in four fundamental stages, each meticulously managed to ensure natural-looking, dense, and lasting results.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'clipboard',   'time' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Consultation & Planning', 'estecapelli' ),     'body' => __( 'Your hair loss is analysed, your expectations evaluated, and a fully personalised treatment plan is created tailored to your unique needs.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',    'time' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Natural Hairline Design', 'estecapelli' ),     'body' => __( 'An aesthetic, natural hairline is carefully designed in harmony with your facial features.', 'estecapelli' ) ),
							array( 'icon' => 'target',      'time' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Hair Follicle Extraction', 'estecapelli' ),    'body' => __( 'Healthy grafts are individually and precisely harvested from the donor area using the least invasive method possible.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle','time' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Graft Implantation', 'estecapelli' ),          'body' => __( 'The harvested follicles are precisely implanted into the target area at the correct angle and direction for a seamless, natural-looking density.', 'estecapelli' ) ),
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
							array( 'question' => __( 'When will I see results?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Final results generally appear within six to twelve months. Shock shedding can occur in the first three months; from month six new, permanent hair begins to grow. Timing varies with the technique used and your individual healing.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is a female hair transplant permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The shedding-resistant follicles taken from the donor area continue to grow like natural hair after transplantation, making it a permanent solution.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are there any risks or side effects?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is a safe procedure. Mild swelling, redness, temporary scabbing and itching may appear afterwards; with correct care and expert guidance these resolve quickly.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can women with fine or sparse hair have a transplant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes, provided there are enough follicles in the donor area. For patients with a weaker donor area, mesotherapy or stem-cell-supported treatments may be recommended alongside.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Full recovery averages one to two months, though returning to daily life is possible right after the procedure.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does the result look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. With the right techniques and an aesthetic hairline design — placing follicles along the natural growth direction — the result is completely natural and undetectable.', 'estecapelli' ) . '</p>' ),
						),
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
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'How is Hair Mesotherapy Performed?', 'estecapelli' ),
						'lead'          => __( "Estecapelli's step-by-step application process.", 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'target',       'time' => __( 'Step 1', 'estecapelli' ), 'title' => __( 'Analysis', 'estecapelli' ),                            'body' => __( 'The hair and scalp are evaluated with TrichoLab to understand follicle quality, density, and the cause of hair loss.', 'estecapelli' ) ),
							array( 'icon' => 'atom',         'time' => __( 'Step 2', 'estecapelli' ), 'title' => __( 'Preparation of the Mesotherapy Mixture', 'estecapelli' ), 'body' => __( 'A customised solution of vitamins, minerals, amino acids, and growth factors is prepared for each patient.', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'time' => __( 'Step 3', 'estecapelli' ), 'title' => __( 'Application', 'estecapelli' ),                         'body' => __( 'The mixture is injected directly into the scalp using fine-tipped needles. For sensitive scalps, a topical anaesthetic can be applied.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'Step 4', 'estecapelli' ), 'title' => __( 'Treatment Program', 'estecapelli' ),                   'body' => __( 'A program of 4–6 sessions is typically applied at intervals of 4–6 weeks, adjusted to your level of hair loss.', 'estecapelli' ) ),
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
							array( 'question' => __( 'How many sessions before it works?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Hair mesotherapy is usually planned as four to six sessions. The exact number is personalised to the severity of hair loss, your general health and your hair structure.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is hair mesotherapy painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is generally painless. You may feel only a slight pricking sensation during application; the procedure stays comfortable even for those with a low pain threshold.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Who is it suitable for?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It can be applied for different types of hair loss in men and women, such as androgenetic alopecia and telogen effluvium. The cause of hair loss should first be assessed in detail by a specialist.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can it be combined with other treatments?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. It can be combined with medical treatments such as minoxidil and finasteride, or with DHI, FUE and Sapphire FUE transplantation. Our specialists determine the best combination for you.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are there any side effects?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is a safe procedure. Rarely, temporary redness, mild swelling or itching may occur and disappear on their own shortly after.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long do the results last?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The effects can last a long time. If the factors causing hair loss persist, periodic maintenance sessions may be recommended to preserve the results.', 'estecapelli' ) . '</p>' ),
						),
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
						'image'         => '',
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
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'How is Sapphire FUE Applied at Estecapelli?', 'estecapelli' ),
						'lead'          => __( 'Every procedure is built on careful planning and precise execution at each stage.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'clipboard',    'time' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Consultation & Planning', 'estecapelli' ),     'body' => __( 'A comprehensive consultation and scalp analysis create the most accurate treatment plan based on your unique hair structure and goals.', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'time' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Local Anaesthesia', 'estecapelli' ),         'body' => __( 'Local anaesthesia is applied to the donor area to ensure a comfortable, pain-free graft harvest.', 'estecapelli' ) ),
							array( 'icon' => 'target',       'time' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Graft Collection', 'estecapelli' ),           'body' => __( 'Healthy follicles are individually harvested from the safe donor area using ultra-fine micromotors.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Sapphire Channel Opening', 'estecapelli' ),   'body' => __( 'Channels are created with sapphire-tipped blades — the most decisive stage for natural density, angle and direction.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Stage 5', 'estecapelli' ), 'title' => __( 'Follicle Implantation', 'estecapelli' ),       'body' => __( 'The harvested follicles are implanted one by one into the pre-opened channels at the correct depth and angle.', 'estecapelli' ) ),
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
							array( 'question' => __( 'What is the difference between Sapphire FUE and standard FUE?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Standard FUE opens channels with steel blades, while Sapphire FUE uses sapphire-tipped blades that create sharper, smoother and smaller channels — meaning a more natural hairline, less scarring and faster healing.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Who is a good candidate?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Candidates are generally over 25 with a healthy donor area and no medical condition that would affect the procedure or healing. A free consultation confirms your suitability.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does it require shaving?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Sapphire FUE typically involves shaving the donor and recipient areas; unshaven options can be discussed during your consultation.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see the final result?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'New growth starts around month three and full, natural density is generally achieved within twelve months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is Sapphire FUE painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Like every hair transplant method at Estecapelli, Sapphire FUE is performed under local anaesthesia, so no pain is felt during the procedure. Your comfort is prioritised at every stage with support from our expert team.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are there any side effects?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Temporary effects such as mild redness, scabbing or shock shedding can occur after the procedure. With proper care and by following expert advice, these settle and disappear completely in a short time.', 'estecapelli' ) . '</p>' ),
						),
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
						'image'         => '',
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
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'How is DHI Performed at Estecapelli?', 'estecapelli' ),
						'lead'          => __( 'A precise, four-stage process managed end to end by our medical team.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'clipboard',    'time' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Consultation & Planning', 'estecapelli' ), 'body' => __( 'Your hair structure and goals are analysed with AI-supported TrichoLab to design a fully personalised plan.', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'time' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Local Anaesthesia', 'estecapelli' ),     'body' => __( 'Local anaesthesia is applied for a comfortable, pain-free procedure.', 'estecapelli' ) ),
							array( 'icon' => 'target',       'time' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Graft Collection', 'estecapelli' ),       'body' => __( 'Follicles are individually extracted from the donor area, ready for direct implantation.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Implantation with Choi Pen', 'estecapelli' ), 'body' => __( 'Grafts are implanted directly with the Choi pen at the precise angle and density for a natural result.', 'estecapelli' ) ),
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
							array( 'question' => __( 'What is the difference between DHI and FUE?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'With DHI, follicles are implanted directly using a Choi pen without opening separate channels first, giving denser placement and faster healing. FUE opens channels before implantation.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is DHI shaving-free?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'DHI is ideal for unshaven and targeted procedures. Depending on the size of the area, existing hair can often be preserved — your consultant will confirm what suits you.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Who is DHI best suited to?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'DHI is an excellent choice for early-stage hair loss, diffuse thinning, smaller areas and patients seeking ultra-precise, dense placement.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see results?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Visible growth typically begins around month three, with full results generally seen by month twelve.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is DHI painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The DHI procedure is carried out under local anaesthesia, so no pain is felt during treatment. At Estecapelli, patient comfort is prioritised at every stage and the process is carefully managed by our expert team.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does recovery take?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Because DHI is a minimally invasive method, recovery is fast. Most patients return to daily life within a few days, and any redness or scabbing subsides shortly after.', 'estecapelli' ) . '</p>' ),
						),
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
						'body'          => '<p>' . esc_html__( 'The VITA Protocol is a two-phase treatment developed by Estecapelli medical experts to increase graft survival. The period grafts spend outside the body is a critical factor in the success of a transplant — VITA’s vitamin-derived serum, rich in vitamins, minerals, growth factors and ATP, strengthens follicles and supports healthy new growth. It combines easily with both FUE and DHI procedures.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
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
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'How is the VITA Protocol Applied?', 'estecapelli' ),
						'lead'          => __( 'A two-stage protocol that integrates seamlessly into your transplant.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'medical-plus', 'time' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Microneedling & Nutrient Serum', 'estecapelli' ), 'body' => __( 'Microchannels are created and a highly concentrated nutrient serum is applied to prepare the scalp.', 'estecapelli' ) ),
							array( 'icon' => 'atom',         'time' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Cold-Vapour Serum on Grafts', 'estecapelli' ),  'body' => __( 'The same nourishing serum is applied to the harvested grafts with a cold-vapour method to keep them strong until implantation.', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'VITA Treatment — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Is VITA a standalone treatment?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'VITA is a protocol that enhances a hair transplant. It is combined with your FUE or DHI procedure to protect grafts and improve survival.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Who benefits most from VITA?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is especially valuable for patients with weak follicles, fine hair, or a donor area that needs every graft to count.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does VITA change recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'By nourishing the scalp and grafts, the protocol supports a faster, healthier healing process.', 'estecapelli' ) . '</p>' ),
						),
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
			// TrichoLab — AI Hair Analysis
			// ============================================================
			array(
				'slug'     => 'tricholab',
				'title'    => 'TrichoLab',
				'category' => 'Hair Transplant',
				'sections' => array(

					array(
						'acf_fc_layout' => 'hero',
						'eyebrow'       => __( 'AI-Powered Hair Analysis', 'estecapelli' ),
						'title'         => __( 'TrichoLab', 'estecapelli' ),
						'lead'          => __( 'An advanced AI-powered hair analysis system that examines the hair and scalp through high-resolution trichoscopic imaging — turning your scalp into precise data that drives a fully personalised transplant plan.', 'estecapelli' ),
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
						'cta_secondary' => array( 'label' => __( 'Chat on WhatsApp', 'estecapelli' ), 'url' => $whatsapp ),
						'media_type'    => 'image',
						'image'         => '',
						'video_id'      => '',
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Technology', 'estecapelli' ),
						'title'         => __( 'What is TrichoLab?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'TrichoLab is an advanced AI-powered hair analysis system that examines the hair and scalp through high-resolution trichoscopic imaging. By measuring critical parameters with millimetric precision, it provides objective, data-driven insights that guide our specialists in planning the most effective treatment for each individual.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'The system evaluates a comprehensive range of factors, including hair follicle density, graft potential, hair shaft thickness, donor area capacity, hair loss mapping and miniaturisation rate. This level of detail allows our team to design a personalised treatment plan based on accurate, real-time data rather than estimation, ensuring the best possible outcome for every patient.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'Hair Analysis with TrichoLab at Estecapelli', 'estecapelli' ),
						'lead'          => __( 'From high-resolution imaging to a precise, personalised plan.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'target',       'time' => __( 'Step 1', 'estecapelli' ), 'title' => __( 'Digital Trichoscopic Scanning', 'estecapelli' ), 'body' => __( 'The scalp is examined with high-resolution micro-imaging, capturing detailed visuals of the hair follicles.', 'estecapelli' ) ),
							array( 'icon' => 'atom',         'time' => __( 'Step 2', 'estecapelli' ), 'title' => __( 'AI-Powered Data Analysis', 'estecapelli' ),     'body' => __( 'Advanced algorithms turn the imaging data into precise numerical values for density and follicular distribution.', 'estecapelli' ) ),
							array( 'icon' => 'clipboard',    'time' => __( 'Step 3', 'estecapelli' ), 'title' => __( 'Personalised Treatment Plan', 'estecapelli' ),   'body' => __( 'The data forms the basis of a fully personalised plan, including the exact number of grafts required.', 'estecapelli' ) ),
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
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Why Estecapelli', 'estecapelli' ),
						'title'         => __( 'Why TrichoLab at Estecapelli?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'TrichoLab is an advanced analysis system available in only a limited number of clinics across Turkey, and at Estecapelli it sits at the core of everything we do, giving our patients access to a level of analysis that few clinics can offer.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Every evaluation is objective, measurable and fully transparent, giving patients a clear understanding of their hair health before any procedure begins. A truly personalised transplant strategy is developed for each patient, built on accurate data rather than guesswork, and planning is designed to be both safe and sustainable — protecting the donor area and maximising long-term results.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'At Estecapelli, every procedure is data-driven, because a natural result starts with an accurate analysis.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
					),

					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'TrichoLab — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Is TrichoLab analysis painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Not at all. It is a non-invasive imaging process that simply scans the scalp — no needles, no discomfort.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Why does TrichoLab matter for my transplant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It calculates the exact number of grafts you need and protects your donor area, so the plan is accurate, natural and sustainable.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is it included in my consultation?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'At Estecapelli, TrichoLab analysis is part of building your personalised treatment plan. Contact us to arrange your assessment.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What exactly is TrichoLab analysis?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'TrichoLab is an advanced hair analysis system that examines the hair and scalp with high-resolution imaging and AI-supported software, measuring hair density, hair strand thickness, donor area capacity and shedding pattern.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What data does it analyse?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It typically measures hair density, follicle distribution, hair strand thickness, donor area capacity, the shedding map and the condition of the scalp.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does TrichoLab determine the exact number of grafts?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'TrichoLab strengthens planning by supporting the graft requirement with scientific data. The final decision is made together with clinical factors such as expert assessment and hairline design.', 'estecapelli' ) . '</p>' ),
						),
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
						'image'         => '',
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
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'Step by Step', 'estecapelli' ),
						'title'         => __( 'The Eyebrow Transplant Stages', 'estecapelli' ),
						'lead'          => __( 'Designed around your facial proportions for a natural, lasting result.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'sparkles',     'time' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Eyebrow Design', 'estecapelli' ),       'body' => __( 'A natural eyebrow shape is created by taking facial proportions, muscle structure and the individual’s preferences into account. This stage is the most critical part of the procedure.', 'estecapelli' ) ),
							array( 'icon' => 'target',       'time' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Graft Harvesting', 'estecapelli' ),     'body' => __( 'Single hair follicles are extracted from the nape area using a specialised micromotor. Using only single follicles is essential for achieving natural-looking results in eyebrow transplantation.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'DHI Implantation', 'estecapelli' ),     'body' => __( 'The DHI method is preferred in eyebrow transplantation. With this technique, channel opening and graft implantation are performed simultaneously thanks to special pens.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Procedure Duration', 'estecapelli' ), 'body' => __( 'Eyebrow transplantation takes approximately 2 to 3 hours. As it is performed under local anaesthesia, the procedure is highly comfortable.', 'estecapelli' ) ),
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
						'body'          => '<p>' . esc_html__( 'The most suitable technique for eyebrow transplantation is the DHI (Direct Hair Implantation) method. With the DHI method, we are able to achieve:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'More controlled direction of eyebrow hairs', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Channel opening and implantation performed simultaneously', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'More successful creation of natural angles', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Shorter recovery time compared to other techniques', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Minimal trauma to the facial area', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'The FUE method is not frequently preferred for the eyebrow area, as it results in a longer recovery period.', 'estecapelli' ) . '</p>',
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
							array( 'question' => __( 'Will the result look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Using single follicles implanted at the correct angle and direction with the DHI method creates a brow that looks and behaves completely naturally.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does the procedure take?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Around 2–3 hours under local anaesthesia, performed as a comfortable day procedure.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Transplanted brow follicles are permanent; after an initial shedding phase they grow in for a lasting result.', 'estecapelli' ) . '</p>' ),
						),
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
						'image'         => '',
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
						'body'          => '<p>' . esc_html__( 'A beard transplant is a surgical procedure that restores fullness to the beard by transplanting hair follicles into sparse, uneven or hairless areas. At Estecapelli, our expert team creates a personalised beard design tailored to each individual’s facial features and expectations. The procedure is performed under local anaesthesia and is generally painless throughout. Recovery time varies from person to person, but most patients return to their social lives relatively quickly, and following the aftercare guidelines provided by our team helps speed up healing and ensures the best possible results.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'Stages of Beard Transplantation', 'estecapelli' ),
						'lead'          => __( 'Careful harvesting and precise placement for a natural beard line.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'target',       'time' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Graft Harvesting', 'estecapelli' ),  'body' => __( 'The FUE method is used for harvesting hairs from the safe donor areas at the back and the sides of the head. Each follicle is carefully extracted one by one to minimise damage and preserve the natural look of the donor area.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Channel Opening', 'estecapelli' ),   'body' => __( 'The direction, depth and angle of the channels are crucial in achieving a natural-looking result. Our experts carefully create micro-channels that align with the natural growth pattern of your beard, ensuring each follicle sits seamlessly within the surrounding hair.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Graft Implantation', 'estecapelli' ), 'body' => __( 'In the final stage, the harvested grafts are carefully placed into the prepared channels one by one. Each follicle is implanted at the correct angle and depth to ensure a seamless, natural appearance — a fuller, well-defined beard that looks and feels completely natural.', 'estecapelli' ) ),
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
							array( 'question' => __( 'Where do the grafts come from?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Healthy follicles are harvested with the FUE method from the safe donor area at the back and sides of the head.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will the beard look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Careful control of the angle, depth and direction of each graft ensures the beard grows in naturally and blends with existing hair.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What side effects should I expect?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'As with any procedure there may be minor, temporary redness, swelling or sensitivity, which settle quickly. Rarely, there is a risk of infection or scarring, minimised by following expert advice.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does recovery take?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Recovery after a beard transplant generally takes 1–2 weeks. The full growth of the transplanted beard and its final appearance form over 6–12 months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When will I see results?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The first beard growth usually begins within 3–6 months, with permanent, clear results appearing at the end of 6–12 months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is a beard transplant permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. When performed with the right techniques by an expert team, a beard transplant delivers permanent results. Estecapelli’s personalised process targets natural, long-lasting satisfaction.', 'estecapelli' ) . '</p>' ),
						),
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
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Rhinoplasty — FAQ', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Who is rhinoplasty suitable for?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Adults whose nose has finished developing, who are in good general health, and who are unhappy with the shape of their nose or have a breathing problem.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does the surgery take?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A rhinoplasty operation usually takes two to three hours, depending on the technique used.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Initial recovery takes seven to ten days; full healing and final settling of the nose shape takes six to twelve months. Swelling and bruising usually fade within one to two weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does rhinoplasty make breathing harder?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'No. On the contrary, functional rhinoplasty can resolve existing breathing problems such as a deviated septum.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will there be visible scars?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Closed rhinoplasty leaves no external scars; with the open technique the incision is tiny and usually unnoticeable.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent and natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Rhinoplasty gives permanent results, and in experienced hands the outcome looks completely natural.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'Rhinoplasty Prices', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Rhinoplasty pricing varies depending on the technique used, the scope of the procedure and your individual needs and anatomy. A personalised quote is provided following a detailed consultation, ensuring full transparency before any decisions are made.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
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
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'BBL — FAQ', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Who is BBL suitable for?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'People in good general health who want more buttock volume and have enough body fat to harvest for the transfer.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How is a BBL performed?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Fat is removed from the body with liposuction, purified, and then injected into the buttocks to add shape and volume.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does the surgery take?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A BBL operation usually takes two to four hours under general anaesthesia.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery, and when can I sit normally?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Initial recovery takes two to three weeks and full recovery six to eight weeks. Avoid sitting directly on the buttocks for the first two to three weeks; a special cushion helps. Light walking is fine after about two weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Some of the injected fat is reabsorbed; the fat that survives is permanent. Final results usually become clear within three to six months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does it look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'With personalised planning and the right technique, BBL gives very natural-looking results.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'BBL Prices', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'BBL pricing varies depending on the technique used (fat transfer or implants), the scope of the procedure, any combined treatments and your individual needs and anatomy. A personalised quote is provided following a detailed consultation, ensuring full transparency before any decisions are made.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
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
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Breast Aesthetics — FAQ', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Who is breast augmentation suitable for?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'People in good general health who are unhappy with their breast volume or have lost volume after pregnancy or weight change.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Which methods are used?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The most common method is silicone implants; for suitable patients, fat transfer can also be used.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Round or teardrop implants?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Both are excellent; the right choice depends on your anatomy and the look you want. Your surgeon recommends the best option during consultation.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does surgery take and how long is recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The operation usually takes one to two hours. Initial recovery is seven to ten days, with full recovery in four to six weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are breast implants safe?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. FDA-approved silicone implants have been used safely for many years.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent and natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Results are long-lasting and implants can be used for many years. With the right implant choice and technique, the result looks very natural. Incisions are placed discreetly and fade over time.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'Breast Aesthetics Prices', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Breast aesthetics pricing — whether for augmentation, reduction or lift — varies depending on several factors. These include the type of implant selected, the scope of the procedure, any additional treatments such as a lift, asymmetry correction or combined surgeries, and the patient’s individual needs and anatomy. A personalised quote is provided following a detailed consultation, ensuring full transparency before any decisions are made.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
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
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Type', 'estecapelli' ),
						'title'         => __( 'Full Abdominoplasty', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Full abdominoplasty is preferred for individuals with advanced sagging and muscle separation throughout the abdominal area. The procedure addresses the abdomen comprehensively through the following steps:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'The entire abdominal area is tightened', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The navel is repositioned for a natural appearance', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Excess skin is removed', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'The underlying muscles are tightened and repaired', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Type', 'estecapelli' ),
						'title'         => __( 'Mini Tummy Tuck', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A mini tummy tuck is suitable for individuals with mild sagging limited to the area below the navel. Compared to a full abdominoplasty, it is a less invasive option with several notable benefits:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Smaller incision', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Faster recovery time', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'No repositioning of the navel', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Type', 'estecapelli' ),
						'title'         => __( 'Liposuction + Abdominoplasty Combination', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'This combination is the ideal approach when both excess fat and skin sagging are present in the abdominal area. Liposuction is first performed to remove unwanted fat, followed by the removal of excess skin for a smoother and more contoured result.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Type', 'estecapelli' ),
						'title'         => __( 'Mommy Makeover', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A mommy makeover combines multiple procedures into a single surgery for women looking to restore their body after childbirth. It typically includes abdominoplasty, breast lift or augmentation, and liposuction, allowing patients to achieve a full transformation with just one recovery period.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
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
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Tummy Tuck — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Does a tummy tuck make you lose weight?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'No. A tummy tuck is a body-contouring operation, not a weight-loss method.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'What is the difference between a mini and a full tummy tuck?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A mini tummy tuck addresses the lower abdomen, while a full tummy tuck covers the whole abdomen and includes muscle repair.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Initial recovery takes about two to three weeks, with full recovery in six to eight weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will there be a scar?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes, but the scar is planned to sit along the underwear line and fades over time.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes, results are long-lasting, though significant weight gain or pregnancy can affect them.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When can I exercise again?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Light walking is possible within a few days; strenuous exercise after about six to eight weeks.', 'estecapelli' ) . '</p>' ),
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
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Techniques', 'estecapelli' ),
						'title'         => __( 'Types of Liposuction', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'At Estecapelli, different liposuction techniques are used to achieve body-contouring goals. Each technique is chosen based on the person’s fat distribution, skin elasticity and desired results.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Technique', 'estecapelli' ),
						'title'         => __( 'Vaser Liposuction', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Vaser Liposuction is an advanced technique that uses ultrasound energy to liquefy fat cells before removal, allowing for precise and gentle body contouring.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
							. '<ul><li>' . esc_html__( 'Targets fat precisely', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Minimally invasive with less tissue trauma', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Smooth, natural-looking results', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Faster recovery compared to traditional liposuction', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Technique', 'estecapelli' ),
						'title'         => __( 'Laser Liposuction', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Laser Liposuction uses intense laser energy to break down fat cells, while simultaneously promoting skin tightening for smoother contours.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
							. '<ul><li>' . esc_html__( 'Melts fat precisely', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Supports skin tightening', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Minimally invasive with quick recovery', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Smooth and natural-looking results', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Technique', 'estecapelli' ),
						'title'         => __( 'Tumescent Liposuction', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Tumescent Liposuction involves injecting a special solution into the targeted area before fat removal to minimise bleeding and bruising. Today, it is often combined with other liposuction techniques for enhanced results.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Technique', 'estecapelli' ),
						'title'         => __( 'PAL (Power Assisted Liposuction)', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'PAL (Power Assisted Liposuction) uses vibration-assisted cannulas to break down fat more easily and efficiently, making the procedure faster and smoother.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
							. '<ul><li>' . esc_html__( 'Easier and faster fat removal', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Less surgeon fatigue, more precise', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Minimally invasive with smoother results', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Can be combined with other techniques', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Technique', 'estecapelli' ),
						'title'         => __( 'Classic Liposuction', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Traditional Liposuction is a conventional fat removal technique performed manually without devices. It remains a preferred option in certain cases.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
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
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Liposuction — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Does liposuction make you lose weight?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'No. Liposuction is a body-shaping procedure, not a weight-loss method.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Which areas can be treated?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The abdomen, waist, flanks, hips, thighs, arms, back, inner knees and chin, among others.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Initial recovery is usually one to two weeks, with full recovery in four to six weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes, the removed fat cells are permanently gone. Weight gain can, however, cause fat to build up in other areas.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does it look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Applied with the right technique by an experienced surgeon, results look natural and proportionate.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'When can I exercise again?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Light walking after a few days; strenuous exercise after about four to six weeks.', 'estecapelli' ) . '</p>' ),
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
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Techniques', 'estecapelli' ),
						'title'         => __( 'Types of Face Lifts', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'At Estecapelli, different facelift techniques are tailored to each person’s anatomy, expectations and needs.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Type', 'estecapelli' ),
						'title'         => __( 'Mini Face Lift', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A Mini Facelift is a minimally invasive procedure designed for younger patients with mild to moderate facial sagging. It focuses on tightening the lower face and jawline, providing a more refreshed and youthful appearance with smaller incisions and a quicker recovery compared to a traditional facelift.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
							. '<ul><li>' . esc_html__( 'Minimally invasive procedure', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Smaller incisions and minimal scarring', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Faster recovery time', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Natural-looking results', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Improves jawline and mild sagging', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Type', 'estecapelli' ),
						'title'         => __( 'Full Face Lift', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A Full Facelift is a comprehensive facial rejuvenation procedure designed for patients with moderate to advanced signs of aging. It targets deeper facial sagging by tightening the skin and underlying tissues of the mid-face, jawline and neck, creating a smoother and more youthful appearance.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
							. '<ul><li>' . esc_html__( 'Treats moderate to severe facial sagging', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Improves jawline and neck contours', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Long-lasting results', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Comprehensive facial rejuvenation', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Natural and balanced appearance', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Type', 'estecapelli' ),
						'title'         => __( 'Deep Plane Face Lift', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A Deep Plane Facelift is an advanced facelift technique designed to treat moderate to severe facial aging. This method lifts the deeper facial tissues rather than only tightening the skin, allowing for more natural and longer-lasting rejuvenation, especially in the mid-face, nasolabial folds, jawline and neck.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
							. '<ul><li>' . esc_html__( 'Lifts deeper facial tissues for natural results', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Improves mid-face volume and nasolabial folds', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Enhances jawline and neck contour', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Long-lasting rejuvenation', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'More natural facial movement and expression', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Type', 'estecapelli' ),
						'title'         => __( 'Neck Lift', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A Neck Lift is a procedure designed to improve loose skin, excess fat and muscle banding in the neck area. It helps create a firmer, smoother and more defined neck and jawline, restoring a more youthful profile.', 'estecapelli' ) . '</p>'
							. '<p><strong>' . esc_html__( 'Advantages:', 'estecapelli' ) . '</strong></p>'
							. '<ul><li>' . esc_html__( 'Tightens loose neck skin', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Improves jawline definition', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Reduces neck bands and excess fat', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Creates a smoother, youthful neck contour', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
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
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Face & Neck Lift — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'What is a facelift?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'A facelift is a surgical procedure that reduces sagging and wrinkles on the face for a younger, more dynamic appearance.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Can a facelift and neck lift be done together?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. As the face and neck form an aesthetic whole, the two are often performed together.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Initial recovery takes ten to fourteen days; full settling of the tissues takes six to eight weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will there be swelling and bruising?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes, swelling and bruising are normal and subside gradually over a few weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes, results last many years, although the natural ageing process continues.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will there be visible scars?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Incisions are hidden around the ears and within the hairline, and the scars fade over time.', 'estecapelli' ) . '</p>' ),
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
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Bariatric Surgery — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Who is bariatric surgery suitable for?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'People with a body mass index (BMI) of 35 or above, or a BMI of 30+ with obesity-related health problems.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Which weight-loss surgeries are performed?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The most common are sleeve gastrectomy (gastric sleeve), gastric bypass and mini gastric bypass.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How much weight will I lose?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Patients typically lose 60 to 80 percent of their excess weight within the first 12 to 18 months.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is bariatric surgery safe?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. Performed by experienced surgeons in fully equipped hospitals, it is a safe and effective method.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Most patients return to daily life within one to two weeks, with full recovery in around four to six weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does it improve other health conditions?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. It brings marked improvement in conditions such as type 2 diabetes, high blood pressure, sleep apnea and joint pain.', 'estecapelli' ) . '</p>' ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Pricing', 'estecapelli' ),
						'title'         => __( 'Obesity Surgery Prices', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Prices vary based on factors such as the chosen method, the patient’s health, the preferred balloon type and the surgical technique. The most accurate prices are determined after an assessment with Estecapelli specialists.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Request a Personalised Quote', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
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
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Operation', 'estecapelli' ),
						'title'         => __( 'How Is Gynecomastia Surgery Performed?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Gynecomastia surgery is performed under general anaesthesia in a fully equipped operating room. The procedure is tailored to each patient’s specific condition, and its content varies depending on the type and degree of gynecomastia present.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Case Type', 'estecapelli' ),
						'title'         => __( 'Cases With Excess Fatty Tissue Only', 'estecapelli' ),
						'body'          => '<ul><li>' . esc_html__( 'Liposuction or Vaser Liposuction is the preferred technique', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Fatty tissue is removed using cannulas inserted through very small incisions in the chest area', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'When skin elasticity is sufficient, a flat and natural chest contour is achieved without the need for additional skin incisions', 'estecapelli' ) . '</li></ul>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Case Type', 'estecapelli' ),
						'title'         => __( 'Cases With Both Breast Tissue Enlargement and Skin Sagging', 'estecapelli' ),
						'body'          => '<ul><li>' . esc_html__( 'Glandular breast tissue is surgically removed', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Excess skin is excised and the chest is reshaped for a firmer, flatter contour', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Inverted T or circular incisions are used when necessary', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'The average duration of the surgery is approximately 1.5–2 hours.', 'estecapelli' ) . '</p>'
							. '<p>' . esc_html__( 'Liposuction-based procedures leave minimal scarring. In surgeries involving tissue and skin removal, incisions are strategically placed in the most discreet areas possible and fade significantly over time with proper care.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
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
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Gynecomastia — Frequently Asked Questions', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'What is gynecomastia?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Gynecomastia is the abnormal enlargement of breast tissue in men due to hormonal or structural causes.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How is gynecomastia treated?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is treated with liposuction and/or surgical removal of the breast tissue.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long is recovery?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Initial recovery takes one to two weeks, with full recovery in four to six weeks.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are the results permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The removed breast tissue does not grow back; the results are permanent.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will it come back?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The risk of recurrence is low unless there are hormonal problems or significant weight gain.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does the chest look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Done with the right technique, the chest takes on a flat, natural appearance.', 'estecapelli' ) . '</p>' ),
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
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'How is a Dental Implant Applied?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Implant treatment follows a structured process designed to deliver safe, long-lasting and natural-looking results. The main steps of the treatment process are as follows.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'Step by Step', 'estecapelli' ),
						'title'         => __( 'The Treatment Process', 'estecapelli' ),
						'lead'          => __( 'A structured, five-stage process for a safe and lasting result.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'target',       'time' => __( 'Step 1', 'estecapelli' ), 'title' => __( 'Clinical Examination & Imaging', 'estecapelli' ), 'body' => __( 'The jawbone is thoroughly analysed using panoramic X-rays and digital tomography. Bone density, implant size and the treatment area are carefully evaluated to create a personalised implant plan, and the patient’s overall oral health and medical history are reviewed.', 'estecapelli' ) ),
							array( 'icon' => 'clipboard',    'time' => __( 'Step 2', 'estecapelli' ), 'title' => __( 'Treatment Planning', 'estecapelli' ),            'body' => __( 'A personalised treatment plan is prepared based on the number of missing teeth, jaw structure and overall oral health. If preliminary procedures such as bone grafting or tooth extraction are required, these are incorporated into the plan.', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'time' => __( 'Step 3', 'estecapelli' ), 'title' => __( 'Implant Placement', 'estecapelli' ),             'body' => __( 'Under local anaesthesia, the titanium implant root is carefully placed into the jawbone, typically taking 15 to 30 minutes per implant. Mild soreness or swelling in the following days is a normal part of healing.', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'time' => __( 'Step 4', 'estecapelli' ), 'title' => __( 'Osseointegration', 'estecapelli' ),               'body' => __( 'A healing period of approximately 2 to 3 months allows the titanium root to fully integrate with the surrounding bone, creating a strong and stable foundation for the prosthetic tooth.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Step 5', 'estecapelli' ), 'title' => __( 'Prosthetic Structure', 'estecapelli' ),           'body' => __( 'Once osseointegration is complete, a custom-made prosthetic tooth is attached. Porcelain, zirconium or hybrid prostheses may be used, designed to match the colour, shape and size of the surrounding natural teeth.', 'estecapelli' ) ),
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
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Dental Implants — FAQ', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Who is a dental implant suitable for?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'People in good general health with enough jawbone who have lost one or more teeth. Conditions such as uncontrolled diabetes or advanced bone loss may need extra evaluation first.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does treatment take?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Treatment usually takes two to four months. After placement the implant needs time to integrate with the bone before the prosthetic tooth is fitted; in some cases same-day implants are possible.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is the procedure painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'It is performed under local anaesthesia, so no pain is felt during placement. Mild sensitivity afterwards is normal.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Does it look natural?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. The prosthetic tooth fitted onto the implant looks and functions like a natural tooth.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Are dental implants permanent?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Yes. With correct care and regular check-ups, implants can be used comfortably for many years.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How should I care for my implant?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Regular brushing, flossing and routine dental check-ups keep the implant and surrounding gum healthy.', 'estecapelli' ) . '</p>' ),
						),
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
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'How Is a Hollywood Smile Applied?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Hollywood Smile is a highly personalised treatment, and the procedure is tailored to the unique needs of each patient. While the specific steps may vary, the process generally follows these key stages.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Step 1', 'estecapelli' ),
						'title'         => __( 'Digital Smile Analysis', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The patient’s facial shape, lip structure, gum line and tooth form are thoroughly analysed. Using this data, a fully personalised smile design is created digitally, allowing both the specialist and the patient to visualise the expected outcome before any treatment begins.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Step 2', 'estecapelli' ),
						'title'         => __( 'Oral and Dental Health Preparation', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Before any aesthetic work begins, the foundation of a healthy oral structure must be established. Depending on the patient’s needs, this may include:', 'estecapelli' ) . '</p>'
							. '<ul><li>' . esc_html__( 'Treatment of any existing cavities', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Professional dental scaling', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Treatment of gum disease', 'estecapelli' ) . '</li>'
							. '<li>' . esc_html__( 'Root canal treatment where necessary', 'estecapelli' ) . '</li></ul>'
							. '<p>' . esc_html__( 'Aesthetic procedures are only initiated once full oral health has been confirmed, ensuring lasting and stable results.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Step 3', 'estecapelli' ),
						'title'         => __( 'Restoring Missing Teeth', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Where missing teeth are present, implant treatment is carried out prior to the aesthetic procedure. Once the implant has fully integrated with the bone, aesthetic crowns are placed to complete the smile design seamlessly.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Step 4', 'estecapelli' ),
						'title'         => __( 'Gum Aesthetics (Gummy Smile Treatment)', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Where gum levels are asymmetrical or excessively visible, laser gum contouring is performed to achieve a balanced and proportionate gum line. This step plays a critical role in the overall smile design, as even the most refined dental work can fall short without a well-defined and symmetrical gum structure.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Step 5', 'estecapelli' ),
						'title'         => __( 'Teeth Renewal', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The most suitable materials are applied to aesthetically reshape and transform the teeth. The most commonly used options in Hollywood Smile treatment are:', 'estecapelli' ) . '</p>'
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
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Step 6', 'estecapelli' ),
						'title'         => __( 'Teeth Whitening and Colour Matching', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'In the final stage, the shade of all teeth is carefully harmonised to create a consistent and balanced appearance. This ensures the overall result looks natural, cohesive and aesthetically refined across the entire smile.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'faq',
						'eyebrow'       => __( 'Good to Know', 'estecapelli' ),
						'title'         => __( 'Hollywood Smile — FAQ', 'estecapelli' ),
						'lead'          => '',
						'items'         => array(
							array( 'question' => __( 'Who is it suitable for?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Anyone with discoloration, staining, gaps, misalignment or worn/asymmetrical teeth and good general oral health who wants a brighter, balanced smile.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How is a Hollywood Smile done?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Depending on your needs it can combine whitening, porcelain laminate veneers, zirconium crowns and gum contouring — all planned from a digital smile analysis of your facial proportions.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'How long does treatment take?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'Treatment is usually completed within three to seven days.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is it painful?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The procedures are generally painless; local anaesthesia is used where needed.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Is it permanent and natural-looking?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'The restorations are long-lasting and, with regular oral care, last for years. Personalised planning and the right materials give a very natural result.', 'estecapelli' ) . '</p>' ),
							array( 'question' => __( 'Will my teeth be damaged?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'No. When performed by experienced dentists, the treatment does not harm your teeth.', 'estecapelli' ) . '</p>' ),
						),
					),
					array( 'acf_fc_layout' => 'related', 'eyebrow' => __( 'Explore Further', 'estecapelli' ), 'title' => __( 'Related Treatments', 'estecapelli' ), 'count' => 3, 'manual' => array() ),
				),
			),

		);
	}
}
