<?php
/**
 * Section: Pricing — single card with plan, price, features, CTA.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow  = $section['eyebrow']   ?? '';
$title    = $section['title']     ?? '';
$lead     = $section['lead']      ?? '';
$plan     = $section['plan_name'] ?? '';
$currency = $section['currency']  ?? '€';
$amount   = $section['amount']    ?? '';
$period   = $section['period']    ?? '';
$features = $section['features']  ?? array();
$note     = $section['note']      ?? '';
$cta      = $section['cta']       ?? array();

if ( ! $amount || ! $plan ) { return; }
?>

<section class="t-price">
	<div class="shell">

		<?php if ( $title || $lead || $eyebrow ) : ?>
			<header class="t-price__head">
				<?php if ( $eyebrow ) : ?>
					<span class="t-price__eyebrow">
						<span class="t-price__eyebrow-mark" aria-hidden="true"></span>
						<?php echo esc_html( $eyebrow ); ?>
					</span>
				<?php endif; ?>
				<?php if ( $title ) : ?>
					<h2 class="t-price__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $lead ) : ?>
					<p class="t-price__lead"><?php echo esc_html( $lead ); ?></p>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<div class="t-price__card">
			<div class="t-price__card-aura" aria-hidden="true"></div>

			<header class="t-price__card-head">
				<span class="t-price__plan"><?php echo esc_html( $plan ); ?></span>
				<div class="t-price__amount">
					<span class="t-price__currency"><?php echo esc_html( $currency ); ?></span>
					<span class="t-price__value"><?php echo esc_html( $amount ); ?></span>
				</div>
				<?php if ( $period ) : ?>
					<span class="t-price__period"><?php echo esc_html( $period ); ?></span>
				<?php endif; ?>
			</header>

			<?php if ( ! empty( $features ) ) : ?>
				<ul class="t-price__features">
					<?php foreach ( $features as $f ) : ?>
						<li class="t-price__feature">
							<span class="t-price__feature-icon" aria-hidden="true">
								<?php estecapelli_icon( 'check-circle', array( 'width' => 16, 'height' => 16 ) ); ?>
							</span>
							<?php echo esc_html( $f['label'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $cta['url'] ) ) : ?>
				<a class="btn btn-accent btn-lg t-price__cta" href="<?php echo esc_url( $cta['url'] ); ?>">
					<?php echo esc_html( $cta['label'] ?: __( 'Get a Quote', 'estecapelli' ) ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $note ) : ?>
				<p class="t-price__note"><?php echo esc_html( $note ); ?></p>
			<?php endif; ?>
		</div>

	</div>
</section>
