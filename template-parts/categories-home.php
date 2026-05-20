<?php
/**
 * Homepage: 4-category accordion exposing every service we offer.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = estecapelli_home_categories();
if ( empty( $data['items'] ) ) {
	return;
}
?>

<section class="categories-home" aria-labelledby="categories-home-title">
	<div class="shell">

		<header class="categories-home__head">
			<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
				<span class="categories-home__eyebrow">
					<span class="categories-home__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $data['eyebrow'] ); ?>
				</span>
			<?php endif; ?>

			<h2 id="categories-home-title" class="categories-home__title">
				<?php echo esc_html( $data['headline'] ); ?>
			</h2>

			<?php if ( ! empty( $data['lead'] ) ) : ?>
				<p class="categories-home__lead"><?php echo esc_html( $data['lead'] ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="categories-home__list" data-category-accordion>
			<?php foreach ( $data['items'] as $i => $cat ) :
				$panel_id = 'cat-panel-' . $cat['key'];
				$is_first = ( 0 === $i );
				?>
				<li class="categories-home__item" data-category-item>
					<button
						type="button"
						class="categories-home__row"
						aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>"
						aria-controls="<?php echo esc_attr( $panel_id ); ?>"
						data-category-toggle
					>
						<span class="categories-home__icon" aria-hidden="true">
							<?php estecapelli_icon( $cat['icon'], array( 'width' => 28, 'height' => 28 ) ); ?>
						</span>
						<span class="categories-home__name"><?php echo esc_html( $cat['label'] ); ?></span>
						<span class="categories-home__chev" aria-hidden="true">
							<?php estecapelli_icon( 'chevron-down', array( 'width' => 18, 'height' => 18 ) ); ?>
						</span>
					</button>

					<div
						id="<?php echo esc_attr( $panel_id ); ?>"
						class="categories-home__panel"
						role="region"
						aria-label="<?php echo esc_attr( $cat['label'] ); ?>"
						<?php echo $is_first ? '' : 'hidden'; ?>
					>
						<?php
						if ( ! empty( $cat['data']['columns'] ) ) :
							$flat = array();
							foreach ( $cat['data']['columns'] as $col ) {
								foreach ( $col as $entry ) {
									$flat[] = $entry;
								}
							}
							?>
							<ul class="categories-home__services">
								<?php foreach ( $flat as $svc ) : ?>
									<li>
										<a class="categories-home__service" href="<?php echo esc_url( $svc['url'] ); ?>">
											<span class="categories-home__service-head">
												<span class="categories-home__service-title"><?php echo esc_html( $svc['label'] ); ?></span>
												<?php if ( ! empty( $svc['badge'] ) ) : ?>
													<span class="categories-home__service-badge"><?php echo esc_html( $svc['badge'] ); ?></span>
												<?php endif; ?>
											</span>
											<?php if ( ! empty( $svc['description'] ) ) : ?>
												<span class="categories-home__service-desc"><?php echo esc_html( $svc['description'] ); ?></span>
											<?php endif; ?>
											<span class="categories-home__service-arrow" aria-hidden="true">
												<?php estecapelli_icon( 'arrow-right', array( 'width' => 14, 'height' => 14 ) ); ?>
											</span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>

							<?php if ( ! empty( $cat['url'] ) ) : ?>
								<a class="categories-home__panel-cta" href="<?php echo esc_url( $cat['url'] ); ?>">
									<?php
									printf(
										/* translators: category name */
										esc_html__( 'See all %s services', 'estecapelli' ),
										esc_html( $cat['label'] )
									);
									?>
									<?php estecapelli_icon( 'arrow-right', array( 'width' => 14, 'height' => 14 ) ); ?>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
