<?php
/**
 * Template Name: Careers
 *
 * The Careers index — every open role at the clinic, one card each, linking to
 * the full posting. Routed here for the page with slug "careers" via the
 * template router in inc/leads.php.
 *
 * Nothing on this page is hard-coded copy. The heading is the page title, the
 * intro is the page's own content, and the eyebrow and the "no openings yet"
 * panel come from the short Careers Page form beside the editor — all of it
 * editable in wp-admin, and all of it translatable per language through WPML
 * like any other page. The theme strings below are only the fallbacks that keep
 * the page presentable before anyone has filled it in.
 *
 * The roles themselves are `job` posts (see inc/careers.php), so the list needs
 * no editing at all: publish a role and it appears, unpublish it and it goes.
 *
 * @package Estecapelli
 */

get_header();

$jobs = estecapelli_open_jobs();

// Every one of these falls back to the theme string when the field is empty, so
// a page created with nothing but a title still renders properly.
$eyebrow     = estecapelli_careers_field( 'careers_eyebrow', __( 'Careers at Estecapelli', 'estecapelli' ) );
$empty_title = estecapelli_careers_field( 'careers_empty_title', __( 'No open positions right now', 'estecapelli' ) );
$empty_text  = estecapelli_careers_field( 'careers_empty_text', __( 'We are not recruiting at the moment, but we are always glad to hear from good people. Send your CV and we will keep it on file for the next opening.', 'estecapelli' ) );
$empty_cta   = estecapelli_careers_field( 'careers_empty_cta', __( 'Send your CV', 'estecapelli' ) );

// The heading and the intro are the page's own title and editor content, run
// through the_content so blocks, shortcodes and WPML behave exactly as they do
// on any other page.
$title = '';
$intro = '';
while ( have_posts() ) :
	the_post();
	$title = get_the_title();
	$intro = apply_filters( 'the_content', get_the_content() );
endwhile;
?>

<div class="careers-page">

	<header class="careers-hero">
		<div class="shell careers-hero__shell">
			<?php if ( $eyebrow ) : ?>
				<span class="careers-hero__eyebrow">
					<span class="careers-hero__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>

			<h1 class="careers-hero__title"><?php echo esc_html( $title ); ?></h1>

			<?php if ( trim( wp_strip_all_tags( $intro ) ) ) : ?>
				<div class="careers-hero__lead"><?php echo wp_kses_post( $intro ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $jobs ) ) : ?>
				<p class="careers-hero__count">
					<?php
					printf(
						esc_html(
							/* translators: %d: number of open roles */
							_n( '%d open position', '%d open positions', count( $jobs ), 'estecapelli' )
						),
						count( $jobs )
					);
					?>
				</p>
			<?php endif; ?>
		</div>
	</header>

	<div class="shell careers-body">
		<?php if ( ! empty( $jobs ) ) : ?>

			<div class="careers-list">
				<?php
				foreach ( $jobs as $job ) :
					$meta    = estecapelli_job_meta( $job->ID );
					$summary = function_exists( 'get_field' ) ? trim( (string) get_field( 'summary', $job->ID ) ) : '';
					if ( '' === $summary ) {
						$summary = wp_trim_words( get_the_excerpt( $job ) ?: wp_strip_all_tags( $job->post_content ), 28, '…' );
					}
					$open = estecapelli_job_is_open( $job->ID );
					?>
					<article class="job-card<?php echo $open ? '' : ' job-card--closed'; ?>">
						<div class="job-card__body">
							<h2 class="job-card__title">
								<a href="<?php echo esc_url( get_permalink( $job ) ); ?>"><?php echo esc_html( get_the_title( $job ) ); ?></a>
							</h2>

							<?php if ( $meta ) : ?>
								<ul class="job-card__meta">
									<?php foreach ( $meta as $item ) : ?>
										<li class="job-chip">
											<?php estecapelli_icon( $item['icon'], array( 'width' => 14, 'height' => 14 ) ); ?>
											<span><?php echo esc_html( $item['value'] ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<?php if ( $summary ) : ?>
								<p class="job-card__summary"><?php echo esc_html( $summary ); ?></p>
							<?php endif; ?>
						</div>

						<div class="job-card__aside">
							<?php if ( ! $open ) : ?>
								<span class="job-card__closed"><?php esc_html_e( 'Applications closed', 'estecapelli' ); ?></span>
							<?php endif; ?>
							<a class="btn btn-primary job-card__cta" href="<?php echo esc_url( get_permalink( $job ) ); ?>">
								<?php esc_html_e( 'View role', 'estecapelli' ); ?>
								<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
							</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

		<?php else : ?>
			<div class="careers-empty">
				<span class="careers-empty__icon" aria-hidden="true"><?php estecapelli_icon( 'clipboard', array( 'width' => 36, 'height' => 36 ) ); ?></span>
				<h2 class="careers-empty__title"><?php echo esc_html( $empty_title ); ?></h2>
				<?php if ( $empty_text ) : ?>
					<p><?php echo esc_html( $empty_text ); ?></p>
				<?php endif; ?>
				<a class="btn btn-primary" href="<?php echo esc_url( 'mailto:' . estecapelli_careers_inbox() ); ?>">
					<?php echo esc_html( $empty_cta ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>

</div>

<?php
get_footer();
