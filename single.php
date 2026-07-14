<?php
/**
 * Single post — article layout with featured image, content, tags, share,
 * and related posts.
 *
 * @package Estecapelli
 */

get_header();

while ( have_posts() ) :
	the_post();

	$cats   = get_the_category();
	$cat    = ! empty( $cats ) ? $cats[0] : null;
	$mins   = max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) );
	$tags   = get_the_tags();
	$permal = get_permalink();
	$title  = get_the_title();
	?>

	<article class="single-post">

		<header class="single-post__hero">
			<div class="shell single-post__hero-shell">
				<a class="single-post__back" href="<?php echo esc_url( estecapelli_indexed_url( '/en/blog' ) ); ?>">
					<?php estecapelli_icon( 'chevron-left', array( 'width' => 16, 'height' => 16 ) ); ?>
					<?php esc_html_e( 'All articles', 'estecapelli' ); ?>
				</a>

				<?php if ( $cat ) : ?>
					<a class="single-post__cat" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
						<?php estecapelli_icon( 'tag', array( 'width' => 12, 'height' => 12 ) ); ?>
						<?php echo esc_html( $cat->name ); ?>
					</a>
				<?php endif; ?>

				<h1 class="single-post__title"><?php the_title(); ?></h1>

				<div class="single-post__meta">
					<span class="single-post__author"><?php echo esc_html( get_the_author() ); ?></span>
					<span class="post-card__sep" aria-hidden="true">·</span>
					<span><?php echo esc_html( get_the_date() ); ?></span>
					<span class="post-card__sep" aria-hidden="true">·</span>
					<span><?php echo esc_html( sprintf( /* translators: %d: minutes */ _n( '%d min read', '%d min read', $mins, 'estecapelli' ), $mins ) ); ?></span>
				</div>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="single-post__media">
				<div class="shell">
					<?php the_post_thumbnail( 'large', array( 'class' => 'single-post__img', 'loading' => 'eager', 'decoding' => 'async', 'alt' => '' ) ); ?>
				</div>
			</figure>
		<?php endif; ?>

		<div class="shell single-post__shell">
			<?php
			// Build the article HTML once, add heading anchors, and derive the TOC.
			list( $post_html, $toc_items ) = estecapelli_extract_toc( apply_filters( 'the_content', get_the_content() ) );
			if ( ! estecapelli_toc_is_hidden() ) {
				echo estecapelli_render_toc( $toc_items ); // Escaped inside the renderer.
			}
			?>
			<div class="single-post__content prose-content">
				<?php
				echo $post_html; // phpcs:ignore WordPress.Security.EscapeOutput -- already run through the_content filters.
				wp_link_pages(
					array(
						'before' => '<div class="single-post__pages">' . esc_html__( 'Pages:', 'estecapelli' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>

			<?php if ( $tags ) : ?>
				<div class="single-post__tags">
					<?php foreach ( $tags as $tag ) : ?>
						<a class="single-post__tag" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="single-post__share">
				<span class="single-post__share-label"><?php esc_html_e( 'Share', 'estecapelli' ); ?></span>
				<a class="single-post__share-btn" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $permal ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on Facebook', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'facebook', array( 'width' => 18, 'height' => 18 ) ); ?>
				</a>
				<a class="single-post__share-btn" href="https://wa.me/?text=<?php echo rawurlencode( $title . ' ' . $permal ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'whatsapp', array( 'width' => 18, 'height' => 18 ) ); ?>
				</a>
				<button type="button" class="single-post__share-btn" data-copy-link="<?php echo esc_url( $permal ); ?>" aria-label="<?php esc_attr_e( 'Copy link', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'link', array( 'width' => 18, 'height' => 18 ) ); ?>
				</button>
			</div>
		</div>

		<?php
		// Related posts — same category, excluding current.
		$related_args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);
		if ( $cat ) {
			$related_args['cat'] = $cat->term_id;
		}
		$related = new WP_Query( $related_args );
		if ( $related->have_posts() ) :
			?>
			<section class="single-related">
				<div class="shell">
					<h2 class="single-related__title"><?php esc_html_e( 'Keep reading', 'estecapelli' ); ?></h2>
					<div class="blog-grid">
						<?php
						while ( $related->have_posts() ) :
							$related->the_post();
							load_template( locate_template( 'template-parts/post-card.php' ), false );
						endwhile;
						?>
					</div>
				</div>
			</section>
			<?php
			wp_reset_postdata();
		endif;
		?>

	</article>

	<?php
endwhile;

get_footer();
