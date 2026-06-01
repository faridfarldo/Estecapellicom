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
			'hair'         => '<path d="M3 21h18"/><path d="M6 21V13"/><path d="M10 21V8"/><path d="M14 21V6"/><path d="M18 21V11"/>',
			'face'         => '<path d="M12 3c4.5 0 8 3.5 8 8 0 3-1 5-1 7v2c0 1-1 2-2 2H9c-1 0-2-1-2-2v-2c0-2-3-4-3-7 0-4.5 3.5-8 8-8z"/><path d="M9 10h.01"/><path d="M15 10h.01"/><path d="M9.5 14c1 1 4 1 5 0"/>',
			'tooth'        => '<path d="M8 3C5.5 3 4 4.5 4 7.5c0 3 .8 6 1.8 9 .5 1.5 1 4 2.2 4 1 0 1.3-2 1.7-4 .3-1.5.8-2.5 2.3-2.5s2 1 2.3 2.5c.4 2 .7 4 1.7 4 1.2 0 1.7-2.5 2.2-4 1-3 1.8-6 1.8-9C20 4.5 18.5 3 16 3c-1.5 0-2.5.7-4 .7S9.5 3 8 3z"/>',
			'medical-plus' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10"/><path d="M7 12h10"/>',
			'bed'          => '<path d="M3 18v-7a2 2 0 0 1 2-2h11a4 4 0 0 1 4 4v5"/><path d="M3 14h18"/><path d="M3 18v3"/><path d="M21 18v3"/><circle cx="8" cy="11.5" r="1.5"/>',
			'car'          => '<path d="M5 17h14"/><path d="M5 17v-4l2-5a2 2 0 0 1 1.8-1.2h6.4A2 2 0 0 1 17 8l2 5v4"/><circle cx="8" cy="17.5" r="1.6"/><circle cx="16" cy="17.5" r="1.6"/>',
			'languages'    => '<path d="M4 5h7"/><path d="M9 3v2c0 4.4-3 7-6 7"/><path d="M5 9c0 2.5 3 5 7 6"/><path d="M13 21l4-9 4 9"/><path d="M14.5 17h5"/>',
			'sparkles'     => '<path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5z" fill="currentColor" stroke="none"/><path d="M19 14l.7 2L22 17l-2.3.7L19 20l-.7-2.3L16 17l2.3-.7z" fill="currentColor" stroke="none"/>',
			'target'       => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.4" fill="currentColor" stroke="none"/>',
			'atom'         => '<circle cx="12" cy="12" r="1.5" fill="currentColor" stroke="none"/><ellipse cx="12" cy="12" rx="10" ry="4"/><ellipse cx="12" cy="12" rx="10" ry="4" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="10" ry="4" transform="rotate(120 12 12)"/>',
			'dna'          => '<path d="M5 4c0 4 14 4 14 8"/><path d="M5 20c0-4 14-4 14-8"/><path d="M7 6h10"/><path d="M7 18h10"/><path d="M9 10h6"/><path d="M9 14h6"/>',
			'shield-check' => '<path d="M12 3l8 3v5c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6z"/><polyline points="9 12 11.5 14.5 16 10"/>',
			'check-circle' => '<circle cx="12" cy="12" r="10"/><polyline points="8 12.5 11 15.5 16 9.5"/>',
			'x-circle'     => '<circle cx="12" cy="12" r="10"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/>',
			'headset'      => '<path d="M4 15v-3a8 8 0 0 1 16 0v3"/><path d="M4 15h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1z"/><path d="M20 15h-2a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h1a3 3 0 0 0 3-3z"/>',
			'image'        => '<rect x="3" y="4" width="18" height="16" rx="2.5"/><circle cx="9" cy="10" r="1.6"/><polyline points="3 17 9 12 13 16 17 12 21 16"/>',
			'clipboard'    => '<rect x="6" y="4" width="12" height="17" rx="2"/><path d="M9 4h6v3h-6z" fill="currentColor" stroke="none"/><polyline points="9 13 11 15 15 11"/>',
			'plane'        => '<path d="M21 13l-9-1.5-3-7.5h-2l1.5 8L3 13.5v1.5l6-1.5 1.5 5h1.5l1-5L21 12z" fill="currentColor" stroke="none"/>',
			'hands-heart'  => '<path d="M12 8.5l-1.2-1.3a2.2 2.2 0 1 0-3.1 3.1L12 14.8l4.3-4.5a2.2 2.2 0 1 0-3.1-3.1L12 8.5z" fill="currentColor" stroke="none"/><path d="M4 17l4-1 4 4 4-4 4 1"/><path d="M3 21h18"/>',
			'quote'        => '<path d="M7 7h4v4H7zm0 4c0 3-2 5-4 5M17 7h4v4h-4zm0 4c0 3-2 5-4 5" fill="currentColor" stroke="none"/>',
			'play'         => '<polygon points="7 5 19 12 7 19 7 5" fill="currentColor" stroke="none"/>',
			'chevron-right' => '<polyline points="9 6 15 12 9 18"/>',
			'chevron-left'  => '<polyline points="15 6 9 12 15 18"/>',
			'building'      => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><line x1="8" y1="7" x2="10" y2="7"/><line x1="14" y1="7" x2="16" y2="7"/><line x1="8" y1="11" x2="10" y2="11"/><line x1="14" y1="11" x2="16" y2="11"/><line x1="8" y1="15" x2="10" y2="15"/><line x1="14" y1="15" x2="16" y2="15"/>',
			'wifi'          => '<path d="M2 9c5-5 15-5 20 0"/><path d="M5 12c4-3 10-3 14 0"/><path d="M8 15c2-1.5 6-1.5 8 0"/><circle cx="12" cy="18" r="1" fill="currentColor" stroke="none"/>',
			'utensils'      => '<path d="M7 2v10a2 2 0 0 1-2 2H4"/><path d="M11 2v20"/><path d="M5 2v8"/><path d="M9 2v8"/><path d="M17 8a4 4 0 0 1 4 4v3h-4v7"/>',
			'concierge'     => '<circle cx="12" cy="7" r="3"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/><line x1="3" y1="21" x2="21" y2="21"/>',
			'calendar'      => '<rect x="3" y="5" width="18" height="16" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="3" x2="8" y2="7"/><line x1="16" y1="3" x2="16" y2="7"/>',
			'tag'           => '<path d="M3 12V4a1 1 0 0 1 1-1h8l9 9-9 9z"/><circle cx="8" cy="8" r="1.5" fill="currentColor" stroke="none"/>',
			'book-open'     => '<path d="M12 5v16"/><path d="M3 5a14 14 0 0 1 9 2v14a14 14 0 0 0-9-2z"/><path d="M21 5a14 14 0 0 0-9 2v14a14 14 0 0 1 9-2z"/>',
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

