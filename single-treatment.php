<?php
/**
 * Single Treatment — page-builder engine.
 *
 * Reads the ACF Flexible Content field `page_sections` and dispatches each
 * layout to its matching renderer at /template-parts/sections/{layout}.php.
 * The layout's fields are passed through via set_query_var('section', …).
 *
 * Falls back to the standard post content when no sections are configured,
 * so an editor can publish a treatment with just the WYSIWYG body if they
 * prefer.
 *
 * @package Estecapelli
 */

get_header();

while ( have_posts() ) :
	the_post();

	$sections = function_exists( 'get_field' ) ? get_field( 'page_sections' ) : array();

	if ( ! empty( $sections ) && is_array( $sections ) ) :

		foreach ( $sections as $section ) :
			$layout = $section['acf_fc_layout'] ?? '';
			if ( ! $layout ) {
				continue;
			}

			// Allow only known templates — guards against arbitrary paths.
			$template = locate_template( 'template-parts/sections/' . sanitize_file_name( $layout ) . '.php' );
			if ( ! $template ) {
				continue;
			}

			set_query_var( 'section', $section );
			load_template( $template, false );
		endforeach;

	else :
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
