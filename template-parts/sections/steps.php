<?php
/**
 * Section: Steps Timeline — numbered procedure flow.
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

if ( empty( $items ) ) { return; }
?>

<section class="t-steps">
	<div class="shell">

		<header class="t-steps__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-steps__eyebrow">
					<span class="t-steps__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>
			<h2 class="t-steps__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $lead ) : ?>
				<p class="t-steps__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<div class="t-steps__carousel ec-carousel" data-carousel>
			<button type="button" class="ec-carousel__nav ec-carousel__nav--prev" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous step', 'estecapelli' ); ?>">
				<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
			</button>

			<ol class="t-steps__track ec-carousel__track" data-carousel-track>
				<?php foreach ( $items as $i => $step ) :
					$icon = $step['icon'] ?? '';
					?>
					<li class="t-steps__item">
						<div class="t-steps__top">
							<?php if ( $icon || ! empty( $step['icon_file'] ) ) : ?>
								<span class="t-steps__icon" aria-hidden="true">
									<?php estecapelli_render_item_icon( $step, array( 'width' => 22, 'height' => 22 ) ); ?>
								</span>
							<?php else : ?>
								<span></span>
							<?php endif; ?>
							<span class="t-steps__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						</div>
						<?php if ( ! empty( $step['time'] ) ) : ?>
							<span class="t-steps__time"><?php echo esc_html( $step['time'] ); ?></span>
						<?php endif; ?>
						<h3 class="t-steps__item-title"><?php echo esc_html( $step['title'] ); ?></h3>
						<?php if ( ! empty( $step['body'] ) ) : ?>
							<p class="t-steps__item-body"><?php echo esc_html( $step['body'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>

			<button type="button" class="ec-carousel__nav ec-carousel__nav--next" data-carousel-next aria-label="<?php esc_attr_e( 'Next step', 'estecapelli' ); ?>">
				<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
			</button>
		</div>

	</div>
</section>
