<?php
/**
 * Careers — open roles at the clinic, and the applications they bring in.
 *
 * A vacancy is a `job` post: the title is the role, the editor body is the
 * description, and the short ACF form beside it carries the facts a candidate
 * scans for (location, contract, department, seniority, closing date). The
 * listing lives at /en/about-us/careers and each role at
 * /en/about-us/careers/{slug} — shared across all language menus, so
 * a new role appears on the site the moment it is published, with no page
 * nesting and no roster to edit.
 *
 * Applications are emailed, never stored. The CV is attached to the message and
 * the uploaded file is deleted from disk in the same request, so a candidate's
 * document never sits in the media library on a public URL — which is exactly
 * where CVs uploaded through a WordPress form usually end up.
 *
 * Anti-spam: the application form reuses the lead primitives from inc/leads.php
 * (signed form stamp, honeypot, timer, rate limit and Turnstile), so the one
 * hardened path covers this form too.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Configuration
 * ---------------------------------------------------------------------- */

/**
 * Where applications are sent. Override in wp-config.php if HR uses a different
 * inbox:
 *   define( 'ESTECAPELLI_CAREERS_TO', 'hr@estecapelli.com' );
 */
if ( ! defined( 'ESTECAPELLI_CAREERS_TO' ) ) {
	define( 'ESTECAPELLI_CAREERS_TO', 'hr@estecapelli.com' );
}

/** Largest CV we accept, in bytes. Anything bigger is a portfolio, not a CV. */
if ( ! defined( 'ESTECAPELLI_CV_MAX_BYTES' ) ) {
	define( 'ESTECAPELLI_CV_MAX_BYTES', 5 * MB_IN_BYTES );
}

/**
 * Document types a CV is allowed to be.
 *
 * Deliberately short. Every extra type is another parser sitting on the
 * clinic's mail server, and nobody has ever needed to send a CV as a .zip.
 *
 * @return array<string,string> extension pattern => mime type.
 */
function estecapelli_cv_mime_types() {
	return (array) apply_filters(
		'estecapelli_cv_mime_types',
		array(
			'pdf'  => 'application/pdf',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'rtf'  => 'application/rtf',
			'odt'  => 'application/vnd.oasis.opendocument.text',
		)
	);
}

/**
 * Where applications actually go.
 *
 * Kept out of the page fields on purpose: the application handler runs with no
 * page in scope, so an inbox that lived on the Careers page would be
 * unreadable at exactly the moment it is needed. wp-config wins, a filter can
 * override it, and the constant above is the default.
 *
 * @return string
 */
function estecapelli_careers_inbox() {
	return (string) apply_filters( 'estecapelli_careers_email_to', ESTECAPELLI_CAREERS_TO, null );
}

/**
 * One piece of editable copy from the Careers page, or the theme's own wording
 * when the editor has not set it.
 *
 * Every string on that page goes through here, which is what lets the whole
 * page be edited in wp-admin — and translated per language by WPML, since the
 * fields live on the page post like any other content — while still rendering
 * properly on a page that was just created and has nothing but a title.
 *
 * @param string $field    ACF field name on the Careers page.
 * @param string $fallback Theme string to use when the field is empty.
 * @return string
 */
function estecapelli_careers_field( $field, $fallback = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}
	$value = trim( (string) get_field( $field, get_the_ID() ) );
	return '' !== $value ? $value : $fallback;
}

/* -------------------------------------------------------------------------
 * The `job` post type
 * ---------------------------------------------------------------------- */

/**
 * Register the vacancy post type.
 *
 * Priority 0 to match the other theme post types, so the rewrite rules are in
 * place before anything reads them.
 */
