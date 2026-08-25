<?php
/**
 * Section: Price Comparison — what a transplant costs per graft, by country.
 *
 * A graft-count slider drives every row: each country shows its typical
 * per-graft market range and the estimated total for the chosen graft count,
 * while the Estecapelli row stays a fixed all-inclusive price that does not
 * move with the slider — which is the whole point of the comparison.
 *
 * The totals are rendered server-side for the starting graft count, so the
 * section is complete and readable with JavaScript disabled; assets/js/main.js
 * only recalculates them as the slider moves.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow  = $section['eyebrow']  ?? '';
$title    = $section['title']    ?? '';
$lead     = $section['lead']     ?? '';
$rows     = $section['rows']     ?? array();
$currency = $section['currency'] ?? '$';
$footnote = $section['footnote'] ?? '';
$includes = ! empty( $section['includes'] ) && is_array( $section['includes'] ) ? $section['includes'] : array();
$hl       = ! empty( $section['highlight'] ) && is_array( $section['highlight'] ) ? $section['highlight'] : array();

if ( ! $title || empty( $rows ) || ! is_array( $rows ) ) { return; }

$grafts_min  = max( 1, (int) ( $section['grafts_min'] ?? 1000 ) );
$grafts_max  = max( $grafts_min + 1, (int) ( $section['grafts_max'] ?? 6000 ) );
$grafts_step = max( 1, (int) ( $section['grafts_step'] ?? 250 ) );
$grafts      = (int) ( $section['grafts_default'] ?? 3000 );
$grafts      = min( $grafts_max, max( $grafts_min, $grafts ) );

$slider_label = $section['slider_label'] ?? __( 'Number of grafts', 'estecapelli' );

/** Format a money amount: no decimals for totals, up to two for per-graft rates. */
$money = function ( $value, $decimals = 0 ) use ( $currency ) {
	return $currency . number_format_i18n( (float) $value, (int) $decimals );
};

/** Show a per-graft rate in its shortest sensible form: 6, 6.5, 6.25. */
$rate = function ( $value ) use ( $money ) {
	$value = (float) $value;
	if ( $value === floor( $value ) ) {
		return $money( $value, 0 );
	}
	return $money( $value, round( $value, 1 ) === round( $value, 2 ) ? 1 : 2 );
};

$cta       = $section['cta'] ?? array();
$cta_url   = $cta['url'] ?? '';
$cta_label = $cta['label'] ?? '';
if ( $cta_url && function_exists( 'estecapelli_localize_theme_url' ) ) {
	$cta_url = estecapelli_localize_theme_url( $cta_url );
}

$uid = 'price-grafts-' . wp_rand( 1000, 9999 );
?>

