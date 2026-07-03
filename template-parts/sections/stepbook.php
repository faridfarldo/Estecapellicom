<?php
/**
 * Section: Step Book — a booklet-style, one-page-at-a-time procedure walk-through.
 *
 * A contents rail (left on desktop / chips on mobile) drives a swipeable deck
 * of full-width "pages", each holding the rich body for one step. Designed for
 * long, detailed step content that would feel cramped in the compact
 * `steps` timeline cards.
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

$eyebrow = $section['eyebrow'] ?? '';
$title   = $section['title']   ?? '';
$lead    = $section['lead']    ?? '';
$items   = $section['items']   ?? array();

if ( empty( $items ) ) {
	return;
}

$total = count( $items );
$uid   = 'book-' . sanitize_title( $title ) . '-' . wp_rand( 100, 999 );
?>

<section class="t-book" aria-labelledby="<?php echo esc_attr( $uid ); ?>-title">
	<div class="shell">

		<header class="t-book__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-book__eyebrow">
					<span class="t-book__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>
			<h2 id="<?php echo esc_attr( $uid ); ?>-title" class="t-book__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $lead ) : ?>
				<p class="t-book__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<div class="t-book__frame" data-stepbook>

			<nav class="t-book__rail" aria-label="<?php esc_attr_e( 'Steps', 'estecapelli' ); ?>">
				<span class="t-book__rail-title"><?php esc_html_e( 'Contents', 'estecapelli' ); ?></span>
				<ol class="t-book__tabs">
					<?php foreach ( $items as $i => $step ) :
						$num = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT );
						?>
						<li>
							<button type="button"
								class="t-book__tab<?php echo 0 === $i ? ' is-active' : ''; ?>"
								data-stepbook-tab="<?php echo (int) $i; ?>"
								<?php echo 0 === $i ? 'aria-current="step"' : ''; ?>>
								<span class="t-book__tab-num" aria-hidden="true"><?php echo esc_html( $num ); ?></span>
								<span class="t-book__tab-label"><?php echo esc_html( $step['title'] ?? '' ); ?></span>
							</button>
						</li>
					<?php endforeach; ?>
				</ol>
			</nav>

			<div class="t-book__pages">

				<ol class="t-book__track" data-stepbook-track>
					<?php foreach ( $items as $i => $step ) :
						$num    = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT );
						$icon   = $step['icon']    ?? '';
						$kick   = $step['eyebrow'] ?? '';
						$stitle = $step['title']   ?? '';
						$body   = $step['body']    ?? '';
						$image  = $step['image']   ?? array();
						$vid    = ! empty( $step['video_url'] ) ? estecapelli_youtube_id( $step['video_url'] ) : '';
						?>
						<li class="t-book__page<?php echo 0 === $i ? ' is-active' : ''; ?>" aria-roledescription="<?php esc_attr_e( 'step', 'estecapelli' ); ?>">
							<div class="t-book__page-copy">
								<span class="t-book__watermark" aria-hidden="true"><?php echo esc_html( $num ); ?></span>
								<div class="t-book__page-head">
									<?php if ( $icon ) : ?>
										<span class="t-book__page-icon" aria-hidden="true">
											<?php estecapelli_icon( $icon, array( 'width' => 24, 'height' => 24 ) ); ?>
										</span>
									<?php endif; ?>
									<div class="t-book__page-heading">
										<?php if ( $kick ) : ?>
											<span class="t-book__page-eyebrow"><?php echo esc_html( $kick ); ?></span>
										<?php endif; ?>
										<h3 class="t-book__page-title"><?php echo esc_html( $stitle ); ?></h3>
									</div>
								</div>
								<?php if ( $body ) : ?>
									<div class="t-book__page-body"><?php echo wp_kses_post( $body ); ?></div>
								<?php endif; ?>
							</div>

							<?php if ( $vid ) : ?>
								<figure class="t-book__page-media t-book__page-media--video">
									<iframe
										src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr( $vid ); ?>?rel=0&modestbranding=1&playsinline=1&controls=0&cc_load_policy=0&iv_load_policy=3&showinfo=0&fs=0&disablekb=1"
										title="<?php echo esc_attr( $stitle ); ?>"
										loading="lazy" frameborder="0"
										allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
										allowfullscreen
									></iframe>
								</figure>
							<?php elseif ( ! empty( $image['url'] ) ) : ?>
								<figure class="t-book__page-media">
									<?php
									$img_id = $image['ID'] ?? $image['id'] ?? 0;
									if ( $img_id ) {
										// Responsive srcset — the browser downloads the size it needs.
										echo wp_get_attachment_image(
											(int) $img_id,
											'large',
											false,
											array( 'class' => 't-book__page-img', 'alt' => $image['alt'] ?? $stitle, 'loading' => 'lazy', 'decoding' => 'async' )
										);
									} else {
										?>
										<img
											class="t-book__page-img"
											src="<?php echo esc_url( $image['url'] ); ?>"
											alt="<?php echo esc_attr( $image['alt'] ?? $stitle ); ?>"
											width="<?php echo (int) ( $image['width']  ?? 1200 ); ?>"
											height="<?php echo (int) ( $image['height'] ?? 750 ); ?>"
											loading="lazy" decoding="async"
										/>
										<?php
									}
									?>
								</figure>
							<?php else : ?>
								<figure class="t-book__page-media t-book__page-media--empty" aria-hidden="true">
									<?php if ( $icon ) : ?>
										<?php estecapelli_icon( $icon, array( 'width' => 56, 'height' => 56 ) ); ?>
									<?php endif; ?>
									<span class="t-book__page-media-num"><?php echo esc_html( $num ); ?></span>
								</figure>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>

				<footer class="t-book__controls">
					<button type="button" class="t-book__nav" data-stepbook-prev aria-label="<?php esc_attr_e( 'Previous step', 'estecapelli' ); ?>">
						<?php estecapelli_icon( 'chevron-down', array( 'width' => 20, 'height' => 20 ) ); ?>
					</button>

					<div class="t-book__meter">
						<span class="t-book__counter">
							<span data-stepbook-current>1</span>&thinsp;/&thinsp;<?php echo esc_html( (string) $total ); ?>
						</span>
						<span class="t-book__progress" aria-hidden="true">
							<span class="t-book__progress-fill" data-stepbook-progress style="width: <?php echo esc_attr( (string) round( 100 / max( 1, $total ) ) ); ?>%;"></span>
						</span>
					</div>

					<button type="button" class="t-book__nav" data-stepbook-next aria-label="<?php esc_attr_e( 'Next step', 'estecapelli' ); ?>">
						<?php estecapelli_icon( 'chevron-down', array( 'width' => 20, 'height' => 20 ) ); ?>
					</button>
				</footer>

			</div>

		</div>

	</div>
</section>
