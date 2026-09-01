<?php
/**
 * Template Name: Estecapelli Homepage
 *
 * Assigned to the Home page of every public language. The template is what
 * identifies a page as a language homepage — see estecapelli_is_home_page_post()
 * — and it is also what makes the ACF "Homepage Content" field groups appear on
 * that page so each language can override any section from the dashboard.
 *
 * WordPress serves the language roots through front-page.php, so this file is
 * only reached if a Home page is ever requested by its own URL before the 301
 * to the language root runs. Rendering the same stack keeps that harmless.
 *
 * @package Estecapelli
 */

get_header();

get_template_part( 'template-parts/home-sections' );

get_footer();