if ( ! function_exists( 'estecapelli_youtube_id' ) ) {
	/**
	 * Extract the 11-character YouTube video ID from any common URL form,
	 * or return the input unchanged if it is already a bare ID.
	 *
	 * Accepts: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID,
	 * youtube.com/shorts/ID, with or without extra query params — and a
	 * plain ID pasted on its own.
	 *
	 * @param string $input URL or video ID.
	 * @return string The video ID, or '' if none could be found.
	 */
	function estecapelli_youtube_id( $input ) {
		$input = trim( (string) $input );
		if ( '' === $input ) {
			return '';
		}

		// Already a bare ID (no slashes, no dots) — accept as-is.
		if ( preg_match( '/^[A-Za-z0-9_-]{11}$/', $input ) ) {
			return $input;
		}

		if ( preg_match( '~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $input, $m ) ) {
			return $m[1];
		}

		return '';
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
							'url'         => home_url( '/en/hair-transplant/exosome-fue-hair-transplant/' ),
							'description' => __( 'Supported by cell-regenerating exosomes, it keeps hair follicles alive for longer-lasting density.', 'estecapelli' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Female Hair Transplant', 'estecapelli' ),
							'url'         => home_url( '/en/hair-transplant/female-hair-transplant/' ),
							'description' => __( 'Special for women, dense and natural-looking hair transplant without shaving.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Hair Mesotherapy', 'estecapelli' ),
							'url'         => home_url( '/en/hair-transplant/hair-mesotherapy/' ),
							'description' => __( 'A vitamin and mineral injection treatment that revitalizes hair follicles.', 'estecapelli' ),
						),
					),
					array(
						array(
							'label'       => __( 'Sapphire FUE Hair Transplant', 'estecapelli' ),
							'url'         => home_url( '/en/hair-transplant/sapphire-fue-hair-transplant/' ),
							'description' => __( 'A natural and permanent hair transplant technique where follicles are placed via sapphire blades.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'TrichoLab', 'estecapelli' ),
							'url'         => home_url( '/en/hair-transplant/tricholab/' ),
							'description' => __( 'Advanced AI-powered hair analysis system that examines the hair and scalp in detail.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Beard Transplant', 'estecapelli' ),
							'url'         => home_url( '/en/hair-transplant/beard-transplant/' ),
							'description' => __( 'Natural beard and mustache transplantation for sparse or non-existing growth.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Post-Hair Transplant Period', 'estecapelli' ),
							'url'         => home_url( '/en/hair-transplant/post-hair-transplant-period/' ),
							'description' => __( 'The post-harvest recovery and hair care period.', 'estecapelli' ),
						),
					),
					array(
						array(
							'label'       => __( 'DHI Hair Transplant', 'estecapelli' ),
							'url'         => home_url( '/en/hair-transplant/dhi-hair-transplant/' ),
							'description' => __( 'A modern hair transplantation method performed with a Choi pen that allows precise placement.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'VITA Treatment', 'estecapelli' ),
							'url'         => home_url( '/en/hair-transplant/vita-treatment/' ),
							'description' => __( "Estecapelli's signature method that revitalizes the scalp and strengthens hair.", 'estecapelli' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Eyebrow Transplant', 'estecapelli' ),
							'url'         => home_url( '/en/hair-transplant/eyebrow-transplant/' ),
							'description' => __( 'Eyebrow transplantation that gives a naturally curved and full eyebrow shape.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Pre-Hair Transplant Period', 'estecapelli' ),
							'url'         => home_url( '/en/hair-transplant/pre-hair-transplant-period/' ),
							'description' => __( 'The preparation and analysis process before hair transplantation.', 'estecapelli' ),
						),
					),
				),
				'feature' => array(
					'eyebrow'     => __( 'PATENTED METHOD', 'estecapelli' ),
					'title'       => __( 'Premium Hair Transplant Consultation', 'estecapelli' ),
					'description' => __( 'At Estecapelli we offer patented Exosome FUE hair transplantation — an advanced treatment designed to support the regeneration and vitality of hair follicles.', 'estecapelli' ),
					'cta_label'   => __( 'Hair Transplant Contact', 'estecapelli' ),
					'cta_url'     => home_url( '/en/contact/' ),
					'video'       => 'mega/hair.mp4',
					'image'       => 'mega/hair.jpg',
					'caption'     => __( 'Enriched by Estecapelli\'s expert staff', 'estecapelli' ),
				),
			),

			'plastic-surgery' => array(
				'columns' => array(
					array(
						array( 'label' => __( 'Rhinoplasty', 'estecapelli' ),               'url' => home_url( '/en/plastic-surgery/rhinoplasty/' ),                                         'description' => __( 'Nose reshaping surgery that refines proportions and function.', 'estecapelli' ) ),
						array( 'label' => __( 'BBL (Brazilian Butt Lift)', 'estecapelli' ), 'url' => home_url( '/en/plastic-surgery/bbl/' ),                                                 'description' => __( 'Natural body contouring with fat transfer to the buttocks.', 'estecapelli' ), 'badge' => __( 'POPULAR', 'estecapelli' ) ),
						array( 'label' => __( 'Liposuction', 'estecapelli' ),               'url' => home_url( '/en/plastic-surgery/liposuction/' ),                                         'description' => __( 'Removes localized fat deposits to reshape the body.', 'estecapelli' ) ),
					),
					array(
						array( 'label' => __( 'Breast Aesthetics', 'estecapelli' ),         'url' => home_url( '/en/plastic-surgery/breast-aesthetics-breast-surgery/' ),                    'description' => __( 'Augmentation, lift, and reduction tailored to your goals.', 'estecapelli' ) ),
						array( 'label' => __( 'Abdominoplasty (Tummy Tuck)', 'estecapelli' ),'url' => home_url( '/en/plastic-surgery/abdominoplasty-tummy-tuck/' ),                          'description' => __( 'Flattens and tightens the abdomen for a smoother profile.', 'estecapelli' ) ),
						array( 'label' => __( 'Gynecomastia', 'estecapelli' ),              'url' => home_url( '/en/plastic-surgery/gynecomastia/' ),                                        'description' => __( 'Surgical treatment of enlarged male breast tissue.', 'estecapelli' ) ),
					),
					array(
						array( 'label' => __( 'Face & Neck Lift Surgery', 'estecapelli' ),  'url' => home_url( '/en/plastic-surgery/face-and-neck-lift-surgery/' ),                          'description' => __( 'Restores facial contour and reduces visible signs of aging.', 'estecapelli' ) ),
						array( 'label' => __( 'Obesity Surgeries (Bariatric)', 'estecapelli' ),'url' => home_url( '/en/plastic-surgery/obesity-surgeries-bariatric-surgery-and-gastric-balloon/' ), 'description' => __( 'Bariatric surgery and gastric balloon for sustainable weight loss.', 'estecapelli' ) ),
					),
				),
				'feature' => array(
					'eyebrow'     => __( 'AESTHETIC EXCELLENCE', 'estecapelli' ),
					'title'       => __( 'Aesthetic Surgery Consultation', 'estecapelli' ),
					'description' => __( 'Board-certified plastic surgeons. Personalized plans, premium hospitals, and recovery support every step of the way.', 'estecapelli' ),
					'cta_label'   => __( 'Plastic Surgery Contact', 'estecapelli' ),
					'cta_url'     => home_url( '/en/contact/' ),
					'video'       => 'mega/plastic.mp4',
					'image'       => 'mega/plastic.jpg',
				),
			),

			'dental-treatment' => array(
				'columns' => array(
					array(
						array( 'label' => __( 'Dental Implant', 'estecapelli' ),  'url' => home_url( '/en/dental-treatment/dental-implant/' ),  'description' => __( 'Permanent replacement for missing teeth with titanium roots.', 'estecapelli' ), 'badge' => __( 'POPULAR', 'estecapelli' ) ),
						array( 'label' => __( 'Hollywood Smile', 'estecapelli' ), 'url' => home_url( '/en/dental-treatment/hollywood-smile/' ), 'description' => __( 'A bespoke makeover that reshapes your smile aesthetic.', 'estecapelli' ) ),
					),
				),
				'feature' => array(
					'eyebrow'     => __( 'FULL-MOUTH PLANS', 'estecapelli' ),
					'title'       => __( 'Dental Treatment Consultation', 'estecapelli' ),
					'description' => __( 'World-class dental care in Istanbul — same-day implants, veneers, and smile design with personalized planning.', 'estecapelli' ),
					'cta_label'   => __( 'Dental Contact', 'estecapelli' ),
					'cta_url'     => home_url( '/en/contact/' ),
					'video'       => 'mega/dental.mp4',
					'image'       => 'mega/dental.jpg',
				),
			),

			'about-us' => array(
				'columns' => array(
					array(
						array( 'label' => __( 'About Estecapelli', 'estecapelli' ),'url' => home_url( '/en/about-us/' ),            'description' => __( 'Who we are and what drives our clinic forward.', 'estecapelli' ) ),
						array( 'label' => __( 'Our Doctors', 'estecapelli' ),     'url' => home_url( '/en/about-us/our-doctors/' ), 'description' => __( 'Meet the surgeons leading every procedure.', 'estecapelli' ) ),
					),
					array(
						array( 'label' => __( 'Our Team', 'estecapelli' ),        'url' => home_url( '/en/about-us/our-team/' ),    'description' => __( 'The full medical and patient-care team behind your treatment.', 'estecapelli' ) ),
					),
				),
				'feature' => array(
					'eyebrow'     => __( '15+ YEARS OF EXPERIENCE', 'estecapelli' ),
					'title'       => __( 'Trusted by 15,000+ Patients', 'estecapelli' ),
					'description' => __( "From your first message to your follow-up months later — Estecapelli's team is with you across 40+ countries.", 'estecapelli' ),
					'cta_label'   => __( 'Speak with our team', 'estecapelli' ),
					'cta_url'     => home_url( '/en/contact/' ),
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
		<div class="megamenu" role="menu" aria-label="<?php echo esc_attr( $key ); ?>" data-mega-key="<?php echo esc_attr( $key ); ?>">
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
			array( 'label' => __( 'Hair Transplant', 'estecapelli' ),  'url' => home_url( '/en/hair-transplant/' ),  'mega' => 'hair-transplant' ),
			array( 'label' => __( 'Plastic Surgery', 'estecapelli' ),  'url' => home_url( '/en/plastic-surgery/' ),  'mega' => 'plastic-surgery' ),
			array( 'label' => __( 'Dental Treatment', 'estecapelli' ), 'url' => home_url( '/en/dental-treatment/' ), 'mega' => 'dental-treatment' ),
			array( 'label' => __( 'Before & After', 'estecapelli' ),   'url' => home_url( '/en/before-after/' ) ),
			array( 'label' => __( 'About Us', 'estecapelli' ),         'url' => home_url( '/en/about-us/' ),         'mega' => 'about-us' ),
			array( 'label' => __( 'Blog', 'estecapelli' ),             'url' => home_url( '/en/blog/' ) ),
			array( 'label' => __( 'Contact', 'estecapelli' ),          'url' => home_url( '/en/contact/' ) ),
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

if ( ! function_exists( 'estecapelli_home_hero' ) ) {
	/**
	 * Homepage hero content (ACF override-ready).
	 * Returns a single associative array that hero-home.php iterates over —
	 * any field can later be overridden via an ACF option page without
	 * touching the renderer.
	 */
	function estecapelli_home_hero() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'home_hero', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}

		return array(
			'eyebrow'    => __( 'EST. 2010 · ISTANBUL, TÜRKİYE', 'estecapelli' ),
			'headline'   => __( 'Aesthetic Excellence, Backed by Medical Trust.', 'estecapelli' ),
			'body'       => __( "From hair restoration to plastic, dental and non-surgical aesthetics — Estecapelli's board-certified doctors deliver hospital-grade care with the precision your transformation deserves.", 'estecapelli' ),
			'rating'     => array(
				'trustpilot_label' => __( 'Excellent', 'estecapelli' ),
				'trustpilot_count' => __( 'on Trustpilot', 'estecapelli' ),
				'google_score'     => '4.9',
				'google_count'     => __( 'Google Reviews', 'estecapelli' ),
			),
			'certs'      => array(
				'image'   => get_template_directory_uri() . '/assets/images/certs.png',
				'alt'     => __( 'Internationally accredited — Ministry of Health, HRSA, NACo, ISO 13485, Certified Medical Travel Agent', 'estecapelli' ),
				'caption' => __( 'Internationally Accredited & Certified', 'estecapelli' ),
			),
			'cta_primary' => array(
				'label' => __( 'Free Consultation', 'estecapelli' ),
				'url'   => home_url( '/contact/' ),
			),
			'cta_whatsapp' => array(
				'label' => __( 'Chat on WhatsApp', 'estecapelli' ),
				'url'   => estecapelli_whatsapp_url( __( 'Hello Estecapelli, I would like to book a free consultation.', 'estecapelli' ) ),
			),
			'doctor'     => array(
				'image'    => get_template_directory_uri() . '/assets/images/doctors/mehmet-hanifi-kutlar.jpg',
				'name'     => __( 'Dr. Mehmet Hanifi Kutlar', 'estecapelli' ),
				'role'     => __( 'Medical Director & Co-founder', 'estecapelli' ),
				'subline'  => __( '15+ years in aesthetic medicine', 'estecapelli' ),
			),
			'credential' => array(
				'eyebrow' => __( 'ACCREDITED CLINIC', 'estecapelli' ),
				'lines'   => array(
					__( 'Ministry of Health Licensed', 'estecapelli' ),
					__( 'ISO 9001 · TURSAB Certified', 'estecapelli' ),
				),
			),
		);
	}
}

if ( ! function_exists( 'estecapelli_trust_stats' ) ) {
	/**
	 * Quiet trust-stats strip rendered below the hero.
	 * Intentionally minimal: 4 metrics inline, no icons, no cards.
	 * ACF override: option 'trust_stats' returns an array of {value,label}.
	 */
	function estecapelli_trust_stats() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'trust_stats', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}

		return array(
			array( 'value' => '15+',                                   'label' => __( 'Years of Experience', 'estecapelli' ) ),
			array( 'value' => '+' . ESTECAPELLI_PATIENT_COUNT,         'label' => __( 'Happy Patients', 'estecapelli' ) ),
			array( 'value' => '+' . ESTECAPELLI_COUNTRY_COUNT,         'label' => __( 'Countries Served', 'estecapelli' ) ),
			array( 'value' => '24/7',                                  'label' => __( 'Support Team', 'estecapelli' ) ),
		);
	}
}

if ( ! function_exists( 'estecapelli_home_services' ) ) {
	/**
	 * Tabbed featured-services block for the homepage.
	 * Tab buttons map to category keys used in estecapelli_megamenu_data();
	 * each tab carries 4 cards. Hair Transplant cards have curated photos;
	 * the other categories fall back to icon-only cards until photos arrive.
	 *
	 * ACF override: option 'home_services' returns the full payload below.
	 */
	function estecapelli_home_services() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'home_services', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}

		$img = get_template_directory_uri() . '/assets/images/services/';

		$hair_tag    = __( 'Hair Transplant', 'estecapelli' );
		$plastic_tag = __( 'Plastic Surgery', 'estecapelli' );
		$dental_tag  = __( 'Dental Treatment', 'estecapelli' );
		$medical_tag = __( 'Medical Treatment', 'estecapelli' );

		return array(
			'eyebrow'    => __( 'WHAT WE TREAT', 'estecapelli' ),
			'headline'   => __( 'Pick a field. See our signature treatments.', 'estecapelli' ),
			'lead'       => __( 'Switch between tabs to explore the methods we are best known for in each field of care.', 'estecapelli' ),
			'categories' => array(
				array(
					'key'   => 'hair-transplant',
					'icon'  => 'hair',
					'label' => $hair_tag,
					'items' => array(
						array(
							'tag'         => $hair_tag,
							'title'       => __( 'Micro Sapphire FUE', 'estecapelli' ),
							'description' => __( 'Sapphire-blade precision for natural density and faster healing.', 'estecapelli' ),
							'image'       => $img . 'sapphire-fue.jpg',
							'url'         => home_url( '/treatments/sapphire-fue-hair-transplant/' ),
						),
						array(
							'tag'         => $hair_tag,
							'title'       => __( 'DHI', 'estecapelli' ),
							'description' => __( 'Choi-pen implantation for precise angle and direction control.', 'estecapelli' ),
							'image'       => $img . 'dhi.jpg',
							'url'         => home_url( '/treatments/dhi-hair-transplant/' ),
						),
						array(
							'tag'         => $hair_tag,
							'title'       => __( 'Exosome Treatment', 'estecapelli' ),
							'description' => __( 'Cell-regenerating exosomes that keep follicles alive longer.', 'estecapelli' ),
							'image'       => $img . 'exosome.jpg',
							'url'         => home_url( '/treatments/exosome-fue-hair-transplant/' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'tag'         => $hair_tag,
							'title'       => __( 'VITA Treatment', 'estecapelli' ),
							'description' => __( "Estecapelli's signature protocol that revitalises scalp & strands.", 'estecapelli' ),
							'image'       => $img . 'vita.jpg',
							'url'         => home_url( '/treatments/vita/' ),
							'badge'       => __( 'SIGNATURE', 'estecapelli' ),
						),
					),
				),
				array(
					'key'   => 'plastic-surgery',
					'icon'  => 'face',
					'label' => $plastic_tag,
					'items' => array(
						array(
							'tag'         => $plastic_tag,
							'title'       => __( 'Rhinoplasty', 'estecapelli' ),
							'description' => __( 'Nose reshaping that refines proportions and function.', 'estecapelli' ),
							'url'         => home_url( '/treatments/rhinoplasty/' ),
						),
						array(
							'tag'         => $plastic_tag,
							'title'       => __( 'BBL', 'estecapelli' ),
							'description' => __( 'Brazilian Butt Lift — natural contouring with fat transfer.', 'estecapelli' ),
							'url'         => home_url( '/treatments/bbl/' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'tag'         => $plastic_tag,
							'title'       => __( 'Facelift', 'estecapelli' ),
							'description' => __( 'Restores facial contour and reduces visible signs of aging.', 'estecapelli' ),
							'url'         => home_url( '/treatments/facelift/' ),
						),
						array(
							'tag'         => $plastic_tag,
							'title'       => __( 'Breast Aesthetics', 'estecapelli' ),
							'description' => __( 'Augmentation, lift and reduction tailored to your goals.', 'estecapelli' ),
							'url'         => home_url( '/treatments/breast-aesthetics/' ),
						),
					),
				),
				array(
					'key'   => 'dental-treatment',
					'icon'  => 'tooth',
					'label' => $dental_tag,
					'items' => array(
						array(
							'tag'         => $dental_tag,
							'title'       => __( 'Dental Implants', 'estecapelli' ),
							'description' => __( 'Permanent replacement for missing teeth with titanium roots.', 'estecapelli' ),
							'url'         => home_url( '/treatments/dental-implants/' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'tag'         => $dental_tag,
							'title'       => __( 'Smile Design', 'estecapelli' ),
							'description' => __( 'A bespoke makeover that reshapes your entire smile.', 'estecapelli' ),
							'url'         => home_url( '/treatments/smile-design/' ),
						),
						array(
							'tag'         => $dental_tag,
							'title'       => __( 'Veneers', 'estecapelli' ),
							'description' => __( 'Thin porcelain shells for a flawless front-of-tooth finish.', 'estecapelli' ),
							'url'         => home_url( '/treatments/veneers/' ),
						),
						array(
							'tag'         => $dental_tag,
							'title'       => __( 'Teeth Whitening', 'estecapelli' ),
							'description' => __( 'Professional bleaching for noticeably brighter teeth.', 'estecapelli' ),
							'url'         => home_url( '/treatments/teeth-whitening/' ),
						),
					),
				),
				array(
					'key'   => 'medical-treatment',
					'icon'  => 'medical-plus',
					'label' => $medical_tag,
					'items' => array(
						array(
							'tag'         => $medical_tag,
							'title'       => __( 'Botox', 'estecapelli' ),
							'description' => __( 'Smooths expression lines for a refreshed, rested look.', 'estecapelli' ),
							'url'         => home_url( '/treatments/botox/' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'tag'         => $medical_tag,
							'title'       => __( 'Dermal Fillers', 'estecapelli' ),
							'description' => __( 'Restores volume to cheeks, jawline and under-eye areas.', 'estecapelli' ),
							'url'         => home_url( '/treatments/dermal-fillers/' ),
						),
						array(
							'tag'         => $medical_tag,
							'title'       => __( 'PRP Treatment', 'estecapelli' ),
							'description' => __( 'Platelet-rich plasma therapy for skin and hair regeneration.', 'estecapelli' ),
							'url'         => home_url( '/treatments/prp-treatment/' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'tag'         => $medical_tag,
							'title'       => __( 'Skin Rejuvenation', 'estecapelli' ),
							'description' => __( 'Non-surgical protocols for firmer, brighter, healthier skin.', 'estecapelli' ),
							'url'         => home_url( '/treatments/skin-rejuvenation/' ),
						),
					),
				),
			),
		);
	}
}

if ( ! function_exists( 'estecapelli_why_choose' ) ) {
	/**
	 * Homepage "Why Choose Estecapelli" — comparison verdict board.
	 *
	 * Returns a single payload the renderer iterates; can be overridden via
	 * ACF option 'home_why_choose' without touching the template.
	 */
	function estecapelli_why_choose() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'home_why_choose', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}

		return array(
			'eyebrow'  => __( 'The Comparison', 'estecapelli' ),
			'headline' => __( 'Global standards, Turkish expertise.', 'estecapelli' ),
			'lead'     => __( 'See how our comprehensive approach and advanced techniques set us apart from other clinics.', 'estecapelli' ),

			'intro'    => array(
				'title'   => __( 'Built for patients who refuse to compromise.', 'estecapelli' ),
				'body'    => __( 'At Estecapelli, we go beyond expectations to deliver personalised, high-quality care backed by years of experience and international standards. Our expert team combines innovative methods with a patient-first approach to deliver results that look natural and feel confident.', 'estecapelli' ),
				'caption' => __( 'Experience the Estecapelli difference — advanced techniques, patient-focused care.', 'estecapelli' ),
				'cta'     => array(
					'label' => __( 'Get a Free Consultation', 'estecapelli' ),
					'url'   => home_url( '/contact/' ),
				),
			),

			'us_label'   => __( 'Estecapelli', 'estecapelli' ),
			'them_label' => __( 'Other Clinics', 'estecapelli' ),
			'us_tag'     => __( 'Premium Care', 'estecapelli' ),
			'them_tag'   => __( 'Standard Care', 'estecapelli' ),

			'features'   => array(
				array(
					'icon'  => 'bed',
					'label' => __( '5-Star Hotel Accommodation', 'estecapelli' ),
					'us'    => true,
					'them'  => true,
				),
				array(
					'icon'  => 'car',
					'label' => __( 'VIP Transfer Service', 'estecapelli' ),
					'us'    => true,
					'them'  => true,
				),
				array(
					'icon'  => 'languages',
					'label' => __( 'Personal Translator', 'estecapelli' ),
					'us'    => true,
					'them'  => true,
				),
				array(
					'icon'  => 'sparkles',
					'label' => __( 'Latest Technology Implementation', 'estecapelli' ),
					'us'    => true,
					'them'  => false,
				),
				array(
					'icon'  => 'target',
					'label' => __( 'Success-Oriented Treatment Planning', 'estecapelli' ),
					'us'    => true,
					'them'  => false,
				),
				array(
					'icon'  => 'atom',
					'label' => __( 'Innovative Techniques (Exosome, FUE, DHI, VITA)', 'estecapelli' ),
					'us'    => true,
					'them'  => false,
				),
				array(
					'icon'  => 'dna',
					'label' => __( 'Regenerative Stem Cell Therapy', 'estecapelli' ),
					'us'    => true,
					'them'  => false,
				),
				array(
					'icon'  => 'headset',
					'label' => __( 'Dedicated Post-Op Follow-Up Team', 'estecapelli' ),
					'us'    => true,
					'them'  => true,
				),
				array(
					'icon'  => 'shield-check',
					'label' => __( 'Pain-Free Anaesthesia', 'estecapelli' ),
					'us'    => true,
					'them'  => false,
				),
			),

			'footnote'   => __( 'Nine details where every choice matters.', 'estecapelli' ),
		);
	}
}

if ( ! function_exists( 'estecapelli_signature_methods' ) ) {
	/**
	 * Homepage "Signature Methods" — three flip cards that auto-rotate.
	 *
	 * Front: photo + eyebrow + title + tease.
	 * Back:  stat + body + two CTAs.
	 *
	 * ACF override: option 'home_signature_methods' returns the full payload.
	 */
	function estecapelli_signature_methods() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'home_signature_methods', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}

		$img = get_template_directory_uri() . '/assets/images/expertise/';

		return array(
			'eyebrow'  => __( 'Our Expertise', 'estecapelli' ),
			'headline' => __( 'Three methods that shape every treatment.', 'estecapelli' ),
			'lead'     => __( 'Two are exclusively ours. One sets the standard for personalised planning. All three power the results our patients trust us for.', 'estecapelli' ),

			'cta_secondary' => array(
				'label' => __( 'Schedule a Free Consultation', 'estecapelli' ),
				'url'   => home_url( '/contact/' ),
			),

			'cards' => array(
				array(
					'key'        => 'exosome',
					'eyebrow'    => __( 'Patented · Estecapelli Exclusive', 'estecapelli' ),
					'title'      => __( 'Exosome FUE', 'estecapelli' ),
					'subtitle'   => __( 'Premium Hair Transplant Method', 'estecapelli' ),
					'tease'      => __( 'Mesenchymal stem-cell support that keeps follicles alive longer.', 'estecapelli' ),
					'image'      => $img . 'exosome.png',
					'icon'       => 'atom',
					'stat'       => '98%',
					'stat_label' => __( 'Follicle survival over 72 hours', 'estecapelli' ),
					'body'       => __( 'Our patented Exosome Treatment is derived from mesenchymal stem cells found in the umbilical cord — designed to lift hair-follicle survival to 98% over 72 hours, with faster recovery, stronger growth, and naturally lasting results.', 'estecapelli' ),
					'cta'        => array(
						'label' => __( 'Learn about Exosome FUE', 'estecapelli' ),
						'url'   => home_url( '/treatments/exosome-fue-hair-transplant/' ),
					),
				),
				array(
					'key'        => 'tricholab',
					'eyebrow'    => __( 'AI-Powered Diagnosis', 'estecapelli' ),
					'title'      => __( 'TrichoLab', 'estecapelli' ),
					'subtitle'   => __( 'Millimetric Hair & Scalp Analysis', 'estecapelli' ),
					'tease'      => __( 'AI maps your scalp before a single graft is planned.', 'estecapelli' ),
					'image'      => $img . 'tricholab.jpg',
					'icon'       => 'target',
					'stat'       => __( 'Millimetric', 'estecapelli' ),
					'stat_label' => __( 'Precision per scalp scan', 'estecapelli' ),
					'body'       => __( 'TrichoLab examines your hair and scalp with millimetric accuracy — measuring follicle density, thickness, donor capacity, and loss patterns — so every graft is planned for your unique anatomy and the result feels naturally yours.', 'estecapelli' ),
					'cta'        => array(
						'label' => __( 'Learn more about TrichoLab', 'estecapelli' ),
						'url'   => home_url( '/treatments/tricholab/' ),
					),
				),
				array(
					'key'        => 'vita',
					'eyebrow'    => __( 'Signature Protocol · Estecapelli Exclusive', 'estecapelli' ),
					'title'      => __( 'VITA Treatment', 'estecapelli' ),
					'subtitle'   => __( 'Power Derived from Vitamins', 'estecapelli' ),
					'tease'      => __( 'A vitamin-cooled bath that keeps grafts strong out of the body.', 'estecapelli' ),
					'image'      => $img . 'vita.jpg',
					'icon'       => 'sparkles',
					'stat'       => __( 'Cool-Vapor', 'estecapelli' ),
					'stat_label' => __( 'Vitamin-nourished grafts', 'estecapelli' ),
					'body'       => __( 'Grafts lose strength the moment they leave the body. Our VITA Protocol bathes them in a specially formulated vitamin cocktail with cool-vapor application — keeping every follicle alive, nourished, and resilient until placement.', 'estecapelli' ),
					'cta'        => array(
						'label' => __( 'Learn more about VITA', 'estecapelli' ),
						'url'   => home_url( '/treatments/vita/' ),
					),
				),
			),
		);
	}
}

