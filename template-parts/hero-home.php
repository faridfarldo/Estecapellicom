<?php
/**
 * Homepage hero — interactive diagonal split of the two trademarked techniques.
 *
 * A fixed intro sits above a clip-path "stage" holding two panels: Exosome on
 * the left, VITA on the right, divided by a moving diagonal seam. Clicking a
 * panel expands it to fill the stage while the other stays a clickable sliver.
 * Progressive enhancement: with no JS both panels stay balanced and readable.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$split  = estecapelli_signature_split();
$intro  = $split['intro'];
$panels = $split['panels'];
?>

<section class="hero-split" aria-labelledby="hero-split-title">

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
					<img
						class="hero-split__cover"
						src="<?php echo esc_url( $p['cover'] ); ?>"
						alt="<?php echo esc_attr( $p['name'] . ' ' . $p['tag'] ); ?>"
						loading="lazy"
						decoding="async"
					/>
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

</section>
