<?php
/**
 * Section: Candidate — "Who is it for" checklist.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow = $section['eyebrow'] ?? '';
$title   = $section['title']   ?? '';
$body    = $section['body']    ?? '';
$footer  = $section['footer']  ?? '';
$image   = $section['image']   ?? array();
$items   = $section['items']   ?? array();
$style   = ! empty( $section['list_style'] ) ? $section['list_style'] : 'cards';
// Uploaded ACF image wins; otherwise fall back to a plain URL (theme-bundled).
$image_src = ! empty( $image['url'] ) ? $image['url'] : ( $section['image_url'] ?? '' );

if ( ! $title || empty( $items ) ) { return; }
?>

<section class="t-cand<?php echo $image_src ? ' t-cand--with-image' : ''; ?>">
	<div class="shell t-cand__shell">

		<div class="t-cand__copy">
			<?php if ( $eyebrow ) : ?>
				<span class="t-cand__eyebrow">
					<span class="t-cand__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>
			<h2 class="t-cand__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $body ) : ?>
				<p class="t-cand__body"><?php echo esc_html( $body ); ?></p>
			<?php endif; ?>

			<?php if ( 'bullets' === $style ) : ?>
				<ul class="t-cand__bullets">
					<?php foreach ( $items as $item ) : ?>
						<li><?php echo esc_html( $item['label'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<ul class="t-cand__list">
					<?php foreach ( $items as $item ) : ?>
						<li class="t-cand__item">
							<span class="t-cand__check" aria-hidden="true">
								<?php estecapelli_render_item_icon( array( 'icon' => $item['icon'] ?? 'check-circle', 'icon_file' => $item['icon_file'] ?? null ), array( 'width' => 22, 'height' => 22 ) ); ?>
							</span>
							<span class="t-cand__label"><?php echo esc_html( $item['label'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $footer ) : ?>
				<p class="t-cand__footer"><?php echo esc_html( $footer ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $image_src ) : ?>
			<figure class="t-cand__media">
				<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" loading="lazy" decoding="async" />
			</figure>
		<?php endif; ?>

	</div>
</section>
