<?php
/**
 * Section: Before & After — image pair gallery.
 *
 * Renders a responsive grid of paired cards. Each card shows before and after
 * side by side with a centred divider and caption.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow = $section['eyebrow'] ?? '';
$title   = $section['title']   ?? '';
$lead    = $section['lead']    ?? '';
$pairs   = $section['pairs']   ?? array();
$cta     = $section['cta']     ?? array();

if ( empty( $pairs ) ) { return; }
?>

<section class="t-gallery">
	<div class="shell">

		<header class="t-gallery__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-gallery__eyebrow">
					<span class="t-gallery__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>
			<h2 class="t-gallery__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $lead ) : ?>
				<p class="t-gallery__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="t-gallery__grid">
			<?php foreach ( $pairs as $pair ) :
				$before = $pair['before'] ?? array();
				$after  = $pair['after']  ?? array();
				if ( empty( $before['url'] ) || empty( $after['url'] ) ) { continue; }
				?>
				<li class="t-gallery__card">
					<div class="t-gallery__pair">
						<figure class="t-gallery__side">
							<img src="<?php echo esc_url( $before['url'] ); ?>" alt="<?php echo esc_attr( $before['alt'] ?? 'Before' ); ?>" loading="lazy" decoding="async" />
							<figcaption class="t-gallery__label t-gallery__label--before"><?php esc_html_e( 'Before', 'estecapelli' ); ?></figcaption>
						</figure>
						<span class="t-gallery__divider" aria-hidden="true"></span>
						<figure class="t-gallery__side">
							<img src="<?php echo esc_url( $after['url'] ); ?>" alt="<?php echo esc_attr( $after['alt'] ?? 'After' ); ?>" loading="lazy" decoding="async" />
							<figcaption class="t-gallery__label t-gallery__label--after"><?php esc_html_e( 'After', 'estecapelli' ); ?></figcaption>
						</figure>
					</div>

					<?php if ( ! empty( $pair['caption'] ) || ! empty( $pair['grafts'] ) ) : ?>
						<div class="t-gallery__meta">
							<?php if ( ! empty( $pair['caption'] ) ) : ?>
								<span class="t-gallery__caption"><?php echo esc_html( $pair['caption'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $pair['grafts'] ) ) : ?>
								<span class="t-gallery__grafts"><?php echo esc_html( $pair['grafts'] ); ?>&nbsp;<?php esc_html_e( 'grafts', 'estecapelli' ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( ! empty( $cta['url'] ) ) : ?>
			<div class="t-gallery__cta-wrap">
				<a class="btn btn-primary" href="<?php echo esc_url( $cta['url'] ); ?>">
					<?php echo esc_html( $cta['label'] ?: __( 'See full gallery', 'estecapelli' ) ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
				</a>
			</div>
		<?php endif; ?>

	</div>
</section>
