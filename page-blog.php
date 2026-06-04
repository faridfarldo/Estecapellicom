<?php
/**
 * Template Name: Blog
 *
 * Blog index — lists WordPress posts (the standard `post` type): a featured
 * latest post, a grid of the rest, category filters and pagination. Routed
 * here for the page with slug "blog" via inc/leads.php.
 *
 * @package Estecapelli
 */

get_header();

$paged = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$per   = 9;

$blog = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => $per,
		'paged'          => $paged,
	)
);

$cats = get_categories( array( 'hide_empty' => true ) );
?>

<div class="blog-page">

	<header class="blog-hero">
		<div class="shell blog-hero__shell">
			<span class="blog-hero__eyebrow">
				<span class="blog-hero__eyebrow-mark" aria-hidden="true"></span>
				<?php esc_html_e( 'From the Journal', 'estecapelli' ); ?>
			</span>
			<h1 class="blog-hero__title"><?php esc_html_e( 'Research, results & recovery', 'estecapelli' ); ?></h1>
			<p class="blog-hero__lead"><?php esc_html_e( 'Expert articles on hair restoration, plastic surgery, dental treatment and the journey to your transformation — written by the Estecapelli team.', 'estecapelli' ); ?></p>

			<?php if ( ! empty( $cats ) && 1 === $paged ) : ?>
				<nav class="blog-filters" aria-label="<?php esc_attr_e( 'Article categories', 'estecapelli' ); ?>">
					<a class="blog-filter is-active" href="<?php echo esc_url( home_url( '/en/blog' ) ); ?>"><?php esc_html_e( 'All', 'estecapelli' ); ?></a>
					<?php foreach ( $cats as $c ) : ?>
						<a class="blog-filter" href="<?php echo esc_url( get_category_link( $c->term_id ) ); ?>"><?php echo esc_html( $c->name ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>
	</header>

	<div class="shell blog-body">
		<?php if ( $blog->have_posts() ) : ?>

			<?php
			// Featured (latest) post — only on the first page.
			$grid_query = $blog;
			if ( 1 === $paged ) :
				$blog->the_post();
				$f_cats = get_the_category();
				$f_cat  = ! empty( $f_cats ) ? $f_cats[0] : null;
				$f_mins = max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) );
				?>
				<article class="blog-feature">
					<a class="blog-feature__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'large', array( 'class' => 'blog-feature__img', 'loading' => 'eager', 'decoding' => 'async', 'alt' => '' ) ); ?>
						<?php else : ?>
							<span class="blog-feature__fallback" aria-hidden="true"><?php estecapelli_icon( 'book-open', array( 'width' => 56, 'height' => 56 ) ); ?></span>
						<?php endif; ?>
						<span class="blog-feature__wash" aria-hidden="true"></span>
					</a>
					<div class="blog-feature__body">
						<span class="blog-feature__badge"><?php esc_html_e( 'Latest article', 'estecapelli' ); ?></span>
						<div class="blog-feature__meta">
							<?php if ( $f_cat ) : ?><span class="blog-feature__cat"><?php echo esc_html( $f_cat->name ); ?></span><span class="post-card__sep" aria-hidden="true">·</span><?php endif; ?>
							<span><?php echo esc_html( get_the_date() ); ?></span>
							<span class="post-card__sep" aria-hidden="true">·</span>
							<span><?php echo esc_html( sprintf( /* translators: %d: minutes */ _n( '%d min read', '%d min read', $f_mins, 'estecapelli' ), $f_mins ) ); ?></span>
						</div>
						<h2 class="blog-feature__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="blog-feature__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 40, '…' ) ); ?></p>
						<a class="btn btn-primary blog-feature__cta" href="<?php the_permalink(); ?>">
							<?php esc_html_e( 'Read the article', 'estecapelli' ); ?>
							<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
						</a>
					</div>
				</article>
			<?php endif; ?>

			<?php if ( $blog->have_posts() ) : ?>
				<div class="blog-grid">
					<?php
					while ( $blog->have_posts() ) :
						$blog->the_post();
						load_template( locate_template( 'template-parts/post-card.php' ), false );
					endwhile;
					?>
				</div>
			<?php endif; ?>

			<?php if ( $blog->max_num_pages > 1 ) : ?>
				<nav class="blog-pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'estecapelli' ); ?>">
					<?php
					echo wp_kses_post(
						paginate_links(
							array(
								'base'      => esc_url_raw( add_query_arg( 'paged', '%#%', home_url( '/en/blog' ) ) ),
								'format'    => '',
								'current'   => $paged,
								'total'     => $blog->max_num_pages,
								'prev_text' => '‹ ' . __( 'Newer', 'estecapelli' ),
								'next_text' => __( 'Older', 'estecapelli' ) . ' ›',
								'mid_size'  => 1,
							)
						)
					);
					?>
				</nav>
			<?php endif; ?>

		<?php else : ?>
			<div class="blog-empty">
				<span class="blog-empty__icon" aria-hidden="true"><?php estecapelli_icon( 'book-open', array( 'width' => 36, 'height' => 36 ) ); ?></span>
				<p><?php esc_html_e( 'No articles have been published yet. New posts will appear here as soon as our team publishes them.', 'estecapelli' ); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<?php wp_reset_postdata(); ?>
</div>

<?php
get_footer();
