<?php
/**
 * Homepage: Before & After album.
 *
 * A premium results gallery reading from the `result` CPT: each card shows
 * the patient's before/after photos, the country they travelled from, the
 * technique and graft count, plus a play button over the photo when a video
 * is defined for that result.
 *
 * The play button reuses the patient-stories video lightbox (rendered by
 * template-parts/patient-stories.php on the same page) via [data-stories-play].
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$results = function_exists( 'estecapelli_recent_results' ) ? estecapelli_recent_results( 10 ) : array();

$cards = array();
foreach ( $results as $result ) {
	$data = estecapelli_result_card_data( $result );
	if ( $data ) {
		$cards[] = $data;
	}
}
if ( empty( $cards ) ) {
	return;
}

$multi       = count( $cards ) > 1;
$gallery_url = home_url( '/en/before-after' );
?>

<section class="home-ba" aria-labelledby="home-ba-title">

	<div class="home-ba__bg" aria-hidden="true">
		<span class="home-ba__glow home-ba__glow--a"></span>
		<span class="home-ba__glow home-ba__glow--b"></span>
	</div>

	<div class="shell">

		<header class="home-ba__head">
			<span class="home-ba__eyebrow">
				<span class="home-ba__eyebrow-mark" aria-hidden="true"></span>
				<?php esc_html_e( 'REAL PATIENTS · REAL RESULTS', 'estecapelli' ); ?>
			</span>
			<h2 id="home-ba-title" class="home-ba__title"><?php esc_html_e( 'Before &amp; After', 'estecapelli' ); ?></h2>
			<p class="home-ba__lead"><?php esc_html_e( 'Outcomes from patients who travelled to us from around the world — every graft planned, every result their own.', 'estecapelli' ); ?></p>
		</header>

		<div class="home-ba__carousel ec-carousel" data-carousel>
			<?php if ( $multi ) : ?>
				<button type="button" class="ec-carousel__nav ec-carousel__nav--prev" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous result', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
				</button>
			<?php endif; ?>

			<ul class="home-ba__track ec-carousel__track" data-carousel-track>
				<?php
				foreach ( $cards as $card ) :
					$before = $card['before'];
					$after  = $card['after'];
					$method = $card['method'];
					$grafts = (int) $card['grafts'];
					$country = $card['country'];
					$video   = $card['video'];
					$title   = trim( $method . ( $country ? ' · ' . $country : '' ) );
					?>
					<li class="home-ba__card">

						<div class="home-ba__pair">
							<figure class="home-ba__half">
								<span class="home-ba__tag"><?php esc_html_e( 'Before', 'estecapelli' ); ?></span>
								<img class="home-ba__img" src="<?php echo esc_url( $before['url'] ); ?>" alt="<?php echo esc_attr( $before['alt'] ?: __( 'Before treatment', 'estecapelli' ) ); ?>" loading="lazy" decoding="async" />
							</figure>
							<figure class="home-ba__half">
								<span class="home-ba__tag home-ba__tag--after"><?php esc_html_e( 'After', 'estecapelli' ); ?></span>
								<img class="home-ba__img" src="<?php echo esc_url( $after['url'] ); ?>" alt="<?php echo esc_attr( $after['alt'] ?: __( 'After treatment', 'estecapelli' ) ); ?>" loading="lazy" decoding="async" />
							</figure>

							<?php if ( $video ) : ?>
								<button
									type="button"
									class="home-ba__play"
									data-stories-play="<?php echo esc_attr( $video ); ?>"
									data-story-title="<?php echo esc_attr( $title ?: __( 'Patient result', 'estecapelli' ) ); ?>"
									aria-label="<?php esc_attr_e( 'Play patient video', 'estecapelli' ); ?>"
								>
									<span class="home-ba__play-ring" aria-hidden="true"></span>
									<?php estecapelli_icon( 'play', array( 'width' => 24, 'height' => 24 ) ); ?>
								</button>
							<?php endif; ?>
						</div>

						<?php if ( $country || $method || $grafts ) : ?>
							<div class="home-ba__meta">
								<?php if ( $country ) : ?>
									<span class="home-ba__country">
										<?php estecapelli_icon( 'globe', array( 'width' => 15, 'height' => 15 ) ); ?>
										<?php echo esc_html( $country ); ?>
									</span>
								<?php endif; ?>

								<?php if ( $method || $grafts ) : ?>
									<div class="home-ba__facts">
										<?php if ( $method ) : ?>
											<span class="home-ba__chip"><?php echo esc_html( $method ); ?></span>
										<?php endif; ?>
										<?php if ( $grafts ) : ?>
											<span class="home-ba__chip home-ba__chip--grafts">
												<?php
												/* translators: %s: number of grafts, with thousands separator. */
												printf( esc_html__( '%s grafts', 'estecapelli' ), esc_html( number_format_i18n( $grafts ) ) );
												?>
											</span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( $multi ) : ?>
				<button type="button" class="ec-carousel__nav ec-carousel__nav--next" data-carousel-next aria-label="<?php esc_attr_e( 'Next result', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
				</button>
			<?php endif; ?>
		</div>

		<div class="home-ba__foot">
			<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $gallery_url ); ?>">
				<?php esc_html_e( 'View all results', 'estecapelli' ); ?>
				<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
			</a>
		</div>

	</div>
</section>
