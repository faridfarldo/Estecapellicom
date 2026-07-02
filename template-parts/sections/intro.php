<?php
/**
 * Section: Intro — image + body (image position left or right).
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

$eyebrow  = $section['eyebrow']        ?? '';
$title    = $section['title']          ?? '';
$body     = $section['body']           ?? '';
$image    = $section['image']          ?? array();
$position = $section['image_position'] ?? 'right';
$cta      = $section['cta']            ?? array();
$mtype    = ! empty( $section['media_type'] ) ? $section['media_type'] : 'image';
$video_id = ! empty( $section['video_url'] ) ? estecapelli_youtube_id( $section['video_url'] ) : '';
// Uploaded ACF image wins; otherwise fall back to a plain URL (theme-bundled).
$image_src = ! empty( $image['url'] ) ? $image['url'] : ( $section['image_url'] ?? '' );

if ( ! $title && ! $body ) {
	return;
}
?>

<section class="t-intro<?php echo 'left' === $position ? ' t-intro--reverse' : ''; ?>" aria-labelledby="t-intro-<?php echo esc_attr( sanitize_title( $title ) ); ?>">
	<div class="shell t-intro__shell">

		<div class="t-intro__copy">
			<?php if ( $eyebrow ) : ?>
				<span class="t-intro__eyebrow">
					<span class="t-intro__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>

			<h2 id="t-intro-<?php echo esc_attr( sanitize_title( $title ) ); ?>" class="t-intro__title">
				<?php echo esc_html( $title ); ?>
			</h2>

			<?php if ( $body ) : ?>
				<div class="t-intro__body">
					<?php echo wp_kses_post( $body ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $cta['url'] ) ) : ?>
				<a class="btn btn-primary t-intro__cta" href="<?php echo esc_url( $cta['url'] ); ?>">
					<?php echo esc_html( $cta['label'] ?: __( 'Learn More', 'estecapelli' ) ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( 'video' === $mtype && $video_id ) : ?>
			<figure class="t-intro__media t-intro__media--video">
				<div class="t-intro__video">
					<iframe
						src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr( $video_id ); ?>?rel=0&modestbranding=1"
						title="<?php echo esc_attr( $title ); ?>"
						loading="lazy"
						frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen
					></iframe>
				</div>
			</figure>
		<?php elseif ( 'image' === $mtype && $image_src ) : ?>
			<figure class="t-intro__media">
				<img
					class="t-intro__img"
					src="<?php echo esc_url( $image_src ); ?>"
					alt="<?php echo esc_attr( $image['alt'] ?? $title ); ?>"
					width="<?php echo (int) ( $image['width']  ?? 1200 ); ?>"
					height="<?php echo (int) ( $image['height'] ?? 800  ); ?>"
					loading="lazy"
					decoding="async"
				/>
			</figure>
		<?php endif; ?>

	</div>
</section>
