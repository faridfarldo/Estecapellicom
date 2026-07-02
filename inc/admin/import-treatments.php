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
 * ACF image / gallery / video slots empty. So for each section, whenever the
 * seed value for `image`, `video_id` or `items` is empty, we keep whatever is
 * already stored on the post instead of overwriting it with nothing.
 *
 * We read the EXISTING value formatted (get_field default) — for flexible
 * content the unformatted value is just an array of layout-name strings, not the
 * row data — then reduce any image back to its attachment ID before writing, so
 * update_field stores it correctly. Matching is by position AND layout type.
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

	foreach ( $new_sections as $i => $section ) {
		if ( empty( $existing[ $i ] ) || ! is_array( $existing[ $i ] ) ) {
			continue;
		}
		$old = $existing[ $i ];

		// Only merge same-type sections so positions can't drift across types.
		$new_layout = $section['acf_fc_layout'] ?? '';
		$old_layout = $old['acf_fc_layout'] ?? '';
		if ( $new_layout && $old_layout && $new_layout !== $old_layout ) {
			continue;
		}

		// Preserve a single uploaded image (store its ID).
		if ( array_key_exists( 'image', $section ) && empty( $section['image'] ) && ! empty( $old['image'] ) ) {
			$id = estecapelli_image_to_id( $old['image'] );
			if ( $id ) {
				$new_sections[ $i ]['image'] = $id;
			}
		}

		// Preserve an uploaded/entered video id (scalar).
		if ( array_key_exists( 'video_id', $section ) && empty( $section['video_id'] ) && ! empty( $old['video_id'] ) ) {
			$new_sections[ $i ]['video_id'] = $old['video_id'];
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
		update_field( 'position', $data['position'] ?? '', $post_id );
		update_field( 'bio', $data['bio'] ?? '', $post_id );
		$credentials = array_map(
			static function ( $label ) {
				return array( 'label' => $label );
			},
			$data['credentials'] ?? array()
		);
		update_field( 'credentials', $credentials, $post_id );
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
