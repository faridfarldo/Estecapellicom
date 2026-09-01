<?php
/**
 * Real Home pages — one per public language.
 *
 * The homepage is drawn entirely by front-page.php, so historically no Page
 * record existed for it in any language. That left two gaps:
 *
 *   • Rank Math had nothing to attach to. The language roots (/en/, /fr/, …)
 *     shipped with no per-language SEO title and no meta description, and the
 *     dashboard offered no place to write them.
 *   • The homepage ACF fields live on a shared Options page, so a value saved
 *     for one language leaked into all seven.
 *
 * This file gives every language root a real published Page:
 *
 *   • Rank Math's per-post SEO box works on it, in each language separately.
 *   • Its ACF "Homepage Content" fields are read BEFORE the shared options
 *     page, so a language can override any homepage section on its own.
 *   • front-page.php still renders the design. The page's post_content is never
 *     output — an editor notice says so.
 *
 * Public URLs do not change. The page's own leaf (/en/home, /fr/maison, …) is
 * already 301'd to its language root by estecapelli_redirect_legacy_language_home(),
 * so no duplicate URL becomes indexable.
 *
 * Nothing here writes to the database. Creating the pages is a deliberate,
 * one-click operation in Tools → Homepage Pages.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Page template that marks a post as a language homepage. */
const ESTECAPELLI_HOME_PAGE_TEMPLATE = 'home-page.php';

/** Option holding the resolved Home page ID for each public language. */
const ESTECAPELLI_HOME_PAGE_IDS_OPTION = 'estecapelli_home_page_ids';

/**
 * Slug for each language's Home page.
 *
 * These are the retired legacy homepage leaves, which already 301 to their
 * language root. Reusing them means the new pages introduce no new URL.
 * Romanian never had a legacy leaf, so it gets its own slug here.
 *
 * @return array<string,string>
 */
function estecapelli_home_page_slugs() {
	$slugs = array();
	foreach ( estecapelli_legacy_home_slugs() as $language => $slug ) {
		$slugs[ $language ] = $slug;
	}
	$slugs['ro'] = 'acasa';

	return $slugs;
}

/**
 * Editorial seed for each Home page: post title plus the Rank Math meta that
 * was missing on every language root.
 *
 * The seed is only ever applied when the field is still empty, so anything
 * written in the dashboard survives a re-run of the setup tool.
 *
 * @return array<string,array<string,string>>
 */
function estecapelli_home_page_seed() {
	return array(
		'en' => array(
			'title'       => 'Home',
			'seo_title'   => 'Hair Transplant & Aesthetic Clinic in Istanbul | Estecapelli',
			'description' => 'Estecapelli is a Ministry of Health licensed clinic in Istanbul offering Exosome FUE and VITA hair transplants, plastic surgery and dental care. Book a free consultation.',
			'keyword'     => 'hair transplant istanbul',
		),
		'tr' => array(
			'title'       => 'Ana Sayfa',
			'seo_title'   => 'İstanbul Saç Ekimi ve Estetik Kliniği | Estecapelli',
			'description' => 'Estecapelli, İstanbul’da Sağlık Bakanlığı ruhsatlı bir kliniktir. Exosome FUE ve VITA saç ekimi, plastik cerrahi ve diş tedavileri. Ücretsiz ön görüşme alın.',
			'keyword'     => 'saç ekimi istanbul',
		),
		'fr' => array(
			'title'       => 'Accueil',
			'seo_title'   => 'Greffe de cheveux à Istanbul, Turquie | Clinique Estecapelli',
			'description' => 'Estecapelli, clinique agréée par le ministère de la Santé à Istanbul : greffe de cheveux Exosome FUE et VITA, chirurgie plastique et soins dentaires. Consultation gratuite.',
			'keyword'     => 'greffe de cheveux istanbul',
		),
		'it' => array(
			'title'       => 'Home',
			'seo_title'   => 'Trapianto di capelli a Istanbul, Turchia | Clinica Estecapelli',
			'description' => 'Estecapelli è una clinica autorizzata dal Ministero della Salute a Istanbul: trapianto di capelli Exosome FUE e VITA, chirurgia plastica e odontoiatria. Consulenza gratuita.',
			'keyword'     => 'trapianto capelli istanbul',
		),
		'es' => array(
			'title'       => 'Inicio',
			'seo_title'   => 'Injerto capilar en Estambul, Turquía | Clínica Estecapelli',
			'description' => 'Estecapelli es una clínica autorizada por el Ministerio de Sanidad en Estambul: injerto capilar Exosome FUE y VITA, cirugía plástica y tratamientos dentales. Consulta gratuita.',
			'keyword'     => 'injerto capilar estambul',
		),
		'pl' => array(
			'title'       => 'Strona główna',
			'seo_title'   => 'Przeszczep włosów w Stambule, Turcja | Klinika Estecapelli',
			'description' => 'Estecapelli to klinika z licencją Ministerstwa Zdrowia w Stambule: przeszczep włosów Exosome FUE i VITA, chirurgia plastyczna i stomatologia. Bezpłatna konsultacja.',
			'keyword'     => 'przeszczep włosów stambuł',
		),
		'pt' => array(
			'title'       => 'Início',
			'seo_title'   => 'Transplante capilar em Istambul, Turquia | Clínica Estecapelli',
			'description' => 'A Estecapelli é uma clínica licenciada pelo Ministério da Saúde em Istambul: transplante capilar Exosome FUE e VITA, cirurgia plástica e medicina dentária. Consulta gratuita.',
			'keyword'     => 'transplante capilar istambul',
		),
		'ro' => array(
			'title'       => 'Acasă',
			'seo_title'   => 'Transplant de păr în Istanbul, Turcia | Clinica Estecapelli',
			'description' => 'Estecapelli este o clinică autorizată de Ministerul Sănătății din Istanbul: transplant de păr Exosome FUE și VITA, chirurgie plastică și stomatologie. Consultație gratuită.',
			'keyword'     => 'transplant de par istanbul',
		),
	);
}

