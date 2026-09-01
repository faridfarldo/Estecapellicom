<?php
/**
 * Doctor repair — put each language's real profile back in its WPML slot, then
 * clear out the duplicates that were left behind.
 *
 * The doctor table grew to roughly 170 posts for six doctors. Two faults did
 * it: an import that failed after creating its post was retried on every admin
 * page load, leaving another post behind each time; and claiming a language
 * slot deletes the previous occupant's WPML row without touching the post, so
 * every claim orphaned a profile. The correct Turkish and Romanian profiles are
 * among those orphans, while French-content duplicates hold the tr/ro slots —
 * which is exactly what those pages print.
 *
 * Both faults are fixed in the importer. This page repairs what they already
 * did, in two deliberate steps:
 *
 *   Relink — point each language slot at the profile whose stored `position`
 *            matches that language's translation, and drop rows filed under a
 *            language WPML does not have (an 'all' row shows one profile in
 *            every language at once).
 *   Trash  — move the leftover duplicates to the bin, in batches, recoverable.
 *
 * Nothing runs on load: the page reports, and each button is a separate POST.
 * Following repair-pl-pages.php, it has its own constant rather than riding on
 * ESTECAPELLI_ENABLE_CONTENT_MUTATIONS, so a repair never puts the bulk
 * importers one misclick away.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/data/doctors-seed.php';

/** How many posts one Trash submission moves, so the request cannot time out. */
const ESTECAPELLI_DOCTOR_REPAIR_TRASH_BATCH = 30;

/** The doctor slugs the repair knows about, from the seed. */
function estecapelli_doctor_repair_slugs() {
	$slugs = array();
	if ( function_exists( 'estecapelli_doctors_seed' ) ) {
		foreach ( estecapelli_doctors_seed() as $doctor ) {
			if ( ! empty( $doctor['slug'] ) ) {
				$slugs[] = (string) $doctor['slug'];
			}
		}
	}
	return $slugs;
}

/** Indexed languages, in column order. */
function estecapelli_doctor_repair_languages() {
	return function_exists( 'estecapelli_indexed_languages' )
		? estecapelli_indexed_languages()
		: array( 'en', 'fr', 'it', 'es', 'pt', 'pl', 'tr', 'ro' );
}

/** The WPML code for an indexed language ('pt' is 'pt-pt' inside WPML). */
function estecapelli_doctor_repair_wpml_code( $language ) {
	return function_exists( 'estecapelli_wpml_language_code' )
		? (string) estecapelli_wpml_language_code( $language )
		: (string) $language;
}

/** Every live post carrying one doctor slug, oldest first. */
function estecapelli_doctor_repair_posts_by_slug( $slug ) {
	global $wpdb;
	return (array) $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_status, post_title
			 FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = 'doctor' AND post_status NOT IN ( 'trash', 'auto-draft' )
			 ORDER BY ID ASC",
			$slug
		)
	);
}

/** The raw WPML row for a doctor post: language and trid, or empty for orphans. */
function estecapelli_doctor_repair_row( $post_id ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';
	$row   = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT trid, language_code FROM {$table}
			 WHERE element_id = %d AND element_type = 'post_doctor'
			 ORDER BY translation_id ASC LIMIT 1",
			(int) $post_id
		)
	);
	return array(
		'trid'     => $row ? (int) $row->trid : 0,
		'language' => $row ? (string) $row->language_code : '',
	);
}

/**
 * Rows on a trid filed under something that is not one of our languages.
 *
 * An 'all' row is the one that matters: WPML reads it as "show this element in
 * every language", so a single French profile surfaces site-wide.
 *
 * @return array<int,array{language:string,element_id:int}>
 */
