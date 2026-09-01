<?php
/**
 * Treatment importer — admin tool under Tools → Treatment Importer.
 *
 * Reads /inc/data/treatments-seed.php and creates or updates `treatment`
 * posts (and their treatment_category terms + ACF page_sections) from
 * that array. Idempotent: re-running an import updates the post in place
 * rather than creating duplicates.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/data/treatments-seed.php';
require_once get_template_directory() . '/inc/data/pages-seed.php';
require_once get_template_directory() . '/inc/data/doctors-seed.php';
require_once get_template_directory() . '/inc/admin/wpml-page-diagnostic.php';

add_action( 'admin_menu', 'estecapelli_register_content_importer' );
function estecapelli_register_content_importer() {
	add_management_page(
		__( 'Estecapelli — Content Importer', 'estecapelli' ),
		__( 'Estecapelli Imports', 'estecapelli' ),
		'manage_options',
		'estecapelli-treatment-importer',
		'estecapelli_render_treatments_importer'
	);
}

/**
 * Reduce an ACF image value to its attachment ID (accepts an ID or the
 * formatted array get_field returns).
 */
function estecapelli_image_to_id( $img ) {
	if ( is_array( $img ) ) {
		return (int) ( $img['ID'] ?? $img['id'] ?? 0 );
	}
	return (int) $img;
}

/**
 * Non-destructive merge for a page's flexible-content sections.
 *
 * Re-importing must NOT wipe media an editor uploaded in the panel. The seed
 * only carries text and (occasionally) theme-bundled image URLs — it leaves the
 * ACF image / gallery / video slots empty, and some optional media keys are
 * omitted entirely. For each section, keep stored media whenever the seed
 * value is empty or absent instead of overwriting it with nothing.
 *
 * We read the EXISTING value formatted (get_field default) — for flexible
 * content the unformatted value is just an array of layout-name strings, not the
 * row data — then reduce any image back to its attachment ID before writing, so
 * update_field stores it correctly.
 *
 * Rows are matched by LAYOUT ORDINAL, not by raw position: a seed row finds its
 * counterpart as "the Nth row of this layout". Raw position breaks the moment a
 * new section is added to the seed above an existing one — every later row would
 * then face a different layout, the merge would skip it, and the seed's empty
 * media slots would wipe the images an editor uploaded. Ordinal matching
 * survives insertions and removals anywhere in the page, and matches how
 * estecapelli_render_page_sections() pairs a translation with its source row.
 *
 * @param array $new_sections Sections from the seed.
 * @param int   $post_id      Target post.
 * @return array Merged sections safe to pass to update_field().
 */
function estecapelli_merge_preserve_media( array $new_sections, $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $new_sections;
	}
	$existing = get_field( 'page_sections', $post_id ); // formatted rows
	if ( ! is_array( $existing ) || empty( $existing ) ) {
		return $new_sections;
	}

	// Group the stored rows by layout, in stored order.
	$existing_by_layout = array();
	foreach ( $existing as $stored_row ) {
		if ( ! is_array( $stored_row ) ) {
			continue;
		}
		$stored_layout = $stored_row['acf_fc_layout'] ?? '';
		if ( '' === $stored_layout ) {
			continue;
		}
		$existing_by_layout[ $stored_layout ][] = $stored_row;
	}

	$layout_seen = array();

	foreach ( $new_sections as $i => $section ) {
		$new_layout = $section['acf_fc_layout'] ?? '';
		if ( '' === $new_layout ) {
			continue;
		}

		$ordinal                    = isset( $layout_seen[ $new_layout ] ) ? $layout_seen[ $new_layout ] : 0;
		$layout_seen[ $new_layout ] = $ordinal + 1;

		if ( empty( $existing_by_layout[ $new_layout ][ $ordinal ] ) ) {
			continue; // brand-new section — nothing stored to preserve.
		}
		$old = $existing_by_layout[ $new_layout ][ $ordinal ];

		// Preserve a single uploaded image (store its ID).
		if ( empty( $section['image'] ) && ! empty( $old['image'] ) ) {
			$id = estecapelli_image_to_id( $old['image'] );
			if ( $id ) {
				$new_sections[ $i ]['image'] = $id;
			}
		}

		// Preserve the selected media mode. This is essential when an editor has
		// changed a seed's default image block to a YouTube video or slider.
		if ( ! empty( $old['media_type'] ) ) {
			$new_sections[ $i ]['media_type'] = $old['media_type'];
		}

		// Preserve uploaded/entered media URLs and video identifiers even when
		// their optional key is absent from the seed row.
		foreach ( array( 'image_url', 'video_id', 'video_url' ) as $media_key ) {
			if ( empty( $section[ $media_key ] ) && ! empty( $old[ $media_key ] ) ) {
				$new_sections[ $i ][ $media_key ] = $old[ $media_key ];
			}
		}

		// Preserve uploaded gallery/repeater rows (before/after) when the seed
		// has none — reducing each row's image back to an attachment ID.
		if ( array_key_exists( 'items', $section ) && empty( $section['items'] ) && ! empty( $old['items'] ) && is_array( $old['items'] ) ) {
			$rows = array();
			foreach ( $old['items'] as $row ) {
				if ( is_array( $row ) && array_key_exists( 'image', $row ) ) {
					$row['image'] = estecapelli_image_to_id( $row['image'] );
				}
				$rows[] = $row;
			}
			$new_sections[ $i ]['items'] = $rows;
		}

		// Even when the seed DOES provide repeater rows, keep any per-row media an
		// editor uploaded that the seed leaves empty — the uploaded custom icon
		// (icon_file) and images — matched by position. Covers the icon+text
		// repeaters (items, stats, points).
		foreach ( array( 'items', 'stats', 'points' ) as $rep ) {
			if ( empty( $section[ $rep ] ) || ! is_array( $section[ $rep ] ) || empty( $old[ $rep ] ) || ! is_array( $old[ $rep ] ) ) {
				continue;
			}
			foreach ( $section[ $rep ] as $ri => $row ) {
				if ( ! isset( $old[ $rep ][ $ri ] ) || ! is_array( $old[ $rep ][ $ri ] ) ) {
					continue;
				}
				$oldrow = $old[ $rep ][ $ri ];
				if ( empty( $row['icon_file'] ) && ! empty( $oldrow['icon_file'] ) ) {
					$new_sections[ $i ][ $rep ][ $ri ]['icon_file'] = estecapelli_image_to_id( $oldrow['icon_file'] );
				}
				if ( empty( $row['image'] ) && ! empty( $oldrow['image'] ) ) {
					$new_sections[ $i ][ $rep ][ $ri ]['image'] = estecapelli_image_to_id( $oldrow['image'] );
				}
				foreach ( array( 'video_id', 'video_url' ) as $media_key ) {
					if ( empty( $row[ $media_key ] ) && ! empty( $oldrow[ $media_key ] ) ) {
						$new_sections[ $i ][ $rep ][ $ri ][ $media_key ] = $oldrow[ $media_key ];
					}
				}
			}
		}

		// Preserve an uploaded photo-slider gallery (array of images → IDs). The
		// seed never carries slider photos, so always keep the editor's when set.
		if ( empty( $section['slider_images'] ) && ! empty( $old['slider_images'] ) && is_array( $old['slider_images'] ) ) {
			$ids = array();
			foreach ( $old['slider_images'] as $img ) {
				$id = estecapelli_image_to_id( $img );
				if ( $id ) {
					$ids[] = $id;
				}
			}
			if ( $ids ) {
				$new_sections[ $i ]['slider_images'] = $ids;
			}
		}
	}

	return $new_sections;
}

/**
 * Find or create a page by slug + parent path; return its ID. Resolves the
 * parent_slug to a post_parent ID, walking the seed if the parent was just
 * created in the same run.
 */
function estecapelli_import_page( array $data, array &$slug_to_id ) {

	if ( empty( $data['slug'] ) || empty( $data['title'] ) ) {
		return new WP_Error( 'invalid_page', __( 'Missing slug or title.', 'estecapelli' ) );
	}

	// Resolve parent ID by slug (may be null for top-level pages).
	$parent_id = 0;
	if ( ! empty( $data['parent'] ) ) {
		if ( isset( $slug_to_id[ $data['parent'] ] ) ) {
			$parent_id = (int) $slug_to_id[ $data['parent'] ];
		} else {
			// Fall back to a DB lookup if the parent wasn't in this run.
			$existing_parent = get_page_by_path( $data['parent'], OBJECT, 'page' );
			if ( $existing_parent ) {
				$parent_id = (int) $existing_parent->ID;
			}
		}
	}

	// Look up existing by full path (e.g. about-us/our-doctors/op-dr-hasan-celik).
	$path     = $data['slug'];
	$walk_id  = $parent_id;
	$path_stack = array( $data['slug'] );
	while ( $walk_id ) {
		$walk = get_post( $walk_id );
		if ( ! $walk ) {
			break;
		}
		array_unshift( $path_stack, $walk->post_name );
		$walk_id = (int) $walk->post_parent;
	}
	$full_path = implode( '/', $path_stack );
	$existing  = get_page_by_path( $full_path, OBJECT, 'page' );

	// If the page isn't where the seed now expects it, it may have moved under a
	// new parent (e.g. TrichoLab relocated to /hair-transplant/tricholab). Fall
	// back to a unique slug match so we update-and-move the existing page instead
	// of creating a duplicate. Only reparent when the slug is unambiguous.
	if ( ! $existing ) {
		$by_slug = get_posts(
			array(
				'post_type'      => 'page',
				'name'           => $data['slug'],
				'post_status'    => 'any',
				'posts_per_page' => 2,
			)
		);
		if ( 1 === count( $by_slug ) ) {
			$existing = $by_slug[0];
		}
	}

	$post_args = array(
		'post_type'    => 'page',
		'post_title'   => $data['title'],
		'post_name'    => $data['slug'],
		'post_status'  => 'publish',
		'post_parent'  => $parent_id,
		// Most pages render from ACF sections; plain pages (e.g. legal) carry
		// their body in an optional `content` key, shown via the page.php fallback.
		'post_content' => $data['content'] ?? '',
	);

	if ( $existing ) {
		$post_args['ID'] = $existing->ID;
		$post_id         = wp_update_post( $post_args, true );
	} else {
		$post_id = wp_insert_post( $post_args, true );
	}

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	$slug_to_id[ $data['slug'] ] = $post_id;

	if ( function_exists( 'update_field' ) && ! empty( $data['sections'] ) ) {
		update_field( 'page_sections', estecapelli_merge_preserve_media( $data['sections'], $post_id ), $post_id );
	}

	return $post_id;
}

