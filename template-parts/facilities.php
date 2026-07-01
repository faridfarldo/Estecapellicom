<?php
/**
 * Homepage: "Our Clinic" — a photo/video gallery of the clinic, followed by a
 * rotating strip of partner-hotel logos.
 *
 * Video tiles reuse the shared patient-stories lightbox via [data-stories-play].
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data    = estecapelli_facilities();
$gallery = $data['gallery'] ?? array();
if ( empty( $gallery ) ) {
	return;
}
$partners = $data['partners'] ?? array();
?>

<section class="facilities" aria-labelledby="facilities-title">

	<div class="facilities__bg" aria-hidden="true">
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

			<h2 id="facilities-title" class="facilities__title"><?php echo esc_html( $data['headline'] ); ?></h2>

			<?php if ( ! empty( $data['lead'] ) ) : ?>
				<p class="facilities__lead"><?php echo esc_html( $data['lead'] ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="facilities__gallery">
			<?php foreach ( $gallery as $i => $item ) :
				$is_big   = ( 0 === $i );
				$is_video = ( 'video' === ( $item['type'] ?? 'image' ) && ! empty( $item['video'] ) );
				$cls      = 'facilities__tile' . ( $is_big ? ' facilities__tile--big' : '' );
				?>
				<li class="<?php echo esc_attr( $cls ); ?>">
					<?php if ( $is_video ) : ?>
						<button type="button" class="facilities__media facilities__media--video" data-stories-play="<?php echo esc_attr( $item['video'] ); ?>" data-story-title="<?php echo esc_attr( $item['caption'] ?? '' ); ?>">
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" decoding="async" />
							<span class="facilities__play" aria-hidden="true">
								<span class="facilities__play-ring"></span>
								<?php estecapelli_icon( 'play', array( 'width' => 22, 'height' => 22 ) ); ?>
							</span>
							<?php if ( ! empty( $item['caption'] ) ) : ?>
								<span class="facilities__caption"><?php echo esc_html( $item['caption'] ); ?></span>
							<?php endif; ?>
						</button>
					<?php else : ?>
						<figure class="facilities__media">
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['caption'] ?? '' ); ?>" loading="lazy" decoding="async" />
							<?php if ( ! empty( $item['caption'] ) ) : ?>
								<figcaption class="facilities__caption"><?php echo esc_html( $item['caption'] ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( ! empty( $data['cta']['url'] ) ) : ?>
			<div class="facilities__foot">
				<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $data['cta']['url'] ); ?>">
					<?php echo esc_html( $data['cta']['label'] ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
				</a>
			</div>
		<?php endif; ?>

	</div>

	<?php if ( ! empty( $partners['logos'] ) ) : ?>
		<div class="facilities__partners">
			<?php if ( ! empty( $partners['title'] ) ) : ?>
				<span class="facilities__partners-title"><?php echo esc_html( $partners['title'] ); ?></span>
			<?php endif; ?>

			<div class="facilities__marquee">
				<ul class="facilities__logos">
					<?php for ( $dup = 0; $dup < 2; $dup++ ) : ?>
						<?php foreach ( $partners['logos'] as $logo ) : ?>
							<li class="facilities__logo" <?php echo 1 === $dup ? 'aria-hidden="true"' : ''; ?>>
								<?php if ( ! empty( $logo['image'] ) ) : ?>
									<img src="<?php echo esc_url( $logo['image'] ); ?>" alt="<?php echo esc_attr( $logo['label'] ?? '' ); ?>" loading="lazy" decoding="async" />
								<?php else : ?>
									<span class="facilities__logo-name">
										<?php echo esc_html( $logo['label'] ?? '' ); ?>
										<?php if ( ! empty( $logo['stars'] ) ) : ?>
											<small class="facilities__logo-stars" aria-label="<?php
												/* translators: %d: number of stars. */
												echo esc_attr( sprintf( _n( '%d star', '%d stars', (int) $logo['stars'], 'estecapelli' ), (int) $logo['stars'] ) );
											?>"><?php echo str_repeat( '★', (int) $logo['stars'] ); ?></small>
										<?php endif; ?>
									</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					<?php endfor; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

</section>
