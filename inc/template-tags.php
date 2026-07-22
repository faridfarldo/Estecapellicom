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
			'link'         => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>',
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
			'cpu'          => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="9.5" y="9.5" width="5" height="5" rx="1"/><path d="M9 2v2M15 2v2M9 20v2M15 20v2M2 9h2M2 15h2M20 9h2M20 15h2"/>',
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

if ( ! function_exists( 'estecapelli_render_item_icon' ) ) {
	/**
	 * Render an item's icon: an uploaded custom icon wins; otherwise the built-in
	 * icon selected in the (hidden) dropdown / seeded default. Used by the
	 * icon+text repeaters (stats, steps, stepbook, candidate, form points).
	 *
	 * @param array $item Repeater row (expects 'icon' string and/or 'icon_file').
	 * @param array $args Passed through to estecapelli_icon() (width/height/class).
	 */
	function estecapelli_render_item_icon( $item, $args = array() ) {
		$file = is_array( $item ) ? ( $item['icon_file'] ?? null ) : null;

		if ( is_array( $file ) && ! empty( $file['ID'] ) ) {
			$path = get_attached_file( (int) $file['ID'] );
			$prepared = ( $path && is_readable( $path ) ) ? estecapelli_prepare_custom_svg( (string) file_get_contents( $path ) ) : null; // phpcs:ignore
			if ( $prepared ) {
				$w = (int) ( $args['width'] ?? 22 );
				$h = (int) ( $args['height'] ?? 22 );
				printf(
					'<svg class="icon icon--custom %1$s" width="%2$d" height="%3$d" viewBox="%4$s" fill="currentColor" stroke="none" aria-hidden="true" focusable="false">%5$s</svg>',
					esc_attr( $args['class'] ?? '' ),
					$w,
					$h,
					esc_attr( $prepared['viewbox'] ),
					$prepared['inner'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
				return;
			}
			// Non-SVG (e.g. PNG) upload: fall back to a plain <img>.
			if ( ! empty( $file['url'] ) ) {
				printf(
					'<img class="icon icon--custom %1$s" src="%2$s" alt="" width="%3$d" height="%4$d" style="object-fit:contain" loading="lazy" decoding="async" />',
					esc_attr( $args['class'] ?? '' ),
					esc_url( $file['url'] ),
					(int) ( $args['width'] ?? 22 ),
					(int) ( $args['height'] ?? 22 )
				);
				return;
			}
		}

		$name = is_array( $item ) ? (string) ( $item['icon'] ?? '' ) : (string) $item;
		if ( '' !== $name ) {
			estecapelli_icon( $name, $args );
		}
	}
}

if ( ! function_exists( 'estecapelli_sanitize_icon_svg' ) ) {
	/**
	 * Sanitise pasted SVG markup for use as an icon. Keeps only the inner shape
	 * elements (our wrapper supplies the <svg> tag, viewBox, size and colour) and
	 * strips scripts, event handlers and anything not on the allow-list.
	 *
	 * @param string $svg Raw pasted markup (full <svg> or inner shapes).
	 * @return string Safe inner-SVG markup.
	 */
	function estecapelli_sanitize_icon_svg( $svg ) {
		$svg = (string) $svg;
		// If a whole <svg> was pasted, keep only its inner markup.
		if ( false !== stripos( $svg, '<svg' ) && preg_match( '#<svg[^>]*>(.*?)</svg>#is', $svg, $m ) ) {
			$svg = $m[1];
		}
		$common  = array(
			'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true,
			'stroke-linejoin' => true, 'opacity' => true, 'transform' => true, 'fill-rule' => true, 'clip-rule' => true,
		);
		$allowed = array(
			'path'     => $common + array( 'd' => true ),
			'circle'   => $common + array( 'cx' => true, 'cy' => true, 'r' => true ),
			'ellipse'  => $common + array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ),
			'rect'     => $common + array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ),
			'line'     => $common + array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ),
			'polyline' => $common + array( 'points' => true ),
			'polygon'  => $common + array( 'points' => true ),
			'g'        => $common,
		);
		return wp_kses( $svg, $allowed );
	}
}

