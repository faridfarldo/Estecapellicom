<?php
/**
 * Polish Page Repair — replace the wrong stored text on Polish pages, and
 * nothing else, so the render-time override can be deleted for good.
 *
 * Why this exists rather than the Polish Content Importer:
 *
 * The WPML diagnostic showed the wiring is already correct — the Polish post
 * exists, is registered as pl, is linked into the English trid group, and holds
 * the right slug. Only its stored text is Turkish. The importer would repair all
 * of that anyway: it rewrites the slug, the parent, the post status, the WPML
 * relationship rows and the ACF rows, and it is unlocked by the same constant
 * that unlocks thirty other importers including "Re-import ALL Pages". None of
 * that is needed here, and all of it is risk.
 *
 * So this tool writes exactly two things, on the Polish post only:
 *
 *   1. post_title  — only when it differs from the reviewed title
 *   2. page_sections
 *
 * It never touches post_name, post_parent, post_status, menu_order, the WPML
 * tables, the English post, or any other language.
 *
 * The rows it writes are the page's OWN current rows with the reviewed Polish
 * text laid over them, using the importers' strict overlay rules: a key absent
 * from the translation keeps its stored value. Every image, attachment id and
 * URL already on that page therefore survives byte for byte — the write only
 * moves text. If the stored structure does not match the translation the
 * overlay refuses and nothing is written at all.
 *
 * Enable deliberately, and only for as long as the repair takes, in
 * wp-config.php:
 *
 *   define( 'ESTECAPELLI_ENABLE_PL_PAGE_REPAIR', true );
 *
 * Its own constant on purpose: ESTECAPELLI_ENABLE_CONTENT_MUTATIONS would put
 * the bulk importers one misclick away, which is exactly what we are avoiding.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** English source slugs this tool may repair, mapped to their Polish JSON. */
function estecapelli_pl_repair_pages() {
	return array(
		'hair-transplant',
		'tricholab',
		'pre-hair-transplant-period',
		'post-hair-transplant-period',
		'plastic-surgery',
		'dental-treatment',
		'about-us',
		'our-team',
		'our-doctors',
		'before-after',
		'contact',
	);
}

/**
 * Lay reviewed translation values over stored ACF data.
 *
 * A local copy of the importers' overlay, deliberately not required from them:
 * those files register their own admin screens behind the bulk-mutation lock.
 * The rules are theirs, unchanged — a key the translation does not mention is
 * left exactly as stored, and any structural disagreement is an error rather
 * than a guess.
 *
 * @param array  $base    Stored value.
 * @param array  $overlay Reviewed translation value.
 * @param string $path    Diagnostic path.
 * @return array|WP_Error
 */
function estecapelli_pl_repair_overlay( array $base, array $overlay, $path = 'page_sections' ) {
	foreach ( $overlay as $key => $value ) {
		$current_path = $path . '/' . $key;

		if ( ! array_key_exists( $key, $base ) ) {
			return new WP_Error( 'pl_repair_unknown_field', sprintf( 'The stored page has no field %s, so it cannot be overlaid.', $current_path ) );
		}
		if ( 'acf_fc_layout' === $key && $base[ $key ] !== $value ) {
			return new WP_Error( 'pl_repair_layout_mismatch', sprintf( 'Section layout does not match at %s (stored "%s", reviewed "%s").', $path, (string) $base[ $key ], (string) $value ) );
		}

		if ( is_array( $value ) ) {
			if ( ! is_array( $base[ $key ] ) ) {
				return new WP_Error( 'pl_repair_type_mismatch', sprintf( 'Structure mismatch at %s.', $current_path ) );
			}
			$merged = estecapelli_pl_repair_overlay( $base[ $key ], $value, $current_path );
			if ( is_wp_error( $merged ) ) {
				return $merged;
			}
			$base[ $key ] = $merged;
		} else {
			if ( is_array( $base[ $key ] ) ) {
				return new WP_Error( 'pl_repair_type_mismatch', sprintf( 'Value mismatch at %s.', $current_path ) );
			}
			$base[ $key ] = $value;
		}
	}

	return $base;
}

