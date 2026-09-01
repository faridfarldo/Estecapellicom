<?php
/**
 * Tools → Homepage Pages.
 *
 * Creates the Page that stands behind each language root, links the seven
 * translations into one WPML group, and records their IDs so the front end can
 * resolve them without a slug lookup on every request.
 *
 * Deliberately additive. It never rewrites a title, SEO field or ACF value that
 * already holds something, so it is safe to press again after the client has
 * edited a homepage — the seed only fills blanks.
 *
 * Unlike the legacy content importers this is not gated behind
 * ESTECAPELLI_ENABLE_CONTENT_MUTATIONS: the pages have to exist before Rank
 * Math has anything to write into, and the operation creates records rather
 * than overwriting authored content.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** WPML's own element type for a page — 'post_page' on a standard install. */
function estecapelli_home_page_element_type() {
	return (string) apply_filters( 'wpml_element_type', 'page' );
}

add_action( 'admin_menu', 'estecapelli_register_home_pages_setup' );
/** Register the screen under Tools. */
function estecapelli_register_home_pages_setup() {
	add_management_page(
		__( 'Homepage Pages', 'estecapelli' ),
		__( 'Homepage Pages', 'estecapelli' ),
		'manage_options',
		'estecapelli-home-pages',
		'estecapelli_render_home_pages_setup'
	);
}

add_action( 'admin_post_estecapelli_setup_home_pages', 'estecapelli_handle_home_pages_setup' );
/** Build or repair every language Home page, then return to the screen. */
function estecapelli_handle_home_pages_setup() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to change homepage settings.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_setup_home_pages' );

	$report = estecapelli_setup_home_pages();
	set_transient( 'estecapelli_home_pages_report', $report, 5 * MINUTE_IN_SECONDS );

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-home-pages', admin_url( 'tools.php' ) ) );
	exit;
}

/**
 * Create the English Home page, translate it into the other six languages and
 * point Settings → Reading at it.
 *
 * @return array<int,array<string,string>> One row per language for the report.
 */
function estecapelli_setup_home_pages() {
	$report = array();
	$seed   = estecapelli_home_page_seed();
	$slugs  = estecapelli_home_page_slugs();
	$ids    = get_option( ESTECAPELLI_HOME_PAGE_IDS_OPTION, array() );
	$ids    = is_array( $ids ) ? $ids : array();

	// English first — it is the WPML source every other language links to.
	$source_id = estecapelli_ensure_home_page( 'en', $seed['en'], $slugs['en'], 0 );
	if ( is_wp_error( $source_id ) ) {
		return array( array( 'language' => 'en', 'status' => 'error', 'message' => $source_id->get_error_message() ) );
	}

	$ids['en'] = $source_id;
	$report[]  = array( 'language' => 'en', 'status' => 'ok', 'message' => sprintf( 'English Home page ready (#%d).', $source_id ) );

	foreach ( $seed as $language => $record ) {
		if ( 'en' === $language ) {
			continue;
		}

		$result = estecapelli_ensure_home_page( $language, $record, (string) ( $slugs[ $language ] ?? '' ), $source_id );
		if ( is_wp_error( $result ) ) {
			$report[] = array( 'language' => $language, 'status' => 'error', 'message' => $result->get_error_message() );
			continue;
		}

		$ids[ $language ] = $result;
		$report[]         = array( 'language' => $language, 'status' => 'ok', 'message' => sprintf( 'Home page ready (#%d).', $result ) );
	}

	update_option( ESTECAPELLI_HOME_PAGE_IDS_OPTION, $ids, false );

	// Make the static front page official. The front end also filters these two
	// options per language, but Settings → Reading should agree with reality and
	// Rank Math reads show_on_front to decide where homepage SEO comes from.
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $source_id );

	flush_rewrite_rules( false );

	return $report;
}

/**
 * Ensure one language's Home page exists, carries the template, and — for a
 * translation — sits in the English page's WPML group.
 *
 * @param string $language  Public language code.
 * @param array  $record    Seed row from estecapelli_home_page_seed().
 * @param string $slug      Desired post slug.
 * @param int    $source_id English Home page ID, or 0 when creating English.
 * @return int|WP_Error Page ID.
 */
