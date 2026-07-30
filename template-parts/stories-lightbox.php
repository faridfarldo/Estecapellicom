<?php
/**
 * Shared YouTube lightbox for every [data-stories-play] trigger on the page.
 *
 * It used to be printed by template-parts/patient-stories.php, which returns
 * early when a language has no stories to show. Turkish is exactly that case
 * (testimonials are withheld for legal reasons), so the clinic-tour video in
 * the facilities section had a play button and no player to open — main.js
 * binds no click handlers at all when the lightbox is missing.
 *
 * Every section that renders a play trigger loads this part; the guard below
 * keeps a page with several of them down to one player.
 *
 * Deliberately a sibling of the sections, not a child: they carry
 * content-visibility: auto, which would otherwise make one of them the
 * containing block for this position: fixed overlay and trap the video player
 * inside that section's box. main.js finds it via document.querySelector.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// A global rather than a static: load_template() includes this file inside its
// own function scope, which every other template part shares.
if ( ! empty( $GLOBALS['estecapelli_stories_lightbox_printed'] ) ) {
	return;
}
$GLOBALS['estecapelli_stories_lightbox_printed'] = true;
?>
<div class="stories__lightbox" data-stories-lightbox hidden role="dialog" aria-modal="true" aria-labelledby="stories-lightbox-title">
	<button type="button" class="stories__lightbox-backdrop" data-stories-lightbox-close aria-label="<?php esc_attr_e( 'Close video', 'estecapelli' ); ?>"></button>
	<div class="stories__lightbox-shell">
		<header class="stories__lightbox-head">
			<h4 id="stories-lightbox-title" class="stories__lightbox-title" data-stories-lightbox-title></h4>
			<button type="button" class="stories__lightbox-close" data-stories-lightbox-close aria-label="<?php esc_attr_e( 'Close', 'estecapelli' ); ?>">
				<?php estecapelli_icon( 'close', array( 'width' => 20, 'height' => 20 ) ); ?>
			</button>
		</header>
		<div class="stories__lightbox-frame" data-stories-lightbox-frame></div>
	</div>
</div>
