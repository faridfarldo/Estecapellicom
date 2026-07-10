<?php
/**
 * Homepage: tabbed featured-services block.
 * Tab buttons across the top, card grid below that swaps on tab change.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = estecapelli_home_services();
if ( empty( $data['categories'] ) ) {
	return;
}
?>

<section class="services-home" aria-labelledby="services-home-title">
	<div class="shell">

		<header class="services-home__head">
			<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
				<span class="services-home__eyebrow">
					<span class="services-home__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $data['eyebrow'] ); ?>
				</span>
			<?php endif; ?>

			<h2 id="services-home-title" class="services-home__title">
				<?php echo esc_html( $data['headline'] ); ?>
			</h2>

			<?php if ( ! empty( $data['lead'] ) ) : ?>
				<p class="services-home__lead"><?php echo esc_html( $data['lead'] ); ?></p>
			<?php endif; ?>
		</header>

		<?php
		// Custom uploaded tab icons per category (inlined so they inherit the tab
		// colour via fill:currentColor). Unmapped categories keep the built-in icon.
		$svc_tab_icons = array(
			'hair-transplant'  => 'hair-transplant.svg',
			'plastic-surgery'  => 'plastic-surgery.svg',
			'dental-treatment' => 'dental.svg',
		);
		?>
		<div class="services-home__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Treatment categories', 'estecapelli' ); ?>" data-services-tablist>
			<?php foreach ( $data['categories'] as $i => $cat ) :
				$tab_id   = 'svc-tab-' . $cat['key'];
				$panel_id = 'svc-panel-' . $cat['key'];
				$is_first = ( 0 === $i );
				?>
				<button
					type="button"
					id="<?php echo esc_attr( $tab_id ); ?>"
					class="services-home__tab"
					role="tab"
					aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>"
					aria-controls="<?php echo esc_attr( $panel_id ); ?>"
					tabindex="<?php echo $is_first ? '0' : '-1'; ?>"
					data-services-tab
				>
					<span class="services-home__tab-icon" aria-hidden="true">
						<?php
						$svc_icon_file = isset( $svc_tab_icons[ $cat['key'] ] )
							? get_template_directory() . '/assets/images/service-icons/' . $svc_tab_icons[ $cat['key'] ]
							: '';
						if ( $svc_icon_file && file_exists( $svc_icon_file ) ) {
							// Trusted theme asset; strip the XML prolog/comments and force
							// currentColor so the glyph matches the active/inactive tab colour.
							$svc_svg = (string) file_get_contents( $svc_icon_file );
							$svc_svg = preg_replace( '/<\?xml.*?\?>/s', '', $svc_svg );
							$svc_svg = preg_replace( '/<!--.*?-->/s', '', $svc_svg );
							$svc_svg = preg_replace( '/<svg\b/', '<svg fill="currentColor" focusable="false"', $svc_svg, 1 );
							echo $svc_svg; // phpcs:ignore WordPress.Security.EscapeOutput
						} else {
							estecapelli_icon( $cat['icon'], array( 'width' => 18, 'height' => 18 ) );
						}
						?>
					</span>
					<span class="services-home__tab-label"><?php echo esc_html( $cat['label'] ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<?php foreach ( $data['categories'] as $i => $cat ) :
			$tab_id   = 'svc-tab-' . $cat['key'];
			$panel_id = 'svc-panel-' . $cat['key'];
			$is_first = ( 0 === $i );
			$count    = count( $cat['items'] );
			$multi    = $count > 1;
			?>
			<div
				id="<?php echo esc_attr( $panel_id ); ?>"
				class="services-home__panel ec-carousel"
				role="tabpanel"
				aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
				data-services-panel
				data-carousel
				<?php echo $is_first ? '' : 'hidden'; ?>
			>
				<?php if ( $multi ) : ?>
					<button type="button" class="ec-carousel__nav ec-carousel__nav--prev" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous treatments', 'estecapelli' ); ?>">
						<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
					</button>
				<?php endif; ?>

				<ul class="services-home__grid" data-carousel-track>
					<?php foreach ( $cat['items'] as $idx => $item ) : ?>
						<li class="services-home__card<?php echo empty( $item['image'] ) ? ' services-home__card--no-image' : ''; ?>" data-cat-key="<?php echo esc_attr( $cat['key'] ); ?>">
							<a class="services-home__link" href="<?php echo esc_url( $item['url'] ); ?>">

								<span class="services-home__media" aria-hidden="true">
									<?php if ( ! empty( $item['image'] ) ) : ?>
										<img
											class="services-home__img"
											src="<?php echo esc_url( $item['image'] ); ?>"
											alt=""
											loading="lazy"
											decoding="async"
											width="1200"
											height="800"
										/>
									<?php else : ?>
										<span class="services-home__media-icon">
											<?php estecapelli_icon( $cat['icon'], array( 'width' => 96, 'height' => 96 ) ); ?>
										</span>
									<?php endif; ?>
									<span class="services-home__media-wash"></span>
								</span>

								<span class="services-home__index" aria-hidden="true">
									<?php echo esc_html( str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>
									<span class="services-home__index-of">/ <?php echo esc_html( str_pad( (string) $count, 2, '0', STR_PAD_LEFT ) ); ?></span>
								</span>

								<?php if ( ! empty( $item['badge'] ) ) : ?>
									<span class="services-home__badge"><?php echo esc_html( $item['badge'] ); ?></span>
								<?php endif; ?>

								<span class="services-home__body">
									<?php if ( ! empty( $item['tag'] ) ) : ?>
										<span class="services-home__tag"><?php echo esc_html( $item['tag'] ); ?></span>
									<?php endif; ?>
									<span class="services-home__name"><?php echo esc_html( $item['title'] ); ?></span>
									<?php if ( ! empty( $item['description'] ) ) : ?>
										<span class="services-home__desc"><?php echo esc_html( $item['description'] ); ?></span>
									<?php endif; ?>
									<span class="services-home__cta">
										<?php esc_html_e( 'Learn More', 'estecapelli' ); ?>
										<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16, 'class' => 'services-home__cta-arrow' ) ); ?>
									</span>
								</span>

							</a>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( $multi ) : ?>
					<button type="button" class="ec-carousel__nav ec-carousel__nav--next" data-carousel-next aria-label="<?php esc_attr_e( 'Next treatments', 'estecapelli' ); ?>">
						<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
					</button>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

	</div>
</section>
