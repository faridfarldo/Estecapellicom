<?php
/**
 * Section: Hero — title, lead, CTAs, optional image or YouTube video.
 *
 * Expects ACF Flexible Content fields under $section.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) {
	return;
}

$eyebrow  = $section['eyebrow']  ?? '';
$title    = $section['title']    ?? '';
$lead     = $section['lead']     ?? '';
$cta_p    = $section['cta_primary']   ?? array();
$cta_s    = $section['cta_secondary'] ?? array();
$mtype    = $section['media_type'] ?? 'image';
$image    = $section['image']    ?? array();
$video_id = $section['video_id'] ?? '';

if ( ! $title ) {
	return;
}
?>

<section class="t-hero" aria-labelledby="t-hero-title">

	<div class="t-hero__bg" aria-hidden="true">
		<span class="t-hero__glow t-hero__glow--a"></span>
		<span class="t-hero__glow t-hero__glow--b"></span>
	</div>

	<div class="shell t-hero__shell">

		<div class="t-hero__copy">

			<?php if ( $eyebrow ) : ?>
				<span class="t-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<h1 id="t-hero-title" class="t-hero__title"><?php echo esc_html( $title ); ?></h1>

			<?php if ( $lead ) : ?>
				<p class="t-hero__lead"><?php echo wp_kses_post( wpautop( $lead ) ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $cta_p['url'] ) || ! empty( $cta_s['url'] ) ) : ?>
				<div class="t-hero__actions">
					<?php if ( ! empty( $cta_p['url'] ) ) : ?>
						<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $cta_p['url'] ); ?>">
							<?php echo esc_html( $cta_p['label'] ?: __( 'Get Started', 'estecapelli' ) ); ?>
							<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
						</a>
					<?php endif; ?>
					<?php if ( ! empty( $cta_s['url'] ) ) : ?>
						<a class="btn btn-ghost-light btn-lg" href="<?php echo esc_url( $cta_s['url'] ); ?>">
							<?php echo esc_html( $cta_s['label'] ?: __( 'Learn More', 'estecapelli' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>

		<?php if ( 'image' === $mtype && ! empty( $image['url'] ) ) : ?>
			<figure class="t-hero__visual">
				<img
					class="t-hero__img"
					src="<?php echo esc_url( $image['url'] ); ?>"
					alt="<?php echo esc_attr( $image['alt'] ?? $title ); ?>"
					width="<?php echo (int) ( $image['width']  ?? 1200 ); ?>"
					height="<?php echo (int) ( $image['height'] ?? 800  ); ?>"
					fetchpriority="high"
				/>
			</figure>
		<?php elseif ( 'video' === $mtype && $video_id ) : ?>
			<figure class="t-hero__visual t-hero__visual--video">
				<div class="t-hero__video">
					<iframe
						src="https://www.youtube.com/embed/<?php echo esc_attr( $video_id ); ?>?rel=0&modestbranding=1"
						title="<?php echo esc_attr( $title ); ?>"
						frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen
					></iframe>
				</div>
			</figure>
		<?php endif; ?>

	</div>
</section>
