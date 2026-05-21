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

<?php get_template_part( 'template-parts/services', 'home' ); ?>

<?php get_template_part( 'template-parts/why', 'choose' ); ?>

<?php get_template_part( 'template-parts/signature', 'methods' ); ?>

<?php get_template_part( 'template-parts/journey', 'steps' ); ?>

<?php
get_footer();