function estecapelli_doctor_repair_stray_rows( $trid ) {
	global $wpdb;
	if ( ! $trid ) {
		return array();
	}

	$expected = array();
	foreach ( estecapelli_doctor_repair_languages() as $language ) {
		$expected[] = estecapelli_doctor_repair_wpml_code( $language );
	}

	$table = $wpdb->prefix . 'icl_translations';
	$rows  = (array) $wpdb->get_results(
		$wpdb->prepare(
			"SELECT element_id, language_code FROM {$table}
			 WHERE trid = %d AND element_type = 'post_doctor'",
			(int) $trid
		)
	);

	$stray = array();
	foreach ( $rows as $row ) {
		if ( ! in_array( (string) $row->language_code, $expected, true ) ) {
			$stray[] = array(
				'language'   => (string) $row->language_code,
				'element_id' => (int) $row->element_id,
			);
		}
	}
	return $stray;
}

/**
 * Work out, for every doctor, which post belongs in which language slot.
 *
 * A post is the candidate for a language when its stored `position` is exactly
 * the value that language's overlay carries. The field is read from post meta
 * rather than through get_field() on purpose — ACFML filters the second, and
 * the point here is to see what is actually on disk.
 *
 * @return array<string,array<string,mixed>>
 */
function estecapelli_doctor_repair_plan() {
	$expected_map = function_exists( 'estecapelli_doctor_diag_expected_positions' )
		? estecapelli_doctor_diag_expected_positions()
		: array();

	/*
	 * Without the reference values nothing can be matched to a language, and an
	 * empty plan does not read as "nothing to do" — every profile would fall
	 * into the bin queue as unclaimed. Refuse outright instead.
	 */
	if ( ! $expected_map ) {
		return array();
	}

	$languages = estecapelli_doctor_repair_languages();
	$plan      = array();

	foreach ( estecapelli_doctor_repair_slugs() as $slug ) {
		$posts     = estecapelli_doctor_repair_posts_by_slug( $slug );
		$source_id = 0;
		$trid      = 0;
		$positions = array();

		foreach ( $posts as $post ) {
			$row                       = estecapelli_doctor_repair_row( $post->ID );
			$positions[ (int) $post->ID ] = array(
				'position' => (string) get_post_meta( $post->ID, 'position', true ),
				'language' => $row['language'],
				'trid'     => $row['trid'],
				'status'   => $post->post_status,
			);
			if ( 'en' === $row['language'] && ! $source_id ) {
				$source_id = (int) $post->ID;
				$trid      = $row['trid'];
			}
		}

		$slots = array();
		$keep  = array();
		if ( $source_id ) {
			$keep[] = $source_id;
		}

		foreach ( $languages as $language ) {
			$wpml_code = estecapelli_doctor_repair_wpml_code( $language );
			$expected  = (string) ( $expected_map[ $slug ][ $language ] ?? '' );
			$current   = ( $trid && function_exists( 'estecapelli_wpml_group_element_id_raw' ) )
				? (int) estecapelli_wpml_group_element_id_raw( $trid, 'post_doctor', $wpml_code )
				: 0;

			$candidates = array();
			if ( '' !== $expected ) {
				foreach ( $positions as $post_id => $info ) {
					if ( $info['position'] === $expected ) {
						$candidates[] = (int) $post_id;
					}
				}
			}

			// A candidate already sitting in the slot stays; otherwise take the
			// oldest, which is the profile that was there before the duplicates.
			$canonical = 0;
			if ( $candidates ) {
				$canonical = in_array( $current, $candidates, true ) ? $current : min( $candidates );
			}
			if ( $canonical ) {
				$keep[] = $canonical;
			}

			$slots[ $language ] = array(
				'wpml_code'  => $wpml_code,
				'expected'   => $expected,
				'current'    => $current,
				'canonical'  => $canonical,
				'candidates' => count( $candidates ),
			);
		}

		$keep  = array_values( array_unique( array_filter( $keep ) ) );
		$extra = array();
		foreach ( $positions as $post_id => $info ) {
			if ( ! in_array( (int) $post_id, $keep, true ) ) {
				$extra[] = (int) $post_id;
			}
		}

		$plan[ $slug ] = array(
			'source_id' => $source_id,
			'trid'      => $trid,
			'total'     => count( $posts ),
			'slots'     => $slots,
			'keep'      => $keep,
			'extra'     => $extra,
			'stray'     => estecapelli_doctor_repair_stray_rows( $trid ),
			'positions' => $positions,
		);
	}

	return $plan;
}

