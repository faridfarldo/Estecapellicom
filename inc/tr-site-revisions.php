<?php
/**
 * Render-time guarantees for the reviewed Turkish homepage and profile copy.
 *
 * Homepage options and several footer fields are shared ACF records. The
 * Turkish review must therefore be applied after those records are read, or a
 * stale English/older Turkish option can replace the approved copy. Everything
 * here is display-only and deliberately limited to Turkish requests.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Apply the approved Turkish homepage service heading after the ACF overlay. */
function estecapelli_tr_revision_home_services( array $data ) {
	if ( ! estecapelli_is_turkish_request() ) {
		return $data;
	}

	$data['headline'] = 'Bir tedavi seç. İmza tekniklerimizi gör.';
	$data['lead']     = 'Tedavilerimiz arasındaki en bilinen yöntemlerimizi keşfetmek için servisler arasında geçiş yap.';

	return $data;
}

/** Apply the reviewed Turkish signature-method text without changing media. */
function estecapelli_tr_revision_signature_methods( array $data ) {
	if ( ! estecapelli_is_turkish_request() ) {
		return $data;
	}

	$data['headline'] = 'Saç ekimi tedavilerinde belirleyici üç yöntem.';

	if ( empty( $data['cards'] ) || ! is_array( $data['cards'] ) ) {
		return $data;
	}

	foreach ( $data['cards'] as &$card ) {
		$key = sanitize_key( (string) ( $card['key'] ?? '' ) );
		if ( 'exosome' === $key ) {
			$card['stat_label'] = '72 saat boyunca saç folikülü canlılığı';
			$card['body']       = 'Saç kökleri vücuttan ayrıldığı anda oksijen ve besin desteğini kaybetmeye başlar; bu durum greftlerin zayıflamasına ve ekilen köklerin tamamının tutunma ihtimalinin azalmasına yol açabilir. Patentli Exosome Tedavimiz, göbek kordonu kaynaklı mezenkimal kök hücrelerden elde edilen biyolojik bileşenlerle greftleri bu kritik sürede destekler. Saç foliküllerini 72 saate kadar canlı tutarak alınan greftlerin %98’e varan oranda korunmasına yardımcı olur; daha hızlı iyileşme, daha güçlü büyüme ve daha doğal, kalıcı sonuçlar hedefler.';
		} elseif ( 'tricholab' === $key ) {
			$card['body'] = 'Her greftin size özel ve anatominize göre planlanmasını sağlar. Böylece sonucun daha doğal ve kişiye özel gerçekleşmesine olanak tanır.';
		}
	}
	unset( $card );

	return $data;
}

/** Apply the reviewed Turkish clinic heading and TrichoLab room name. */
function estecapelli_tr_revision_facilities( array $data ) {
	if ( ! estecapelli_is_turkish_request() ) {
		return $data;
	}

	$data['headline'] = 'İstanbul kliniğimizi uzaktan gezin.';
	if ( ! empty( $data['gallery'] ) && is_array( $data['gallery'] ) ) {
		foreach ( $data['gallery'] as &$item ) {
			$caption = strtolower( remove_accents( (string) ( $item['caption'] ?? '' ) ) );
			if ( false !== strpos( $caption, 'tricholab' ) ) {
				$item['caption'] = 'TrichoLab Muayene Odası';
			}
		}
		unset( $item );
	}

	return $data;
}

/** Apply the reviewed Turkish personal-plan CTA after shared ACF options load. */
function estecapelli_tr_revision_why_choose( array $data ) {
	if ( estecapelli_is_turkish_request() && isset( $data['intro'] ) && is_array( $data['intro'] ) ) {
		$data['intro']['cta']['label'] = 'Özel Plan Edin.';
	}

	return $data;
}

