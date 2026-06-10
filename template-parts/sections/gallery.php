<?php
/**
 * Section: Before & After — gallery of single composite images.
 *
 * Each item is one image the editor has already composed (before/after
 * laid out in their own design), plus an optional caption and graft count.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow = $section['eyebrow'] ?? '';
$title   = $section['title']   ?? '';
$lead    = $section['lead']    ?? '';
$items   = $section['items']   ?? array();
$cta     = $section['cta']     ?? array();

if ( empty( $items ) ) { return; }

// Drop items without an image up front so the nav reflects the real count.
$items = array_values(
	array_filter(
		$items,
		static function ( $item ) {
			return ! empty( $item['image']['url'] );
		}
	)
);
if ( empty( $items ) ) { return; }

$multi = count( $items ) > 1;
?>

<section class="t-gallery">
	<div class="shell">

		<header class="t-gallery__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-gallery__eyebrow">
					<span class="t-gallery__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>
			<h2 class="t-gallery__title"><?php echo esc_html( $title ); ?></h2>
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
					$image = $item['image'] ?? array();
					?>
					<li class="t-gallery__card">
						<figure class="t-gallery__media">
							<?php
							$img_id = $image['ID'] ?? $image['id'] ?? 0;
							$img_alt = $image['alt'] ?? __( 'Before and after result', 'estecapelli' );
							if ( $img_id ) {
								// Responsive srcset — the browser downloads the size it needs.
								echo wp_get_attachment_image(
									(int) $img_id,
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
									width="<?php echo (int) ( $image['width']  ?? 1600 ); ?>"
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

		<?php if ( ! empty( $cta['url'] ) ) : ?>
			<div class="t-gallery__cta-wrap">
				<a class="btn btn-primary" href="<?php echo esc_url( $cta['url'] ); ?>">
					<?php echo esc_html( $cta['label'] ?: __( 'See full gallery', 'estecapelli' ) ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
				</a>
			</div>
		<?php endif; ?>

	</div>
</section>