add_action( 'admin_menu', 'estecapelli_register_doctor_repair' );
/** Put the repair under Tools. */
function estecapelli_register_doctor_repair() {
	add_management_page(
		__( 'Estecapelli — Doctor Repair', 'estecapelli' ),
		__( 'Doctor Repair', 'estecapelli' ),
		'manage_options',
		'estecapelli-doctor-repair',
		'estecapelli_render_doctor_repair'
	);
}

add_action( 'admin_post_estecapelli_doctor_repair', 'estecapelli_handle_doctor_repair' );
/** Run whichever step was submitted, then return to the report. */
function estecapelli_handle_doctor_repair() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_doctor_repair' );

	$mode   = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : '';
	$notice = 'relink' === $mode ? estecapelli_doctor_repair_relink() : '';
	if ( 'trash' === $mode ) {
		$notice = empty( $_POST['confirm'] )
			? __( 'Nothing was trashed — the confirmation box was not ticked.', 'estecapelli' )
			: estecapelli_doctor_repair_trash();
	}

	set_transient( 'estecapelli_doctor_repair_notice', $notice, MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'tools.php?page=estecapelli-doctor-repair' ) );
	exit;
}

/** Step one — put every language slot back on the right profile. */
function estecapelli_doctor_repair_relink() {
	$relinked = 0;
	$cleared  = 0;
	$failed   = array();

	foreach ( estecapelli_doctor_repair_plan() as $slug => $entry ) {
		if ( ! $entry['trid'] ) {
			$failed[] = $slug . ' — no English source to hang the group on';
			continue;
		}

		// Stray rows go first: an 'all' row occupies no real language slot but
		// still makes its post render everywhere, including over a slot this
		// loop is about to set correctly.
		foreach ( $entry['stray'] as $stray ) {
			if ( estecapelli_wpml_delete_relationship_raw( $stray['element_id'], 'post_doctor', $entry['trid'], $stray['language'] ) ) {
				++$cleared;
			}
		}

		foreach ( $entry['slots'] as $language => $slot ) {
			if ( 'en' === $language || ! $slot['canonical'] || $slot['canonical'] === $slot['current'] ) {
				continue;
			}
			$done = estecapelli_wpml_replace_language_slot_raw(
				$slot['canonical'],
				'post_doctor',
				$entry['trid'],
				$slot['wpml_code'],
				'en'
			);
			if ( $done ) {
				++$relinked;
			} else {
				$reason   = function_exists( 'estecapelli_wpml_last_slot_error' ) ? estecapelli_wpml_last_slot_error() : '';
				$failed[] = sprintf( '%s/%s%s', $slug, $language, $reason ? ' — ' . $reason : '' );
			}
		}
	}

	$message = sprintf(
		/* translators: 1: number of language slots repointed, 2: number of stray rows removed */
		__( 'Relinked %1$d language slots and removed %2$d stray rows.', 'estecapelli' ),
		$relinked,
		$cleared
	);
	if ( $failed ) {
		$message .= ' ' . __( 'Failed:', 'estecapelli' ) . ' ' . implode( '; ', $failed );
	}
	return $message;
}

