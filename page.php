<?php
/**
 * The template for displaying single pages.
 *
 * @package Estecapelli
 */

get_header();
?>

<section class="shell py-16">

	<?php while ( have_posts() ) : the_post(); ?>

		<article <?php post_class( 'max-w-3xl mx-auto' ); ?>>
			<header class="mb-10">
				<h1 class="text-4xl md:text-5xl font-bold tracking-tight text-brand-950"><?php the_title(); ?></h1>
			</header>

			<div class="prose-content text-brand-900/90 leading-relaxed [&_p]:mb-4 [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-brand-950 [&_h2]:mt-10 [&_h2]:mb-4 [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-brand-900 [&_h3]:mt-8 [&_h3]:mb-3 [&_a]:text-brand-600 [&_a]:underline hover:[&_a]:text-brand-700 [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-4 [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:mb-4 [&_img]:rounded-xl [&_img]:my-6">
				<?php the_content(); ?>
			</div>
		</article>

	<?php endwhile; ?>

</section>

<?php
get_footer();