function estecapelli_register_job_cpt() {
	register_post_type(
		'job',
		array(
			'labels'              => array(
				'name'                  => __( 'Careers', 'estecapelli' ),
				'singular_name'         => __( 'Job', 'estecapelli' ),
				'menu_name'             => __( 'Careers', 'estecapelli' ),
				'add_new'               => __( 'Add Role', 'estecapelli' ),
				'add_new_item'          => __( 'Add New Role', 'estecapelli' ),
				'new_item'              => __( 'New Role', 'estecapelli' ),
				'edit_item'             => __( 'Edit Role', 'estecapelli' ),
				'view_item'             => __( 'View Role', 'estecapelli' ),
				'all_items'             => __( 'All Roles', 'estecapelli' ),
				'search_items'          => __( 'Search Roles', 'estecapelli' ),
				'not_found'             => __( 'No open roles yet. Click “Add Role” to post one.', 'estecapelli' ),
				'not_found_in_trash'    => __( 'No roles in the trash.', 'estecapelli' ),
			),
			'public'              => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-id-alt',
			'menu_position'       => 27,
			'has_archive'         => false,
			// Same treatment as the doctor profiles: WPML adds the language
			// prefix and strips it from incoming requests, so it must NOT be
			// baked into the slug here.
			'rewrite'             => array( 'slug' => 'about-us/careers', 'with_front' => false ),
			'supports'            => array( 'title', 'editor', 'excerpt', 'page-attributes' ),
			'show_in_nav_menus'   => true,
		)
	);

	// The listing page and the vacancies share a base path, so WordPress
	// resolves /about-us/careers/{slug} as a child page of the listing and 404s
	// before the post-type rule is ever reached. Exactly the problem the doctor
	// profiles have, solved the same way.
	add_rewrite_rule(
		'^(?:en/)?about-us/careers/([^/]+)/?$',
		'index.php?job=$matches[1]',
		'top'
	);
}
add_action( 'init', 'estecapelli_register_job_cpt', 0 );

/**
 * All menus share the English Careers page, and every role uses its English URL.
 * Run after WPML's permalink filters, including when editing in another language.
 * Keep WordPress's placeholder for sample permalinks in the editor.
 */
function estecapelli_job_permalink( $url, $post, $leavename = false ) {
	if ( ! $post || 'job' !== $post->post_type ) {
		return $url;
	}
	$slug = $leavename ? '%job%' : $post->post_name;
	if ( ! $slug ) {
		return $url;
	}
	return estecapelli_unfiltered_home_url() . user_trailingslashit( '/en/about-us/careers/' . $slug, 'single' );
}
add_filter( 'post_type_link', 'estecapelli_job_permalink', 1000, 3 );

/**
 * Jobs are one shared collection, even if WPML still has the old translatable
 * setting cached. The listing already uses get_posts() with suppressed filters;
 * use the same policy for the main job query and the Careers admin list.
 *
 * This leaves WordPress's status/capability checks intact. Do not apply it to
 * mixed queries, other post types, or secondary queries elsewhere on the site.
 *
 * @param WP_Query $query Main query being prepared.
 */
function estecapelli_shared_job_query( $query ) {
	if ( ! $query->is_main_query() || 'job' !== $query->get( 'post_type' ) ) {
		return;
	}
	$query->set( 'suppress_filters', true );
}
add_action( 'pre_get_posts', 'estecapelli_shared_job_query', PHP_INT_MAX );

/**
 * Open roles, in the order the editor arranged them.
 *
 * @param int $limit Maximum roles to return.
 * @return WP_Post[]
 */
function estecapelli_open_jobs( $limit = 50 ) {
	return get_posts(
		array(
			'post_type'      => 'job',
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, (int) $limit ),
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		)
	);
}

/**
 * The facts a candidate scans for before reading a word of the description.
 *
 * Every one is optional: a role with nothing filled in still renders, it just
 * shows no meta chips. Returned as label/value/icon so the listing card and the
 * single page can render the same set without agreeing on field names.
 *
 * @param int $job_id Vacancy post ID.
 * @return array<int,array{key:string,label:string,value:string,icon:string}>
 */
