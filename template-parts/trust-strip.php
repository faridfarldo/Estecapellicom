<?php
/**
 * Trust-stats strip — "Trusted Worldwide" band below the hero.
 *
 * Animated world band: a dark section with a faint dotted world-map backdrop,
 * glowing "country" nodes, and numbers that count up when scrolled into view
 * (see initCountUp() in assets/js/main.js). Numeric values carry data-count /
 * data-prefix / data-suffix so the count-up keeps the exact formatting; the
 * server-rendered text is the final value, so it still reads correctly with JS
 * off or reduced motion.
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

	<div class="trust-strip__map" aria-hidden="true">
		<span class="trust-strip__node trust-strip__node--1"></span>
		<span class="trust-strip__node trust-strip__node--2"></span>
		<span class="trust-strip__node trust-strip__node--3"></span>
		<span class="trust-strip__node trust-strip__node--4"></span>
		<span class="trust-strip__node trust-strip__node--5"></span>
	</div>

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
				<?php
				// Parse a "+15,000" / "15+" style value into prefix + number + suffix
				// so the count-up can rebuild it with the same formatting. Values
				// with no clean number run (e.g. "24/7") stay static.
				$value = (string) $stat['value'];
				$anim  = ( false === strpos( $value, '/' ) && preg_match( '/^([^0-9]*)([0-9,]+)([^0-9]*)$/', $value, $m ) );
				?>
				<li class="trust-strip__item">
					<span class="trust-strip__value"<?php if ( $anim ) : ?> data-count="<?php echo esc_attr( str_replace( ',', '', $m[2] ) ); ?>" data-prefix="<?php echo esc_attr( $m[1] ); ?>" data-suffix="<?php echo esc_attr( $m[3] ); ?>"<?php endif; ?>><?php echo esc_html( $value ); ?></span>
					<span class="trust-strip__accent" aria-hidden="true"></span>
					<span class="trust-strip__label"><?php echo esc_html( $stat['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
