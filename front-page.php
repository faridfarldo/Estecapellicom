<?php
/**
 * The homepage template.
 *
 * Loaded automatically when "Front page displays" is left on "Your latest posts"
 * or when a static page is set as the front page.
 *
 * @package Estecapelli
 */

get_header();
?>

<?php get_template_part( 'template-parts/hero', 'home' ); ?>

<?php get_template_part( 'template-parts/trust', 'strip' ); ?>

<?php get_template_part( 'template-parts/hair-analysis-lab' ); ?>

<?php if ( ! function_exists( 'estecapelli_is_turkish_request' ) || ! estecapelli_is_turkish_request() ) : ?>
	<?php get_template_part( 'template-parts/home-before-after' ); ?>
<?php endif; ?>

<?php get_template_part( 'template-parts/services', 'home' ); ?>

<?php get_template_part( 'template-parts/why', 'choose' ); ?>

<?php get_template_part( 'template-parts/signature', 'methods' ); ?>

<?php get_template_part( 'template-parts/patient', 'stories' ); ?>

<?php get_template_part( 'template-parts/facilities' ); ?>

<?php get_template_part( 'template-parts/latest', 'posts' ); ?>

<?php
get_footer();
