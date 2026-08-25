<?php
/**
 * Section: Before & After — pulled automatically from the treatment galleries.
 *
 * The hand-filled `gallery` layout needs an editor to upload composites into
 * that specific page, so it renders nothing until someone does. This one reads
 * the before/after composites already attached to the treatments in a category
 * (the same source as the Before & After page) and mixes them, so a category
 * landing page always shows real results without a second upload.
 *
 * Markup and CSS are shared with the `gallery` layout — same carousel, same
 * lightbox — so the two look identical wherever both appear.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

if ( ! function_exists( 'estecapelli_gallery_grouped' ) ) { return; }

$eyebrow  = $section['eyebrow']  ?? '';
$title    = $section['title']    ?? '';
$lead     = $section['lead']     ?? '';
$category = $section['category'] ?? 'hair-transplant';
$count    = max( 1, (int) ( $section['count'] ?? 8 ) );
$cta      = $section['cta']      ?? array();

// Locate the requested category. Buckets keep the English slug in every
// language, so this match is language-independent.
$bucket = null;
foreach ( estecapelli_gallery_grouped() as $group ) {
	if ( isset( $group['term']->slug ) && $group['term']->slug === $category ) {
		$bucket = $group;
		break;
	}
}
if ( ! $bucket || empty( $bucket['services'] ) ) { return; }

// Take one result from each treatment in turn, so the strip opens with a spread
// of techniques instead of every composite from whichever one happens to sort
// first. Captions fall back to the treatment name.
$lists = array();
foreach ( $bucket['services'] as $service_bucket ) {
	if ( empty( $service_bucket['items'] ) || ! is_array( $service_bucket['items'] ) ) {
		continue;
	}
	$lists[] = array(
		'label' => ! empty( $service_bucket['service'] ) ? get_the_title( $service_bucket['service'] ) : '',
		'items' => array_values( $service_bucket['items'] ),
	);
}
if ( empty( $lists ) ) { return; }

$items = array();
$seen  = array();
$round = 0;
$more  = true;
while ( $more && count( $items ) < $count ) {
	$more = false;
	foreach ( $lists as $list ) {
		if ( ! isset( $list['items'][ $round ] ) ) {
			continue;
		}
		$more = true;

		$item  = $list['items'][ $round ];
		$image = $item['image'] ?? array();
		$url   = $image['url'] ?? '';
		if ( '' === $url ) {
			continue;
		}

		// One composite can hang off more than one treatment; show it once.
		$key = (string) ( $image['ID'] ?? $image['id'] ?? $url );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}
		$seen[ $key ] = true;

		if ( empty( $item['caption'] ) && $list['label'] ) {
			$item['caption'] = $list['label'];
		}
		$items[] = $item;

		if ( count( $items ) >= $count ) {
			break;
		}
	}
	$round++;
}

if ( empty( $items ) ) { return; }

$multi = count( $items ) > 1;

$cta_url = $cta['url'] ?? '';
if ( $cta_url && function_exists( 'estecapelli_localize_theme_url' ) ) {
	$cta_url = estecapelli_localize_theme_url( $cta_url );
}
?>

<section class="t-gallery t-gallery--auto">
	<div class="shell">

		<header class="t-gallery__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-gallery__eyebrow">
					<span class="t-gallery__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<h2 class="t-gallery__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $lead ) : ?>
				<p class="t-gallery__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<div class="t-gallery__carousel ec-carousel" data-carousel>
			<?php if ( $multi ) : ?>
				<button type="button" class="ec-carousel__nav ec-carousel__nav--prev" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous result', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
				</button>
			<?php endif; ?>

			<ul class="t-gallery__track ec-carousel__track" data-carousel-track>
				<?php foreach ( $items as $item ) :
					$image    = $item['image'] ?? array();
					$img_id   = (int) ( $image['ID'] ?? $image['id'] ?? 0 );
					$img_alt  = $image['alt'] ?? __( 'Before and after result', 'estecapelli' );
					$zoom_url = $img_id ? wp_get_attachment_image_url( $img_id, 'full' ) : ( $image['url'] ?? '' );
					?>
					<li class="t-gallery__card">
						<figure class="t-gallery__media" data-img-zoom="<?php echo esc_url( $zoom_url ); ?>" data-caption="<?php echo esc_attr( $item['caption'] ?? '' ); ?>" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Enlarge result', 'estecapelli' ); ?>">
							<?php
							if ( $img_id ) {
								echo wp_get_attachment_image(
									$img_id,
									'large',
									false,
									array( 'alt' => $img_alt, 'loading' => 'lazy', 'decoding' => 'async' )
								);
							} else {
								?>
								<img
									src="<?php echo esc_url( $image['url'] ); ?>"
									alt="<?php echo esc_attr( $img_alt ); ?>"
									loading="lazy"
									decoding="async"
									width="<?php echo (int) ( $image['width'] ?? 1600 ); ?>"
									height="<?php echo (int) ( $image['height'] ?? 1000 ); ?>"
								/>
								<?php
							}
							?>
						</figure>

						<?php if ( ! empty( $item['caption'] ) || ! empty( $item['grafts'] ) ) : ?>
							<div class="t-gallery__meta">
								<?php if ( ! empty( $item['caption'] ) ) : ?>
									<span class="t-gallery__caption"><?php echo esc_html( $item['caption'] ); ?></span>
								<?php endif; ?>
								<?php if ( ! empty( $item['grafts'] ) ) : ?>
									<span class="t-gallery__grafts"><?php echo esc_html( $item['grafts'] ); ?>&nbsp;<?php esc_html_e( 'grafts', 'estecapelli' ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( $multi ) : ?>
				<button type="button" class="ec-carousel__nav ec-carousel__nav--next" data-carousel-next aria-label="<?php esc_attr_e( 'Next result', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
				</button>
			<?php endif; ?>
		</div>

		<?php if ( $cta_url ) : ?>
			<div class="t-gallery__cta-wrap">
				<a class="btn btn-primary" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo esc_html( ! empty( $cta['label'] ) ? $cta['label'] : __( 'See full gallery', 'estecapelli' ) ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
				</a>
			</div>
		<?php endif; ?>

	</div>
</section>
