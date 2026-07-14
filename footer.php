<?php
/**
 * The footer for the theme.
 *
 * @package Estecapelli
 */

$contact    = estecapelli_footer_contact();
$treatments = estecapelli_footer_treatments();
$sitemap    = estecapelli_footer_sitemap();

$footer_languages = estecapelli_header_languages();
$footer_language  = reset( $footer_languages );
foreach ( $footer_languages as $language ) {
	if ( ! empty( $language['active'] ) ) {
		$footer_language = $language;
		break;
	}
}
$footer_language_code = strtoupper( estecapelli_indexed_language_code( $footer_language['language_code'] ?? 'en' ) );
$footer_language_name = (string) ( $footer_language['native_name'] ?? $footer_language_code );
$footer_language_flag = (string) ( $footer_language['country_flag_url'] ?? '' );
?>
</main><!-- /#main -->

<aside class="cta-band" aria-label="<?php esc_attr_e( 'Book a consultation', 'estecapelli' ); ?>">
	<div class="shell cta-band-inner">
		<div>
			<h2><?php esc_html_e( 'Ready to start your transformation?', 'estecapelli' ); ?></h2>
			<p><?php esc_html_e( 'Speak with our medical team — free, no obligation. Get a personalized plan based on your goals.', 'estecapelli' ); ?></p>
		</div>
		<div class="cta-band__actions">
			<a class="btn btn-accent btn-lg" href="<?php echo esc_url( estecapelli_indexed_url( '/en/contact' ) ); ?>">
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

				<div class="site-footer__licences">
					<h4 class="site-footer__sub-heading"><?php esc_html_e( 'Our Licences', 'estecapelli' ); ?></h4>
					<img
						class="site-footer__certs"
						src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/certs.png' ); ?>"
						alt="<?php esc_attr_e( 'Internationally accredited & certified — Ministry of Health, Certified Medical Travel Agency, HRSA, NACo Award, ISO 13485', 'estecapelli' ); ?>"
						loading="lazy"
					/>
				</div>
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

				<div class="site-footer__language">
					<h4 class="site-footer__sub-heading"><?php esc_html_e( 'Language', 'estecapelli' ); ?></h4>
					<div class="lang-switch lang-switch--footer" data-lang-switch>
						<button
							type="button"
							class="lang-switch__toggle"
							aria-expanded="false"
							aria-controls="footer-language-menu"
							aria-label="<?php echo esc_attr( sprintf( __( 'Choose language. Current language: %s', 'estecapelli' ), $footer_language_name ) ); ?>"
						>
							<?php if ( $footer_language_flag ) : ?>
								<img class="lang-switch__current-flag" src="<?php echo esc_url( $footer_language_flag ); ?>" width="20" height="14" alt="" />
							<?php else : ?>
								<?php estecapelli_icon( 'globe', array( 'width' => 16, 'height' => 16 ) ); ?>
							<?php endif; ?>
							<span class="lang-switch__current"><?php echo esc_html( $footer_language_code ); ?></span>
							<?php estecapelli_icon( 'chevron-down', array( 'width' => 12, 'height' => 12, 'class' => 'chev' ) ); ?>
						</button>
						<ul class="lang-switch__menu" id="footer-language-menu" hidden>
							<?php foreach ( $footer_languages as $language ) :
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
				</div>
			</div>

			<!-- Column 4: Logo + Quick form -->
			<div class="site-footer__col site-footer__col--lead">
				<div class="site-footer__brand">
					<?php estecapelli_brand_mark( 'footer' ); ?>
				</div>

				<form class="lead-form" method="post" action="<?php echo esc_url( estecapelli_indexed_url( '/en/contact' ) ); ?>">
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
							class="js-intl-phone"
							type="tel"
							name="lead_phone"
							required
							autocomplete="tel"
							inputmode="tel"
							placeholder="<?php esc_attr_e( 'Phone number', 'estecapelli' ); ?>"
						/>
					</div>
					<?php estecapelli_lead_tracking_fields( 'footer' ); ?>
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
						'<li><a href="%1$s">%2$s</a></li><li><a href="%3$s">%4$s</a></li><li><a href="%5$s">%6$s</a></li><li><a href="%7$s">%8$s</a></li>',
						esc_url( estecapelli_translated_page_url( 'privacy-policy' ) ),
						esc_html__( 'Privacy Policy', 'estecapelli' ),
						esc_url( estecapelli_translated_page_url( 'terms' ) ),
						esc_html__( 'Terms', 'estecapelli' ),
						esc_url( estecapelli_translated_page_url( 'cookie-policy' ) ),
						esc_html__( 'Cookie Policy', 'estecapelli' ),
						esc_url( estecapelli_translated_page_url( 'kvkk-disclosure' ) ),
						esc_html__( 'KVKK Notice', 'estecapelli' )
					);
					echo '</ul>';
				}
				?>
			</nav>
		</div>

	</div>
</footer>

<?php get_template_part( 'template-parts/lead-popup' ); ?>

<!-- Floating WhatsApp button — present on every page, fixed bottom-right. -->
<a class="float-wp" href="<?php echo esc_url( estecapelli_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'estecapelli' ); ?>">
	<?php estecapelli_icon( 'whatsapp', array( 'width' => 24, 'height' => 24 ) ); ?>
	<span class="float-wp-text">
		<strong><?php esc_html_e( 'Free Hair Analysis', 'estecapelli' ); ?></strong>
		<span><?php esc_html_e( 'Reply in 2 minutes', 'estecapelli' ); ?></span>
	</span>
</a>

<!-- Global image lightbox — opened by any [data-img-zoom] (single) or
     [data-img-gallery] (JSON array → browsable gallery with arrows). -->
<div class="img-lightbox" data-img-lightbox hidden role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Photo viewer', 'estecapelli' ); ?>">
	<button type="button" class="img-lightbox__backdrop" data-img-lightbox-close aria-label="<?php esc_attr_e( 'Close', 'estecapelli' ); ?>"></button>
	<figure class="img-lightbox__shell">
		<button type="button" class="img-lightbox__close" data-img-lightbox-close aria-label="<?php esc_attr_e( 'Close', 'estecapelli' ); ?>">
			<?php estecapelli_icon( 'close', array( 'width' => 22, 'height' => 22 ) ); ?>
		</button>
		<button type="button" class="img-lightbox__nav img-lightbox__nav--prev" data-img-lightbox-prev aria-label="<?php esc_attr_e( 'Previous photo', 'estecapelli' ); ?>" hidden>
			<?php estecapelli_icon( 'chevron-left', array( 'width' => 26, 'height' => 26 ) ); ?>
		</button>
		<img class="img-lightbox__img" data-img-lightbox-img src="" alt="" />
		<button type="button" class="img-lightbox__nav img-lightbox__nav--next" data-img-lightbox-next aria-label="<?php esc_attr_e( 'Next photo', 'estecapelli' ); ?>" hidden>
			<?php estecapelli_icon( 'chevron-right', array( 'width' => 26, 'height' => 26 ) ); ?>
		</button>
		<span class="img-lightbox__count" data-img-lightbox-count hidden></span>
	</figure>
</div>

<?php wp_footer(); ?>
</body>
</html>