if ( ! function_exists( 'estecapelli_prepare_custom_svg' ) ) {
	/**
	 * Turn raw SVG markup into a safe payload for estecapelli_icon(): the inner
	 * shapes (sanitised) plus the original viewBox, so any icon size/ratio works.
	 *
	 * @param string $raw Raw SVG file contents or pasted markup.
	 * @return array{inner:string,viewbox:string}|null
	 */
	function estecapelli_prepare_custom_svg( $raw ) {
		$raw = (string) $raw;
		if ( function_exists( 'estecapelli_sanitize_svg_markup' ) ) {
			$raw = estecapelli_sanitize_svg_markup( $raw );
		}
		if ( false === stripos( $raw, '<svg' ) ) {
			return null;
		}
		$viewbox = '0 0 24 24';
		if ( preg_match( '/viewBox\s*=\s*["\']([^"\']+)["\']/i', $raw, $m ) ) {
			$viewbox = trim( $m[1] );
		}
		$inner = $raw;
		if ( preg_match( '#<svg[^>]*>(.*)</svg>#is', $raw, $m ) ) {
			$inner = $m[1];
		}
		$inner = estecapelli_sanitize_icon_svg( $inner ); // allow-list the shapes
		if ( '' === trim( $inner ) ) {
			return null;
		}
		return array( 'inner' => trim( $inner ), 'viewbox' => $viewbox );
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
			'address'  => __( '19 Mayıs Mah. Dr. Hüsnü İsmet Öztürk Sk. Plaza Sitesi No: 1E/4, Şişli / İstanbul, Türkiye', 'estecapelli' ),
			'phone'    => '+90 541 541 0041',
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
				foreach ( $acf as &$item ) {
					if ( ! empty( $item['url'] ) ) {
						$item['url'] = estecapelli_localize_theme_url( $item['url'] );
					}
				}
				unset( $item );
				return $acf;
			}
		}
		return array(
			array( 'label' => __( 'Sapphire FUE Hair Transplant', 'estecapelli' ), 'url' => estecapelli_indexed_url( '/en/hair-transplant/sapphire-fue-hair-transplant' ) ),
			array( 'label' => __( 'Exosome FUE Hair Transplant', 'estecapelli' ),  'url' => estecapelli_indexed_url( '/en/hair-transplant/exosome-fue-hair-transplant' ) ),
			array( 'label' => __( 'DHI Hair Transplant', 'estecapelli' ),          'url' => estecapelli_indexed_url( '/en/hair-transplant/dhi-hair-transplant' ) ),
			array( 'label' => __( 'VITA Treatment', 'estecapelli' ),               'url' => estecapelli_indexed_url( '/en/hair-transplant/vita-treatment' ) ),
			array( 'label' => __( 'Female Hair Transplant', 'estecapelli' ),       'url' => estecapelli_indexed_url( '/en/hair-transplant/female-hair-transplant' ) ),
			array( 'label' => __( 'Beard Transplant', 'estecapelli' ),             'url' => estecapelli_indexed_url( '/en/hair-transplant/beard-transplant' ) ),
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
				foreach ( $acf as &$item ) {
					if ( ! empty( $item['url'] ) ) {
						$item['url'] = estecapelli_localize_theme_url( $item['url'] );
					}
				}
				unset( $item );
				return $acf;
			}
		}
		return array(
			array( 'label' => __( 'Home', 'estecapelli' ),             'url' => estecapelli_indexed_url( '/en' ) ),
			array( 'label' => __( 'About Us', 'estecapelli' ),         'url' => estecapelli_indexed_url( '/en/about-us' ) ),
			array( 'label' => __( 'Our Doctors', 'estecapelli' ),      'url' => estecapelli_indexed_url( '/en/about-us/our-doctors' ) ),
			array( 'label' => __( 'Before & After', 'estecapelli' ),   'url' => estecapelli_indexed_url( '/en/before-after' ) ),
			array( 'label' => __( 'Treatments', 'estecapelli' ),       'url' => estecapelli_indexed_url( '/en/hair-transplant' ) ),
			array( 'label' => __( 'Blog', 'estecapelli' ),             'url' => estecapelli_indexed_url( '/en/blog' ) ),
			array( 'label' => __( 'Contact', 'estecapelli' ),          'url' => estecapelli_indexed_url( '/en/contact' ) ),
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
							'label'       => __( 'Sapphire FUE Hair Transplant', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/sapphire-fue-hair-transplant' ),
							'description' => __( 'A natural and permanent hair transplant technique where follicles are placed via sapphire blades.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'DHI Hair Transplant', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/dhi-hair-transplant' ),
							'description' => __( 'A modern hair transplantation method performed with a Choi pen that allows precise placement.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Exosome FUE Hair Transplant', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/exosome-fue-hair-transplant' ),
							'description' => __( 'Supported by cell-regenerating exosomes, it keeps hair follicles alive for longer-lasting density.', 'estecapelli' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
					),
					array(
						array(
							'label'       => __( 'VITA Treatment', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/vita-treatment' ),
							'description' => __( "Estecapelli's signature method that revitalizes the scalp and strengthens hair.", 'estecapelli' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Female Hair Transplant', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/female-hair-transplant' ),
							'description' => __( 'Special for women, dense and natural-looking hair transplant without shaving.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Hair Mesotherapy', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/hair-mesotherapy' ),
							'description' => __( 'A vitamin and mineral injection treatment that revitalizes hair follicles.', 'estecapelli' ),
						),
					),
					array(
						array(
							'label'       => __( 'Beard Transplant', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/beard-transplant' ),
							'description' => __( 'Natural beard and mustache transplantation for sparse or non-existing growth.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Eyebrow Transplant', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/eyebrow-transplant' ),
							'description' => __( 'Eyebrow transplantation that gives a naturally curved and full eyebrow shape.', 'estecapelli' ),
						),
					),
				),
				// Care & technology: part of hair transplant, but not a treatment in itself —
				// rendered below a divider, visually set apart from the treatments above.
				'extra' => array(
					'heading' => __( 'Hair Transplant Care & Technology', 'estecapelli' ),
					'items'   => array(
						array(
							'label'       => __( 'Pre-Hair Transplant Period', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/pre-hair-transplant-period' ),
							'description' => __( 'The preparation and analysis process before hair transplantation.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'Post-Hair Transplant Period', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/post-hair-transplant-period' ),
							'description' => __( 'The post-procedure recovery and hair care period.', 'estecapelli' ),
						),
						array(
							'label'       => __( 'TrichoLab', 'estecapelli' ),
							'url'         => estecapelli_nav_url( '/en/hair-transplant/tricholab' ),
							'description' => __( 'Advanced AI-powered hair analysis system that examines the hair and scalp in detail.', 'estecapelli' ),
						),
					),
				),
				'feature' => array(
					'eyebrow'     => __( 'ESTECAPELLI AI', 'estecapelli' ),
					'title'       => __( 'AI Analysis by Estecapelli', 'estecapelli' ),
					'description' => __( 'We have trained an AI to guide every patient to the best next step — get a first, personalised hair-loss assessment in seconds.', 'estecapelli' ),
					'cta_label'   => __( 'Start AI Analysis', 'estecapelli' ),
					'cta_url'     => estecapelli_nav_url( '/en/' ) . '#ai-analysis',
					'video'       => '',
					'image'       => 'hair-analysis/ailogo.webp',
					'caption'     => '',
				),
			),

			'plastic-surgery' => array(
				'columns' => array(
					array(
						array( 'label' => __( 'Rhinoplasty', 'estecapelli' ),               'url' => estecapelli_nav_url( '/en/plastic-surgery/rhinoplasty' ),                                         'description' => __( 'Nose reshaping surgery that refines proportions and function.', 'estecapelli' ) ),
						array( 'label' => __( 'BBL (Brazilian Butt Lift)', 'estecapelli' ), 'url' => estecapelli_nav_url( '/en/plastic-surgery/bbl' ),                                                 'description' => __( 'Natural body contouring with fat transfer to the buttocks.', 'estecapelli' ) ),
						array( 'label' => __( 'Liposuction', 'estecapelli' ),               'url' => estecapelli_nav_url( '/en/plastic-surgery/liposuction' ),                                         'description' => __( 'Removes localized fat deposits to reshape the body.', 'estecapelli' ) ),
					),
					array(
						array( 'label' => __( 'Breast Aesthetics', 'estecapelli' ),         'url' => estecapelli_nav_url( '/en/plastic-surgery/breast-aesthetics-breast-surgery' ),                    'description' => __( 'Augmentation, lift, and reduction tailored to your goals.', 'estecapelli' ) ),
						array( 'label' => __( 'Abdominoplasty (Tummy Tuck)', 'estecapelli' ),'url' => estecapelli_nav_url( '/en/plastic-surgery/abdominoplasty-tummy-tuck' ),                          'description' => __( 'Flattens and tightens the abdomen for a smoother profile.', 'estecapelli' ) ),
						array( 'label' => __( 'Gynecomastia', 'estecapelli' ),              'url' => estecapelli_nav_url( '/en/plastic-surgery/gynecomastia' ),                                        'description' => __( 'Surgical treatment of enlarged male breast tissue.', 'estecapelli' ) ),
					),
					array(
						array( 'label' => __( 'Face & Neck Lift Surgery', 'estecapelli' ),  'url' => estecapelli_nav_url( '/en/plastic-surgery/face-and-neck-lift-surgery' ),                          'description' => __( 'Restores facial contour and reduces visible signs of aging.', 'estecapelli' ) ),
						array( 'label' => __( 'Obesity Surgeries (Bariatric)', 'estecapelli' ),'url' => estecapelli_nav_url( '/en/plastic-surgery/obesity-surgeries-bariatric-surgery-and-gastric-balloon' ), 'description' => __( 'Bariatric surgery and gastric balloon for sustainable weight loss.', 'estecapelli' ) ),
					),
				),
			),

			'dental-treatment' => array(
				'columns' => array(
					array(
						array( 'label' => __( 'Dental Implant', 'estecapelli' ),  'url' => estecapelli_nav_url( '/en/dental-treatment/dental-implant' ),  'description' => __( 'Permanent replacement for missing teeth with titanium roots.', 'estecapelli' ) ),
						array( 'label' => __( 'Hollywood Smile', 'estecapelli' ), 'url' => estecapelli_nav_url( '/en/dental-treatment/hollywood-smile' ), 'description' => __( 'A bespoke makeover that reshapes your smile aesthetic.', 'estecapelli' ) ),
					),
				),
			),

			'about-us' => array(
				'columns' => array(
					array(
						array( 'label' => __( 'About Estecapelli', 'estecapelli' ),'url' => estecapelli_nav_url( '/en/about-us' ),            'description' => __( 'Who we are and what drives our clinic forward.', 'estecapelli' ) ),
						array( 'label' => __( 'Our Doctors', 'estecapelli' ),     'url' => estecapelli_nav_url( '/en/about-us/our-doctors' ), 'description' => __( 'Meet the surgeons leading every procedure.', 'estecapelli' ) ),
					),
					array(
						array( 'label' => __( 'Our Team', 'estecapelli' ),        'url' => estecapelli_nav_url( '/en/about-us/our-team' ),    'description' => __( 'The full medical and patient-care team behind your treatment.', 'estecapelli' ) ),
					),
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
		<div class="megamenu<?php echo empty( $data['feature'] ) ? ' megamenu--no-feature' : ''; ?>" role="menu" aria-label="<?php echo esc_attr( $key ); ?>" data-mega-key="<?php echo esc_attr( $key ); ?>">
			<div class="megamenu__inner">
				<div class="megamenu__main">
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

					<?php if ( ! empty( $data['extra']['items'] ) ) : $ex = $data['extra']; ?>
						<div class="megamenu__extra">
							<?php if ( ! empty( $ex['heading'] ) ) : ?>
								<span class="megamenu__extra-heading"><?php echo esc_html( $ex['heading'] ); ?></span>
							<?php endif; ?>
							<div class="megamenu__extra-items">
								<?php foreach ( $ex['items'] as $item ) : ?>
									<a class="megamenu__item megamenu__item--care" href="<?php echo esc_url( $item['url'] ); ?>" role="menuitem">
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
						</div>
					<?php endif; ?>
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
			array( 'label' => __( 'Hair Transplant', 'estecapelli' ),  'url' => estecapelli_nav_url( '/en/hair-transplant' ),  'mega' => 'hair-transplant' ),
			array( 'label' => __( 'Plastic Surgery', 'estecapelli' ),  'url' => estecapelli_nav_url( '/en/plastic-surgery' ),  'mega' => 'plastic-surgery' ),
			array( 'label' => __( 'Dental Treatment', 'estecapelli' ), 'url' => estecapelli_nav_url( '/en/dental-treatment' ), 'mega' => 'dental-treatment' ),
			array( 'label' => __( 'Before & After', 'estecapelli' ),   'url' => estecapelli_nav_url( '/en/before-after' ) ),
			array( 'label' => __( 'About Us', 'estecapelli' ),         'url' => estecapelli_nav_url( '/en/about-us' ),         'mega' => 'about-us' ),
			array( 'label' => __( 'Blog', 'estecapelli' ),             'url' => estecapelli_nav_url( '/en/blog' ) ),
			array( 'label' => __( 'Contact Us', 'estecapelli' ),       'url' => estecapelli_nav_url( '/en/contact' ) ),
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

if ( ! function_exists( 'estecapelli_acf_overlay' ) ) {
	/**
	 * Overlay ACF values onto built-in defaults without ever losing data.
	 *
	 * - Empty scalars (''/null) keep the default — so an editor only changes what
	 *   they actually fill in.
	 * - Associative arrays are merged recursively (edit one nested field, the rest
	 *   stay default).
	 * - List/repeater arrays replace the default only when the editor added rows;
	 *   an empty repeater keeps the current list.
	 */
	function estecapelli_acf_overlay( $defaults, $acf ) {
		if ( ! is_array( $acf ) ) {
			return $defaults;
		}
		$out = is_array( $defaults ) ? $defaults : array();
		foreach ( $acf as $k => $v ) {
			if ( is_array( $v ) ) {
				$is_list = ( array() === $v ) || ( array_keys( $v ) === range( 0, count( $v ) - 1 ) );
				if ( $is_list ) {
					if ( ! empty( $v ) ) {
						$out[ $k ] = $v;
					}
				} elseif ( isset( $out[ $k ] ) && is_array( $out[ $k ] ) ) {
					$out[ $k ] = estecapelli_acf_overlay( $out[ $k ], $v );
				} elseif ( ! empty( $v ) ) {
					$out[ $k ] = $v;
				}
			} elseif ( '' !== $v && null !== $v ) {
				$out[ $k ] = $v;
			}
		}
		return $out;
	}
}

if ( ! function_exists( 'estecapelli_home_hero' ) ) {
	/**
	 * Homepage hero content. ACF (Homepage Content → Hero) overlays the defaults
	 * per-field; empty fields keep the built-in value.
	 */
	function estecapelli_home_hero() {
		$defaults = estecapelli_home_hero_defaults();
		if ( ! function_exists( 'get_field' ) ) {
			return $defaults;
		}
		return estecapelli_acf_overlay( $defaults, get_field( 'home_hero', 'option' ) );
	}
}

if ( ! function_exists( 'estecapelli_home_hero_defaults' ) ) {
	/** Built-in hero content — the base the ACF editor overlays. */
	function estecapelli_home_hero_defaults() {
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
				'url'   => home_url( '/en/contact' ),
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

if ( ! function_exists( 'estecapelli_signature_split' ) ) {
	/**
	 * Interactive diagonal split-hero for the two in-house, trademarked
	 * techniques. Returns a fixed intro plus two panels (VITA on the right,
	 * Exosome on the left) — each panel expands over the other on click.
	 *
	 * ACF override: option 'signature_split' returns the full payload below.
	 */
	function estecapelli_signature_split() {
		$defaults = estecapelli_signature_split_defaults();
		$payload  = $defaults;
		if ( function_exists( 'get_field' ) ) {
			$payload = estecapelli_acf_overlay( $defaults, get_field( 'signature_split', 'option' ) );
		}

		return function_exists( 'estecapelli_localize_home_hero_payload' )
			? estecapelli_localize_home_hero_payload( $payload )
			: $payload;
	}
}

if ( ! function_exists( 'estecapelli_signature_split_defaults' ) ) {
	/** Built-in Exosome/VITA split content — the base the ACF editor overlays. */
	function estecapelli_signature_split_defaults() {
		$cta = home_url( '/en/contact' );
		$img = get_template_directory_uri() . '/assets/images/techniques/';

		return array(
			'intro' => array(
				'eyebrow'  => __( 'OUR SIGNATURE METHODS', 'estecapelli' ),
				'headline' => __( 'Two techniques. Registered to our name.', 'estecapelli' ),
				'body'     => __( 'Two exclusive, trademarked protocols — developed in-house.', 'estecapelli' ),
				'hint'     => __( 'Select a method to play', 'estecapelli' ),
			),
			// Order in the array is render order; CSS places exosome left, vita right.
			// 'video' is a YouTube ID; collapsed panels show 'cover', open ones play it.
			'panels' => array(
				'vita' => array(
					'key'       => 'vita',
					'name'      => __( 'VITA', 'estecapelli' ),
					'trademark' => '®',
					'tag'       => __( 'Treatment', 'estecapelli' ),
					'cover'     => $img . 'vita-cover.webp',
					'video'     => '8C9DLaNJynU',
					'cta'       => array(
						'label' => __( 'Explore VITA®', 'estecapelli' ),
						'url'   => $cta,
					),
				),
				'exosome' => array(
					'key'       => 'exosome',
					'name'      => __( 'Exosome', 'estecapelli' ),
					'trademark' => '®',
					'tag'       => __( 'Treatment', 'estecapelli' ),
					'cover'     => $img . 'exosome-cover.webp',
					'video'     => '6_OK4rQ9cxE',
					'cta'       => array(
						'label' => __( 'Explore Exosome®', 'estecapelli' ),
						'url'   => $cta,
					),
				),
			),
		);
	}
}

if ( ! function_exists( 'estecapelli_hero_slides' ) ) {
	/**
	 * Extra hero slides (2 and 3) for the 3-slide hero carousel. Slide 1 is the
	 * Exosome/VITA split (estecapelli_signature_split). All content is
	 * placeholder-friendly and ACF-overridable via option 'hero_slides'.
	 */
	function estecapelli_hero_slides() {
		$defaults = estecapelli_hero_slides_defaults();
		$payload  = $defaults;
		if ( function_exists( 'get_field' ) ) {
			$payload = estecapelli_acf_overlay( $defaults, get_field( 'hero_slides', 'option' ) );
		}

		return function_exists( 'estecapelli_localize_home_hero_payload' )
			? estecapelli_localize_home_hero_payload( $payload )
			: $payload;
	}
}

if ( ! function_exists( 'estecapelli_hero_slides_defaults' ) ) {
	/** Built-in hero slides 2 & 3 content — the base the ACF editor overlays. */
	function estecapelli_hero_slides_defaults() {
		$img      = get_template_directory_uri() . '/assets/images/';
		$contact  = home_url( '/en/contact' );
		$whatsapp = function_exists( 'estecapelli_whatsapp_url' ) ? estecapelli_whatsapp_url() : $contact;

		return array(
			// Slide 2 — "most experienced expert" (Bosley-style).
			'expert' => array(
				'eyebrow'  => __( 'EST. 2010 · ISTANBUL', 'estecapelli' ),
				'headline' => __( 'One of the World’s Most Experienced Hair Restoration Teams', 'estecapelli' ),
				'body'     => __( 'Join the thousands who have come to Estecapelli to regain their confidence.', 'estecapelli' ),
				'cta'      => array( 'label' => __( 'Schedule a Free Consultation', 'estecapelli' ), 'url' => $contact ),
				'photo'    => $img . 'doctors/mehmet-hanifi-kutlar.jpg',
				'badge'    => array(
					'value' => __( 'Over 50,000+', 'estecapelli' ),
					'label' => __( '5-Star Results', 'estecapelli' ),
				),
				'caption'  => __( 'Actual Estecapelli patient. Individual results may vary.', 'estecapelli' ),
				'patient'  => __( 'Devin H.', 'estecapelli' ),
				'before'   => $img . 'techniques/exosome-cover.webp',
				'after'    => $img . 'techniques/vita-cover.webp',
				'years'    => array( 'from' => '2010', 'to' => '2025', 'label' => __( 'FIFTEEN YEARS', 'estecapelli' ) ),
			),

			// Slide 3 — women's hair transplant, with an auto-playing intro video.
			'women' => array(
				'eyebrow'  => __( 'FOR WOMEN · DISCREET & NATURAL', 'estecapelli' ),
				'headline' => __( 'Women’s Hair Transplant in Istanbul', 'estecapelli' ),
				'body'     => __( 'Thinning along the parting, a receding hairline or overall loss of density affects women too. Our female-focused approach restores fullness with unshaven techniques, complete privacy and natural, permanent results.', 'estecapelli' ),
				'points'   => array(
					__( 'Unshaven, discreet techniques', 'estecapelli' ),
					__( 'Natural hairline & density design', 'estecapelli' ),
					__( 'Female patient privacy at every step', 'estecapelli' ),
				),
				'cta'      => array( 'label' => __( 'Free Consultation', 'estecapelli' ), 'url' => $contact ),
				'video'    => 'eT6ep9dk4BM', // Women's hair transplant intro (YouTube ID)
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
		$defaults = estecapelli_trust_stats_defaults();
		if ( ! function_exists( 'get_field' ) ) {
			return $defaults;
		}
		$acf = get_field( 'trust_stats', 'option' );
		return ( ! empty( $acf ) && is_array( $acf ) ) ? $acf : $defaults;
	}
}

if ( ! function_exists( 'estecapelli_trust_stats_defaults' ) ) {
	/** Built-in trust-stats — the base the ACF editor overlays. */
	function estecapelli_trust_stats_defaults() {
		return array(
			array( 'icon' => 'calendar', 'value' => '15+',                           'label' => __( 'Years of Experience', 'estecapelli' ) ),
			array( 'icon' => 'hair',     'value' => '+' . ESTECAPELLI_PATIENT_COUNT,  'label' => __( 'Happy Patients', 'estecapelli' ) ),
			array( 'icon' => 'globe',    'value' => '+' . ESTECAPELLI_COUNTRY_COUNT,  'label' => __( 'Countries Served', 'estecapelli' ) ),
			array( 'icon' => 'headset',  'value' => '24/7',                           'label' => __( 'Support Team', 'estecapelli' ) ),
		);
	}
}

if ( ! function_exists( 'estecapelli_home_services_from_treatments' ) ) {
	/**
	 * Build the homepage services payload from real `treatment` posts.
	 *
	 * Tabs are the `treatment_category` terms (ordered by a known field-of-care
	 * sequence); each card is a treatment in that category, with its cover =
	 * featured image, title = post title, description = excerpt and link =
	 * permalink. Returns an empty array when no treatments are published yet,
	 * so the caller can fall back to the curated defaults.
	 */
	function estecapelli_home_services_from_treatments() {
		if ( ! post_type_exists( 'treatment' ) || ! taxonomy_exists( 'treatment_category' ) ) {
			return array();
		}

		// Icons aren't stored on terms — map the known category slugs.
		$icons = array(
			'hair-transplant'   => 'hair',
			'plastic-surgery'   => 'sparkles',
			'dental-treatment'  => 'tooth',
			'medical-treatment' => 'medical-plus',
		);
		// Preferred tab order; unknown categories fall in after these.
		$order   = array( 'hair-transplant', 'plastic-surgery', 'dental-treatment', 'medical-treatment' );
		// Show more than the 4 visible at once; the rest page in via the carousel.
		$per_tab = 12;

		$terms = get_terms( array(
			'taxonomy'   => 'treatment_category',
			'hide_empty' => true,
		) );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return array();
		}

		// Order by each term's English source slug so translated categories keep
		// the same field-of-care sequence as the English homepage.
		usort( $terms, function ( $a, $b ) use ( $order ) {
			$ia = array_search( estecapelli_wpml_source_term_slug( $a ), $order, true );
			$ib = array_search( estecapelli_wpml_source_term_slug( $b ), $order, true );
			$ia = ( false === $ia ) ? PHP_INT_MAX : $ia;
			$ib = ( false === $ib ) ? PHP_INT_MAX : $ib;
			return $ia <=> $ib;
		} );

		$categories = array();

		foreach ( $terms as $term ) {
			$query = new WP_Query( array(
				'post_type'           => 'treatment',
				'post_status'         => 'publish',
				'posts_per_page'      => $per_tab,
				'orderby'             => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'tax_query'           => array(
					array(
						'taxonomy'         => 'treatment_category',
						'field'            => 'term_id',
						'terms'            => $term->term_id,
						'include_children' => false,
					),
				),
			) );

			if ( ! $query->have_posts() ) {
				continue;
			}

			// Apply the fixed hair-transplant display order (others keep query
			// order). Rank on the English source slug so translated treatments
			// sort identically to the English homepage.
			$posts = $query->posts;
			if ( function_exists( 'estecapelli_treatment_order_rank' ) ) {
				usort(
					$posts,
					function ( $a, $b ) {
						$ra = estecapelli_treatment_order_rank( estecapelli_wpml_source_post_slug( $a ) );
						$rb = estecapelli_treatment_order_rank( estecapelli_wpml_source_post_slug( $b ) );
						return ( $ra !== $rb ) ? $ra - $rb : 0;
					}
				);
			}

			$items = array();
			foreach ( $posts as $post ) {
				$items[] = array(
					'tag'         => $term->name,
					'title'       => get_the_title( $post ),
					'description' => wp_trim_words( get_the_excerpt( $post ), 16, '…' ),
					'image'       => (string) get_the_post_thumbnail_url( $post, 'large' ),
					'url'         => get_permalink( $post ),
					// Optional editorial flag (POPULAR / SIGNATURE …) via post meta.
					'badge'       => (string) get_post_meta( $post->ID, '_home_card_badge', true ),
				);
			}

			// English source slug drives the icon (SVG file + fallback glyph) so
			// every translated category shows the same icon as the English tab.
			$source_slug  = estecapelli_wpml_source_term_slug( $term );
			$categories[] = array(
				'key'      => $term->slug,
				'icon_key' => $source_slug,
				'icon'     => $icons[ $source_slug ] ?? 'medical-plus',
				'label'    => $term->name,
				'items'    => $items,
			);
		}
		wp_reset_postdata();

		if ( empty( $categories ) ) {
			return array();
		}

		return array(
			'eyebrow'    => __( 'What We Treat', 'estecapelli' ),
			'headline'   => __( 'Pick a field. See our signature treatments.', 'estecapelli' ),
			'lead'       => __( 'Switch between tabs to explore the methods we are best known for in each field of care.', 'estecapelli' ),
			'categories' => $categories,
		);
	}
}

if ( ! function_exists( 'estecapelli_home_services' ) ) {
	/**
	 * Tabbed featured-services block for the homepage.
	 *
	 * Source of truth, in order: an ACF option override, then the real
	 * `treatment` posts (covers + cards read from the service section), then a
	 * curated hardcoded fallback so the block always renders something.
	 */
	function estecapelli_home_services() {
		if ( function_exists( 'get_field' ) ) {
			$acf = get_field( 'home_services', 'option' );
			if ( ! empty( $acf ) ) {
				return $acf;
			}
		}

		$from_cpt = estecapelli_home_services_from_treatments();
		if ( ! empty( $from_cpt['categories'] ) ) {
			return $from_cpt;
		}

		$img = get_template_directory_uri() . '/assets/images/services/';

		$hair_tag    = __( 'Hair Transplant', 'estecapelli' );
		$plastic_tag = __( 'Plastic Surgery', 'estecapelli' );
		$dental_tag  = __( 'Dental Treatment', 'estecapelli' );
		$medical_tag = __( 'Medical Treatment', 'estecapelli' );

		return array(
			'eyebrow'    => __( 'What We Treat', 'estecapelli' ),
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
							'url'         => home_url( '/en/hair-transplant/sapphire-fue-hair-transplant' ),
						),
						array(
							'tag'         => $hair_tag,
							'title'       => __( 'DHI', 'estecapelli' ),
							'description' => __( 'Choi-pen implantation for precise angle and direction control.', 'estecapelli' ),
							'image'       => $img . 'dhi.jpg',
							'url'         => home_url( '/en/hair-transplant/dhi-hair-transplant' ),
						),
						array(
							'tag'         => $hair_tag,
							'title'       => __( 'Exosome Treatment', 'estecapelli' ),
							'description' => __( 'Cell-regenerating exosomes that keep follicles alive longer.', 'estecapelli' ),
							'image'       => $img . 'exosome.jpg',
							'url'         => home_url( '/en/hair-transplant/exosome-fue-hair-transplant' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'tag'         => $hair_tag,
							'title'       => __( 'VITA Treatment', 'estecapelli' ),
							'description' => __( "Estecapelli's signature protocol that revitalises scalp & strands.", 'estecapelli' ),
							'image'       => $img . 'vita.jpg',
							'url'         => home_url( '/en/hair-transplant/vita-treatment' ),
							'badge'       => __( 'SIGNATURE', 'estecapelli' ),
						),
					),
				),
				array(
					'key'   => 'plastic-surgery',
					'icon'  => 'sparkles',
					'label' => $plastic_tag,
					'items' => array(
						array(
							'tag'         => $plastic_tag,
							'title'       => __( 'Rhinoplasty', 'estecapelli' ),
							'description' => __( 'Nose reshaping that refines proportions and function.', 'estecapelli' ),
							'url'         => home_url( '/en/plastic-surgery/rhinoplasty' ),
						),
						array(
							'tag'         => $plastic_tag,
							'title'       => __( 'BBL', 'estecapelli' ),
							'description' => __( 'Brazilian Butt Lift — natural contouring with fat transfer.', 'estecapelli' ),
							'url'         => home_url( '/en/plastic-surgery/bbl' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'tag'         => $plastic_tag,
							'title'       => __( 'Facelift', 'estecapelli' ),
							'description' => __( 'Restores facial contour and reduces visible signs of aging.', 'estecapelli' ),
							'url'         => home_url( '/en/plastic-surgery/face-and-neck-lift-surgery' ),
						),
						array(
							'tag'         => $plastic_tag,
							'title'       => __( 'Breast Aesthetics', 'estecapelli' ),
							'description' => __( 'Augmentation, lift and reduction tailored to your goals.', 'estecapelli' ),
							'url'         => home_url( '/en/plastic-surgery/breast-aesthetics-breast-surgery' ),
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
							'url'         => home_url( '/en/dental-treatment/dental-implant' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'tag'         => $dental_tag,
							'title'       => __( 'Smile Design', 'estecapelli' ),
							'description' => __( 'A bespoke makeover that reshapes your entire smile.', 'estecapelli' ),
							'url'         => home_url( '/en/dental-treatment/hollywood-smile' ),
						),
						array(
							'tag'         => $dental_tag,
							'title'       => __( 'Veneers', 'estecapelli' ),
							'description' => __( 'Thin porcelain shells for a flawless front-of-tooth finish.', 'estecapelli' ),
							'url'         => home_url( '/en/dental-treatment' ),
						),
						array(
							'tag'         => $dental_tag,
							'title'       => __( 'Teeth Whitening', 'estecapelli' ),
							'description' => __( 'Professional bleaching for noticeably brighter teeth.', 'estecapelli' ),
							'url'         => home_url( '/en/dental-treatment' ),
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
							'url'         => home_url( '/en/contact' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'tag'         => $medical_tag,
							'title'       => __( 'Dermal Fillers', 'estecapelli' ),
							'description' => __( 'Restores volume to cheeks, jawline and under-eye areas.', 'estecapelli' ),
							'url'         => home_url( '/en/contact' ),
						),
						array(
							'tag'         => $medical_tag,
							'title'       => __( 'PRP Treatment', 'estecapelli' ),
							'description' => __( 'Platelet-rich plasma therapy for skin and hair regeneration.', 'estecapelli' ),
							'url'         => home_url( '/en/contact' ),
							'badge'       => __( 'POPULAR', 'estecapelli' ),
						),
						array(
							'tag'         => $medical_tag,
							'title'       => __( 'Skin Rejuvenation', 'estecapelli' ),
							'description' => __( 'Non-surgical protocols for firmer, brighter, healthier skin.', 'estecapelli' ),
							'url'         => home_url( '/en/contact' ),
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
		$defaults = estecapelli_why_choose_defaults();
		if ( ! function_exists( 'get_field' ) ) {
			return $defaults;
		}
		return estecapelli_acf_overlay( $defaults, get_field( 'home_why_choose', 'option' ) );
	}
}

if ( ! function_exists( 'estecapelli_why_choose_defaults' ) ) {
	/** Built-in "Why Choose" content — the base the ACF editor overlays. */
	function estecapelli_why_choose_defaults() {
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
					'url'   => home_url( '/en/contact' ),
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
		$defaults = estecapelli_signature_methods_defaults();
		if ( ! function_exists( 'get_field' ) ) {
			return $defaults;
		}
		return estecapelli_acf_overlay( $defaults, get_field( 'home_signature_methods', 'option' ) );
	}
}

if ( ! function_exists( 'estecapelli_signature_methods_defaults' ) ) {
	/** Built-in "Signature Methods" content — the base the ACF editor overlays. */
	function estecapelli_signature_methods_defaults() {
		$img = get_template_directory_uri() . '/assets/images/expertise/';

		return array(
			'eyebrow'  => __( 'Our Expertise', 'estecapelli' ),
			'headline' => __( 'Three methods that shape every treatment.', 'estecapelli' ),
			'lead'     => __( 'Two are exclusively ours. One sets the standard for personalised planning. All three power the results our patients trust us for.', 'estecapelli' ),

			'cta_secondary' => array(
				'label' => __( 'Schedule a Free Consultation', 'estecapelli' ),
				'url'   => home_url( '/en/contact' ),
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
						'url'   => home_url( '/en/hair-transplant/exosome-fue-hair-transplant' ),
					),
				),
				array(
					'key'        => 'tricholab',
					'eyebrow'    => __( 'AI-Powered Diagnosis', 'estecapelli' ),
					'title'      => __( 'TrichoLab', 'estecapelli' ),
					'subtitle'   => __( 'Millimetric Hair & Scalp Analysis', 'estecapelli' ),
					'tease'      => __( 'AI maps your scalp before a single graft is planned.', 'estecapelli' ),
					'image'      => $img . 'tricholab.webp',
					'icon'       => 'target',
					'stat'       => __( 'Millimetric', 'estecapelli' ),
					'stat_label' => __( 'Precision per scalp scan', 'estecapelli' ),
					'body'       => __( 'TrichoLab examines your hair and scalp with millimetric accuracy — measuring follicle density, thickness, donor capacity, and loss patterns — so every graft is planned for your unique anatomy and the result feels naturally yours.', 'estecapelli' ),
					'cta'        => array(
						'label' => __( 'Learn more about TrichoLab', 'estecapelli' ),
						'url'   => home_url( '/en/hair-transplant/tricholab' ),
					),
				),
				array(
					'key'        => 'vita',
					'eyebrow'    => __( 'Signature Protocol · Estecapelli Exclusive', 'estecapelli' ),
					'title'      => __( 'VITA Treatment', 'estecapelli' ),
					'subtitle'   => __( 'Power Derived from Vitamins', 'estecapelli' ),
					'tease'      => __( 'A vitamin-cooled bath that keeps grafts strong out of the body.', 'estecapelli' ),
					'image'      => get_template_directory_uri() . '/assets/images/techniques/vita-cover.webp',
					'icon'       => 'sparkles',
					'stat'       => __( 'Cool-Vapor', 'estecapelli' ),
					'stat_label' => __( 'Vitamin-nourished grafts', 'estecapelli' ),
					'body'       => __( 'Grafts lose strength the moment they leave the body. Our VITA Protocol bathes them in a specially formulated vitamin cocktail with cool-vapor application — keeping every follicle alive, nourished, and resilient until placement.', 'estecapelli' ),
					'cta'        => array(
						'label' => __( 'Learn more about VITA', 'estecapelli' ),
						'url'   => home_url( '/en/hair-transplant/vita-treatment' ),
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
	 * ACF (Homepage Content options page) overlays the defaults per-field: any
	 * empty field or image keeps the built-in value, matched by patient Key.
	 */
	function estecapelli_patient_stories() {
		$lang = estecapelli_stories_current_lang();

		// Turkish: patient testimonials must not be published for legal reasons.
		// Empty stories makes the whole section render nothing — the template
		// (template-parts/patient-stories.php) returns early when 'stories' is empty.
		if ( 'tr' === $lang ) {
			return array( 'eyebrow' => '', 'headline' => '', 'lead' => '', 'stories' => array() );
		}

		// A language with its own authored patient set takes over completely for
		// that language (its own patients, country, flag, grafts, video, story).
		$localized = estecapelli_patient_stories_localized();
		if ( isset( $localized[ $lang ] ) ) {
			return $localized[ $lang ];
		}

		$defaults = estecapelli_patient_stories_defaults();
		if ( ! function_exists( 'get_field' ) ) {
			return $defaults;
		}

		$out = $defaults;

		// Section text — overlay only the fields the editor actually filled in.
		foreach ( array(
			'eyebrow'  => 'home_stories_eyebrow',
			'headline' => 'home_stories_headline',
			'lead'     => 'home_stories_lead',
		) as $k => $field ) {
			$v = get_field( $field, 'option' );
			if ( is_string( $v ) && '' !== $v ) {
				$out[ $k ] = $v;
			}
		}

		// Patient list — overlay each row onto the default with the same Key, so an
		// empty field or image keeps the current value. No rows = pure defaults.
		$rows = get_field( 'home_patient_stories_list', 'option' );
		if ( ! empty( $rows ) && is_array( $rows ) ) {
			$by_key = array();
			foreach ( $defaults['stories'] as $s ) {
				$by_key[ $s['key'] ] = $s;
			}
			$merged = array();
			foreach ( $rows as $row ) {
				$key  = ! empty( $row['key'] ) ? sanitize_title( $row['key'] ) : ( ! empty( $row['name'] ) ? sanitize_title( $row['name'] ) : '' );
				$base = isset( $by_key[ $key ] ) ? $by_key[ $key ] : array(
					'key'         => $key ? $key : 'patient',
					'name'        => '',
					'country'     => '',
					'country_iso' => '',
					'flag'        => '',
					'grafts'      => '',
					'technique'   => '',
					'rating'      => 5,
					'video_id'    => '',
					'pre_title'   => '',
					'body'        => '',
				);
				foreach ( array( 'name', 'country', 'flag', 'grafts', 'technique', 'video_id', 'pre_title', 'body', 'poster_pos', 'photo_pos' ) as $f ) {
					if ( isset( $row[ $f ] ) && is_string( $row[ $f ] ) && '' !== $row[ $f ] ) {
						$base[ $f ] = $row[ $f ];
					}
				}
				if ( isset( $row['rating'] ) && '' !== $row['rating'] && null !== $row['rating'] ) {
					$base['rating'] = (int) $row['rating'];
				}
				if ( ! empty( $row['poster'] ) ) {
					$base['poster'] = is_array( $row['poster'] ) ? ( isset( $row['poster']['url'] ) ? $row['poster']['url'] : '' ) : $row['poster'];
				}
				if ( ! empty( $row['photo'] ) ) {
					$base['photo'] = is_array( $row['photo'] ) ? ( isset( $row['photo']['url'] ) ? $row['photo']['url'] : '' ) : $row['photo'];
				}
				$merged[] = $base;
			}
			if ( ! empty( $merged ) ) {
				$out['stories'] = $merged;
			}
		}

		return $out;
	}
}

if ( ! function_exists( 'estecapelli_patient_stories_defaults' ) ) {
	/**
	 * Built-in Patient Stories content — the base the ACF editor overlays.
	 */
	function estecapelli_patient_stories_defaults() {
		return array(
			'eyebrow'  => __( 'Real Stories', 'estecapelli' ),
			'headline' => __( 'Their results speak louder than any ad ever could.', 'estecapelli' ),
			'lead'     => __( 'Hear, in their own words, how patients from around the world describe their Estecapelli journey — from first consultation to long-term result.', 'estecapelli' ),

			// Real patient stories — added one by one. Each may set 'image' (a real
			// photo used for the poster + wall thumbnail); without it the renderer
			// falls back to the YouTube thumbnail.
			'stories' => array(
				array(
					'key'         => 'alexandre-t',
					'name'        => __( 'Alexandre T.', 'estecapelli' ),
					'country'     => __( 'Ireland', 'estecapelli' ),
					'country_iso' => 'IE',
					'flag'        => '🇮🇪',
					'grafts'      => '5,000',
					'technique'   => 'DHI Vita',
					'rating'      => 5,
					'video_id'    => 'NJhvWUPd370',
					// Big stage box = the YouTube video thumbnail; side playlist = the
					// patient's own after-photo.
					'poster'      => 'https://i.ytimg.com/vi/NJhvWUPd370/oardefault.jpg',
					'photo'       => get_template_directory_uri() . '/assets/images/stories/alexandre-t-after.jpg',
					'pre_title'   => __( 'Hairline redesigned, density restored from front to crown.', 'estecapelli' ),
					'body'        => __( 'Alexandre came to our clinic from Ireland to redesign his hairline and improve density across the frontal area, mid-scalp and crown. Based on his desired hair model and donor capacity, we prioritised a natural, face-appropriate hairline first, then a balanced, homogeneous distribution through the mid-scalp and crown. The procedure was performed with the DHI Vita technique in a single session, transplanting 5,000 grafts in total — around 3,000 in the frontal area and hairline and roughly 2,000 across the mid-scalp and crown. The operation progressed normally, with PRP support applied at the end.', 'estecapelli' ),
				),
				array(
					'key'         => 'craig-n',
					'name'        => __( 'Craig N.', 'estecapelli' ),
					'country'     => __( 'Scotland', 'estecapelli' ),
					'country_iso' => 'GB-SCT',
					'flag'        => '🏴󠁧󠁢󠁳󠁣󠁴󠁿',
					'grafts'      => '5,400',
					'technique'   => 'FUE Vita',
					'rating'      => 5,
					'video_id'    => 'YrZleFBZ9j8',
					'poster'      => 'https://i.ytimg.com/vi/YrZleFBZ9j8/oardefault.jpg',
					'photo'       => get_template_directory_uri() . '/assets/images/stories/craig-n-after.jpg',
					'pre_title'   => __( 'A denser frontal hairline and a fuller crown.', 'estecapelli' ),
					'body'        => __( 'Craig came to our clinic from Scotland to improve his hairline and increase density across the frontal area and crown. Based on his hair model and consultation plan, we prioritised a denser, more natural frontal hairline first, then a homogeneous graft distribution through the top and crown to cover the visible gaps. The procedure was performed with the FUE Vita technique in a single session, transplanting 5,400 grafts in total. Grafts were extracted evenly from the donor area with good quality, then implanted with attention to natural direction, density balance and overall coverage. The operation progressed normally, with PRP support applied at the end.', 'estecapelli' ),
				),
				array(
					'key'         => 'dale-f',
					'name'        => __( 'Dale F.', 'estecapelli' ),
					'country'     => __( 'England', 'estecapelli' ),
					'country_iso' => 'GB-ENG',
					'flag'        => '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
					'grafts'      => '4,500',
					'technique'   => 'Vita Protocol',
					'rating'      => 5,
					'video_id'    => 'crCXBnCyKNA',
					'poster'      => 'https://i.ytimg.com/vi/crCXBnCyKNA/oardefault.jpg',
					'photo'       => get_template_directory_uri() . '/assets/images/stories/dale-f-after.jpg',
					'pre_title'   => __( 'Stronger density across front, mid-scalp and crown.', 'estecapelli' ),
					'body'        => __( 'Dale came to our clinic from England to increase density across the frontal area, mid-scalp and crown. Based on his consultation plan and donor capacity, we focused on building stronger density in the front and crown first, while balancing the mid-scalp for an even result. The procedure was performed with the Vita protocol in a single session, transplanting 4,500 grafts in total. With a good-density donor area, the grafts were distributed to the planned coverage needs — higher density prioritised in the frontal area and crown, and the mid-scalp reinforced for a more homogeneous overall look. PRP support was applied at the end of the procedure.', 'estecapelli' ),
				),
				array(
					'key'         => 'danny-j',
					'name'        => __( 'Danny J.', 'estecapelli' ),
					'country'     => __( 'England', 'estecapelli' ),
					'country_iso' => 'GB-ENG',
					'flag'        => '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
					'grafts'      => '5,000',
					'technique'   => 'FUE Vita',
					'rating'      => 5,
					'video_id'    => '9R4HJY_PKJI',
					'poster'      => 'https://i.ytimg.com/vi/9R4HJY_PKJI/oardefault.jpg',
					'photo'       => get_template_directory_uri() . '/assets/images/stories/danny-j-after.jpg',
					'pre_title'   => __( 'Frontal density extended through the mid-scalp.', 'estecapelli' ),
					'body'        => __( 'Danny came to our clinic from England to improve density in his frontal area and extend coverage toward the mid-scalp. Based on his consultation plan, we prioritised harvesting the maximum number of grafts and building a dense, natural-looking result in the thinning zones, aiming for good density up to the midsection. The procedure was performed with the FUE Vita technique in a single session, transplanting 5,000 grafts in total. Grafts were extracted homogeneously from the donor area with good quality, then implanted with attention to natural direction, density balance and frontal-to-mid-scalp coverage. The operation progressed normally, with PRP support applied at the end.', 'estecapelli' ),
				),
				array(
					'key'         => 'pascal-s',
					'name'        => __( 'Pascal S.', 'estecapelli' ),
					'country'     => __( 'Canada', 'estecapelli' ),
					'country_iso' => 'CA',
					'flag'        => '🇨🇦',
					'grafts'      => '5,000',
					'technique'   => 'Exosome FUE',
					'rating'      => 5,
					'video_id'    => 'qeFHkZVpK1o',
					// This video's default thumbnail shows the presenter, not Pascal, so the
					// big box uses an actual frame from the video where Pascal waves (sd3).
					// The side thumbnail keeps his after-photo, framed onto his face (which
					// sits low in that shot).
					'poster'      => get_template_directory_uri() . '/assets/images/stories/pascal-s-poster.jpg',
					'photo'       => get_template_directory_uri() . '/assets/images/stories/pascal-s-after.jpg',
					'photo_pos'   => 'center 72%',
					'pre_title'   => __( 'Frontal density extended toward the mid-scalp.', 'estecapelli' ),
					'body'        => __( 'Pascal came to our clinic from Canada to improve density in his frontal area and extend coverage toward the mid-scalp. Based on his consultation plan and donor capacity, we planned maximum graft extraction to cover the frontal area up to the midsection with good density, with a second session planned for full coverage. The procedure was performed with the Exosome FUE technique, transplanting 5,000 grafts in this session. Grafts were extracted homogeneously from the donor area with good hair quality, then implanted with attention to natural direction, density balance and frontal-to-mid-scalp coverage. PRP support was applied at the end of the procedure.', 'estecapelli' ),
				),
				array(
					'key'         => 'ricardo-d',
					'name'        => __( 'Ricardo D.', 'estecapelli' ),
					'country'     => __( 'Ireland', 'estecapelli' ),
					'country_iso' => 'IE',
					'flag'        => '🇮🇪',
					'grafts'      => '4,200',
					'technique'   => 'DHI Vita',
					'rating'      => 5,
					'video_id'    => '63VpV8fHm70',
					'poster'      => 'https://i.ytimg.com/vi/63VpV8fHm70/oardefault.jpg',
					'photo'       => get_template_directory_uri() . '/assets/images/stories/ricardo-d-after.jpg',
					'pre_title'   => __( 'A natural hairline with balanced front-to-crown density.', 'estecapelli' ),
					'body'        => __( 'Ricardo came to our clinic from Ireland to restore a natural hairline and improve density across the frontal area, temples and a small thinning area on the crown. Based on his consultation plan and his wish for a natural-looking result, we adjusted the frontal line to his facial structure and planned dense implantation in the front, with additional grafts placed in the temples and crown for overall balance. The procedure was performed with the DHI Vita technique in a single session, transplanting approximately 4,200 grafts. The grafts were extracted with good hair quality, then implanted with attention to natural direction, symmetry, density balance and frontal-to-crown coverage. The operation progressed normally, with PRP support at the end.', 'estecapelli' ),
				),
				array(
					'key'         => 'sam-c',
					'name'        => __( 'Sam C.', 'estecapelli' ),
					'country'     => __( 'Ireland', 'estecapelli' ),
					'country_iso' => 'IE',
					'flag'        => '🇮🇪',
					'grafts'      => '6,200',
					'technique'   => 'FUE Vita',
					'rating'      => 5,
					'video_id'    => 'A6TO--UisSg',
					'poster'      => 'https://i.ytimg.com/vi/A6TO--UisSg/oardefault.jpg',
					'photo'       => get_template_directory_uri() . '/assets/images/stories/sam-c-after.jpg',
					'pre_title'   => __( 'Full-coverage restoration in a single session.', 'estecapelli' ),
					'body'        => __( 'Sam came to our clinic from Ireland to improve his hairline and increase overall density across the frontal area, mid-scalp and crown. Based on his consultation plan and his wish for full coverage in a single session, we planned maximum graft extraction and focused on building good density in the frontal hairline first, then distributed the remaining grafts through the mid-scalp and crown for balanced coverage. The procedure was performed with the FUE Vita technique in a single session, transplanting 6,200 grafts in total. Grafts were extracted homogeneously from the donor area with good quality, then implanted with attention to natural direction, density balance and overall coverage. The operation progressed normally, with PRP support at the end.', 'estecapelli' ),
				),
			),
		);
	}
}

if ( ! function_exists( 'estecapelli_stories_current_lang' ) ) {
	/**
	 * Current display language for the Patient Stories section, normalised to the
	 * public indexed code (en/es/it/fr/pl/pt/tr). WPML may store Portuguese as
	 * "pt-pt"; everything else already matches the indexed code.
	 */
	function estecapelli_stories_current_lang() {
		$lang = apply_filters( 'wpml_current_language', null );
		if ( ! $lang && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$lang = ICL_LANGUAGE_CODE;
		}
		$lang = is_string( $lang ) ? strtolower( $lang ) : '';
		if ( 'pt-pt' === $lang ) {
			$lang = 'pt';
		}
		return $lang;
	}
}

if ( ! function_exists( 'estecapelli_patient_stories_localized' ) ) {
	/**
	 * Per-language authored Patient Stories.
	 *
	 * Each language listed here fully replaces the English default set with its
	 * own real patients — same structure/fields as English (name, country, flag,
	 * grafts, technique, video, caption, story), only the patient and their
	 * country change. Video is always the YouTube (Shorts) id. Languages NOT
	 * listed keep the English defaults; Turkish is handled separately (hidden).
	 *
	 * Images live in /assets/images/stories/{lang}/. Patients without a usable
	 * photo use a still frame pulled from their own testimonial video.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	function estecapelli_patient_stories_localized() {
		$img = function ( $file ) {
			return get_template_directory_uri() . '/assets/images/stories/' . $file;
		};

		return array(
			'es' => array(
				'eyebrow'  => 'Historias reales',
				'headline' => 'Sus resultados hablan más alto que cualquier anuncio.',
				'lead'     => 'Escucha, en sus propias palabras, cómo pacientes de todo el mundo describen su experiencia con Estecapelli, desde la primera consulta hasta el resultado a largo plazo.',
				'stories'  => array(
					array(
						'key'         => 'jose-luis-p',
						'name'        => 'José Luis P.',
						'country'     => 'España',
						'country_iso' => 'ES',
						'flag'        => '🇪🇸',
						'grafts'      => '4,700',
						'technique'   => 'FUE Vita',
						'rating'      => 5,
						'video_id'    => 'ootaBdwLjvc',
						'poster'      => $img( 'es/jose-luis-p.jpg' ),
						'poster_pos'  => 'center 32%',
						'photo'       => $img( 'es/jose-luis-p.jpg' ),
						'photo_pos'   => 'center 32%',
						'pre_title'   => 'Línea frontal densa y cobertura hasta la zona media.',
						'body'        => 'José Luis llegó desde España a Estecapelli para realizarse un trasplante capilar con la técnica FUE Vita. Tras una evaluación detallada de la zona donante y de las áreas afectadas por la pérdida de cabello, se diseñó un plan de tratamiento personalizado dividido en dos sesiones. Durante la primera intervención se planificó extraer el mayor número de injertos posible de forma segura. La implantación comenzó en la zona frontal y continuó hacia la parte media del cuero cabelludo, con el objetivo de cubrir la mayor superficie posible y mejorar la densidad de manera equilibrada. Se recomendó una segunda sesión para completar las áreas restantes y conseguir una cobertura más uniforme. Después del procedimiento, José Luis recibió todas las indicaciones postoperatorias necesarias y comenzó su recuperación bajo el seguimiento del equipo médico de Estecapelli.',
					),
					array(
						'key'         => 'silvio-r',
						'name'        => 'Silvio R.',
						'country'     => 'Venezuela',
						'country_iso' => 'VE',
						'flag'        => '🇻🇪',
						'grafts'      => '5,600',
						'technique'   => 'FUE Vita',
						'rating'      => 5,
						'video_id'    => 'Uq84U2zkbr8',
						'poster'      => $img( 'es/silvio-r.jpg' ),
						'poster_pos'  => 'center 28%',
						'photo'       => $img( 'es/silvio-r.jpg' ),
						'photo_pos'   => 'center 28%',
						'pre_title'   => 'Trasplante capilar y de barba con densidad frontal alta.',
						'body'        => 'Silvio Rafael llegó desde Venezuela a Estecapelli para someterse a un trasplante capilar y de barba con la técnica FUE Vita. Tras una evaluación detallada de la zona donante y de las áreas que requerían mayor cobertura, se diseñó un plan de tratamiento personalizado según sus necesidades. Durante el procedimiento se planificó extraer el máximo número de injertos posible de forma segura. La prioridad se centró en la zona frontal del cuero cabelludo, donde se realizó una implantación de alta densidad para mejorar la línea capilar y conseguir una apariencia más definida y natural. Además, también se planificó aumentar la densidad de la barba y de las cejas, distribuyendo los injertos de acuerdo con la cantidad disponible y manteniendo un resultado armónico con los rasgos faciales del paciente. Después del procedimiento, Silvio Rafael recibió todas las indicaciones postoperatorias necesarias y comenzó su recuperación bajo el seguimiento del equipo médico de Estecapelli.',
					),
					array(
						'key'         => 'tony-a',
						'name'        => 'Tony A.',
						'country'     => 'Estados Unidos',
						'country_iso' => 'US',
						'flag'        => '🇺🇸',
						'grafts'      => '5,800',
						'technique'   => 'FUE Vita',
						'rating'      => 5,
						'video_id'    => 'SAkP6s_XX7k',
						'poster'      => $img( 'es/tony-a.jpg' ),
						'poster_pos'  => 'center 30%',
						'photo'       => $img( 'es/tony-a.jpg' ),
						'photo_pos'   => 'center 30%',
						'pre_title'   => 'Cobertura completa en una sola sesión.',
						'body'        => 'Tony llegó desde Estados Unidos a Estecapelli para someterse a un trasplante capilar con la técnica FUE Vita. Tras una evaluación detallada de la zona donante y de las áreas afectadas por la pérdida de cabello, se diseñó un plan de tratamiento personalizado según sus preferencias. Durante el procedimiento, la zona frontal recibió la mayor prioridad, con una implantación de alta densidad para mejorar la línea capilar y crear una apariencia más definida y natural. En la zona media, los injertos se distribuyeron con una densidad moderada, mientras que en la coronilla se realizó una cobertura más ligera. Aunque el equipo médico recomendó completar el tratamiento en dos sesiones, Tony prefirió tratar todas las zonas en una sola intervención. Después del procedimiento, recibió todas las indicaciones postoperatorias necesarias y comenzó su recuperación bajo el seguimiento del equipo médico de Estecapelli.',
					),
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
		$defaults = estecapelli_facilities_defaults();
		if ( ! function_exists( 'get_field' ) ) {
			return $defaults;
		}
		return estecapelli_acf_overlay( $defaults, get_field( 'home_facilities', 'option' ) );
	}
}

if ( ! function_exists( 'estecapelli_facilities_defaults' ) ) {
	/** Built-in "Our Facilities" content — the base the ACF editor overlays. */
	function estecapelli_facilities_defaults() {
		$img     = get_template_directory_uri() . '/assets/images/';
		$contact = home_url( '/en/contact' );

		return array(
			'eyebrow'  => __( 'Our Clinic', 'estecapelli' ),
			'headline' => __( 'Step inside our Istanbul clinic', 'estecapelli' ),
			'lead'     => __( 'A Ministry-of-Health licensed, hospital-grade facility in the heart of Istanbul — modern operation rooms, sterile theatres and a permanent on-site medical team.', 'estecapelli' ),
			'cta'      => array(
				'label' => __( 'Book a Free Consultation', 'estecapelli' ),
				'url'   => $contact,
			),

			// Mixed photo + video gallery of the clinic. type: image | video
			// (video opens the shared patient-stories lightbox).
			// Big video tile (left blank for now — swap in the clinic video later),
			// then four room tiles. Each room tile carries a gallery of photos:
			// the first is the cover; clicking opens the lightbox to browse them.
			'gallery' => array(
				array( 'type' => 'video', 'image' => $img . 'facilities/clinic-cover.webp', 'video' => 'uP3r9sn-EtM', 'caption' => __( 'Clinic walkthrough', 'estecapelli' ) ),
				array(
					'type'    => 'image',
					'caption' => __( 'Hair Transplant Surgery Room', 'estecapelli' ),
					'images'  => array(
						$img . 'clinic/surgery/surgery-1.webp',
						$img . 'clinic/surgery/surgery-2.webp',
						$img . 'clinic/surgery/surgery-3.webp',
						$img . 'clinic/surgery/surgery-4.webp',
						$img . 'clinic/surgery/surgery-5.webp',
					),
				),
				array(
					'type'    => 'image',
					'caption' => __( 'TrichoLab Room', 'estecapelli' ),
					'images'  => array(
						$img . 'clinic/tricolab/tricolab-1.webp',
						$img . 'clinic/tricolab/tricolab-2.webp',
					),
				),
				array(
					'type'    => 'image',
					'caption' => __( 'Lobby', 'estecapelli' ),
					'images'  => array(
						$img . 'clinic/lobby/lobby-1.webp',
						$img . 'clinic/lobby/lobby-2.webp',
						$img . 'clinic/lobby/lobby-3.webp',
					),
				),
				array(
					'type'    => 'image',
					'caption' => __( 'Dental Clinic', 'estecapelli' ),
					'images'  => array(
						$img . 'clinic/dental/dental-1.webp',
					),
				),
			),

			// Partner hotels — a rotating strip. Add 'image' => url to a logo entry
			// to show a real logo instead of the text wordmark. 'stars' shows a small
			// rating under the name (0/omit = hide). Source: partner-hotels sheet.
			'partners' => array(
				'title' => __( 'Our partner hotels', 'estecapelli' ),
				'logos' => array(
					array( 'label' => 'Hilton',             'stars' => 5, 'image' => $img . 'hotels/hilton.webp' ),
					array( 'label' => 'Grand Cevahir',      'stars' => 5, 'image' => $img . 'hotels/grand-cevahir.webp' ),
					array( 'label' => 'Crowne Plaza',       'stars' => 5, 'image' => $img . 'hotels/crowne-plaza.webp' ),
					array( 'label' => 'Craton',             'stars' => 5, 'image' => $img . 'hotels/craton.webp' ),
					array( 'label' => 'The Elysium Taksim', 'stars' => 5, 'image' => $img . 'hotels/elysium.webp' ),
					array( 'label' => 'La Quinta',          'stars' => 5, 'image' => $img . 'hotels/la-quinta.webp' ),
					array( 'label' => 'Days Inn',           'stars' => 4, 'image' => $img . 'hotels/days-inn.webp' ),
					array( 'label' => 'Naz City',           'stars' => 4, 'image' => $img . 'hotels/naz-city.webp' ),
					array( 'label' => 'Perahill',           'stars' => 4, 'image' => $img . 'hotels/perahill.webp' ),
					array( 'label' => 'Halifaks',           'stars' => 4, 'image' => $img . 'hotels/halifaks.webp' ),
					array( 'label' => 'Urban',              'stars' => 4, 'image' => $img . 'hotels/urban.webp' ),
					array( 'label' => 'Boursier',           'stars' => 4, 'image' => $img . 'hotels/boursier.webp' ),
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
