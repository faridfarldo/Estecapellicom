<?php
/**
 * Lead popup — a site-wide modal consultation form (name, phone, email, note).
 *
 * Rendered once in the footer, hidden by default. Opened by main.js whenever a
 * visitor clicks any "Free Consultation" / Contact CTA (see assets/js/main.js).
 * Submits over AJAX to the `estecapelli_lead` action so the visitor never
 * leaves the page; the hidden tracking fields (page url/title, UTM) are filled
 * by JS at open time, and `lead_source` is set to "popup" for attribution.
 *
 * Progressive enhancement: without JS this markup stays hidden and the CTA
 * simply navigates to the contact page, so nothing breaks.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wa_url = function_exists( 'estecapelli_whatsapp_url' ) ? estecapelli_whatsapp_url() : '#';
?>
<div class="lead-popup" id="lead-popup" hidden>
	<div class="lead-popup__overlay" data-lead-close></div>

	<div class="lead-popup__dialog" role="dialog" aria-modal="true" aria-labelledby="lead-popup-title">
		<button type="button" class="lead-popup__close" data-lead-close aria-label="<?php esc_attr_e( 'Close', 'estecapelli' ); ?>">
			<?php estecapelli_icon( 'close', array( 'width' => 22, 'height' => 22 ) ); ?>
		</button>

		<div class="lead-popup__head">
			<span class="lead-popup__eyebrow">
				<span class="lead-popup__eyebrow-mark" aria-hidden="true"></span>
				<?php esc_html_e( 'Free Consultation', 'estecapelli' ); ?>
			</span>
			<h2 class="lead-popup__title" id="lead-popup-title"><?php esc_html_e( 'Book your free consultation', 'estecapelli' ); ?></h2>
			<p class="lead-popup__lead"><?php esc_html_e( 'Leave your details and a medical consultant will get back to you shortly — no obligation.', 'estecapelli' ); ?></p>
		</div>

		<form class="lead-popup__form contact-form" method="post" action="<?php echo esc_url( home_url( '/en/contact' ) ); ?>" novalidate>
			<div class="contact-form__field">
				<label for="pop-name"><?php esc_html_e( 'Full name', 'estecapelli' ); ?> <span aria-hidden="true">*</span></label>
				<input id="pop-name" type="text" name="lead_name" required autocomplete="name" placeholder="<?php esc_attr_e( 'Name and surname', 'estecapelli' ); ?>" />
			</div>
			<div class="contact-form__field">
				<label for="pop-phone"><?php esc_html_e( 'Phone', 'estecapelli' ); ?> <span aria-hidden="true">*</span></label>
				<input id="pop-phone" class="js-intl-phone" type="tel" name="lead_phone" required autocomplete="tel" inputmode="tel" placeholder="<?php esc_attr_e( 'Phone number', 'estecapelli' ); ?>" />
			</div>
			<div class="contact-form__field">
				<label for="pop-email"><?php esc_html_e( 'Email', 'estecapelli' ); ?></label>
				<input id="pop-email" type="email" name="lead_email" autocomplete="email" placeholder="<?php esc_attr_e( 'you@example.com', 'estecapelli' ); ?>" />
			</div>
			<div class="contact-form__field">
				<label for="pop-note"><?php esc_html_e( 'Note', 'estecapelli' ); ?></label>
				<textarea id="pop-note" name="lead_message" rows="3" placeholder="<?php esc_attr_e( 'Tell us about your goals, or any questions you have…', 'estecapelli' ); ?>"></textarea>
			</div>

			<!-- Tracking — lead_page_url/title + utm_* are filled by JS at open time. -->
			<input type="hidden" name="lead_source" value="popup" />
			<input type="hidden" name="lead_page_url" value="" />
			<input type="hidden" name="lead_page_title" value="" />
			<input type="hidden" name="utm_source" value="" />
			<input type="hidden" name="utm_medium" value="" />
			<input type="hidden" name="utm_campaign" value="" />
			<input type="hidden" name="utm_content" value="" />
			<input type="hidden" name="utm_term" value="" />
			<input type="hidden" name="lead_return" value="<?php echo esc_url( home_url( '/en/contact' ) ); ?>" />
			<?php wp_nonce_field( 'estecapelli_lead', 'estecapelli_lead_nonce' ); ?>

			<div class="lead-popup__feedback" role="status" aria-live="polite" hidden></div>

			<div class="contact-form__actions">
				<button type="submit" class="btn btn-primary btn-lg lead-popup__submit">
					<span class="lead-popup__submit-label"><?php esc_html_e( 'Request a Free Consultation', 'estecapelli' ); ?></span>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
				</button>
				<a class="btn btn-ghost contact-form__wa" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener">
					<?php estecapelli_icon( 'whatsapp', array( 'width' => 18, 'height' => 18 ) ); ?>
					<?php esc_html_e( 'Or chat on WhatsApp', 'estecapelli' ); ?>
				</a>
			</div>
		</form>
	</div>
</div>
<?php
