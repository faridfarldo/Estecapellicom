<?php
/**
 * Homepage hero — 3-slide carousel.
 *
 *   Slide 1: interactive Exosome/VITA diagonal split (estecapelli_signature_split).
 *   Slide 2: "most experienced" expert slide with patient photo + reviews badge.
 *   Slide 3: women's hair transplant — copy beside an auto-playing intro video.
 *
 * Slides 2 & 3 read from estecapelli_hero_slides(). Auto-advances; arrows + dots.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$split  = estecapelli_signature_split();
$intro  = $split['intro'];
$panels = $split['panels'];

$slides = estecapelli_hero_slides();
$exp    = $slides['expert'];
$women  = $slides['women'];

// Real patient before/after sets for the "experience" slide. Each folder under
// /assets/images/hero-results/{id}/ holds after.jpeg (the big result) plus
// b1.jpeg / b2.jpeg (the two "before" angles). The slide auto-rotates through
// these every few seconds (see initHeroResults in main.js).
$result_ids  = array( 2, 4, 5, 8, 10, 12 );
$result_base = get_template_directory_uri() . '/assets/images/hero-results/';
// Per-patient framing for the big "after" shot (the photos have different
// orientations and the face sits at a different height in each).
$result_pos  = array(
	2  => '50% 30%',
	4  => '50% 12%',
	5  => '50% 30%',
	8  => '50% 45%',
	10 => '50% 45%',
	12 => '50% 100%',
);
$results     = array();
foreach ( $result_ids as $rid ) {
	$results[] = array(
		'after' => $result_base . $rid . '/after.jpeg',
		'b1'    => $result_base . $rid . '/b1.jpeg',
		'b2'    => $result_base . $rid . '/b2.jpeg',
		'pos'   => isset( $result_pos[ $rid ] ) ? $result_pos[ $rid ] : '50% 25%',
	);
}
?>

<section class="hero-x" aria-label="<?php esc_attr_e( 'Highlights', 'estecapelli' ); ?>" data-hero-carousel>
	<div class="hero-x__viewport">
		<div class="hero-x__track" data-hero-track>

			<!-- ===== Slide 1 — Exosome / VITA split ===== -->
			<div class="hero-x__slide" data-hero-slide data-hero-title="<?php esc_attr_e( 'Signature Methods', 'estecapelli' ); ?>" data-hero-thumb="<?php echo esc_url( $panels['exosome']['cover'] ?? '' ); ?>">
				<div class="hero-split" aria-labelledby="hero-split-title">

					<div class="hero-split__bg" aria-hidden="true">
						<span class="hero-split__glow hero-split__glow--a"></span>
						<span class="hero-split__glow hero-split__glow--b"></span>
						<span class="hero-split__grid"></span>
					</div>

					<div class="shell hero-split__intro">
						<?php if ( ! empty( $intro['eyebrow'] ) ) : ?>
							<span class="hero-split__eyebrow"><?php echo esc_html( $intro['eyebrow'] ); ?></span>
						<?php endif; ?>
						<h1 id="hero-split-title" class="hero-split__headline"><?php echo esc_html( $intro['headline'] ); ?></h1>
						<p class="hero-split__lead"><?php echo esc_html( $intro['body'] ); ?></p>
						<?php if ( ! empty( $intro['hint'] ) ) : ?>
							<span class="hero-split__hint">
								<?php estecapelli_icon( 'sparkles', array( 'width' => 14, 'height' => 14 ) ); ?>
								<?php echo esc_html( $intro['hint'] ); ?>
							</span>
						<?php endif; ?>
					</div>

					<div class="hero-split__stage" data-split-stage data-split-active="">
						<?php foreach ( $panels as $key => $p ) : ?>
							<div class="hero-split__panel hero-split__panel--<?php echo esc_attr( $key ); ?>" data-split-panel="<?php echo esc_attr( $key ); ?>" data-open="false">

								<?php if ( ! empty( $p['cover'] ) ) : ?>
									<img class="hero-split__cover" src="<?php echo esc_url( $p['cover'] ); ?>" alt="<?php echo esc_attr( $p['name'] . ' ' . $p['tag'] ); ?>" loading="lazy" decoding="async" />
								<?php endif; ?>

								<?php if ( ! empty( $p['video'] ) ) : ?>
									<div class="hero-split__video" data-split-video data-video-id="<?php echo esc_attr( $p['video'] ); ?>" aria-hidden="true"></div>
									<button type="button" class="hero-split__close" data-split-close="<?php echo esc_attr( $key ); ?>" aria-label="<?php esc_attr_e( 'Close video', 'estecapelli' ); ?>">&times;</button>
								<?php endif; ?>

								<span class="hero-split__scrim" aria-hidden="true"></span>
								<span class="hero-split__seam" aria-hidden="true"></span>

								<?php if ( ! empty( $p['video'] ) ) : ?>
									<span class="hero-split__play" aria-hidden="true">
										<?php estecapelli_icon( 'play', array( 'width' => 22, 'height' => 22 ) ); ?>
									</span>
								<?php endif; ?>

								<button type="button" class="hero-split__toggle" data-split-toggle="<?php echo esc_attr( $key ); ?>" aria-pressed="false">
									<span class="sr-only"><?php
										/* translators: %s: technique name. */
										printf( esc_html__( 'Play %s video', 'estecapelli' ), esc_html( $p['name'] ) );
									?></span>
								</button>

								<div class="hero-split__content">
									<span class="hero-split__name">
										<?php echo esc_html( $p['name'] ); ?><sup class="hero-split__tm"><?php echo esc_html( $p['trademark'] ); ?></sup>
										<span class="hero-split__tag"><?php echo esc_html( $p['tag'] ); ?></span>
									</span>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<?php $hs_learn = $panels['exosome']['cta']['url'] ?? ( $panels['vita']['cta']['url'] ?? home_url( '/en/' ) ); ?>
					<div class="shell hero-split__more">
						<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $hs_learn ); ?>">
							<?php esc_html_e( 'Learn more', 'estecapelli' ); ?>
							<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
						</a>
					</div>

				</div>
			</div>

			<!-- ===== Slide 2 — Most experienced ===== -->
			<div class="hero-x__slide hero-x__slide--expert" data-hero-slide data-hero-title="<?php esc_attr_e( 'World-Class Experience', 'estecapelli' ); ?>" data-hero-thumb="<?php echo esc_url( $results[0]['after'] ?? $exp['photo'] ); ?>">
				<div class="hero-x__bg" aria-hidden="true">
					<span class="hero-x__glow hero-x__glow--a"></span>
					<span class="hero-x__glow hero-x__glow--b"></span>
				</div>

				<div class="shell hero-exp">
					<div class="hero-exp__copy">
						<?php if ( ! empty( $exp['eyebrow'] ) ) : ?>
							<span class="hero-exp__eyebrow"><?php echo esc_html( $exp['eyebrow'] ); ?></span>
						<?php endif; ?>
						<h2 class="hero-exp__title"><?php echo esc_html( $exp['headline'] ); ?></h2>
						<p class="hero-exp__lead"><?php echo esc_html( $exp['body'] ); ?></p>
						<?php if ( ! empty( $exp['cta']['url'] ) ) : ?>
							<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $exp['cta']['url'] ); ?>">
								<?php echo esc_html( $exp['cta']['label'] ); ?>
								<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
							</a>
						<?php endif; ?>
					</div>

					<div class="hero-exp__media" data-hero-results="<?php echo esc_attr( wp_json_encode( $results ) ); ?>">
						<figure class="hero-exp__photo">
							<img data-result-after src="<?php echo esc_url( $results[0]['after'] ); ?>" alt="<?php esc_attr_e( 'Estecapelli patient result', 'estecapelli' ); ?>" style="object-position: <?php echo esc_attr( $results[0]['pos'] ); ?>;" decoding="async" />

							<?php if ( ! empty( $exp['badge'] ) ) : ?>
								<span class="hero-exp__reviews">
									<span class="hero-exp__stars" aria-hidden="true">
										<?php for ( $i = 0; $i < 5; $i++ ) {
											estecapelli_icon( 'star', array( 'width' => 14, 'height' => 14 ) );
										} ?>
									</span>
									<strong><?php echo esc_html( $exp['badge']['value'] ); ?></strong>
									<span><?php echo esc_html( $exp['badge']['label'] ); ?></span>
								</span>
							<?php endif; ?>

							<figcaption class="hero-exp__cap">
								<strong><?php esc_html_e( 'After result', 'estecapelli' ); ?></strong>
								<?php esc_html_e( 'Actual Estecapelli patient. Individual results may vary.', 'estecapelli' ); ?>
							</figcaption>
						</figure>

						<div class="hero-exp__controls">
							<button type="button" class="hero-exp__arrow" data-result-prev aria-label="<?php esc_attr_e( 'Previous patient', 'estecapelli' ); ?>">
								<?php estecapelli_icon( 'chevron-left', array( 'width' => 20, 'height' => 20 ) ); ?>
							</button>
							<div class="hero-exp__ba">
								<figure>
									<img data-result-b1 src="<?php echo esc_url( $results[0]['b1'] ); ?>" alt="" loading="lazy" decoding="async" />
									<figcaption><?php esc_html_e( 'Before', 'estecapelli' ); ?></figcaption>
								</figure>
								<figure>
									<img data-result-b2 src="<?php echo esc_url( $results[0]['b2'] ); ?>" alt="" loading="lazy" decoding="async" />
									<figcaption><?php esc_html_e( 'Before', 'estecapelli' ); ?></figcaption>
								</figure>
							</div>
							<button type="button" class="hero-exp__arrow" data-result-next aria-label="<?php esc_attr_e( 'Next patient', 'estecapelli' ); ?>">
								<?php estecapelli_icon( 'chevron-right', array( 'width' => 20, 'height' => 20 ) ); ?>
							</button>
						</div>

						<?php if ( ! empty( $exp['years'] ) ) : ?>
							<span class="hero-exp__seal" aria-hidden="true">
								<small><?php echo esc_html( $exp['years']['from'] ); ?></small>
								<strong>ESTECAPELLI</strong>
								<small><?php echo esc_html( $exp['years']['label'] ); ?></small>
								<small><?php echo esc_html( $exp['years']['to'] ); ?></small>
							</span>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- ===== Slide 3 — Women's hair transplant ===== -->
			<?php
			$women_vid   = $women['video'] ?? '';
			$women_thumb = $women_vid ? 'https://i.ytimg.com/vi/' . rawurlencode( $women_vid ) . '/hqdefault.jpg' : '';
			// Muted, looping autoplay — starts on its own with no click. Muted is
			// required for browsers to allow unprompted autoplay; native controls let
			// the visitor unmute. loop needs the playlist=<id> companion param.
			$women_src = $women_vid ? 'https://www.youtube.com/embed/' . rawurlencode( $women_vid )
				. '?autoplay=1&mute=1&loop=1&playlist=' . rawurlencode( $women_vid )
				. '&controls=1&rel=0&modestbranding=1&playsinline=1' : '';
			?>
			<div class="hero-x__slide hero-x__slide--women" data-hero-slide data-hero-title="<?php esc_attr_e( 'Women’s Hair Transplant', 'estecapelli' ); ?>" data-hero-thumb="<?php echo esc_url( $women_thumb ); ?>">
				<div class="hero-x__bg" aria-hidden="true">
					<span class="hero-x__glow hero-x__glow--a"></span>
					<span class="hero-x__glow hero-x__glow--b"></span>
				</div>

				<div class="shell hero-wmn">
					<div class="hero-wmn__copy">
						<?php if ( ! empty( $women['eyebrow'] ) ) : ?>
							<span class="hero-wmn__eyebrow"><?php echo esc_html( $women['eyebrow'] ); ?></span>
						<?php endif; ?>
						<h2 class="hero-wmn__title"><?php echo esc_html( $women['headline'] ); ?></h2>
						<?php if ( ! empty( $women['body'] ) ) : ?>
							<p class="hero-wmn__lead"><?php echo esc_html( $women['body'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $women['points'] ) ) : ?>
							<ul class="hero-wmn__points">
								<?php foreach ( $women['points'] as $point ) : ?>
									<li>
										<?php estecapelli_icon( 'check-circle', array( 'width' => 18, 'height' => 18 ) ); ?>
										<?php echo esc_html( $point ); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( ! empty( $women['cta']['url'] ) ) : ?>
							<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $women['cta']['url'] ); ?>">
								<?php echo esc_html( $women['cta']['label'] ); ?>
								<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
							</a>
						<?php endif; ?>
					</div>

					<?php if ( $women_src ) : ?>
						<div class="hero-wmn__media">
							<div class="hero-wmn__video">
								<iframe
									src="<?php echo esc_url( $women_src ); ?>"
									title="<?php esc_attr_e( 'Women’s hair transplant at Estecapelli', 'estecapelli' ); ?>"
									allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
									allowfullscreen
									loading="lazy"></iframe>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>

		</div>
	</div>

	<button type="button" class="hero-x__nav hero-x__nav--prev" data-hero-prev aria-label="<?php esc_attr_e( 'Previous slide', 'estecapelli' ); ?>">
		<span class="hero-x__nav-thumb" data-hero-prev-thumb aria-hidden="true"></span>
		<span class="hero-x__nav-chevron"><?php estecapelli_icon( 'chevron-left', array( 'width' => 24, 'height' => 24 ) ); ?></span>
	</button>
	<button type="button" class="hero-x__nav hero-x__nav--next" data-hero-next aria-label="<?php esc_attr_e( 'Next slide', 'estecapelli' ); ?>">
		<span class="hero-x__nav-thumb" data-hero-next-thumb aria-hidden="true"></span>
		<span class="hero-x__nav-chevron"><?php estecapelli_icon( 'chevron-right', array( 'width' => 24, 'height' => 24 ) ); ?></span>
	</button>

	<div class="hero-x__dots" role="tablist" aria-label="<?php esc_attr_e( 'Choose slide', 'estecapelli' ); ?>">
		<?php for ( $i = 0; $i < 3; $i++ ) : ?>
			<button type="button" class="hero-x__dot" data-hero-dot="<?php echo (int) $i; ?>" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-label="<?php
				/* translators: %d: slide number. */
				printf( esc_attr__( 'Slide %d', 'estecapelli' ), (int) $i + 1 );
			?>"></button>
		<?php endfor; ?>
	</div>
</section>
