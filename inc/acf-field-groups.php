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
									'label'             => __( 'YouTube video ID', 'estecapelli' ),
									'name'              => 'video_id',
									'type'              => 'text',
									'instructions'      => __( 'Just the ID — e.g. dQw4w9WgXcQ from youtube.com/watch?v=dQw4w9WgXcQ', 'estecapelli' ),
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
									'key'           => 'field_intro_image',
									'label'         => __( 'Image', 'estecapelli' ),
									'name'          => 'image',
									'type'          => 'image',
									'return_format' => 'array',
									'preview_size'  => 'medium',
								),
								array(
									'key'           => 'field_intro_image_position',
									'label'         => __( 'Image position', 'estecapelli' ),
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

					),
				),
			),
		)
	);
}
