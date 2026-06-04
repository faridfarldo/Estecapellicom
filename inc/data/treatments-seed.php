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
						'body'          => '<p>' . esc_html__( "At Estecapelli, we offer patented Exosome FUE hair transplantation — an advanced treatment designed to support the regeneration and vitality of hair follicles. The specially formulated solution used in this method is derived from mesenchymal stem cells, containing numerous bioactive components that promote tissue regeneration and follicle longevity.", 'estecapelli' ) . '</p>',
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
						'body'          => '<p>' . esc_html__( "Female hair transplant is a specialised procedure designed to address hair loss and thinning in women, delivering permanent and natural-looking results. Unlike male hair transplants, the technique is carefully adapted to suit women's unique hair structure, growth patterns, and hairline aesthetics. At Estecapelli, every treatment plan begins with a thorough analysis of what is driving your hair loss.", 'estecapelli' ) . '</p>',
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
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Sapphire FUE Recovery Process: What to Expect', 'estecapelli' ),
						'body'          => '<p><strong>' . esc_html__( 'First days and weeks:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Mild redness and swelling are normal and subside quickly.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Days 7–10:', 'estecapelli' ) . '</strong> ' . esc_html__( 'The scab-shedding phase is a natural part of healing.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Months 1–3:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Temporary shock shedding may occur — completely expected.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Months 3–12:', 'estecapelli' ) . '</strong> ' . esc_html__( 'New hair grows in with full density and a natural look, supported by our follow-up team every step of the way.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
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
						'body'          => '<p>' . esc_html__( 'TrichoLab is an advanced AI-powered hair analysis system that examines the hair and scalp through high-resolution trichoscopic imaging. By measuring hair density, follicular distribution and donor capacity with scientific precision, it removes guesswork from hair transplant planning. It is available in only a limited number of clinics in Turkey, and at Estecapelli it sits at the heart of every treatment plan.', 'estecapelli' ) . '</p>',
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
						'body'          => '<p>' . esc_html__( 'Eyebrows are one of the most defining elements of facial expression. Eyebrow transplantation is a precise microsurgical procedure that restores brows that have thinned, fallen out or lost their shape. It follows principles similar to hair transplantation but demands far greater precision: single follicles are used and implanted with the DHI method to achieve a natural angle, direction and density.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'How is Eyebrow Transplantation Performed?', 'estecapelli' ),
						'lead'          => __( 'Designed around your facial proportions for a natural, lasting result.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'sparkles',     'time' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Eyebrow Design', 'estecapelli' ),     'body' => __( 'A natural brow shape is created from your facial proportions, muscle structure and personal preferences.', 'estecapelli' ) ),
							array( 'icon' => 'target',       'time' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Graft Harvesting', 'estecapelli' ),   'body' => __( 'Single follicles are extracted from the nape with a specialised micromotor — essential for a natural brow.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'DHI Implantation', 'estecapelli' ),   'body' => __( 'With the DHI method, channel opening and implantation happen together for precise angle and direction control.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'Stage 4', 'estecapelli' ), 'title' => __( 'Procedure Duration', 'estecapelli' ), 'body' => __( 'The procedure takes roughly 2–3 hours under local anaesthesia and is very comfortable.', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After the Procedure', 'estecapelli' ),
						'title'         => __( 'What is the Recovery Process Like?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'On the first day, mild redness and sensitivity are normal. Within about two weeks, light scabbing clears. As with hair transplantation, shock shedding may occur before the permanent brow hairs grow in with a natural shape and density. The DHI method is the most suitable technique for eyebrow transplantation thanks to its precise control.', 'estecapelli' ) . '</p>',
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
						'title'         => __( 'How Does a Beard Transplant Work?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The beard is one of the most defining aesthetic features for men. A beard transplant restores fullness by transplanting hair follicles into sparse, uneven or patchy areas. Healthy grafts are harvested from a safe donor area and implanted into the beard region at the correct angle and direction, delivering a result that is both permanent and completely natural.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'right',
						'cta'           => array( 'label' => '', 'url' => '' ),
					),

					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'Stages of Beard Transplantation', 'estecapelli' ),
						'lead'          => __( 'Careful harvesting and precise placement for a natural beard line.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'target',       'time' => __( 'Stage 1', 'estecapelli' ), 'title' => __( 'Graft Harvesting', 'estecapelli' ),  'body' => __( 'Follicles are harvested with the FUE method from the safe donor areas at the back and sides of the head.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( 'Stage 2', 'estecapelli' ), 'title' => __( 'Channel Opening', 'estecapelli' ),   'body' => __( 'The direction, depth and angle of the channels are carefully set — the key to a natural-looking beard.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Stage 3', 'estecapelli' ), 'title' => __( 'Graft Implantation', 'estecapelli' ), 'body' => __( 'The harvested grafts are placed one by one into the prepared channels at the correct angle for seamless density.', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why Choose It', 'estecapelli' ),
						'title'         => __( 'Advantages of Beard Transplantation', 'estecapelli' ),
						'body'          => __( 'Permanent, natural results that grow in alignment with your beard:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'check-circle', 'label' => __( 'Permanent and natural-looking results', 'estecapelli' ) ),
							array( 'icon' => 'hair',         'label' => __( 'Fills sparse, patchy or uneven areas', 'estecapelli' ) ),
							array( 'icon' => 'face',         'label' => __( 'Grows in alignment with your facial features', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Uses your own follicles for a seamless blend', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Minimal, temporary side effects', 'estecapelli' ) ),
						),
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
						'body'          => '<p>' . esc_html__( 'Rhinoplasty reshapes the nose to improve its form, size and function. It can be performed for aesthetic reasons, to correct breathing problems, or both — always planned around facial harmony and the individual’s anatomy.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Techniques', 'estecapelli' ),
						'title'         => __( 'Types of Rhinoplasty', 'estecapelli' ),
						'body'          => __( 'At Estecapelli the right technique is chosen for your anatomy and goals:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'medical-plus', 'label' => __( 'Open Rhinoplasty — full access via a small incision under the nostrils', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Closed Rhinoplasty — all incisions inside the nose, no visible scars', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Tipplasty — refinement of the nasal tip only', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'Septum Deviation Surgery — corrects the midline cartilage/bone for better breathing', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'label' => __( 'Non-Surgical Rhinoplasty — fillers soften a bump or lift the tip; temporary, lasting around 12–18 months', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery Timeline', 'estecapelli' ),
						'lead'          => __( 'What to expect after your rhinoplasty.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'time' => __( 'First 24h', 'estecapelli' ), 'title' => __( 'Mild Swelling', 'estecapelli' ), 'body' => __( 'Mild pain and swelling may occur and are easily managed.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'Week 1', 'estecapelli' ),   'title' => __( 'Splint Removal', 'estecapelli' ), 'body' => __( 'Most bruising begins to fade and the splint is typically removed.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( 'Months', 'estecapelli' ),   'title' => __( 'Final Shape', 'estecapelli' ),   'body' => __( 'Swelling subsides gradually and the refined, natural shape settles over the following months.', 'estecapelli' ) ),
						),
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
						'body'          => '<p>' . esc_html__( 'BBL enhances the shape and volume of the buttocks and improves overall body proportion. The most popular approach uses the patient’s own fat in two steps: liposuction to harvest excess fat, then purification and re-injection into the buttocks for a natural, lasting result. Silicone implants are an alternative for patients without enough fat.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why Choose It', 'estecapelli' ),
						'title'         => __( 'What BBL Can Improve', 'estecapelli' ),
						'body'          => __( 'BBL addresses a range of shape and proportion concerns:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'target',      'label' => __( 'Flat or shapeless buttocks', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',    'label' => __( 'Lack of waist-to-hip definition', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart', 'label' => __( 'Volume loss after weight change', 'estecapelli' ) ),
							array( 'icon' => 'star',        'label' => __( 'A more balanced, curved silhouette', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery & Sitting Rules', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Avoid sitting directly on the buttocks for the first 2–3 weeks; pressure should be transferred to the thighs with a special cushion. Transferred fat cells that survive become permanent and last for years, barring major weight changes.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
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
						'body'          => '<p>' . esc_html__( 'Abdominoplasty is a body-contouring procedure that corrects sagging, looseness and muscle separation in the abdominal area, removing excess skin and fat to create a firmer, flatter profile.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Techniques', 'estecapelli' ),
						'title'         => __( 'Types of Tummy Tuck', 'estecapelli' ),
						'body'          => __( 'The right approach depends on the degree of sagging and muscle separation:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'medical-plus', 'label' => __( 'Full Abdominoplasty — for advanced sagging and muscle separation', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Mini Tummy Tuck — for mild sagging below the navel', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Liposuction + Abdominoplasty — when both excess fat and skin are present', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Mommy Makeover — combines procedures to restore the body after childbirth', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery Process', 'estecapelli' ),
						'lead'          => __( 'A general guide — recovery varies from person to person.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'time' => __( 'Day 1', 'estecapelli' ),     'title' => __( 'Tightness', 'estecapelli' ),       'body' => __( 'A feeling of tightness is normal; one night in hospital is typical.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'Weeks 2–6', 'estecapelli' ), 'title' => __( 'Back to Routine', 'estecapelli' ), 'body' => __( 'Swelling decreases gradually; light activity is reintroduced with a corset worn as advised.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( '6–12 Months', 'estecapelli' ),'title' => __( 'Final Result', 'estecapelli' ),   'body' => __( 'The abdominal contour reaches its most refined, natural state and scars continue to fade.', 'estecapelli' ) ),
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
						'body'          => '<p>' . esc_html__( 'Liposuction reduces localized fat deposits that resist diet and exercise, reshaping the body’s contours. It can be applied to many areas where stubborn fat accumulates.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Techniques', 'estecapelli' ),
						'title'         => __( 'Types of Liposuction', 'estecapelli' ),
						'body'          => __( 'The technique is chosen for your goals and anatomy:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'atom',         'label' => __( 'Vaser Liposuction — ultrasound energy for precise, gentle contouring', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Laser Liposuction — breaks down fat while tightening the skin', 'estecapelli' ) ),
							array( 'icon' => 'hands-heart',  'label' => __( 'Tumescent Liposuction — minimizes bleeding and discomfort', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'PAL (Power-Assisted) — vibration-assisted, efficient fat removal', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Classic Liposuction — conventional manual technique', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery Process', 'estecapelli' ),
						'lead'          => __( 'Comfort is prioritised with careful post-operative monitoring.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'time' => __( 'First Days', 'estecapelli' ), 'title' => __( 'Compression Garment', 'estecapelli' ), 'body' => __( 'Mild bruising and swelling may occur; a compression garment is started.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'Weeks 2–3', 'estecapelli' ),  'title' => __( 'Back to Activity', 'estecapelli' ),    'body' => __( 'Swelling diminishes and a gradual return to sport is possible.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( '3–6 Months', 'estecapelli' ), 'title' => __( 'Final Contours', 'estecapelli' ),     'body' => __( 'Body contours settle and final results are revealed.', 'estecapelli' ) ),
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
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'What is a Face & Neck Lift?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'A facelift lifts sagging facial tissues, removes excess skin and restores definition; a neck lift corrects sagging, banding and a double chin. Together they rejuvenate the lower face and neck for a natural, refreshed look.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Techniques', 'estecapelli' ),
						'title'         => __( 'Types of Face Lift', 'estecapelli' ),
						'body'          => __( 'Techniques are tailored to your anatomy, age and expectations:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'sparkles',     'label' => __( 'Mini Facelift — minimally invasive, for mild to moderate sagging', 'estecapelli' ) ),
							array( 'icon' => 'face',         'label' => __( 'Full Facelift — comprehensive rejuvenation for moderate to advanced aging', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Deep Plane Facelift — advanced lift for moderate to severe aging', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Neck Lift — firms loose skin, fat and muscle banding in the neck', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery Process', 'estecapelli' ),
						'lead'          => __( 'A general guide; recovery varies per person.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'time' => __( 'First Days', 'estecapelli' ), 'title' => __( 'Swelling', 'estecapelli' ),       'body' => __( 'Mild pain, swelling and bruising are normal; a one-night hospital stay may be needed.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'Weeks 2–3', 'estecapelli' ), 'title' => __( 'Resume Life', 'estecapelli' ),    'body' => __( 'Swelling and bruising fade and most normal activities resume.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( '1–3 Months', 'estecapelli' ),'title' => __( 'Final Contour', 'estecapelli' ),  'body' => __( 'Final results become visible and facial contours are fully defined.', 'estecapelli' ) ),
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
						'eyebrow'       => __( 'The Procedure', 'estecapelli' ),
						'title'         => __( 'What is Bariatric Surgery?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Bariatric surgery is a general term for procedures used to treat severe obesity, promoting significant, sustainable weight loss. The right method is chosen based on your BMI, health and goals.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Methods', 'estecapelli' ),
						'title'         => __( 'Bariatric Methods', 'estecapelli' ),
						'body'          => __( 'The most common approaches include:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'medical-plus', 'label' => __( 'Gastric Sleeve (Sleeve Gastrectomy)', 'estecapelli' ) ),
							array( 'icon' => 'target',       'label' => __( 'Gastric Bypass', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'label' => __( 'Gastric Balloon (endoscopic, non-surgical)', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Gastric Band', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'Non-Surgical Option', 'estecapelli' ),
						'title'         => __( 'What is a Gastric Balloon?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'The gastric balloon is an inflatable device placed in the stomach via endoscopy — no surgery required. It is suitable for people with a BMI of 30–35, or higher-BMI patients preparing for surgery, helping reduce food intake and kick-start weight loss.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'left', 'cta' => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact' ) ),
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
						'title'         => __( 'What is Gynecomastia?', 'estecapelli' ),
						'body'          => '<p>' . esc_html__( 'Gynecomastia is a condition in which male breast tissue grows beyond its normal size, giving the chest a fuller appearance. It often results from a hormonal imbalance between estrogen and testosterone, among other causes.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'The Approach', 'estecapelli' ),
						'title'         => __( 'How Surgery is Performed', 'estecapelli' ),
						'body'          => __( 'Performed under general anaesthesia, the technique depends on the case:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'target',       'label' => __( 'Excess fatty tissue only → liposuction / Vaser liposuction', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'label' => __( 'Glandular tissue with skin sagging → tissue removal + skin reshaping', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Natural, masculine chest contour as the goal', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Recovery Process', 'estecapelli' ),
						'lead'          => __( 'Generally comfortable and well tolerated.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'shield-check', 'time' => __( 'First Week', 'estecapelli' ), 'title' => __( 'Compression Vest', 'estecapelli' ), 'body' => __( 'Swelling and sensitivity are expected; a compression garment is essential for proper healing.', 'estecapelli' ) ),
							array( 'icon' => 'calendar',     'time' => __( 'Weeks 2–3', 'estecapelli' ),  'title' => __( 'Back to Routine', 'estecapelli' ),  'body' => __( 'Swelling largely subsides and light daily activity resumes.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( '1 Month+', 'estecapelli' ),   'title' => __( 'Defined Chest', 'estecapelli' ),    'body' => __( 'The chest takes on a natural, defined appearance; chest-intensive exercise gradually returns.', 'estecapelli' ) ),
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
						'body'          => '<p>' . esc_html__( 'A dental implant is a modern treatment that restores natural tooth function and appearance by placing biocompatible titanium roots into the jawbone. Tooth loss affects more than aesthetics — it impairs chewing and can impact the digestive system and jaw health — which implants address permanently.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'How is a Dental Implant Applied?', 'estecapelli' ),
						'lead'          => __( 'A structured process for safe, long-lasting, natural-looking results.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'target',       'time' => __( 'Step 1', 'estecapelli' ), 'title' => __( 'Examination & Imaging', 'estecapelli' ), 'body' => __( 'The jawbone is analysed with panoramic X-rays and digital tomography to assess bone density and implant size.', 'estecapelli' ) ),
							array( 'icon' => 'clipboard',    'time' => __( 'Step 2', 'estecapelli' ), 'title' => __( 'Treatment Planning', 'estecapelli' ),   'body' => __( 'A personalised plan is prepared based on the number of missing teeth, jaw structure and oral health.', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'time' => __( 'Step 3', 'estecapelli' ), 'title' => __( 'Implant Placement', 'estecapelli' ),    'body' => __( 'Under local anaesthesia the titanium root is precisely placed into the jawbone.', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'time' => __( 'Step 4', 'estecapelli' ), 'title' => __( 'Osseointegration', 'estecapelli' ),     'body' => __( 'A healing period of about 2–3 months lets the implant fully integrate with the bone.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Step 5', 'estecapelli' ), 'title' => __( 'Prosthetic Tooth', 'estecapelli' ),     'body' => __( 'A custom-made prosthetic tooth is attached for a natural look and full chewing function.', 'estecapelli' ) ),
						),
					),
					array(
						'acf_fc_layout' => 'candidate',
						'eyebrow'       => __( 'Why Choose It', 'estecapelli' ),
						'title'         => __( 'Advantages of Dental Implants', 'estecapelli' ),
						'body'          => __( 'Implants are the gold standard for replacing missing teeth:', 'estecapelli' ),
						'image'         => '',
						'items'         => array(
							array( 'icon' => 'tooth',        'label' => __( 'Natural tooth appearance and feel', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'label' => __( 'Maximum chewing performance', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'label' => __( 'No alteration of adjacent healthy teeth', 'estecapelli' ) ),
							array( 'icon' => 'star',         'label' => __( 'Long-lasting, durable solution', 'estecapelli' ) ),
						),
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
						'body'          => '<p>' . esc_html__( 'Hollywood Smile is a comprehensive smile-design treatment that harmonises the relationship between teeth, gums and lips to create a symmetrical, bright and natural-looking smile. It is an ideal solution for discoloration, staining, gaps, misalignment and worn or asymmetrical teeth.', 'estecapelli' ) . '</p>',
						'image'         => '', 'image_position' => 'right', 'cta' => array( 'label' => '', 'url' => '' ),
					),
					array(
						'acf_fc_layout' => 'steps',
						'eyebrow'       => __( 'The Process', 'estecapelli' ),
						'title'         => __( 'How is a Hollywood Smile Applied?', 'estecapelli' ),
						'lead'          => __( 'A highly personalised treatment tailored to each patient.', 'estecapelli' ),
						'items'         => array(
							array( 'icon' => 'target',       'time' => __( 'Step 1', 'estecapelli' ), 'title' => __( 'Digital Smile Analysis', 'estecapelli' ), 'body' => __( 'Facial shape, lip structure, gum line and tooth form are analysed to design a fully personalised smile.', 'estecapelli' ) ),
							array( 'icon' => 'shield-check', 'time' => __( 'Step 2', 'estecapelli' ), 'title' => __( 'Oral Health Preparation', 'estecapelli' ), 'body' => __( 'A healthy oral foundation is established before any aesthetic work begins.', 'estecapelli' ) ),
							array( 'icon' => 'medical-plus', 'time' => __( 'Step 3', 'estecapelli' ), 'title' => __( 'Restoring Missing Teeth', 'estecapelli' ), 'body' => __( 'Where teeth are missing, implant treatment is completed first.', 'estecapelli' ) ),
							array( 'icon' => 'sparkles',     'time' => __( 'Step 4', 'estecapelli' ), 'title' => __( 'Gum Aesthetics', 'estecapelli' ),         'body' => __( 'Laser gum contouring balances asymmetrical or gummy gum lines.', 'estecapelli' ) ),
							array( 'icon' => 'check-circle', 'time' => __( 'Step 5', 'estecapelli' ), 'title' => __( 'Teeth Renewal & Whitening', 'estecapelli' ), 'body' => __( 'Teeth are aesthetically reshaped and the final shade is harmonised for a consistent, balanced result.', 'estecapelli' ) ),
						),
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
