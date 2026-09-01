<?php
/**
 * The homepage template.
 *
 * Serves every language root (/en/, /fr/, /it/ …). Each of those requests is
 * resolved to that language's Home page, which exists so Rank Math and the
 * dashboard have something per-language to attach to; the layout itself comes
 * from the shared section stack below, never from the page's post_content.
 *
 * @package Estecapelli
 */

get_header();

get_template_part( 'template-parts/home-sections' );

get_footer();
