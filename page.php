<?php
/**
 * The template for displaying single pages.
 *
 * @package Estecapelli
 */

get_header();
?>

<section class="container" style="padding-block: var(--s-8);">

	<?php while ( have_posts() ) : the_post(); ?>

		<article <?php post_class(); ?>>
			<header class="page-header" style="margin-bottom: var(--s-6);">
				<h1><?php the_title(); ?></h1>
			</header>

			<div class="page-content">
				<?php the_content(); ?>
			</div>
		</article>

	<?php endwhile; ?>

</section>

<?php
get_footer();
