<?php
/**
 * Homepage hero — 3-slide carousel.
 *
 *   Slide 1: interactive Exosome/VITA diagonal split (estecapelli_signature_split).
 *   Slide 2: "most experienced" expert slide with patient photo + reviews badge.
 *   Slide 3: awards, Google review and a short intro video.
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
$aw     = $slides['awards'];

// Real patient before/after sets for the "experience" slide. Each folder under
// /assets/images/hero-results/{id}/ holds after.jpeg (the big result) plus
// b1.jpeg / b2.jpeg (the two "before" angles). The slide auto-rotates through
// these every few seconds (see initHeroResults in main.js).
$result_ids  = array( 2, 4, 5, 8, 10, 12 );
$result_base = get_template_directory_uri() . '/assets/images/hero-results/';
$results     = array();
foreach ( $result_ids as $rid ) {
	$results[] = array(
		'after' => $result_base . $rid . '/after.jpeg',
		'b1'    => $result_base . $rid . '/b1.jpeg',
		'b2'    => $result_base . $rid . '/b2.jpeg',
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

									<?php if ( ! empty( $p['cta']['url'] ) ) : ?>
										<div class="hero-split__detail">
											<a class="btn btn-accent hero-split__cta" href="<?php echo esc_url( $p['cta']['url'] ); ?>">
												<?php echo esc_html( $p['cta']['label'] ); ?>
												<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
											</a>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
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
							<img data-result-after src="<?php echo esc_url( $results[0]['after'] ); ?>" alt="<?php esc_attr_e( 'Estecapelli patient result', 'estecapelli' ); ?>" decoding="async" />

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

			<!-- ===== Slide 3 — Awards & reviews ===== -->
			<div class="hero-x__slide hero-x__slide--awards" data-hero-slide data-hero-title="<?php esc_attr_e( 'Awards & Reviews', 'estecapelli' ); ?>" data-hero-thumb="<?php echo esc_url( $exp['after'] ); ?>">
				<div class="hero-x__bg" aria-hidden="true">
					<span class="hero-x__glow hero-x__glow--a"></span>
					<span class="hero-x__glow hero-x__glow--b"></span>
				</div>

				<div class="shell hero-aw">
					<div class="hero-aw__copy">
						<?php if ( ! empty( $aw['eyebrow'] ) ) : ?>
							<span class="hero-aw__eyebrow"><?php echo esc_html( $aw['eyebrow'] ); ?></span>
						<?php endif; ?>
						<h2 class="hero-aw__title"><?php echo esc_html( $aw['headline'] ); ?></h2>

						<?php if ( ! empty( $aw['points'] ) ) : ?>
							<ul class="hero-aw__points">
								<?php foreach ( $aw['points'] as $point ) : ?>
									<li>
										<?php estecapelli_icon( 'check-circle', array( 'width' => 18, 'height' => 18 ) ); ?>
										<?php echo esc_html( $point ); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( ! empty( $aw['cta']['url'] ) ) : ?>
							<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $aw['cta']['url'] ); ?>">
								<?php echo esc_html( $aw['cta']['label'] ); ?>
								<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
							</a>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $aw['video'] ) ) : ?>
						<div class="hero-aw__media">
							<button type="button" class="hero-aw__video" data-stories-play="<?php echo esc_attr( $aw['video'] ); ?>" data-story-title="<?php esc_attr_e( 'Estecapelli — introduction', 'estecapelli' ); ?>">
								<span class="hero-aw__video-ring" aria-hidden="true"></span>
								<?php estecapelli_icon( 'play', array( 'width' => 26, 'height' => 26 ) ); ?>
								<span class="hero-aw__video-label"><?php esc_html_e( 'Watch our intro', 'estecapelli' ); ?></span>
							</button>
						</div>
					<?php endif; ?>
				</div>

				<div class="shell hero-aw__strip">
					<?php if ( ! empty( $aw['awards'] ) ) : ?>
						<ul class="hero-aw__badges">
							<?php foreach ( $aw['awards'] as $award ) : ?>
								<li><img src="<?php echo esc_url( $award['image'] ); ?>" alt="<?php echo esc_attr( $award['alt'] ); ?>" loading="lazy" /></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( ! empty( $aw['review'] ) ) : ?>
						<div class="hero-aw__review">
							<span class="hero-aw__stars" aria-hidden="true">
								<?php for ( $i = 0; $i < 5; $i++ ) {
									estecapelli_icon( 'star', array( 'width' => 16, 'height' => 16 ) );
								} ?>
							</span>
							<div class="hero-aw__review-text">
								<strong><?php echo esc_html( $aw['review']['score'] . '/' . $aw['review']['out_of'] ); ?></strong>
								<span><?php echo esc_html( $aw['review']['count'] ); ?></span>
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
