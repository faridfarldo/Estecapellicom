<?php
/**
 * Archive — category, tag and author listings of blog posts.
 *
 * @package Estecapelli
 */

get_header();

$arch_title = wp_strip_all_tags( get_the_archive_title() );
// Drop WP's "Category:" / "Tag:" prefix for a cleaner heading.
$clean_title = preg_replace( '/^[^:]+:\s*/', '', $arch_title );
$arch_desc   = get_the_archive_description();
?>

<div class="blog-page">

	<header class="blog-hero">
		<div class="shell blog-hero__shell">
			<span class="blog-hero__eyebrow">
				<span class="blog-hero__eyebrow-mark" aria-hidden="true"></span>
				<?php esc_html_e( 'From the Journal', 'estecapelli' ); ?>
			</span>
			<h1 class="blog-hero__title"><?php echo esc_html( $clean_title ); ?></h1>
			<?php if ( $arch_desc ) : ?>
				<div class="blog-hero__lead"><?php echo wp_kses_post( $arch_desc ); ?></div>
			<?php else : ?>
				<p class="blog-hero__lead"><?php esc_html_e( 'Articles from the Estecapelli journal.', 'estecapelli' ); ?></p>
			<?php endif; ?>
			<a class="blog-hero__all" href="<?php echo esc_url( estecapelli_indexed_url( '/en/blog' ) ); ?>">
				<?php estecapelli_icon( 'chevron-left', array( 'width' => 14, 'height' => 14 ) ); ?>
				<?php esc_html_e( 'All articles', 'estecapelli' ); ?>
			</a>
		</div>
	</header>

	<div class="shell blog-body">
		<?php if ( have_posts() ) : ?>
			<div class="blog-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					load_template( locate_template( 'template-parts/post-card.php' ), false );
				endwhile;
				?>
			</div>

			<nav class="blog-pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'estecapelli' ); ?>">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => '‹ ' . __( 'Newer', 'estecapelli' ),
						'next_text' => __( 'Older', 'estecapelli' ) . ' ›',
					)
				);
				?>
			</nav>
		<?php else : ?>
			<div class="blog-empty">
				<span class="blog-empty__icon" aria-hidden="true"><?php estecapelli_icon( 'book-open', array( 'width' => 36, 'height' => 36 ) ); ?></span>
				<p><?php esc_html_e( 'No articles found here yet.', 'estecapelli' ); ?></p>
			</div>
		<?php endif; ?>
	</div>

</div>

<?php
get_footer();
