<?php
/**
 * Before & After — service-page carousel.
 *
 * Auto-appended to the bottom of every treatment page that has results.
 * Expects query vars: `ba_results` (WP_Post[]) and `ba_title` (string).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$results = get_query_var( 'ba_results' );
if ( ! is_array( $results ) || empty( $results ) ) { return; }

$service_title = get_query_var( 'ba_title' );

// Build card data first so an all-empty set renders nothing.
$cards = array();
foreach ( $results as $result ) {
	$data = estecapelli_result_card_data( $result );
	if ( $data ) {
		$cards[] = $data;
	}
}
if ( empty( $cards ) ) { return; }

$multi = count( $cards ) > 1;
?>

<section class="t-ba" aria-label="<?php esc_attr_e( 'Before and after results', 'estecapelli' ); ?>">
	<div class="shell">

		<header class="t-ba__head">
			<span class="t-ba__eyebrow">
				<span class="t-ba__eyebrow-mark" aria-hidden="true"></span>
				<?php esc_html_e( 'Real Results', 'estecapelli' ); ?>
			</span>
			<h2 class="t-ba__title"><?php esc_html_e( 'Before &amp; After', 'estecapelli' ); ?></h2>
			<?php if ( $service_title ) : ?>
				<p class="t-ba__lead">
					<?php
					/* translators: %s: service / treatment name. */
					printf( esc_html__( 'Outcomes from our %s patients.', 'estecapelli' ), esc_html( $service_title ) );
					?>
				</p>
			<?php endif; ?>
		</header>

		<div class="t-ba__carousel ec-carousel" data-carousel>
			<?php if ( $multi ) : ?>
				<button type="button" class="ec-carousel__nav ec-carousel__nav--prev" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous result', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
				</button>
			<?php endif; ?>

			<div class="t-ba__track ec-carousel__track" data-carousel-track>
				<?php
				foreach ( $cards as $card ) {
					set_query_var( 'ba_card', $card );
					load_template( locate_template( 'template-parts/before-after-card.php' ), false );
				}
				?>
			</div>

			<?php if ( $multi ) : ?>
				<button type="button" class="ec-carousel__nav ec-carousel__nav--next" data-carousel-next aria-label="<?php esc_attr_e( 'Next result', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
				</button>
			<?php endif; ?>
		</div>

	</div>
</section>
