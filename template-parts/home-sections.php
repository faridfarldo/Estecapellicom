<?php
/**
 * The homepage section stack.
 *
 * Shared by front-page.php (the language roots) and home-page.php (the page
 * template assigned to each language's Home page) so the two can never drift.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
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
