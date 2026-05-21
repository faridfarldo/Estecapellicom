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

		<ol class="t-steps__list">
			<?php foreach ( $items as $i => $step ) :
				$icon = $step['icon'] ?? '';
				?>
				<li class="t-steps__item" style="--num: <?php echo (int) ( $i + 1 ); ?>">
					<span class="t-steps__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<div class="t-steps__card">
						<div class="t-steps__card-head">
							<?php if ( $icon ) : ?>
								<span class="t-steps__icon" aria-hidden="true">
									<?php estecapelli_icon( $icon, array( 'width' => 22, 'height' => 22 ) ); ?>
								</span>
							<?php endif; ?>
							<?php if ( ! empty( $step['time'] ) ) : ?>
								<span class="t-steps__time"><?php echo esc_html( $step['time'] ); ?></span>
							<?php endif; ?>
						</div>
						<h3 class="t-steps__item-title"><?php echo esc_html( $step['title'] ); ?></h3>
						<?php if ( ! empty( $step['body'] ) ) : ?>
							<p class="t-steps__item-body"><?php echo esc_html( $step['body'] ); ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>

	</div>
</section>