if ( ! function_exists( 'estecapelli_journey_steps' ) ) {
	/**
	 * Homepage "Your Journey" — six step cards arranged around a centred photo.
	 *
	 * ACF override: option 'home_journey_steps' returns the full payload.
	 */
	function estecapelli_journey_steps() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'home_journey_steps', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}

		return array(
			'eyebrow'  => __( 'Your Journey', 'estecapelli' ),
			'headline' => __( 'From your first photo to twelve months later — we walk every step with you.', 'estecapelli' ),
			'lead'     => __( 'Six clear stages, one dedicated team. Here is what your hair transplant journey looks like with Estecapelli.', 'estecapelli' ),

			'photo'     => 'team.png',
			'photo_alt' => __( 'Estecapelli patient consultants ready to welcome you', 'estecapelli' ),

			'closing' => array(
				'text' => __( 'Ready to take the first step towards restoring your confidence?', 'estecapelli' ),
				'cta'  => array(
					'label' => __( 'Schedule Your Free Consultation', 'estecapelli' ),
					'url'   => home_url( '/contact/' ),
				),
			),

			'steps' => array(
				array(
					'time'  => __( '1 Minute', 'estecapelli' ),
					'icon'  => 'image',
					'title' => __( 'Send Us Your Photos', 'estecapelli' ),
					'body'  => __( 'To get started, simply share clear photos of your hair and receive a free online consultation from our clinic.', 'estecapelli' ),
					'link'  => array(
						'label' => __( 'Meet Our Doctors', 'estecapelli' ),
						'url'   => home_url( '/doctors/' ),
					),
				),
				array(
					'time'  => __( 'Day 1', 'estecapelli' ),
					'icon'  => 'clipboard',
					'title' => __( "Let's Plan Your Operation", 'estecapelli' ),
					'body'  => __( 'Based on a detailed analysis of your hair and scalp, our experts design a customised treatment plan to achieve the most natural-looking results.', 'estecapelli' ),
					'link'  => array(
						'label' => __( 'Explore Our Technology', 'estecapelli' ),
						'url'   => home_url( '/treatments/tricholab/' ),
					),
				),
				array(
					'time'  => __( 'Day 2', 'estecapelli' ),
					'icon'  => 'plane',
					'title' => __( 'Flights, Transport & Hotel', 'estecapelli' ),
					'body'  => __( 'Once your flight is confirmed, our team arranges your hotel accommodation and private VIP transfers between the airport, hotel and clinic for a smooth, stress-free stay.', 'estecapelli' ),
					'link'  => array(
						'label' => __( 'Learn More', 'estecapelli' ),
						'url'   => home_url( '/about/' ),
					),
				),
				array(
					'time'  => __( 'Day 3', 'estecapelli' ),
					'icon'  => 'hair',
					'title' => __( 'Hair Transplant Day', 'estecapelli' ),
					'body'  => __( 'On the day of your procedure, we guide you through every step. The transplant is performed under local anaesthesia, with no overnight hospital stay required.', 'estecapelli' ),
					'link'  => array(
						'label' => __( 'See Success Stories', 'estecapelli' ),
						'url'   => home_url( '/before-after/' ),
					),
				),
				array(
					'time'  => __( '2–3 Weeks', 'estecapelli' ),
					'icon'  => 'hands-heart',
					'title' => __( 'Post-Operative Care', 'estecapelli' ),
					'body'  => __( 'Dressing and the first hair washing procedures are carried out by our team to ensure a complication-free recovery.', 'estecapelli' ),
					'link'  => array(
						'label' => __( 'View Care Plans', 'estecapelli' ),
						'url'   => home_url( '/care/' ),
					),
				),
				array(
					'time'  => __( '12 Months', 'estecapelli' ),
					'icon'  => 'headset',
					'title' => __( 'Follow-Up Care Service', 'estecapelli' ),
					'body'  => __( 'Our dedicated post-operative support team guides you through recovery and hair growth, ensuring a streamlined journey toward your desired results.', 'estecapelli' ),
					'link'  => array(
						'label' => __( 'Get Free Consultation', 'estecapelli' ),
						'url'   => home_url( '/contact/' ),
					),
				),
			),
		);
	}
}

