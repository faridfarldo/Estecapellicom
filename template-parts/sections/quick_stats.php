<?php
/**
 * Section: Quick Stats — small strip of icon + value + label cells.
 *
 * Renders up to 6 stat cells in an equal grid.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) {
	return;
}

$eyebrow = $section['eyebrow'] ?? '';
$stats   = $section['stats']   ?? array();

if ( empty( $stats ) ) {
	return;
}
?>

<section class="t-stats" aria-label="<?php esc_attr_e( 'Procedure quick facts', 'estecapelli' ); ?>">
	<div class="shell">

		<?php if ( $eyebrow ) : ?>
			<div class="t-stats__eyebrow">
				<span class="t-stats__eyebrow-mark" aria-hidden="true"></span>
				<?php echo esc_html( $eyebrow ); ?>
			</div>
		<?php endif; ?>

		<ul class="t-stats__list" style="--cols: <?php echo (int) min( 6, count( $stats ) ); ?>">
			<?php foreach ( $stats as $stat ) :
				$icon  = $stat['icon']  ?? '';
				$value = $stat['value'] ?? '';
				$label = $stat['label'] ?? '';
				if ( ! $value ) { continue; }
				?>
				<li class="t-stats__cell">
					<?php if ( $icon || ! empty( $stat['icon_file'] ) ) : ?>
						<span class="t-stats__icon" aria-hidden="true">
							<?php estecapelli_render_item_icon( $stat, array( 'width' => 22, 'height' => 22 ) ); ?>
						</span>
					<?php endif; ?>
					<span class="t-stats__value"><?php echo esc_html( $value ); ?></span>
					<span class="t-stats__label"><?php echo esc_html( $label ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
