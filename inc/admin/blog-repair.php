<?php
/**
 * Blog repair — the doctor repair, applied to the posts that went the same way.
 *
 * Nine articles own around 130 posts. The unattended import sweep retried a
 * failing item on every wp-admin page load and left a post behind each time,
 * and claiming a WPML language slot deletes the previous occupant's row without
 * touching the post — so the leftovers became orphans. An orphaned post has no
 * language, so it loses its /fr/ or /es/ prefix and gets served from the root:
 * 76 of the URLs in post-sitemap.xml are bare /blog/… copies.
 *
 * Matching here is safer than it was for the doctors. The article a post is
 * meant to be is written in its slug, and inc/indexed-urls.php already holds
 * the frozen contract of which slug belongs to which language. Nothing outside
 * that contract is ever read, relinked or binned.
 *
 * Three steps, each its own button:
 *
 *   Relink — point each language slot at the post carrying that language's
 *            contract slug.
 *   Slug   — restore a contract slug the database has drifted from. Two Spanish
 *            articles are stored as `es-doloroso-…` while the indexed URL is
 *            `/es/blog/-es-doloroso-…`, the leading dash being what is left of
 *            the opening `¿`. Both currently 301 to the blog listing, so the
 *            articles cannot be read at all.
 *   Bin    — the leftover copies, in batches, recoverable.
 *
 * Read-only until a button is pressed, and behind its own constant like the
 * other repairs.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** How many posts one Bin submission moves, so the request cannot time out. */
const ESTECAPELLI_BLOG_REPAIR_TRASH_BATCH = 30;

/**
 * Every post carrying one exact slug, unfiltered by language.
 *
 * @return array<int,object>
 */
function estecapelli_blog_repair_posts_by_slug( $slug ) {
	global $wpdb;
	if ( '' === (string) $slug ) {
		return array();
	}
	return (array) $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_name, post_status, post_title
			 FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = 'post' AND post_status NOT IN ( 'trash', 'auto-draft' )
			 ORDER BY ID ASC",
			$slug
		)
	);
}

/**
 * The slugs a post for this contract entry could legitimately be stored under.
 *
 * WordPress will not always keep a leading dash, so the contract slug and its
 * trimmed form are both accepted as the same article. Only the contract form is
 * ever written back.
 *
 * @return array<int,string>
 */
function estecapelli_blog_repair_slug_variants( $slug ) {
	$variants = array( (string) $slug );
	$trimmed  = ltrim( (string) $slug, '-' );
	if ( '' !== $trimmed && $trimmed !== $slug ) {
		$variants[] = $trimmed;
	}
	return $variants;
}

/** The raw WPML row for a post: language and trid, empty for an orphan. */
function estecapelli_blog_repair_row( $post_id ) {
	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';
	$row   = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT trid, language_code FROM {$table}
			 WHERE element_id = %d AND element_type = 'post_post'
			 ORDER BY translation_id ASC LIMIT 1",
			(int) $post_id
		)
	);
	return array(
		'trid'     => $row ? (int) $row->trid : 0,
		'language' => $row ? (string) $row->language_code : '',
	);
}

/** The WPML code for an indexed language ('pt' is 'pt-pt' inside WPML). */
function estecapelli_blog_repair_wpml_code( $language ) {
	return function_exists( 'estecapelli_wpml_language_code' )
		? (string) estecapelli_wpml_language_code( $language )
		: (string) $language;
}

/**
 * Work out, for every article, which post belongs in which language slot.
 *
 * @return array<string,array<string,mixed>>
 */
