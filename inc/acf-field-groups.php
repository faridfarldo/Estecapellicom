<?php
/**
 * ACF field groups, registered via PHP so they live in version control.
 *
 * The `treatment_page_builder` group exposes a Flexible Content field whose
 * layouts map 1:1 to renderers in /template-parts/sections/. To add a new
 * section type:
 *   1. Add a layout block here.
 *   2. Add a matching file at /template-parts/sections/{layout_name}.php.
 *   3. The engine (single-treatment.php) wires them up automatically.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'estecapelli_register_acf_field_groups' );
function estecapelli_register_acf_field_groups() {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Homepage content settings page — holds the option-scoped homepage fields.
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => __( 'Homepage Content', 'estecapelli' ),
				'menu_title' => __( 'Homepage Content', 'estecapelli' ),
				'menu_slug'  => 'estecapelli-homepage',
				'capability' => 'edit_theme_options',
				'position'   => 59,
				'icon_url'   => 'dashicons-admin-home',
				'redirect'   => false,
			)
		);
	}

	// Icons available in estecapelli_icon() — exposed as ACF dropdowns.
	$icon_choices = array(
		''             => __( '— None —', 'estecapelli' ),
		'star'         => 'star',
		'sparkles'     => 'sparkles',
		'target'       => 'target',
		'atom'         => 'atom',
		'dna'          => 'dna',
		'shield-check' => 'shield-check',
		'check-circle' => 'check-circle',
		'medical-plus' => 'medical-plus',
		'hair'         => 'hair',
		'face'         => 'face',
		'tooth'        => 'tooth',
		'bed'          => 'bed',
		'car'          => 'car',
		'plane'        => 'plane',
		'clipboard'    => 'clipboard',
		'image'        => 'image',
		'hands-heart'  => 'hands-heart',
		'headset'      => 'headset',
		'building'     => 'building',
		'wifi'         => 'wifi',
		'utensils'     => 'utensils',
		'concierge'    => 'concierge',
		'calendar'     => 'calendar',
		'tag'          => 'tag',
		'book-open'    => 'book-open',
		'phone'        => 'phone',
		'mail'         => 'mail',
		'map-pin'      => 'map-pin',
		'globe'        => 'globe',
		'languages'    => 'languages',
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_treatment_page_builder',
			'title'                 => __( 'Treatment Page Builder', 'estecapelli' ),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'treatment',
					),
				),
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'fields'                => array(
				array(
					'key'        => 'field_treatment_sections',
					'label'      => __( 'Page Sections', 'estecapelli' ),
					'name'       => 'page_sections',
					'type'       => 'flexible_content',
					'button_label' => __( '+ Add Section', 'estecapelli' ),
					'min'        => 0,
					'layouts'    => array(

						// ============================================================ Hero
						'hero' => array(
							'key'        => 'layout_hero',
							'name'       => 'hero',
							'label'      => __( 'Hero — title, lead, CTAs, media', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array(
									'key'   => 'field_hero_eyebrow',
									'label' => __( 'Eyebrow', 'estecapelli' ),
									'name'  => 'eyebrow',
									'type'  => 'text',
								),
								array(
									'key'      => 'field_hero_title',
									'label'    => __( 'Title', 'estecapelli' ),
									'name'     => 'title',
									'type'     => 'text',
									'required' => 1,
								),
								array(
									'key'   => 'field_hero_lead',
									'label' => __( 'Lead paragraph', 'estecapelli' ),
									'name'  => 'lead',
									'type'  => 'textarea',
									'rows'  => 3,
								),
								array(
									'key'           => 'field_hero_cta_primary',
									'label'         => __( 'Primary CTA', 'estecapelli' ),
									'name'          => 'cta_primary',
									'type'          => 'group',
									'layout'        => 'block',
									'sub_fields'    => array(
										array( 'key' => 'field_hero_cta_p_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text' ),
										array( 'key' => 'field_hero_cta_p_url',   'label' => __( 'URL',   'estecapelli' ), 'name' => 'url',   'type' => 'url'  ),
									),
								),
								array(
									'key'        => 'field_hero_cta_secondary',
									'label'      => __( 'Secondary CTA (optional)', 'estecapelli' ),
									'name'       => 'cta_secondary',
									'type'       => 'group',
									'layout'     => 'block',
									'sub_fields' => array(
										array( 'key' => 'field_hero_cta_s_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text' ),
										array( 'key' => 'field_hero_cta_s_url',   'label' => __( 'URL',   'estecapelli' ), 'name' => 'url',   'type' => 'url'  ),
									),
								),
								array(
									'key'           => 'field_hero_media_type',
									'label'         => __( 'Media type', 'estecapelli' ),
									'name'          => 'media_type',
									'type'          => 'radio',
									'layout'        => 'horizontal',
									'choices'       => array(
										'image' => __( 'Image', 'estecapelli' ),
										'video' => __( 'YouTube video', 'estecapelli' ),
										'none'  => __( 'None', 'estecapelli' ),
									),
									'default_value' => 'image',
								),
								array(
									'key'               => 'field_hero_image',
									'label'             => __( 'Image', 'estecapelli' ),
									'name'              => 'image',
									'type'              => 'image',
									'return_format'     => 'array',
									'preview_size'      => 'medium',
									'conditional_logic' => array( array( array( 'field' => 'field_hero_media_type', 'operator' => '==', 'value' => 'image' ) ) ),
								),
								array(
									'key'               => 'field_hero_video_id',
									'label'             => __( 'YouTube video link', 'estecapelli' ),
									'name'              => 'video_id',
									'type'              => 'text',
									'instructions'      => __( 'Paste the full YouTube link (e.g. https://youtu.be/dQw4w9WgXcQ) or just the video ID.', 'estecapelli' ),
									'conditional_logic' => array( array( array( 'field' => 'field_hero_media_type', 'operator' => '==', 'value' => 'video' ) ) ),
								),
							),
						),

						// ============================================================ Quick stats
						'quick_stats' => array(
							'key'        => 'layout_quick_stats',
							'name'       => 'quick_stats',
							'label'      => __( 'Quick Stats — small strip of icon + value + label cells', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array(
									'key'   => 'field_stats_eyebrow',
									'label' => __( 'Eyebrow (optional)', 'estecapelli' ),
									'name'  => 'eyebrow',
									'type'  => 'text',
								),
								array(
									'key'        => 'field_stats_items',
									'label'      => __( 'Stats', 'estecapelli' ),
									'name'       => 'stats',
									'type'       => 'repeater',
									'min'        => 1,
									'max'        => 6,
									'button_label' => __( '+ Add Stat', 'estecapelli' ),
									'layout'     => 'table',
									'sub_fields' => array(
										array( 'key' => 'field_stat_icon',  'label' => __( 'Icon', 'estecapelli' ),  'name' => 'icon',  'type' => 'select', 'choices' => $icon_choices, 'allow_null' => 1 ),
										array( 'key' => 'field_stat_value', 'label' => __( 'Value', 'estecapelli' ), 'name' => 'value', 'type' => 'text', 'required' => 1 ),
										array( 'key' => 'field_stat_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'required' => 1 ),
									),
								),
							),
						),

						// ============================================================ Steps timeline
						'steps' => array(
							'key'        => 'layout_steps',
							'name'       => 'steps',
							'label'      => __( 'Steps Timeline — numbered procedure flow', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_steps_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_steps_title',   'label' => __( 'Title',   'estecapelli' ), 'name' => 'title',   'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_steps_lead',    'label' => __( 'Lead',    'estecapelli' ), 'name' => 'lead',    'type' => 'textarea', 'rows' => 2 ),
								array(
									'key'          => 'field_steps_items',
									'label'        => __( 'Steps', 'estecapelli' ),
									'name'         => 'items',
									'type'         => 'repeater',
									'min'          => 1,
									'max'          => 8,
									'button_label' => __( '+ Add Step', 'estecapelli' ),
									'layout'       => 'block',
									'sub_fields'   => array(
										array( 'key' => 'field_step_icon',  'label' => __( 'Icon', 'estecapelli' ),  'name' => 'icon',  'type' => 'select', 'choices' => $icon_choices, 'allow_null' => 1 ),
										array( 'key' => 'field_step_time',  'label' => __( 'Time / phase', 'estecapelli' ), 'name' => 'time', 'type' => 'text' ),
										array( 'key' => 'field_step_title', 'label' => __( 'Title', 'estecapelli' ), 'name' => 'title', 'type' => 'text', 'required' => 1 ),
										array( 'key' => 'field_step_body',  'label' => __( 'Body',  'estecapelli' ), 'name' => 'body',  'type' => 'textarea', 'rows' => 3 ),
									),
								),
							),
						),

						// ============================================================ Step Book — booklet walk-through
						'stepbook' => array(
							'key'        => 'layout_stepbook',
							'name'       => 'stepbook',
							'label'      => __( 'Step Book — booklet walk-through (rich, one step per page)', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_book_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_book_title',   'label' => __( 'Title',   'estecapelli' ), 'name' => 'title',   'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_book_lead',    'label' => __( 'Lead',    'estecapelli' ), 'name' => 'lead',    'type' => 'textarea', 'rows' => 2 ),
								array(
									'key'          => 'field_book_items',
									'label'        => __( 'Steps', 'estecapelli' ),
									'name'         => 'items',
									'type'         => 'repeater',
									'min'          => 1,
									'max'          => 12,
									'button_label' => __( '+ Add Step', 'estecapelli' ),
									'layout'       => 'block',
									'sub_fields'   => array(
										array( 'key' => 'field_book_item_icon',    'label' => __( 'Icon', 'estecapelli' ), 'name' => 'icon', 'type' => 'select', 'choices' => $icon_choices, 'allow_null' => 1 ),
										array( 'key' => 'field_book_item_eyebrow', 'label' => __( 'Eyebrow / label (e.g. Step 1)', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
										array( 'key' => 'field_book_item_title',   'label' => __( 'Title', 'estecapelli' ), 'name' => 'title', 'type' => 'text', 'required' => 1 ),
										array( 'key' => 'field_book_item_body',    'label' => __( 'Body (HTML allowed)', 'estecapelli' ), 'name' => 'body', 'type' => 'wysiwyg', 'tabs' => 'all', 'media_upload' => 0 ),
										array( 'key' => 'field_book_item_image',   'label' => __( 'Step image (optional)', 'estecapelli' ), 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'instructions' => __( 'Shown under the step text. Recommended ratio 16:10.', 'estecapelli' ) ),
										array( 'key' => 'field_book_item_video',   'label' => __( 'Step video URL (optional)', 'estecapelli' ), 'name' => 'video_url', 'type' => 'url', 'instructions' => __( 'YouTube URL. Takes priority over the image when both are set.', 'estecapelli' ) ),
									),
								),
							),
						),

						// ============================================================ Candidate checklist
						'candidate' => array(
							'key'        => 'layout_candidate',
							'name'       => 'candidate',
							'label'      => __( 'Candidate — "Who is it for" checklist', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_cand_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_cand_title',   'label' => __( 'Title',   'estecapelli' ), 'name' => 'title',   'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_cand_body',    'label' => __( 'Intro paragraph', 'estecapelli' ), 'name' => 'body', 'type' => 'textarea', 'rows' => 3 ),
								array( 'key' => 'field_cand_footer', 'label' => __( 'Closing paragraph (optional, after the list)', 'estecapelli' ), 'name' => 'footer', 'type' => 'textarea', 'rows' => 3 ),
								array(
									'key'           => 'field_cand_image',
									'label'         => __( 'Side image (optional)', 'estecapelli' ),
									'name'          => 'image',
									'type'          => 'image',
									'return_format' => 'array',
									'preview_size'  => 'medium',
								),
								array(
									'key'          => 'field_cand_items',
									'label'        => __( 'Checklist items', 'estecapelli' ),
									'name'         => 'items',
									'type'         => 'repeater',
									'min'          => 1,
									'max'          => 10,
									'button_label' => __( '+ Add Criterion', 'estecapelli' ),
									'layout'       => 'table',
									'sub_fields'   => array(
										array( 'key' => 'field_cand_item_icon',  'label' => __( 'Icon', 'estecapelli' ),  'name' => 'icon',  'type' => 'select', 'choices' => $icon_choices, 'allow_null' => 1 ),
										array( 'key' => 'field_cand_item_label', 'label' => __( 'Criterion', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'required' => 1 ),
									),
								),
							),
						),

						// ============================================================ Before / After gallery
						'gallery' => array(
							'key'        => 'layout_gallery',
							'name'       => 'gallery',
							'label'      => __( 'Before & After — image pair gallery', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_gal_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_gal_title',   'label' => __( 'Title',   'estecapelli' ), 'name' => 'title',   'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_gal_lead',    'label' => __( 'Lead',    'estecapelli' ), 'name' => 'lead',    'type' => 'textarea', 'rows' => 2 ),
								array(
									'key'          => 'field_gal_items',
									'label'        => __( 'Before / After composites', 'estecapelli' ),
									'instructions' => __( 'Upload a single image where the before/after composition is already prepared. Recommended ratio: 16:10 (e.g. 1600×1000 px).', 'estecapelli' ),
									'name'         => 'items',
									'type'         => 'repeater',
									'min'          => 1,
									'max'          => 0, // 0 = unlimited before/after composites
									'button_label' => __( '+ Add Image', 'estecapelli' ),
									'layout'       => 'block',
									'sub_fields'   => array(
										array( 'key' => 'field_gal_image',   'label' => __( 'Composite image', 'estecapelli' ), 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'required' => 1 ),
										array( 'key' => 'field_gal_caption', 'label' => __( 'Caption / months', 'estecapelli' ), 'name' => 'caption', 'type' => 'text' ),
										array( 'key' => 'field_gal_grafts',  'label' => __( 'Grafts (optional)', 'estecapelli' ), 'name' => 'grafts',  'type' => 'text' ),
									),
								),
								array(
									'key'        => 'field_gal_cta',
									'label'      => __( 'Gallery CTA (optional)', 'estecapelli' ),
									'name'       => 'cta',
									'type'       => 'group',
									'layout'     => 'block',
									'sub_fields' => array(
										array( 'key' => 'field_gal_cta_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text' ),
										array( 'key' => 'field_gal_cta_url',   'label' => __( 'URL',   'estecapelli' ), 'name' => 'url',   'type' => 'url'  ),
									),
								),
							),
						),

						// ============================================================ FAQ accordion
						'faq' => array(
							'key'        => 'layout_faq',
							'name'       => 'faq',
							'label'      => __( 'FAQ — accordion of questions', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_faq_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_faq_title',   'label' => __( 'Title',   'estecapelli' ), 'name' => 'title',   'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_faq_lead',    'label' => __( 'Lead',    'estecapelli' ), 'name' => 'lead',    'type' => 'textarea', 'rows' => 2 ),
								array(
									'key'          => 'field_faq_items',
									'label'        => __( 'Questions', 'estecapelli' ),
									'name'         => 'items',
									'type'         => 'repeater',
									'min'          => 1,
									'max'          => 20,
									'button_label' => __( '+ Add Question', 'estecapelli' ),
									'layout'       => 'block',
									'sub_fields'   => array(
										array( 'key' => 'field_faq_q', 'label' => __( 'Question', 'estecapelli' ), 'name' => 'question', 'type' => 'text', 'required' => 1 ),
										array(
											'key' => 'field_faq_a',
											'label' => __( 'Answer', 'estecapelli' ),
											'name' => 'answer',
											'type' => 'wysiwyg',
											'tabs' => 'visual',
											'toolbar' => 'basic',
											'media_upload' => 0,
											'required' => 1,
										),
									),
								),
							),
						),

						// ============================================================ Pricing
						'pricing' => array(
							'key'        => 'layout_pricing',
							'name'       => 'pricing',
							'label'      => __( 'Pricing — card with price + features + CTA', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_price_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_price_title',   'label' => __( 'Section title', 'estecapelli' ), 'name' => 'title', 'type' => 'text' ),
								array( 'key' => 'field_price_lead',    'label' => __( 'Lead', 'estecapelli' ), 'name' => 'lead', 'type' => 'textarea', 'rows' => 2 ),
								array( 'key' => 'field_price_plan',    'label' => __( 'Plan name', 'estecapelli' ), 'name' => 'plan_name', 'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_price_currency','label' => __( 'Currency symbol', 'estecapelli' ), 'name' => 'currency', 'type' => 'text', 'default_value' => '€' ),
								array( 'key' => 'field_price_amount',  'label' => __( 'Amount (or "From")', 'estecapelli' ), 'name' => 'amount', 'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_price_period',  'label' => __( 'Period / unit (e.g. "all-inclusive")', 'estecapelli' ), 'name' => 'period', 'type' => 'text' ),
								array(
									'key'          => 'field_price_features',
									'label'        => __( 'Included features', 'estecapelli' ),
									'name'         => 'features',
									'type'         => 'repeater',
									'min'          => 1,
									'max'          => 12,
									'button_label' => __( '+ Add Feature', 'estecapelli' ),
									'layout'       => 'table',
									'sub_fields'   => array(
										array( 'key' => 'field_price_feat', 'label' => __( 'Feature', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'required' => 1 ),
									),
								),
								array( 'key' => 'field_price_note', 'label' => __( 'Note / disclaimer (optional)', 'estecapelli' ), 'name' => 'note', 'type' => 'textarea', 'rows' => 2 ),
								array(
									'key'        => 'field_price_cta',
									'label'      => __( 'CTA', 'estecapelli' ),
									'name'       => 'cta',
									'type'       => 'group',
									'layout'     => 'block',
									'sub_fields' => array(
										array( 'key' => 'field_price_cta_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text' ),
										array( 'key' => 'field_price_cta_url',   'label' => __( 'URL',   'estecapelli' ), 'name' => 'url',   'type' => 'url'  ),
									),
								),
							),
						),

						// ============================================================ Doctor card
						'doctor' => array(
							'key'        => 'layout_doctor',
							'name'       => 'doctor',
							'label'      => __( 'Doctor — profile card linked to a CTA', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_doc_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_doc_name',    'label' => __( 'Doctor name', 'estecapelli' ), 'name' => 'name', 'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_doc_role',    'label' => __( 'Role / title', 'estecapelli' ), 'name' => 'role', 'type' => 'text' ),
								array( 'key' => 'field_doc_photo',   'label' => __( 'Photo', 'estecapelli' ), 'name' => 'photo', 'type' => 'image', 'return_format' => 'array' ),
								array( 'key' => 'field_doc_body',    'label' => __( 'Bio paragraph', 'estecapelli' ), 'name' => 'body', 'type' => 'textarea', 'rows' => 4 ),
								array(
									'key'          => 'field_doc_credentials',
									'label'        => __( 'Credentials', 'estecapelli' ),
									'name'         => 'credentials',
									'type'         => 'repeater',
									'min'          => 0,
									'max'          => 6,
									'button_label' => __( '+ Add Credential', 'estecapelli' ),
									'layout'       => 'table',
									'sub_fields'   => array(
										array( 'key' => 'field_doc_cred_label', 'label' => __( 'Credential', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'required' => 1 ),
									),
								),
								array(
									'key'        => 'field_doc_cta',
									'label'      => __( 'CTA (optional)', 'estecapelli' ),
									'name'       => 'cta',
									'type'       => 'group',
									'layout'     => 'block',
									'sub_fields' => array(
										array( 'key' => 'field_doc_cta_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text' ),
										array( 'key' => 'field_doc_cta_url',   'label' => __( 'URL',   'estecapelli' ), 'name' => 'url',   'type' => 'url'  ),
									),
								),
							),
						),

						// ============================================================ Team — member grid
						'team' => array(
							'key'        => 'layout_team',
							'name'       => 'team',
							'label'      => __( 'Team — member grid (roles or languages)', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_team_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_team_title',   'label' => __( 'Title', 'estecapelli' ), 'name' => 'title', 'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_team_lead',    'label' => __( 'Lead', 'estecapelli' ), 'name' => 'lead', 'type' => 'textarea', 'rows' => 2 ),
								array(
									'key'           => 'field_team_variant',
									'label'         => __( 'Variant', 'estecapelli' ),
									'name'          => 'variant',
									'type'          => 'select',
									'choices'       => array( 'medical' => __( 'Medical Team (show role)', 'estecapelli' ), 'consultant' => __( 'Consultants (show languages)', 'estecapelli' ) ),
									'default_value' => 'medical',
								),
								array(
									'key'          => 'field_team_members',
									'label'        => __( 'Members', 'estecapelli' ),
									'name'         => 'members',
									'type'         => 'repeater',
									'min'          => 0,
									'max'          => 24,
									'button_label' => __( '+ Add Member', 'estecapelli' ),
									'layout'       => 'block',
									'sub_fields'   => array(
										array( 'key' => 'field_team_m_photo', 'label' => __( 'Photo', 'estecapelli' ), 'name' => 'photo', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
										array( 'key' => 'field_team_m_name',  'label' => __( 'Name', 'estecapelli' ), 'name' => 'name', 'type' => 'text', 'required' => 1 ),
										array( 'key' => 'field_team_m_role',  'label' => __( 'Position / role (e.g. Nurse)', 'estecapelli' ), 'name' => 'role', 'type' => 'text' ),
										array(
											'key'          => 'field_team_m_langs',
											'label'        => __( 'Languages (for consultants)', 'estecapelli' ),
											'name'         => 'languages',
											'type'         => 'repeater',
											'min'          => 0,
											'max'          => 10,
											'button_label' => __( '+ Add Language', 'estecapelli' ),
											'layout'       => 'table',
											'sub_fields'   => array(
												array( 'key' => 'field_team_lang_country', 'label' => __( 'Country code (ISO, e.g. gb, tr, sa, ru)', 'estecapelli' ), 'name' => 'country', 'type' => 'text' ),
												array( 'key' => 'field_team_lang_label',   'label' => __( 'Language', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'required' => 1 ),
											),
										),
									),
								),
							),
						),

						// ============================================================ Doctors — roster grid linked to résumés
						'doctors' => array(
							'key'        => 'layout_doctors',
							'name'       => 'doctors',
							'label'      => __( 'Doctors — roster grid (name, position, résumé link)', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_docs_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_docs_title',   'label' => __( 'Title', 'estecapelli' ), 'name' => 'title', 'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_docs_lead',    'label' => __( 'Lead', 'estecapelli' ), 'name' => 'lead', 'type' => 'textarea', 'rows' => 2 ),
								array(
									'key'          => 'field_docs_members',
									'label'        => __( 'Doctors', 'estecapelli' ),
									'name'         => 'members',
									'type'         => 'repeater',
									'min'          => 0,
									'max'          => 24,
									'button_label' => __( '+ Add Doctor', 'estecapelli' ),
									'layout'       => 'block',
									'sub_fields'   => array(
										array( 'key' => 'field_docs_m_photo',    'label' => __( 'Photo', 'estecapelli' ), 'name' => 'photo', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
										array( 'key' => 'field_docs_m_name',     'label' => __( 'Name', 'estecapelli' ), 'name' => 'name', 'type' => 'text', 'required' => 1 ),
										array( 'key' => 'field_docs_m_position', 'label' => __( 'Position / specialty', 'estecapelli' ), 'name' => 'position', 'type' => 'text' ),
										array( 'key' => 'field_docs_m_resume',   'label' => __( 'Résumé / profile URL', 'estecapelli' ), 'name' => 'resume_url', 'type' => 'url', 'instructions' => __( 'Link to this doctor\'s profile page. Leave empty to hide the button.', 'estecapelli' ) ),
									),
								),
							),
						),

						// ============================================================ Related treatments
						'related' => array(
							'key'        => 'layout_related',
							'name'       => 'related',
							'label'      => __( 'Related Treatments — auto-queried cards', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_rel_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_rel_title',   'label' => __( 'Title', 'estecapelli' ), 'name' => 'title', 'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_rel_count',   'label' => __( 'How many to show', 'estecapelli' ), 'name' => 'count', 'type' => 'number', 'default_value' => 3, 'min' => 1, 'max' => 12 ),
								array(
									'key'          => 'field_rel_category',
									'label'        => __( 'Category slug (optional)', 'estecapelli' ),
									'instructions' => __( 'On a category landing page, enter a treatment-category slug (e.g. hair-transplant) to list every treatment in that category. Leave empty on treatment pages.', 'estecapelli' ),
									'name'         => 'category',
									'type'         => 'text',
								),
								array(
									'key'           => 'field_rel_manual',
									'label'         => __( 'Manual selection (optional)', 'estecapelli' ),
									'instructions'  => __( 'Leave empty to auto-pull treatments from the same category.', 'estecapelli' ),
									'name'          => 'manual',
									'type'          => 'post_object',
									'post_type'     => array( 'treatment' ),
									'multiple'      => 1,
									'return_format' => 'id',
									'ui'            => 1,
								),
							),
						),

						// ============================================================ Intro
						'intro' => array(
							'key'        => 'layout_intro',
							'name'       => 'intro',
							'label'      => __( 'Intro — image + body (left/right)', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array(
									'key'   => 'field_intro_eyebrow',
									'label' => __( 'Eyebrow', 'estecapelli' ),
									'name'  => 'eyebrow',
									'type'  => 'text',
								),
								array(
									'key'      => 'field_intro_title',
									'label'    => __( 'Title', 'estecapelli' ),
									'name'     => 'title',
									'type'     => 'text',
									'required' => 1,
								),
								array(
									'key'   => 'field_intro_body',
									'label' => __( 'Body', 'estecapelli' ),
									'name'  => 'body',
									'type'  => 'wysiwyg',
									'tabs'  => 'visual',
									'toolbar' => 'basic',
									'media_upload' => 0,
								),
								array(
									'key'           => 'field_intro_media_type',
									'label'         => __( 'Media type', 'estecapelli' ),
									'name'          => 'media_type',
									'type'          => 'radio',
									'layout'        => 'horizontal',
									'choices'       => array(
										'image' => __( 'Image', 'estecapelli' ),
										'video' => __( 'YouTube video', 'estecapelli' ),
										'none'  => __( 'None', 'estecapelli' ),
									),
									'default_value' => 'image',
								),
								array(
									'key'               => 'field_intro_image',
									'label'             => __( 'Image', 'estecapelli' ),
									'name'              => 'image',
									'type'              => 'image',
									'return_format'     => 'array',
									'preview_size'      => 'medium',
									'conditional_logic' => array( array( array( 'field' => 'field_intro_media_type', 'operator' => '==', 'value' => 'image' ) ) ),
								),
								array(
									'key'               => 'field_intro_video_url',
									'label'             => __( 'YouTube video link', 'estecapelli' ),
									'name'              => 'video_url',
									'type'              => 'text',
									'instructions'      => __( 'Paste the full YouTube link (e.g. https://youtu.be/dQw4w9WgXcQ) or just the video ID.', 'estecapelli' ),
									'conditional_logic' => array( array( array( 'field' => 'field_intro_media_type', 'operator' => '==', 'value' => 'video' ) ) ),
								),
								array(
									'key'           => 'field_intro_image_position',
									'label'         => __( 'Media position', 'estecapelli' ),
									'name'          => 'image_position',
									'type'          => 'radio',
									'layout'        => 'horizontal',
									'choices'       => array(
										'left'  => __( 'Left', 'estecapelli' ),
										'right' => __( 'Right', 'estecapelli' ),
									),
									'default_value' => 'right',
								),
								array(
									'key'        => 'field_intro_cta',
									'label'      => __( 'CTA (optional)', 'estecapelli' ),
									'name'       => 'cta',
									'type'       => 'group',
									'layout'     => 'block',
									'sub_fields' => array(
										array( 'key' => 'field_intro_cta_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text' ),
										array( 'key' => 'field_intro_cta_url',   'label' => __( 'URL',   'estecapelli' ), 'name' => 'url',   'type' => 'url'  ),
									),
								),
							),
						),

						// ============================================================ Lead form
						'form' => array(
							'key'        => 'layout_form',
							'name'       => 'form',
							'label'      => __( 'Lead Form — name, phone, email, note capture', 'estecapelli' ),
							'display'    => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_form_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_form_title',    'label' => __( 'Title', 'estecapelli' ), 'name' => 'title', 'type' => 'text', 'required' => 1 ),
								array( 'key' => 'field_form_lead',    'label' => __( 'Lead paragraph', 'estecapelli' ), 'name' => 'lead', 'type' => 'textarea', 'rows' => 3 ),
								array(
									'key'          => 'field_form_points',
									'label'        => __( 'Trust points (optional, shown beside the form)', 'estecapelli' ),
									'name'         => 'points',
									'type'         => 'repeater',
									'min'          => 0,
									'max'          => 6,
									'button_label' => __( '+ Add Point', 'estecapelli' ),
									'layout'       => 'table',
									'sub_fields'   => array(
										array( 'key' => 'field_form_point_icon',  'label' => __( 'Icon', 'estecapelli' ),  'name' => 'icon',  'type' => 'select', 'choices' => $icon_choices, 'allow_null' => 1 ),
										array( 'key' => 'field_form_point_label', 'label' => __( 'Text', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'required' => 1 ),
									),
								),
								array( 'key' => 'field_form_submit', 'label' => __( 'Submit button label', 'estecapelli' ), 'name' => 'submit_label', 'type' => 'text', 'default_value' => __( 'Request a Free Consultation', 'estecapelli' ) ),
								array( 'key' => 'field_form_show_wa', 'label' => __( 'Show WhatsApp button', 'estecapelli' ), 'name' => 'show_whatsapp', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
							),
						),

					),
				),
			),
		)
	);

	// ============================================================
	// Before & After result fields
	// ============================================================
	acf_add_local_field_group(
		array(
			'key'                   => 'group_result_fields',
			'title'                 => __( 'Before & After', 'estecapelli' ),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'result',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'fields'                => array(
				array(
					'key'           => 'field_result_before',
					'label'         => __( 'Before image', 'estecapelli' ),
					'name'          => 'before_image',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'required'      => 1,
				),
				array(
					'key'           => 'field_result_after',
					'label'         => __( 'After image', 'estecapelli' ),
					'name'          => 'after_image',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'required'      => 1,
				),
				array(
					'key'           => 'field_result_service',
					'label'         => __( 'Service', 'estecapelli' ),
					'name'          => 'service',
					'type'          => 'post_object',
					'instructions'  => __( 'The treatment this result belongs to. Drives the carousel on that service page and the grouping on the Before & After page.', 'estecapelli' ),
					'post_type'     => array( 'treatment' ),
					'return_format' => 'id',
					'multiple'      => 0,
					'allow_null'    => 0,
					'ui'            => 1,
					'required'      => 1,
				),
				array(
					'key'           => 'field_result_method',
					'label'         => __( 'Method label (optional)', 'estecapelli' ),
					'name'          => 'method',
					'type'          => 'text',
					'instructions'  => __( 'Shown under the photo. Leave empty to use the service name.', 'estecapelli' ),
				),
				array(
					'key'           => 'field_result_grafts',
					'label'         => __( 'Graft count (optional)', 'estecapelli' ),
					'name'          => 'grafts',
					'type'          => 'number',
					'instructions'  => __( 'Hair transplant results only. Leave empty for other procedures.', 'estecapelli' ),
					'min'           => 0,
				),
			),
		)
	);

	// ============================================================
	// Doctor profile fields
	//
	// A doctor is its own post type (one entry per surgeon). The editor only
	// fills this short form — name is the post title, everything else lives
	// here. The single-doctor template and the "Our Doctors" roster grid both
	// read these fields, so a new doctor appears on the site automatically with
	// no page nesting and no roster editing.
	// ============================================================
	acf_add_local_field_group(
		array(
			'key'                   => 'group_doctor_fields',
			'title'                 => __( 'Doctor Profile', 'estecapelli' ),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'doctor',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'fields'                => array(
				array(
					'key'          => 'field_doctor_position',
					'label'        => __( 'Position / title', 'estecapelli' ),
					'name'         => 'position',
					'type'         => 'text',
					'instructions' => __( 'e.g. Plastic & Reconstructive Surgeon. Shown under the name on the card and profile.', 'estecapelli' ),
				),
				array(
					'key'           => 'field_doctor_photo',
					'label'         => __( 'Photo', 'estecapelli' ),
					'name'          => 'photo',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'instructions'  => __( 'Portrait photo. Leave empty to show the doctor’s initials instead.', 'estecapelli' ),
				),
				array(
					'key'          => 'field_doctor_bio',
					'label'        => __( 'Bio', 'estecapelli' ),
					'name'         => 'bio',
					'type'         => 'textarea',
					'rows'         => 6,
					'instructions' => __( 'A short paragraph about the doctor’s training, experience and approach.', 'estecapelli' ),
				),
				array(
					'key'          => 'field_doctor_credentials',
					'label'        => __( 'Credentials', 'estecapelli' ),
					'name'         => 'credentials',
					'type'         => 'repeater',
					'min'          => 0,
					'max'          => 12,
					'button_label' => __( '+ Add Credential', 'estecapelli' ),
					'layout'       => 'table',
					'sub_fields'   => array(
						array( 'key' => 'field_doctor_cred_label', 'label' => __( 'Credential', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'required' => 1 ),
					),
				),
			),
		)
	);

	/* ===== Homepage — Patient Stories =====
	   Edits overlay the theme defaults: any field left empty keeps the current
	   value, and an empty image keeps the current photo (matched by Key). So the
	   page never loses data and nothing has to be re-imported. */
	acf_add_local_field_group(
		array(
			'key'                   => 'group_home_patient_stories',
			'title'                 => __( 'Homepage — Patient Stories', 'estecapelli' ),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'estecapelli-homepage',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'fields'                => array(
				array( 'key' => 'field_home_stories_eyebrow', 'label' => __( 'Section eyebrow', 'estecapelli' ), 'name' => 'home_stories_eyebrow', 'type' => 'text', 'instructions' => __( 'Small label above the heading. Empty = keep current.', 'estecapelli' ), 'wrapper' => array( 'width' => '30' ) ),
				array( 'key' => 'field_home_stories_headline', 'label' => __( 'Section headline', 'estecapelli' ), 'name' => 'home_stories_headline', 'type' => 'text', 'instructions' => __( 'Empty = keep current.', 'estecapelli' ), 'wrapper' => array( 'width' => '70' ) ),
				array( 'key' => 'field_home_stories_lead', 'label' => __( 'Section lead', 'estecapelli' ), 'name' => 'home_stories_lead', 'type' => 'textarea', 'rows' => 2, 'instructions' => __( 'Empty = keep current.', 'estecapelli' ) ),
				array(
					'key'          => 'field_home_stories_list',
					'label'        => __( 'Patients', 'estecapelli' ),
					'name'         => 'home_patient_stories_list',
					'type'         => 'repeater',
					'instructions' => __( 'Each row is a patient. Leave an image empty to keep the current one. Keep the Key unchanged so existing photos/videos stay matched.', 'estecapelli' ),
					'min'          => 0,
					'layout'       => 'block',
					'button_label' => __( '+ Add Patient', 'estecapelli' ),
					'sub_fields'   => array(
						array( 'key' => 'field_hs_key', 'label' => __( 'Key', 'estecapelli' ), 'name' => 'key', 'type' => 'text', 'instructions' => __( 'Internal id, e.g. alexandre-t. Do not change for existing patients.', 'estecapelli' ), 'wrapper' => array( 'width' => '30' ) ),
						array( 'key' => 'field_hs_name', 'label' => __( 'Name', 'estecapelli' ), 'name' => 'name', 'type' => 'text', 'wrapper' => array( 'width' => '40' ) ),
						array( 'key' => 'field_hs_rating', 'label' => __( 'Rating (1–5)', 'estecapelli' ), 'name' => 'rating', 'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5, 'wrapper' => array( 'width' => '30' ) ),
						array( 'key' => 'field_hs_country', 'label' => __( 'Country', 'estecapelli' ), 'name' => 'country', 'type' => 'text', 'wrapper' => array( 'width' => '40' ) ),
						array( 'key' => 'field_hs_flag', 'label' => __( 'Flag emoji', 'estecapelli' ), 'name' => 'flag', 'type' => 'text', 'wrapper' => array( 'width' => '20' ) ),
						array( 'key' => 'field_hs_grafts', 'label' => __( 'Grafts', 'estecapelli' ), 'name' => 'grafts', 'type' => 'text', 'wrapper' => array( 'width' => '20' ) ),
						array( 'key' => 'field_hs_technique', 'label' => __( 'Technique', 'estecapelli' ), 'name' => 'technique', 'type' => 'text', 'wrapper' => array( 'width' => '20' ) ),
						array( 'key' => 'field_hs_video', 'label' => __( 'YouTube video ID', 'estecapelli' ), 'name' => 'video_id', 'type' => 'text', 'instructions' => __( 'The id after /shorts/ or watch?v=', 'estecapelli' ), 'wrapper' => array( 'width' => '40' ) ),
						array( 'key' => 'field_hs_pretitle', 'label' => __( 'Short caption', 'estecapelli' ), 'name' => 'pre_title', 'type' => 'text', 'wrapper' => array( 'width' => '60' ) ),
						array( 'key' => 'field_hs_body', 'label' => __( 'Story', 'estecapelli' ), 'name' => 'body', 'type' => 'textarea', 'rows' => 4 ),
						array( 'key' => 'field_hs_poster', 'label' => __( 'Big-box image', 'estecapelli' ), 'name' => 'poster', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => __( 'Square (1:1). Empty = keep current frame.', 'estecapelli' ), 'wrapper' => array( 'width' => '50' ) ),
						array( 'key' => 'field_hs_poster_pos', 'label' => __( 'Big-box focus', 'estecapelli' ), 'name' => 'poster_pos', 'type' => 'text', 'instructions' => __( 'Optional, e.g. center 60%.', 'estecapelli' ), 'wrapper' => array( 'width' => '50' ) ),
						array( 'key' => 'field_hs_photo', 'label' => __( 'Playlist image', 'estecapelli' ), 'name' => 'photo', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => __( 'Square (1:1). Empty = keep current photo.', 'estecapelli' ), 'wrapper' => array( 'width' => '50' ) ),
						array( 'key' => 'field_hs_photo_pos', 'label' => __( 'Playlist focus', 'estecapelli' ), 'name' => 'photo_pos', 'type' => 'text', 'instructions' => __( 'Optional, e.g. center 70%.', 'estecapelli' ), 'wrapper' => array( 'width' => '50' ) ),
					),
				),
			),
		)
	);

	$home_loc = array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'estecapelli-homepage' ) ) );

	/* ===== Hero — Signature split (Exosome / VITA) ===== */
	$split_panel = function ( $p, $label ) {
		return array(
			'key' => 'field_hsp_' . $p, 'label' => $label, 'name' => $p, 'type' => 'group', 'layout' => 'block',
			'sub_fields' => array(
				array( 'key' => 'field_hsp_' . $p . '_name', 'label' => __( 'Name', 'estecapelli' ), 'name' => 'name', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
				array( 'key' => 'field_hsp_' . $p . '_tag', 'label' => __( 'Tag', 'estecapelli' ), 'name' => 'tag', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
				array( 'key' => 'field_hsp_' . $p . '_video', 'label' => __( 'YouTube video ID', 'estecapelli' ), 'name' => 'video', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
				array( 'key' => 'field_hsp_' . $p . '_cover', 'label' => __( 'Cover image', 'estecapelli' ), 'name' => 'cover', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => __( 'Empty = keep current.', 'estecapelli' ), 'wrapper' => array( 'width' => '50' ) ),
			),
		);
	};
	acf_add_local_field_group(
		array(
			'key'        => 'group_home_split',
			'title'      => __( 'Homepage — Hero Signature Split', 'estecapelli' ),
			'location'   => $home_loc,
			'menu_order' => 2,
			'active'     => true,
			'fields'     => array(
				array(
					'key' => 'field_hs_split', 'label' => __( 'Exosome / VITA', 'estecapelli' ), 'name' => 'signature_split', 'type' => 'group', 'layout' => 'block',
					'instructions' => __( 'Empty fields keep the current content.', 'estecapelli' ),
					'sub_fields' => array(
						array(
							'key' => 'field_hs_intro', 'label' => __( 'Intro', 'estecapelli' ), 'name' => 'intro', 'type' => 'group', 'layout' => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_hs_intro_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_hs_intro_headline', 'label' => __( 'Headline', 'estecapelli' ), 'name' => 'headline', 'type' => 'text' ),
								array( 'key' => 'field_hs_intro_body', 'label' => __( 'Body', 'estecapelli' ), 'name' => 'body', 'type' => 'text' ),
								array( 'key' => 'field_hs_intro_hint', 'label' => __( 'Hint', 'estecapelli' ), 'name' => 'hint', 'type' => 'text' ),
							),
						),
						array(
							'key' => 'field_hs_panels', 'label' => __( 'Panels', 'estecapelli' ), 'name' => 'panels', 'type' => 'group', 'layout' => 'block',
							'sub_fields' => array(
								$split_panel( 'vita', __( 'VITA panel', 'estecapelli' ) ),
								$split_panel( 'exosome', __( 'Exosome panel', 'estecapelli' ) ),
							),
						),
					),
				),
			),
		)
	);

	/* ===== Hero — Slides 2 & 3 (Expert + Women) ===== */
	acf_add_local_field_group(
		array(
			'key'        => 'group_home_hero_slides',
			'title'      => __( 'Homepage — Hero Slides 2 & 3', 'estecapelli' ),
			'location'   => $home_loc,
			'menu_order' => 3,
			'active'     => true,
			'fields'     => array(
				array(
					'key' => 'field_hsl', 'label' => __( 'Hero slides', 'estecapelli' ), 'name' => 'hero_slides', 'type' => 'group', 'layout' => 'block',
					'instructions' => __( 'Empty fields keep the current content.', 'estecapelli' ),
					'sub_fields' => array(
						array(
							'key' => 'field_hsl_expert', 'label' => __( 'Slide 2 — Expert', 'estecapelli' ), 'name' => 'expert', 'type' => 'group', 'layout' => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_hsl_e_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_hsl_e_headline', 'label' => __( 'Headline', 'estecapelli' ), 'name' => 'headline', 'type' => 'text' ),
								array( 'key' => 'field_hsl_e_body', 'label' => __( 'Body', 'estecapelli' ), 'name' => 'body', 'type' => 'textarea', 'rows' => 2 ),
							),
						),
						array(
							'key' => 'field_hsl_women', 'label' => __( 'Slide 3 — Women', 'estecapelli' ), 'name' => 'women', 'type' => 'group', 'layout' => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_hsl_w_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
								array( 'key' => 'field_hsl_w_headline', 'label' => __( 'Headline', 'estecapelli' ), 'name' => 'headline', 'type' => 'text' ),
								array( 'key' => 'field_hsl_w_body', 'label' => __( 'Body', 'estecapelli' ), 'name' => 'body', 'type' => 'textarea', 'rows' => 3 ),
								array( 'key' => 'field_hsl_w_video', 'label' => __( 'YouTube video ID', 'estecapelli' ), 'name' => 'video', 'type' => 'text' ),
							),
						),
					),
				),
			),
		)
	);

	/* ===== Trust stats strip ===== */
	acf_add_local_field_group(
		array(
			'key'        => 'group_home_trust_stats',
			'title'      => __( 'Homepage — Trust Stats', 'estecapelli' ),
			'location'   => $home_loc,
			'menu_order' => 4,
			'active'     => true,
			'fields'     => array(
				array(
					'key' => 'field_ts_list', 'label' => __( 'Stats', 'estecapelli' ), 'name' => 'trust_stats', 'type' => 'repeater', 'layout' => 'table',
					'instructions' => __( 'The 4 numbers under the hero. Leave the whole list untouched to keep the current values.', 'estecapelli' ),
					'min' => 0, 'button_label' => __( '+ Add stat', 'estecapelli' ),
					'sub_fields' => array(
						array( 'key' => 'field_ts_icon', 'label' => __( 'Icon', 'estecapelli' ), 'name' => 'icon', 'type' => 'text', 'instructions' => __( 'e.g. calendar, hair, globe, headset', 'estecapelli' ) ),
						array( 'key' => 'field_ts_value', 'label' => __( 'Value', 'estecapelli' ), 'name' => 'value', 'type' => 'text' ),
						array( 'key' => 'field_ts_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text' ),
					),
				),
			),
		)
	);
	/* ===== Why Choose ===== */
	acf_add_local_field_group(
		array(
			'key'        => 'group_home_why_choose',
			'title'      => __( 'Homepage — Why Choose', 'estecapelli' ),
			'location'   => $home_loc,
			'menu_order' => 5,
			'active'     => true,
			'fields'     => array(
				array(
					'key' => 'field_wc', 'label' => __( 'Why Choose', 'estecapelli' ), 'name' => 'home_why_choose', 'type' => 'group', 'layout' => 'block',
					'instructions' => __( 'Empty fields keep the current content. (The 9 comparison rows stay as-is for now.)', 'estecapelli' ),
					'sub_fields' => array(
						array( 'key' => 'field_wc_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
						array( 'key' => 'field_wc_headline', 'label' => __( 'Headline', 'estecapelli' ), 'name' => 'headline', 'type' => 'text' ),
						array( 'key' => 'field_wc_lead', 'label' => __( 'Lead', 'estecapelli' ), 'name' => 'lead', 'type' => 'textarea', 'rows' => 2 ),
						array(
							'key' => 'field_wc_intro', 'label' => __( 'Intro block', 'estecapelli' ), 'name' => 'intro', 'type' => 'group', 'layout' => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_wc_intro_title', 'label' => __( 'Title', 'estecapelli' ), 'name' => 'title', 'type' => 'text' ),
								array( 'key' => 'field_wc_intro_body', 'label' => __( 'Body', 'estecapelli' ), 'name' => 'body', 'type' => 'textarea', 'rows' => 3 ),
								array( 'key' => 'field_wc_intro_caption', 'label' => __( 'Caption', 'estecapelli' ), 'name' => 'caption', 'type' => 'text' ),
								array(
									'key' => 'field_wc_intro_cta', 'label' => __( 'Button', 'estecapelli' ), 'name' => 'cta', 'type' => 'group', 'layout' => 'block',
									'sub_fields' => array(
										array( 'key' => 'field_wc_cta_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
										array( 'key' => 'field_wc_cta_url', 'label' => __( 'URL', 'estecapelli' ), 'name' => 'url', 'type' => 'url', 'wrapper' => array( 'width' => '50' ) ),
									),
								),
							),
						),
						array( 'key' => 'field_wc_us_label', 'label' => __( 'Our column label', 'estecapelli' ), 'name' => 'us_label', 'type' => 'text', 'wrapper' => array( 'width' => '25' ) ),
						array( 'key' => 'field_wc_them_label', 'label' => __( 'Others column label', 'estecapelli' ), 'name' => 'them_label', 'type' => 'text', 'wrapper' => array( 'width' => '25' ) ),
						array( 'key' => 'field_wc_us_tag', 'label' => __( 'Our tag', 'estecapelli' ), 'name' => 'us_tag', 'type' => 'text', 'wrapper' => array( 'width' => '25' ) ),
						array( 'key' => 'field_wc_them_tag', 'label' => __( 'Others tag', 'estecapelli' ), 'name' => 'them_tag', 'type' => 'text', 'wrapper' => array( 'width' => '25' ) ),
						array( 'key' => 'field_wc_footnote', 'label' => __( 'Footnote', 'estecapelli' ), 'name' => 'footnote', 'type' => 'text' ),
					),
				),
			),
		)
	);

	/* ===== Signature Methods (heading + button) ===== */
	acf_add_local_field_group(
		array(
			'key'        => 'group_home_methods',
			'title'      => __( 'Homepage — Signature Methods', 'estecapelli' ),
			'location'   => $home_loc,
			'menu_order' => 6,
			'active'     => true,
			'fields'     => array(
				array(
					'key' => 'field_sm', 'label' => __( 'Signature Methods', 'estecapelli' ), 'name' => 'home_signature_methods', 'type' => 'group', 'layout' => 'block',
					'instructions' => __( 'Empty fields keep the current content. (The 3 method cards stay as-is for now.)', 'estecapelli' ),
					'sub_fields' => array(
						array( 'key' => 'field_sm_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
						array( 'key' => 'field_sm_headline', 'label' => __( 'Headline', 'estecapelli' ), 'name' => 'headline', 'type' => 'text' ),
						array( 'key' => 'field_sm_lead', 'label' => __( 'Lead', 'estecapelli' ), 'name' => 'lead', 'type' => 'textarea', 'rows' => 2 ),
						array(
							'key' => 'field_sm_cta2', 'label' => __( 'Button', 'estecapelli' ), 'name' => 'cta_secondary', 'type' => 'group', 'layout' => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_sm_cta2_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
								array( 'key' => 'field_sm_cta2_url', 'label' => __( 'URL', 'estecapelli' ), 'name' => 'url', 'type' => 'url', 'wrapper' => array( 'width' => '50' ) ),
							),
						),
					),
				),
			),
		)
	);

	/* ===== Your Journey (heading + closing) ===== */
	acf_add_local_field_group(
		array(
			'key'        => 'group_home_journey',
			'title'      => __( 'Homepage — Your Journey', 'estecapelli' ),
			'location'   => $home_loc,
			'menu_order' => 7,
			'active'     => true,
			'fields'     => array(
				array(
					'key' => 'field_jn', 'label' => __( 'Your Journey', 'estecapelli' ), 'name' => 'home_journey_steps', 'type' => 'group', 'layout' => 'block',
					'instructions' => __( 'Empty fields keep the current content. (The 6 step cards stay as-is for now.)', 'estecapelli' ),
					'sub_fields' => array(
						array( 'key' => 'field_jn_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
						array( 'key' => 'field_jn_headline', 'label' => __( 'Headline', 'estecapelli' ), 'name' => 'headline', 'type' => 'text' ),
						array( 'key' => 'field_jn_lead', 'label' => __( 'Lead', 'estecapelli' ), 'name' => 'lead', 'type' => 'textarea', 'rows' => 2 ),
						array(
							'key' => 'field_jn_closing', 'label' => __( 'Closing block', 'estecapelli' ), 'name' => 'closing', 'type' => 'group', 'layout' => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_jn_closing_text', 'label' => __( 'Text', 'estecapelli' ), 'name' => 'text', 'type' => 'text' ),
								array(
									'key' => 'field_jn_closing_cta', 'label' => __( 'Button', 'estecapelli' ), 'name' => 'cta', 'type' => 'group', 'layout' => 'block',
									'sub_fields' => array(
										array( 'key' => 'field_jn_cta_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
										array( 'key' => 'field_jn_cta_url', 'label' => __( 'URL', 'estecapelli' ), 'name' => 'url', 'type' => 'url', 'wrapper' => array( 'width' => '50' ) ),
									),
								),
							),
						),
					),
				),
			),
		)
	);

	/* ===== Our Facilities (heading + button) ===== */
	acf_add_local_field_group(
		array(
			'key'        => 'group_home_facilities',
			'title'      => __( 'Homepage — Our Facilities', 'estecapelli' ),
			'location'   => $home_loc,
			'menu_order' => 8,
			'active'     => true,
			'fields'     => array(
				array(
					'key' => 'field_fc', 'label' => __( 'Our Facilities', 'estecapelli' ), 'name' => 'home_facilities', 'type' => 'group', 'layout' => 'block',
					'instructions' => __( 'Empty fields keep the current content. (The clinic gallery stays as-is for now.)', 'estecapelli' ),
					'sub_fields' => array(
						array( 'key' => 'field_fc_eyebrow', 'label' => __( 'Eyebrow', 'estecapelli' ), 'name' => 'eyebrow', 'type' => 'text' ),
						array( 'key' => 'field_fc_headline', 'label' => __( 'Headline', 'estecapelli' ), 'name' => 'headline', 'type' => 'text' ),
						array( 'key' => 'field_fc_lead', 'label' => __( 'Lead', 'estecapelli' ), 'name' => 'lead', 'type' => 'textarea', 'rows' => 2 ),
						array(
							'key' => 'field_fc_cta', 'label' => __( 'Button', 'estecapelli' ), 'name' => 'cta', 'type' => 'group', 'layout' => 'block',
							'sub_fields' => array(
								array( 'key' => 'field_fc_cta_label', 'label' => __( 'Label', 'estecapelli' ), 'name' => 'label', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
								array( 'key' => 'field_fc_cta_url', 'label' => __( 'URL', 'estecapelli' ), 'name' => 'url', 'type' => 'url', 'wrapper' => array( 'width' => '50' ) ),
							),
						),
					),
				),
			),
		)
	);
}

/**
 * One-time seed: copy the current Patient Stories defaults into the ACF options
 * so the admin opens pre-filled with today's content (no re-import). Text only —
 * images are left empty and fall back to the theme files by Key, so nothing is
 * re-uploaded. Runs once, never overwrites anything already entered.
 */
add_action( 'admin_init', 'estecapelli_seed_home_patient_stories' );
function estecapelli_seed_home_patient_stories() {
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
		return;
	}

	// Patient stories — seed once so the admin opens with the current 7 patients.
	if ( ! get_option( 'estecapelli_home_stories_seeded' ) && function_exists( 'estecapelli_patient_stories_defaults' ) ) {
		$existing = get_field( 'home_patient_stories_list', 'option' );
		if ( empty( $existing ) ) {
			$d    = estecapelli_patient_stories_defaults();
			$rows = array();
			foreach ( $d['stories'] as $s ) {
				$rows[] = array(
					'key'        => $s['key'],
					'name'       => $s['name'],
					'rating'     => isset( $s['rating'] ) ? $s['rating'] : 5,
					'country'    => isset( $s['country'] ) ? $s['country'] : '',
					'flag'       => isset( $s['flag'] ) ? $s['flag'] : '',
					'grafts'     => isset( $s['grafts'] ) ? $s['grafts'] : '',
					'technique'  => isset( $s['technique'] ) ? $s['technique'] : '',
					'video_id'   => isset( $s['video_id'] ) ? $s['video_id'] : '',
					'pre_title'  => isset( $s['pre_title'] ) ? $s['pre_title'] : '',
					'body'       => isset( $s['body'] ) ? $s['body'] : '',
					'poster'     => '', // keep the theme frame (matched by Key)
					'poster_pos' => isset( $s['poster_pos'] ) ? $s['poster_pos'] : '',
					'photo'      => '', // keep the theme photo (matched by Key)
					'photo_pos'  => isset( $s['photo_pos'] ) ? $s['photo_pos'] : '',
				);
			}
			update_field( 'home_patient_stories_list', $rows, 'option' );

			if ( '' === (string) get_field( 'home_stories_eyebrow', 'option' ) ) {
				update_field( 'home_stories_eyebrow', $d['eyebrow'], 'option' );
			}
			if ( '' === (string) get_field( 'home_stories_headline', 'option' ) ) {
				update_field( 'home_stories_headline', $d['headline'], 'option' );
			}
			if ( '' === (string) get_field( 'home_stories_lead', 'option' ) ) {
				update_field( 'home_stories_lead', $d['lead'], 'option' );
			}
		}
		update_option( 'estecapelli_home_stories_seeded', 1 );
	}

	// Trust stats — seed once (own flag, so it runs even though stories already did).
	if ( ! get_option( 'estecapelli_trust_stats_seeded' ) && function_exists( 'estecapelli_trust_stats_defaults' ) ) {
		$ts = get_field( 'trust_stats', 'option' );
		if ( empty( $ts ) ) {
			update_field( 'trust_stats', estecapelli_trust_stats_defaults(), 'option' );
		}
		update_option( 'estecapelli_trust_stats_seeded', 1 );
	}
}
