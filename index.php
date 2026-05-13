<?php
/**
 * The main template file (blog index / fallback).
 *
 * @package Estecapelli
 */

get_header();
?>

<section class="shell py-16">

	<?php if ( have_posts() ) : ?>

		<header class="mb-12">
			<h1 class="text-4xl font-bold tracking-tight text-brand-950">
				<?php
				if ( is_home() && ! is_front_page() ) {
					single_post_title();
				} else {
					esc_html_e( 'Latest from the blog', 'estecapelli' );
				}
				?>
			</h1>
		</header>

		<div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'group rounded-2xl border border-brand-100 bg-white p-6 shadow-soft transition hover:-translate-y-1 hover:shadow-card' ); ?>>
					<h2 class="text-xl font-semibold text-brand-950 mb-3">
						<a href="<?php the_permalink(); ?>" class="hover:text-brand-600 transition-colors"><?php the_title(); ?></a>
					</h2>
					<div class="text-sm text-brand-700/80 leading-relaxed"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		</div>

		<div class="mt-12">
			<?php the_posts_pagination(); ?>
		</div>

	<?php else : ?>

		<h1 class="text-3xl font-bold text-brand-950 mb-4"><?php esc_html_e( 'Nothing here yet', 'estecapelli' ); ?></h1>
		<p class="text-brand-700/80"><?php esc_html_e( 'Check back soon — content is on the way.', 'estecapelli' ); ?></p>

	<?php endif; ?>

</section>

<?php
get_footer();
