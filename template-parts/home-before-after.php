<?php
/**
 * Homepage: Before & After gallery (special).
 *
 * Read-only: pulls the same before/after composites that the Before & After
 * page shows (estecapelli_gallery_grouped — images added to each treatment's
 * gallery section), flattens them and shows a premium carousel on the home
 * page. Does NOT modify any Before & After data, function or page.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$groups = function_exists( 'estecapelli_gallery_grouped' ) ? estecapelli_gallery_grouped() : array();

// Flatten the grouped composites into a flat list of cards.
$cards = array();
foreach ( $groups as $group ) {
	$category = $group['term']->name;
	foreach ( $group['services'] as $sb ) {
		$svc       = $sb['service'];
		$technique = $svc ? get_the_title( $svc ) : '';
		$url       = $svc ? get_permalink( $svc ) : '';
		foreach ( $sb['items'] as $item ) {
			if ( empty( $item['image']['url'] ) ) {
				continue;
			}
			$cards[] = array(
				'image'     => $item['image'],
				'caption'   => trim( (string) ( $item['caption'] ?? '' ) ),
				'grafts'    => trim( (string) ( $item['grafts'] ?? '' ) ),
				'technique' => $technique,
				'category'  => $category,
				'url'       => $url,
			);
		}
	}
}
if ( empty( $cards ) ) {
	return;
}

// Keep the homepage light — show the first dozen, full set lives on the page.
$cards       = array_slice( $cards, 0, 12 );
$multi       = count( $cards ) > 1;
$gallery_url = home_url( '/en/before-after' );
?>

<section class="home-ba" aria-labelledby="home-ba-title">

	<div class="home-ba__bg" aria-hidden="true">
		<span class="home-ba__glow home-ba__glow--a"></span>
		<span class="home-ba__glow home-ba__glow--b"></span>
	</div>

	<div class="shell">

		<header class="home-ba__head">
			<span class="home-ba__eyebrow">
				<span class="home-ba__eyebrow-mark" aria-hidden="true"></span>
				<?php esc_html_e( 'REAL PATIENTS · REAL RESULTS', 'estecapelli' ); ?>
			</span>
			<h2 id="home-ba-title" class="home-ba__title"><?php esc_html_e( 'Before &amp; After', 'estecapelli' ); ?></h2>
			<p class="home-ba__lead"><?php esc_html_e( 'A look at the transformations our patients trust us for — every result planned around their own anatomy.', 'estecapelli' ); ?></p>
		</header>

		<div class="home-ba__carousel ec-carousel" data-carousel>
			<?php if ( $multi ) : ?>
				<button type="button" class="ec-carousel__nav ec-carousel__nav--prev" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous result', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
				</button>
			<?php endif; ?>

			<ul class="home-ba__track ec-carousel__track" data-carousel-track>
				<?php
				foreach ( $cards as $card ) :
					$image = $card['image'];
					$tag   = 'li';
					?>
					<li class="home-ba__card">
						<?php
						$has_link = ! empty( $card['url'] );
						$open     = $has_link ? '<a class="home-ba__frame" href="' . esc_url( $card['url'] ) . '">' : '<div class="home-ba__frame">';
						$close    = $has_link ? '</a>' : '</div>';
						echo $open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
							<figure class="home-ba__media">
								<?php
								$img_id = $image['ID'] ?? $image['id'] ?? 0;
								$alt    = $image['alt'] ?? __( 'Before and after result', 'estecapelli' );
								if ( $img_id ) {
									echo wp_get_attachment_image(
										(int) $img_id,
										'large',
										false,
										array( 'class' => 'home-ba__img', 'alt' => $alt, 'loading' => 'lazy', 'decoding' => 'async' )
									);
								} else {
									?>
									<img class="home-ba__img" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" />
									<?php
								}
								?>
								<span class="home-ba__shade" aria-hidden="true"></span>

								<?php if ( $card['category'] ) : ?>
									<span class="home-ba__cat"><?php echo esc_html( $card['category'] ); ?></span>
								<?php endif; ?>

								<?php if ( $card['technique'] || $card['grafts'] || $card['caption'] ) : ?>
									<figcaption class="home-ba__overlay">
										<?php if ( $card['technique'] ) : ?>
											<span class="home-ba__technique"><?php echo esc_html( $card['technique'] ); ?></span>
										<?php endif; ?>
										<div class="home-ba__facts">
											<?php if ( $card['grafts'] ) : ?>
												<span class="home-ba__chip home-ba__chip--grafts">
													<?php
													/* translators: %s: graft count. */
													printf( esc_html__( '%s grafts', 'estecapelli' ), esc_html( $card['grafts'] ) );
													?>
												</span>
											<?php endif; ?>
											<?php if ( $card['caption'] ) : ?>
												<span class="home-ba__chip"><?php echo esc_html( $card['caption'] ); ?></span>
											<?php endif; ?>
										</div>
									</figcaption>
								<?php endif; ?>
							</figure>
						<?php echo $close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( $multi ) : ?>
				<button type="button" class="ec-carousel__nav ec-carousel__nav--next" data-carousel-next aria-label="<?php esc_attr_e( 'Next result', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'chevron-down', array( 'width' => 22, 'height' => 22 ) ); ?>
				</button>
			<?php endif; ?>
		</div>

		<div class="home-ba__foot">
			<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $gallery_url ); ?>">
				<?php esc_html_e( 'View all results', 'estecapelli' ); ?>
				<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
			</a>
		</div>

	</div>
</section>