/** Turkish footer copy and temporary Before/After removal. */
function estecapelli_tr_revision_footer( array $contact, array $sitemap ) {
	if ( ! estecapelli_is_turkish_request() ) {
		return array( $contact, $sitemap );
	}

	$contact['heading'] = 'İletişim';
	$sitemap = array_values(
		array_filter(
			$sitemap,
			static function ( $item ) {
				return ! estecapelli_tr_revision_is_before_after_url( (string) ( $item['url'] ?? '' ) );
			}
		)
	);

	return array( $contact, $sitemap );
}

/** Whether a URL points to any Turkish (or stale English) Before/After route. */
function estecapelli_tr_revision_is_before_after_url( $url ) {
	$path = strtolower( rawurldecode( (string) wp_parse_url( $url, PHP_URL_PATH ) ) );
	return (bool) preg_match( '#/(?:oncesi-sonrasi|before-after)(?:/|$)#', $path );
}

add_filter( 'wp_nav_menu_objects', 'estecapelli_tr_revision_remove_before_after_menu', 30 );
/** Hide every Before/After menu entry on Turkish requests only. */
function estecapelli_tr_revision_remove_before_after_menu( $items ) {
	if ( ! estecapelli_is_turkish_request() || ! is_array( $items ) ) {
		return $items;
	}

	return array_values(
		array_filter(
			$items,
			static function ( $item ) {
				return ! estecapelli_tr_revision_is_before_after_url( (string) ( $item->url ?? '' ) );
			}
		)
	);
}

add_action( 'template_redirect', 'estecapelli_tr_revision_disable_before_after', 1 );
/** Temporarily disable the Turkish gallery and every Turkish result child URL. */
function estecapelli_tr_revision_disable_before_after() {
	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path    = strtolower( rawurldecode( (string) wp_parse_url( $request, PHP_URL_PATH ) ) );
	if ( ! estecapelli_is_turkish_request() && ! preg_match( '#^/tr(?:/|$)#', $path ) ) {
		return;
	}
	if ( ! preg_match( '#^/(?:tr/)?oncesi-sonrasi(?:/|$)#', $path ) ) {
		return;
	}

	$target = function_exists( 'estecapelli_indexed_url' )
		? estecapelli_indexed_url( '/en', 'tr' )
		: home_url( '/tr/' );
	nocache_headers();
	wp_safe_redirect( $target, 302, 'Estecapelli' );
	exit;
}

/** Load the two reviewed Turkish doctor profiles from version-controlled JSON. */
function estecapelli_tr_revision_doctor_profile( $post_id, array $profile ) {
	if ( ! estecapelli_is_turkish_request() ) {
		return $profile;
	}

	$slug = function_exists( 'estecapelli_wpml_source_post_slug' )
		? estecapelli_wpml_source_post_slug( get_post( $post_id ) )
		: get_post_field( 'post_name', $post_id );
	$files = array(
		'prof-dr-binnur-ustun' => 'prof-dr-binnur-ustun.json',
		'op-dr-hasan-celik'     => 'op-dr-hasan-celik.json',
	);
	if ( empty( $files[ $slug ] ) ) {
		return $profile;
	}

	$file = get_template_directory() . '/inc/data/translations/tr/doctors/' . $files[ $slug ];
	if ( ! is_readable( $file ) ) {
		return $profile;
	}

	$data = json_decode( (string) file_get_contents( $file ), true );
	if ( ! is_array( $data ) ) {
		return $profile;
	}

	foreach ( array( 'name', 'position', 'bio', 'credentials' ) as $field ) {
		if ( array_key_exists( $field, $data ) ) {
			$profile[ $field ] = $data[ $field ];
		}
	}

	return $profile;
}

/** Replace the temporary imported French category label on Turkish pages. */
function estecapelli_tr_revision_category_label( $name ) {
	$key = strtolower( trim( remove_accents( (string) $name ) ) );
	$key = preg_replace( '/[\s_-]+/', ' ', $key );
	return in_array( $key, array( 'non classe', 'uncategorized' ), true ) ? 'Başlıksız' : $name;
}

