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
		<span class="trustpilot-badge" aria-label="<?php esc_attr_e( 'Trustpilot rating: 5 out of 5, Excellent', 'estecapelli' ); ?>">
			<span class="trustpilot-badge__label"><?php esc_html_e( 'Excellent', 'estecapelli' ); ?></span>
			<span class="trustpilot-badge__stars" aria-hidden="true">
				<?php for ( $i = 0; $i < 5; $i++ ) : ?>
					<?php estecapelli_icon( 'star', array( 'width' => 14, 'height' => 14, 'class' => 'trustpilot-badge__star' ) ); ?>
				<?php endfor; ?>
			</span>
			<span class="trustpilot-badge__name">Trustpilot</span>
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

if ( ! function_exists( 'estecapelli_primary_menu_fallback' ) ) {
	/**
	 * Fallback nav shown when no menu is assigned to the "primary" location.
	 * Mirrors the live site's main nav so the header is presentable before WP-admin setup.
	 * Items marked with a chevron visually indicate they would have a dropdown
	 * once WP admin nests child items beneath them.
	 */
	function estecapelli_primary_menu_fallback() {
		$items = array(
			array( 'label' => __( 'Hair Transplant', 'estecapelli' ),  'url' => home_url( '/hair-transplant/' ),  'dropdown' => true ),
			array( 'label' => __( 'Plastic Surgery', 'estecapelli' ),  'url' => home_url( '/plastic-surgery/' ),  'dropdown' => true ),
			array( 'label' => __( 'Dental Treatment', 'estecapelli' ), 'url' => home_url( '/dental-treatment/' ), 'dropdown' => true ),
			array( 'label' => __( 'Exosome Treatment', 'estecapelli' ),'url' => home_url( '/exosome-treatment/' ),'dropdown' => false, 'badge' => __( 'NEW', 'estecapelli' ) ),
			array( 'label' => __( 'Before & After', 'estecapelli' ),   'url' => home_url( '/before-after/' ),     'dropdown' => false ),
			array( 'label' => __( 'About Us', 'estecapelli' ),         'url' => home_url( '/about/' ),            'dropdown' => true ),
			array( 'label' => __( 'Blog', 'estecapelli' ),             'url' => home_url( '/blog/' ),             'dropdown' => false ),
			array( 'label' => __( 'Contact', 'estecapelli' ),          'url' => home_url( '/contact/' ),          'dropdown' => false ),
		);

		echo '<ul class="site-nav__list">';
		foreach ( $items as $item ) {
			$badge = ! empty( $item['badge'] )
				? sprintf( '<span class="nav-badge">%s</span>', esc_html( $item['badge'] ) )
				: '';

			$chevron = ! empty( $item['dropdown'] )
				? '<svg class="chev" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true" focusable="false"><path d="M2 4 L6 8 L10 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>'
				: '';

			printf(
				'<li><a href="%1$s">%2$s%3$s%4$s</a></li>',
				esc_url( $item['url'] ),
				esc_html( $item['label'] ),
				$badge, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$chevron // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
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
		$candidates = array( 'logo.svg', 'logo-horizontal.svg', 'logo.png', 'logo-horizontal.png' );
		$base_dir   = get_template_directory() . '/assets/images/';
		$base_uri   = get_template_directory_uri() . '/assets/images/';
		$logo_url   = '';

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
				'<img class="%1$s" src="%2$s" alt="%3$s" width="240" height="44" />',
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
