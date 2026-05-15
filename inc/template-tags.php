<?php
/**
 * Template tags — reusable presentational helpers.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'estecapelli_icon' ) ) {
	/**
	 * Inline SVG icons (single source of truth, no external sprite).
	 *
	 * @param string $name  Icon name: whatsapp, phone, globe, chevron-down, star, menu, close.
	 * @param array  $args  Optional attrs (class, width, height, aria-label).
	 */
	function estecapelli_icon( $name, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'class'      => '',
			'width'      => 20,
			'height'     => 20,
			'aria-label' => '',
		) );

		$attr = sprintf(
			'class="icon icon--%1$s %2$s" width="%3$d" height="%4$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" %5$s',
			esc_attr( $name ),
			esc_attr( $args['class'] ),
			(int) $args['width'],
			(int) $args['height'],
			$args['aria-label'] ? sprintf( 'role="img" aria-label="%s"', esc_attr( $args['aria-label'] ) ) : 'aria-hidden="true" focusable="false"'
		);

		$paths = array(
			'whatsapp'     => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.297-.497.1-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.57-.085 1.758-.719 2.006-1.413.247-.694.247-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.002-5.45 4.437-9.884 9.888-9.884a9.82 9.82 0 0 1 6.988 2.898 9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z" fill="currentColor" stroke="none"/>',
			'phone'        => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.37 1.9.72 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.35 1.85.59 2.81.72A2 2 0 0 1 22 16.92Z"/>',
			'globe'        => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
			'chevron-down' => '<polyline points="6 9 12 15 18 9"/>',
			'star'         => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="currentColor" stroke="none"/>',
			'menu'         => '<line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/>',
			'close'        => '<line x1="6" y1="6" x2="18" y2="18"/><line x1="6" y1="18" x2="18" y2="6"/>',
			'arrow-right'  => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
			'mail'         => '<rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="22 6 12 13 2 6"/>',
			'map-pin'      => '<path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
			'instagram'    => '<rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>',
			'facebook'     => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
			'youtube'      => '<path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="currentColor" stroke="none"/>',
			'tiktok'       => '<path d="M16 8a5 5 0 0 0 5 5V8.5a4.5 4.5 0 0 1-4.5-4.5H13v12a3 3 0 1 1-3-3v-3a6 6 0 1 0 6 6V8z" fill="currentColor" stroke="none"/>',
		);

		$path = $paths[ $name ] ?? '';

		echo '<svg ' . $attr . '>' . $path . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'estecapelli_trustpilot_badge' ) ) {
	/**
	 * Trustpilot-style 5-star "Excellent" badge. Inline-rendered; replace with
	 * the real Trustpilot embed when an account is connected.
	 */
	function estecapelli_trustpilot_badge() {
		?>
		<span class="trustpilot" aria-label="<?php esc_attr_e( 'Trustpilot rating: 5 out of 5, Excellent', 'estecapelli' ); ?>">
			<span class="trustpilot__label"><?php esc_html_e( 'Excellent', 'estecapelli' ); ?></span>
			<span class="trustpilot__stars" aria-hidden="true">
				<?php for ( $i = 0; $i < 5; $i++ ) : ?>
					<?php estecapelli_icon( 'star', array( 'width' => 14, 'height' => 14, 'class' => 'trustpilot__star' ) ); ?>
				<?php endfor; ?>
			</span>
			<span class="trustpilot__name">Trustpilot</span>
		</span>
		<?php
	}
}

if ( ! function_exists( 'estecapelli_whatsapp_url' ) ) {
	/**
	 * Build a wa.me URL from the configured WhatsApp number.
	 */
	function estecapelli_whatsapp_url( $message = '' ) {
		$number = preg_replace( '/[^0-9]/', '', ESTECAPELLI_WHATSAPP );
		$url    = 'https://wa.me/' . $number;
		if ( $message ) {
			$url .= '?text=' . rawurlencode( $message );
		}
		return $url;
	}
}

