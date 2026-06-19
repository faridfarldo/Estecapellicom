<?php
/**
 * Template Name: Before & After Gallery
 *
 * Aggregates the before/after composites from every treatment's "gallery"
 * section, grouped by category (main group, e.g. Hair Transplant) then by
 * service (technique). Whatever an editor adds to a treatment's gallery
 * section shows up here automatically. Data: estecapelli_gallery_grouped().
 *
 * @package Estecapelli
 */

get_header();

$groups = estecapelli_gallery_grouped();

// Intro text: the page's own content, falling back to the lead of its first
// hero section (set via the seed / page builder) so the header isn't bare.
$ba_intro = '';
if ( function_exists( 'get_field' ) ) {
	$ba_sections = get_field( 'page_sections', get_the_ID() );
	if ( ! empty( $ba_sections ) && is_array( $ba_sections ) ) {
		foreach ( $ba_sections as $ba_section ) {
			if ( 'hero' === ( $ba_section['acf_fc_layout'] ?? '' ) && ! empty( $ba_section['lead'] ) ) {
				$ba_intro = $ba_section['lead'];
				break;
			}
		}
	}
}
?>

<div class="ba-page">

	<header class="ba-page__hero">
		<div class="shell">
			<span class="t-ba__eyebrow">
				<span class="t-ba__eyebrow-mark" aria-hidden="true"></span>
				<?php esc_html_e( 'Real Results', 'estecapelli' ); ?>
			</span>
			<h1 class="ba-page__title"><?php the_title(); ?></h1>
			<?php
			$ba_content = '';
			while ( have_posts() ) :
				the_post();
				$ba_content = trim( wp_strip_all_tags( get_the_content() ) );
			endwhile;

			if ( $ba_content ) :
				?>
				<div class="ba-page__intro"><?php echo wpautop( wp_kses_post( get_the_content() ) ); ?></div>
			<?php elseif ( $ba_intro ) : ?>
				<p class="ba-page__intro"><?php echo esc_html( $ba_intro ); ?></p>
			<?php endif; ?>
		</div>
	</header>

	<?php
	// Keep only categories whose services actually have images.
	$renderable = array();
	foreach ( $groups as $group ) {
		$services = array();
		foreach ( $group['services'] as $sb ) {
			if ( $sb['service'] && ! empty( $sb['items'] ) ) {
				$services[] = $sb;
			}
		}
		if ( ! empty( $services ) ) {
			$renderable[] = array( 'group' => $group, 'services' => $services );
		}
	}
	$multi_cat = count( $renderable ) > 1;
	?>

	<?php if ( empty( $renderable ) ) : ?>
		<div class="shell">
			<p class="ba-page__empty"><?php esc_html_e( 'Results will appear here soon.', 'estecapelli' ); ?></p>
		</div>
	<?php else : ?>
		<div class="ba-cats-wrap" data-cats>

			<?php if ( $multi_cat ) : ?>
				<div class="shell">
					<div class="ba-cats" role="tablist" aria-label="<?php esc_attr_e( 'Treatment categories', 'estecapelli' ); ?>" data-cats-tablist>
						<?php foreach ( $renderable as $ci => $r ) :
							$cat_slug = $r['group']['term']->slug;
							?>
							<button
								type="button"
								id="ba-cat-tab-<?php echo esc_attr( $cat_slug ); ?>"
								class="ba-cat-tab"
								role="tab"
								aria-selected="<?php echo 0 === $ci ? 'true' : 'false'; ?>"
								aria-controls="ba-cat-panel-<?php echo esc_attr( $cat_slug ); ?>"
								tabindex="<?php echo 0 === $ci ? '0' : '-1'; ?>"
								data-cats-tab
							>
								<?php echo esc_html( $r['group']['term']->name ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php
			foreach ( $renderable as $ci => $r ) :
				$group    = $r['group'];
				$services = $r['services'];
				$cat_slug = $group['term']->slug;
				$multi    = count( $services ) > 1;
				?>
				<div
					id="ba-cat-panel-<?php echo esc_attr( $cat_slug ); ?>"
					class="ba-cat-panel"
					<?php if ( $multi_cat ) : ?>
						role="tabpanel"
						aria-labelledby="ba-cat-tab-<?php echo esc_attr( $cat_slug ); ?>"
						data-cats-panel
						<?php echo 0 === $ci ? '' : 'hidden'; ?>
					<?php endif; ?>
				>
				<section class="ba-group">
					<div class="shell">
						<?php if ( ! $multi_cat ) : ?>
							<h2 class="ba-group__title"><?php echo esc_html( $group['term']->name ); ?></h2>
						<?php endif; ?>

						<?php if ( $multi ) : ?>
						<div class="ba-tabs" role="tablist" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: category name */ __( '%s services', 'estecapelli' ), $group['term']->name ) ); ?>" data-services-tablist>
							<?php foreach ( $services as $i => $sb ) :
								$svc = $sb['service'];
								?>
								<button
									type="button"
									id="ba-tab-<?php echo esc_attr( $cat_slug . '-' . $svc->ID ); ?>"
									class="ba-tab"
									role="tab"
									aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
									aria-controls="ba-panel-<?php echo esc_attr( $cat_slug . '-' . $svc->ID ); ?>"
									tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>"
									data-services-tab
								>
									<?php echo esc_html( get_the_title( $svc ) ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php foreach ( $services as $i => $sb ) :
						$svc   = $sb['service'];
						$items = $sb['items'];
						?>
						<div
							id="ba-panel-<?php echo esc_attr( $cat_slug . '-' . $svc->ID ); ?>"
							class="ba-panel"
							<?php if ( $multi ) : ?>
								role="tabpanel"
								aria-labelledby="ba-tab-<?php echo esc_attr( $cat_slug . '-' . $svc->ID ); ?>"
								data-services-panel
								<?php echo 0 === $i ? '' : 'hidden'; ?>
							<?php endif; ?>
						>
							<div class="ba-technique__head">
								<h3 class="ba-technique__title"><?php echo esc_html( get_the_title( $svc ) ); ?></h3>
								<a class="ba-technique__link" href="<?php echo esc_url( get_permalink( $svc ) ); ?>">
									<?php esc_html_e( 'View treatment', 'estecapelli' ); ?>
									<?php estecapelli_icon( 'arrow-right', array( 'width' => 15, 'height' => 15 ) ); ?>
								</a>
							</div>

							<ul class="t-gallery__grid">
								<?php foreach ( $items as $item ) :
									$image = $item['image'] ?? array();
									if ( empty( $image['url'] ) ) { continue; }
									?>
									<li class="t-gallery__card">
										<figure class="t-gallery__media">
											<img
												src="<?php echo esc_url( $image['url'] ); ?>"
												alt="<?php echo esc_attr( $image['alt'] ?: __( 'Before and after result', 'estecapelli' ) ); ?>"
												loading="lazy"
												decoding="async"
												width="<?php echo (int) ( $image['width']  ?? 1080 ); ?>"
												height="<?php echo (int) ( $image['height'] ?? 1080 ); ?>"
											/>
										</figure>

										<?php if ( ! empty( $item['caption'] ) || ! empty( $item['grafts'] ) ) : ?>
											<div class="t-gallery__meta">
												<?php if ( ! empty( $item['caption'] ) ) : ?>
													<span class="t-gallery__caption"><?php echo esc_html( $item['caption'] ); ?></span>
												<?php endif; ?>
												<?php if ( ! empty( $item['grafts'] ) ) : ?>
													<span class="t-gallery__grafts"><?php echo esc_html( $item['grafts'] ); ?>&nbsp;<?php esc_html_e( 'grafts', 'estecapelli' ); ?></span>
												<?php endif; ?>
											</div>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
				</div>
			<?php endforeach; ?>

		</div>
	<?php endif; ?>

</div>

<?php
get_footer();
