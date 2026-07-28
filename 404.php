<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * This file used to be an empty stub, so a missing URL rendered a completely
 * blank page — no header, no navigation, no way back. Anyone arriving from a
 * stale Google result or a mistyped address was simply lost, and because the
 * template never called get_header() the analytics container never loaded
 * either, so those hits were invisible.
 *
 * Every link below is built with estecapelli_indexed_url(), which resolves the
 * localized route for the CURRENT language — a Turkish visitor is offered
 * /tr/sac-ekimi, not the English path.
 *
 * @package Estecapelli
 */

get_header();

$e404_language = function_exists( 'estecapelli_indexed_language_code' ) ? estecapelli_indexed_language_code() : 'en';

/** Localized URL for an English source route. */
$e404_url = static function ( $path ) use ( $e404_language ) {
	return function_exists( 'estecapelli_indexed_url' )
		? estecapelli_indexed_url( $path, $e404_language )
		: home_url( $path );
};

$e404_links = array(
	array( 'path' => '/en/hair-transplant',  'label' => __( 'Hair Transplant', 'estecapelli' ),  'icon' => 'hair' ),
	array( 'path' => '/en/plastic-surgery',  'label' => __( 'Plastic Surgery', 'estecapelli' ),  'icon' => 'face' ),
	array( 'path' => '/en/dental-treatment', 'label' => __( 'Dental Treatment', 'estecapelli' ), 'icon' => 'tooth' ),
	array( 'path' => '/en/before-after',     'label' => __( 'Before & After', 'estecapelli' ),   'icon' => 'sparkles' ),
	array( 'path' => '/en/about-us',         'label' => __( 'About Us', 'estecapelli' ),         'icon' => 'medical-plus' ),
	array( 'path' => '/en/blog',             'label' => __( 'Blog', 'estecapelli' ),             'icon' => 'link' ),
);
?>

<section class="shell py-20 md:py-28">
	<div class="max-w-3xl mx-auto text-center">

		<p class="text-7xl md:text-9xl font-bold tracking-tight text-brand-950/15 leading-none select-none" aria-hidden="true">404</p>

		<h1 class="mt-4 text-3xl md:text-4xl font-bold tracking-tight text-brand-950">
			<?php esc_html_e( 'This page could not be found', 'estecapelli' ); ?>
		</h1>

		<p class="mt-4 text-base md:text-lg text-brand-900/70 leading-relaxed">
			<?php esc_html_e( 'The page you are looking for may have been moved or no longer exists. You can head back to the homepage, or pick one of the sections below.', 'estecapelli' ); ?>
		</p>

		<div class="mt-8 flex flex-wrap items-center justify-center gap-3">
			<a class="btn btn-accent btn-lg" href="<?php echo esc_url( $e404_url( '/en' ) ); ?>">
				<?php esc_html_e( 'Back to homepage', 'estecapelli' ); ?>
			</a>
			<a class="btn btn-ghost btn-lg" href="<?php echo esc_url( $e404_url( '/en/contact' ) ); ?>">
				<?php esc_html_e( 'Contact us', 'estecapelli' ); ?>
			</a>
		</div>

		<nav class="mt-14 text-left" aria-label="<?php esc_attr_e( 'Site sections', 'estecapelli' ); ?>">
			<h2 class="text-sm font-semibold uppercase tracking-wider text-brand-900/50 text-center">
				<?php esc_html_e( 'Popular sections', 'estecapelli' ); ?>
			</h2>
			<ul class="mt-6 grid gap-3 sm:grid-cols-2">
				<?php foreach ( $e404_links as $e404_link ) : ?>
					<li>
						<a
							class="flex items-center gap-3 rounded-xl border border-brand-950/10 px-4 py-3 text-brand-950 transition hover:border-brand-600/40 hover:bg-brand-50"
							href="<?php echo esc_url( $e404_url( $e404_link['path'] ) ); ?>"
						>
							<?php estecapelli_icon( $e404_link['icon'], array( 'width' => 20, 'height' => 20, 'class' => 'shrink-0 text-brand-600' ) ); ?>
							<span class="font-medium"><?php echo esc_html( $e404_link['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

	</div>
</section>

<?php
get_footer();
