<?php
/**
 * The footer for the theme.
 *
 * @package Estecapelli
 */
?>
</main><!-- /#main -->

<footer class="site-footer" role="contentinfo">
	<div class="container">

		<div class="site-footer__top">
			<div class="site-footer__brand">
				<?php estecapelli_brand_mark( 'footer' ); ?>
			</div>
			<p class="site-footer__tagline">
				<?php esc_html_e( 'World-class hair transplant, plastic surgery, and dental care in Türkiye. Trusted by 15,000+ patients across 40+ countries.', 'estecapelli' ); ?>
			</p>
		</div>

		<?php
		$has_footer_widgets = is_active_sidebar( 'footer-1' )
			|| is_active_sidebar( 'footer-2' )
			|| is_active_sidebar( 'footer-3' )
			|| is_active_sidebar( 'footer-4' );
		?>

		<?php if ( $has_footer_widgets ) : ?>
			<div class="site-footer__widgets">
				<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
					<?php if ( is_active_sidebar( "footer-{$i}" ) ) : ?>
						<div class="site-footer__col">
							<?php dynamic_sidebar( "footer-{$i}" ); ?>
						</div>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
		<?php endif; ?>

		<div class="site-footer__bottom">
			<p class="site-footer__copy">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'estecapelli' ); ?>
			</p>
			<nav class="site-footer__legal" aria-label="<?php esc_attr_e( 'Footer legal', 'estecapelli' ); ?>">
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'site-footer__legal-list',
							'depth'          => 1,
						)
					);
				} else {
					echo '<ul class="site-footer__legal-list">';
					printf(
						'<li><a href="%1$s">%2$s</a></li><li><a href="%3$s">%4$s</a></li>',
						esc_url( home_url( '/privacy-policy/' ) ),
						esc_html__( 'Privacy Policy', 'estecapelli' ),
						esc_url( home_url( '/terms/' ) ),
						esc_html__( 'Terms', 'estecapelli' )
					);
					echo '</ul>';
				}
				?>
			</nav>
		</div>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
