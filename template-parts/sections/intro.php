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

		<?php if ( ! empty( $image['url'] ) ) : ?>
			<figure class="t-intro__media">
				<img
					class="t-intro__img"
					src="<?php echo esc_url( $image['url'] ); ?>"
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
