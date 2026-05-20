<?php
/**
 * Minimal trust-stats strip — sits below the hero.
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

<section class="trust-strip" aria-label="<?php esc_attr_e( 'Patients trust us by the numbers', 'estecapelli' ); ?>">
	<span class="trust-strip__rule trust-strip__rule--top" aria-hidden="true"></span>

	<div class="shell trust-strip__shell">

		<div class="trust-strip__eyebrow" aria-hidden="true">
			<span class="trust-strip__eyebrow-line"></span>
			<span class="trust-strip__eyebrow-mark"></span>
			<span class="trust-strip__eyebrow-text"><?php esc_html_e( 'Trusted Worldwide', 'estecapelli' ); ?></span>
			<span class="trust-strip__eyebrow-mark"></span>
			<span class="trust-strip__eyebrow-line"></span>
		</div>

		<ul class="trust-strip__list">
			<?php foreach ( $stats as $stat ) : ?>
				<li class="trust-strip__item">
					<span class="trust-strip__value"><?php echo esc_html( $stat['value'] ); ?></span>
					<span class="trust-strip__accent" aria-hidden="true"></span>
					<span class="trust-strip__label"><?php echo esc_html( $stat['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>

	<span class="trust-strip__rule trust-strip__rule--bot" aria-hidden="true"></span>
</section>