function estecapelli_blog_repair_plan() {
	if ( ! function_exists( 'estecapelli_indexed_blog_slugs' ) ) {
		return array();
	}

	$contract = estecapelli_indexed_blog_slugs();
	if ( ! $contract ) {
		return array();
	}

	$plan = array();

	foreach ( $contract as $source_slug => $by_language ) {
		if ( ! is_array( $by_language ) || empty( $by_language['en'] ) ) {
			continue;
		}

		// Every post carrying any slug this article owns, in any language.
		$owned = array();
		foreach ( $by_language as $slug ) {
			foreach ( estecapelli_blog_repair_slug_variants( $slug ) as $variant ) {
				foreach ( estecapelli_blog_repair_posts_by_slug( $variant ) as $post ) {
					$row                          = estecapelli_blog_repair_row( $post->ID );
					$owned[ (int) $post->ID ] = array(
						'slug'     => (string) $post->post_name,
						'title'    => (string) $post->post_title,
						'status'   => (string) $post->post_status,
						'language' => $row['language'],
						'trid'     => $row['trid'],
					);
				}
			}
		}

		$source_id = 0;
		$trid      = 0;
		foreach ( $owned as $post_id => $info ) {
			if ( 'en' === $info['language'] && in_array( $info['slug'], estecapelli_blog_repair_slug_variants( $by_language['en'] ), true ) ) {
				$source_id = (int) $post_id;
				$trid      = (int) $info['trid'];
				break;
			}
		}

		$slots = array();
		$keep  = array();
		if ( $source_id ) {
			$keep[] = $source_id;
		}

		foreach ( $by_language as $language => $expected_slug ) {
			$wpml_code = estecapelli_blog_repair_wpml_code( $language );
			$variants  = estecapelli_blog_repair_slug_variants( $expected_slug );
			$current   = ( $trid && function_exists( 'estecapelli_wpml_group_element_id_raw' ) )
				? (int) estecapelli_wpml_group_element_id_raw( $trid, 'post_post', $wpml_code )
				: 0;

			$candidates = array();
			foreach ( $owned as $post_id => $info ) {
				if ( in_array( $info['slug'], $variants, true ) ) {
					$candidates[] = (int) $post_id;
				}
			}

			// A candidate already in the slot stays; otherwise the oldest, which
			// is the post that held the slot before the duplicates piled up.
			$canonical = 0;
			if ( $candidates ) {
				$canonical = in_array( $current, $candidates, true ) ? $current : min( $candidates );
			}
			if ( $canonical ) {
				$keep[] = $canonical;
			}

			$slots[ $language ] = array(
				'wpml_code'  => $wpml_code,
				'expected'   => (string) $expected_slug,
				'current'    => $current,
				'canonical'  => $canonical,
				'stored'     => $canonical ? $owned[ $canonical ]['slug'] : '',
				'candidates' => count( $candidates ),
			);
		}

		$keep  = array_values( array_unique( array_filter( $keep ) ) );
		$extra = array();
		foreach ( $owned as $post_id => $info ) {
			if ( ! in_array( (int) $post_id, $keep, true ) ) {
				$extra[] = (int) $post_id;
			}
		}

		$plan[ $source_slug ] = array(
			'source_id' => $source_id,
			'trid'      => $trid,
			'total'     => count( $owned ),
			'slots'     => $slots,
			'keep'      => $keep,
			'extra'     => $extra,
			'owned'     => $owned,
		);
	}

	return $plan;
}

add_action( 'admin_menu', 'estecapelli_register_blog_repair' );
/** Put the repair under Tools. */
function estecapelli_register_blog_repair() {
	add_management_page(
		__( 'Estecapelli — Blog Repair', 'estecapelli' ),
		__( 'Blog Repair', 'estecapelli' ),
		'manage_options',
		'estecapelli-blog-repair',
		'estecapelli_render_blog_repair'
	);
}

add_action( 'admin_post_estecapelli_blog_repair', 'estecapelli_handle_blog_repair' );
/** Run whichever step was submitted, then return to the report. */
function estecapelli_handle_blog_repair() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_blog_repair' );

	$mode   = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : '';
	$notice = '';

	if ( 'relink' === $mode ) {
		$notice = estecapelli_blog_repair_relink();
	} elseif ( 'slugs' === $mode ) {
		$notice = estecapelli_blog_repair_slugs();
	} elseif ( 'trash' === $mode ) {
		$notice = empty( $_POST['confirm'] )
			? __( 'Nothing was binned — the confirmation box was not ticked.', 'estecapelli' )
			: estecapelli_blog_repair_trash();
	}

	set_transient( 'estecapelli_blog_repair_notice', $notice, MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'tools.php?page=estecapelli-blog-repair' ) );
	exit;
}