/** Reviewed Polish translation for one English page slug. */
function estecapelli_pl_repair_translation( $source_slug ) {
	$file = get_template_directory() . '/inc/data/translations/pl/pages/' . $source_slug . '.json';
	if ( ! is_readable( $file ) ) {
		return new WP_Error( 'pl_repair_no_translation', sprintf( 'No reviewed Polish file for %s.', $source_slug ) );
	}

	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! is_array( $data ) || empty( $data['title'] ) || empty( $data['sections'] ) || ! is_array( $data['sections'] ) ) {
		return new WP_Error( 'pl_repair_bad_translation', sprintf( 'The reviewed Polish file for %s is incomplete.', $source_slug ) );
	}

	return $data;
}

/**
 * Everything the screen and the writer need for one page, or a WP_Error saying
 * precisely why the page must not be touched.
 *
 * Nothing here writes. It is the same set of checks the writer runs again
 * immediately before it saves.
 *
 * @param string $source_slug English page slug.
 * @return array|WP_Error
 */
function estecapelli_pl_repair_plan( $source_slug ) {
	if ( ! in_array( $source_slug, estecapelli_pl_repair_pages(), true ) ) {
		return new WP_Error( 'pl_repair_out_of_scope', 'That page is not in this tool’s list.' );
	}

	$translation = estecapelli_pl_repair_translation( $source_slug );
	if ( is_wp_error( $translation ) ) {
		return $translation;
	}

	$source_id = function_exists( 'estecapelli_source_post_id' )
		? (int) estecapelli_source_post_id( $source_slug, 'page' )
		: 0;
	if ( ! $source_id ) {
		return new WP_Error( 'pl_repair_no_source', sprintf( 'No published English page with the slug %s.', $source_slug ) );
	}

	$details = apply_filters(
		'wpml_element_language_details',
		null,
		array( 'element_id' => $source_id, 'element_type' => 'page' )
	);
	$trid    = is_object( $details ) ? (int) ( $details->trid ?? 0 ) : (int) ( $details['trid'] ?? 0 );
	$en_lang = is_object( $details ) ? (string) ( $details->language_code ?? '' ) : (string) ( $details['language_code'] ?? '' );
	if ( ! $trid || 'en' !== $en_lang ) {
		return new WP_Error( 'pl_repair_source_unlinked', 'The English page is not registered in WPML as English.' );
	}

	$pl_id = (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, 'pl' );
	if ( ! $pl_id || $pl_id === $source_id ) {
		return new WP_Error( 'pl_repair_no_polish_post', 'WPML has no Polish translation linked for this page. This tool will not create one.' );
	}

	$pl_post = get_post( $pl_id );
	if ( ! $pl_post instanceof WP_Post || 'page' !== $pl_post->post_type ) {
		return new WP_Error( 'pl_repair_bad_polish_post', sprintf( 'The linked Polish record #%d is not a page.', $pl_id ) );
	}
	if ( 'publish' !== $pl_post->post_status ) {
		return new WP_Error( 'pl_repair_not_published', sprintf( 'The Polish page #%d is %s, not published.', $pl_id, $pl_post->post_status ) );
	}

	// Confirm from WPML's own record, not just the object-id filter.
	$pl_details = apply_filters(
		'wpml_element_language_details',
		null,
		array( 'element_id' => $pl_id, 'element_type' => 'page' )
	);
	$pl_lang = is_object( $pl_details ) ? (string) ( $pl_details->language_code ?? '' ) : (string) ( $pl_details['language_code'] ?? '' );
	if ( 'pl' !== $pl_lang ) {
		return new WP_Error( 'pl_repair_wrong_language', sprintf( 'Post #%d is registered as "%s", not Polish. Refusing to write.', $pl_id, $pl_lang ?: 'no language' ) );
	}

	$stored = function_exists( 'get_field' ) ? get_field( 'page_sections', $pl_id ) : null;
	$stored = is_array( $stored ) ? $stored : array();

	$title_matches = trim( (string) $pl_post->post_title ) === trim( (string) $translation['title'] );

	// Stored rows are the base, so every image and id already on the page is
	// carried through untouched and only text moves.
	if ( $stored ) {
		$merged = estecapelli_pl_repair_overlay( $stored, $translation['sections'] );
		if ( is_wp_error( $merged ) ) {
			return $merged;
		}
		$media_preserved = true;
	} else {
		$merged          = $translation['sections'];
		$media_preserved = false;
	}

	return array(
		'source_slug'     => $source_slug,
		'source_id'       => $source_id,
		'pl_id'           => $pl_id,
		'pl_slug'         => $pl_post->post_name,
		'stored_title'    => (string) $pl_post->post_title,
		'reviewed_title'  => (string) $translation['title'],
		'title_matches'   => $title_matches,
		'stored_rows'     => count( $stored ),
		'reviewed_rows'   => count( $translation['sections'] ),
		'sections'        => $merged,
		'sections_change' => $stored !== $merged,
		'media_preserved' => $media_preserved,
	);
}