if ( ! function_exists( 'estecapelli_patient_stories' ) ) {
	/**
	 * Homepage "Real Stories" — cinematic patient-testimonial stage.
	 *
	 * Each story carries a YouTube ID, a hero pre-title, a long editorial
	 * body, a 5-star rating, and three meta pills (grafts / technique /
	 * country). The renderer shows one story as the hero and the rest in a
	 * vertical "poster wall" the visitor can click to swap.
	 *
	 * ACF override: option 'home_patient_stories' returns the full payload.
	 */
	function estecapelli_patient_stories() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'home_patient_stories', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}

		return array(
			'eyebrow'  => __( 'Real Stories', 'estecapelli' ),
			'headline' => __( 'Their results speak louder than any ad ever could.', 'estecapelli' ),
			'lead'     => __( 'Hear, in their own words, how patients from around the world describe their Estecapelli journey — from first consultation to long-term result.', 'estecapelli' ),

			// Placeholder stories — user will swap YouTube IDs and details later.
			'stories' => array(
				array(
					'key'         => 'alexander-p',
					'name'        => __( 'Alexander P.', 'estecapelli' ),
					'country'     => __( 'Germany', 'estecapelli' ),
					'country_iso' => 'DE',
					'flag'        => '🇩🇪',
					'grafts'      => '4,280',
					'technique'   => 'DHI',
					'rating'      => 5,
					'video_id'    => '',
					'pre_title'   => __( 'A naturally fuller beard, designed graft by graft.', 'estecapelli' ),
					'body'        => __( 'Using the DHI technique, 4,280 grafts were transferred from the scalp donor area to enhance the naturally sparse beard. The beard was designed according to the patient\'s preferences and facial structure. With moderate graft quality averaging 2.6 hairs per follicle, the procedure achieved natural and satisfying results.', 'estecapelli' ),
				),
				array(
					'key'         => 'juan-cv',
					'name'        => __( 'Juan C. V.', 'estecapelli' ),
					'country'     => __( 'United States', 'estecapelli' ),
					'country_iso' => 'US',
					'flag'        => '🇺🇸',
					'grafts'      => '3,340',
					'technique'   => 'DHI',
					'rating'      => 5,
					'video_id'    => '',
					'pre_title'   => __( '11 months, Norwood 3 reversed.', 'estecapelli' ),
					'body'        => __( 'Our male patient from the USA completed his 11-month DHI journey with 3,340 grafts, each averaging 2.4 hairs. He arrived with Norwood 3 hair loss and visible thinning. Dense implantation was performed on bald spots and the crown, delivering precise placement, natural direction, and satisfying coverage.', 'estecapelli' ),
				),
				array(
					'key'         => 'matthew-k',
					'name'        => __( 'Matthew K.', 'estecapelli' ),
					'country'     => __( 'Canada', 'estecapelli' ),
					'country_iso' => 'CA',
					'flag'        => '🇨🇦',
					'grafts'      => '3,500',
					'technique'   => 'Sapphire FUE',
					'rating'      => 5,
					'video_id'    => '',
					'pre_title'   => __( 'Hairline rebuilt with surgical precision.', 'estecapelli' ),
					'body'        => __( 'For this Canadian patient, 3,500 grafts were harvested using a 9-4 punch system. With an impressive average of 3 hairs per graft, intensive implantation reconstructed the frontal hairline and the area immediately behind it. At 10 months the result shows excellent density, natural direction, and a balanced appearance.', 'estecapelli' ),
				),
				array(
					'key'         => 'olivier-r',
					'name'        => __( 'Olivier R.', 'estecapelli' ),
					'country'     => __( 'France', 'estecapelli' ),
					'country_iso' => 'FR',
					'flag'        => '🇫🇷',
					'grafts'      => '4,000',
					'technique'   => 'Sapphire FUE',
					'rating'      => 5,
					'video_id'    => '',
					'pre_title'   => __( 'A receding hairline, fully restored.', 'estecapelli' ),
					'body'        => __( 'Our French patient arrived with a deep receding hairline and a wide frontal recession. We extracted 4,000 grafts with the Sapphire FUE technique and rebuilt a softly rounded hairline that frames the face naturally. Twelve-month follow-up confirmed excellent density and direction.', 'estecapelli' ),
				),
				array(
					'key'         => 'hassan-m',
					'name'        => __( 'Hassan M.', 'estecapelli' ),
					'country'     => __( 'United Arab Emirates', 'estecapelli' ),
					'country_iso' => 'AE',
					'flag'        => '🇦🇪',
					'grafts'      => '3,800',
					'technique'   => 'DHI',
					'rating'      => 5,
					'video_id'    => '',
					'pre_title'   => __( 'Crown thickening with zero downtime.', 'estecapelli' ),
					'body'        => __( 'For this patient from the UAE we implanted 3,800 grafts with the DHI technique, focusing on crown thickening and minor temporal reinforcement. The session was completed in a single sitting with no overnight stay and the patient returned home within 48 hours. Follow-up at 9 months confirmed dense, natural growth.', 'estecapelli' ),
				),
			),
		);
	}
}