/**
 * Find or create a treatment by slug; return its ID.
 */
function estecapelli_import_treatment( array $data ) {

	if ( empty( $data['slug'] ) || empty( $data['title'] ) ) {
		return new WP_Error( 'invalid_treatment', __( 'Missing slug or title.', 'estecapelli' ) );
	}

	$existing = get_page_by_path( $data['slug'], OBJECT, 'treatment' );

	$post_args = array(
		'post_type'    => 'treatment',
		'post_title'   => $data['title'],
		'post_name'    => $data['slug'],
		'post_status'  => 'publish',
		'post_content' => '',
	);

	if ( $existing ) {
		$post_args['ID'] = $existing->ID;
		$post_id         = wp_update_post( $post_args, true );
	} else {
		$post_id = wp_insert_post( $post_args, true );
	}

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	// Assign category — create the term if it doesn't exist.
	if ( ! empty( $data['category'] ) ) {
		$term = term_exists( $data['category'], 'treatment_category' );
		if ( ! $term ) {
			$term = wp_insert_term( $data['category'], 'treatment_category' );
		}
		if ( ! is_wp_error( $term ) ) {
			$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
			wp_set_object_terms( $post_id, array( $term_id ), 'treatment_category', false );
		}
	}

	// Write ACF Flexible Content (preserving any uploaded media on re-import).
	if ( function_exists( 'update_field' ) && ! empty( $data['sections'] ) ) {
		update_field( 'page_sections', estecapelli_merge_preserve_media( $data['sections'], $post_id ), $post_id );
	}

	return $post_id;
}

/**
 * Extract the source-controlled Hero rows that may have been contaminated.
 *
 * @return array<int,array<string,mixed>>
 */
function estecapelli_english_seeded_hero_targets() {
	$targets = array();
	$groups  = array(
		'treatment' => estecapelli_treatments_seed(),
		'page'      => estecapelli_pages_seed(),
	);

	foreach ( $groups as $post_type => $items ) {
		foreach ( $items as $item ) {
			if ( empty( $item['slug'] ) || empty( $item['sections'] ) || ! is_array( $item['sections'] ) ) {
				continue;
			}

			foreach ( $item['sections'] as $section ) {
				if ( 'hero' !== ( $section['acf_fc_layout'] ?? '' ) ) {
					continue;
				}
				$targets[] = array(
					'post_type' => $post_type,
					'slug'      => $item['slug'],
					'category'  => $item['category'] ?? '',
					'hero'      => $section,
				);
				break;
			}
		}
	}

	return $targets;
}

/**
 * Load the French Hero overlay for a seeded target when one exists.
 *
 * It supplies an additional known-contamination value alongside the runtime
 * gettext dictionary. No translated value is ever written by this repair.
 *
 * @param array<string,mixed> $target Seeded target descriptor.
 * @return array<string,mixed>
 */
function estecapelli_french_hero_overlay_for_target( $target ) {
	if ( 'page' === $target['post_type'] ) {
		$folder = 'pages';
	} else {
		$folders = array(
			'Hair Transplant' => 'hair-transplant',
			'Plastic Surgery' => 'plastic-surgery',
			'Dental Treatment' => 'dental-treatment',
		);
		$folder = $folders[ $target['category'] ] ?? '';
	}

	if ( ! $folder ) {
		return array();
	}

	$file = get_template_directory() . '/inc/data/translations/fr/' . $folder . '/' . $target['slug'] . '.json';
	if ( ! is_readable( $file ) ) {
		return array();
	}

	$data = json_decode( (string) file_get_contents( $file ), true );
	if ( ! is_array( $data ) || empty( $data['sections'] ) || ! is_array( $data['sections'] ) ) {
		return array();
	}

	foreach ( $data['sections'] as $section ) {
		if ( 'hero' === ( $section['acf_fc_layout'] ?? '' ) ) {
			return $section;
		}
	}

	return array();
}

/**
 * Read one nested Hero value using the same path in seed and French overlay.
 *
 * @param array<string,mixed> $hero Hero row.
 * @param string              $path Dot-separated field path.
 * @return string
 */
function estecapelli_hero_value_at_path( $hero, $path ) {
	$value = $hero;
	foreach ( explode( '.', $path ) as $key ) {
		if ( ! is_array( $value ) || ! array_key_exists( $key, $value ) ) {
			return '';
		}
		$value = $value[ $key ];
	}

	return is_scalar( $value ) ? (string) $value : '';
}

/**
 * Repair French-contaminated Hero copy across every seeded English page.
 *
 * Only individual values that exactly equal a known French translation are
 * replaced. English editorial values that differ from the seed are preserved,
 * and no flexible-content row or media value is rewritten.
 *
 * @return array<string,mixed>|WP_Error Repair report, or a verification error.
 */
function estecapelli_repair_english_seeded_hero_copy() {
	$fr_strings = estecapelli_nav_translations()['fr'] ?? array();
	$field_map  = array(
		'eyebrow'                    => 'eyebrow',
		'title'                      => 'title',
		'lead'                       => 'lead',
		'cta_primary.label'          => 'cta_primary_label',
		'cta_secondary.label'        => 'cta_secondary_label',
	);
	$report     = array(
		'targets'        => 0,
		'changed_posts'  => 0,
		'changed_values' => 0,
		'skipped_values' => 0,
		'errors'         => array(),
	);
	$changes    = array();

	foreach ( estecapelli_english_seeded_hero_targets() as $target ) {
		$report['targets']++;
		$source_id = function_exists( 'estecapelli_source_post_id' )
			? (int) estecapelli_source_post_id( $target['slug'], $target['post_type'] )
			: 0;
		if ( ! $source_id ) {
			$report['errors'][] = sprintf( '%s/%s: published English source not found.', $target['post_type'], $target['slug'] );
			continue;
		}

		if ( defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'WPML_VERSION' ) ) {
			$details  = apply_filters(
				'wpml_element_language_details',
				null,
				array(
					'element_id'   => $source_id,
					'element_type' => $target['post_type'],
				)
			);
			$language = is_object( $details ) ? (string) ( $details->language_code ?? '' ) : (string) ( $details['language_code'] ?? '' );
			if ( 'en' !== $language ) {
				$report['errors'][] = sprintf( '%s/%s: post %d is "%s", not English.', $target['post_type'], $target['slug'], $source_id, $language ?: 'unknown' );
				continue;
			}
		}

		$layouts = get_post_meta( $source_id, 'page_sections', true );
		if ( ! is_array( $layouts ) ) {
			$report['errors'][] = sprintf( '%s/%s: no readable page_sections layout index.', $target['post_type'], $target['slug'] );
			continue;
		}
		$hero_index = array_search( 'hero', $layouts, true );
		if ( false === $hero_index ) {
			$report['errors'][] = sprintf( '%s/%s: no Hero row found.', $target['post_type'], $target['slug'] );
			continue;
		}

		$french_hero = estecapelli_french_hero_overlay_for_target( $target );
		$prefix      = 'page_sections_' . (int) $hero_index . '_';
		foreach ( $field_map as $path => $meta_suffix ) {
			$expected = estecapelli_hero_value_at_path( $target['hero'], $path );
			if ( '' === $expected ) {
				continue;
			}

			$meta_key = $prefix . $meta_suffix;
			$current  = (string) get_post_meta( $source_id, $meta_key, true );
			if ( $current === $expected ) {
				continue;
			}

			$contaminated = array();
			if ( isset( $fr_strings[ $expected ] ) ) {
				$contaminated[] = (string) $fr_strings[ $expected ];
			}
			$overlay_value = estecapelli_hero_value_at_path( $french_hero, $path );
			if ( '' !== $overlay_value ) {
				$contaminated[] = $overlay_value;
			}
			$contaminated = array_values( array_unique( array_filter( $contaminated, 'strlen' ) ) );

			if ( ! in_array( $current, $contaminated, true ) ) {
				$report['skipped_values']++;
				continue;
			}

			$changes[] = array(
				'post_id'  => $source_id,
				'post_type' => $target['post_type'],
				'slug'     => $target['slug'],
				'meta_key' => $meta_key,
				'before'   => $current,
				'after'    => $expected,
			);
		}
	}

	$backup_key = 'estecapelli_english_hero_copy_backup_20260723_v2';
	if ( $changes && false === get_option( $backup_key, false ) ) {
		add_option(
			$backup_key,
			array(
				'changes'    => $changes,
				'created_at' => current_time( 'mysql', true ),
			),
			'',
			false
		);
	}

	$changed_posts = array();
	foreach ( $changes as $change ) {
		update_post_meta( $change['post_id'], $change['meta_key'], $change['after'] );
		if ( $change['after'] !== (string) get_post_meta( $change['post_id'], $change['meta_key'], true ) ) {
			return new WP_Error(
				'english_hero_verification_failed',
				sprintf( 'Repair verification failed for %s on post %d.', $change['meta_key'], $change['post_id'] )
			);
		}
		$changed_posts[ $change['post_id'] ] = true;
		$report['changed_values']++;
	}
	foreach ( array_keys( $changed_posts ) as $post_id ) {
		clean_post_cache( $post_id );
	}
	$report['changed_posts'] = count( $changed_posts );

	return $report;
}