/** Step one — put every language slot back on the right post. */
function estecapelli_blog_repair_relink() {
	$relinked = 0;
	$failed   = array();

	foreach ( estecapelli_blog_repair_plan() as $source_slug => $entry ) {
		if ( ! $entry['trid'] ) {
			$failed[] = $source_slug . ' — no English source to hang the group on';
			continue;
		}
		foreach ( $entry['slots'] as $language => $slot ) {
			if ( 'en' === $language || ! $slot['canonical'] || $slot['canonical'] === $slot['current'] ) {
				continue;
			}
			$done = estecapelli_wpml_replace_language_slot_raw(
				$slot['canonical'],
				'post_post',
				$entry['trid'],
				$slot['wpml_code'],
				'en'
			);
			if ( $done ) {
				++$relinked;
			} else {
				$reason   = function_exists( 'estecapelli_wpml_last_slot_error' ) ? estecapelli_wpml_last_slot_error() : '';
				$failed[] = sprintf( '%s/%s%s', $source_slug, $language, $reason ? ' — ' . $reason : '' );
			}
		}
	}

	$message = sprintf(
		/* translators: %d: number of language slots repointed */
		__( 'Relinked %d language slots.', 'estecapelli' ),
		$relinked
	);
	if ( $failed ) {
		$message .= ' ' . __( 'Failed:', 'estecapelli' ) . ' ' . implode( '; ', $failed );
	}
	return $message;
}

/** Step two — restore contract slugs the database has drifted from. */
function estecapelli_blog_repair_slugs() {
	global $wpdb;
	$fixed  = 0;
	$failed = array();

	foreach ( estecapelli_blog_repair_plan() as $source_slug => $entry ) {
		foreach ( $entry['slots'] as $language => $slot ) {
			if ( ! $slot['canonical'] || '' === $slot['expected'] || $slot['stored'] === $slot['expected'] ) {
				continue;
			}

			/*
			 * Written straight to the column. wp_update_post() runs the slug
			 * through wp_unique_post_slug(), which is what dropped the leading
			 * dash in the first place and would drop it again.
			 */
			$saved = $wpdb->update(
				$wpdb->posts,
				array( 'post_name' => $slot['expected'] ),
				array( 'ID' => (int) $slot['canonical'] ),
				array( '%s' ),
				array( '%d' )
			);
			if ( false === $saved ) {
				$failed[] = $source_slug . '/' . $language;
				continue;
			}
			clean_post_cache( (int) $slot['canonical'] );
			++$fixed;
		}
	}

	$message = sprintf(
		/* translators: %d: number of slugs restored */
		__( 'Restored %d slugs to the indexed contract.', 'estecapelli' ),
		$fixed
	);
	if ( $failed ) {
		$message .= ' ' . __( 'Failed:', 'estecapelli' ) . ' ' . implode( '; ', $failed );
	}
	return $message;
}

/** Step three — bin one batch of the leftover copies. */
function estecapelli_blog_repair_trash() {
	$trashed   = 0;
	$remaining = 0;
	$skipped   = 0;

	foreach ( estecapelli_blog_repair_plan() as $entry ) {
		// A post still sitting in a language slot is never binned, whatever the
		// plan says — that would leave a binned post serving a language.
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
			if ( $trashed >= ESTECAPELLI_BLOG_REPAIR_TRASH_BATCH ) {
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
			/* translators: 1: number binned, 2: number still queued, 3: number skipped */
			__( 'Binned %1$d copies. %2$d still queued. %3$d were left alone because they still hold a language slot — run step 1 first.', 'estecapelli' ),
			$trashed,
			$remaining,
			$skipped
		);
	}

	return sprintf(
		/* translators: 1: number binned, 2: number still queued */
		__( 'Binned %1$d copies. %2$d still queued — run it again to continue.', 'estecapelli' ),
		$trashed,
		$remaining
	);
}

