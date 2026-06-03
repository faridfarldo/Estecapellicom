<?php
/**
 * Before & After — single case card (before | after, side by side).
 *
 * Shared by the service-page carousel and the Before & After page.
 * Expects `ba_card` query var: array from estecapelli_result_card_data().
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$card = get_query_var( 'ba_card' );
if ( ! is_array( $card ) ) { return; }

$before = $card['before'];
$after  = $card['after'];
$method = $card['method'] ?? '';
$grafts = (int) ( $card['grafts'] ?? 0 );
?>
<article class="ba-card">
	<div class="ba-card__pair">
		<figure class="ba-card__half">
			<span class="ba-card__tag ba-card__tag--before"><?php esc_html_e( 'Before', 'estecapelli' ); ?></span>
			<img
				class="ba-card__img"
				src="<?php echo esc_url( $before['url'] ); ?>"
				alt="<?php echo esc_attr( $before['alt'] ?: __( 'Before treatment', 'estecapelli' ) ); ?>"
				loading="lazy"
				decoding="async"
				width="<?php echo (int) ( $before['width'] ?? 800 ); ?>"
				height="<?php echo (int) ( $before['height'] ?? 800 ); ?>"
			/>
		</figure>
		<figure class="ba-card__half">
			<span class="ba-card__tag ba-card__tag--after"><?php esc_html_e( 'After', 'estecapelli' ); ?></span>
			<img
				class="ba-card__img"
				src="<?php echo esc_url( $after['url'] ); ?>"
				alt="<?php echo esc_attr( $after['alt'] ?: __( 'After treatment', 'estecapelli' ) ); ?>"
				loading="lazy"
				decoding="async"
				width="<?php echo (int) ( $after['width'] ?? 800 ); ?>"
				height="<?php echo (int) ( $after['height'] ?? 800 ); ?>"
			/>
		</figure>
	</div>

	<?php if ( $method || $grafts ) : ?>
		<div class="ba-card__meta">
			<?php if ( $method ) : ?>
				<span class="ba-card__method"><?php echo esc_html( $method ); ?></span>
			<?php endif; ?>
			<?php if ( $grafts ) : ?>
				<span class="ba-card__grafts">
					<?php
					/* translators: %s: number of grafts, already formatted with thousands separator. */
					printf( esc_html__( '%s grafts', 'estecapelli' ), esc_html( number_format_i18n( $grafts ) ) );
					?>
				</span>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</article>
