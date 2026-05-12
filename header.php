<?php
/**
 * The header for the theme.
 *
 * @package Estecapelli
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'estecapelli' ); ?></a>

<header class="site-header" data-site-header>
	<div class="container site-header__inner">

		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php estecapelli_brand_mark( 'header' ); ?>
		</a>

		<button
			class="site-header__nav-toggle"
			type="button"
			aria-expanded="false"
			aria-controls="primary-nav"
			data-nav-toggle
		>
			<span class="sr-only"><?php esc_html_e( 'Toggle menu', 'estecapelli' ); ?></span>
			<svg data-icon-open viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
				<line x1="4" y1="7" x2="20" y2="7" />
				<line x1="4" y1="12" x2="20" y2="12" />
				<line x1="4" y1="17" x2="20" y2="17" />
			</svg>
			<svg data-icon-close viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
				<line x1="6" y1="6" x2="18" y2="18" />
				<line x1="6" y1="18" x2="18" y2="6" />
			</svg>
		</button>

		<nav class="site-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'estecapelli' ); ?>" data-site-nav>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-nav__list',
					'fallback_cb'    => 'estecapelli_primary_menu_fallback',
					'depth'          => 2,
				)
			);
			?>
		</nav>

		<div class="site-header__cta">
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
				<?php esc_html_e( 'Free Consultation', 'estecapelli' ); ?>
			</a>
		</div>

	</div>
</header>

<main id="main" class="site-main">