add_filter( 'get_term', 'estecapelli_tr_revision_filter_term', 30, 2 );
/** Localize a category term object without mutating WordPress' cached object. */
function estecapelli_tr_revision_filter_term( $term, $taxonomy ) {
	if ( ! estecapelli_is_turkish_request() || 'category' !== $taxonomy || ! $term instanceof WP_Term ) {
		return $term;
	}

	$label = estecapelli_tr_revision_category_label( $term->name );
	if ( $label === $term->name ) {
		return $term;
	}

	$term       = clone $term;
	$term->name = $label;
	return $term;
}

add_filter( 'get_the_terms', 'estecapelli_tr_revision_filter_post_terms', 30, 3 );
/** Cover category chips returned by get_the_category()/get_the_terms(). */
function estecapelli_tr_revision_filter_post_terms( $terms, $post_id, $taxonomy ) {
	if ( ! estecapelli_is_turkish_request() || 'category' !== $taxonomy || ! is_array( $terms ) ) {
		return $terms;
	}

	foreach ( $terms as $index => $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		$label = estecapelli_tr_revision_category_label( $term->name );
		if ( $label !== $term->name ) {
			$terms[ $index ]       = clone $term;
			$terms[ $index ]->name = $label;
		}
	}

	return $terms;
}

add_filter( 'get_terms', 'estecapelli_tr_revision_filter_term_list', 30, 4 );
/** Cover the category-filter list returned by get_categories(). */
function estecapelli_tr_revision_filter_term_list( $terms, $taxonomies, $args, $term_query ) {
	if ( ! estecapelli_is_turkish_request() || ! in_array( 'category', (array) $taxonomies, true ) || ! is_array( $terms ) ) {
		return $terms;
	}

	foreach ( $terms as $index => $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		$label = estecapelli_tr_revision_category_label( $term->name );
		if ( $label !== $term->name ) {
			$terms[ $index ]       = clone $term;
			$terms[ $index ]->name = $label;
		}
	}

	return $terms;
}

add_filter( 'gettext', 'estecapelli_tr_revision_gettext', 100, 3 );
/** Force the reviewed UI phrases even when WPML still stores an older value. */
function estecapelli_tr_revision_gettext( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || ! estecapelli_is_turkish_request() ) {
		return $translation;
	}

	$strings = array(
		'Hollywood smiles, veneers and full-mouth restorations — crafted by our specialists for a natural, confident result that lasts.' => 'Hollywood Gülüşü, Lamine veneer ve tüm ağız restorasyonları.',
		'Hollywood Smile & porcelain veneers' => 'Hollywood Gülüşü ve porselen Lamine veneerler',
		'Start self-assessment' => 'Kişiselleştirilmiş planınızı hemen edinin.',
		'Use the arrows to browse · click any photo to enlarge' => 'Göz atmak için okları kullanın – büyütmek için herhangi bir fotoğrafa tıklayın',
		'View Résumé' => 'Görüntülemek için Tıklayın',
		'Book a Free Consultation' => 'Ücretsiz Randevu Oluşturun',
		'Visit Us' => 'İletişim',
		'Language' => 'Dil',
		'Request Call Back' => 'Sizi Arayalım',
		'Call Me Back' => 'Sizi Arayalım',
		'Book your free consultation' => 'Ücretsiz Analiz Edinin.',
		'Leave your details and a medical consultant will get back to you shortly — no obligation.' => 'Bilgilerinizi bırakın ve medikal danışmanlarımız sizinle kısa süre içerisinde iletişime geçsin.',
		'Full name' => 'İsim Soyisim',
		'Name and surname' => 'İsim Soyisim',
		'Get a Personal Plan' => 'Özel Plan Edin.',
		'Get Your Personal Plan' => 'Özel Plan Edin.',
		'Zone 1' => 'Bölge 1',
		'Zone 2' => 'Bölge 2',
		'Zone 3' => 'Bölge 3',
		'Zone 4' => 'Bölge 4',
		'Zone 5' => 'Bölge 5',
		'Zone 6' => 'Bölge 6',
	);

	return $strings[ $text ] ?? $translation;
}
