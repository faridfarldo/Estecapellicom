<?php
/**
 * Section: Related Treatments — manual selection OR auto-pull from same category.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow = $section['eyebrow'] ?? '';
$title   = $section['title']   ?? '';
$count   = (int) ( $section['count'] ?? 3 );
$manual  = $section['manual']  ?? array();

$query_args = array(
	'post_type'           => 'treatment',
	'posts_per_page'      => max( 1, $count ),
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
	'post__not_in'        => array( get_the_ID() ),
);

if ( ! empty( $manual ) ) {
	$query_args['post__in'] = array_map( 'intval', (array) $manual );
	$query_args['orderby']  = 'post__in';
} else {
	// Auto: pull from same category as the current treatment.
	$terms = wp_get_post_terms( get_the_ID(), 'treatment_category', array( 'fields' => 'ids' ) );
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'treatment_category',
				'field'    => 'term_id',
				'terms'    => $terms,
			),
		);
	}
	$query_args['orderby'] = 'rand';
}

$related = new WP_Query( $query_args );
if ( ! $related->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>

<section class="t-related">
	<div class="shell">

		<header class="t-related__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-related__eyebrow">
					<span class="t-related__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>
			<h2 class="t-related__title"><?php echo esc_html( $title ); ?></h2>
		</header>

		<ul class="t-related__grid">
			<?php while ( $related->have_posts() ) : $related->the_post();
				$thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
				?>
				<li class="t-related__card">
					<a class="t-related__link" href="<?php the_permalink(); ?>">
						<span class="t-related__media">
							<?php if ( $thumb ) : ?>
								<img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy" decoding="async" />
							<?php else : ?>
								<span class="t-related__media-fallback" aria-hidden="true">
									<?php estecapelli_icon( 'medical-plus', array( 'width' => 36, 'height' => 36 ) ); ?>
								</span>
							<?php endif; ?>
							<span class="t-related__media-wash"></span>
						</span>
						<span class="t-related__body">
							<span class="t-related__name"><?php the_title(); ?></span>
							<?php if ( has_excerpt() ) : ?>
								<span class="t-related__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></span>
							<?php endif; ?>
							<span class="t-related__cta">
								<?php esc_html_e( 'Learn more', 'estecapelli' ); ?>
								<?php estecapelli_icon( 'arrow-right', array( 'width' => 14, 'height' => 14, 'class' => 't-related__arrow' ) ); ?>
							</span>
						</span>
					</a>
				</li>
			<?php endwhile; ?>
		</ul>

	</div>
</section>

<?php wp_reset_postdata(); ?>