if ( ! function_exists( 'estecapelli_facilities' ) ) {
	/**
	 * Homepage "Our Facilities" — hotel + clinic editorial cards.
	 *
	 * Image lookup is extension-agnostic: drop `hotel.{png,webp,jpg,jpeg}`
	 * or `clinic.{png,webp,jpg,jpeg}` into /assets/images/facilities/ and
	 * the renderer will find it.
	 *
	 * ACF override: option 'home_facilities' returns the full payload.
	 */
	function estecapelli_facilities() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'home_facilities', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}

		return array(
			'eyebrow'  => __( 'Our Facilities', 'estecapelli' ),
			'headline' => __( 'A clinic that performs. A hotel that pampers.', 'estecapelli' ),
			'lead'     => __( 'Two addresses, one continuous experience — both curated to keep you comfortable from the moment you land in Istanbul until the day you fly home.', 'estecapelli' ),

			'cards' => array(
				array(
					'key'      => 'hotel',
					'kind'     => __( 'Your Stay', 'estecapelli' ),
					'name'     => __( 'Five-Star Partner Hotel', 'estecapelli' ),
					'location' => __( 'City centre · Istanbul, Türkiye', 'estecapelli' ),
					'body'     => __( 'A handpicked 5-star hotel with full board, a quiet recovery suite, and 24/7 patient support — minutes from the clinic and the city\'s landmarks.', 'estecapelli' ),
					'image'    => 'hotel',
					'amenities' => array(
						array( 'icon' => 'bed',       'label' => __( '5-Star Room', 'estecapelli' ) ),
						array( 'icon' => 'utensils',  'label' => __( 'Full Board', 'estecapelli' ) ),
						array( 'icon' => 'wifi',      'label' => __( 'High-Speed Wi-Fi', 'estecapelli' ) ),
						array( 'icon' => 'concierge', 'label' => __( '24/7 Patient Care', 'estecapelli' ) ),
					),
					'cta' => array(
						'label' => __( 'Explore the Hotel', 'estecapelli' ),
						'url'   => home_url( '/hospital/#hotel' ),
					),
				),
				array(
					'key'      => 'clinic',
					'kind'     => __( 'Your Clinic', 'estecapelli' ),
					'name'     => __( 'Hospital-Grade Estecapelli Clinic', 'estecapelli' ),
					'location' => __( 'Şişli · Istanbul, Türkiye', 'estecapelli' ),
					'body'     => __( 'A Ministry-of-Health licensed facility with modern operation rooms, sapphire-blade instruments, and a permanent on-site medical team led by Dr. Mehmet Hanifi Kutlar.', 'estecapelli' ),
					'image'    => 'clinic',
					'amenities' => array(
						array( 'icon' => 'building',     'label' => __( 'Ministry Licensed', 'estecapelli' ) ),
						array( 'icon' => 'shield-check', 'label' => __( 'Sterile Theatres', 'estecapelli' ) ),
						array( 'icon' => 'target',       'label' => __( 'Latest Equipment', 'estecapelli' ) ),
						array( 'icon' => 'medical-plus', 'label' => __( 'On-Site Doctors', 'estecapelli' ) ),
					),
					'cta' => array(
						'label' => __( 'Tour the Clinic', 'estecapelli' ),
						'url'   => home_url( '/hospital/#clinic' ),
					),
				),
			),
		);
	}
}

if ( ! function_exists( 'estecapelli_resolve_image' ) ) {
	/**
	 * Find an image under /assets/images/{folder}/{base}.{png|webp|jpg|jpeg}.
	 * Returns the URL, or '' if no candidate exists.
	 */
	function estecapelli_resolve_image( $folder, $base ) {
		$dir = get_template_directory() . '/assets/images/' . $folder . '/';
		$uri = get_template_directory_uri() . '/assets/images/' . $folder . '/';
		foreach ( array( 'png', 'webp', 'jpg', 'jpeg' ) as $ext ) {
			if ( file_exists( $dir . $base . '.' . $ext ) ) {
				return $uri . $base . '.' . $ext;
			}
		}
		return '';
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