function estecapelli_job_meta( $job_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$fields = array(
		'location'   => array( __( 'Location', 'estecapelli' ), 'map-pin' ),
		'type'       => array( __( 'Contract', 'estecapelli' ), 'calendar' ),
		'department' => array( __( 'Department', 'estecapelli' ), 'building' ),
		'experience' => array( __( 'Experience', 'estecapelli' ), 'target' ),
	);

	$meta = array();
	foreach ( $fields as $name => $spec ) {
		$value = trim( (string) get_field( $name, $job_id ) );
		if ( '' === $value ) {
			continue;
		}
		$meta[] = array(
			'key'   => $name,
			'label' => $spec[0],
			'value' => $value,
			'icon'  => $spec[1],
		);
	}

	return $meta;
}

/**
 * Is this role still accepting applications?
 *
 * A closing date is optional, and a role without one stays open until the
 * clinic unpublishes it. A date that has passed hides the form and says so,
 * rather than taking an application nobody will read.
 *
 * @param int $job_id Vacancy post ID.
 * @return bool
 */
function estecapelli_job_is_open( $job_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return true;
	}
	$closes = trim( (string) get_field( 'closes_on', $job_id ) );
	if ( '' === $closes ) {
		return true;
	}
	// ACF returns Ymd for a date picker with that return format.
	$timestamp = strtotime( $closes );
	if ( ! $timestamp ) {
		return true;
	}

	// Compared in the site's own timezone: a deadline is a local date, and
	// closing a vacancy hours early because the server runs on UTC is the kind
	// of detail a candidate in Istanbul notices and we never would.
	return $timestamp >= (int) strtotime( current_time( 'Y-m-d' ) );
}

/* -------------------------------------------------------------------------
 * Applications
 * ---------------------------------------------------------------------- */

/**
 * Map an application error code to the message the candidate sees.
 *
 * @param string $code Error code.
 * @return string
 */
function estecapelli_application_error_message( $code ) {
	$map = array(
		'missing_name'  => __( 'Please enter your name.', 'estecapelli' ),
		'missing_email' => __( 'Please enter your email address.', 'estecapelli' ),
		'invalid_email' => __( 'Please enter a valid email address.', 'estecapelli' ),
		'missing_cv'    => __( 'Please attach your CV.', 'estecapelli' ),
		'cv_too_large'  => sprintf(
			/* translators: %s: file size limit, e.g. "5 MB" */
			__( 'Your CV is larger than %s. Please attach a smaller file.', 'estecapelli' ),
			size_format( ESTECAPELLI_CV_MAX_BYTES )
		),
		'cv_type'       => __( 'Please attach your CV as a PDF or Word document.', 'estecapelli' ),
		'cv_failed'     => __( 'Your CV could not be uploaded. Please try again.', 'estecapelli' ),
		'form_expired'  => __( 'Please refresh the page and submit the form again.', 'estecapelli' ),
		'rate_limited'  => __( 'Too many requests. Please wait a few minutes and try again.', 'estecapelli' ),
		'send_failed'   => __( 'Your application could not be sent. Please email us directly instead.', 'estecapelli' ),
	);
	return $map[ $code ] ?? __( 'Something went wrong. Please try again.', 'estecapelli' );
}

/**
 * Accept an application: validate it, email it to HR with the CV attached, and
 * leave nothing behind on disk.
 *
 * Runs on template_redirect like the lead handler, and redirects back to the
 * vacancy either way — success or a message the candidate can act on. An
 * application is never silently dropped.
 */