/**
 * Resolve the Home page ID for one public language.
 *
 * The recorded option is authoritative because it is written by the setup tool
 * once the WPML translation group is linked. The slug lookup behind it keeps
 * the pages resolvable if the option is ever cleared.
 *
 * @param string $language Public language code. Defaults to the request language.
 * @return int Page ID, or 0 when the language has no Home page yet.
 */
function estecapelli_home_page_id( $language = '' ) {
	static $cache = array();

	$language = $language ? estecapelli_indexed_language_code( $language ) : estecapelli_current_language_code();
	if ( ! in_array( $language, estecapelli_indexed_languages(), true ) ) {
		return 0;
	}

	if ( isset( $cache[ $language ] ) ) {
		return $cache[ $language ];
	}

	$recorded = get_option( ESTECAPELLI_HOME_PAGE_IDS_OPTION, array() );
	$id       = is_array( $recorded ) ? (int) ( $recorded[ $language ] ?? 0 ) : 0;

	if ( $id && 'publish' !== get_post_status( $id ) ) {
		$id = 0; // Recorded page was trashed or deleted — fall through to the slug.
	}

	if ( ! $id ) {
		$id = estecapelli_home_page_id_by_slug( $language );
	}

	$cache[ $language ] = $id;

	return $id;
}

/**
 * Find a language's Home page by its slug and template.
 *
 * The template check is what separates a Home page from any other page that
 * happens to share the slug, which matters because English and Italian both
 * use "home".
 */
function estecapelli_home_page_id_by_slug( $language ) {
	$slugs = estecapelli_home_page_slugs();
	$slug  = (string) ( $slugs[ $language ] ?? '' );
	if ( '' === $slug ) {
		return 0;
	}

	global $wpdb;
	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'page' AND post_status = 'publish' ORDER BY ID ASC",
			$slug
		)
	);

	foreach ( $ids as $candidate ) {
		$candidate = (int) $candidate;
		if ( ESTECAPELLI_HOME_PAGE_TEMPLATE !== get_page_template_slug( $candidate ) ) {
			continue;
		}
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return $candidate;
		}
		if ( estecapelli_home_page_language( $candidate ) === $language ) {
			return $candidate;
		}
	}

	return 0;
}

/** The public language code WPML has recorded for a page. */
function estecapelli_home_page_language( $post_id ) {
	$details = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => (int) $post_id,
			'element_type' => 'post_page',
		)
	);

	$code = is_object( $details ) ? (string) $details->language_code : '';

	return $code ? estecapelli_indexed_language_code( $code ) : '';
}

/**
 * The language this request belongs to.
 *
 * The public URL wins, exactly as it does for the hero payloads: WPML can still
 * be reporting the default language while an indexed route is being resolved.
 */
function estecapelli_current_language_code() {
	$language = estecapelli_request_language_code();
	if ( $language ) {
		return $language;
	}

	return estecapelli_indexed_language_code() ?: 'en';
}

