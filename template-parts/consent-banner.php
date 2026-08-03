<?php
/**
 * Cookie consent banner — the visitor-facing half of Google Consent Mode v2.
 *
 * Rendered on every front-end page and hidden by default. assets/js/consent.js
 * reveals it only when there is no valid stored choice, so a returning visitor
 * never sees it twice; the choice itself is replayed to Google *before* the GTM
 * container loads (see inc/analytics.php).
 *
 * Markup is printed server-side rather than injected by JS so the dialog is in
 * the accessibility tree from the first paint and appearing costs no layout
 * shift. Nothing here loads a third-party script.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$consent_privacy = function_exists( 'estecapelli_translated_page_url' ) ? estecapelli_translated_page_url( 'privacy-policy' ) : '';
$consent_cookies = function_exists( 'estecapelli_translated_page_url' ) ? estecapelli_translated_page_url( 'cookie-policy' ) : '';
?>
<div class="consent" id="consent-banner" role="dialog" aria-modal="false" aria-labelledby="consent-title" aria-describedby="consent-text" hidden>
	<div class="consent__card">

		<div class="consent__intro">
			<h2 class="consent__title" id="consent-title"><?php esc_html_e( 'We value your privacy', 'estecapelli' ); ?></h2>
			<p class="consent__text" id="consent-text">
				<?php esc_html_e( 'We use cookies to keep the site working, to understand how it is used and to improve it. You choose what we may store — your choice can be changed at any time.', 'estecapelli' ); ?>
				<?php if ( $consent_privacy ) : ?>
					<a class="consent__link" href="<?php echo esc_url( $consent_privacy ); ?>"><?php esc_html_e( 'Privacy Policy', 'estecapelli' ); ?></a>
				<?php endif; ?>
				<?php if ( $consent_cookies ) : ?>
					<a class="consent__link" href="<?php echo esc_url( $consent_cookies ); ?>"><?php esc_html_e( 'Cookie Policy', 'estecapelli' ); ?></a>
				<?php endif; ?>
			</p>
		</div>

		<!-- Granular choices, collapsed until the visitor asks for them. Keeping
		     them behind "Customise" keeps the first view to a single sentence and
		     three equally weighted buttons — no dark pattern. -->
		<div class="consent__options" data-consent-options hidden>
			<label class="consent__option consent__option--locked">
				<input type="checkbox" checked disabled />
				<span>
					<strong><?php esc_html_e( 'Strictly necessary', 'estecapelli' ); ?></strong>
					<span class="consent__option-note"><?php esc_html_e( 'Required for the site, its forms and the security check to work. Always on.', 'estecapelli' ); ?></span>
				</span>
			</label>

			<label class="consent__option">
				<input type="checkbox" data-consent-toggle="analytics" />
				<span>
					<strong><?php esc_html_e( 'Analytics', 'estecapelli' ); ?></strong>
					<span class="consent__option-note"><?php esc_html_e( 'Anonymous statistics about which pages are read and where visitors struggle, so we can improve them.', 'estecapelli' ); ?></span>
				</span>
			</label>

			<label class="consent__option">
				<input type="checkbox" data-consent-toggle="marketing" />
				<span>
					<strong><?php esc_html_e( 'Marketing', 'estecapelli' ); ?></strong>
					<span class="consent__option-note"><?php esc_html_e( 'Lets us measure our advertising and show you more relevant information.', 'estecapelli' ); ?></span>
				</span>
			</label>
		</div>

		<div class="consent__actions">
			<button type="button" class="btn btn-primary consent__btn" data-consent-accept><?php esc_html_e( 'Accept all', 'estecapelli' ); ?></button>
			<button type="button" class="btn btn-ghost consent__btn" data-consent-reject><?php esc_html_e( 'Reject all', 'estecapelli' ); ?></button>
			<button type="button" class="consent__more" data-consent-customise aria-expanded="false" aria-controls="consent-banner"><?php esc_html_e( 'Customise', 'estecapelli' ); ?></button>
			<button type="button" class="btn btn-primary consent__btn" data-consent-save hidden><?php esc_html_e( 'Save my choices', 'estecapelli' ); ?></button>
		</div>

	</div>
</div>
