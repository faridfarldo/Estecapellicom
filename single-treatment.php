<?php
/**
 * Single Treatment.
 *
 * Renders via the shared section engine when ACF page_sections is set;
 * falls back to standard post content otherwise.
 *
 * @package Estecapelli
 */

get_header();

while ( have_posts() ) :
	the_post();

	if ( ! estecapelli_render_page_sections() ) :
		?>
		<article class="treatment-fallback shell">
			<header>
				<h1 class="treatment-fallback__title"><?php the_title(); ?></h1>
			</header>
			<div class="treatment-fallback__content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endif;

endwhile;

get_footer();