/** Step two — bin one batch of the leftover duplicates. */
function estecapelli_doctor_repair_trash() {
	$trashed   = 0;
	$remaining = 0;

	$skipped = 0;

	foreach ( estecapelli_doctor_repair_plan() as $entry ) {
		// A profile still sitting in a language slot is never binned, whatever
		// the plan says about it — that would leave a trashed post serving a
		// language. It becomes binnable once step 1 has moved the slot off it,
		// which makes the two steps safe to press in either order.
		$occupied = array();
		foreach ( $entry['slots'] as $slot ) {
			if ( $slot['current'] ) {
				$occupied[] = (int) $slot['current'];
			}
		}

		foreach ( $entry['extra'] as $post_id ) {
			if ( in_array( (int) $post_id, $occupied, true ) ) {
				++$skipped;
				continue;
			}
			if ( $trashed >= ESTECAPELLI_DOCTOR_REPAIR_TRASH_BATCH ) {
				++$remaining;
				continue;
			}
			if ( wp_trash_post( $post_id ) ) {
				++$trashed;
			}
		}
	}

	if ( $skipped ) {
		return sprintf(
			/* translators: 1: number trashed, 2: number still queued, 3: number skipped */
			__( 'Moved %1$d duplicate profiles to the bin. %2$d still queued. %3$d were left alone because they still hold a language slot — run step 1 first.', 'estecapelli' ),
			$trashed,
			$remaining,
			$skipped
		);
	}

	return sprintf(
		/* translators: 1: number of posts trashed, 2: number still queued */
		__( 'Moved %1$d duplicate profiles to the bin. %2$d still queued — run it again to continue.', 'estecapelli' ),
		$trashed,
		$remaining
	);
}