/*
 * ---------------------------------------------------------------------------
 * Front-page identity
 * ---------------------------------------------------------------------------
 */

/**
 * Point page_on_front at the Home page of the language being served.
 *
 * WordPress compares get_option('page_on_front') with the queried object to
 * decide is_front_page(). Without this filter only the English root would
 * answer true, and everything keyed on it — the hero split in header.php, the
 * Hair Analysis widget enqueue, the GA4 page_type — would silently switch off
 * on /fr/, /it/ and the rest.
 *
 * Admin screens keep the stored value so Settings → Reading stays truthful.
 */
function estecapelli_filter_page_on_front( $value ) {
	if ( is_admin() || wp_doing_ajax() ) {
		return $value;
	}

	// Resolving the Home page asks WPML for the current language, and WPML is
	// free to read options while answering. Hand back the stored value rather
	// than re-entering the lookup.
	static $resolving = false;
	if ( $resolving ) {
		return $value;
	}

	$resolving = true;
	$id        = estecapelli_home_page_id();
	$resolving = false;

	return $id ?: $value;
}
add_filter( 'option_page_on_front', 'estecapelli_filter_page_on_front' );

/**
 * Answer "a static page" for show_on_front once the Home pages exist.
 *
 * is_front_page() only consults page_on_front when show_on_front says "page".
 * The setup tool writes that setting, but the front end must not depend on it
 * staying written: a language root now queries a page, so the "posts" branch
 * (which needs is_home()) would answer false and quietly demote the homepage to
 * an ordinary page. Filtering keeps the two in step no matter what Settings →
 * Reading holds.
 */
function estecapelli_filter_show_on_front( $value ) {
	if ( is_admin() || wp_doing_ajax() ) {
		return $value;
	}

	static $resolving = false;
	if ( $resolving ) {
		return $value;
	}

	$resolving = true;
	$id        = estecapelli_home_page_id();
	$resolving = false;

	return $id ? 'page' : $value;
}
add_filter( 'option_show_on_front', 'estecapelli_filter_show_on_front' );

/**
 * Keep the front page canonical on the language root.
 *
 * Rank Math builds the canonical from the page permalink, which for the Home
 * page is its own leaf (/en/home). That leaf is a 301 source, so it must never
 * be advertised as canonical.
 */
function estecapelli_home_canonical_url( $canonical ) {
	if ( ! is_front_page() ) {
		return $canonical;
	}

	return estecapelli_unfiltered_home_url() . '/' . estecapelli_current_language_code() . '/';
}
add_filter( 'rank_math/frontend/canonical', 'estecapelli_home_canonical_url', 5 );
add_filter( 'rank_math/opengraph/url', 'estecapelli_home_canonical_url', 5 );

/**
 * Keep the front page's hreflang set on the language roots.
 *
 * WPML builds hreflang from each translation's permalink. Now that a real page
 * sits behind every root, that would advertise /fr/maison/, /es/inicio/ and the
 * rest — every one of them a 301 source. Rewrite the set to the roots the
 * indexed URL contract actually serves.
 */
function estecapelli_home_hreflangs( $hreflangs ) {
	if ( ! is_array( $hreflangs ) || ! is_front_page() ) {
		return $hreflangs;
	}

	$home = estecapelli_unfiltered_home_url();
	foreach ( $hreflangs as $code => $url ) {
		if ( 'x-default' === strtolower( (string) $code ) ) {
			$hreflangs[ $code ] = $home . '/en/';
			continue;
		}

		$public = estecapelli_indexed_language_code( $code );
		if ( in_array( $public, estecapelli_indexed_languages(), true ) ) {
			$hreflangs[ $code ] = $home . '/' . $public . '/';
		}
	}

	return $hreflangs;
}
add_filter( 'wpml_hreflangs', 'estecapelli_home_hreflangs', 20 );

/**
 * List each Home page in the sitemap as its language root.
 *
 * Rank Math takes the entry URL from the permalink, which for these pages is
 * the 301 leaf (/en/home, /fr/maison …). Submitting a redirect as an indexable
 * URL is exactly what the indexed route contract exists to prevent, so swap in
 * the root the page actually serves.
 *
 * @param array  $entry  Sitemap entry, 'loc' among its keys.
 * @param string $type   Object type ('post', 'term', 'user').
 * @param object $object The post being listed.
 * @return array
 */