/** Run the site-wide surgical repair once after deployment. */
add_action( 'admin_init', 'estecapelli_maybe_repair_english_seeded_hero_copy', 75 );
function estecapelli_maybe_repair_english_seeded_hero_copy() {
	$version = '2026-07-23.2';
	if (
		$version === get_option( 'estecapelli_english_hero_copy_repair_version' ) ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_repair_english_seeded_hero_copy();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_english_hero_copy_repair_error', $result->get_error_message(), false );
		return;
	}
	if ( ! empty( $result['errors'] ) ) {
		update_option( 'estecapelli_english_hero_copy_repair_error', implode( ' ', $result['errors'] ), false );
		return;
	}

	update_option( 'estecapelli_english_hero_copy_repair_version', $version, false );
	delete_option( 'estecapelli_english_hero_copy_repair_error' );
	set_transient( 'estecapelli_english_hero_copy_repair_success', $result, 5 * MINUTE_IN_SECONDS );
}

/** Report the one-time repair outcome to an administrator. */
add_action( 'admin_notices', 'estecapelli_english_hero_copy_repair_notice' );
function estecapelli_english_hero_copy_repair_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$result = get_transient( 'estecapelli_english_hero_copy_repair_success' );
	if ( is_array( $result ) ) {
		delete_transient( 'estecapelli_english_hero_copy_repair_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html(
				sprintf(
					'English Hero repair completed across the site: %d contaminated values corrected on %d posts; %d editorial values preserved.',
					(int) $result['changed_values'],
					(int) $result['changed_posts'],
					(int) $result['skipped_values']
				)
			)
		);
		return;
	}

	$error = get_option( 'estecapelli_english_hero_copy_repair_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html__( 'Site-wide English Hero repair failed.', 'estecapelli' ),
			esc_html( $error )
		);
	}
}

/**
 * Find or create a doctor by slug; return its ID. Writes the Doctor Profile
 * ACF fields and trashes the legacy nested page it replaces (trashing frees the
 * slug, so the doctor CPT can own the same URL). The photo field is left
 * untouched so re-running an import never wipes an uploaded portrait.
 */
function estecapelli_import_doctor( array $data ) {

	if ( empty( $data['slug'] ) || empty( $data['name'] ) ) {
		return new WP_Error( 'invalid_doctor', __( 'Missing slug or name.', 'estecapelli' ) );
	}

	$existing = get_page_by_path( $data['slug'], OBJECT, 'doctor' );

	/*
	 * A corrected slug must rename the existing profile, not create a second one.
	 * get_page_by_path() only ever looks for the current slug, so a seed whose
	 * slug has been fixed would otherwise insert a duplicate and strand the
	 * original record — along with its uploaded portrait and WPML links.
	 */
	if ( ! $existing && ! empty( $data['previous_slug'] ) ) {
		$existing = get_page_by_path( $data['previous_slug'], OBJECT, 'doctor' );
	}

	$post_args = array(
		'post_type'    => 'doctor',
		'post_title'   => $data['name'],
		'post_name'    => $data['slug'],
		'post_status'  => 'publish',
		'post_content' => '',
		'menu_order'   => (int) ( $data['menu_order'] ?? 0 ),
	);

	if ( $existing ) {
		$post_args['ID'] = $existing->ID;
		$post_id         = wp_update_post( $post_args, true );
	} else {
		$post_id = wp_insert_post( $post_args, true );
	}

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	if ( function_exists( 'update_field' ) ) {
		// Non-destructive: on an existing profile only fill a field the editor
		// has left empty, so title/bio/credentials edited in the dashboard
		// survive a re-import instead of reverting to the seed. A brand-new
		// profile is still seeded in full. Mirrors the treatment importer.
		$fresh = ! $existing;

		if ( $fresh || '' === trim( (string) get_field( 'position', $post_id ) ) ) {
			update_field( 'position', $data['position'] ?? '', $post_id );
		}
		if ( $fresh || '' === trim( (string) get_field( 'bio', $post_id ) ) ) {
			update_field( 'bio', $data['bio'] ?? '', $post_id );
		}
		if ( $fresh || empty( get_field( 'credentials', $post_id ) ) ) {
			$credentials = array_map(
				static function ( $label ) {
					return array( 'label' => $label );
				},
				$data['credentials'] ?? array()
			);
			update_field( 'credentials', $credentials, $post_id );
		}
		// Theme-bundled résumé photo URL (thumbnail is left untouched). Only the
		// url fallback is seeded; an editor-uploaded résumé photo wins over it.
		update_field( 'resume_photo_url', $data['resume_photo_url'] ?? '', $post_id );
	}

	// Retire the legacy page so the content lives in one place and the doctor
	// CPT owns the URL. wp_trash_post() suffixes the old slug with __trashed,
	// freeing the path for the new profile.
	if ( ! empty( $data['old_page_path'] ) ) {
		$old = get_page_by_path( $data['old_page_path'], OBJECT, 'page' );
		if ( $old && 'trash' !== $old->post_status ) {
			wp_trash_post( $old->ID );
		}
	}

	return $post_id;
}

/**
 * Give an English treatment back to English after a translation import took it.
 *
 * A translation import could once adopt the English original when a treatment
 * keeps the same slug in every language (bbl). The adopted post was rewritten in
 * the translated language and reassigned to it, leaving its WPML group with no
 * English element: /en/<slug> stops resolving and every later import for that
 * slug is refused. The engine no longer does this; this repairs the records it
 * already damaged, then rebuilds the English copy from the seed.
 *
 * @param string $source_slug English treatment slug.
 * @return string|WP_Error Success message.
 */
function estecapelli_repair_hijacked_treatment_source( $source_slug ) {
	global $wpdb;

	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'repair_wpml_missing', 'WPML is required to repair a treatment source.' );
	}

	$seed = null;
	foreach ( estecapelli_treatments_seed() as $treatment ) {
		if ( $source_slug === ( $treatment['slug'] ?? '' ) ) {
			$seed = $treatment;
			break;
		}
	}
	if ( ! $seed ) {
		return new WP_Error( 'repair_missing_seed', sprintf( 'No English seed defines the treatment %s.', $source_slug ) );
	}

	$element_type  = apply_filters( 'wpml_element_type', 'treatment' );
	$candidate_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = 'treatment' AND post_status <> 'trash'
			 ORDER BY ID ASC",
			$source_slug
		)
	);
	$candidate_ids = array_map( 'intval', (array) $candidate_ids );
	if ( ! $candidate_ids ) {
		return new WP_Error( 'repair_missing_post', sprintf( 'No treatment post uses the slug %s.', $source_slug ) );
	}

	// Never touch a slug that still has its English post; the damage is specific.
	$languages = array();
	foreach ( $candidate_ids as $candidate_id ) {
		$details                    = apply_filters(
			'wpml_element_language_details',
			null,
			array(
				'element_id'   => $candidate_id,
				'element_type' => 'treatment',
			)
		);
		$languages[ $candidate_id ] = array(
			'language' => (string) estecapelli_it_hair_detail( $details, 'language_code' ),
			'trid'     => (int) estecapelli_it_hair_detail( $details, 'trid' ),
		);

		if ( 'en' === $languages[ $candidate_id ]['language'] ) {
			return new WP_Error(
				'repair_not_needed',
				sprintf( '%s already has an English source (ID %d). Nothing to repair.', $source_slug, $candidate_id )
			);
		}
	}

	$hijacked_id = 0;
	foreach ( $languages as $candidate_id => $state ) {
		if ( $state['trid'] && ! estecapelli_wpml_group_element_id_raw( $state['trid'], $element_type, 'en' ) ) {
			$hijacked_id = (int) $candidate_id;
			break;
		}
	}
	if ( ! $hijacked_id ) {
		return new WP_Error(
			'repair_no_candidate',
			sprintf( 'No post using the slug %s is an English original whose language was reassigned.', $source_slug )
		);
	}

	$trid          = $languages[ $hijacked_id ]['trid'];
	$taken_by      = $languages[ $hijacked_id ]['language'];
	$restore_args  = array(
		'element_id'           => $hijacked_id,
		'element_type'         => $element_type,
		'trid'                 => $trid,
		'language_code'        => 'en',
		'source_language_code' => null,
		'check_duplicates'     => false,
	);

	do_action( 'wpml_set_element_language_details', $restore_args );

	if ( ! estecapelli_wpml_element_matches_raw( $hijacked_id, $element_type, $trid, 'en' ) ) {
		$repaired = estecapelli_wpml_repair_relationship_raw( $hijacked_id, $element_type, $trid, 'en', null );
		if ( ! $repaired ) {
			return new WP_Error(
				'repair_language_failed',
				sprintf( 'WPML refused to return post %d to English. %s', $hijacked_id, estecapelli_wpml_last_slot_error() )
			);
		}
	}

	/*
	 * The post still holds translated copy, so rebuild English from the seed.
	 * This admin request may be running in any language, which would otherwise
	 * resolve the seed's category to that language's term and re-translate the
	 * post we just recovered.
	 */
	$previous_language = apply_filters( 'wpml_current_language', null );
	do_action( 'wpml_switch_language', 'en' );
	add_filter( 'wpml_disable_term_adjust_id', '__return_true' );

	$restored = estecapelli_import_treatment( $seed );

	remove_filter( 'wpml_disable_term_adjust_id', '__return_true' );
	do_action( 'wpml_switch_language', $previous_language );

	if ( is_wp_error( $restored ) ) {
		return $restored;
	}

	return sprintf(
		'Post %d was registered as "%s"; it is English again and its copy was rebuilt from the seed. Re-import the %s translations to create their own posts.',
		$hijacked_id,
		$taken_by,
		$source_slug
	);
}

/**
 * Treatments whose English post was taken over by a translation import.
 *
 * @return array<string,array<string,mixed>> Slug => post ID and the language holding it.
 */