function estecapelli_handle_application() {
	if ( empty( $_POST['estecapelli_apply_nonce'] ) || ! is_scalar( $_POST['estecapelli_apply_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['estecapelli_apply_nonce'] ) ), 'estecapelli_apply' ) ) {
		return;
	}

	$job_id = isset( $_POST['job_id'] ) ? absint( $_POST['job_id'] ) : 0;
	$job    = $job_id ? get_post( $job_id ) : null;
	$return = $job ? get_permalink( $job ) : home_url( '/' );

	$error = estecapelli_process_application( $job );

	wp_safe_redirect(
		add_query_arg(
			$error
				? array( 'apply_error' => rawurlencode( $error ) )
				: array( 'applied' => '1' ),
			$return
		) . '#apply'
	);
	exit;
}
add_action( 'template_redirect', 'estecapelli_handle_application', 5 );

/**
 * Validate and deliver one application.
 *
 * @param WP_Post|null $job The vacancy applied for.
 * @return string Empty on success, otherwise an error code for the redirect.
 */
function estecapelli_process_application( $job ) {
	$g = static function ( $key, $filter = 'text', $max = 255 ) {
		if ( ! isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return '';
		}
		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! is_scalar( $raw ) ) {
			return '';
		}
		$value = 'email' === $filter
			? sanitize_email( $raw )
			: ( 'textarea' === $filter ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw ) );
		return function_exists( 'mb_substr' ) ? mb_substr( (string) $value, 0, $max ) : substr( (string) $value, 0, $max );
	};

	$name    = $g( 'applicant_name', 'text', 120 );
	$email   = $g( 'applicant_email', 'email', 254 );
	$phone   = $g( 'applicant_phone', 'text', 40 );
	$message = $g( 'applicant_message', 'textarea', 4000 );

	if ( '' === $name ) {
		return 'missing_name';
	}
	if ( '' === $email ) {
		return 'missing_email';
	}
	if ( ! is_email( $email ) ) {
		return 'invalid_email';
	}

	// The same stamp/honeypot/Turnstile gate every lead form goes through. It is
	// the only thing here allowed to reject on suspicion, and it fails loudly so
	// a real candidate can retry.
	if ( function_exists( 'estecapelli_check_lead_antispam' ) ) {
		$antispam = estecapelli_check_lead_antispam( array( 'source' => 'careers' ) );
		if ( is_wp_error( $antispam ) ) {
			return $antispam->get_error_code();
		}
	}
	if ( function_exists( 'estecapelli_rate_limit' ) ) {
		$limited = estecapelli_rate_limit( 'job_application', 5, HOUR_IN_SECONDS );
		if ( is_wp_error( $limited ) ) {
			return 'rate_limited';
		}
	}

	$cv = estecapelli_receive_cv();
	if ( is_wp_error( $cv ) ) {
		return $cv->get_error_code();
	}

	// Rename before sending so HR receives "ahmet-yilmaz-cv.pdf" rather than the
	// random name it was stored under. Done here, where the file is owned and
	// deleted, so there is exactly one variable holding its path.
	$friendly = trailingslashit( dirname( $cv['file'] ) ) . estecapelli_cv_attachment_name( $name, $cv );
	if ( $friendly !== $cv['file'] && rename( $cv['file'], $friendly ) ) {
		$cv['file'] = $friendly;
	}

	$sent = estecapelli_send_application( $job, $name, $email, $phone, $message, $cv );

	// The CV leaves with the email and nothing else. Deleted whether the send
	// worked or not — a failed application must not leave a stranger's CV
	// sitting in the uploads directory on a guessable URL.
	if ( ! empty( $cv['file'] ) && file_exists( $cv['file'] ) ) {
		wp_delete_file( $cv['file'] );
	}

	return $sent ? '' : 'send_failed';
}

/**
 * Take the uploaded CV, check it is really a document, and park it where it can
 * be attached.
 *
 * WordPress's own uploader does the mime sniffing (wp_check_filetype_and_ext
 * reads the file, it does not trust the extension or the browser's content
 * type), so a .pdf that is actually a PHP script is rejected here rather than
 * landing in uploads.
 *
 * @return array{file:string,name:string}|WP_Error
 */