function estecapelli_home_sitemap_entry( $entry, $type, $object ) {
	if ( 'post' !== $type || ! isset( $entry['loc'] ) || ! isset( $object->ID ) ) {
		return $entry;
	}
	if ( ! estecapelli_is_home_page_post( $object->ID ) ) {
		return $entry;
	}

	$language = estecapelli_home_page_language( $object->ID );
	if ( ! $language ) {
		// WPML off, or the page is not in a translation group yet: the only Home
		// page that can be is English.
		$language = 'en';
	}

	$entry['loc'] = estecapelli_unfiltered_home_url() . '/' . $language . '/';

	return $entry;
}
add_filter( 'rank_math/sitemap/entry', 'estecapelli_home_sitemap_entry', 10, 3 );

/**
 * Serve the homepage title and meta description from the Home page itself.
 *
 * Rank Math has a dedicated front-page paper that can answer from the single
 * global Titles &amp; Meta → Homepage setting. That setting has no language
 * dimension, so every root would share one title — the very problem the Home
 * pages exist to fix. Whenever the queried Home page carries its own value,
 * that value wins.
 *
 * @param string $value Whatever Rank Math resolved.
 * @param string $meta  Post meta key holding the per-language override.
 * @return string
 */
function estecapelli_home_seo_meta( $value, $meta ) {
	if ( ! is_front_page() ) {
		return $value;
	}

	$id = estecapelli_home_page_id();
	if ( ! $id ) {
		return $value;
	}

	$own = (string) get_post_meta( $id, $meta, true );
	if ( '' === $own ) {
		return $value;
	}

	// This filter fires after Rank Math has expanded its %variables%, so a
	// hand-written "%sitename%" would otherwise be printed literally.
	if ( false !== strpos( $own, '%' ) && class_exists( '\RankMath\Helper' ) ) {
		$own = (string) \RankMath\Helper::replace_vars( $own, get_post( $id ) );
	}

	return $own;
}

add_filter(
	'rank_math/frontend/title',
	function ( $title ) {
		return estecapelli_home_seo_meta( $title, 'rank_math_title' );
	},
	20
);

add_filter(
	'rank_math/frontend/description',
	function ( $description ) {
		return estecapelli_home_seo_meta( $description, 'rank_math_description' );
	},
	20
);

/*
 * ---------------------------------------------------------------------------
 * Per-language ACF overlay
 * ---------------------------------------------------------------------------
 */

/**
 * Read a homepage ACF field for the language being served.
 *
 * Resolution order, first non-empty wins:
 *
 *   1. the current language's Home page — per-language dashboard content
 *   2. the shared "Homepage Content" options page — the existing global values
 *
 * Returning null from both leaves the coded defaults in place, which is what
 * estecapelli_acf_overlay() expects.
 *
 * @param string $field ACF field name.
 * @return mixed
 */
function estecapelli_home_field( $field ) {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	$id = estecapelli_home_page_id();
	if ( $id ) {
		$value = get_field( $field, $id );
		if ( ! estecapelli_home_field_is_empty( $value ) ) {
			return $value;
		}
	}

	return get_field( $field, 'option' );
}

/** Whether an ACF value carries nothing worth overlaying. */
function estecapelli_home_field_is_empty( $value ) {
	if ( null === $value || '' === $value || false === $value ) {
		return true;
	}
	if ( ! is_array( $value ) ) {
		return false;
	}
	foreach ( $value as $item ) {
		if ( ! estecapelli_home_field_is_empty( $item ) ) {
			return false;
		}
	}

	return true;
}

/*
 * ---------------------------------------------------------------------------
 * Editor experience
 * ---------------------------------------------------------------------------
 */

/** Whether a post is one of the language Home pages. */
function estecapelli_is_home_page_post( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id || 'page' !== get_post_type( $post_id ) ) {
		return false;
	}

	return ESTECAPELLI_HOME_PAGE_TEMPLATE === get_page_template_slug( $post_id );
}

/** Explain in the editor that the body content is not what visitors see. */
function estecapelli_home_page_editor_notice() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'page' !== $screen->id ) {
		return;
	}

	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
	if ( ! estecapelli_is_home_page_post( $post_id ) ) {
		return;
	}

	$message = __( 'The layout is rendered by the theme, so anything typed in the content editor below is ignored. Use the SEO panel for this language&#8217;s title and meta description, and the Homepage Content fields to override any section.', 'estecapelli' );

	echo '<div class="notice notice-info"><p><strong>' . esc_html__( 'This is a language homepage.', 'estecapelli' ) . '</strong> ' . wp_kses_post( $message ) . '</p></div>';
}
add_action( 'admin_notices', 'estecapelli_home_page_editor_notice' );