function estecapelli_ensure_home_page( $language, array $record, $slug, $source_id ) {
	if ( '' === $slug ) {
		return new WP_Error( 'home_page_missing_slug', sprintf( 'No Home page slug is defined for %s.', $language ) );
	}

	$wpml_language = estecapelli_wpml_language_code( $language );
	$page_id       = estecapelli_home_page_id( $language );

	if ( ! $page_id ) {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $record['title'],
				'post_name'    => $slug,
				'post_content' => '',
				'post_parent'  => 0,
			),
			true
		);
		if ( is_wp_error( $page_id ) ) {
			return $page_id;
		}
	}

	$page_id = (int) $page_id;

	// The template is the identity marker — without it the page is invisible to
	// estecapelli_home_page_id_by_slug() and the ACF groups do not appear.
	update_post_meta( $page_id, '_wp_page_template', ESTECAPELLI_HOME_PAGE_TEMPLATE );

	estecapelli_seed_home_page_seo( $page_id, $record );

	if ( ! $source_id || ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
		return $page_id;
	}

	$linked = estecapelli_link_home_page_translation( $page_id, $source_id, $wpml_language );
	if ( is_wp_error( $linked ) ) {
		return $linked;
	}

	// WordPress suffixes a duplicate slug ("home" is both English and Italian)
	// and WPML can rename one while linking. The leaf is a 301 source either
	// way, but only the contract slug is covered by that redirect, so put it
	// back once the translation group is settled.
	estecapelli_force_home_page_slug( $page_id, $slug );

	return $page_id;
}

/**
 * Write a Home page's slug straight to the posts table.
 *
 * wp_update_post() would send it back through wp_unique_post_slug(), which is
 * what appended the suffix in the first place. Only ever called with a slug
 * from estecapelli_home_page_slugs().
 */
function estecapelli_force_home_page_slug( $page_id, $slug ) {
	global $wpdb;

	$page_id = (int) $page_id;
	if ( $slug === get_post_field( 'post_name', $page_id ) ) {
		return;
	}

	$wpdb->update( $wpdb->posts, array( 'post_name' => $slug ), array( 'ID' => $page_id ) );
	clean_post_cache( $page_id );
}

/**
 * Fill the Rank Math fields that were empty on the language roots.
 *
 * Only blanks are written. Once someone has typed a title or description in the
 * SEO panel, re-running the tool leaves it alone.
 */
function estecapelli_seed_home_page_seo( $page_id, array $record ) {
	$fields = array(
		'rank_math_title'         => $record['seo_title'],
		'rank_math_description'   => $record['description'],
		'rank_math_focus_keyword' => $record['keyword'],
	);

	foreach ( $fields as $meta_key => $value ) {
		if ( '' === (string) get_post_meta( $page_id, $meta_key, true ) ) {
			update_post_meta( $page_id, $meta_key, $value );
		}
	}
}

/**
 * Put a translated Home page in the English page's WPML translation group.
 *
 * Mirrors the linking the page importers do: set the language details, then
 * force the language slot, because a stale or duplicated row in WPML's
 * translations table otherwise leaves the page orphaned from its source.
 *
 * @return true|WP_Error
 */
function estecapelli_link_home_page_translation( $page_id, $source_id, $wpml_language ) {
	$source_details = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => (int) $source_id,
			'element_type' => 'page',
		)
	);

	$trid            = is_object( $source_details ) ? (int) $source_details->trid : 0;
	$source_language = is_object( $source_details ) ? (string) $source_details->language_code : '';
	if ( ! $trid || 'en' !== $source_language ) {
		return new WP_Error( 'home_page_unlinked_source', 'The English Home page has no WPML translation group yet.' );
	}

	// A previous run, or a hand-made translation, may already hold this slot
	// with a different page. Clear it so the slot can be claimed cleanly.
	$existing = estecapelli_wpml_group_element_id_raw( $trid, estecapelli_home_page_element_type(), $wpml_language );
	if ( $existing && (int) $existing !== (int) $page_id ) {
		estecapelli_wpml_delete_relationship_raw( $existing, estecapelli_home_page_element_type(), $trid, $wpml_language );
	}

	do_action(
		'wpml_set_element_language_details',
		array(
			'element_id'           => (int) $page_id,
			'element_type'         => estecapelli_home_page_element_type(),
			'trid'                 => $trid,
			'language_code'        => $wpml_language,
			'source_language_code' => 'en',
			'check_duplicates'     => false,
		)
	);
	delete_post_meta( $page_id, '_icl_lang_duplicate_of' );

	if ( ! estecapelli_wpml_replace_language_slot_raw( $page_id, estecapelli_home_page_element_type(), $trid, $wpml_language, 'en' ) ) {
		$reason = function_exists( 'estecapelli_wpml_last_slot_error' ) ? estecapelli_wpml_last_slot_error() : '';
		return new WP_Error( 'home_page_link_failed', 'The WPML link could not be rebuilt' . ( $reason ? ' — ' . $reason : '.' ) );
	}

	if ( ! estecapelli_wpml_element_matches_raw( $page_id, estecapelli_home_page_element_type(), $trid, $wpml_language ) ) {
		return new WP_Error( 'home_page_link_verification_failed', 'WPML did not retain the link to the English Home page.' );
	}

	return true;
}