if ( ! function_exists( 'estecapelli_footer_contact' ) ) {
	/**
	 * Footer contact block data (location, address, phone, email, social).
	 * ACF override: option 'footer_contact' takes precedence when set.
	 */
	function estecapelli_footer_contact() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'footer_contact', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}
		return array(
			'heading'  => __( 'Visit Us', 'estecapelli' ),
			'address'  => __( 'Istanbul, Türkiye', 'estecapelli' ),
			'phone'    => '+90 543 148 88 88',
			'whatsapp' => ESTECAPELLI_WHATSAPP,
			'email'    => 'info@estecapelli.com',
			'socials'  => array(
				array( 'label' => 'Instagram', 'icon' => 'instagram', 'url' => 'https://instagram.com/estecapelli' ),
				array( 'label' => 'Facebook',  'icon' => 'facebook',  'url' => 'https://facebook.com/estecapelli' ),
				array( 'label' => 'YouTube',   'icon' => 'youtube',   'url' => 'https://youtube.com/@estecapelli' ),
				array( 'label' => 'TikTok',    'icon' => 'tiktok',    'url' => 'https://tiktok.com/@estecapelli' ),
			),
		);
	}
}

if ( ! function_exists( 'estecapelli_footer_treatments' ) ) {
	/**
	 * Hair-transplant treatment links shown in the footer column 2.
	 * Curated subset of the full mega-menu list (per user selection).
	 */
	function estecapelli_footer_treatments() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'footer_treatments', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}
		return array(
			array( 'label' => __( 'Sapphire FUE Hair Transplant', 'estecapelli' ), 'url' => home_url( '/treatments/sapphire-fue-hair-transplant/' ) ),
			array( 'label' => __( 'Exosome FUE Hair Transplant', 'estecapelli' ),  'url' => home_url( '/treatments/exosome-fue-hair-transplant/' ) ),
			array( 'label' => __( 'DHI Hair Transplant', 'estecapelli' ),          'url' => home_url( '/treatments/dhi-hair-transplant/' ) ),
			array( 'label' => __( 'VITA Treatment', 'estecapelli' ),               'url' => home_url( '/treatments/vita/' ) ),
			array( 'label' => __( 'Female Hair Transplant', 'estecapelli' ),       'url' => home_url( '/treatments/female-hair-transplant/' ) ),
			array( 'label' => __( 'Beard Transplant', 'estecapelli' ),             'url' => home_url( '/treatments/beard-transplant/' ) ),
		);
	}
}

if ( ! function_exists( 'estecapelli_footer_sitemap' ) ) {
	/**
	 * Sitemap-style link group for footer column 3.
	 */
	function estecapelli_footer_sitemap() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'footer_sitemap', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}
		return array(
			array( 'label' => __( 'Home', 'estecapelli' ),             'url' => home_url( '/' ) ),
			array( 'label' => __( 'About Us', 'estecapelli' ),         'url' => home_url( '/about/' ) ),
			array( 'label' => __( 'Our Doctors', 'estecapelli' ),      'url' => home_url( '/doctors/' ) ),
			array( 'label' => __( 'Before & After', 'estecapelli' ),   'url' => home_url( '/before-after/' ) ),
			array( 'label' => __( 'Treatments', 'estecapelli' ),       'url' => home_url( '/treatments/' ) ),
			array( 'label' => __( 'Blog', 'estecapelli' ),             'url' => home_url( '/blog/' ) ),
			array( 'label' => __( 'Contact', 'estecapelli' ),          'url' => home_url( '/contact/' ) ),
		);
	}
}

if ( ! function_exists( 'estecapelli_footer_badges' ) ) {
	/**
	 * Returns paths to certification badge images found in assets/images/badges/.
	 * User drops files into that folder; renderer iterates whatever's present.
	 */
	function estecapelli_footer_badges() {
		$dir = get_template_directory() . '/assets/images/badges';
		$uri = get_template_directory_uri() . '/assets/images/badges';

		if ( ! is_dir( $dir ) ) {
			return array();
		}

		$files = glob( $dir . '/*.{png,jpg,jpeg,webp,svg}', GLOB_BRACE );
		if ( ! $files ) {
			return array();
		}

		sort( $files ); // 01-foo.png, 02-bar.png ... define order via filename prefix
		$badges = array();
		foreach ( $files as $f ) {
			$basename = basename( $f );
			$alt      = ucwords( str_replace( array( '-', '_' ), ' ', pathinfo( $basename, PATHINFO_FILENAME ) ) );
			$alt      = preg_replace( '/^\d+\s+/', '', $alt ); // strip "01 " prefix from alt
			$badges[] = array(
				'src' => $uri . '/' . $basename,
				'alt' => $alt,
			);
		}
		return $badges;
	}
}