/** Render the report and its two buttons. */
function estecapelli_render_doctor_repair() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$notice = (string) get_transient( 'estecapelli_doctor_repair_notice' );
	if ( '' !== $notice ) {
		delete_transient( 'estecapelli_doctor_repair_notice' );
	}

	$plan      = estecapelli_doctor_repair_plan();
	$languages = estecapelli_doctor_repair_languages();

	if ( ! $plan ) {
		echo '<div class="wrap"><h1>' . esc_html__( 'Doctor Repair', 'estecapelli' ) . '</h1>';
		echo '<div class="notice notice-error"><p>' . esc_html__( 'The reference positions could not be read from the seed and the JSON overlays, so nothing can be matched to a language. Nothing is safe to do here until that loads — check that Tools → Doctor Languages renders section 3.', 'estecapelli' ) . '</p></div></div>';
		return;
	}
	$total_extra = 0;
	$total_relink = 0;
	$total_stray = 0;
	foreach ( $plan as $entry ) {
		$total_extra += count( $entry['extra'] );
		$total_stray += count( $entry['stray'] );
		foreach ( $entry['slots'] as $language => $slot ) {
			if ( 'en' !== $language && $slot['canonical'] && $slot['canonical'] !== $slot['current'] ) {
				++$total_relink;
			}
		}
	}

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Doctor Repair', 'estecapelli' ) . '</h1>';
	if ( '' !== $notice ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $notice ) . '</p></div>';
	}
	echo '<p class="description">' . esc_html__( 'This page only reports until a button below is pressed. A profile is matched to a language by its stored position field: whichever post holds exactly that language\'s translated position is the one that belongs in its slot.', 'estecapelli' ) . '</p>';

	echo '<h2>' . esc_html__( 'Which profile belongs in which slot', 'estecapelli' ) . '</h2>';
	echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Doctor', 'estecapelli' ) . '</th>';
	foreach ( $languages as $language ) {
		echo '<th><code>' . esc_html( $language ) . '</code></th>';
	}
	echo '<th>' . esc_html__( 'Posts', 'estecapelli' ) . '</th><th>' . esc_html__( 'To bin', 'estecapelli' ) . '</th></tr></thead><tbody>';

	foreach ( $plan as $slug => $entry ) {
		echo '<tr><td><code>' . esc_html( $slug ) . '</code>';
		if ( ! $entry['source_id'] ) {
			echo '<br /><small style="color:#b32d2e;">' . esc_html__( 'no English source', 'estecapelli' ) . '</small>';
		}
		echo '</td>';

		foreach ( $languages as $language ) {
			$slot = $entry['slots'][ $language ];
			if ( ! $slot['canonical'] ) {
				echo '<td><span style="color:#8a6d00;">' . esc_html__( 'none found', 'estecapelli' ) . '</span></td>';
				continue;
			}
			if ( $slot['canonical'] === $slot['current'] ) {
				echo '<td><span style="color:#1a7f37;">' . (int) $slot['canonical'] . ' ' . esc_html__( 'OK', 'estecapelli' ) . '</span></td>';
				continue;
			}
			echo '<td><strong>' . (int) $slot['current'] . '</strong> &rarr; <strong style="color:#b32d2e;">' . (int) $slot['canonical'] . '</strong></td>';
		}

		echo '<td>' . (int) $entry['total'] . '</td>';
		echo '<td>' . (int) count( $entry['extra'] ) . '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';

	if ( $total_stray ) {
		echo '<p><strong>' . esc_html(
			sprintf(
				/* translators: %d: number of rows */
				__( '%d rows are filed under a language WPML does not have (an "all" row shows one profile in every language). Relink removes them.', 'estecapelli' ),
				$total_stray
			)
		) . '</strong></p>';
	}

	echo '<h2>' . esc_html__( 'Step 1 — relink the language slots', 'estecapelli' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Repoints each slot at the profile above. It writes only WPML relationship rows; no profile content is touched and no post is deleted. This is the step that fixes what visitors see.', 'estecapelli' ) . '</p>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	echo '<input type="hidden" name="action" value="estecapelli_doctor_repair">';
	echo '<input type="hidden" name="mode" value="relink">';
	wp_nonce_field( 'estecapelli_doctor_repair' );
	echo '<button type="submit" class="button button-primary"' . ( $total_relink || $total_stray ? '' : ' disabled' ) . '>';
	echo esc_html(
		sprintf(
			/* translators: %d: number of slots */
			__( 'Relink %d slots', 'estecapelli' ),
			$total_relink
		)
	);
	echo '</button></form>';

	echo '<h2>' . esc_html__( 'Step 2 — bin the duplicates', 'estecapelli' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Every profile not claimed by a language above. They go to the bin, not to permanent deletion, so anything taken by mistake can be restored. Do step 1 first — until the slots are right, a profile that is still needed can look unclaimed.', 'estecapelli' ) . '</p>';
	echo '<p><strong>' . esc_html(
		sprintf(
			/* translators: 1: number queued, 2: batch size */
			__( '%1$d profiles queued, %2$d per press.', 'estecapelli' ),
			$total_extra,
			ESTECAPELLI_DOCTOR_REPAIR_TRASH_BATCH
		)
	) . '</strong></p>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	echo '<input type="hidden" name="action" value="estecapelli_doctor_repair">';
	echo '<input type="hidden" name="mode" value="trash">';
	wp_nonce_field( 'estecapelli_doctor_repair' );
	echo '<p><label><input type="checkbox" name="confirm" value="1"> ' . esc_html__( 'Yes, move these profiles to the bin.', 'estecapelli' ) . '</label></p>';
	echo '<button type="submit" class="button button-secondary"' . ( $total_extra ? '' : ' disabled' ) . '>' . esc_html__( 'Bin one batch', 'estecapelli' ) . '</button>';
	echo '</form>';

	echo '<h2>' . esc_html__( 'The profiles queued for the bin', 'estecapelli' ) . '</h2>';
	echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'ID', 'estecapelli' ) . '</th><th>' . esc_html__( 'Doctor', 'estecapelli' ) . '</th><th>' . esc_html__( 'WPML row', 'estecapelli' ) . '</th><th>' . esc_html__( 'position', 'estecapelli' ) . '</th></tr></thead><tbody>';
	foreach ( $plan as $slug => $entry ) {
		foreach ( $entry['extra'] as $post_id ) {
			$info = $entry['positions'][ $post_id ];
			echo '<tr>';
			echo '<td>' . (int) $post_id . '</td>';
			echo '<td><code>' . esc_html( $slug ) . '</code></td>';
			echo '<td><code>' . esc_html( $info['language'] ? $info['language'] : 'orphan' ) . '</code></td>';
			echo '<td><small>' . esc_html( $info['position'] ) . '</small></td>';
			echo '</tr>';
		}
	}
	echo '</tbody></table>';
	echo '</div>';
}
