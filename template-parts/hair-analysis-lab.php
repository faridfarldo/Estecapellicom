<?php
/**
 * Homepage: Hair Analysis Lab — "Analysis With Estecapelli AI".
 *
 * Deliberately OFF-THEME: this section uses its own golden / brown palette
 * and red CTAs (scoped under .hal) so it stands clearly apart from the rest
 * of the site.
 *
 * Flow: the visitor first picks one of two options —
 *   1. Self-assessment: they mark their own hair-loss area, or
 *   2. Photo analysis: they upload photos for the AI to review.
 * The forms below are PLACEHOLDERS; exact fields/layout to be finalised.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="hal" aria-labelledby="hal-title" data-hal>

	<div class="hal__bg" aria-hidden="true">
		<span class="hal__glow hal__glow--a"></span>
		<span class="hal__glow hal__glow--b"></span>
		<span class="hal__grid"></span>
	</div>

	<div class="shell hal__shell">

		<header class="hal__head">
			<span class="hal__eyebrow">
				<span class="hal__eyebrow-mark" aria-hidden="true"></span>
				<?php esc_html_e( 'HAIR ANALYSIS LAB', 'estecapelli' ); ?>
			</span>
			<h2 id="hal-title" class="hal__title">
				<?php esc_html_e( 'Analysis With Estecapelli', 'estecapelli' ); ?>
				<span class="hal__title-ai">AI</span>
			</h2>
			<p class="hal__lead"><?php esc_html_e( 'Get a personalised hair-loss assessment. Choose how you would like to begin.', 'estecapelli' ); ?></p>
		</header>

		<!-- Step 1: choose an option -->
		<div class="hal__options" data-hal-options>

			<button type="button" class="hal__option" data-hal-pick="self">
				<span class="hal__option-icon" aria-hidden="true">
					<?php estecapelli_icon( 'target', array( 'width' => 26, 'height' => 26 ) ); ?>
				</span>
				<span class="hal__option-title"><?php esc_html_e( 'I know my hair-loss area', 'estecapelli' ); ?></span>
				<span class="hal__option-desc"><?php esc_html_e( 'Mark the areas where you are losing hair and tell us about your goals.', 'estecapelli' ); ?></span>
				<span class="hal__option-cta">
					<?php esc_html_e( 'Start self-assessment', 'estecapelli' ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
				</span>
			</button>

			<button type="button" class="hal__option" data-hal-pick="photos">
				<span class="hal__option-icon" aria-hidden="true">
					<?php estecapelli_icon( 'image', array( 'width' => 26, 'height' => 26 ) ); ?>
				</span>
				<span class="hal__option-title"><?php esc_html_e( 'Analyse my photos with AI', 'estecapelli' ); ?></span>
				<span class="hal__option-desc"><?php esc_html_e( 'Upload a few photos of your hair and our AI prepares your assessment.', 'estecapelli' ); ?></span>
				<span class="hal__option-cta">
					<?php esc_html_e( 'Upload photos', 'estecapelli' ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
				</span>
			</button>

		</div>

		<!-- Step 2a: self-assessment form (PLACEHOLDER) -->
		<div class="hal__panel" data-hal-form="self" hidden>
			<button type="button" class="hal__back" data-hal-back>
				<?php estecapelli_icon( 'chevron-left', array( 'width' => 16, 'height' => 16 ) ); ?>
				<?php esc_html_e( 'Back to options', 'estecapelli' ); ?>
			</button>

			<form class="hal__form" action="#" novalidate>

				<fieldset class="hal__fieldset">
					<legend class="hal__legend"><?php esc_html_e( 'Tap the areas where you are losing hair', 'estecapelli' ); ?></legend>

					<div class="hal__zonemap" data-hal-zonemap>
						<img class="hal__zoneimg" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/hair-analysis/scalp-zones.webp' ); ?>" alt="<?php esc_attr_e( 'Scalp zones', 'estecapelli' ); ?>" loading="lazy" decoding="async" />
						<svg class="hal__zonesvg" viewBox="0 0 667 799" aria-hidden="true">
							<polygon class="hal__zone hal__zone--5" data-hal-zone="Zone 5" points="224.01,307.39 226.90,267.89 228.52,244.12 233.38,222.03 246.52,199.94 262.91,182.90 283.98,170.36 318.19,164.18 343.58,163.62 371.86,170.92 401.21,185.71 423.00,211.73 429.66,227.08 434.52,244.12 436.14,264.52 435.96,304.40 397.07,313.20 393.83,250.30 392.38,226.33 373.84,191.89 351.15,175.97 321.62,172.60 292.26,184.59 269.39,218.47 269.39,249.92 282.36,279.31 301.81,295.79 323.60,302.53 351.15,300.84 378.16,281.93 392.92,249.92 396.53,313.20 383.56,313.76 367.35,317.13 348.63,319.00 327.02,319.56 312.61,320.87 296.94,320.12 269.21,316.38 241.66,312.26 231.04,310.39"></polygon>
							<ellipse class="hal__zone hal__zone--6" data-hal-zone="Zone 6" cx="330.26" cy="238.69" rx="61.41" ry="63.84"></ellipse>
							<polygon class="hal__zone hal__zone--4" data-hal-zone="Zone 4" points="222.57,309.27 212.49,382.28 232.66,366.36 252.11,355.32 265.61,350.45 272.63,349.14 282.18,347.27 297.48,343.71 307.39,342.40 323.78,341.47 339.80,341.28 363.93,342.96 385.72,346.71 402.83,353.45 424.62,362.62 441.73,373.67 450.01,380.40 446.59,355.32 439.38,330.23 432.90,305.52 393.29,313.57 360.33,318.44 294.96,320.87 262.55,316.57 234.82,311.70"></polygon>
							<polygon class="hal__zone hal__zone--2" data-hal-zone="Zone 2" points="213.39,382.46 233.38,384.71 268.85,419.91 292.62,435.26 319.99,438.06 342.86,439.38 361.77,435.07 376.00,430.01 387.52,421.59 395.99,417.28 412.01,406.24 451.27,382.28 438.12,370.67 415.25,358.69 391.30,349.89 365.91,405.68 344.66,412.98 314.59,412.98 293.34,401.37 286.32,379.84 279.30,362.06 272.27,349.89 252.83,355.32 238.06,363.37 221.49,375.54"></polygon>
							<polygon class="hal__zone hal__zone--3" data-hal-zone="Zone 3" points="272.99,348.95 282.90,367.86 289.38,387.52 292.44,396.69 297.66,402.87 312.43,411.48 327.74,413.17 345.38,412.04 362.49,408.30 368.97,400.25 374.38,383.77 377.80,376.47 392.02,350.08 380.14,345.02 347.19,341.28 324.14,341.28 312.97,342.59 294.60,344.46"></polygon>
							<polygon class="hal__zone hal__zone--1" data-hal-zone="Zone 1" points="398.87,415.04 410.75,407.74 451.99,383.21 448.93,408.86 430.20,405.30"></polygon>
							<polygon class="hal__zone hal__zone--1" data-hal-zone="Zone 1" points="212.67,383.21 214.47,405.30 223.83,402.68 236.26,403.99 248.68,407.74 263.99,413.73 239.86,391.82 229.24,385.65"></polygon>
						</svg>
					</div>

					<input type="hidden" name="hal_zones" data-hal-zones value="" />

					<div class="hal__zonesummary" data-hal-summary>
						<span class="hal__zonesummary-label"><?php esc_html_e( 'Selected areas', 'estecapelli' ); ?></span>
						<span class="hal__zonesummary-empty"><?php esc_html_e( 'No area selected yet.', 'estecapelli' ); ?></span>
						<span class="hal__zonesummary-list" data-hal-zonelist></span>
					</div>
				</fieldset>

				<div class="hal__row">
					<label class="hal__field">
						<span class="hal__label"><?php esc_html_e( 'Full name', 'estecapelli' ); ?></span>
						<input type="text" name="hal_name" placeholder="<?php esc_attr_e( 'Your name', 'estecapelli' ); ?>" />
					</label>
					<label class="hal__field">
						<span class="hal__label"><?php esc_html_e( 'WhatsApp / Email', 'estecapelli' ); ?></span>
						<input type="text" name="hal_contact" placeholder="<?php esc_attr_e( 'How can we reach you?', 'estecapelli' ); ?>" />
					</label>
				</div>

				<button type="button" class="hal__submit"><?php esc_html_e( 'Get my analysis', 'estecapelli' ); ?></button>
			</form>
		</div>

		<!-- Step 2b: photo-upload form (PLACEHOLDER) -->
		<div class="hal__panel" data-hal-form="photos" hidden>
			<button type="button" class="hal__back" data-hal-back>
				<?php estecapelli_icon( 'chevron-left', array( 'width' => 16, 'height' => 16 ) ); ?>
				<?php esc_html_e( 'Back to options', 'estecapelli' ); ?>
			</button>

			<form class="hal__form" action="#" novalidate>
				<p class="hal__form-note"><?php esc_html_e( 'Placeholder — fields to be finalised.', 'estecapelli' ); ?></p>

				<div class="hal__uploads">
					<?php
					$shots = array(
						__( 'Front', 'estecapelli' ),
						__( 'Top', 'estecapelli' ),
						__( 'Left side', 'estecapelli' ),
						__( 'Right side', 'estecapelli' ),
					);
					foreach ( $shots as $shot ) :
						?>
						<label class="hal__upload">
							<?php estecapelli_icon( 'image', array( 'width' => 22, 'height' => 22 ) ); ?>
							<span class="hal__upload-label"><?php echo esc_html( $shot ); ?></span>
							<input type="file" name="hal_photo[]" accept="image/*" />
						</label>
					<?php endforeach; ?>
				</div>

				<div class="hal__row">
					<label class="hal__field">
						<span class="hal__label"><?php esc_html_e( 'Full name', 'estecapelli' ); ?></span>
						<input type="text" name="hal_name" placeholder="<?php esc_attr_e( 'Your name', 'estecapelli' ); ?>" />
					</label>
					<label class="hal__field">
						<span class="hal__label"><?php esc_html_e( 'WhatsApp / Email', 'estecapelli' ); ?></span>
						<input type="text" name="hal_contact" placeholder="<?php esc_attr_e( 'How can we reach you?', 'estecapelli' ); ?>" />
					</label>
				</div>

				<button type="button" class="hal__submit"><?php esc_html_e( 'Send for AI analysis', 'estecapelli' ); ?></button>
			</form>
		</div>

	</div>
</section>
