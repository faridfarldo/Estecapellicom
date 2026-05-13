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
			'whatsapp'     => '<path d="M20.5 3.5a10.4 10.4 0 0 0-17.5 10.5L2 22l8.2-1.5A10.4 10.4 0 1 0 20.5 3.5Z"/><path d="M8.5 8.5c.5-.3 1.4-.3 1.8.3l.7 1.5c.3.7 0 1.1-.4 1.5l-.5.5c.6 1.3 1.4 2.1 2.7 2.7l.5-.5c.4-.4.8-.7 1.5-.4l1.5.7c.6.4.6 1.3.3 1.8-.9 1.6-3.3 1.8-5.6.1-1.8-1.3-3-2.5-4.3-4.3-1.7-2.3-1.5-4.7.1-5.6Z" fill="currentColor" stroke="none"/>',
			'phone'        => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.37 1.9.72 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.35 1.85.59 2.81.72A2 2 0 0 1 22 16.92Z"/>',
			'globe'        => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
			'chevron-down' => '<polyline points="6 9 12 15 18 9"/>',
			'star'         => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="currentColor" stroke="none"/>',
			'menu'         => '<line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/>',
			'close'        => '<line x1="6" y1="6" x2="18" y2="18"/><line x1="6" y1="18" x2="18" y2="6"/>',
			'arrow-right'  => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
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

		$candidates = ( 'footer' === $context )
			? array( 'logo-white.svg', 'logo-white.png', 'logo.svg', 'logo.webp', 'logo.png' )
			: array( 'logo.svg', 'logo-horizontal.svg', 'logo.webp', 'logo.png', 'logo-horizontal.png' );

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
			printf(
				'<img class="%1$s" src="%2$s" alt="%3$s" width="240" height="74" />',
				esc_attr( $img_class ),
				esc_url( $logo_url ),
				esc_attr( get_bloginfo( 'name' ) )
			);
			return;
		}

		printf(
			'<span class="%1$s"><span>Este</span><span>capelli</span></span>',
			esc_attr( $text_class )
		);
	}
}
