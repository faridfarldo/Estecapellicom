<?php
/**
 * Homepage: "Signature Methods" — three auto-rotating flip cards.
 *
 * Front face: photo + eyebrow + title + tease.
 * Back face:  stat hero + body + primary & secondary CTAs.
 *
 * Cards flip independently on a staggered CSS animation cycle;
 * hovering / focusing a card pauses the animation so the visitor can read.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = estecapelli_signature_methods();
if ( empty( $data['cards'] ) ) {
	return;
}

$secondary = $data['cta_secondary'] ?? array();

// Defensive fallbacks: if the ACF data leaves a card's "Learn more" link or the
// shared consultation link empty, resolve sensible defaults so each card is
// always connected to its own page and the consultation CTA opens the popup.
$cta_fallbacks   = array(
	'exosome'   => home_url( '/en/hair-transplant/exosome-fue-hair-transplant' ),
	'tricholab' => home_url( '/en/hair-transplant/tricholab' ),
	'vita'      => home_url( '/en/hair-transplant/vita-treatment' ),
);
$secondary_url   = ! empty( $secondary['url'] ) ? $secondary['url'] : home_url( '/en/contact' );
$secondary_label = ! empty( $secondary['label'] ) ? $secondary['label'] : ( $secondary['title'] ?? __( 'Schedule a Free Consultation', 'estecapelli' ) );
?>

<section class="signature" aria-labelledby="signature-title">

	<div class="signature__bg" aria-hidden="true">
		<span class="signature__bg-glow signature__bg-glow--a"></span>
		<span class="signature__bg-glow signature__bg-glow--b"></span>
	</div>

	<div class="shell">

		<header class="signature__head">
			<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
				<span class="signature__eyebrow" aria-hidden="true">
					<span class="signature__eyebrow-mark"></span>
					<?php echo esc_html( $data['eyebrow'] ); ?>
				</span>
			<?php endif; ?>

			<h2 id="signature-title" class="signature__title">
				<?php echo esc_html( $data['headline'] ); ?>
			</h2>

			<?php if ( ! empty( $data['lead'] ) ) : ?>
				<p class="signature__lead"><?php echo esc_html( $data['lead'] ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="signature__grid">
			<?php foreach ( $data['cards'] as $i => $card ) : ?>
				<li class="signature__card" style="--card: <?php echo (int) $i; ?>" data-key="<?php echo esc_attr( $card['key'] ); ?>">

					<div class="signature__inner" tabindex="0" aria-label="<?php echo esc_attr( $card['title'] . ' — ' . $card['subtitle'] ); ?>">

						<!-- Front face -->
						<div class="signature__face signature__face--front">
							<div class="signature__media" aria-hidden="true">
								<img
									class="signature__img"
									src="<?php echo esc_url( $card['image'] ); ?>"
									alt=""
									loading="lazy"
									decoding="async"
								/>
								<span class="signature__media-wash"></span>
							</div>

							<?php if ( ! empty( $card['eyebrow'] ) ) : ?>
								<span class="signature__card-eyebrow">
									<span class="signature__card-eyebrow-dot" aria-hidden="true"></span>
									<?php echo esc_html( $card['eyebrow'] ); ?>
								</span>
							<?php endif; ?>

							<div class="signature__front-body">
								<h3 class="signature__card-title">
									<?php echo esc_html( $card['title'] ); ?>
									<span class="signature__card-subtitle"><?php echo esc_html( $card['subtitle'] ); ?></span>
								</h3>
								<?php if ( ! empty( $card['tease'] ) ) : ?>
									<p class="signature__card-tease"><?php echo esc_html( $card['tease'] ); ?></p>
								<?php endif; ?>
							</div>

							<span class="signature__hint" aria-hidden="true">
								<span class="signature__hint-icon">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
										<path d="M21 12a9 9 0 1 1-3-6.7"/>
										<polyline points="21 4 21 9 16 9"/>
									</svg>
								</span>
								<?php esc_html_e( 'Hover or tap to discover', 'estecapelli' ); ?>
							</span>
						</div>

						<!-- Back face -->
						<div class="signature__face signature__face--back">
							<div class="signature__back-aura" aria-hidden="true"></div>

							<div class="signature__back-stat">
								<span class="signature__back-stat-value"><?php echo esc_html( $card['stat'] ); ?></span>
								<span class="signature__back-stat-label"><?php echo esc_html( $card['stat_label'] ); ?></span>
							</div>

							<div class="signature__back-body">
								<h3 class="signature__back-title"><?php echo esc_html( $card['title'] ); ?></h3>
								<p class="signature__back-text"><?php echo esc_html( $card['body'] ); ?></p>
							</div>

							<?php
							$cta       = $card['cta'] ?? array();
							$cta_url   = ! empty( $cta['url'] ) ? $cta['url'] : ( $cta_fallbacks[ $card['key'] ] ?? '' );
							$cta_label = $cta['label'] ?? ( $cta['title'] ?? '' );
							if ( '' === $cta_label ) {
								$cta_label = __( 'Learn more', 'estecapelli' );
							}
							?>
							<div class="signature__back-actions">
								<?php if ( $cta_url ) : ?>
									<a class="btn btn-accent btn-sm" href="<?php echo esc_url( $cta_url ); ?>">
										<?php echo esc_html( $cta_label ); ?>
										<?php estecapelli_icon( 'arrow-right', array( 'width' => 14, 'height' => 14 ) ); ?>
									</a>
								<?php endif; ?>
								<?php // Always open the consultation popup (href is the no-JS fallback). ?>
								<a class="btn btn-ghost-light btn-sm" href="<?php echo esc_url( $secondary_url ); ?>" data-lead-popup>
									<?php echo esc_html( $secondary_label ); ?>
								</a>
							</div>
						</div>

					</div>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
