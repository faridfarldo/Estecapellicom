<?php
/**
 * Homepage: "Our Facilities" — two large editorial cards (hotel + clinic).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = estecapelli_facilities();
if ( empty( $data['cards'] ) ) {
	return;
}
?>

<section class="facilities" aria-labelledby="facilities-title">

	<div class="facilities__bg" aria-hidden="true">
		<span class="facilities__bg-line"></span>
		<span class="facilities__bg-mark facilities__bg-mark--a"></span>
		<span class="facilities__bg-mark facilities__bg-mark--b"></span>
	</div>

	<div class="shell">

		<header class="facilities__head">
			<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
				<span class="facilities__eyebrow">
					<span class="facilities__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $data['eyebrow'] ); ?>
				</span>
			<?php endif; ?>

			<h2 id="facilities-title" class="facilities__title">
				<?php echo esc_html( $data['headline'] ); ?>
			</h2>

			<?php if ( ! empty( $data['lead'] ) ) : ?>
				<p class="facilities__lead"><?php echo esc_html( $data['lead'] ); ?></p>
			<?php endif; ?>
		</header>

		<div class="facilities__grid">
			<?php foreach ( $data['cards'] as $i => $card ) :
				$image = estecapelli_resolve_image( 'facilities', $card['image'] );
				?>
				<article class="facilities__card" data-key="<?php echo esc_attr( $card['key'] ); ?>" style="--card: <?php echo (int) $i; ?>">

					<a class="facilities__card-link" href="<?php echo esc_url( $card['cta']['url'] ); ?>" aria-label="<?php echo esc_attr( $card['name'] ); ?>">

						<div class="facilities__media<?php echo $image ? '' : ' facilities__media--placeholder'; ?>" aria-hidden="true">
							<?php if ( $image ) : ?>
								<img class="facilities__img" src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy" decoding="async" />
							<?php else : ?>
								<span class="facilities__media-fallback">
									<?php estecapelli_icon( 'hotel' === $card['key'] ? 'bed' : 'building', array( 'width' => 64, 'height' => 64 ) ); ?>
								</span>
							<?php endif; ?>
							<span class="facilities__media-wash"></span>

							<span class="facilities__kind">
								<span class="facilities__kind-dot" aria-hidden="true"></span>
								<?php echo esc_html( $card['kind'] ); ?>
							</span>
						</div>

						<div class="facilities__body">
							<h3 class="facilities__name"><?php echo esc_html( $card['name'] ); ?></h3>

							<?php if ( ! empty( $card['location'] ) ) : ?>
								<p class="facilities__location">
									<?php estecapelli_icon( 'map-pin', array( 'width' => 14, 'height' => 14, 'class' => 'facilities__location-icon' ) ); ?>
									<?php echo esc_html( $card['location'] ); ?>
								</p>
							<?php endif; ?>

							<p class="facilities__text"><?php echo esc_html( $card['body'] ); ?></p>

							<?php if ( ! empty( $card['amenities'] ) ) : ?>
								<ul class="facilities__amenities">
									<?php foreach ( $card['amenities'] as $a ) : ?>
										<li class="facilities__amenity">
											<span class="facilities__amenity-icon" aria-hidden="true">
												<?php estecapelli_icon( $a['icon'], array( 'width' => 14, 'height' => 14 ) ); ?>
											</span>
											<?php echo esc_html( $a['label'] ); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<span class="facilities__cta">
								<?php echo esc_html( $card['cta']['label'] ); ?>
								<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16, 'class' => 'facilities__cta-arrow' ) ); ?>
							</span>
						</div>

					</a>
				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>
