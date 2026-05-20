<?php
/**
 * Homepage hero — trust-driven, doctor-led.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero = estecapelli_home_hero();
?>

<section class="hero-home" aria-labelledby="hero-home-title">

	<div class="hero-home__bg" aria-hidden="true">
		<span class="hero-home__glow hero-home__glow--a"></span>
		<span class="hero-home__glow hero-home__glow--b"></span>
		<span class="hero-home__grid"></span>
	</div>

	<div class="shell hero-home__shell">

		<div class="hero-home__copy">

			<?php if ( ! empty( $hero['eyebrow'] ) ) : ?>
				<span class="hero-home__eyebrow"><?php echo esc_html( $hero['eyebrow'] ); ?></span>
			<?php endif; ?>

			<h1 id="hero-home-title" class="hero-home__title">
				<?php echo esc_html( $hero['headline'] ); ?>
			</h1>

			<p class="hero-home__lead"><?php echo esc_html( $hero['body'] ); ?></p>

			<?php if ( ! empty( $hero['rating'] ) ) : $r = $hero['rating']; ?>
				<div class="hero-home__ratings" role="group" aria-label="<?php esc_attr_e( 'Patient ratings', 'estecapelli' ); ?>">
					<span class="hero-home__rating">
						<span class="hero-home__rating-stars" aria-hidden="true">
							<?php for ( $i = 0; $i < 5; $i++ ) : ?>
								<?php estecapelli_icon( 'star', array( 'width' => 14, 'height' => 14, 'class' => 'hero-home__rating-star hero-home__rating-star--trust' ) ); ?>
							<?php endfor; ?>
						</span>
						<span class="hero-home__rating-text">
							<strong><?php echo esc_html( $r['trustpilot_label'] ); ?></strong>
							<span><?php echo esc_html( $r['trustpilot_count'] ); ?></span>
						</span>
					</span>

					<span class="hero-home__rating-sep" aria-hidden="true"></span>

					<span class="hero-home__rating">
						<span class="hero-home__rating-stars" aria-hidden="true">
							<?php for ( $i = 0; $i < 5; $i++ ) : ?>
								<?php estecapelli_icon( 'star', array( 'width' => 14, 'height' => 14, 'class' => 'hero-home__rating-star hero-home__rating-star--google' ) ); ?>
							<?php endfor; ?>
						</span>
						<span class="hero-home__rating-text">
							<strong><?php echo esc_html( $r['google_score'] ); ?></strong>
							<span><?php echo esc_html( $r['google_count'] ); ?></span>
						</span>
					</span>
				</div>
			<?php endif; ?>

			<div class="hero-home__actions">
				<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $hero['cta_primary']['url'] ); ?>">
					<?php echo esc_html( $hero['cta_primary']['label'] ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
				</a>
				<a class="btn btn-whatsapp" href="<?php echo esc_url( $hero['cta_whatsapp']['url'] ); ?>" target="_blank" rel="noopener">
					<?php estecapelli_icon( 'whatsapp', array( 'width' => 18, 'height' => 18 ) ); ?>
					<?php echo esc_html( $hero['cta_whatsapp']['label'] ); ?>
				</a>
			</div>

			<?php if ( ! empty( $hero['stats'] ) ) : ?>
				<dl class="hero-home__stats">
					<?php foreach ( $hero['stats'] as $stat ) : ?>
						<div class="hero-home__stat">
							<dt class="hero-home__stat-value"><?php echo esc_html( $stat['value'] ); ?></dt>
							<dd class="hero-home__stat-label"><?php echo esc_html( $stat['label'] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

		</div>

		<div class="hero-home__visual">

			<div class="hero-home__photo-wrap">
				<div class="hero-home__photo-aura" aria-hidden="true"></div>
				<img
					class="hero-home__photo"
					src="<?php echo esc_url( $hero['doctor']['image'] ); ?>"
					alt="<?php echo esc_attr( $hero['doctor']['name'] . ' — ' . $hero['doctor']['role'] ); ?>"
					width="780"
					height="780"
					fetchpriority="high"
				/>

				<div class="hero-home__card hero-home__card--doctor" aria-hidden="true">
					<span class="hero-home__card-dot" aria-hidden="true"></span>
					<div class="hero-home__card-body">
						<strong class="hero-home__card-title"><?php echo esc_html( $hero['doctor']['name'] ); ?></strong>
						<span class="hero-home__card-meta"><?php echo esc_html( $hero['doctor']['role'] ); ?></span>
					</div>
				</div>

				<?php if ( ! empty( $hero['credential'] ) ) : $c = $hero['credential']; ?>
					<div class="hero-home__card hero-home__card--cred" aria-hidden="true">
						<span class="hero-home__card-eyebrow"><?php echo esc_html( $c['eyebrow'] ); ?></span>
						<?php foreach ( $c['lines'] as $line ) : ?>
							<span class="hero-home__card-line"><?php echo esc_html( $line ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

		</div>

	</div>
</section>