/** Render the setup screen. */
function estecapelli_render_home_pages_setup() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$report = get_transient( 'estecapelli_home_pages_report' );
	delete_transient( 'estecapelli_home_pages_report' );

	$seed  = estecapelli_home_page_seed();
	$slugs = estecapelli_home_page_slugs();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Homepage Pages', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Every language root (/en/, /fr/, /it/ …) is drawn by the theme, not by a page, which is why Rank Math had no title or meta description to edit for any of them. This creates one real Page per language behind those roots.', 'estecapelli' ); ?></p>
		<p><?php esc_html_e( 'The layout does not change: front-page.php still renders the homepage. What each page adds is a per-language SEO panel and a per-language copy of the Homepage Content fields.', 'estecapelli' ); ?></p>
		<p><strong><?php esc_html_e( 'Safe to run more than once.', 'estecapelli' ); ?></strong> <?php esc_html_e( 'Existing pages are reused, and an SEO field that already holds text is never overwritten.', 'estecapelli' ); ?></p>

		<?php if ( is_array( $report ) ) : ?>
			<?php foreach ( $report as $row ) : ?>
				<div class="notice notice-<?php echo 'ok' === $row['status'] ? 'success' : 'error'; ?> inline">
					<p><code><?php echo esc_html( $row['language'] ); ?></code> — <?php echo esc_html( $row['message'] ); ?></p>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:1100px;margin-top:1rem;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Language', 'estecapelli' ); ?></th>
					<th><?php esc_html_e( 'Root URL', 'estecapelli' ); ?></th>
					<th><?php esc_html_e( 'Page', 'estecapelli' ); ?></th>
					<th><?php esc_html_e( 'SEO title', 'estecapelli' ); ?></th>
					<th><?php esc_html_e( 'Meta description', 'estecapelli' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $seed as $language => $record ) : ?>
					<?php
					$page_id     = estecapelli_home_page_id( $language );
					$seo_title   = $page_id ? (string) get_post_meta( $page_id, 'rank_math_title', true ) : '';
					$description = $page_id ? (string) get_post_meta( $page_id, 'rank_math_description', true ) : '';
					?>
					<tr>
						<td><code><?php echo esc_html( $language ); ?></code></td>
						<td><code><?php echo esc_html( '/' . $language . '/' ); ?></code></td>
						<td>
							<?php if ( $page_id ) : ?>
								<a href="<?php echo esc_url( (string) get_edit_post_link( $page_id ) ); ?>"><?php esc_html_e( 'Edit', 'estecapelli' ); ?></a>
								<code><?php echo esc_html( $slugs[ $language ] ?? '' ); ?></code>
							<?php else : ?>
								<span style="color:#777;"><?php esc_html_e( 'Not created yet', 'estecapelli' ); ?></span>
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( $seo_title ?: $record['seo_title'] ); ?><?php echo $seo_title ? '' : ' <em>(' . esc_html__( 'will be seeded', 'estecapelli' ) . ')</em>'; ?></td>
						<td><?php echo esc_html( $description ?: $record['description'] ); ?><?php echo $description ? '' : ' <em>(' . esc_html__( 'will be seeded', 'estecapelli' ) . ')</em>'; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1.5rem;">
			<input type="hidden" name="action" value="estecapelli_setup_home_pages">
			<?php wp_nonce_field( 'estecapelli_setup_home_pages' ); ?>
			<button type="submit" class="button button-primary button-hero"><?php esc_html_e( 'Create / repair homepage pages', 'estecapelli' ); ?></button>
		</form>
	</div>
	<?php
}
