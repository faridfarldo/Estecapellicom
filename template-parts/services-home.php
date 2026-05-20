<?php
/**
 * Homepage: featured hair-transplant services grid.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = estecapelli_home_services();
if ( empty( $data['items'] ) ) {
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

		<ul class="services-home__grid">
			<?php foreach ( $data['items'] as $i => $item ) : ?>
				<li class="services-home__card">
					<a class="services-home__link" href="<?php echo esc_url( $item['url'] ); ?>">

						<span class="services-home__media" aria-hidden="true">
							<img
								class="services-home__img"
								src="<?php echo esc_url( $item['image'] ); ?>"
								alt=""
								loading="lazy"
								decoding="async"
								width="1200"
								height="800"
							/>
							<span class="services-home__media-wash"></span>
						</span>

						<span class="services-home__index" aria-hidden="true">
							<?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>
							<span class="services-home__index-of">/ <?php echo esc_html( str_pad( (string) count( $data['items'] ), 2, '0', STR_PAD_LEFT ) ); ?></span>
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

	</div>
</section>
