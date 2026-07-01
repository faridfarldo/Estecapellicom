<?php
/**
 * Homepage: Hair-transplant Before & After gallery.
 *
 * Tabs = hair-transplant techniques (services). Each technique shows an
 * auto-scrolling marquee of its before/after composites; clicking an image
 * pauses the marquee and opens a viewer (large image + thumbnail strip).
 *
 * Read-only: reuses estecapelli_gallery_grouped() (images from each
 * treatment's gallery section). Does NOT modify any Before & After data,
 * function or page. Plastic surgery / dental are intentionally excluded.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$groups = function_exists( 'estecapelli_gallery_grouped' ) ? estecapelli_gallery_grouped() : array();

// Only the hair-transplant category feeds this section.
$ht_group = null;
foreach ( $groups as $group ) {
	if ( isset( $group['term'] ) && 'hair-transplant' === $group['term']->slug ) {
		$ht_group = $group;
		break;
	}
}
if ( ! $ht_group ) {
	return;
}

// Each service in the category becomes a technique tab carrying its images.
$techniques = array();
foreach ( $ht_group['services'] as $sb ) {
	$svc = $sb['service'];
	if ( ! $svc ) {
		continue;
	}

	$images = array();
	foreach ( $sb['items'] as $item ) {
		if ( empty( $item['image']['url'] ) ) {
			continue;
		}
		$im      = $item['image'];
		$sizes   = $im['sizes'] ?? array();
		$images[] = array(
			'thumb' => $sizes['medium'] ?? ( $sizes['large'] ?? $im['url'] ),
			'full'  => $sizes['large'] ?? $im['url'],
			'alt'   => $im['alt'] ?: get_the_title( $svc ),
		);
	}
	if ( empty( $images ) ) {
		continue;
	}

	$techniques[] = array(
		'id'     => $svc->ID,
		'title'  => get_the_title( $svc ),
		'url'    => get_permalink( $svc ),
		'images' => $images,
	);
}
if ( empty( $techniques ) ) {
	return;
}

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
				<?php esc_html_e( 'Real Patients · Real Results', 'estecapelli' ); ?>
			</span>
			<h2 id="home-ba-title" class="home-ba__title"><?php esc_html_e( 'Before &amp; After', 'estecapelli' ); ?></h2>
			<p class="home-ba__lead"><?php esc_html_e( 'Choose a hair-transplant technique to browse real patient results.', 'estecapelli' ); ?></p>
		</header>

		<div class="home-ba__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Hair transplant techniques', 'estecapelli' ); ?>" data-services-tablist>
			<?php foreach ( $techniques as $i => $tech ) :
				$is_first = ( 0 === $i );
				?>
				<button
					type="button"
					id="home-ba-tab-<?php echo (int) $tech['id']; ?>"
					class="home-ba__tab"
					role="tab"
					aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>"
					aria-controls="home-ba-panel-<?php echo (int) $tech['id']; ?>"
					tabindex="<?php echo $is_first ? '0' : '-1'; ?>"
					data-services-tab
				>
					<?php echo esc_html( $tech['title'] ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php foreach ( $techniques as $i => $tech ) :
			$is_first = ( 0 === $i );
			?>
			<div
				id="home-ba-panel-<?php echo (int) $tech['id']; ?>"
				class="home-ba__panel"
				role="tabpanel"
				aria-labelledby="home-ba-tab-<?php echo (int) $tech['id']; ?>"
				data-services-panel
				data-hba
				<?php echo $is_first ? '' : 'hidden'; ?>
			>

				<!-- Moving tableau wall — click any photo to open the gallery viewer.
				     Images are emitted twice so the scroll loops seamlessly. -->
				<?php
				// Widen one half so the full-bleed wall always covers the viewport,
				// then emit two identical halves for a seamless -50%→0 loop.
				$half_imgs = $tech['images'];
				while ( count( $half_imgs ) < 12 ) {
					$half_imgs = array_merge( $half_imgs, $tech['images'] );
				}
				?>
				<div class="home-ba__wall" data-hba-board>
					<ul class="home-ba__walltrack">
						<?php for ( $dup = 0; $dup < 2; $dup++ ) : ?>
							<?php foreach ( $half_imgs as $idx => $img ) :
								// Every 5th tile is a big 2x2 block; the pattern is keyed to the
								// real index so both copies match and the loop stays seamless.
								$is_big = ( 0 === $idx % 5 );
								?>
								<li class="home-ba__cell<?php echo $is_big ? ' home-ba__cell--big' : ''; ?>" <?php echo 1 === $dup ? 'aria-hidden="true"' : ''; ?>>
									<button
										type="button"
										class="home-ba__open"
										data-hba-open
										data-hba-src="<?php echo esc_url( $img['full'] ); ?>"
										data-hba-alt="<?php echo esc_attr( $img['alt'] ); ?>"
										<?php echo 1 === $dup ? 'tabindex="-1" aria-hidden="true"' : ''; ?>
										aria-label="<?php esc_attr_e( 'Open the gallery', 'estecapelli' ); ?>"
									>
										<img src="<?php echo esc_url( $img['thumb'] ); ?>" alt="" loading="lazy" decoding="async" />
									</button>
								</li>
							<?php endforeach; ?>
						<?php endfor; ?>
					</ul>
					<span class="home-ba__hint" aria-hidden="true">
						<?php estecapelli_icon( 'image', array( 'width' => 16, 'height' => 16 ) ); ?>
						<?php esc_html_e( 'Click any photo to open the gallery', 'estecapelli' ); ?>
					</span>
				</div>

				<!-- Viewer: large image + thumbnail strip -->
				<div class="home-ba__viewer" data-hba-viewer hidden>
					<button type="button" class="home-ba__back" data-hba-back>
						<?php estecapelli_icon( 'chevron-left', array( 'width' => 16, 'height' => 16 ) ); ?>
						<?php esc_html_e( 'Back to gallery', 'estecapelli' ); ?>
					</button>

					<figure class="home-ba__stage">
						<img class="home-ba__main" data-hba-main src="" alt="" decoding="async" />
					</figure>

					<ul class="home-ba__thumbs">
						<?php foreach ( $tech['images'] as $idx => $img ) : ?>
							<li>
								<button
									type="button"
									class="home-ba__thumb"
									data-hba-thumb
									data-hba-src="<?php echo esc_url( $img['full'] ); ?>"
									data-hba-alt="<?php echo esc_attr( $img['alt'] ); ?>"
									aria-label="<?php
										/* translators: %d: image number. */
										printf( esc_attr__( 'View result %d', 'estecapelli' ), (int) $idx + 1 );
									?>"
								>
									<img src="<?php echo esc_url( $img['thumb'] ); ?>" alt="" loading="lazy" decoding="async" />
								</button>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

			</div>
		<?php endforeach; ?>

		<div class="home-ba__foot">
			<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $gallery_url ); ?>">
				<?php esc_html_e( 'View all results', 'estecapelli' ); ?>
				<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
			</a>
		</div>

	</div>
</section>
