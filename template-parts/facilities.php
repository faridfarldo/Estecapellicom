<?php
/**
 * Homepage: "Our Clinic" — a photo/video gallery of the clinic, followed by a
 * rotating strip of partner-hotel logos.
 *
 * Video tiles reuse the shared patient-stories lightbox via [data-stories-play].
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data    = estecapelli_facilities();
$gallery = $data['gallery'] ?? array();
if ( empty( $gallery ) ) {
	return;
}
$partners = $data['partners'] ?? array();
?>

<section class="facilities" aria-labelledby="facilities-title">

	<div class="facilities__bg" aria-hidden="true">
		<span class="facilities__bg-mark facilities__bg-mark--a"></span>
		<span class="facilities__bg-mark facilities__bg-mark--b"></span>
	</div>

	<div class="shell">

		<header class="facilities__head">
			<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
				<span class="facilities__eyebrow">
					<span class="facilities__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $data['eyebrow'] ); ?>
				</span>
			<?php endif; ?>

			<h2 id="facilities-title" class="facilities__title"><?php echo esc_html( $data['headline'] ); ?></h2>

			<?php if ( ! empty( $data['lead'] ) ) : ?>
				<p class="facilities__lead"><?php echo esc_html( $data['lead'] ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="facilities__gallery">
			<?php foreach ( $gallery as $i => $item ) :
				$is_big   = ( 0 === $i );
				$is_video = ( 'video' === ( $item['type'] ?? 'image' ) && ! empty( $item['video'] ) );
				$cls      = 'facilities__tile' . ( $is_big ? ' facilities__tile--big' : '' );
				?>
				<li class="<?php echo esc_attr( $cls ); ?>">
					<?php if ( $is_video ) : ?>
						<button type="button" class="facilities__media facilities__media--video" data-stories-play="<?php echo esc_attr( $item['video'] ); ?>" data-story-title="<?php echo esc_attr( $item['caption'] ?? '' ); ?>">
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" decoding="async" />
							<span class="facilities__play" aria-hidden="true">
								<span class="facilities__play-ring"></span>
								<?php estecapelli_icon( 'play', array( 'width' => 22, 'height' => 22 ) ); ?>
							</span>
							<?php if ( ! empty( $item['caption'] ) ) : ?>
								<span class="facilities__caption"><?php echo esc_html( $item['caption'] ); ?></span>
							<?php endif; ?>
						</button>
					<?php else :
						// A tile can carry a whole gallery of photos (one room). The first
						// is the cover; clicking opens the gallery lightbox with arrows.
						$imgs = ! empty( $item['images'] ) && is_array( $item['images'] ) ? array_values( array_filter( $item['images'] ) ) : array();
						if ( empty( $imgs ) && ! empty( $item['image'] ) ) { $imgs = array( $item['image'] ); }
						$cover = $imgs[0] ?? '';
						?>
						<button type="button" class="facilities__media facilities__media--photo" data-img-gallery="<?php echo esc_attr( wp_json_encode( $imgs ) ); ?>" data-caption="<?php echo esc_attr( $item['caption'] ?? '' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Open gallery: %s', 'estecapelli' ), $item['caption'] ?? '' ) ); ?>">
							<?php
							// alt="" on purpose: the caption below the photo already says
							// "Lobby" / "Dental Clinic", and the button announces "Open
							// gallery: <caption>". A matching alt made a screen reader read
							// the same words three times over.
							?>
							<img src="<?php echo esc_url( $cover ); ?>" alt="" loading="lazy" decoding="async" />
							<span class="facilities__zoom" aria-hidden="true">
								<?php estecapelli_icon( 'image', array( 'width' => 18, 'height' => 18 ) ); ?>
							</span>
							<?php if ( ! empty( $item['caption'] ) ) : ?>
								<span class="facilities__caption"><?php echo esc_html( $item['caption'] ); ?></span>
							<?php endif; ?>
						</button>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>

	<?php if ( ! empty( $partners['logos'] ) ) : ?>
		<div class="facilities__partners">
			<?php if ( ! empty( $partners['title'] ) ) : ?>
				<span class="facilities__partners-title"><?php echo esc_html( $partners['title'] ); ?></span>
			<?php endif; ?>

			<div class="facilities__marquee" data-facilities-marquee>
				<ul class="facilities__logos">
					<?php for ( $dup = 0; $dup < 2; $dup++ ) : ?>
						<?php foreach ( $partners['logos'] as $logo ) : ?>
							<li class="facilities__logo" <?php echo 1 === $dup ? 'aria-hidden="true"' : ''; ?>>
								<?php if ( ! empty( $logo['image'] ) ) : ?>
									<?php
									// Lazy on purpose. These sit far below the fold but used to
									// load eagerly — 1.8 MB on the critical path of every page —
									// because the marquee waits for every image to decode() before
									// it starts, and a lazy image never decodes until it is in
									// view. main.js now flips them to eager as the section
									// approaches the viewport, then runs that same decode gate.
									?>
									<img class="facilities__logo-photo" src="<?php echo esc_url( $logo['image'] ); ?>" alt="<?php echo esc_attr( $logo['label'] ?? '' ); ?>" loading="lazy" decoding="async" />
								<?php endif; ?>
								<span class="facilities__logo-name">
									<?php echo esc_html( $logo['label'] ?? '' ); ?>
									<?php if ( ! empty( $logo['stars'] ) ) : ?>
										<small class="facilities__logo-stars" aria-label="<?php
											/* translators: %d: number of stars. */
											echo esc_attr( sprintf( _n( '%d star', '%d stars', (int) $logo['stars'], 'estecapelli' ), (int) $logo['stars'] ) );
										?>"><?php echo str_repeat( '★', (int) $logo['stars'] ); ?></small>
									<?php endif; ?>
								</span>
							</li>
						<?php endforeach; ?>
					<?php endfor; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

</section>

<?php
// The lightbox lives OUTSIDE the section on purpose. The section carries
// content-visibility: auto so the browser can skip laying it out while it is
// off screen — but that also makes the section a containing block for
// position: fixed descendants, which would trap this full-screen overlay
// inside the section's box. main.js looks it up with document.querySelector,
// so being a sibling changes nothing else.
?>
<!-- Image lightbox — opened by any [data-facilities-zoom] photo tile. -->
<div class="facilities__lightbox" data-facilities-lightbox hidden role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Photo viewer', 'estecapelli' ); ?>">
	<button type="button" class="facilities__lightbox-backdrop" data-facilities-lightbox-close aria-label="<?php esc_attr_e( 'Close', 'estecapelli' ); ?>"></button>
	<figure class="facilities__lightbox-shell">
		<button type="button" class="facilities__lightbox-close" data-facilities-lightbox-close aria-label="<?php esc_attr_e( 'Close', 'estecapelli' ); ?>">
			<?php estecapelli_icon( 'close', array( 'width' => 22, 'height' => 22 ) ); ?>
		</button>
		<img class="facilities__lightbox-img" data-facilities-lightbox-img src="" alt="" />
		<figcaption class="facilities__lightbox-caption" data-facilities-lightbox-caption></figcaption>
	</figure>
</div>
