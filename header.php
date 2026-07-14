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

<?php
$header_languages = estecapelli_header_languages();
$current_language = reset( $header_languages );
foreach ( $header_languages as $header_language ) {
	if ( ! empty( $header_language['active'] ) ) {
		$current_language = $header_language;
		break;
	}
}
$current_language_code = strtoupper( sanitize_key( $current_language['language_code'] ?? 'en' ) );
$current_language_name = (string) ( $current_language['native_name'] ?? $current_language_code );
$current_language_flag = (string) ( $current_language['country_flag_url'] ?? '' );
?>

<header class="site-header" data-site-header>

	<div class="mainbar">
		<div class="mainbar-inner">

			<a class="brand-link" href="<?php echo esc_url( home_url( '/en/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php estecapelli_brand_mark( 'header' ); ?>
			</a>

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

				<a class="btn btn-primary site-nav__mobile-cta" href="<?php echo esc_url( home_url( '/en/contact' ) ); ?>">
					<?php esc_html_e( 'Free Consultation', 'estecapelli' ); ?>
				</a>
			</nav>

			<div class="mainbar-actions">
				<div class="lang-switch" data-lang-switch>
					<button
						type="button"
						class="lang-switch__toggle"
						aria-expanded="false"
						aria-controls="header-language-menu"
						aria-label="<?php echo esc_attr( sprintf( __( 'Choose language. Current language: %s', 'estecapelli' ), $current_language_name ) ); ?>"
					>
						<?php if ( $current_language_flag ) : ?>
							<img class="lang-switch__current-flag" src="<?php echo esc_url( $current_language_flag ); ?>" width="20" height="14" alt="" />
						<?php else : ?>
							<?php estecapelli_icon( 'globe', array( 'width' => 16, 'height' => 16 ) ); ?>
						<?php endif; ?>
						<span class="lang-switch__current"><?php echo esc_html( $current_language_code ); ?></span>
						<?php estecapelli_icon( 'chevron-down', array( 'width' => 12, 'height' => 12, 'class' => 'chev' ) ); ?>
					</button>
					<ul class="lang-switch__menu" id="header-language-menu" hidden>
						<?php foreach ( $header_languages as $language ) :
							$code      = sanitize_key( $language['language_code'] ?? '' );
							$name      = (string) ( $language['native_name'] ?? strtoupper( $code ) );
							$url       = (string) ( $language['url'] ?? '' );
							$is_active = ! empty( $language['active'] );
							$flag      = (string) ( $language['country_flag_url'] ?? '' );
							?>
							<li class="<?php echo $is_active ? 'is-active' : ''; ?>">
								<a
									href="<?php echo esc_url( $url ); ?>"
									lang="<?php echo esc_attr( $code ); ?>"
									hreflang="<?php echo esc_attr( $code ); ?>"
									<?php echo $is_active ? 'aria-current="page"' : ''; ?>
								>
									<?php if ( $flag ) : ?>
										<img class="lang-switch__flag" src="<?php echo esc_url( $flag ); ?>" width="18" height="12" alt="" />
									<?php endif; ?>
									<span class="lang-code"><?php echo esc_html( strtoupper( $code ) ); ?></span>
									<span class="lang-switch__name"><?php echo esc_html( $name ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="mainbar-cta">
					<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/en/contact' ) ); ?>">
						<?php esc_html_e( 'Free Consultation', 'estecapelli' ); ?>
						<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
					</a>
				</div>

				<button
					class="nav-toggle"
					type="button"
					aria-expanded="false"
					aria-controls="primary-nav"
					aria-label="<?php esc_attr_e( 'Toggle menu', 'estecapelli' ); ?>"
					data-nav-toggle
				>
					<?php estecapelli_icon( 'menu',  array( 'width' => 24, 'height' => 24, 'class' => 'icon-open' ) ); ?>
					<?php estecapelli_icon( 'close', array( 'width' => 24, 'height' => 24, 'class' => 'icon-close' ) ); ?>
				</button>
			</div>

		</div>
	</div>
</header>

<main id="main" class="site-main">
