<?php
/**
 * Template Name: Contact
 *
 * Dedicated Contact page: quick contact methods, a consultation form
 * (handled in inc/leads.php), clinic details, working hours, a free
 * photo-analysis prompt, a "speak your language" flag row and a map.
 *
 * Routed here for the page with slug "contact" via inc/leads.php.
 *
 * @package Estecapelli
 */

get_header();

$contact   = estecapelli_footer_contact();
$phone     = $contact['phone']   ?? '';
$email     = $contact['email']   ?? '';
$address   = $contact['address'] ?? '';
$tel       = preg_replace( '/[^0-9+]/', '', $phone );
$wa_url    = estecapelli_whatsapp_url( __( 'Hello Estecapelli, I would like to book a free consultation.', 'estecapelli' ) );
$wa_photos = estecapelli_whatsapp_url( __( 'Hello Estecapelli, I would like a free analysis. Here are my photos:', 'estecapelli' ) );
$instagram = '';
foreach ( (array) ( $contact['socials'] ?? array() ) as $s ) {
	if ( 'instagram' === ( $s['icon'] ?? '' ) ) { $instagram = $s['url']; break; }
}

$languages = array(
	array( 'cc' => 'gb', 'label' => __( 'English', 'estecapelli' ) ),
	array( 'cc' => 'tr', 'label' => __( 'Türkçe', 'estecapelli' ) ),
	array( 'cc' => 'sa', 'label' => __( 'العربية', 'estecapelli' ) ),
	array( 'cc' => 'ru', 'label' => __( 'Русский', 'estecapelli' ) ),
	array( 'cc' => 'fr', 'label' => __( 'Français', 'estecapelli' ) ),
	array( 'cc' => 'de', 'label' => __( 'Deutsch', 'estecapelli' ) ),
	array( 'cc' => 'es', 'label' => __( 'Español', 'estecapelli' ) ),
);

