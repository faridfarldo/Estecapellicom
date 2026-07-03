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
$slides   = ( 'slider' === $mtype && ! empty( $section['slider_images'] ) && is_array( $section['slider_images'] ) ) ? $section['slider_images'] : array();
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

		<?php if ( 'slider' === $mtype && $slides ) : ?>
			<?php
			$slide_urls = array();
			foreach ( $slides as $slide ) {
				if ( ! empty( $slide['url'] ) ) {
					$slide_urls[] = $slide['url'];
				}
			}
			?>
			<figure class="t-intro__media t-intro__media--slider" data-intro-slider>
				<div class="t-intro__slides">
					<?php foreach ( $slides as $s_index => $slide ) : ?>
						<?php if ( empty( $slide['url'] ) ) { continue; } ?>
						<img
							class="t-intro__slide<?php echo 0 === $s_index ? ' is-active' : ''; ?>"
							src="<?php echo esc_url( $slide['url'] ); ?>"
							alt="<?php echo esc_attr( $slide['alt'] ?? $title ); ?>"
							loading="lazy"
							decoding="async"
							data-img-zoom="<?php echo esc_url( $slide['url'] ); ?>"
							data-img-gallery="<?php echo esc_attr( wp_json_encode( $slide_urls ) ); ?>"
						/>
					<?php endforeach; ?>
				</div>
				<?php if ( count( $slides ) > 1 ) : ?>
					<button type="button" class="t-intro__slider-nav t-intro__slider-nav--prev" data-intro-slider-prev aria-label="<?php esc_attr_e( 'Previous photo', 'estecapelli' ); ?>">
						<?php estecapelli_icon( 'chevron-left', array( 'width' => 22, 'height' => 22 ) ); ?>
					</button>
					<button type="button" class="t-intro__slider-nav t-intro__slider-nav--next" data-intro-slider-next aria-label="<?php esc_attr_e( 'Next photo', 'estecapelli' ); ?>">
						<?php estecapelli_icon( 'chevron-right', array( 'width' => 22, 'height' => 22 ) ); ?>
					</button>
					<div class="t-intro__slider-dots" data-intro-slider-dots>
						<?php foreach ( $slides as $d_index => $slide ) : ?>
							<button type="button" class="t-intro__slider-dot<?php echo 0 === $d_index ? ' is-active' : ''; ?>" data-intro-slider-dot="<?php echo (int) $d_index; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Go to photo %d', 'estecapelli' ), $d_index + 1 ) ); ?>"></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</figure>
		<?php elseif ( 'video' === $mtype && $video_id ) : ?>
			<figure class="t-intro__media t-intro__media--video">
				<div class="t-intro__video">
					<iframe
						src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr( $video_id ); ?>?rel=0&modestbranding=1&playsinline=1&controls=0&cc_load_policy=0&iv_load_policy=3&showinfo=0&fs=0&disablekb=1"
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
