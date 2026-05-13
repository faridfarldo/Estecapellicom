<?php
/**
 * The footer for the theme.
 *
 * @package Estecapelli
 */
?>
</main><!-- /#main -->

<aside class="cta-band" aria-label="<?php esc_attr_e( 'Book a consultation', 'estecapelli' ); ?>">
	<div class="shell cta-band-inner">
		<div>
			<h2><?php esc_html_e( 'Ready to start your transformation?', 'estecapelli' ); ?></h2>
			<p><?php esc_html_e( 'Speak with our medical team — free, no obligation. Get a personalized plan based on your goals.', 'estecapelli' ); ?></p>
		</div>
		<div class="cta-band__actions">
			<a class="btn btn-accent btn-lg" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
				<?php esc_html_e( 'Get a Free Consultation', 'estecapelli' ); ?>
				<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
			</a>
			<a class="btn btn-ghost-light btn-lg" href="<?php echo esc_url( estecapelli_whatsapp_url() ); ?>" target="_blank" rel="noopener">
				<?php estecapelli_icon( 'whatsapp', array( 'width' => 18, 'height' => 18 ) ); ?>
				<?php esc_html_e( 'Chat on WhatsApp', 'estecapelli' ); ?>
			</a>
		</div>
	</div>
</aside>

<footer class="site-footer" role="contentinfo">
	<div class="shell">

		<div class="site-footer__top">
			<div class="site-footer__brand-block">
				<div>
					<?php estecapelli_brand_mark( 'footer' ); ?>
				</div>
				<p class="site-footer__tagline">
					<?php esc_html_e( 'World-class hair transplant, plastic surgery, and dental care in Türkiye. Trusted by 15,000+ patients across 40+ countries.', 'estecapelli' ); ?>
				</p>
				<div class="site-footer__trust">
					<span class="site-footer__trust-item"><strong>15+</strong> <?php esc_html_e( 'Years', 'estecapelli' ); ?></span>
					<span class="site-footer__trust-item"><strong>15,000+</strong> <?php esc_html_e( 'Patients', 'estecapelli' ); ?></span>
					<span class="site-footer__trust-item"><strong>40+</strong> <?php esc_html_e( 'Countries', 'estecapelli' ); ?></span>
					<span class="site-footer__trust-item"><strong>24/7</strong> <?php esc_html_e( 'Support', 'estecapelli' ); ?></span>
				</div>
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
			<?php else : ?>
				<div class="site-footer__widgets">
					<div class="site-footer__col">
						<h3><?php esc_html_e( 'Treatments', 'estecapelli' ); ?></h3>
						<ul>
							<li><a href="<?php echo esc_url( home_url( '/hair-transplant/' ) ); ?>"><?php esc_html_e( 'Hair Transplant', 'estecapelli' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/plastic-surgery/' ) ); ?>"><?php esc_html_e( 'Plastic Surgery', 'estecapelli' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/dental-treatment/' ) ); ?>"><?php esc_html_e( 'Dental Treatment', 'estecapelli' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/exosome-treatment/' ) ); ?>"><?php esc_html_e( 'Exosome Treatment', 'estecapelli' ); ?></a></li>
						</ul>
					</div>
					<div class="site-footer__col">
						<h3><?php esc_html_e( 'Company', 'estecapelli' ); ?></h3>
						<ul>
							<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'estecapelli' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/doctors/' ) ); ?>"><?php esc_html_e( 'Our Doctors', 'estecapelli' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/before-after/' ) ); ?>"><?php esc_html_e( 'Before &amp; After', 'estecapelli' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog', 'estecapelli' ); ?></a></li>
						</ul>
					</div>
					<div class="site-footer__col">
						<h3><?php esc_html_e( 'Contact', 'estecapelli' ); ?></h3>
						<ul>
							<li>
								<a href="<?php echo esc_url( estecapelli_whatsapp_url() ); ?>" target="_blank" rel="noopener">
									<?php estecapelli_icon( 'whatsapp', array( 'width' => 16, 'height' => 16 ) ); ?>
									WhatsApp
								</a>
							</li>
							<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Form', 'estecapelli' ); ?></a></li>
							<li><?php esc_html_e( 'Istanbul, Türkiye', 'estecapelli' ); ?></li>
						</ul>
					</div>
					<div class="site-footer__col">
						<h3><?php esc_html_e( 'Languages', 'estecapelli' ); ?></h3>
						<ul class="site-footer__langs">
							<?php
							$footer_langs = array( 'EN' => 'English', 'TR' => 'Türkçe', 'DE' => 'Deutsch', 'ES' => 'Español', 'FR' => 'Français', 'IT' => 'Italiano', 'PT' => 'Português', 'PL' => 'Polski', 'AR' => 'العربية' );
							foreach ( $footer_langs as $code => $name ) :
								?>
								<li><a href="#" data-lang="<?php echo esc_attr( strtolower( $code ) ); ?>"><span class="site-footer__lang-code"><?php echo esc_html( $code ); ?></span> <?php echo esc_html( $name ); ?></a></li>
								<?php
							endforeach;
							?>
						</ul>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div class="site-footer__bottom">
			<p>
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'estecapelli' ); ?>
			</p>
			<nav aria-label="<?php esc_attr_e( 'Footer legal', 'estecapelli' ); ?>">
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