<section class="t-cmp" aria-labelledby="t-cmp-title">
	<div class="shell">

		<header class="t-cmp__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-cmp__eyebrow">
					<span class="t-cmp__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>

			<h2 id="t-cmp-title" class="t-cmp__title"><?php echo esc_html( $title ); ?></h2>

			<?php if ( $lead ) : ?>
				<p class="t-cmp__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<div
			class="t-cmp__panel"
			data-price-compare
			data-currency="<?php echo esc_attr( $currency ); ?>"
			data-grafts="<?php echo (int) $grafts; ?>"
		>

			<div class="t-cmp__control">
				<label class="t-cmp__control-label" for="<?php echo esc_attr( $uid ); ?>">
					<?php echo esc_html( $slider_label ); ?>
				</label>

				<output class="t-cmp__control-value" for="<?php echo esc_attr( $uid ); ?>" data-price-count>
					<?php echo esc_html( number_format_i18n( $grafts ) ); ?>
				</output>

				<input
					class="t-cmp__range"
					id="<?php echo esc_attr( $uid ); ?>"
					type="range"
					min="<?php echo (int) $grafts_min; ?>"
					max="<?php echo (int) $grafts_max; ?>"
					step="<?php echo (int) $grafts_step; ?>"
					value="<?php echo (int) $grafts; ?>"
					data-price-slider
				/>

				<div class="t-cmp__range-scale" aria-hidden="true">
					<span><?php echo esc_html( number_format_i18n( $grafts_min ) ); ?></span>
					<span><?php echo esc_html( number_format_i18n( $grafts_max ) ); ?></span>
				</div>
			</div>

			<ul class="t-cmp__list">

				<li class="t-cmp__row t-cmp__row--head" aria-hidden="true">
					<span class="t-cmp__cell t-cmp__cell--country"><?php esc_html_e( 'Country', 'estecapelli' ); ?></span>
					<span class="t-cmp__cell t-cmp__cell--rate"><?php esc_html_e( 'Per graft', 'estecapelli' ); ?></span>
					<span class="t-cmp__cell t-cmp__cell--total"><?php esc_html_e( 'Estimated total', 'estecapelli' ); ?></span>
				</li>

				<?php foreach ( $rows as $row ) :
					if ( empty( $row['country'] ) ) { continue; }
					$min = (float) ( $row['per_graft_min'] ?? 0 );
					$max = (float) ( $row['per_graft_max'] ?? 0 );
					if ( $min <= 0 && $max <= 0 ) { continue; }
					?>
					<li
						class="t-cmp__row"
						data-price-row
						data-per-graft-min="<?php echo esc_attr( $min ); ?>"
						data-per-graft-max="<?php echo esc_attr( $max ); ?>"
					>
						<span class="t-cmp__cell t-cmp__cell--country">
							<?php estecapelli_flag( $row['flag'] ?? '', array( 'class' => 't-cmp__flag' ) ); ?>
							<span class="t-cmp__country"><?php echo esc_html( $row['country'] ); ?></span>
						</span>

						<span class="t-cmp__cell t-cmp__cell--rate">
							<span class="t-cmp__cell-label"><?php esc_html_e( 'Per graft', 'estecapelli' ); ?></span>
							<?php echo esc_html( $rate( $min ) . ' – ' . $rate( $max ) ); ?>
						</span>

						<span class="t-cmp__cell t-cmp__cell--total">
							<span class="t-cmp__cell-label"><?php esc_html_e( 'Estimated total', 'estecapelli' ); ?></span>
							<span data-price-total><?php echo esc_html( $money( $min * $grafts ) . ' – ' . $money( $max * $grafts ) ); ?></span>
						</span>
					</li>
				<?php endforeach; ?>

				<?php if ( ! empty( $hl['country'] ) ) : ?>
					<li class="t-cmp__row t-cmp__row--us">
						<span class="t-cmp__cell t-cmp__cell--country">
							<?php estecapelli_flag( $hl['flag'] ?? '', array( 'class' => 't-cmp__flag' ) ); ?>
							<span class="t-cmp__country">
								<?php echo esc_html( $hl['country'] ); ?>
								<?php if ( ! empty( $hl['clinic'] ) ) : ?>
									<span class="t-cmp__clinic"><?php echo esc_html( $hl['clinic'] ); ?></span>
								<?php endif; ?>
							</span>
						</span>

						<span class="t-cmp__cell t-cmp__cell--rate">
							<?php if ( ! empty( $hl['badge'] ) ) : ?>
								<span class="t-cmp__badge"><?php echo esc_html( $hl['badge'] ); ?></span>
							<?php endif; ?>
						</span>

						<span class="t-cmp__cell t-cmp__cell--total">
							<span class="t-cmp__cell-label"><?php esc_html_e( 'All-inclusive', 'estecapelli' ); ?></span>
							<span class="t-cmp__fixed">
								<?php
								$hl_min = (float) ( $hl['price_min'] ?? 0 );
								$hl_max = (float) ( $hl['price_max'] ?? 0 );
								echo esc_html( $hl_max > $hl_min ? $money( $hl_min ) . ' – ' . $money( $hl_max ) : $money( $hl_min ) );
								?>
							</span>
							<?php if ( ! empty( $hl['note'] ) ) : ?>
								<span class="t-cmp__fixed-note"><?php echo esc_html( $hl['note'] ); ?></span>
							<?php endif; ?>
						</span>
					</li>
				<?php endif; ?>

			</ul>

			<?php if ( $includes ) : ?>
				<ul class="t-cmp__includes">
					<?php foreach ( $includes as $inc ) :
						if ( empty( $inc['label'] ) ) { continue; }
						?>
						<li class="t-cmp__include">
							<span class="t-cmp__include-icon" aria-hidden="true">
								<?php
								estecapelli_render_item_icon(
									array(
										'icon'      => $inc['icon'] ?? 'check-circle',
										'icon_file' => $inc['icon_file'] ?? null,
									),
									array( 'width' => 18, 'height' => 18 )
								);
								?>
							</span>
							<?php echo esc_html( $inc['label'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $cta_url && $cta_label ) : ?>
				<div class="t-cmp__actions">
					<a class="btn btn-accent" href="<?php echo esc_url( $cta_url ); ?>" data-lead-popup>
						<?php echo esc_html( $cta_label ); ?>
					</a>
				</div>
			<?php endif; ?>

			<?php if ( $footnote ) : ?>
				<p class="t-cmp__footnote"><?php echo esc_html( $footnote ); ?></p>
			<?php endif; ?>

		</div>

	</div>
</section>
