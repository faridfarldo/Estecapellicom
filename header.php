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

	<div class="topbar" data-topbar>
		<div class="container topbar__inner">
			<div class="topbar__left">
				<?php estecapelli_trustpilot_badge(); ?>
				<span class="topbar__patients">
					<?php
					printf(
						/* translators: 1: patient count, 2: country count */
						esc_html__( '+%1$s Happy Patients from +%2$s Countries', 'estecapelli' ),
						esc_html( ESTECAPELLI_PATIENT_COUNT ),
						esc_html( ESTECAPELLI_COUNTRY_COUNT )
					);
					?>
				</span>
			</div>

			<div class="topbar__right">
				<div class="lang-switch" data-lang-switch>
					<button type="button" class="lang-switch__toggle" aria-expanded="false" aria-controls="lang-menu">
						<?php estecapelli_icon( 'globe', array( 'width' => 16, 'height' => 16 ) ); ?>
						<span class="lang-switch__current">EN</span>
						<?php estecapelli_icon( 'chevron-down', array( 'width' => 12, 'height' => 12 ) ); ?>
					</button>
					<ul class="lang-switch__menu" id="lang-menu" hidden>
						<?php
						$langs = array( 'EN' => 'English', 'TR' => 'Türkçe', 'DE' => 'Deutsch', 'ES' => 'Español', 'FR' => 'Français', 'IT' => 'Italiano', 'PT' => 'Português', 'PL' => 'Polski', 'AR' => 'العربية' );
						foreach ( $langs as $code => $name ) :
							?>
							<li><a href="#" data-lang="<?php echo esc_attr( strtolower( $code ) ); ?>"><span class="lang-switch__code"><?php echo esc_html( $code ); ?></span> <?php echo esc_html( $name ); ?></a></li>
							<?php
						endforeach;
						?>
					</ul>
				</div>

				<a class="topbar__whatsapp" href="<?php echo esc_url( estecapelli_whatsapp_url() ); ?>" target="_blank" rel="noopener">
					<?php estecapelli_icon( 'whatsapp', array( 'width' => 18, 'height' => 18 ) ); ?>
					<span><?php esc_html_e( 'WhatsApp', 'estecapelli' ); ?></span>
				</a>
			</div>
		</div>
	</div>

	<div class="mainbar">
		<div class="container mainbar__inner">

			<a class="mainbar__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php estecapelli_brand_mark( 'header' ); ?>
			</a>

			<button
				class="mainbar__nav-toggle"
				type="button"
				aria-expanded="false"
				aria-controls="primary-nav"
				aria-label="<?php esc_attr_e( 'Toggle menu', 'estecapelli' ); ?>"
				data-nav-toggle
			>
				<?php estecapelli_icon( 'menu', array( 'width' => 24, 'height' => 24, 'class' => 'nav-toggle__icon nav-toggle__icon--open' ) ); ?>
				<?php estecapelli_icon( 'close', array( 'width' => 24, 'height' => 24, 'class' => 'nav-toggle__icon nav-toggle__icon--close' ) ); ?>
			</button>

			<nav class="site-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'estecapelli' ); ?>" data-site-nav>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'site-nav__list',
						'fallback_cb'    => 'estecapelli_primary_menu_fallback',
						'walker'         => new Estecapelli_Walker_Nav_Menu(),
						'depth'          => 2,
					)
				);
				?>

				<a class="site-nav__mobile-cta btn btn--brand" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
					<?php esc_html_e( 'Free Consultation', 'estecapelli' ); ?>
				</a>
			</nav>

			<div class="mainbar__cta">
				<a class="btn btn--brand" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
					<?php esc_html_e( 'Free Consultation', 'estecapelli' ); ?>
				</a>
			</div>

		</div>
	</div>
</header>

<main id="main" class="site-main">
