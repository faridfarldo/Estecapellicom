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
					// Icon intentionally not rendered — this module is icon-free.
					$label = ! empty( $step['time'] ) ? $step['time'] : ( $step['eyebrow'] ?? '' );
					?>
					<li class="t-steps__item">
						<div class="t-steps__top">
							<span class="t-steps__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						</div>
						<?php if ( $label ) : ?>
							<span class="t-steps__time"><?php echo esc_html( $label ); ?></span>
						<?php endif; ?>
						<h3 class="t-steps__item-title"><?php echo esc_html( $step['title'] ); ?></h3>
						<?php if ( ! empty( $step['body'] ) ) : ?>
							<div class="t-steps__item-body"><?php echo wp_kses_post( $step['body'] ); ?></div>
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