function estecapelli_hijacked_treatment_sources() {
	global $wpdb;

	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return array();
	}

	$element_type = apply_filters( 'wpml_element_type', 'treatment' );
	$hijacked     = array();

	foreach ( estecapelli_treatments_seed() as $treatment ) {
		$slug = $treatment['slug'] ?? '';
		if ( ! $slug ) {
			continue;
		}

		$candidate_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				 WHERE post_name = %s AND post_type = 'treatment' AND post_status <> 'trash'
				 ORDER BY ID ASC",
				$slug
			)
		);

		$has_english = false;
		$candidate   = array();
		foreach ( array_map( 'intval', (array) $candidate_ids ) as $candidate_id ) {
			$details  = apply_filters(
				'wpml_element_language_details',
				null,
				array(
					'element_id'   => $candidate_id,
					'element_type' => 'treatment',
				)
			);
			$language = (string) estecapelli_it_hair_detail( $details, 'language_code' );
			$trid     = (int) estecapelli_it_hair_detail( $details, 'trid' );

			if ( 'en' === $language ) {
				$has_english = true;
				break;
			}
			if ( ! $candidate && $trid && ! estecapelli_wpml_group_element_id_raw( $trid, $element_type, 'en' ) ) {
				$candidate = array(
					'id'       => $candidate_id,
					'language' => $language,
				);
			}
		}

		if ( ! $has_english && $candidate ) {
			$hijacked[ $slug ] = $candidate;
		}
	}

	return $hijacked;
}

/**
 * Report every post carrying a treatment slug that is shared across languages.
 *
 * bbl, tricholab and plastic-surgery-overview keep one slug in all seven
 * languages, so their records cannot be told apart by slug and a wrong WPML
 * language silently sends the public URL to another language's post. This lists
 * the raw truth for those slugs so a mismatch is visible rather than inferred.
 *
 * @return array<string,array<string,mixed>> Shared slug => group and post rows.
 */
function estecapelli_shared_slug_treatment_report() {
	global $wpdb;

	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return array();
	}

	$element_type = apply_filters( 'wpml_element_type', 'treatment' );
	$report       = array();

	foreach ( estecapelli_indexed_treatment_slugs() as $source_slug => $by_language ) {
		// Only slugs that are identical in every language are ambiguous.
		if ( count( array_unique( $by_language ) ) > 1 ) {
			continue;
		}

		/*
		 * Suffixed variants matter as much as the exact slug. WordPress renames a
		 * colliding post to "bbl-2", and a shared slug is precisely what causes a
		 * collision, so a page can exist, hold the right copy, and still never
		 * answer its indexed URL. Reporting only the exact slug would hide it.
		 */
		$post_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				 WHERE ( post_name = %s OR post_name REGEXP %s )
				 AND post_type = 'treatment' AND post_status <> 'trash'
				 ORDER BY ID ASC",
				$source_slug,
				'^' . $source_slug . '-[0-9]+$'
			)
		);

		$rows = array();
		$trid = 0;
		foreach ( array_map( 'intval', (array) $post_ids ) as $post_id ) {
			$details = apply_filters(
				'wpml_element_language_details',
				null,
				array(
					'element_id'   => $post_id,
					'element_type' => 'treatment',
				)
			);
			$language = (string) estecapelli_it_hair_detail( $details, 'language_code' );
			$trid     = $trid ?: (int) estecapelli_it_hair_detail( $details, 'trid' );

			$rows[] = array(
				'id'       => $post_id,
				'language' => $language ? $language : '(none)',
				'slug'     => (string) get_post_field( 'post_name', $post_id ),
				'title'    => (string) get_post_field( 'post_title', $post_id ),
			);
		}

		$report[ $source_slug ] = array(
			'posts' => $rows,
			'group' => $trid ? estecapelli_wpml_describe_group_raw( $trid, $element_type ) : '(no translation group)',
		);
	}

	return $report;
}