/** Render the report and its three buttons. */
function estecapelli_render_blog_repair() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$notice = (string) get_transient( 'estecapelli_blog_repair_notice' );
	if ( '' !== $notice ) {
		delete_transient( 'estecapelli_blog_repair_notice' );
	}

	$plan = estecapelli_blog_repair_plan();

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Blog Repair', 'estecapelli' ) . '</h1>';
	if ( '' !== $notice ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $notice ) . '</p></div>';
	}

	if ( ! $plan ) {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'The indexed blog contract could not be read, so no post can be matched to an article. Nothing is safe to do here until that loads.', 'estecapelli' ) . '</p></div></div>';
		return;
	}

	echo '<p class="description">' . esc_html__( 'Read-only until a button is pressed. A post is matched to an article and a language by its slug, against the frozen contract in inc/indexed-urls.php. Posts whose slug is not in that contract are never touched.', 'estecapelli' ) . '</p>';

	$total_relink = 0;
	$total_slugs  = 0;
	$total_extra  = 0;
	foreach ( $plan as $entry ) {
		$total_extra += count( $entry['extra'] );
		foreach ( $entry['slots'] as $language => $slot ) {
			if ( 'en' !== $language && $slot['canonical'] && $slot['canonical'] !== $slot['current'] ) {
				++$total_relink;
			}
			if ( $slot['canonical'] && '' !== $slot['expected'] && $slot['stored'] !== $slot['expected'] ) {
				++$total_slugs;
			}
		}
	}

	echo '<h2>' . esc_html__( 'Every article, language by language', 'estecapelli' ) . '</h2>';
	echo '<table class="widefat striped"><thead><tr>';
	echo '<th>' . esc_html__( 'Article', 'estecapelli' ) . '</th>';
	echo '<th>' . esc_html__( 'Language', 'estecapelli' ) . '</th>';
	echo '<th>' . esc_html__( 'In the slot', 'estecapelli' ) . '</th>';
	echo '<th>' . esc_html__( 'Should be', 'estecapelli' ) . '</th>';
	echo '<th>' . esc_html__( 'Slug', 'estecapelli' ) . '</th>';
	echo '<th>' . esc_html__( 'Copies', 'estecapelli' ) . '</th>';
	echo '</tr></thead><tbody>';

	foreach ( $plan as $source_slug => $entry ) {
		foreach ( $entry['slots'] as $language => $slot ) {
			$slug_wrong = $slot['canonical'] && '' !== $slot['expected'] && $slot['stored'] !== $slot['expected'];
			$slot_wrong = 'en' !== $language && $slot['canonical'] && $slot['canonical'] !== $slot['current'];
			if ( ! $slug_wrong && ! $slot_wrong && $slot['candidates'] < 2 ) {
				continue; // nothing to say about this one.
			}

			echo '<tr>';
			echo '<td><code>' . esc_html( $source_slug ) . '</code></td>';
			echo '<td><code>' . esc_html( $language ) . '</code></td>';
			echo '<td>' . ( $slot['current'] ? (int) $slot['current'] : '<span style="color:#b32d2e;">' . esc_html__( 'empty', 'estecapelli' ) . '</span>' ) . '</td>';
			echo '<td>' . ( $slot['canonical'] ? (int) $slot['canonical'] : '<span style="color:#8a6d00;">' . esc_html__( 'none found', 'estecapelli' ) . '</span>' ) . '</td>';
			if ( $slug_wrong ) {
				echo '<td><code>' . esc_html( $slot['stored'] ) . '</code><br /><strong style="color:#b32d2e;">&rarr; <code>' . esc_html( $slot['expected'] ) . '</code></strong></td>';
			} else {
				echo '<td><code>' . esc_html( $slot['expected'] ) . '</code></td>';
			}
			echo '<td>' . (int) $slot['candidates'] . '</td>';
			echo '</tr>';
		}
	}
	echo '</tbody></table>';

	/*
	 * An article whose English source has gone cannot be relinked — there is no
	 * group to hang a translation on — so its posts are neither repaired nor
	 * binned, and an orphan among them is unreachable from the Posts screen:
	 * having no language at all, it does not appear under any language filter,
	 * "All languages" included. Listing it here with an edit link is the only
	 * way to get at it.
	 */
	$stranded = array();
	foreach ( $plan as $source_slug => $entry ) {
		if ( $entry['trid'] ) {
			continue;
		}
		foreach ( $entry['owned'] as $post_id => $info ) {
			$stranded[] = array( 'source' => $source_slug, 'id' => (int) $post_id ) + $info;
		}
	}

	if ( $stranded ) {
		echo '<h2>' . esc_html__( 'Stranded — no English source for these articles', 'estecapelli' ) . '</h2>';
		echo '<p class="description">' . esc_html__( 'The contract still lists these articles, but the English post they translate no longer carries that slug — it was renamed. Nothing here can be relinked, and nothing is queued for the bin: an article that cannot be verified is left alone. Open each one, compare it with the article the contract entry was renamed to, and bin it from the dashboard if it is the same text.', 'estecapelli' ) . '</p>';
		echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'ID', 'estecapelli' ) . '</th><th>' . esc_html__( 'Contract entry', 'estecapelli' ) . '</th><th>' . esc_html__( 'Slug', 'estecapelli' ) . '</th><th>' . esc_html__( 'WPML row', 'estecapelli' ) . '</th><th>' . esc_html__( 'Title', 'estecapelli' ) . '</th><th>' . esc_html__( 'Open', 'estecapelli' ) . '</th></tr></thead><tbody>';
		foreach ( $stranded as $row ) {
			echo '<tr>';
			echo '<td>' . (int) $row['id'] . '</td>';
			echo '<td><code>' . esc_html( $row['source'] ) . '</code></td>';
			echo '<td><code>' . esc_html( $row['slug'] ) . '</code></td>';
			echo '<td><code>' . esc_html( $row['language'] ? $row['language'] : 'orphan' ) . '</code></td>';
			echo '<td><small>' . esc_html( $row['title'] ) . '</small></td>';
			echo '<td><a href="' . esc_url( get_edit_post_link( $row['id'], 'raw' ) ) . '">' . esc_html__( 'Edit', 'estecapelli' ) . '</a></td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}

	echo '<h2>' . esc_html__( 'Step 1 — relink the language slots', 'estecapelli' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Writes only WPML relationship rows. No post content is touched and nothing is deleted. This is what gives the orphans their language prefix back.', 'estecapelli' ) . '</p>';
	estecapelli_blog_repair_button( 'relink', sprintf( __( 'Relink %d slots', 'estecapelli' ), $total_relink ), $total_relink > 0 );

	echo '<h2>' . esc_html__( 'Step 2 — restore the contract slugs', 'estecapelli' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Only where the stored slug differs from the indexed one, which is why two Spanish articles currently redirect to the blog listing instead of opening. Run step 1 first, so the slug is written to the post that will actually serve the URL.', 'estecapelli' ) . '</p>';
	estecapelli_blog_repair_button( 'slugs', sprintf( __( 'Restore %d slugs', 'estecapelli' ), $total_slugs ), $total_slugs > 0 );

	echo '<h2>' . esc_html__( 'Step 3 — bin the copies', 'estecapelli' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Every copy not claimed by a language above. They go to the bin, not to permanent deletion. A post still holding a slot is skipped rather than binned.', 'estecapelli' ) . '</p>';
	echo '<p><strong>' . esc_html(
		sprintf(
			/* translators: 1: number queued, 2: batch size */
			__( '%1$d copies queued, %2$d per press.', 'estecapelli' ),
			$total_extra,
			ESTECAPELLI_BLOG_REPAIR_TRASH_BATCH
		)
	) . '</strong></p>';
	estecapelli_blog_repair_button( 'trash', __( 'Bin one batch', 'estecapelli' ), $total_extra > 0, true );

	echo '<h2>' . esc_html__( 'The copies queued for the bin', 'estecapelli' ) . '</h2>';
	echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'ID', 'estecapelli' ) . '</th><th>' . esc_html__( 'Slug', 'estecapelli' ) . '</th><th>' . esc_html__( 'WPML row', 'estecapelli' ) . '</th><th>' . esc_html__( 'Title', 'estecapelli' ) . '</th></tr></thead><tbody>';
	foreach ( $plan as $entry ) {
		foreach ( $entry['extra'] as $post_id ) {
			$info = $entry['owned'][ $post_id ];
			echo '<tr>';
			echo '<td>' . (int) $post_id . '</td>';
			echo '<td><code>' . esc_html( $info['slug'] ) . '</code></td>';
			echo '<td><code>' . esc_html( $info['language'] ? $info['language'] : 'orphan' ) . '</code></td>';
			echo '<td><small>' . esc_html( $info['title'] ) . '</small></td>';
			echo '</tr>';
		}
	}
	echo '</tbody></table>';
	echo '</div>';
}

/**
 * One submit form for a repair step.
 *
 * @param string $mode    Step identifier.
 * @param string $label   Button text.
 * @param bool   $enabled Whether there is anything to do.
 * @param bool   $confirm Whether to require a ticked box first.
 * @return void
 */
function estecapelli_blog_repair_button( $mode, $label, $enabled, $confirm = false ) {
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	echo '<input type="hidden" name="action" value="estecapelli_blog_repair">';
	echo '<input type="hidden" name="mode" value="' . esc_attr( $mode ) . '">';
	wp_nonce_field( 'estecapelli_blog_repair' );
	if ( $confirm ) {
		echo '<p><label><input type="checkbox" name="confirm" value="1"> ' . esc_html__( 'Yes, move these copies to the bin.', 'estecapelli' ) . '</label></p>';
	}
	echo '<button type="submit" class="button button-primary"' . ( $enabled ? '' : ' disabled' ) . '>' . esc_html( $label ) . '</button>';
	echo '</form>';
}
