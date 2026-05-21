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

		<ul class="t-gallery__grid">
			<?php foreach ( $items as $item ) :
				$image = $item['image'] ?? array();
				if ( empty( $image['url'] ) ) { continue; }
				?>
				<li class="t-gallery__card">
					<figure class="t-gallery__media">
						<img
							src="<?php echo esc_url( $image['url'] ); ?>"
							alt="<?php echo esc_attr( $image['alt'] ?? __( 'Before and after result', 'estecapelli' ) ); ?>"
							loading="lazy"
							decoding="async"
							width="<?php echo (int) ( $image['width']  ?? 1600 ); ?>"
							height="<?php echo (int) ( $image['height'] ?? 1000 ); ?>"
						/>
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