function estecapelli_render_treatments_importer() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$messages = array();

	// Handle import request (treatments and pages share the same form).
	if ( isset( $_POST['estecapelli_action'] ) && check_admin_referer( 'estecapelli_import_treatments' ) ) {

		$treatments = estecapelli_treatments_seed();
		$pages      = estecapelli_pages_seed();
		$doctors    = estecapelli_doctors_seed();
		$target     = sanitize_text_field( wp_unslash( $_POST['estecapelli_action'] ) );

		// Return an English treatment taken over by a translation import.
		if ( 0 === strpos( $target, '__repair_source__' ) ) {
			$repair_slug = substr( $target, strlen( '__repair_source__' ) );
			$result      = estecapelli_repair_hijacked_treatment_source( $repair_slug );
			$messages[]  = array(
				'type' => is_wp_error( $result ) ? 'error' : 'success',
				'text' => sprintf(
					'Repair English source (%s) - %s',
					esc_html( $repair_slug ),
					esc_html( is_wp_error( $result ) ? $result->get_error_message() : $result )
				),
			);
		}

		// French Hair Transplant translations use the same familiar importer UI,
		// but remain a separate, deliberately narrow and idempotent operation.
		if ( '__fr_hair_treatments__' === $target && function_exists( 'estecapelli_run_fr_hair_import' ) ) {
			$result = estecapelli_run_fr_hair_import();
			if ( is_wp_error( $result ) ) {
				update_option( 'estecapelli_fr_hair_import_error', $result->get_error_message(), false );
				$messages[] = array(
					'type' => 'error',
					'text' => sprintf( 'French Hair Transplant translations - %s', esc_html( $result->get_error_message() ) ),
				);
			} else {
				update_option( 'estecapelli_fr_hair_import_version', ESTECAPELLI_FR_HAIR_IMPORT_VERSION, false );
				delete_option( 'estecapelli_fr_hair_import_error' );
				$messages[] = array(
					'type' => 'success',
					'text' => sprintf( 'Imported or repaired %d French Hair Transplant translations.', count( $result ) ),
				);
			}
		}

		// Italian Hair Transplant translations are also available as an
		// explicit batch so administrators can safely re-run them on demand.
		if ( '__it_hair_treatments__' === $target && function_exists( 'estecapelli_run_it_hair_import' ) ) {
			$result = estecapelli_run_it_hair_import();
			if ( is_wp_error( $result ) ) {
				update_option( 'estecapelli_it_hair_import_error', $result->get_error_message(), false );
				$messages[] = array(
					'type' => 'error',
					'text' => sprintf( 'Italian Hair Transplant translations - %s', esc_html( $result->get_error_message() ) ),
				);
			} else {
				update_option( 'estecapelli_it_hair_import_version', ESTECAPELLI_IT_HAIR_IMPORT_VERSION, false );
				delete_option( 'estecapelli_it_hair_import_error' );
				$messages[] = array(
					'type' => 'success',
					'text' => sprintf( 'Imported or repaired %d Italian Hair Transplant translations.', count( $result ) ),
				);
			}
		}

		// Polish Hair Transplant translations run one page per request. Saving all
		// eight flexible-content pages together can exceed ACFML's execution time.
		$pl_hair_prefix = '__pl_hair_treatment__';
		if ( 0 === strpos( $target, $pl_hair_prefix ) && function_exists( 'estecapelli_run_pl_hair_import' ) ) {
			$source_slug = substr( $target, strlen( $pl_hair_prefix ) );
			$result      = estecapelli_run_pl_hair_import( $source_slug );
			if ( is_wp_error( $result ) ) {
				update_option( 'estecapelli_pl_hair_import_error', $result->get_error_message(), false );
				$messages[] = array(
					'type' => 'error',
					'text' => sprintf( 'Polish Hair Transplant translations - %s', esc_html( $result->get_error_message() ) ),
				);
			} else {
				delete_option( 'estecapelli_pl_hair_import_error' );
				$messages[] = array(
					'type' => 'success',
					'text' => sprintf( 'Imported or repaired Polish Hair Transplant translation: %s.', esc_html( $source_slug ) ),
				);
			}
		}

		// Polish Plastic Surgery translations also run one page per request.
		$pl_plastic_prefix = '__pl_plastic_treatment__';
		if ( 0 === strpos( $target, $pl_plastic_prefix ) && function_exists( 'estecapelli_run_pl_plastic_import' ) ) {
			$source_slug = substr( $target, strlen( $pl_plastic_prefix ) );
			$result      = estecapelli_run_pl_plastic_import( $source_slug );
			if ( is_wp_error( $result ) ) {
				update_option( 'estecapelli_pl_plastic_import_error', $result->get_error_message(), false );
				$messages[] = array(
					'type' => 'error',
					'text' => sprintf( 'Polish Plastic Surgery translations - %s', esc_html( $result->get_error_message() ) ),
				);
			} else {
				delete_option( 'estecapelli_pl_plastic_import_error' );
				$messages[] = array(
					'type' => 'success',
					'text' => sprintf( 'Imported or repaired Polish Plastic Surgery translation: %s.', esc_html( $source_slug ) ),
				);
			}
		}

		// Italian Plastic Surgery translations, one page per request.
		$it_plastic_prefix = '__it_plastic_treatment__';
		if ( 0 === strpos( $target, $it_plastic_prefix ) && function_exists( 'estecapelli_run_it_plastic_import' ) ) {
			$source_slug = substr( $target, strlen( $it_plastic_prefix ) );
			$result      = estecapelli_run_it_plastic_import( $source_slug );
			if ( is_wp_error( $result ) ) {
				update_option( 'estecapelli_it_plastic_import_error', $result->get_error_message(), false );
				$messages[] = array(
					'type' => 'error',
					'text' => sprintf( 'Italian Plastic Surgery translations - %s', esc_html( $result->get_error_message() ) ),
				);
			} else {
				delete_option( 'estecapelli_it_plastic_import_error' );
				$messages[] = array(
					'type' => 'success',
					'text' => sprintf( 'Imported or repaired Italian Plastic Surgery translation: %s.', esc_html( $source_slug ) ),
				);
			}
		}

		// Treatments.
		foreach ( $treatments as $t ) {
			if ( '__all__' !== $target && '__all_treatments__' !== $target && $t['slug'] !== $target ) {
				continue;
			}
			$result = estecapelli_import_treatment( $t );
			if ( is_wp_error( $result ) ) {
				$messages[] = array( 'type' => 'error', 'text' => sprintf( '%s — %s', esc_html( $t['title'] ), esc_html( $result->get_error_message() ) ) );
			} else {
				$messages[] = array(
					'type' => 'success',
					'text' => sprintf(
						/* translators: 1: title, 2: links */
						__( 'Imported treatment: %1$s — %2$s', 'estecapelli' ),
						esc_html( $t['title'] ),
						'<a href="' . esc_url( get_edit_post_link( $result ) ) . '">edit</a> · <a href="' . esc_url( get_permalink( $result ) ) . '" target="_blank">view</a>'
					),
				);
			}
		}

		// Pages — order in the seed must keep parents before children.
		$slug_to_id = array();
		foreach ( $pages as $p ) {
			if ( '__all__' !== $target && '__all_pages__' !== $target && $p['slug'] !== $target ) {
				continue;
			}
			$result = estecapelli_import_page( $p, $slug_to_id );
			if ( is_wp_error( $result ) ) {
				$messages[] = array( 'type' => 'error', 'text' => sprintf( '%s — %s', esc_html( $p['title'] ), esc_html( $result->get_error_message() ) ) );
			} else {
				$messages[] = array(
					'type' => 'success',
					'text' => sprintf(
						/* translators: 1: title, 2: links */
						__( 'Imported page: %1$s — %2$s', 'estecapelli' ),
						esc_html( $p['title'] ),
						'<a href="' . esc_url( get_edit_post_link( $result ) ) . '">edit</a> · <a href="' . esc_url( get_permalink( $result ) ) . '" target="_blank">view</a>'
					),
				);
			}
		}

		// Doctors — creates the doctor CPT posts and trashes the legacy
		// nested pages they replace.
		foreach ( $doctors as $d ) {
			if ( '__all__' !== $target && '__all_doctors__' !== $target && ( 'doctor:' . $d['slug'] ) !== $target ) {
				continue;
			}
			$result = estecapelli_import_doctor( $d );
			if ( is_wp_error( $result ) ) {
				$messages[] = array( 'type' => 'error', 'text' => sprintf( '%s — %s', esc_html( $d['name'] ), esc_html( $result->get_error_message() ) ) );
			} else {
				$messages[] = array(
					'type' => 'success',
					'text' => sprintf(
						/* translators: 1: name, 2: links */
						__( 'Imported doctor: %1$s — %2$s', 'estecapelli' ),
						esc_html( $d['name'] ),
						'<a href="' . esc_url( get_edit_post_link( $result ) ) . '">edit</a> · <a href="' . esc_url( get_permalink( $result ) ) . '" target="_blank">view</a>'
					),
				);
			}
		}
	}

	$treatments = estecapelli_treatments_seed();
	$pages      = estecapelli_pages_seed();
	$doctors    = estecapelli_doctors_seed();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Treatment Importer', 'estecapelli' ); ?></h1>
		<p class="description" style="max-width:740px;">
			<?php esc_html_e( 'Each row below is a treatment defined in /inc/data/treatments-seed.php. Click "Import / Re-import" to push that treatment into WordPress. Existing posts with the same slug are updated in place — no duplicates are created.', 'estecapelli' ); ?>
		</p>

		<?php foreach ( $messages as $m ) : ?>
			<div class="notice notice-<?php echo esc_attr( $m['type'] ); ?> is-dismissible">
				<p><?php echo wp_kses_post( $m['text'] ); ?></p>
			</div>
		<?php endforeach; ?>

		<form method="post" style="margin-top:1rem;">
			<?php wp_nonce_field( 'estecapelli_import_treatments' ); ?>

			<table class="widefat striped" style="max-width:980px;">
				<thead>
					<tr>
						<th style="width:32%;"><?php esc_html_e( 'Treatment', 'estecapelli' ); ?></th>
						<th style="width:18%;"><?php esc_html_e( 'Slug', 'estecapelli' ); ?></th>
						<th style="width:15%;"><?php esc_html_e( 'Category', 'estecapelli' ); ?></th>
						<th style="width:10%;"><?php esc_html_e( 'Sections', 'estecapelli' ); ?></th>
						<th style="width:12%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
						<th style="width:13%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $treatments as $t ) :
						$existing = get_page_by_path( $t['slug'], OBJECT, 'treatment' );
						?>
						<tr>
							<td><strong><?php echo esc_html( $t['title'] ); ?></strong></td>
							<td><code><?php echo esc_html( $t['slug'] ); ?></code></td>
							<td><?php echo esc_html( $t['category'] ); ?></td>
							<td><?php echo (int) count( $t['sections'] ?? array() ); ?></td>
							<td>
								<?php if ( $existing ) : ?>
									<span style="color:#0d8551;">● <?php esc_html_e( 'Exists', 'estecapelli' ); ?></span><br>
									<a href="<?php echo esc_url( get_edit_post_link( $existing->ID ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a> ·
									<a href="<?php echo esc_url( get_permalink( $existing->ID ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
								<?php else : ?>
									<span style="color:#888;">○ <?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<button type="submit" name="estecapelli_action" value="<?php echo esc_attr( $t['slug'] ); ?>" class="button">
									<?php echo $existing ? esc_html__( 'Re-import', 'estecapelli' ) : esc_html__( 'Import', 'estecapelli' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<p style="margin-top:1.25rem;">
				<button type="submit" name="estecapelli_action" value="__all_treatments__" class="button button-primary">
					<?php esc_html_e( 'Import / Re-import All Treatments', 'estecapelli' ); ?>
				</button>
			</p>

			<?php $hijacked_sources = estecapelli_hijacked_treatment_sources(); ?>
			<?php if ( $hijacked_sources ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'English sources needing repair', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'These treatments have no English post: a translation import took the English original over and reassigned it to its own language. Repairing returns the post to English and rebuilds its copy from the seed. Re-import that language afterwards so the translation gets its own post.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:40%;"><?php esc_html_e( 'English source', 'estecapelli' ); ?></th>
							<th style="width:40%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:20%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $hijacked_sources as $hijacked_slug => $hijacked_state ) : ?>
							<tr>
								<td><code><?php echo esc_html( $hijacked_slug ); ?></code></td>
								<td>
									<span style="color:#b26200;">
										<?php
										printf(
											/* translators: 1: post ID, 2: WPML language code. */
											esc_html__( 'Post %1$d is registered as "%2$s"', 'estecapelli' ),
											(int) $hijacked_state['id'],
											esc_html( $hijacked_state['language'] )
										);
										?>
									</span>
									- <a href="<?php echo esc_url( get_edit_post_link( $hijacked_state['id'] ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
								</td>
								<td>
									<button type="submit" name="estecapelli_action" value="__repair_source__<?php echo esc_attr( $hijacked_slug ); ?>" class="button">
										<?php esc_html_e( 'Repair English source', 'estecapelli' ); ?>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php $shared_slug_report = estecapelli_shared_slug_treatment_report(); ?>
			<?php if ( $shared_slug_report ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Treatments sharing one slug in every language', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'These slugs are identical in all languages, so their posts can only be told apart by their WPML language. Every language should appear exactly once, on its own post ID. A language that is missing here has no page, and two languages on one post ID means an import took another language\'s post over.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:22%;"><?php esc_html_e( 'Slug', 'estecapelli' ); ?></th>
							<th style="width:44%;"><?php esc_html_e( 'Posts using it', 'estecapelli' ); ?></th>
							<th style="width:34%;"><?php esc_html_e( 'WPML group', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $shared_slug_report as $shared_slug => $shared_state ) : ?>
							<tr>
								<td><code><?php echo esc_html( $shared_slug ); ?></code></td>
								<td>
									<?php if ( ! $shared_state['posts'] ) : ?>
										<span style="color:#888;"><?php esc_html_e( 'No post uses this slug', 'estecapelli' ); ?></span>
									<?php else : ?>
										<?php foreach ( $shared_state['posts'] as $shared_post ) : ?>
											<?php $slug_is_wrong = ( $shared_post['slug'] !== $shared_slug ); ?>
											<div>
												<code><?php echo esc_html( $shared_post['language'] ); ?></code>
												— <?php esc_html_e( 'post', 'estecapelli' ); ?> <?php echo (int) $shared_post['id']; ?>
												<code style="<?php echo $slug_is_wrong ? 'background:#fcf0e4;color:#b26200;' : ''; ?>"><?php echo esc_html( $shared_post['slug'] ); ?></code>
												<a href="<?php echo esc_url( get_edit_post_link( $shared_post['id'] ) ); ?>"><?php echo esc_html( $shared_post['title'] ); ?></a>
												<?php if ( $slug_is_wrong ) : ?>
													<strong style="color:#b26200;"><?php esc_html_e( '← wrong slug, this URL cannot resolve', 'estecapelli' ); ?></strong>
												<?php endif; ?>
											</div>
										<?php endforeach; ?>
									<?php endif; ?>
								</td>
								<td><code style="font-size:11px;"><?php echo esc_html( $shared_state['group'] ); ?></code></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_fr_hair_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'French Hair Transplant translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Imports the complete French copy for the eight Hair Transplant treatments and repairs their WPML links. It is safe to run again.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:34%;"><?php esc_html_e( 'English source', 'estecapelli' ); ?></th>
							<th style="width:38%;"><?php esc_html_e( 'French slug', 'estecapelli' ); ?></th>
							<th style="width:28%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_fr_hair_manifest() as $source_slug => $french_slug ) :
							$source_id    = estecapelli_source_post_id( $source_slug, 'treatment' );
							$french_id    = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, 'fr' ) : 0;
							$needs_repair = $french_id && (
								get_post_meta( $french_id, '_icl_lang_duplicate_of', true ) ||
								get_post_field( 'post_title', $french_id ) === get_post_field( 'post_title', $source_id )
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $french_slug ); ?></code></td>
								<td>
									<?php if ( $needs_repair ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - needs repair', 'estecapelli' ); ?></span>
										- <a href="<?php echo esc_url( get_edit_post_link( $french_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
									<?php elseif ( $french_id && $french_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
										- <a href="<?php echo esc_url( get_edit_post_link( $french_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $french_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<p style="margin-top:1.25rem;">
					<button type="submit" name="estecapelli_action" value="__fr_hair_treatments__" class="button button-primary">
						<?php esc_html_e( 'Import / Repair All 8 French Treatments', 'estecapelli' ); ?>
					</button>
				</p>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_fr_plastic_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'French Plastic Surgery translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Use an individual Import / Repair button to rebuild that treatment and its French WPML relationship.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:28%;"><?php esc_html_e( 'English source', 'estecapelli' ); ?></th>
							<th style="width:32%;"><?php esc_html_e( 'French slug', 'estecapelli' ); ?></th>
							<th style="width:24%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:16%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_fr_plastic_manifest() as $source_slug => $french_slug ) :
							$source_id    = estecapelli_source_post_id( $source_slug, 'treatment' );
							$linked_id    = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, 'fr' ) : 0;
							$candidate_id = estecapelli_fr_hair_raw_post_id( $french_slug, $source_id );
							$french_id    = ( $linked_id && $linked_id !== $source_id ) ? $linked_id : $candidate_id;
							$action_url   = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'estecapelli_import_fr_plastic_treatment',
										'source' => $source_slug,
									),
									admin_url( 'admin-post.php' )
								),
								'estecapelli_import_fr_plastic_' . $source_slug
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $french_slug ); ?></code></td>
								<td>
									<?php if ( $linked_id && $linked_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
									<?php elseif ( $candidate_id ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - link needs repair', 'estecapelli' ); ?></span>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
									<?php if ( $french_id ) : ?>
										- <a href="<?php echo esc_url( get_edit_post_link( $french_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $french_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo esc_url( $action_url ); ?>" class="button button-primary">
										<?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_fr_dental_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'French Dental translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Use an individual Import / Repair button to rebuild that treatment and its French WPML relationship.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:28%;"><?php esc_html_e( 'English source', 'estecapelli' ); ?></th>
							<th style="width:32%;"><?php esc_html_e( 'French slug', 'estecapelli' ); ?></th>
							<th style="width:24%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:16%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_fr_dental_manifest() as $source_slug => $french_slug ) :
							$source_id    = estecapelli_source_post_id( $source_slug, 'treatment' );
							$linked_id    = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, 'fr' ) : 0;
							$candidate_id = estecapelli_fr_hair_raw_post_id( $french_slug, $source_id );
							$french_id    = ( $linked_id && $linked_id !== $source_id ) ? $linked_id : $candidate_id;
							$action_url   = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'estecapelli_import_fr_dental_treatment',
										'source' => $source_slug,
									),
									admin_url( 'admin-post.php' )
								),
								'estecapelli_import_fr_dental_' . $source_slug
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $french_slug ); ?></code></td>
								<td>
									<?php if ( $linked_id && $linked_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
									<?php elseif ( $candidate_id ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - link needs repair', 'estecapelli' ); ?></span>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
									<?php if ( $french_id ) : ?>
										- <a href="<?php echo esc_url( get_edit_post_link( $french_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $french_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo esc_url( $action_url ); ?>" class="button button-primary">
										<?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_fr_pages_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'French page translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Translates the regular pages (landing pages, TrichoLab, pre/post-transplant, About tree, Before &amp; After, Blog, Contact) and links each to its English original. Use an individual Import / Repair button to rebuild one page and its French WPML relationship.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:28%;"><?php esc_html_e( 'English page', 'estecapelli' ); ?></th>
							<th style="width:32%;"><?php esc_html_e( 'French slug', 'estecapelli' ); ?></th>
							<th style="width:24%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:16%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_fr_pages_manifest() as $source_slug => $french_slug ) :
							$source_id    = estecapelli_source_post_id( $source_slug, 'page' );
							$linked_id    = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, 'fr' ) : 0;
							$candidate_id = estecapelli_fr_page_raw_post_id( $french_slug, $source_id );
							$french_id    = ( $linked_id && $linked_id !== $source_id ) ? $linked_id : $candidate_id;
							$action_url   = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'estecapelli_import_fr_page',
										'source' => $source_slug,
									),
									admin_url( 'admin-post.php' )
								),
								'estecapelli_import_fr_page_' . $source_slug
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $french_slug ); ?></code></td>
								<td>
									<?php if ( $linked_id && $linked_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
									<?php elseif ( $candidate_id ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - link needs repair', 'estecapelli' ); ?></span>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
									<?php if ( $french_id ) : ?>
										- <a href="<?php echo esc_url( get_edit_post_link( $french_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $french_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo esc_url( $action_url ); ?>" class="button button-primary">
										<?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_it_hair_pages_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Italian Hair Transplant page translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Imports the Italian Hair Transplant landing page, TrichoLab and the pre/post-transplant care pages. Use an individual Import / Repair button to rebuild one page and its Italian WPML relationship.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:28%;"><?php esc_html_e( 'English page', 'estecapelli' ); ?></th>
							<th style="width:32%;"><?php esc_html_e( 'Italian slug', 'estecapelli' ); ?></th>
							<th style="width:24%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:16%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_it_hair_pages_manifest() as $source_slug => $italian_slug ) :
							$source_id    = estecapelli_source_post_id( $source_slug, 'page' );
							$linked_id    = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, 'it' ) : 0;
							$candidate_id = estecapelli_it_hair_page_raw_post_id( $italian_slug, $source_id );
							$italian_id   = ( $linked_id && $linked_id !== $source_id ) ? $linked_id : $candidate_id;
							$action_url   = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'estecapelli_import_it_hair_page',
										'source' => $source_slug,
									),
									admin_url( 'admin-post.php' )
								),
								'estecapelli_import_it_hair_page_' . $source_slug
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $italian_slug ); ?></code></td>
								<td>
									<?php if ( $linked_id && $linked_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
									<?php elseif ( $candidate_id ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - link needs repair', 'estecapelli' ); ?></span>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
									<?php if ( $italian_id ) : ?>
										- <a href="<?php echo esc_url( get_edit_post_link( $italian_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $italian_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo esc_url( $action_url ); ?>" class="button button-primary">
										<?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_it_pages_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Italian page translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Imports the Italian landing, About, Our Team, Our Doctors, Medical Director, Before & After and Contact pages individually. Use Import / Repair to rebuild the selected page and its Italian WPML relationship.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:28%;"><?php esc_html_e( 'English page', 'estecapelli' ); ?></th>
							<th style="width:32%;"><?php esc_html_e( 'Italian slug', 'estecapelli' ); ?></th>
							<th style="width:24%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:16%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_it_pages_manifest() as $source_slug => $italian_slug ) :
							$source_id    = estecapelli_source_post_id( $source_slug, 'page' );
							$linked_id    = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, 'it' ) : 0;
							$candidate_id = estecapelli_it_template_page_raw_target_id( $italian_slug, $source_id, 'it' );
							$italian_id   = ( $linked_id && $linked_id !== $source_id ) ? $linked_id : $candidate_id;
							$action_url   = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'estecapelli_import_it_page',
										'source' => $source_slug,
									),
									admin_url( 'admin-post.php' )
								),
								'estecapelli_import_it_page_' . $source_slug
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $italian_slug ); ?></code></td>
								<td>
									<?php if ( $linked_id && $linked_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
									<?php elseif ( $candidate_id ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - link needs repair', 'estecapelli' ); ?></span>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
									<?php if ( $italian_id ) : ?>
										- <a href="<?php echo esc_url( get_edit_post_link( $italian_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $italian_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo esc_url( $action_url ); ?>" class="button button-primary">
										<?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_it_hair_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Italian Hair Transplant translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Imports the available Italian copy for the Hair Transplant treatments and repairs their WPML links. It is safe to run again.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:34%;"><?php esc_html_e( 'English source', 'estecapelli' ); ?></th>
							<th style="width:38%;"><?php esc_html_e( 'Italian slug', 'estecapelli' ); ?></th>
							<th style="width:28%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_it_hair_manifest() as $source_slug => $italian_slug ) :
							$source_id    = estecapelli_source_post_id( $source_slug, 'treatment' );
							$italian_id   = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, 'it' ) : 0;
							$needs_repair = $italian_id && (
								get_post_meta( $italian_id, '_icl_lang_duplicate_of', true ) ||
								get_post_field( 'post_title', $italian_id ) === get_post_field( 'post_title', $source_id )
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $italian_slug ); ?></code></td>
								<td>
									<?php if ( $needs_repair ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - needs repair', 'estecapelli' ); ?></span>
										- <a href="<?php echo esc_url( get_edit_post_link( $italian_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
									<?php elseif ( $italian_id && $italian_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
										- <a href="<?php echo esc_url( get_edit_post_link( $italian_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $italian_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<p style="margin-top:1.25rem;">
					<button type="submit" name="estecapelli_action" value="__it_hair_treatments__" class="button button-primary">
						<?php esc_html_e( 'Import / Repair All Italian Treatments', 'estecapelli' ); ?>
					</button>
				</p>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_pl_hair_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Polish Hair Transplant translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Imports one Polish treatment per request to stay within the server execution limit. Each action is idempotent and safe to run again.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:28%;"><?php esc_html_e( 'English source', 'estecapelli' ); ?></th>
							<th style="width:32%;"><?php esc_html_e( 'Polish slug', 'estecapelli' ); ?></th>
							<th style="width:24%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:16%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_pl_hair_manifest() as $source_slug => $polish_slug ) :
							$source_id    = estecapelli_source_post_id( $source_slug, 'treatment' );
							$polish_id    = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, 'pl' ) : 0;
							$needs_repair = $polish_id && (
								get_post_meta( $polish_id, '_icl_lang_duplicate_of', true ) ||
								get_post_field( 'post_title', $polish_id ) === get_post_field( 'post_title', $source_id )
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $polish_slug ); ?></code></td>
								<td>
									<?php if ( $needs_repair ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - needs repair', 'estecapelli' ); ?></span>
										- <a href="<?php echo esc_url( get_edit_post_link( $polish_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
									<?php elseif ( $polish_id && $polish_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
										- <a href="<?php echo esc_url( get_edit_post_link( $polish_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $polish_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
								</td>
								<td>
									<button type="submit" name="estecapelli_action" value="<?php echo esc_attr( '__pl_hair_treatment__' . $source_slug ); ?>" class="button button-primary">
										<?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_it_plastic_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Italian Plastic Surgery translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Imports one Italian treatment per request to stay within the server execution limit. Each action is idempotent and safe to run again.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:28%;"><?php esc_html_e( 'English source', 'estecapelli' ); ?></th>
							<th style="width:32%;"><?php esc_html_e( 'Italian slug', 'estecapelli' ); ?></th>
							<th style="width:24%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:16%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_it_plastic_manifest() as $source_slug => $italian_slug ) :
							$source_id    = estecapelli_source_post_id( $source_slug, 'treatment' );
							$italian_id   = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, 'it' ) : 0;
							$needs_repair = $italian_id && (
								get_post_meta( $italian_id, '_icl_lang_duplicate_of', true ) ||
								get_post_field( 'post_title', $italian_id ) === get_post_field( 'post_title', $source_id )
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $italian_slug ); ?></code></td>
								<td>
									<?php if ( $needs_repair ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - needs repair', 'estecapelli' ); ?></span>
										- <a href="<?php echo esc_url( get_edit_post_link( $italian_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
									<?php elseif ( $italian_id && $italian_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
										- <a href="<?php echo esc_url( get_edit_post_link( $italian_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $italian_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
								</td>
								<td>
									<button type="submit" name="estecapelli_action" value="<?php echo esc_attr( '__it_plastic_treatment__' . $source_slug ); ?>" class="button button-primary">
										<?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_pl_plastic_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Polish Plastic Surgery translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Imports one Polish treatment per request to stay within the server execution limit. Each action is idempotent and safe to run again.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:28%;"><?php esc_html_e( 'English source', 'estecapelli' ); ?></th>
							<th style="width:32%;"><?php esc_html_e( 'Polish slug', 'estecapelli' ); ?></th>
							<th style="width:24%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:16%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_pl_plastic_manifest() as $source_slug => $polish_slug ) :
							$source_id    = estecapelli_source_post_id( $source_slug, 'treatment' );
							$polish_id    = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'treatment', false, 'pl' ) : 0;
							$needs_repair = $polish_id && (
								get_post_meta( $polish_id, '_icl_lang_duplicate_of', true ) ||
								get_post_field( 'post_title', $polish_id ) === get_post_field( 'post_title', $source_id )
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $polish_slug ); ?></code></td>
								<td>
									<?php if ( $needs_repair ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - needs repair', 'estecapelli' ); ?></span>
										- <a href="<?php echo esc_url( get_edit_post_link( $polish_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
									<?php elseif ( $polish_id && $polish_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
										- <a href="<?php echo esc_url( get_edit_post_link( $polish_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $polish_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
								</td>
								<td>
									<button type="submit" name="estecapelli_action" value="<?php echo esc_attr( '__pl_plastic_treatment__' . $source_slug ); ?>" class="button button-primary">
										<?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_fr_blog_manifest' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'French blog translations', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'Translates the blog articles and links each to its English original. Use an individual Import / Repair button to rebuild one post and its French WPML relationship.', 'estecapelli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:28%;"><?php esc_html_e( 'English post', 'estecapelli' ); ?></th>
							<th style="width:32%;"><?php esc_html_e( 'French slug', 'estecapelli' ); ?></th>
							<th style="width:24%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:16%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( estecapelli_fr_blog_manifest() as $source_slug => $meta ) :
							$french_slug  = $meta['slug'];
							$source_id    = estecapelli_source_post_id( $source_slug, 'post' );
							$linked_id    = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'post', false, 'fr' ) : 0;
							$candidate_id = estecapelli_fr_blog_raw_post_id( $french_slug, $source_id );
							$french_id    = ( $linked_id && $linked_id !== $source_id ) ? $linked_id : $candidate_id;
							$action_url   = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'estecapelli_import_fr_blog',
										'source' => $source_slug,
									),
									admin_url( 'admin-post.php' )
								),
								'estecapelli_import_fr_blog_' . $source_slug
							);
							?>
							<tr>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( $french_slug ); ?></code></td>
								<td>
									<?php if ( $linked_id && $linked_id !== $source_id ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
									<?php elseif ( $candidate_id ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Exists - link needs repair', 'estecapelli' ); ?></span>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
									<?php if ( $french_id ) : ?>
										- <a href="<?php echo esc_url( get_edit_post_link( $french_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										| <a href="<?php echo esc_url( get_permalink( $french_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo esc_url( $action_url ); ?>" class="button button-primary">
										<?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( function_exists( 'estecapelli_run_blog_i18n_import' ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Blog translations + SEO (all languages)', 'estecapelli' ); ?></h2>
				<p class="description" style="max-width:740px;">
					<?php esc_html_e( 'One import brings every translated blog article (French, Turkish, Italian, Spanish, Polish, Portuguese, Romanian) into WordPress, links each to its English original, and writes the Rank Math meta description + focus keyword for every language, English included. Idempotent and safe to run again.', 'estecapelli' ); ?>
				</p>

				<?php
				$blog_i18n_all_url = wp_nonce_url(
					add_query_arg( array( 'action' => 'estecapelli_import_blog_i18n' ), admin_url( 'admin-post.php' ) ),
					'estecapelli_import_blog_i18n'
				);
				?>
				<p>
					<a href="<?php echo esc_url( $blog_i18n_all_url ); ?>" class="button button-primary button-hero">
						<?php esc_html_e( 'Import ALL blog translations + SEO', 'estecapelli' ); ?>
					</a>
				</p>

				<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
					<thead>
						<tr>
							<th style="width:8%;"><?php esc_html_e( 'Lang', 'estecapelli' ); ?></th>
							<th style="width:30%;"><?php esc_html_e( 'English post', 'estecapelli' ); ?></th>
							<th style="width:32%;"><?php esc_html_e( 'Translated slug', 'estecapelli' ); ?></th>
							<th style="width:18%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
							<th style="width:12%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( estecapelli_blog_i18n_languages() as $blog_lang ) :
							foreach ( estecapelli_indexed_blog_slugs() as $blog_source_slug => $blog_langs ) :
								if ( empty( $blog_langs[ $blog_lang ] ) ) {
									continue;
								}
								$blog_target_slug = $blog_langs[ $blog_lang ];
								$blog_wpml_lang   = estecapelli_wpml_language_code( $blog_lang );
								$blog_source_id   = estecapelli_source_post_id( $blog_source_slug, 'post' );
								$blog_linked_id   = $blog_source_id ? (int) apply_filters( 'wpml_object_id', $blog_source_id, 'post', false, $blog_wpml_lang ) : 0;
								$blog_candidate   = estecapelli_blog_i18n_raw_post_id( $blog_target_slug, $blog_source_id );
								$blog_exists      = ( $blog_linked_id && $blog_linked_id !== $blog_source_id );
								$blog_view_id     = $blog_exists ? $blog_linked_id : $blog_candidate;
								$blog_one_url     = wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'estecapelli_import_blog_i18n_one',
											'lang'   => $blog_lang,
											'source' => $blog_source_slug,
										),
										admin_url( 'admin-post.php' )
									),
									'estecapelli_import_blog_i18n_one_' . $blog_lang . '_' . $blog_source_slug
								);
								?>
								<tr>
									<td><code><?php echo esc_html( strtoupper( $blog_lang ) ); ?></code></td>
									<td><code><?php echo esc_html( $blog_source_slug ); ?></code></td>
									<td><code><?php echo esc_html( $blog_target_slug ); ?></code></td>
									<td>
										<?php if ( $blog_exists ) : ?>
											<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
										<?php elseif ( $blog_candidate ) : ?>
											<span style="color:#b26200;"><?php esc_html_e( 'Exists - link needs repair', 'estecapelli' ); ?></span>
										<?php else : ?>
											<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
										<?php endif; ?>
										<?php if ( $blog_view_id ) : ?>
											- <a href="<?php echo esc_url( get_edit_post_link( $blog_view_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
										<?php endif; ?>
									</td>
									<td>
										<a href="<?php echo esc_url( $blog_one_url ); ?>" class="button"><?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?></a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Pages', 'estecapelli' ); ?></h2>
			<p class="description" style="max-width:740px;">
				<?php esc_html_e( 'Each row is a regular WordPress page scaffolded with a basic Hero section. After importing, edit each page in the WordPress editor to build it out with the page-builder sections.', 'estecapelli' ); ?>
			</p>

			<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
				<thead>
					<tr>
						<th style="width:32%;"><?php esc_html_e( 'Page', 'estecapelli' ); ?></th>
						<th style="width:24%;"><?php esc_html_e( 'Slug', 'estecapelli' ); ?></th>
						<th style="width:14%;"><?php esc_html_e( 'Parent', 'estecapelli' ); ?></th>
						<th style="width:8%;"><?php esc_html_e( 'Sections', 'estecapelli' ); ?></th>
						<th style="width:12%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
						<th style="width:10%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $pages as $p ) :
						// Build full hierarchical path for lookup.
						$path_parts = array( $p['slug'] );
						$walk_slug  = $p['parent'] ?? null;
						$guard      = 0;
						while ( $walk_slug && $guard < 10 ) {
							array_unshift( $path_parts, $walk_slug );
							$found_parent = null;
							foreach ( $pages as $candidate ) {
								if ( $candidate['slug'] === $walk_slug ) {
									$found_parent = $candidate;
									break;
								}
							}
							$walk_slug = $found_parent['parent'] ?? null;
							$guard++;
						}
						$full_path = implode( '/', $path_parts );
						$existing  = get_page_by_path( $full_path, OBJECT, 'page' );
						?>
						<tr>
							<td><strong><?php echo esc_html( $p['title'] ); ?></strong></td>
							<td><code><?php echo esc_html( $full_path ); ?></code></td>
							<td><?php echo esc_html( $p['parent'] ?: '—' ); ?></td>
							<td><?php echo (int) count( $p['sections'] ?? array() ); ?></td>
							<td>
								<?php if ( $existing ) : ?>
									<span style="color:#0d8551;">● <?php esc_html_e( 'Exists', 'estecapelli' ); ?></span><br>
									<a href="<?php echo esc_url( get_edit_post_link( $existing->ID ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a> ·
									<a href="<?php echo esc_url( get_permalink( $existing->ID ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
								<?php else : ?>
									<span style="color:#888;">○ <?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<button type="submit" name="estecapelli_action" value="<?php echo esc_attr( $p['slug'] ); ?>" class="button">
									<?php echo $existing ? esc_html__( 'Re-import', 'estecapelli' ) : esc_html__( 'Import', 'estecapelli' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<p style="margin-top:1.25rem;">
				<button type="submit" name="estecapelli_action" value="__all_pages__" class="button button-primary">
					<?php esc_html_e( 'Import / Re-import All Pages', 'estecapelli' ); ?>
				</button>
			</p>

			<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Doctors', 'estecapelli' ); ?></h2>
			<p class="description" style="max-width:740px;">
				<?php esc_html_e( 'Migrates the existing doctor profiles into the Doctors post type. Each becomes a single entry under the “Doctors” menu — no page nesting — and the old nested page it replaces is moved to the trash. The “Our Doctors” roster grid then lists every doctor automatically. Re-importing overwrites the position, bio and credentials, but never the uploaded photo.', 'estecapelli' ); ?>
			</p>

			<table class="widefat striped" style="max-width:980px; margin-top:1rem;">
				<thead>
					<tr>
						<th style="width:30%;"><?php esc_html_e( 'Doctor', 'estecapelli' ); ?></th>
						<th style="width:22%;"><?php esc_html_e( 'Slug', 'estecapelli' ); ?></th>
						<th style="width:24%;"><?php esc_html_e( 'Replaces page', 'estecapelli' ); ?></th>
						<th style="width:12%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
						<th style="width:12%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $doctors as $d ) :
						$existing = get_page_by_path( $d['slug'], OBJECT, 'doctor' );
						?>
						<tr>
							<td><strong><?php echo esc_html( $d['name'] ); ?></strong></td>
							<td><code><?php echo esc_html( $d['slug'] ); ?></code></td>
							<td><code><?php echo esc_html( $d['old_page_path'] ?? '—' ); ?></code></td>
							<td>
								<?php if ( $existing ) : ?>
									<span style="color:#0d8551;">● <?php esc_html_e( 'Exists', 'estecapelli' ); ?></span><br>
									<a href="<?php echo esc_url( get_edit_post_link( $existing->ID ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a> ·
									<a href="<?php echo esc_url( get_permalink( $existing->ID ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
								<?php else : ?>
									<span style="color:#888;">○ <?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<button type="submit" name="estecapelli_action" value="doctor:<?php echo esc_attr( $d['slug'] ); ?>" class="button">
									<?php echo $existing ? esc_html__( 'Re-import', 'estecapelli' ) : esc_html__( 'Migrate', 'estecapelli' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<p style="margin-top:1.25rem;">
				<button type="submit" name="estecapelli_action" value="__all_doctors__" class="button button-primary">
					<?php esc_html_e( 'Migrate / Re-import All Doctors', 'estecapelli' ); ?>
				</button>
				<button type="submit" name="estecapelli_action" value="__all__" class="button button-primary button-large" style="margin-left:1rem;">
					<?php esc_html_e( 'Import / Re-import EVERYTHING', 'estecapelli' ); ?>
				</button>
			</p>

			<p class="description" style="max-width:740px; margin-top:1rem;">
				<strong><?php esc_html_e( 'Heads-up:', 'estecapelli' ); ?></strong>
				<?php esc_html_e( 'Re-importing refreshes the section text from the seed, but PRESERVES any images, before/after galleries and videos you uploaded in the editor (kept whenever the seed leaves that slot empty). Manual text edits to a section are still replaced.', 'estecapelli' ); ?>
			</p>
		</form>
	</div>
	<?php
}

/**
 * One-time correction for Prof. Dr. Binnur Üstün's profile.
 *
 * Her title/bio were hand-edited in the dashboard (Medical Director → Chief
 * Physician), then an earlier destructive re-import reverted them to the seed.
 * The importer is now non-destructive, so it will never overwrite the stale
 * value on its own — this pushes the corrected seed onto her existing record
 * exactly once, then latches an option so it never runs again. Leaves every
 * other profile untouched.
 */
add_action( 'admin_init', 'estecapelli_reseed_binnur_chief_physician' );
function estecapelli_reseed_binnur_chief_physician() {
	if ( get_option( 'estecapelli_binnur_chief_reseed' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'estecapelli_doctors_seed' ) ) {
		return; // ACF / seed not ready yet — retry on the next admin load.
	}

	$post = get_page_by_path( 'prof-dr-binnur-ustun', OBJECT, 'doctor' );
	if ( ! $post ) {
		return; // Not imported yet — retry on the next admin load.
	}

	$seed = null;
	foreach ( estecapelli_doctors_seed() as $doctor ) {
		if ( 'prof-dr-binnur-ustun' === ( $doctor['slug'] ?? '' ) ) {
			$seed = $doctor;
			break;
		}
	}
	if ( ! $seed ) {
		return;
	}

	update_field( 'position', $seed['position'] ?? '', $post->ID );
	update_field( 'bio', $seed['bio'] ?? '', $post->ID );
	update_field(
		'credentials',
		array_map(
			static function ( $label ) {
				return array( 'label' => $label );
			},
			$seed['credentials'] ?? array()
		),
		$post->ID
	);

	update_option( 'estecapelli_binnur_chief_reseed', 1 );
}

/**
 * Run one language's full page + doctor sweep once per version, on admin_init.
 *
 * Brings the ES/PT/PL/TR importers to parity with the FR/IT auto-importers:
 * bump the language's autorun version and the next few wp-admin loads re-import
 * every page and doctor for that language from the corrected files — no clicking
 * through the per-row table.
 *
 * Only ONE language sweep runs per request. A first load after deploy therefore
 * cannot fire all four at once and blow the ACFML execution limit; the remaining
 * languages settle on the next few admin page loads.
 *
 * @param string   $option_key Version option name for this language.
 * @param string   $version    Target version constant value.
 * @param callable $run_all    Imports the whole language; returns true|WP_Error.
 */
function estecapelli_autorun_language_import( $option_key, $version, callable $items_provider, callable $import_one ) {
	if ( get_option( $option_key ) === $version ) {
		return; // this language is fully imported for this version.
	}
	if ( ! current_user_can( 'manage_options' ) || ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) ) {
		return;
	}
	// Import at most ONE item per request, and only while the request is still
	// light. A single item is fast; a whole-language sweep is not — it blew the
	// 60s limit. Deferring when the request is already busy also keeps this off
	// the heavy FR/IT sweep on the first load after a deploy.
	$elapsed = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? ( microtime( true ) - (float) $_SERVER['REQUEST_TIME_FLOAT'] ) : 0;
	if ( $elapsed > 8 || ! empty( $GLOBALS['estecapelli_language_autorun_ran_this_request'] ) ) {
		return;
	}

	$progress_key = $option_key . '_progress';
	$progress     = get_option( $progress_key );
	if ( ! is_array( $progress ) || ( $progress['version'] ?? null ) !== $version ) {
		$progress = array( 'version' => $version, 'done' => array(), 'failures' => array() ); // fresh version.
	}
	$done     = is_array( $progress['done'] ) ? $progress['done'] : array();
	$failures = isset( $progress['failures'] ) && is_array( $progress['failures'] ) ? $progress['failures'] : array();

	foreach ( call_user_func( $items_provider ) as $item ) {
		$id = $item['kind'] . ':' . $item['slug'];
		if ( in_array( $id, $done, true ) ) {
			continue; // already imported on an earlier load.
		}
		$GLOBALS['estecapelli_language_autorun_ran_this_request'] = true;
		$result = call_user_func( $import_one, $item['kind'], $item['slug'] );
		if ( is_wp_error( $result ) ) {
			/*
			 * Three attempts, then move on. A failing item used to be retried on
			 * every single admin page load for as long as the version stayed
			 * unfinished, and an importer that failed partway through left a new
			 * post behind on each attempt — which is how six doctors ended up
			 * owning roughly 170 posts. Giving up on the item keeps the rest of
			 * the language importable and leaves the error on the screen.
			 */
			$failures[ $id ] = (int) ( $failures[ $id ] ?? 0 ) + 1;
			$message         = $id . ' — ' . $result->get_error_message();
			if ( $failures[ $id ] >= 3 ) {
				$done[]  = $id;
				$message = sprintf( 'skipped after %d failed attempts: %s', (int) $failures[ $id ], $message );
			}
			set_transient( $option_key . '_error', $message, 10 * MINUTE_IN_SECONDS );
			update_option( $progress_key, array( 'version' => $version, 'done' => $done, 'failures' => $failures ), false );
			return;
		}
		$done[] = $id;
		unset( $failures[ $id ] );
		update_option( $progress_key, array( 'version' => $version, 'done' => $done, 'failures' => $failures ), false );
		delete_transient( $option_key . '_error' );
		return; // exactly one item this request — cannot exceed the time limit.
	}

	// Every item imported → mark the version complete and drop the progress row.
	update_option( $option_key, $version, false );
	delete_option( $progress_key );
}

add_action( 'admin_notices', 'estecapelli_language_autorun_notice' );
/** Surface any language auto-import error so a stalled sweep is visible. */
function estecapelli_language_autorun_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	foreach ( array( 'es', 'pt', 'ro', 'pl', 'tr' ) as $lang ) {
		$error = get_transient( "estecapelli_{$lang}_autorun_version_error" );
		if ( $error ) {
			printf(
				'<div class="notice notice-error"><p><strong>%s:</strong> %s</p></div>',
				esc_html( sprintf( 'Estecapelli %s auto-import paused', strtoupper( $lang ) ) ),
				esc_html( $error )
			);
		}
	}
}
