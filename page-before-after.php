<?php
/**
 * Template Name: Before & After Gallery
 *
 * Lists every result grouped by category (main group, e.g. Hair Transplant)
 * then by service (technique, e.g. DHI, Sapphire FUE). Data comes from the
 * `result` CPT via estecapelli_results_grouped().
 *
 * @package Estecapelli
 */

get_header();

$groups = estecapelli_results_grouped();
?>

<div class="ba-page">

	<header class="ba-page__hero">
		<div class="shell">
			<span class="t-ba__eyebrow">
				<span class="t-ba__eyebrow-mark" aria-hidden="true"></span>
				<?php esc_html_e( 'Real Results', 'estecapelli' ); ?>
			</span>
			<h1 class="ba-page__title"><?php the_title(); ?></h1>
			<?php
			while ( have_posts() ) :
				the_post();
				$intro = get_the_content();
				if ( trim( wp_strip_all_tags( $intro ) ) ) :
					?>
					<div class="ba-page__intro"><?php the_content(); ?></div>
					<?php
				endif;
			endwhile;
			?>
		</div>
	</header>

	<?php if ( empty( $groups ) ) : ?>
		<div class="shell">
			<p class="ba-page__empty"><?php esc_html_e( 'Results will appear here soon.', 'estecapelli' ); ?></p>
		</div>
	<?php else : ?>
		<?php foreach ( $groups as $group ) : ?>
			<section class="ba-group">
				<div class="shell">
					<h2 class="ba-group__title"><?php echo esc_html( $group['term']->name ); ?></h2>

					<?php foreach ( $group['services'] as $service_block ) :
						$service = $service_block['service'];
						if ( ! $service ) { continue; }

						$cards = array();
						foreach ( $service_block['results'] as $result ) {
							$data = estecapelli_result_card_data( $result );
							if ( $data ) { $cards[] = $data; }
						}
						if ( empty( $cards ) ) { continue; }
						?>
						<div class="ba-technique">
							<div class="ba-technique__head">
								<h3 class="ba-technique__title"><?php echo esc_html( get_the_title( $service ) ); ?></h3>
								<a class="ba-technique__link" href="<?php echo esc_url( get_permalink( $service ) ); ?>">
									<?php esc_html_e( 'View treatment', 'estecapelli' ); ?>
									<?php estecapelli_icon( 'arrow-right', array( 'width' => 15, 'height' => 15 ) ); ?>
								</a>
							</div>

							<div class="ba-grid">
								<?php
								foreach ( $cards as $card ) {
									set_query_var( 'ba_card', $card );
									load_template( locate_template( 'template-parts/before-after-card.php' ), false );
								}
								?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>
	<?php endif; ?>

</div>

<?php
get_footer();