if ( ! function_exists( 'estecapelli_megamenu_data' ) ) {
	/**
	 * Hardcoded mega-menu data keyed by parent slug.
	 * Returns null when the key has no mega data (item renders as a simple link).
	 *
	 * Each mega-menu has:
	 *   - columns: an array of columns, each column is an array of items
	 *     each item has: label, url, description, optional badge
	 *   - feature: right-side promo block with image, title, description, CTA
	 */
	function estecapelli_megamenu_data( $key ) {
		$menus = array(
			'hair-transplant' => array(
				'columns' => array(
					array(
						array(
							'label'       => __( 'Exosome FUE Hair Transplant', 'estecapelli' ),
							'url'         => home_url( '/treatments/exosome-fue-hair-transplant/' ),
							'description' => __( 'Supported by cell-regenerating exosomes, it keeps hair follicles alive for longer-lasting density.', 'estecapelli' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Female Hair Transplant', 'estecapelli' ),
							'url'         => home_url( '/treatments/female-hair-transplant/' ),
							'description' => __( 'Special for women, dense and natural-looking hair transplant without shaving.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Hair Mesotherapy', 'estecapelli' ),
							'url'         => home_url( '/treatments/hair-mesotherapy/' ),
							'description' => __( 'A vitamin and mineral injection treatment that revitalizes hair follicles.', 'estecapelli' ),
						),
					),
					array(
						array(
							'label'       => __( 'Sapphire FUE Hair Transplant', 'estecapelli' ),
							'url'         => home_url( '/treatments/sapphire-fue-hair-transplant/' ),
							'description' => __( 'A natural and permanent hair transplant technique where follicles are placed via sapphire blades.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'TrichoLab', 'estecapelli' ),
							'url'         => home_url( '/treatments/tricholab/' ),
							'description' => __( 'Advanced AI-powered hair analysis system that examines the hair and scalp in detail.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Beard Transplant', 'estecapelli' ),
							'url'         => home_url( '/treatments/beard-transplant/' ),
							'description' => __( 'Natural beard and mustache transplantation for sparse or non-existing growth.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Post-Hair Transplant Period', 'estecapelli' ),
							'url'         => home_url( '/treatments/post-hair-transplant-period/' ),
							'description' => __( 'The post-harvest recovery and hair care period.', 'estecapelli' ),
						),
					),
					array(
						array(
							'label'       => __( 'DHI Hair Transplant', 'estecapelli' ),
							'url'         => home_url( '/treatments/dhi-hair-transplant/' ),
							'description' => __( 'A modern hair transplantation method performed with a Choi pen that allows precise placement.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'VITA Treatment', 'estecapelli' ),
							'url'         => home_url( '/treatments/vita/' ),
							'description' => __( "Estecapelli's signature method that revitalizes the scalp and strengthens hair.", 'estecapelli' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Eyebrow Transplant', 'estecapelli' ),
							'url'         => home_url( '/treatments/eyebrow-transplant/' ),
							'description' => __( 'Eyebrow transplantation that gives a naturally curved and full eyebrow shape.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Pre-Hair Transplant Period', 'estecapelli' ),
							'url'         => home_url( '/treatments/pre-hair-transplant-period/' ),
							'description' => __( 'The preparation and analysis process before hair transplantation.', 'estecapelli' ),
						),
					),
				),
				'feature' => array(
					'eyebrow'     => __( 'PATENTED METHOD', 'estecapelli' ),
					'title'       => __( 'Premium Hair Transplant Consultation', 'estecapelli' ),
					'description' => __( 'At Estecapelli we offer patented Exosome FUE hair transplantation — an advanced treatment designed to support the regeneration and vitality of hair follicles.', 'estecapelli' ),
					'cta_label'   => __( 'Hair Transplant Contact', 'estecapelli' ),
					'cta_url'     => home_url( '/contact/?service=hair-transplant' ),
					'video'       => 'mega/hair.mp4',
					'image'       => 'mega/hair.jpg',
					'caption'     => __( 'Enriched by Estecapelli\'s expert staff', 'estecapelli' ),
				),
			),

			'plastic-surgery' => array(
				'columns' => array(
					array(
						array( 'label' => __( 'Rhinoplasty', 'estecapelli' ),     'url' => home_url( '/treatments/rhinoplasty/' ),     'description' => __( 'Nose reshaping surgery that refines proportions and function.', 'estecapelli' ) ),
						array( 'label' => __( 'Tipplasty', 'estecapelli' ),       'url' => home_url( '/treatments/tipplasty/' ),       'description' => __( 'Targeted refinement of the nasal tip only.', 'estecapelli' ) ),
						array( 'label' => __( 'Facelift', 'estecapelli' ),        'url' => home_url( '/treatments/facelift/' ),        'description' => __( 'Restores facial contour and reduces visible signs of aging.', 'estecapelli' ) ),
					),
					array(
						array( 'label' => __( 'BBL (Brazilian Butt Lift)', 'estecapelli' ), 'url' => home_url( '/treatments/bbl/' ),         'description' => __( 'Natural body contouring with fat transfer to the buttocks.', 'estecapelli' ), 'badge' => __( 'POPULAR', 'estecapelli' ) ),
						array( 'label' => __( 'Liposuction', 'estecapelli' ),     'url' => home_url( '/treatments/liposuction/' ),     'description' => __( 'Removes localized fat deposits to reshape the body.', 'estecapelli' ) ),
						array( 'label' => __( 'Tummy Tuck', 'estecapelli' ),      'url' => home_url( '/treatments/tummy-tuck/' ),      'description' => __( 'Flattens and tightens the abdomen for a smoother profile.', 'estecapelli' ) ),
					),
					array(
						array( 'label' => __( 'Breast Aesthetics', 'estecapelli' ),'url' => home_url( '/treatments/breast-aesthetics/' ), 'description' => __( 'Augmentation, lift, and reduction tailored to your goals.', 'estecapelli' ) ),
						array( 'label' => __( 'Gynecomastia', 'estecapelli' ),    'url' => home_url( '/treatments/gynecomastia/' ),    'description' => __( 'Surgical treatment of enlarged male breast tissue.', 'estecapelli' ) ),
					),
				),
				'feature' => array(
					'eyebrow'     => __( 'AESTHETIC EXCELLENCE', 'estecapelli' ),
					'title'       => __( 'Aesthetic Surgery Consultation', 'estecapelli' ),
					'description' => __( 'Board-certified plastic surgeons. Personalized plans, premium hospitals, and recovery support every step of the way.', 'estecapelli' ),
					'cta_label'   => __( 'Plastic Surgery Contact', 'estecapelli' ),
					'cta_url'     => home_url( '/contact/?service=plastic-surgery' ),
					'video'       => 'mega/plastic.mp4',
					'image'       => 'mega/plastic.jpg',
				),
			),

			'dental-treatment' => array(
				'columns' => array(
					array(
						array( 'label' => __( 'Dental Implants', 'estecapelli' ), 'url' => home_url( '/treatments/dental-implants/' ), 'description' => __( 'Permanent replacement for missing teeth with titanium roots.', 'estecapelli' ), 'badge' => __( 'POPULAR', 'estecapelli' ) ),
						array( 'label' => __( 'Smile Design', 'estecapelli' ),    'url' => home_url( '/treatments/smile-design/' ),    'description' => __( 'A bespoke makeover that reshapes your smile aesthetic.', 'estecapelli' ) ),
					),
					array(
						array( 'label' => __( 'Veneers', 'estecapelli' ),         'url' => home_url( '/treatments/veneers/' ),         'description' => __( 'Thin porcelain or composite shells for a flawless front-of-tooth finish.', 'estecapelli' ) ),
						array( 'label' => __( 'Teeth Whitening', 'estecapelli' ), 'url' => home_url( '/treatments/teeth-whitening/' ), 'description' => __( 'Professional bleaching for noticeably brighter teeth.', 'estecapelli' ) ),
					),
				),
				'feature' => array(
					'eyebrow'     => __( 'FULL-MOUTH PLANS', 'estecapelli' ),
					'title'       => __( 'Dental Treatment Consultation', 'estecapelli' ),
					'description' => __( 'World-class dental care in Istanbul — same-day implants, veneers, and smile design with personalized planning.', 'estecapelli' ),
					'cta_label'   => __( 'Dental Contact', 'estecapelli' ),
					'cta_url'     => home_url( '/contact/?service=dental' ),
					'video'       => 'mega/dental.mp4',
					'image'       => 'mega/dental.jpg',
				),
			),

			'about-us' => array(
				'columns' => array(
					array(
						array( 'label' => __( 'About Estecapelli', 'estecapelli' ),'url' => home_url( '/about/' ),         'description' => __( 'Who we are and what drives our clinic forward.', 'estecapelli' ) ),
						array( 'label' => __( 'Our Doctors', 'estecapelli' ),     'url' => home_url( '/doctors/' ),       'description' => __( 'Meet the surgeons leading every procedure.', 'estecapelli' ) ),
					),
					array(
						array( 'label' => __( 'Our Team', 'estecapelli' ),        'url' => home_url( '/team/' ),          'description' => __( 'The full medical and patient-care team behind your treatment.', 'estecapelli' ) ),
						array( 'label' => __( 'Hospital & Facilities', 'estecapelli' ), 'url' => home_url( '/hospital/' ), 'description' => __( 'High-end accredited hospitals in central Istanbul.', 'estecapelli' ) ),
					),
				),
				'feature' => array(
					'eyebrow'     => __( '15+ YEARS OF EXPERIENCE', 'estecapelli' ),
					'title'       => __( 'Trusted by 15,000+ Patients', 'estecapelli' ),
					'description' => __( "From your first message to your follow-up months later — Estecapelli's team is with you across 40+ countries.", 'estecapelli' ),
					'cta_label'   => __( 'Speak with our team', 'estecapelli' ),
					'cta_url'     => home_url( '/contact/' ),
					'video'       => 'mega/about.mp4',
					'image'       => 'mega/about.jpg',
				),
			),
		);

		return $menus[ $key ] ?? null;
	}
}

if ( ! function_exists( 'estecapelli_render_megamenu' ) ) {
	/**
	 * Render the mega-menu HTML for a given parent key.
	 */
	function estecapelli_render_megamenu( $key ) {
		$data = estecapelli_megamenu_data( $key );
		if ( ! $data ) {
			return;
		}
		?>
		<div class="megamenu" role="menu" aria-label="<?php echo esc_attr( $key ); ?>">
			<div class="megamenu__inner">
				<div class="megamenu__cols">
					<?php foreach ( $data['columns'] as $col ) : ?>
						<div class="megamenu__col">
							<?php foreach ( $col as $item ) : ?>
								<a class="megamenu__item" href="<?php echo esc_url( $item['url'] ); ?>" role="menuitem">
									<span class="megamenu__item-head">
										<span class="megamenu__item-label"><?php echo esc_html( $item['label'] ); ?></span>
										<?php if ( ! empty( $item['badge'] ) ) : ?>
											<span class="megamenu__item-badge"><?php echo esc_html( $item['badge'] ); ?></span>
										<?php endif; ?>
									</span>
									<?php if ( ! empty( $item['description'] ) ) : ?>
										<span class="megamenu__item-desc"><?php echo esc_html( $item['description'] ); ?></span>
									<?php endif; ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( ! empty( $data['feature'] ) ) : $f = $data['feature']; ?>
					<?php
					$base_dir = get_template_directory() . '/assets/images/';
					$base_uri = get_template_directory_uri() . '/assets/images/';

					$video_url = '';
					if ( ! empty( $f['video'] ) && file_exists( $base_dir . $f['video'] ) ) {
						$video_url = $base_uri . $f['video'];
					}

					$image_url = '';
					if ( ! empty( $f['image'] ) && file_exists( $base_dir . $f['image'] ) ) {
						$image_url = $base_uri . $f['image'];
					}

					$has_media = ( $video_url || $image_url );
					?>
					<div class="megamenu__feature">
						<div class="megamenu__feature-art <?php echo $has_media ? 'megamenu__feature-art--has-media' : ''; ?>" aria-hidden="true">
							<?php if ( $video_url ) : ?>
								<video class="megamenu__feature-media" autoplay muted loop playsinline preload="metadata"<?php echo $image_url ? ' poster="' . esc_url( $image_url ) . '"' : ''; ?>>
									<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4" />
								</video>
							<?php elseif ( $image_url ) : ?>
								<img class="megamenu__feature-media" src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy" />
							<?php endif; ?>
							<?php if ( $has_media && ! empty( $f['caption'] ) ) : ?>
								<span class="megamenu__feature-caption"><?php echo esc_html( $f['caption'] ); ?></span>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $f['eyebrow'] ) ) : ?>
							<span class="megamenu__feature-eyebrow"><?php echo esc_html( $f['eyebrow'] ); ?></span>
						<?php endif; ?>
						<h3 class="megamenu__feature-title"><?php echo esc_html( $f['title'] ); ?></h3>
						<p class="megamenu__feature-desc"><?php echo esc_html( $f['description'] ); ?></p>
						<a class="btn btn-primary btn-sm megamenu__feature-cta" href="<?php echo esc_url( $f['cta_url'] ); ?>">
							<?php echo esc_html( $f['cta_label'] ); ?>
							<?php estecapelli_icon( 'arrow-right', array( 'width' => 14, 'height' => 14 ) ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'estecapelli_primary_menu_fallback' ) ) {
	/**
	 * Fallback nav shown when no menu is assigned to the "primary" location.
	 * Renders top-level items and mega-menus for items keyed in
	 * estecapelli_megamenu_data().
	 */
	function estecapelli_primary_menu_fallback() {
		$items = array(
			array( 'label' => __( 'Hair Transplant', 'estecapelli' ),  'url' => home_url( '/hair-transplant/' ),  'mega' => 'hair-transplant' ),
			array( 'label' => __( 'Plastic Surgery', 'estecapelli' ),  'url' => home_url( '/plastic-surgery/' ),  'mega' => 'plastic-surgery' ),
			array( 'label' => __( 'Dental Treatment', 'estecapelli' ), 'url' => home_url( '/dental-treatment/' ), 'mega' => 'dental-treatment' ),
			array( 'label' => __( 'Exosome Treatment', 'estecapelli' ),'url' => home_url( '/exosome-treatment/' ),'badge' => __( 'NEW', 'estecapelli' ) ),
			array( 'label' => __( 'Before & After', 'estecapelli' ),   'url' => home_url( '/before-after/' ) ),
			array( 'label' => __( 'About Us', 'estecapelli' ),         'url' => home_url( '/about/' ),            'mega' => 'about-us' ),
			array( 'label' => __( 'Blog', 'estecapelli' ),             'url' => home_url( '/blog/' ) ),
			array( 'label' => __( 'Contact', 'estecapelli' ),          'url' => home_url( '/contact/' ) ),
		);

		echo '<ul class="site-nav__list">';
		foreach ( $items as $item ) {
			$has_mega = ! empty( $item['mega'] ) && estecapelli_megamenu_data( $item['mega'] );

			$badge = ! empty( $item['badge'] )
				? sprintf( '<span class="nav-badge">%s</span>', esc_html( $item['badge'] ) )
				: '';

			$chevron = $has_mega
				? '<svg class="chev" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true" focusable="false"><path d="M2 4 L6 8 L10 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>'
				: '';

			$li_class = $has_mega ? ' class="has-megamenu"' : '';

			printf(
				'<li%1$s><a href="%2$s">%3$s%4$s%5$s</a>',
				$li_class, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_url( $item['url'] ),
				esc_html( $item['label'] ),
				$badge, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$chevron // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);

			if ( $has_mega ) {
				estecapelli_render_megamenu( $item['mega'] );
			}

			echo '</li>';
		}
		echo '</ul>';
	}
}

if ( ! function_exists( 'estecapelli_brand_mark' ) ) {
	/**
	 * Render the brand mark (logo image if present, else styled text wordmark).
	 * Picks the first available file from /assets/images/ in this order:
	 *   logo.svg, logo-horizontal.svg, logo.png, logo-horizontal.png
	 *
	 * @param string $context 'header' or 'footer'.
	 */
	function estecapelli_brand_mark( $context = 'header' ) {
		$base_dir   = get_template_directory() . '/assets/images/';
		$base_uri   = get_template_directory_uri() . '/assets/images/';

		$candidates = array( 'logo.webp', 'logo.png' );

		$logo_url = '';
		foreach ( $candidates as $file ) {
			if ( file_exists( $base_dir . $file ) ) {
				$logo_url = $base_uri . $file;
				break;
			}
		}

		$img_class  = ( 'footer' === $context ) ? 'site-footer__logo' : 'brand-logo';
		$text_class = ( 'footer' === $context ) ? 'site-footer__wordmark' : 'brand-wordmark';

		if ( $logo_url ) {
			$is_footer = ( 'footer' === $context );
			printf(
				'<img class="%1$s" src="%2$s" alt="%3$s" width="%4$d" height="%5$d" />',
				esc_attr( $img_class ),
				esc_url( $logo_url ),
				esc_attr( get_bloginfo( 'name' ) ),
				$is_footer ? 210 : 170,
				$is_footer ? 60  : 48
			);
			return;
		}

		printf(
			'<span class="%1$s"><span>Este</span><span>capelli</span></span>',
			esc_attr( $text_class )
		);
	}
}
