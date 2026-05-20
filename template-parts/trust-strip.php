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
	<div class="shell trust-strip__shell">
		<ul class="trust-strip__list">
			<?php foreach ( $stats as $stat ) : ?>
				<li class="trust-strip__item">
					<span class="trust-strip__value"><?php echo esc_html( $stat['value'] ); ?></span>
					<span class="trust-strip__label"><?php echo esc_html( $stat['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
