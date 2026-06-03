<?php
/**
 * The footer for the theme.
 *
 * @package Estecapelli
 */

$contact    = estecapelli_footer_contact();
$treatments = estecapelli_footer_treatments();
$sitemap    = estecapelli_footer_sitemap();
$badges     = estecapelli_footer_badges();
?>
</main><!-- /#main -->

<aside class="cta-band" aria-label="<?php esc_attr_e( 'Book a consultation', 'estecapelli' ); ?>">
	<div class="shell cta-band-inner">
		<div>
			<h2><?php esc_html_e( 'Ready to start your transformation?', 'estecapelli' ); ?></h2>
			<p><?php esc_html_e( 'Speak with our medical team — free, no obligation. Get a personalized plan based on your goals.', 'estecapelli' ); ?></p>
		</div>
		<div class="cta-band__actions">
			<a class="btn btn-accent btn-lg" href="<?php echo esc_url( home_url( '/en/contact' ) ); ?>">
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

		<div class="site-footer__grid">

			<!-- Column 1: Visit Us — location, contact, social, licences -->
			<div class="site-footer__col site-footer__col--visit">
				<h3 class="site-footer__heading"><?php echo esc_html( $contact['heading'] ); ?></h3>
				<ul class="site-footer__contact">
					<?php if ( ! empty( $contact['address'] ) ) : ?>
						<li>
							<?php estecapelli_icon( 'map-pin', array( 'width' => 16, 'height' => 16, 'class' => 'site-footer__contact-icon' ) ); ?>
							<span><?php echo esc_html( $contact['address'] ); ?></span>
						</li>
					<?php endif; ?>
					<?php if ( ! empty( $contact['phone'] ) ) : ?>
						<li>
							<?php estecapelli_icon( 'phone', array( 'width' => 16, 'height' => 16, 'class' => 'site-footer__contact-icon' ) ); ?>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact['phone'] ) ); ?>"><?php echo esc_html( $contact['phone'] ); ?></a>
						</li>
					<?php endif; ?>
					<?php if ( ! empty( $contact['email'] ) ) : ?>
						<li>
							<?php estecapelli_icon( 'mail', array( 'width' => 16, 'height' => 16, 'class' => 'site-footer__contact-icon' ) ); ?>
							<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><?php echo esc_html( $contact['email'] ); ?></a>
						</li>
					<?php endif; ?>
				</ul>

				<?php if ( ! empty( $contact['socials'] ) ) : ?>
					<ul class="site-footer__socials">
						<?php foreach ( $contact['socials'] as $s ) : ?>
							<li>
								<a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $s['label'] ); ?>">
									<?php estecapelli_icon( $s['icon'], array( 'width' => 18, 'height' => 18 ) ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( ! empty( $badges ) ) : ?>
					<div class="site-footer__licences">
						<h4 class="site-footer__sub-heading"><?php esc_html_e( 'Our Licences', 'estecapelli' ); ?></h4>
						<div class="site-footer__licences-grid">
							<?php foreach ( $badges as $b ) : ?>
								<img class="site-footer__licence" src="<?php echo esc_url( $b['src'] ); ?>" alt="<?php echo esc_attr( $b['alt'] ); ?>" loading="lazy" />
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<!-- Column 2: Hair Transplant treatments -->
			<div class="site-footer__col">
				<h3 class="site-footer__heading"><?php esc_html_e( 'Hair Transplant', 'estecapelli' ); ?></h3>
				<ul class="site-footer__links">
					<?php foreach ( $treatments as $t ) : ?>
						<li><a href="<?php echo esc_url( $t['url'] ); ?>"><?php echo esc_html( $t['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<!-- Column 3: Sitemap -->
			<div class="site-footer__col">
				<h3 class="site-footer__heading"><?php esc_html_e( 'Sitemap', 'estecapelli' ); ?></h3>
				<ul class="site-footer__links">
					<?php foreach ( $sitemap as $s ) : ?>
						<li><a href="<?php echo esc_url( $s['url'] ); ?>"><?php echo esc_html( $s['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<!-- Column 4: Logo + Quick form -->
			<div class="site-footer__col site-footer__col--lead">
				<div class="site-footer__brand">
					<?php estecapelli_brand_mark( 'footer' ); ?>
				</div>

				<form class="lead-form" method="post" action="<?php echo esc_url( home_url( '/en/contact' ) ); ?>">
					<p class="lead-form__intro"><?php esc_html_e( 'Get a free consultation — leave your details and we will reach out.', 'estecapelli' ); ?></p>
					<div class="lead-form__field">
						<label for="lead-name" class="sr-only"><?php esc_html_e( 'Name and surname', 'estecapelli' ); ?></label>
						<input
							id="lead-name"
							type="text"
							name="lead_name"
							required
							autocomplete="name"
							placeholder="<?php esc_attr_e( 'Name and surname', 'estecapelli' ); ?>"
						/>
					</div>
					<div class="lead-form__field">
						<label for="lead-phone" class="sr-only"><?php esc_html_e( 'Phone number', 'estecapelli' ); ?></label>
						<input
							id="lead-phone"
							type="tel"
							name="lead_phone"
							required
							autocomplete="tel"
							inputmode="tel"
							placeholder="<?php esc_attr_e( 'Phone number (with country code)', 'estecapelli' ); ?>"
						/>
					</div>
					<input type="hidden" name="lead_source" value="footer" />
					<?php wp_nonce_field( 'estecapelli_lead', 'estecapelli_lead_nonce' ); ?>
					<button type="submit" class="btn btn-primary lead-form__submit">
						<?php esc_html_e( 'Request Call Back', 'estecapelli' ); ?>
						<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
					</button>
				</form>
			</div>
		</div>

		<div class="site-footer__bottom">
			<p class="site-footer__copy">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'estecapelli' ); ?>
			</p>
			<nav class="site-footer__legal-nav" aria-label="<?php esc_attr_e( 'Footer legal', 'estecapelli' ); ?>">
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
						esc_url( home_url( '/en/privacy-policy' ) ),
						esc_html__( 'Privacy Policy', 'estecapelli' ),
						esc_url( home_url( '/en/terms' ) ),
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
