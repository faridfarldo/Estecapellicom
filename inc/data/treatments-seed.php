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
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
						'cta'           => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
							array( 'label' => __( 'Hormonal changes (pregnancy, postpartum, menopause)', 'estecapelli' ) ),
							array( 'label' => __( 'Genetic predisposition and family history', 'estecapelli' ) ),
							array( 'label' => __( 'Stress and emotional factors', 'estecapelli' ) ),
							array( 'label' => __( 'Nutritional deficiencies (iron, biotin, zinc, B-vitamins)', 'estecapelli' ) ),
							array( 'label' => __( 'Environmental factors (pollution, UV, chemicals)', 'estecapelli' ) ),
							array( 'label' => __( 'Health conditions (thyroid disorders, PCOS, alopecia)', 'estecapelli' ) ),
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
							array( 'label' => __( 'Natural and permanent results', 'estecapelli' ) ),
							array( 'label' => __( 'Increased hair density in thinning areas', 'estecapelli' ) ),
							array( 'label' => __( 'Minimum scarring, maximum aesthetics', 'estecapelli' ) ),
							array( 'label' => __( 'Fast recovery, back to daily life within days', 'estecapelli' ) ),
							array( 'label' => __( 'Shaving-free with the DHI technique', 'estecapelli' ) ),
							array( 'label' => __( 'Personalised hairline design', 'estecapelli' ) ),
							array( 'label' => __( 'Rejuvenating effect — a fresher, younger look', 'estecapelli' ) ),
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
						'cta'           => array( 'label' => __( 'Speak to a Consultant', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
							array( 'label' => __( 'Strengthens hair follicles and promotes new growth', 'estecapelli' ) ),
							array( 'label' => __( 'Reduces hair loss with no downtime', 'estecapelli' ) ),
							array( 'label' => __( 'Personalised vitamin and mineral cocktail', 'estecapelli' ) ),
							array( 'label' => __( 'Painless, comfortable sessions', 'estecapelli' ) ),
							array( 'label' => __( 'Compatible with hair-transplant after-care', 'estecapelli' ) ),
							array( 'label' => __( 'Quick visible results over a few sessions', 'estecapelli' ) ),
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
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
							array( 'label' => __( 'Permanent and reliable, natural-looking results', 'estecapelli' ) ),
							array( 'label' => __( 'High precision with sapphire-tipped blades', 'estecapelli' ) ),
							array( 'label' => __( 'Less tissue trauma and faster healing', 'estecapelli' ) ),
							array( 'label' => __( 'High density and a natural appearance', 'estecapelli' ) ),
							array( 'label' => __( 'Gentle, suitable for sensitive scalps', 'estecapelli' ) ),
							array( 'label' => __( 'Reduced risk of infection thanks to sapphire’s antibacterial properties', 'estecapelli' ) ),
							array( 'label' => __( 'Personalised planning with TrichoLab scientific analysis', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'Sapphire FUE Recovery Process: What to Expect', 'estecapelli' ),
						'body'          => '<p><strong>' . esc_html__( 'First days and weeks:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Mild redness and swelling are normal and subside quickly.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Days 7–10:', 'estecapelli' ) . '</strong> ' . esc_html__( 'The scab-shedding phase is a natural part of healing.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Months 1–3:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Temporary shock shedding may occur — completely expected.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Months 3–12:', 'estecapelli' ) . '</strong> ' . esc_html__( 'New hair grows in with full density and a natural look, supported by our follow-up team every step of the way.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
							array( 'label' => __( 'Permanent and reliable results from meticulous planning', 'estecapelli' ) ),
							array( 'label' => __( 'High precision — direct implantation, no pre-made channels', 'estecapelli' ) ),
							array( 'label' => __( 'Less trauma and faster healing', 'estecapelli' ) ),
							array( 'label' => __( 'High density and a natural appearance', 'estecapelli' ) ),
							array( 'label' => __( 'Suitable for sensitive skin', 'estecapelli' ) ),
							array( 'label' => __( 'Reduced risk of infection with no open incisions', 'estecapelli' ) ),
							array( 'label' => __( 'Personalised planning with scientific TrichoLab analysis', 'estecapelli' ) ),
						),
					),

					array(
						'acf_fc_layout' => 'intro',
						'eyebrow'       => __( 'After Surgery', 'estecapelli' ),
						'title'         => __( 'DHI Recovery Process: What to Expect', 'estecapelli' ),
						'body'          => '<p><strong>' . esc_html__( 'First days and weeks:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Mild redness and swelling are normal and resolve quickly.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Scab shedding:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Small scabs naturally shed as part of healing.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Months 1–4:', 'estecapelli' ) . '</strong> ' . esc_html__( 'Temporary shock shedding occurs as follicles enter a new growth cycle.', 'estecapelli' ) . '</p><p><strong>' . esc_html__( 'Months 3–12:', 'estecapelli' ) . '</strong> ' . esc_html__( 'New hair grows in with full, natural density, supported by our follow-up team.', 'estecapelli' ) . '</p>',
						'image'         => '',
						'image_position' => 'left',
						'cta'           => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
							array( 'label' => __( 'Protects graft vitality outside the body', 'estecapelli' ) ),
							array( 'label' => __( 'Higher graft survival and retention', 'estecapelli' ) ),
							array( 'label' => __( 'Nourishes follicles with vitamins, minerals and ATP', 'estecapelli' ) ),
							array( 'label' => __( 'Accelerates the healing process', 'estecapelli' ) ),
							array( 'label' => __( 'Combines with both FUE and DHI procedures', 'estecapelli' ) ),
							array( 'label' => __( 'Ideal for weak follicles, fine hair or limited donor areas', 'estecapelli' ) ),
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
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
							array( 'label' => __( 'Scientific assessment of donor area capacity — no overharvesting', 'estecapelli' ) ),
							array( 'label' => __( 'Accurate graft calculation, eliminating under- and over-planning', 'estecapelli' ) ),
							array( 'label' => __( 'Natural, symmetrical design based on your existing hair', 'estecapelli' ) ),
							array( 'label' => __( 'Long-term protection of the donor area', 'estecapelli' ) ),
							array( 'label' => __( 'Available in only a limited number of clinics in Turkey', 'estecapelli' ) ),
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
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
						'cta'           => array( 'label' => __( 'Schedule Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
						'cta_primary'   => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => home_url( '/en/contact/' ) ),
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
							array( 'label' => __( 'Permanent and natural-looking results', 'estecapelli' ) ),
							array( 'label' => __( 'Fills sparse, patchy or uneven areas', 'estecapelli' ) ),
							array( 'label' => __( 'Grows in alignment with your facial features', 'estecapelli' ) ),
							array( 'label' => __( 'Uses your own follicles for a seamless blend', 'estecapelli' ) ),
							array( 'label' => __( 'Minimal, temporary side effects', 'estecapelli' ) ),
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
							array( 'question' => __( 'What side effects should I expect?', 'estecapelli' ), 'answer' => '<p>' . esc_html__( 'As with any procedure there may be minor, temporary redness, swelling or sensitivity, which settle quickly.', 'estecapelli' ) . '</p>' ),
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

		);
	}
}
