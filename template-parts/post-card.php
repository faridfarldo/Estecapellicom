<?php
/**
 * Shared blog post card — used in the blog index, archives and related posts.
 *
 * Expects the loop to be set up (uses the current post).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$cats = get_the_category();
$cat  = ! empty( $cats ) ? $cats[0] : null;
$mins = max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) );
?>
<article <?php post_class( 'post-card' ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'large', array( 'class' => 'post-card__img', 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); ?>
		<?php else : ?>
			<span class="post-card__fallback" aria-hidden="true"><?php estecapelli_icon( 'book-open', array( 'width' => 40, 'height' => 40 ) ); ?></span>
		<?php endif; ?>
		<span class="post-card__wash" aria-hidden="true"></span>
		<?php if ( $cat ) : ?>
			<span class="post-card__cat">
				<?php estecapelli_icon( 'tag', array( 'width' => 12, 'height' => 12 ) ); ?>
				<?php echo esc_html( $cat->name ); ?>
			</span>
		<?php endif; ?>
	</a>

	<div class="post-card__body">
		<div class="post-card__meta">
			<span class="post-card__meta-item">
				<?php estecapelli_icon( 'calendar', array( 'width' => 14, 'height' => 14, 'class' => 'post-card__meta-icon' ) ); ?>
				<?php echo esc_html( get_the_date() ); ?>
			</span>
			<span class="post-card__sep" aria-hidden="true">·</span>
			<span class="post-card__meta-item"><?php echo esc_html( sprintf( /* translators: %d: minutes */ _n( '%d min read', '%d min read', $mins, 'estecapelli' ), $mins ) ); ?></span>
		</div>

		<h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>

		<span class="post-card__cta">
			<?php esc_html_e( 'Read more', 'estecapelli' ); ?>
			<?php estecapelli_icon( 'arrow-right', array( 'width' => 14, 'height' => 14, 'class' => 'post-card__arrow' ) ); ?>
		</span>
	</div>
</article>
