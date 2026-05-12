<?php
/**
 * The main template file (blog index / fallback).
 *
 * @package Estecapelli
 */

get_header();
?>

<section class="container" style="padding-block: var(--s-8);">

	<?php if ( have_posts() ) : ?>

		<header class="page-header" style="margin-bottom: var(--s-7);">
			<h1>
				<?php
				if ( is_home() && ! is_front_page() ) {
					single_post_title();
				} else {
					esc_html_e( 'Latest from the blog', 'estecapelli' );
				}
				?>
			</h1>
		</header>

		<div class="post-list" style="display: grid; gap: var(--s-6);">
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="post-excerpt"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		</div>

		<?php the_posts_pagination(); ?>

	<?php else : ?>

		<h1><?php esc_html_e( 'Nothing here yet', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Check back soon — content is on the way.', 'estecapelli' ); ?></p>

	<?php endif; ?>

</section>

<?php
get_footer();
