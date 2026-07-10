<?php
/**
 * Table of contents for long-form content (blog posts + plain pages).
 *
 * Two helpers: one adds stable ids to the h2/h3 headings in a rendered content
 * string and collects them; the other renders the TOC nav from that list. The
 * scroll-spy + collapse behaviour lives in assets/js/main.js (initTOC).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'estecapelli_extract_toc' ) ) {
	/**
	 * Inject anchor ids into a content string's h2/h3 headings and collect them.
	 *
	 * @param string $html Rendered content (already run through the_content filters).
	 * @return array{0:string,1:array<int,array{level:int,id:string,text:string}>}
	 *               [ modified_html, toc_items ].
	 */
	function estecapelli_extract_toc( $html ) {
		$items = array();
		if ( ! is_string( $html ) || '' === trim( $html ) ) {
			return array( (string) $html, $items );
		}

		$used = array();

		$html = preg_replace_callback(
			'/<h([23])\b([^>]*)>(.*?)<\/h\1>/is',
			function ( $m ) use ( &$items, &$used ) {
				$level = (int) $m[1];
				$attrs = $m[2];
				$inner = $m[3];

				$text = trim( html_entity_decode( wp_strip_all_tags( $inner ), ENT_QUOTES, 'UTF-8' ) );
				if ( '' === $text ) {
					return $m[0]; // Empty heading — leave it untouched, skip in TOC.
				}

				// Reuse an existing id; otherwise derive a unique slug from the text.
				if ( preg_match( '/\sid=("|\')(.*?)\1/i', $attrs, $idm ) ) {
					$id = $idm[2];
				} else {
					$base = sanitize_title( $text );
					if ( '' === $base ) {
						$base = 'section';
					}
					$id = $base;
					$n  = 2;
					while ( isset( $used[ $id ] ) ) {
						$id = $base . '-' . $n;
						++$n;
					}
					$attrs .= ' id="' . esc_attr( $id ) . '"';
				}

				$used[ $id ] = true;
				$items[]     = array(
					'level' => $level,
					'id'    => $id,
					'text'  => $text,
				);

				return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
			},
			$html
		);

		return array( $html, $items );
	}
}

if ( ! function_exists( 'estecapelli_render_toc' ) ) {
	/**
	 * Render the TOC nav. Returns '' when there are too few sections to bother.
	 *
	 * @param array $items From estecapelli_extract_toc().
	 * @param array $args  Optional: 'title', 'min' (minimum headings to show).
	 * @return string HTML.
	 */
	function estecapelli_render_toc( $items, $args = array() ) {
		$min = isset( $args['min'] ) ? (int) $args['min'] : 3;
		if ( count( $items ) < $min ) {
			return '';
		}
		$title = $args['title'] ?? __( 'On this page', 'estecapelli' );

		ob_start();
		?>
		<nav class="toc" data-toc aria-label="<?php esc_attr_e( 'Table of contents', 'estecapelli' ); ?>">
			<button type="button" class="toc__head" data-toc-toggle aria-expanded="true">
				<span class="toc__title"><?php echo esc_html( $title ); ?></span>
				<span class="toc__chevron" aria-hidden="true"><?php estecapelli_icon( 'chevron-down', array( 'width' => 18, 'height' => 18 ) ); ?></span>
			</button>
			<ol class="toc__list" data-toc-list>
				<?php foreach ( $items as $it ) : ?>
					<li class="toc__item toc__item--h<?php echo (int) $it['level']; ?>">
						<a class="toc__link" href="#<?php echo esc_attr( $it['id'] ); ?>" data-toc-link="<?php echo esc_attr( $it['id'] ); ?>"><?php echo esc_html( $it['text'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ol>
		</nav>
		<?php
		return (string) ob_get_clean();
	}
}
