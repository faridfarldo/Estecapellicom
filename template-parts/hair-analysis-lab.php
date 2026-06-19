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
				<p class="hal__form-note"><?php esc_html_e( 'Placeholder — fields to be finalised.', 'estecapelli' ); ?></p>

				<fieldset class="hal__fieldset">
					<legend class="hal__legend"><?php esc_html_e( 'Where are you losing hair?', 'estecapelli' ); ?></legend>
					<div class="hal__chips">
						<?php
						$areas = array(
							__( 'Receding hairline', 'estecapelli' ),
							__( 'Temples', 'estecapelli' ),
							__( 'Crown', 'estecapelli' ),
							__( 'Mid-scalp', 'estecapelli' ),
							__( 'Overall thinning', 'estecapelli' ),
						);
						foreach ( $areas as $area ) :
							?>
							<label class="hal__chip">
								<input type="checkbox" name="hal_area[]" value="<?php echo esc_attr( $area ); ?>" />
								<span><?php echo esc_html( $area ); ?></span>
							</label>
						<?php endforeach; ?>
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