$sent = isset( $_GET['sent'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>

<div class="contact-page">

	<!-- Hero -->
	<header class="contact-hero">
		<div class="shell contact-hero__shell">
			<span class="contact-hero__eyebrow">
				<span class="contact-hero__eyebrow-mark" aria-hidden="true"></span>
				<?php esc_html_e( 'Contact Us', 'estecapelli' ); ?>
			</span>
			<h1 class="contact-hero__title"><?php esc_html_e( 'Let’s start your journey', 'estecapelli' ); ?></h1>
			<p class="contact-hero__lead">
				<?php esc_html_e( 'Reach our team by WhatsApp, phone or email — or leave your details and a medical consultant will get back to you in your own language.', 'estecapelli' ); ?>
			</p>
			<span class="contact-hero__badge">
				<?php estecapelli_icon( 'headset', array( 'width' => 16, 'height' => 16 ) ); ?>
				<?php esc_html_e( 'We usually reply within an hour', 'estecapelli' ); ?>
			</span>
		</div>
	</header>

	<!-- Quick contact methods -->
	<section class="contact-methods">
		<div class="shell">
			<ul class="contact-methods__grid">
				<li>
					<a class="contact-method contact-method--whatsapp" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener">
						<span class="contact-method__icon"><?php estecapelli_icon( 'whatsapp', array( 'width' => 24, 'height' => 24 ) ); ?></span>
						<span class="contact-method__label"><?php esc_html_e( 'WhatsApp', 'estecapelli' ); ?></span>
						<span class="contact-method__value"><?php esc_html_e( 'Chat with us now', 'estecapelli' ); ?></span>
					</a>
				</li>
				<li>
					<a class="contact-method" href="tel:<?php echo esc_attr( $tel ); ?>">
						<span class="contact-method__icon"><?php estecapelli_icon( 'phone', array( 'width' => 22, 'height' => 22 ) ); ?></span>
						<span class="contact-method__label"><?php esc_html_e( 'Call', 'estecapelli' ); ?></span>
						<span class="contact-method__value"><?php echo esc_html( $phone ); ?></span>
					</a>
				</li>
				<li>
					<a class="contact-method" href="mailto:<?php echo esc_attr( $email ); ?>">
						<span class="contact-method__icon"><?php estecapelli_icon( 'mail', array( 'width' => 22, 'height' => 22 ) ); ?></span>
						<span class="contact-method__label"><?php esc_html_e( 'Email', 'estecapelli' ); ?></span>
						<span class="contact-method__value"><?php echo esc_html( $email ); ?></span>
					</a>
				</li>
				<?php if ( $instagram ) : ?>
				<li>
					<a class="contact-method" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener">
						<span class="contact-method__icon"><?php estecapelli_icon( 'instagram', array( 'width' => 22, 'height' => 22 ) ); ?></span>
						<span class="contact-method__label"><?php esc_html_e( 'Instagram', 'estecapelli' ); ?></span>
						<span class="contact-method__value">@estecapelli</span>
					</a>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</section>

	<!-- Form + clinic details -->
	<section class="contact-main">
		<div class="shell contact-main__shell">

			<div class="contact-form-card" id="contact-form">
				<h2 class="contact-form-card__title"><?php esc_html_e( 'Request a free consultation', 'estecapelli' ); ?></h2>
				<p class="contact-form-card__sub"><?php esc_html_e( 'Tell us a little about you and we’ll be in touch shortly.', 'estecapelli' ); ?></p>

				<?php if ( $sent ) : ?>
					<div class="contact-alert" role="status">
						<?php estecapelli_icon( 'check-circle', array( 'width' => 20, 'height' => 20 ) ); ?>
						<span><?php esc_html_e( 'Thank you! Your request has been received — our team will contact you shortly.', 'estecapelli' ); ?></span>
					</div>
				<?php endif; ?>

				<form class="contact-form" method="post" action="<?php echo esc_url( home_url( '/en/contact' ) ); ?>">
					<div class="contact-form__row">
						<div class="contact-form__field">
							<label for="cf-name"><?php esc_html_e( 'Full name', 'estecapelli' ); ?> <span aria-hidden="true">*</span></label>
							<input id="cf-name" type="text" name="lead_name" required autocomplete="name" placeholder="<?php esc_attr_e( 'Name and surname', 'estecapelli' ); ?>" />
						</div>
						<div class="contact-form__field">
							<label for="cf-phone"><?php esc_html_e( 'Phone', 'estecapelli' ); ?> <span aria-hidden="true">*</span></label>
							<input id="cf-phone" type="tel" name="lead_phone" required autocomplete="tel" inputmode="tel" placeholder="<?php esc_attr_e( 'With country code', 'estecapelli' ); ?>" />
						</div>
					</div>
					<div class="contact-form__row">
						<div class="contact-form__field">
							<label for="cf-email"><?php esc_html_e( 'Email', 'estecapelli' ); ?></label>
							<input id="cf-email" type="email" name="lead_email" autocomplete="email" placeholder="<?php esc_attr_e( 'you@example.com', 'estecapelli' ); ?>" />
						</div>
						<div class="contact-form__field">
							<label for="cf-treatment"><?php esc_html_e( 'Interested in', 'estecapelli' ); ?></label>
							<select id="cf-treatment" name="lead_treatment">
								<option value=""><?php esc_html_e( 'Select a treatment', 'estecapelli' ); ?></option>
								<option><?php esc_html_e( 'Hair Transplant', 'estecapelli' ); ?></option>
								<option><?php esc_html_e( 'Plastic Surgery', 'estecapelli' ); ?></option>
								<option><?php esc_html_e( 'Dental Treatment', 'estecapelli' ); ?></option>
								<option><?php esc_html_e( 'Not sure yet', 'estecapelli' ); ?></option>
							</select>
						</div>
					</div>
					<div class="contact-form__field">
						<label for="cf-message"><?php esc_html_e( 'Message', 'estecapelli' ); ?></label>
						<textarea id="cf-message" name="lead_message" rows="4" placeholder="<?php esc_attr_e( 'Tell us about your goals, or any questions you have…', 'estecapelli' ); ?>"></textarea>
					</div>

					<input type="hidden" name="lead_source" value="contact" />
					<?php wp_nonce_field( 'estecapelli_lead', 'estecapelli_lead_nonce' ); ?>

					<div class="contact-form__actions">
						<button type="submit" class="btn btn-primary btn-lg">
							<?php esc_html_e( 'Send Request', 'estecapelli' ); ?>
							<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
						</button>
						<a class="btn btn-ghost contact-form__wa" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener">
							<?php estecapelli_icon( 'whatsapp', array( 'width' => 18, 'height' => 18 ) ); ?>
							<?php esc_html_e( 'Or chat on WhatsApp', 'estecapelli' ); ?>
						</a>
					</div>
				</form>
			</div>

			<aside class="contact-aside">
				<div class="contact-info">
					<h2 class="contact-info__title"><?php esc_html_e( 'Visit & reach us', 'estecapelli' ); ?></h2>
					<ul class="contact-info__list">
						<?php if ( $address ) : ?>
							<li>
								<span class="contact-info__icon"><?php estecapelli_icon( 'map-pin', array( 'width' => 18, 'height' => 18 ) ); ?></span>
								<span><span class="contact-info__k"><?php esc_html_e( 'Address', 'estecapelli' ); ?></span><?php echo esc_html( $address ); ?></span>
							</li>
						<?php endif; ?>
						<li>
							<span class="contact-info__icon"><?php estecapelli_icon( 'phone', array( 'width' => 18, 'height' => 18 ) ); ?></span>
							<span><span class="contact-info__k"><?php esc_html_e( 'Phone', 'estecapelli' ); ?></span><a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $phone ); ?></a></span>
						</li>
						<li>
							<span class="contact-info__icon"><?php estecapelli_icon( 'mail', array( 'width' => 18, 'height' => 18 ) ); ?></span>
							<span><span class="contact-info__k"><?php esc_html_e( 'Email', 'estecapelli' ); ?></span><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></span>
						</li>
						<li>
							<span class="contact-info__icon"><?php estecapelli_icon( 'calendar', array( 'width' => 18, 'height' => 18 ) ); ?></span>
							<span>
								<span class="contact-info__k"><?php esc_html_e( 'Working hours', 'estecapelli' ); ?></span>
								<?php esc_html_e( 'Monday – Saturday: 09:00 – 18:00 (GMT+3)', 'estecapelli' ); ?><br />
								<?php esc_html_e( 'Sunday: Closed', 'estecapelli' ); ?>
							</span>
						</li>
					</ul>

					<div class="contact-info__socials">
						<?php foreach ( (array) ( $contact['socials'] ?? array() ) as $s ) : ?>
							<a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $s['label'] ); ?>">
								<?php estecapelli_icon( $s['icon'], array( 'width' => 18, 'height' => 18 ) ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="contact-next">
					<h3 class="contact-next__title"><?php esc_html_e( 'What happens next?', 'estecapelli' ); ?></h3>
					<ol class="contact-next__steps">
						<li><span class="contact-next__num">1</span><?php esc_html_e( 'We review your request and reach out to understand your goals.', 'estecapelli' ); ?></li>
						<li><span class="contact-next__num">2</span><?php esc_html_e( 'You receive a free, personalised treatment plan and quote.', 'estecapelli' ); ?></li>
						<li><span class="contact-next__num">3</span><?php esc_html_e( 'We arrange your dates, travel and stay — and welcome you to Istanbul.', 'estecapelli' ); ?></li>
					</ol>
				</div>
			</aside>

		</div>
	</section>

	<!-- Free photo analysis -->
	<section class="contact-photos">
		<div class="shell contact-photos__shell">
			<div class="contact-photos__icon" aria-hidden="true"><?php estecapelli_icon( 'image', array( 'width' => 30, 'height' => 30 ) ); ?></div>
			<div class="contact-photos__copy">
				<h2 class="contact-photos__title"><?php esc_html_e( 'Send your photos, get a free analysis', 'estecapelli' ); ?></h2>
				<p class="contact-photos__text"><?php esc_html_e( 'Share a few photos of your hair, face or smile and our specialists will assess your case and recommend the right approach — at no cost and with no obligation.', 'estecapelli' ); ?></p>
			</div>
			<a class="btn btn-accent btn-lg contact-photos__cta" href="<?php echo esc_url( $wa_photos ); ?>" target="_blank" rel="noopener">
				<?php estecapelli_icon( 'whatsapp', array( 'width' => 18, 'height' => 18 ) ); ?>
				<?php esc_html_e( 'Send Photos on WhatsApp', 'estecapelli' ); ?>
			</a>
		</div>
	</section>

	<!-- Speak your language -->
	<section class="contact-langs">
		<div class="shell">
			<h2 class="contact-langs__title"><?php esc_html_e( 'Talk to us in your own language', 'estecapelli' ); ?></h2>
			<ul class="contact-langs__list">
				<?php foreach ( $languages as $lang ) : ?>
					<li class="contact-langs__chip">
						<img class="contact-langs__flag" src="https://flagcdn.com/w40/<?php echo esc_attr( $lang['cc'] ); ?>.png" srcset="https://flagcdn.com/w80/<?php echo esc_attr( $lang['cc'] ); ?>.png 2x" width="22" height="16" alt="" loading="lazy" decoding="async" />
						<span><?php echo esc_html( $lang['label'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<!-- Map -->
	<section class="contact-map">
		<div class="contact-map__frame">
			<iframe
				title="<?php esc_attr_e( 'Estecapelli clinic location', 'estecapelli' ); ?>"
				src="https://maps.google.com/maps?q=<?php echo rawurlencode( 'Estecapelli Istanbul Türkiye' ); ?>&z=12&output=embed"
				loading="lazy"
				referrerpolicy="no-referrer-when-downgrade"
				allowfullscreen></iframe>
		</div>
	</section>

</div>

<?php
get_footer();
