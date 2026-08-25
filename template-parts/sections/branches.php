<?php
/**
 * Section: Our Branches — one card per Estecapelli clinic location.
 *
 * A branch can go live with nothing but a city: the address, phone, opening
 * hours, map link and photo each print only when they are filled in, so the
 * card stays clean while those details are still being collected.
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

<section class="t-branch" aria-labelledby="t-branch-title">
	<div class="shell">

		<header class="t-branch__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-branch__eyebrow">
					<span class="t-branch__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>

			<h2 id="t-branch-title" class="t-branch__title"><?php echo esc_html( $title ); ?></h2>

			<?php if ( $lead ) : ?>
				<p class="t-branch__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="t-branch__grid" style="--cols: <?php echo (int) min( 3, count( $items ) ); ?>">
			<?php foreach ( $items as $item ) :
				if ( empty( $item['city'] ) ) { continue; }

				$image     = $item['image'] ?? array();
				$image_src = ! empty( $image['url'] ) ? $image['url'] : ( $item['image_url'] ?? '' );
				if ( $image_src && ! preg_match( '#^(?:https?:)?//#', $image_src ) ) {
					$image_src = get_template_directory_uri() . '/' . ltrim( $image_src, '/' );
				}

				$points  = ! empty( $item['points'] ) && is_array( $item['points'] ) ? $item['points'] : array();
				$address = trim( (string) ( $item['address'] ?? '' ) );
				$phone   = trim( (string) ( $item['phone'] ?? '' ) );
				$hours   = trim( (string) ( $item['hours'] ?? '' ) );
				$map_url = $item['map_url'] ?? '';
				$has_contact = ( $address || $phone || $hours || $map_url );
				?>
				<li class="t-branch__card">

					<?php if ( $image_src ) : ?>
						<figure class="t-branch__media">
							<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" loading="lazy" decoding="async" />
						</figure>
					<?php endif; ?>

					<div class="t-branch__card-head">
						<?php if ( ! empty( $item['badge'] ) ) : ?>
							<span class="t-branch__badge"><?php echo esc_html( $item['badge'] ); ?></span>
						<?php endif; ?>

						<h3 class="t-branch__city">
							<?php estecapelli_flag( $item['flag'] ?? '', array( 'class' => 't-branch__flag' ) ); ?>
							<span><?php echo esc_html( $item['city'] ); ?></span>
						</h3>

						<?php if ( ! empty( $item['country'] ) ) : ?>
							<span class="t-branch__country"><?php echo esc_html( $item['country'] ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $item['kicker'] ) ) : ?>
							<p class="t-branch__kicker"><?php echo esc_html( $item['kicker'] ); ?></p>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $item['body'] ) ) : ?>
						<div class="t-branch__body"><?php echo wp_kses_post( $item['body'] ); ?></div>
					<?php endif; ?>

					<?php if ( $points ) : ?>
						<ul class="t-branch__points">
							<?php foreach ( $points as $point ) :
								if ( empty( $point['label'] ) ) { continue; }
								?>
								<li class="t-branch__point">
									<span class="t-branch__point-icon" aria-hidden="true">
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
									<span class="t-branch__point-label"><?php echo esc_html( $point['label'] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $has_contact ) : ?>
						<dl class="t-branch__contact">
							<?php if ( $address ) : ?>
								<dt class="t-branch__contact-term"><?php esc_html_e( 'Address', 'estecapelli' ); ?></dt>
								<dd class="t-branch__contact-def"><?php echo nl2br( esc_html( $address ) ); ?></dd>
							<?php endif; ?>
							<?php if ( $phone ) : ?>
								<dt class="t-branch__contact-term"><?php esc_html_e( 'Phone', 'estecapelli' ); ?></dt>
								<dd class="t-branch__contact-def">
									<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
								</dd>
							<?php endif; ?>
							<?php if ( $hours ) : ?>
								<dt class="t-branch__contact-term"><?php esc_html_e( 'Hours', 'estecapelli' ); ?></dt>
								<dd class="t-branch__contact-def"><?php echo esc_html( $hours ); ?></dd>
							<?php endif; ?>
						</dl>

						<?php if ( $map_url ) : ?>
							<a class="t-branch__map" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener">
								<?php estecapelli_icon( 'map-pin', array( 'width' => 16, 'height' => 16 ) ); ?>
								<?php esc_html_e( 'Open in maps', 'estecapelli' ); ?>
							</a>
						<?php endif; ?>
					<?php endif; ?>

				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $footnote || ( $cta_url && $cta_label ) ) : ?>
			<footer class="t-branch__foot">
				<?php if ( $footnote ) : ?>
					<p class="t-branch__footnote"><?php echo esc_html( $footnote ); ?></p>
				<?php endif; ?>
				<?php if ( $cta_url && $cta_label ) : ?>
					<a class="btn btn-primary" href="<?php echo esc_url( $cta_url ); ?>" data-lead-popup>
						<?php echo esc_html( $cta_label ); ?>
					</a>
				<?php endif; ?>
			</footer>
		<?php endif; ?>

	</div>
</section>
