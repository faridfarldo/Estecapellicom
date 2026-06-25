<?php
/**
 * Trust-stats section — an auto-rotating "in numbers" slot reel.
 *
 * The stats stack vertically inside a fixed window and roll continuously like a
 * casino reel: the figure in the centre is sharp and enlarged, the ones above
 * and below are blurred and dimmed (the focus depth comes from the top/bottom
 * blur overlays). It loops seamlessly and runs on its own — see initTrustReel()
 * in assets/js/main.js.
 *
 * Progressive enhancement: with JS off or reduced motion the same markup renders
 * as a plain, fully readable list of every stat (the reel window is only applied
 * by JS via the .is-reel class).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = estecapelli_trust_stats();
if ( empty( $stats ) ) {
	return;
}
?>

<section class="trust-reel" aria-label="<?php esc_attr_e( 'Patients trust us by the numbers', 'estecapelli' ); ?>">

	<div class="trust-reel__blobs" aria-hidden="true">
		<span class="trust-reel__blob trust-reel__blob--1"></span>
		<span class="trust-reel__blob trust-reel__blob--2"></span>
		<span class="trust-reel__blob trust-reel__blob--3"></span>
	</div>

	<div class="shell trust-reel__shell">

		<p class="trust-reel__heading"><?php esc_html_e( 'Estecapelli in numbers', 'estecapelli' ); ?></p>

		<div class="trust-reel__viewport" data-reel>
			<ul class="trust-reel__track" data-reel-track>
				<?php foreach ( $stats as $stat ) : ?>
					<li class="trust-reel__item">
						<span class="trust-reel__num"><?php echo esc_html( $stat['value'] ); ?></span>
						<span class="trust-reel__label"><?php echo esc_html( $stat['label'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

			<span class="trust-reel__fade trust-reel__fade--top" aria-hidden="true"></span>
			<span class="trust-reel__fade trust-reel__fade--bottom" aria-hidden="true"></span>
		</div>

	</div>
</section>
