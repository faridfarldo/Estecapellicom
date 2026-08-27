<?php
/**
 * WPML page diagnostic — read-only report under Tools → WPML Page Diagnostic.
 *
 * Answers one question: for a handful of English pages, is each language's
 * translation actually LINKED into the same WPML translation group (trid), or
 * does WPML still show a "+" because the translated post is either missing or
 * orphaned under its own trid?
 *
 * It changes NOTHING — every query is a SELECT / read filter. Built to explain
 * why Pre/Post Hair Transplant show "+" in every language column even though the
 * translated pages exist.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Languages we expect a translation in, and the exact translated leaf slugs. */
function estecapelli_wpml_diag_targets() {
	return array(
		'hair-transplant' => array(
			'label' => 'Hair Transplant (parent)',
			'slugs' => array(
				'fr' => 'greffe-de-cheveux',
				'it' => 'trapianto-di-capelli',
				'es' => 'trasplante-de-cabello',
				'pt' => 'transplante-capilar',
				'pl' => 'przeszczep-wlosow',
				'tr' => 'sac-ekimi',
			),
		),
		'pre-hair-transplant-period' => array(
			'label' => 'Pre Hair Transplant Period',
			'slugs' => array(
				'fr' => 'periode-pre-transplantation-capillaire',
				'it' => 'periodo-pre-trapianto-di-capelli',
				'es' => 'periodo-previo-al-trasplante-capilar',
				'pt' => 'periodo-pre-transplante-capilar',
				'pl' => 'okres-przed-przeszczepem-wlosow',
				'tr' => 'sac-ekim-oncesi-donem',
			),
		),
		'post-hair-transplant-period' => array(
			'label' => 'Post Hair Transplant Period',
			'slugs' => array(
				'fr' => 'periode-post-greffe-de-cheveux',
				'it' => 'periodo-post-trapianto-di-capelli',
				'es' => 'periodo-posterior-al-trasplante-capilar',
				'pt' => 'periodo-pos-transplante-capilar',
				'pl' => 'okres-po-przeszczepie-wlosow',
				'tr' => 'sac-ekimi-sonrasi-donem',
			),
		),
		// The rest of the page set, so the report covers every page the Polish
		// importer can act on. Only the Polish leaf slug is recorded for these;
		// a language with no slug on record is reported as such, not as broken.
		'tricholab'        => array( 'label' => 'TrichoLab',        'slugs' => array( 'pl' => 'tricholab' ) ),
		'plastic-surgery'  => array( 'label' => 'Plastic Surgery',  'slugs' => array( 'pl' => 'chirurgia-plastyczna' ) ),
		'dental-treatment' => array( 'label' => 'Dental Treatment', 'slugs' => array( 'pl' => 'leczenie-stomatologiczne' ) ),
		'about-us'         => array( 'label' => 'About Us',         'slugs' => array( 'pl' => 'o-nas' ) ),
		'our-team'         => array( 'label' => 'Our Team',         'slugs' => array( 'pl' => 'nasz-zespol' ) ),
		'our-doctors'      => array( 'label' => 'Our Doctors',      'slugs' => array( 'pl' => 'nasi-lekarze' ) ),
		'medical-director' => array( 'label' => 'Chief Physician',  'slugs' => array( 'pl' => 'dyrektor-medyczny' ) ),
		'before-after'     => array( 'label' => 'Before & After',   'slugs' => array( 'pl' => 'przed-po' ) ),
		'contact'          => array( 'label' => 'Contact',          'slugs' => array( 'pl' => 'kontakt' ) ),
		'blog'             => array( 'label' => 'Blog',             'slugs' => array( 'pl' => 'blog', 'ro' => 'blog' ) ),
	);
}

/** All languages the report walks, in column order. */
function estecapelli_wpml_diag_languages() {
	return array( 'en', 'fr', 'it', 'es', 'pt', 'pl', 'tr', 'ro' );
}

/** Raw post row for a slug in a given post_type, ignoring request filtering. */
function estecapelli_wpml_diag_raw_posts_by_slug( $slug ) {
	global $wpdb;
	if ( '' === (string) $slug ) {
		return array();
	}
	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_status, post_name, post_title
			 FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = 'page' AND post_status <> 'trash'
			 ORDER BY ID ASC",
			$slug
		)
	);
}

/** WPML language details (trid, language_code, source_language_code) for a post. */
function estecapelli_wpml_diag_details( $post_id ) {
	$details = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => (int) $post_id,
			'element_type' => 'page',
		)
	);
	if ( is_object( $details ) ) {
		return array(
			'trid'            => (int) ( $details->trid ?? 0 ),
			'language_code'   => (string) ( $details->language_code ?? '' ),
			'source_language' => (string) ( $details->source_language_code ?? '' ),
		);
	}
	if ( is_array( $details ) ) {
		return array(
			'trid'            => (int) ( $details['trid'] ?? 0 ),
			'language_code'   => (string) ( $details['language_code'] ?? '' ),
			'source_language' => (string) ( $details['source_language_code'] ?? '' ),
		);
	}
	return array( 'trid' => 0, 'language_code' => '', 'source_language' => '' );
}

