<?php
/**
 * Section: After Care — the post-operative kit and the support around it.
 *
 * Cards describe what the patient goes home with and how each item supports
 * regrowth and long-term maintenance. A photo is optional per card: without
 * one the card falls back to its icon, so the section is complete from the
 * seed alone and photos can be uploaded later without a re-import.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow  = $section['eyebrow']  ?? '';
$title    = $section['title']    ?? '';
$lead     = $section['lead']     ?? '';
$footnote = $section['footnote'] ?? '';
$items    = $section['items']    ?? array();

if ( ! $title || empty( $items ) || ! is_array( $items ) ) { return; }

$cta       = $section['cta'] ?? array();
$cta_url   = $cta['url'] ?? '';
$cta_label = $cta['label'] ?? '';
if ( $cta_url && function_exists( 'estecapelli_localize_theme_url' ) ) {
	$cta_url = estecapelli_localize_theme_url( $cta_url );
}
?>

<section class="t-care" aria-labelledby="t-care-title">
	<div class="shell">

		<header class="t-care__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-care__eyebrow">
					<span class="t-care__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>

			<h2 id="t-care-title" class="t-care__title"><?php echo esc_html( $title ); ?></h2>

			<?php if ( $lead ) : ?>
				<p class="t-care__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="t-care__grid">
			<?php foreach ( $items as $item ) :
				if ( empty( $item['name'] ) ) { continue; }

				$image     = $item['image'] ?? array();
				$image_src = ! empty( $image['url'] ) ? $image['url'] : ( $item['image_url'] ?? '' );
				if ( $image_src && ! preg_match( '#^(?:https?:)?//#', $image_src ) ) {
					$image_src = get_template_directory_uri() . '/' . ltrim( $image_src, '/' );
				}
				?>
				<li class="t-care__card">

					<?php if ( $image_src ) : ?>
						<figure class="t-care__media">
							<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" loading="lazy" decoding="async" />
						</figure>
					<?php else : ?>
						<span class="t-care__icon" aria-hidden="true">
							<?php
							estecapelli_render_item_icon(
								array(
									'icon'      => $item['icon'] ?? 'sparkles',
									'icon_file' => $item['icon_file'] ?? null,
								),
								array( 'width' => 24, 'height' => 24 )
							);
							?>
						</span>
					<?php endif; ?>

					<h3 class="t-care__name"><?php echo esc_html( $item['name'] ); ?></h3>

					<?php if ( ! empty( $item['body'] ) ) : ?>
						<p class="t-care__text"><?php echo esc_html( $item['body'] ); ?></p>
					<?php endif; ?>

				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $footnote || ( $cta_url && $cta_label ) ) : ?>
			<footer class="t-care__foot">
				<?php if ( $footnote ) : ?>
					<p class="t-care__footnote"><?php echo esc_html( $footnote ); ?></p>
				<?php endif; ?>

				<?php if ( $cta_url && $cta_label ) : ?>
					<a class="t-care__cta" href="<?php echo esc_url( $cta_url ); ?>">
						<?php echo esc_html( $cta_label ); ?>
						<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
					</a>
				<?php endif; ?>
			</footer>
		<?php endif; ?>

	</div>
</section>