/**
 * Write one page. Two fields, one post.
 *
 * @param string $source_slug English page slug.
 * @return string|WP_Error Human-readable summary of what changed.
 */
function estecapelli_pl_repair_run( $source_slug ) {
	$plan = estecapelli_pl_repair_plan( $source_slug );
	if ( is_wp_error( $plan ) ) {
		return $plan;
	}
	if ( $plan['title_matches'] && ! $plan['sections_change'] ) {
		return sprintf( '%s — already correct, nothing written.', $source_slug );
	}

	$changed = array();

	if ( ! $plan['title_matches'] ) {
		$updated = wp_update_post(
			array( 'ID' => $plan['pl_id'], 'post_title' => $plan['reviewed_title'] ),
			true
		);
		if ( is_wp_error( $updated ) ) {
			return $updated;
		}
		$changed[] = 'title';
	}

	if ( $plan['sections_change'] ) {
		if ( ! function_exists( 'update_field' ) ) {
			return new WP_Error( 'pl_repair_no_acf', 'ACF is not available, so the sections cannot be written.' );
		}
		update_field( 'page_sections', $plan['sections'], $plan['pl_id'] );

		// Read back rather than trust the write.
		$saved = get_field( 'page_sections', $plan['pl_id'] );
		if ( ! is_array( $saved ) || count( $saved ) !== count( $plan['sections'] ) ) {
			return new WP_Error( 'pl_repair_not_saved', sprintf( 'The sections did not save for %s. Restore from your backup before retrying.', $source_slug ) );
		}
		$changed[] = 'sections';
	}

	clean_post_cache( $plan['pl_id'] );

	return sprintf(
		'%s — post #%d updated (%s).',
		$source_slug,
		$plan['pl_id'],
		implode( ' + ', $changed )
	);
}

add_action( 'admin_menu', 'estecapelli_register_pl_page_repair' );
/** Register the repair screen under Tools. */
function estecapelli_register_pl_page_repair() {
	add_management_page(
		__( 'Estecapelli — Polish Page Repair', 'estecapelli' ),
		__( 'Polish Page Repair', 'estecapelli' ),
		'manage_options',
		'estecapelli-pl-page-repair',
		'estecapelli_render_pl_page_repair'
	);
}

add_action( 'admin_post_estecapelli_pl_page_repair', 'estecapelli_handle_pl_page_repair' );
/** Run one page, or every page that needs it. */
function estecapelli_handle_pl_page_repair() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_pl_page_repair' );

	$slug    = isset( $_POST['page_slug'] ) ? sanitize_key( wp_unslash( $_POST['page_slug'] ) ) : '';
	$targets = ( 'all' === $slug ) ? estecapelli_pl_repair_pages() : array( $slug );

	$done   = array();
	$failed = array();
	foreach ( $targets as $target ) {
		$result = estecapelli_pl_repair_run( $target );
		if ( is_wp_error( $result ) ) {
			$failed[] = $target . ': ' . $result->get_error_message();
			continue;
		}
		$done[] = $result;
	}

	set_transient( 'estecapelli_pl_repair_done', $done, 60 );
	set_transient( 'estecapelli_pl_repair_failed', $failed, 60 );

	wp_safe_redirect( admin_url( 'tools.php?page=estecapelli-pl-page-repair' ) );
	exit;
}