function estecapelli_receive_cv() {
	if ( empty( $_FILES['applicant_cv'] ) || ! isset( $_FILES['applicant_cv']['name'] ) ) {
		return new WP_Error( 'missing_cv', __( 'Please attach your CV.', 'estecapelli' ) );
	}

	$file = $_FILES['applicant_cv']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- handled by wp_handle_upload below.

	if ( ! empty( $file['error'] ) ) {
		// UPLOAD_ERR_INI_SIZE / FORM_SIZE mean the server bounced it on size
		// before PHP ever saw the bytes; everything else is a broken upload.
		$oversize = in_array( (int) $file['error'], array( UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ), true );
		if ( UPLOAD_ERR_NO_FILE === (int) $file['error'] ) {
			return new WP_Error( 'missing_cv', __( 'Please attach your CV.', 'estecapelli' ) );
		}
		return new WP_Error( $oversize ? 'cv_too_large' : 'cv_failed', __( 'Your CV could not be uploaded.', 'estecapelli' ) );
	}
	if ( (int) $file['size'] > ESTECAPELLI_CV_MAX_BYTES ) {
		return new WP_Error( 'cv_too_large', __( 'Your CV is too large.', 'estecapelli' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';

	$uploaded = wp_handle_upload(
		$file,
		array(
			'test_form' => false,
			'mimes'     => estecapelli_cv_mime_types(),
			// Never registered as an attachment, so it is not in the media
			// library and not in any sitemap — it exists for the length of this
			// request and is deleted by the caller.
			'unique_filename_callback' => static function ( $dir, $filename, $ext ) {
				return 'cv-' . wp_generate_password( 12, false ) . $ext;
			},
		)
	);

	if ( isset( $uploaded['error'] ) ) {
		// wp_handle_upload reports a rejected type through the same channel as a
		// genuine failure, so the candidate is told which one it was.
		$type_rejected = false !== stripos( (string) $uploaded['error'], 'file type' );
		return new WP_Error( $type_rejected ? 'cv_type' : 'cv_failed', (string) $uploaded['error'] );
	}

	return array(
		'file' => (string) $uploaded['file'],
		'name' => sanitize_file_name( (string) $file['name'] ),
	);
}

/**
 * Email one application to HR, CV attached.
 *
 * @param WP_Post|null $job     Vacancy applied for.
 * @param string       $name    Candidate name.
 * @param string       $email   Candidate email.
 * @param string       $phone   Candidate phone.
 * @param string       $message Candidate covering note.
 * @param array        $cv      Uploaded CV: file path + original name.
 * @return bool
 */
function estecapelli_send_application( $job, $name, $email, $phone, $message, array $cv ) {
	$role    = $job ? get_the_title( $job ) : __( 'General application', 'estecapelli' );
	$to      = (string) apply_filters( 'estecapelli_careers_email_to', ESTECAPELLI_CAREERS_TO, $job );
	$subject = sprintf(
		/* translators: 1: role title, 2: candidate name */
		__( 'Application: %1$s — %2$s', 'estecapelli' ),
		$role,
		$name
	);

	$lines = array(
		__( 'Role', 'estecapelli' ) . ': ' . $role,
		__( 'Name', 'estecapelli' ) . ': ' . $name,
		__( 'Email', 'estecapelli' ) . ': ' . $email,
		__( 'Phone', 'estecapelli' ) . ': ' . ( $phone ?: '-' ),
	);
	if ( $job ) {
		$lines[] = __( 'Posting', 'estecapelli' ) . ': ' . get_permalink( $job );
	}
	$lines[] = '';
	$lines[] = __( 'Message', 'estecapelli' ) . ':';
	$lines[] = $message ?: '-';

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'From: %s <%s>', get_bloginfo( 'name' ), ESTECAPELLI_MAIL_FROM ),
		// So HR can reply to the candidate straight from the notification.
		sprintf( 'Reply-To: %s <%s>', $name, $email ),
	);

	$sent = wp_mail( $to, $subject, implode( "\r\n", $lines ), $headers, array( $cv['file'] ) );

	if ( ! $sent ) {
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			sprintf( '[estecapelli] Job application NOT sent (role: %s, candidate: %s).', $role, $email )
		);
	}

	return (bool) $sent;
}

/**
 * A filename HR can file without renaming: the candidate's name, their CV's own
 * extension.
 *
 * @param string $name Candidate name.
 * @param array  $cv   Uploaded CV.
 * @return string
 */
function estecapelli_cv_attachment_name( $name, array $cv ) {
	$ext  = pathinfo( $cv['name'], PATHINFO_EXTENSION );
	$stem = sanitize_title( $name );
	if ( '' === $stem ) {
		$stem = 'cv';
	}
	return $ext ? $stem . '-cv.' . strtolower( $ext ) : $stem . '-cv';
}
