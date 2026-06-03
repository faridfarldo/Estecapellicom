<?php
/**
 * Homepage: "Latest from the Journal" — magazine spread of recent blog posts.
 *
 * Pulls the four most recent posts from the default WordPress post type and
 * renders them as one big featured card on the left plus a stacked list on
 * the right. Any time the editor publishes a new post, this section updates.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$latest = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 4,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

$total = (int) $latest->post_count;
?>

<section class="posts" aria-labelledby="posts-title">

	<div class="posts__bg" aria-hidden="true">
		<span class="posts__bg-glow"></span>
	</div>

	<div class="shell">

		<header class="posts__head">
			<div class="posts__head-text">
				<span class="posts__eyebrow">
					<span class="posts__eyebrow-mark" aria-hidden="true"></span>
					<?php esc_html_e( 'From the Journal', 'estecapelli' ); ?>
				</span>

				<h2 id="posts-title" class="posts__title">
					<?php esc_html_e( 'Research, results and recovery — written by our team.', 'estecapelli' ); ?>
				</h2>
			</div>

			<a class="posts__head-cta" href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ?: home_url( '/en/blog' ) ); ?>">
				<?php esc_html_e( 'View all articles', 'estecapelli' ); ?>
				<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
			</a>
		</header>

		<?php if ( $total === 0 ) : ?>

			<div class="posts__empty">
				<span class="posts__empty-icon" aria-hidden="true">
					<?php estecapelli_icon( 'book-open', array( 'width' => 32, 'height' => 32 ) ); ?>
				</span>
				<p class="posts__empty-text">
					<?php esc_html_e( 'No articles have been published yet. New posts will appear here as soon as your team publishes them.', 'estecapelli' ); ?>
				</p>
				<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>">
					<?php esc_html_e( 'Write the first article', 'estecapelli' ); ?>
				</a>
			</div>

		<?php else : ?>

			<div class="posts__spread">

				<?php
				$latest->the_post();
				$cats = get_the_category();
				$cat  = ! empty( $cats ) ? $cats[0] : null;
				?>
				<article class="posts__feature">
					<a class="posts__feature-link" href="<?php the_permalink(); ?>">

						<div class="posts__feature-media">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large', array( 'class' => 'posts__feature-img', 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); ?>
							<?php else : ?>
								<span class="posts__feature-fallback" aria-hidden="true">
									<?php estecapelli_icon( 'book-open', array( 'width' => 56, 'height' => 56 ) ); ?>
								</span>
							<?php endif; ?>
							<span class="posts__feature-wash"></span>

							<?php if ( $cat ) : ?>
								<span class="posts__feature-cat">
									<?php estecapelli_icon( 'tag', array( 'width' => 12, 'height' => 12 ) ); ?>
									<?php echo esc_html( $cat->name ); ?>
								</span>
							<?php endif; ?>
						</div>

						<div class="posts__feature-body">
							<div class="posts__feature-meta">
								<span class="posts__meta-item">
									<?php estecapelli_icon( 'calendar', array( 'width' => 14, 'height' => 14, 'class' => 'posts__meta-icon' ) ); ?>
									<?php echo esc_html( get_the_date() ); ?>
								</span>
								<span class="posts__meta-sep" aria-hidden="true">·</span>
								<span class="posts__meta-item">
									<?php echo esc_html( sprintf( _n( '%d min read', '%d min read', max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) ), 'estecapelli' ), max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) ) ) ); ?>
								</span>
							</div>

							<h3 class="posts__feature-title"><?php the_title(); ?></h3>

							<p class="posts__feature-excerpt">
								<?php
								$excerpt = get_the_excerpt();
								echo esc_html( wp_trim_words( $excerpt, 38, '…' ) );
								?>
							</p>

							<span class="posts__feature-cta">
								<?php esc_html_e( 'Read the article', 'estecapelli' ); ?>
								<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16, 'class' => 'posts__feature-arrow' ) ); ?>
							</span>
						</div>
					</a>
				</article>

				<?php if ( $latest->post_count > 1 ) : ?>
					<ul class="posts__list">
						<?php while ( $latest->have_posts() ) : $latest->the_post();
							$cats = get_the_category();
							$cat  = ! empty( $cats ) ? $cats[0] : null;
							?>
							<li class="posts__list-item">
								<a class="posts__list-link" href="<?php the_permalink(); ?>">

									<span class="posts__list-thumb">
										<?php if ( has_post_thumbnail() ) : ?>
											<?php the_post_thumbnail( 'medium', array( 'class' => 'posts__list-img', 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); ?>
										<?php else : ?>
											<span class="posts__list-fallback" aria-hidden="true">
												<?php estecapelli_icon( 'book-open', array( 'width' => 22, 'height' => 22 ) ); ?>
											</span>
										<?php endif; ?>
									</span>

									<span class="posts__list-body">
										<span class="posts__list-meta">
											<?php if ( $cat ) : ?>
												<span class="posts__list-cat"><?php echo esc_html( $cat->name ); ?></span>
												<span class="posts__meta-sep" aria-hidden="true">·</span>
											<?php endif; ?>
											<span class="posts__list-date"><?php echo esc_html( get_the_date() ); ?></span>
										</span>
										<h4 class="posts__list-title"><?php the_title(); ?></h4>
										<span class="posts__list-cta">
											<?php esc_html_e( 'Read more', 'estecapelli' ); ?>
											<?php estecapelli_icon( 'arrow-right', array( 'width' => 12, 'height' => 12, 'class' => 'posts__list-arrow' ) ); ?>
										</span>
									</span>
								</a>
							</li>
						<?php endwhile; ?>
					</ul>
				<?php endif; ?>

			</div>

		<?php endif; ?>

	</div>
</section>

<?php wp_reset_postdata(); ?>