/** Render the repair screen. */
function estecapelli_render_pl_page_repair() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$done   = get_transient( 'estecapelli_pl_repair_done' );
	$failed = get_transient( 'estecapelli_pl_repair_failed' );
	delete_transient( 'estecapelli_pl_repair_done' );
	delete_transient( 'estecapelli_pl_repair_failed' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Polish Page Repair', 'estecapelli' ); ?></h1>
		<p class="description" style="max-width:820px;">
			<?php esc_html_e( 'Writes the reviewed Polish title and section text onto the Polish page, and nothing else. Slugs, parents, publish status, WPML links, the English page and every other language are left untouched. Images and attachment ids already on the page are preserved: the stored rows are the base and only text is laid over them.', 'estecapelli' ); ?>
		</p>

		<?php if ( ! empty( $done ) ) : ?>
			<div class="notice notice-success"><p><strong><?php esc_html_e( 'Repaired', 'estecapelli' ); ?></strong></p>
				<ul style="list-style:disc;margin-left:1.25rem;"><?php foreach ( (array) $done as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?></ul>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $failed ) ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'Not written', 'estecapelli' ); ?></strong></p>
				<ul style="list-style:disc;margin-left:1.25rem;"><?php foreach ( (array) $failed as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?></ul>
			</div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:1100px;margin-top:1rem;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'English page', 'estecapelli' ); ?></th>
					<th><?php esc_html_e( 'Polish post', 'estecapelli' ); ?></th>
					<th><?php esc_html_e( 'Stored title', 'estecapelli' ); ?></th>
					<th><?php esc_html_e( 'Reviewed title', 'estecapelli' ); ?></th>
					<th><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
					<th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$needs_repair = 0;
				foreach ( estecapelli_pl_repair_pages() as $source_slug ) :
					$plan     = estecapelli_pl_repair_plan( $source_slug );
					$blocked  = is_wp_error( $plan );
					$repair   = ! $blocked && ( ! $plan['title_matches'] || $plan['sections_change'] );
					$needs_repair += $repair ? 1 : 0;
					?>
					<tr>
						<td><code><?php echo esc_html( $source_slug ); ?></code></td>
						<td><?php echo $blocked ? '—' : '#' . (int) $plan['pl_id'] . ' · <code>' . esc_html( $plan['pl_slug'] ) . '</code>'; // phpcs:ignore ?></td>
						<td><?php echo $blocked ? '—' : esc_html( $plan['stored_title'] ); ?></td>
						<td><?php echo $blocked ? '—' : esc_html( $plan['reviewed_title'] ); ?></td>
						<td>
							<?php if ( $blocked ) : ?>
								<span style="color:#b32d2e;font-weight:600;"><?php esc_html_e( 'Blocked', 'estecapelli' ); ?></span><br>
								<span class="description"><?php echo esc_html( $plan->get_error_message() ); ?></span>
							<?php elseif ( ! $repair ) : ?>
								<span style="color:#1a7f37;font-weight:600;"><?php esc_html_e( 'Already correct', 'estecapelli' ); ?></span>
							<?php else : ?>
								<span style="color:#8a6d00;font-weight:600;"><?php esc_html_e( 'Needs repair', 'estecapelli' ); ?></span><br>
								<span class="description">
									<?php
									printf(
										/* translators: 1: stored row count, 2: reviewed row count */
										esc_html__( '%1$d stored rows, %2$d reviewed rows.', 'estecapelli' ),
										(int) $plan['stored_rows'],
										(int) $plan['reviewed_rows']
									);
									echo ' ';
									echo $plan['media_preserved']
										? esc_html__( 'Images preserved from the stored rows.', 'estecapelli' )
										: esc_html__( 'No stored rows — reviewed rows will be written as they are.', 'estecapelli' );
									?>
								</span>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( $repair ) : ?>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0;">
									<input type="hidden" name="action" value="estecapelli_pl_page_repair">
									<input type="hidden" name="page_slug" value="<?php echo esc_attr( $source_slug ); ?>">
									<?php wp_nonce_field( 'estecapelli_pl_page_repair' ); ?>
									<button type="submit" class="button button-primary"><?php esc_html_e( 'Repair this page', 'estecapelli' ); ?></button>
								</form>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ( $needs_repair > 1 ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1.5rem;">
				<input type="hidden" name="action" value="estecapelli_pl_page_repair">
				<input type="hidden" name="page_slug" value="all">
				<?php wp_nonce_field( 'estecapelli_pl_page_repair' ); ?>
				<button type="submit" class="button button-primary button-hero">
					<?php
					printf(
						/* translators: %d: number of pages */
						esc_html__( 'Repair all %d pages that need it', 'estecapelli' ),
						(int) $needs_repair
					);
					?>
				</button>
				<p class="description"><?php esc_html_e( 'Pages already correct are skipped, not rewritten.', 'estecapelli' ); ?></p>
			</form>
		<?php endif; ?>

		<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'When it is done', 'estecapelli' ); ?></h2>
		<p style="max-width:820px;">
			<?php esc_html_e( 'Once every row reads "Already correct", remove ESTECAPELLI_ENABLE_PL_PAGE_REPAIR from wp-config.php. The render-time override switches itself off per page as each title is repaired, so the site never shows the old content in between, and inc/pl-content-revisions.php can then be deleted.', 'estecapelli' ); ?>
		</p>
	</div>
	<?php
}
