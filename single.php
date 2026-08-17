<?php
/**
 * Single post — magazine-style article layout.
 *
 * The hero pairs the headline with the featured image side by side, the body
 * runs beside a sticky "On this page" rail, and the article always closes with
 * the related-articles row.
 *
 * @package Estecapelli
 */

get_header();

while ( have_posts() ) :
	the_post();

	$cats = get_the_category();
	$cat  = ! empty( $cats ) ? $cats[0] : null;

	/*
	 * Drop the default "Uncategorized" term. Every language inherits it from
	 * WordPress rather than from editorial work, so it surfaces in the wrong
	 * language ("Non classé" on an Italian article) and tells the reader
	 * nothing. Without it the related row falls back to the newest articles,
	 * which is a better signal than an empty category anyway.
	 */
	if ( $cat ) {
		$cat_source = (int) apply_filters( 'wpml_object_id', $cat->term_id, 'category', true, 'en' );
		if ( ( $cat_source ? $cat_source : $cat->term_id ) === (int) get_option( 'default_category' ) ) {
			$cat = null;
		}
	}

	$mins   = max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) );
	$tags   = get_the_tags();
	$permal = get_permalink();
	$title  = get_the_title();
	$lead   = wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 30, '…' );
	$blog   = estecapelli_indexed_url( '/en/blog' );
	$logo   = get_template_directory_uri() . '/assets/images/logo.webp';

	// Build the article HTML once, add heading anchors, and derive the TOC. Two
	// headings are enough to be worth a rail — most articles then carry one.
	list( $post_html, $toc_items ) = estecapelli_extract_toc( apply_filters( 'the_content', get_the_content() ) );
	$toc_html = estecapelli_toc_is_hidden() ? '' : estecapelli_render_toc( $toc_items, array( 'min' => 2 ) );
	?>

	<div class="reading-bar" aria-hidden="true"><span class="reading-bar__fill" data-reading-progress></span></div>

	<article class="article">

		<header class="article-hero">
			<div class="shell article-hero__shell">
				<div class="article-hero__text">
					<a class="article-hero__back" href="<?php echo esc_url( $blog ); ?>">
						<?php estecapelli_icon( 'chevron-left', array( 'width' => 16, 'height' => 16 ) ); ?>
						<?php esc_html_e( 'All articles', 'estecapelli' ); ?>
					</a>

					<?php if ( $cat ) : ?>
						<a class="article-hero__cat" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
							<?php estecapelli_icon( 'tag', array( 'width' => 13, 'height' => 13 ) ); ?>
							<?php echo esc_html( $cat->name ); ?>
						</a>
					<?php endif; ?>

					<h1 class="article-hero__title"><?php the_title(); ?></h1>

					<?php if ( $lead ) : ?>
						<p class="article-hero__lead"><?php echo esc_html( $lead ); ?></p>
					<?php endif; ?>

					<div class="article-hero__meta">
						<?php // Byline is always the clinic, never the WordPress account that typed it. ?>
						<span class="article-hero__brand">
							<img class="article-hero__brand-img" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="116" height="33" loading="eager" decoding="async" />
						</span>
						<span class="article-hero__dot" aria-hidden="true"></span>
						<span><?php echo esc_html( get_the_date() ); ?></span>
						<span class="article-hero__dot" aria-hidden="true"></span>
						<span><?php echo esc_html( sprintf( /* translators: %d: minutes */ _n( '%d min read', '%d min read', $mins, 'estecapelli' ), $mins ) ); ?></span>
					</div>
				</div>

				<figure class="article-hero__media">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', array( 'class' => 'article-hero__img', 'loading' => 'eager', 'decoding' => 'async', 'alt' => '' ) ); ?>
					<?php else : ?>
						<span class="article-hero__img article-hero__img--empty" aria-hidden="true">
							<?php estecapelli_icon( 'book-open', array( 'width' => 56, 'height' => 56 ) ); ?>
						</span>
					<?php endif; ?>
				</figure>
			</div>
		</header>

		<div class="shell">
			<div class="article-layout">

				<?php // The rail always renders: an article with too few headings for a ?>
				<?php // table of contents still gets the consultation card beside it.     ?>
				<aside class="article-aside">
					<?php echo $toc_html; // Escaped inside the renderer. ?>

					<div class="article-cta">
						<span class="article-cta__eyebrow">
							<?php estecapelli_icon( 'sparkles', array( 'width' => 14, 'height' => 14 ) ); ?>
							<?php esc_html_e( 'Free Consultation', 'estecapelli' ); ?>
						</span>
						<p class="article-cta__title"><?php esc_html_e( 'Book your free consultation', 'estecapelli' ); ?></p>
						<p class="article-cta__text"><?php esc_html_e( 'Reply in 2 minutes', 'estecapelli' ); ?></p>
						<a class="btn btn-accent article-cta__btn" href="<?php echo esc_url( estecapelli_indexed_url( '/en/contact' ) ); ?>">
							<?php esc_html_e( 'Get a Free Consultation', 'estecapelli' ); ?>
							<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
						</a>
					</div>
				</aside>

				<div class="article-main">
					<div class="single-post__content prose-content" data-article-body>
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
						<a class="single-post__share-btn single-post__share-btn--fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $permal ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on Facebook', 'estecapelli' ); ?>">
							<?php estecapelli_icon( 'facebook', array( 'width' => 18, 'height' => 18 ) ); ?>
						</a>
						<a class="single-post__share-btn single-post__share-btn--wa" href="https://wa.me/?text=<?php echo rawurlencode( $title . ' ' . $permal ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'estecapelli' ); ?>">
							<?php estecapelli_icon( 'whatsapp', array( 'width' => 18, 'height' => 18 ) ); ?>
						</a>
						<button type="button" class="single-post__share-btn" data-copy-link="<?php echo esc_url( $permal ); ?>" aria-label="<?php esc_attr_e( 'Copy link', 'estecapelli' ); ?>">
							<?php estecapelli_icon( 'link', array( 'width' => 18, 'height' => 18 ) ); ?>
						</button>
					</div>
				</div>

			</div>
		</div>

		<?php
		/*
		 * Related posts — same category first, then the newest other articles.
		 *
		 * Category alone is not a reliable source here: a translated article may
		 * carry no category, or sit in a category that holds only itself, and the
		 * section then disappeared entirely (as it did across Italian). Topping
		 * the row up keeps "Keep reading" under every article in every language.
		 */
		$related_args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'fields'              => 'ids',
		);

		$related_ids = array();
		if ( $cat ) {
			$in_category = new WP_Query( array_merge( $related_args, array( 'cat' => $cat->term_id ) ) );
			$related_ids = $in_category->posts;
		}
		if ( count( $related_ids ) < 3 ) {
			$fill = new WP_Query(
				array_merge(
					$related_args,
					array(
						'posts_per_page' => 3 - count( $related_ids ),
						'post__not_in'   => array_merge( array( get_the_ID() ), $related_ids ),
					)
				)
			);
			$related_ids = array_merge( $related_ids, $fill->posts );
		}

		if ( $related_ids ) :
			$related = new WP_Query(
				array(
					'post_type'           => 'post',
					'post__in'            => $related_ids,
					'orderby'             => 'post__in',
					'posts_per_page'      => count( $related_ids ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			?>
			<section class="single-related">
				<div class="shell">
					<div class="single-related__head">
						<div>
							<span class="single-related__eyebrow">
								<span class="single-related__eyebrow-mark" aria-hidden="true"></span>
								<?php esc_html_e( 'From the Journal', 'estecapelli' ); ?>
							</span>
							<h2 class="single-related__title"><?php esc_html_e( 'Keep reading', 'estecapelli' ); ?></h2>
						</div>
						<a class="single-related__all" href="<?php echo esc_url( $blog ); ?>">
							<?php esc_html_e( 'All articles', 'estecapelli' ); ?>
							<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
						</a>
					</div>
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
