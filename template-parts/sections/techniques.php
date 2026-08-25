<?php
/**
 * Section: Signature Techniques — the methods that only exist at Estecapelli.
 *
 * Each card carries a badge, the technique name, a highlight figure, the
 * explanatory body and a checklist of advantages, plus a link through to the
 * technique's own treatment page. Copy is authored in the seed / translation
 * JSON; the card image is optional and may be uploaded per post.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow = $section['eyebrow'] ?? '';
$title   = $section['title']   ?? '';
$lead    = $section['lead']    ?? '';
$items   = $section['items']   ?? array();

if ( ! $title || empty( $items ) || ! is_array( $items ) ) { return; }
?>

<section class="t-tech" aria-labelledby="t-tech-title">
	<div class="t-tech__bg" aria-hidden="true">
		<span class="t-tech__glow t-tech__glow--a"></span>
		<span class="t-tech__glow t-tech__glow--b"></span>
	</div>

	<div class="shell">

		<header class="t-tech__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-tech__eyebrow">
					<span class="t-tech__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>

			<h2 id="t-tech-title" class="t-tech__title"><?php echo esc_html( $title ); ?></h2>

			<?php if ( $lead ) : ?>
				<p class="t-tech__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="t-tech__grid" style="--cols: <?php echo (int) min( 3, count( $items ) ); ?>">
			<?php foreach ( $items as $item ) :
				if ( empty( $item['name'] ) ) { continue; }

				$image     = $item['image'] ?? array();
				$image_src = ! empty( $image['url'] ) ? $image['url'] : ( $item['image_url'] ?? '' );
				// A theme-bundled path is stored relative to the theme root.
				if ( $image_src && ! preg_match( '#^(?:https?:)?//#', $image_src ) ) {
					$image_src = get_template_directory_uri() . '/' . ltrim( $image_src, '/' );
				}

				$cta       = $item['cta'] ?? array();
				$cta_url   = $cta['url'] ?? '';
				$cta_label = $cta['label'] ?? '';
				if ( $cta_url && function_exists( 'estecapelli_localize_theme_url' ) ) {
					$cta_url = estecapelli_localize_theme_url( $cta_url );
				}
				$points = ! empty( $item['points'] ) && is_array( $item['points'] ) ? $item['points'] : array();
				?>
				<li class="t-tech__card">

					<?php if ( $image_src ) : ?>
						<figure class="t-tech__media">
							<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" loading="lazy" decoding="async" />
						</figure>
					<?php endif; ?>

					<div class="t-tech__card-head">
						<?php if ( ! empty( $item['badge'] ) ) : ?>
							<span class="t-tech__badge"><?php echo esc_html( $item['badge'] ); ?></span>
						<?php endif; ?>

						<h3 class="t-tech__name"><?php echo esc_html( $item['name'] ); ?></h3>

						<?php if ( ! empty( $item['tagline'] ) ) : ?>
							<p class="t-tech__tagline"><?php echo esc_html( $item['tagline'] ); ?></p>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $item['stat_value'] ) ) : ?>
						<div class="t-tech__stat">
							<span class="t-tech__stat-value"><?php echo esc_html( $item['stat_value'] ); ?></span>
							<?php if ( ! empty( $item['stat_label'] ) ) : ?>
								<span class="t-tech__stat-label"><?php echo esc_html( $item['stat_label'] ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $item['body'] ) ) : ?>
						<div class="t-tech__body"><?php echo wp_kses_post( $item['body'] ); ?></div>
					<?php endif; ?>

					<?php if ( $points ) : ?>
						<ul class="t-tech__points">
							<?php foreach ( $points as $point ) :
								if ( empty( $point['label'] ) ) { continue; }
								?>
								<li class="t-tech__point">
									<span class="t-tech__point-icon" aria-hidden="true">
										<?php
										estecapelli_render_item_icon(
											array(
												'icon'      => $point['icon'] ?? 'check-circle',
												'icon_file' => $point['icon_file'] ?? null,
											),
											array( 'width' => 18, 'height' => 18 )
										);
										?>
									</span>
									<span class="t-tech__point-label"><?php echo esc_html( $point['label'] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $cta_url && $cta_label ) : ?>
						<a class="t-tech__cta" href="<?php echo esc_url( $cta_url ); ?>">
							<?php echo esc_html( $cta_label ); ?>
							<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
						</a>
					<?php endif; ?>

				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