/** Every element WPML has grouped under one trid, keyed by language. */
function estecapelli_wpml_diag_group( $trid ) {
	global $wpdb;
	if ( ! $trid ) {
		return array();
	}
	$table = $wpdb->prefix . 'icl_translations';
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT element_id, language_code, source_language_code
			 FROM {$table}
			 WHERE trid = %d AND element_type = 'post_page'
			 ORDER BY language_code ASC",
			$trid
		)
	);
	$out = array();
	foreach ( (array) $rows as $row ) {
		$out[ $row->language_code ] = (int) $row->element_id;
	}
	return $out;
}

add_action( 'admin_menu', 'estecapelli_register_wpml_page_diagnostic' );
function estecapelli_register_wpml_page_diagnostic() {
	add_management_page(
		__( 'Estecapelli — WPML Page Diagnostic', 'estecapelli' ),
		__( 'WPML Page Diagnostic', 'estecapelli' ),
		'manage_options',
		'estecapelli-wpml-page-diagnostic',
		'estecapelli_render_wpml_page_diagnostic'
	);
}

function estecapelli_render_wpml_page_diagnostic() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$wpml_on   = defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'WPML_VERSION' );
	$languages = estecapelli_wpml_diag_languages();
	$targets   = estecapelli_wpml_diag_targets();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'WPML Page Diagnostic', 'estecapelli' ); ?></h1>
		<p class="description" style="max-width:820px;">
			<?php esc_html_e( 'Read-only. For each English page below it shows the WPML translation group (trid), then which languages are linked into that group versus a translated page that exists but sits outside the group (the cause of a stray "+" in the WPML column).', 'estecapelli' ); ?>
		</p>

		<?php if ( ! $wpml_on ) : ?>
			<div class="notice notice-error"><p><?php esc_html_e( 'WPML is not active — nothing to diagnose.', 'estecapelli' ); ?></p></div>
			</div>
			<?php
			return;
		endif;

		foreach ( $targets as $source_slug => $target ) :
			// By slug, not by path. get_page_by_path() wants the full hierarchical
			// path, so a child page such as hair-transplant/pre-hair-transplant-period
			// was reported as a missing English source while it was live and
			// perfectly fine. This is the same raw lookup the importers use to
			// resolve their source page, so the report now agrees with them.
			$source_id = function_exists( 'estecapelli_source_post_id' )
				? (int) estecapelli_source_post_id( $source_slug, 'page' )
				: 0;
			// Never pass 0 to get_post(), which falls back to the global post.
			$source = $source_id ? get_post( $source_id ) : get_page_by_path( $source_slug, OBJECT, 'page' );
			?>
			<h2 style="margin-top:2rem;"><?php echo esc_html( $target['label'] ); ?> <code><?php echo esc_html( $source_slug ); ?></code></h2>

			<?php if ( ! $source ) : ?>
				<div class="notice notice-error inline"><p><?php esc_html_e( 'No English page found for this slug. The source page itself is missing — every language will show "+" until it is imported.', 'estecapelli' ); ?></p></div>
				<?php continue; endif; ?>

			<?php
			$source_details = estecapelli_wpml_diag_details( $source->ID );
			$trid           = $source_details['trid'];
			$group          = estecapelli_wpml_diag_group( $trid );
			?>
			<p style="margin:.25rem 0;">
				<strong><?php esc_html_e( 'English source:', 'estecapelli' ); ?></strong>
				#<?php echo (int) $source->ID; ?>
				&middot; <?php esc_html_e( 'WPML language:', 'estecapelli' ); ?> <code><?php echo esc_html( $source_details['language_code'] ?: '—' ); ?></code>
				&middot; trid: <code><?php echo $trid ? (int) $trid : '—'; ?></code>
				<?php if ( ! $trid || 'en' !== $source_details['language_code'] ) : ?>
					<span style="color:#b32d2e;font-weight:600;">
						&nbsp;⚠ <?php esc_html_e( 'The source is not registered in WPML as English — translations cannot link until this is fixed.', 'estecapelli' ); ?>
					</span>
				<?php endif; ?>
			</p>

			<table class="widefat striped" style="max-width:1000px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Language', 'estecapelli' ); ?></th>
						<th><?php esc_html_e( 'Linked in group?', 'estecapelli' ); ?></th>
						<th><?php esc_html_e( 'Linked post', 'estecapelli' ); ?></th>
						<th><?php esc_html_e( 'Translated page exists (by slug)?', 'estecapelli' ); ?></th>
						<th><?php esc_html_e( 'Verdict', 'estecapelli' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $languages as $lang ) :
						if ( 'en' === $lang ) {
							continue;
						}
						$linked_id     = isset( $group[ $lang ] ) ? (int) $group[ $lang ] : 0;
						$linked_post   = $linked_id ? get_post( $linked_id ) : null;
						$expected_slug = $target['slugs'][ $lang ] ?? '';
						$raw_matches   = $expected_slug ? estecapelli_wpml_diag_raw_posts_by_slug( $expected_slug ) : array();

						// Only a page that is ITSELF in this language can be this
						// language's orphan. Some slugs are deliberately identical
						// across languages — "blog" and "tricholab" are — so the raw
						// lookup always returns something, and judging on that alone
						// reported the Romanian blog page as an orphan to relink when
						// no Romanian page existed at all. The whole list is still
						// printed in the next column, because seeing which languages
						// do hold the slug is the useful part.
						$own_language_matches = array();
						foreach ( $raw_matches as $raw_match ) {
							$raw_details = estecapelli_wpml_diag_details( (int) $raw_match->ID );
							$raw_lang    = strtolower( (string) $raw_details['language_code'] );
							if ( $raw_lang === strtolower( (string) estecapelli_wpml_language_code( $lang ) ) ) {
								$own_language_matches[] = $raw_match;
							}
						}

						// Verdict logic.
						if ( $linked_id && $linked_post ) {
							$verdict = '<span style="color:#1a7f37;font-weight:600;">' . esc_html__( 'OK — linked', 'estecapelli' ) . '</span>';
						} elseif ( $own_language_matches ) {
							$verdict = '<span style="color:#b32d2e;font-weight:600;">' . esc_html__( 'ORPHAN — page exists but NOT linked (this is the "+")', 'estecapelli' ) . '</span>';
						} elseif ( ! $expected_slug ) {
							$verdict = '<span style="color:#8a6d00;">' . esc_html__( 'No expected slug on record for this language', 'estecapelli' ) . '</span>';
						} else {
							$verdict = '<span style="color:#8a6d00;">' . esc_html__( 'MISSING — no translated page at all', 'estecapelli' ) . '</span>';
						}
						?>
						<tr>
							<td><code><?php echo esc_html( $lang ); ?></code></td>
							<td><?php echo $linked_id ? esc_html__( 'Yes', 'estecapelli' ) : esc_html__( 'No (+)', 'estecapelli' ); ?></td>
							<td>
								<?php if ( $linked_post ) : ?>
									#<?php echo (int) $linked_post->ID; ?> &middot; <code><?php echo esc_html( $linked_post->post_name ); ?></code> &middot; <?php echo esc_html( $linked_post->post_status ); ?>
								<?php else : ?>
									—
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $raw_matches ) : ?>
									<?php foreach ( $raw_matches as $m ) : ?>
										#<?php echo (int) $m->ID; ?> &middot; <code><?php echo esc_html( $m->post_name ); ?></code>
										<?php $md = estecapelli_wpml_diag_details( $m->ID ); ?>
										(<?php esc_html_e( 'lang', 'estecapelli' ); ?> <code><?php echo esc_html( $md['language_code'] ?: '—' ); ?></code>, trid <code><?php echo $md['trid'] ? (int) $md['trid'] : '—'; ?></code>)<br>
									<?php endforeach; ?>
									<?php if ( $expected_slug ) : ?><span class="description"><?php echo esc_html( $expected_slug ); ?></span><?php endif; ?>
								<?php else : ?>
									<?php echo $expected_slug ? esc_html__( 'No', 'estecapelli' ) . ' — <code>' . esc_html( $expected_slug ) . '</code>' : '—'; // phpcs:ignore ?>
								<?php endif; ?>
							</td>
							<td><?php echo wp_kses_post( $verdict ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endforeach; ?>

		<h2 style="margin-top:2rem;"><?php esc_html_e( 'How to read this', 'estecapelli' ); ?></h2>
		<ul style="max-width:820px; list-style:disc; margin-left:1.25rem;">
			<li><strong>OK — linked</strong>: <?php esc_html_e( 'the translation sits in the English page\'s trid group; WPML shows the pencil, not the "+".', 'estecapelli' ); ?></li>
			<li><strong>ORPHAN</strong>: <?php esc_html_e( 'a translated page exists but under a different (or no) trid, so WPML does not know it is a translation and shows "+". Re-running that language\'s page importer relinks it to the English trid.', 'estecapelli' ); ?></li>
			<li><strong>MISSING</strong>: <?php esc_html_e( 'no translated page exists for that language yet.', 'estecapelli' ); ?></li>
			<li><?php esc_html_e( 'If the English source line shows the ⚠ warning, fix that first: no language can link until the source is a registered English page with a trid.', 'estecapelli' ); ?></li>
		</ul>
	</div>
	<?php
}
