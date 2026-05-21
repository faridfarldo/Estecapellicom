<?php
/**
 * Section: FAQ accordion — native <details>/<summary>, no JS.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow = $section['eyebrow'] ?? '';
$title   = $section['title']   ?? '';
$lead    = $section['lead']    ?? '';
$items   = $section['items']   ?? array();

if ( empty( $items ) ) { return; }
?>

<section class="t-faq">
	<div class="shell">

		<header class="t-faq__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-faq__eyebrow">
					<span class="t-faq__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>
			<h2 class="t-faq__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $lead ) : ?>
				<p class="t-faq__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<div class="t-faq__list">
			<?php foreach ( $items as $i => $item ) :
				if ( empty( $item['question'] ) || empty( $item['answer'] ) ) { continue; }
				?>
				<details class="t-faq__item"<?php echo 0 === $i ? ' open' : ''; ?>>
					<summary class="t-faq__q">
						<span class="t-faq__q-text"><?php echo esc_html( $item['question'] ); ?></span>
						<span class="t-faq__q-icon" aria-hidden="true">
							<?php estecapelli_icon( 'chevron-down', array( 'width' => 18, 'height' => 18 ) ); ?>
						</span>
					</summary>
					<div class="t-faq__a">
						<?php echo wp_kses_post( $item['answer'] ); ?>
					</div>
				</details>
			<?php endforeach; ?>
		</div>

	</div>
</section>
