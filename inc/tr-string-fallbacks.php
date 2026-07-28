<?php
/**
 * Turkish fallbacks for front-end Contact and lead-form strings.
 *
 * WPML String Translation remains authoritative. These values are used only
 * when the current Turkish translation is still identical to its English
 * source, keeping the coded Contact template and shared popup independent of
 * the Advanced Translation Editor.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Whether the current front-end request uses the indexed Turkish language. */
function estecapelli_is_turkish_request() {
	$language = (string) apply_filters( 'wpml_current_language', null );
	if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
		$language = estecapelli_indexed_language_code( $language );
	}
	if ( $language ) {
		return 'tr' === $language;
	}

	return 0 === strpos( strtolower( (string) determine_locale() ), 'tr_' );
}

add_filter( 'gettext', 'estecapelli_tr_gettext_fallback', 25, 3 );
/** Supply Turkish only when no earlier translation provider changed the text. */
function estecapelli_tr_gettext_fallback( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || $translation !== $text || ! estecapelli_is_turkish_request() ) {
		return $translation;
	}

	static $strings = null;
	if ( null === $strings ) {
		$strings = array(
			// Journal / blog UI.
			'From the Journal' => 'Dergiden',
			'Research, results & recovery' => 'Araştırma, sonuçlar ve iyileşme',
			'Expert articles on hair restoration, plastic surgery, dental treatment and the journey to your transformation — written by the Estecapelli team.' => 'Saç restorasyonu, estetik cerrahi, diş tedavileri ve dönüşüm yolculuğunuz hakkında Estecapelli ekibi tarafından hazırlanan uzman yazıları.',
			'Article categories' => 'Makale kategorileri',
			'All' => 'Tümü',
			'Latest article' => 'En yeni makale',
			'Read the article' => 'Makaleyi okuyun',
			'Articles from the Estecapelli journal.' => 'Estecapelli dergisinden makaleler.',
			'All articles' => 'Tüm makaleler',
			'Read more' => 'Devamını okuyun',
			'Posts pagination' => 'Makale sayfaları',
			'Newer' => 'Daha yeni',
			'Older' => 'Daha eski',
			'No articles found here yet.' => 'Burada henüz makale bulunamadı.',
			'No articles have been published yet. New posts will appear here as soon as our team publishes them.' => 'Henüz hiçbir makale yayımlanmadı. Ekibimiz yeni içerikler yayımladığında burada görünecek.',

			// Shared consultation popup and lead forms.
			'Free Consultation' => 'Ücretsiz Konsültasyon',
			'Close' => 'Kapat',
			'Book your free consultation' => 'Ücretsiz konsültasyonunuzu planlayın',
			'Leave your details and a medical consultant will get back to you shortly — no obligation.' => 'Bilgilerinizi bırakın; bir medikal danışmanımız kısa süre içinde sizinle iletişime geçsin — hiçbir yükümlülük yok.',
			'Full name' => 'Ad Soyad',
			'Name and surname' => 'Adınız ve soyadınız',
			'Phone' => 'Telefon',
			'Phone number' => 'Telefon numarası',
			'Email' => 'E-posta',
			'Note' => 'Not',
			'Tell us about your goals, or any questions you have…' => 'Hedeflerinizi veya sorularınızı bize iletin…',
			'Request a Free Consultation' => 'Ücretsiz Konsültasyon Talep Edin',
			'Get a free consultation — leave your details and we will reach out.' => 'Ücretsiz konsültasyon için bilgilerinizi bırakın; sizinle iletişime geçelim.',
			'Request Call Back' => 'Geri Arama Talep Edin',
			'Thank you! Your request has been received — our team will contact you shortly.' => 'Teşekkürler! Talebiniz alındı — ekibimiz kısa süre içinde sizinle iletişime geçecek.',
			'Sending…' => 'Gönderiliyor…',
			'Something went wrong. Please try again or reach us on WhatsApp.' => 'Bir sorun oluştu. Lütfen tekrar deneyin veya WhatsApp üzerinden bize ulaşın.',
			'Please enter a valid phone number.' => 'Lütfen geçerli bir telefon numarası girin.',
			'Please enter a valid email address.' => 'Lütfen geçerli bir e-posta adresi girin.',
			'Please enter your name.' => 'Lütfen adınızı girin.',
			'Name is required.' => 'Ad alanı zorunludur.',
			'Please enter your phone number.' => 'Lütfen telefon numaranızı girin.',
			'Please select a valid country code.' => 'Lütfen geçerli bir ülke kodu seçin.',
			'This number is too short for the selected country.' => 'Bu numara seçilen ülke için çok kısa.',
			'This number is too long for the selected country.' => 'Bu numara seçilen ülke için çok uzun.',
			'Please enter the full phone number, including the area code.' => 'Lütfen alan kodu dâhil telefon numarasının tamamını girin.',
			'Please enter a valid phone number for the selected country.' => 'Lütfen seçilen ülke için geçerli bir telefon numarası girin.',
			'Hello Estecapelli, I would like to book a free consultation.' => 'Merhaba Estecapelli, ücretsiz bir konsültasyon randevusu almak istiyorum.',

			// Contact page.
			'Hello Estecapelli, I would like a free analysis. Here are my photos:' => 'Merhaba Estecapelli, ücretsiz analiz yaptırmak istiyorum. Fotoğraflarım:',
			'English' => 'İngilizce',
			'French' => 'Fransızca',
			'Italian' => 'İtalyanca',
			'Spanish' => 'İspanyolca',
			'Polish' => 'Lehçe',
			'Portuguese' => 'Portekizce',
			'Contact Us' => 'İletişim',
			'Let’s start your journey' => 'Yolculuğunuza birlikte başlayalım',
			'Reach our team by WhatsApp, phone or email — or leave your details and a medical consultant will get back to you in your own language.' => 'Ekibimize WhatsApp, telefon veya e-posta yoluyla ulaşın ya da bilgilerinizi bırakın; bir medikal danışmanımız sizinle kendi dilinizde iletişime geçsin.',
			'We usually reply within an hour' => 'Genellikle bir saat içinde yanıt veriyoruz',
			'Chat with us now' => 'Hemen bize yazın',
			'Call' => 'Arayın',
			'Request a free consultation' => 'Ücretsiz konsültasyon talep edin',
			'Tell us a little about you and we’ll be in touch shortly.' => 'Bize kendinizden ve hedeflerinizden kısaca bahsedin; sizinle en kısa sürede iletişime geçelim.',
			'Interested in' => 'İlgilendiğiniz tedavi',
			'Select a treatment' => 'Bir tedavi seçin',
			'Hair Transplant' => 'Saç Ekimi',
			'Plastic Surgery' => 'Estetik Cerrahi',
			'Dental Treatment' => 'Diş Tedavisi',
			'Not sure yet' => 'Henüz emin değilim',
			'Message' => 'Mesaj',
			'Send Request' => 'Talebi Gönder',
			'Visit & reach us' => 'Bizi ziyaret edin ve bize ulaşın',
			'Address' => 'Adres',
			'Working hours' => 'Çalışma saatleri',
			'Monday – Sunday: 09:00 – 18:00 (GMT+3)' => 'Pazartesi – Pazar: 09:00 – 18:00 (GMT+3)',
			'What happens next?' => 'Sonraki adımlar neler?',
			'We review your request and reach out to understand your goals.' => 'Talebinizi inceler ve hedeflerinizi anlamak için sizinle iletişime geçeriz.',
			'You receive a free, personalised treatment plan and quote.' => 'Size ücretsiz, kişiye özel bir tedavi planı ve fiyat teklifi sunarız.',
			'We arrange your dates, travel and stay — and welcome you to Istanbul.' => 'Tarihlerinizi, seyahatinizi ve konaklamanızı planlar; sizi İstanbul’da karşılarız.',
			'Send your photos, get a free analysis' => 'Fotoğraflarınızı gönderin, ücretsiz analiz alın',
			'Share a few photos of your hair, face or smile and our specialists will assess your case and recommend the right approach — at no cost and with no obligation.' => 'Saçınızın, yüzünüzün veya gülüşünüzün birkaç fotoğrafını paylaşın; uzmanlarımız durumunuzu değerlendirip size en uygun yaklaşımı ücretsiz ve hiçbir yükümlülük olmadan önersin.',
			'Send Photos on WhatsApp' => 'Fotoğrafları WhatsApp’tan Gönderin',
			'Talk to us in your own language' => 'Bizimle kendi dilinizde konuşun',
			'Estecapelli clinic location' => 'Estecapelli klinik konumu',
			// Floating WhatsApp button + fake WhatsApp chat popup.
			'Chat on WhatsApp' => 'WhatsApp’tan yazın',
			'Free Hair Analysis' => 'Ücretsiz saç analizi',
			'Reply in 2 minutes' => '2 dakikada yanıt',
			'WhatsApp chat' => 'WhatsApp sohbeti',
			'Close chat' => 'Sohbeti kapat',
			'online' => 'çevrimiçi',
			'Today' => 'Bugün',
			'Hi! 👋 Welcome to Estecapelli. Tell us which treatment you are interested in and we will get back to you within minutes.' => 'Merhaba! 👋 Estecapelli’ye hoş geldiniz. Hangi tedaviyle ilgilendiğinizi yazın, birkaç dakika içinde size dönelim.',
			'Type a message' => 'Bir mesaj yazın',
			'Write your message first' => 'Önce mesajınızı yazın',
			'Send' => 'Gönder',
			'Confirm your message' => 'Mesajınızı onaylayın',
			'This message will be sent to our WhatsApp:' => 'Bu mesaj WhatsApp hattımıza gönderilecek:',
			'Cancel' => 'İptal',
			// 404 page.
			'This page could not be found' => 'Bu sayfa bulunamadı',
			'The page you are looking for may have been moved or no longer exists. You can head back to the homepage, or pick one of the sections below.' => 'Aradığınız sayfa taşınmış veya artık mevcut olmayabilir. Ana sayfaya dönebilir ya da aşağıdaki bölümlerden birini seçebilirsiniz.',
			'Back to homepage' => 'Ana sayfaya dön',
			'Contact us' => 'Bize ulaşın',
			'Popular sections' => 'Öne çıkan bölümler',
			'Site sections' => 'Site bölümleri',
		);
	}

	return $strings[ $text ] ?? $translation;
}

add_filter( 'ngettext', 'estecapelli_tr_ngettext_fallback', 25, 5 );
/** Translate the reading-time label used by blog cards and articles. */
function estecapelli_tr_ngettext_fallback( $translation, $single, $plural, $number, $domain ) {
	if (
		'estecapelli' === $domain &&
		estecapelli_is_turkish_request() &&
		in_array( $translation, array( $single, $plural ), true ) &&
		'%d min read' === $single &&
		'%d min read' === $plural
	) {
		return '%d dk okuma';
	}

	return $translation;
}

add_action( 'wp_enqueue_scripts', 'estecapelli_tr_enqueue_contact_i18n', 20 );
/** Add Turkish validation messages to the two front-end form controllers. */
function estecapelli_tr_enqueue_contact_i18n() {
	if ( ! estecapelli_is_turkish_request() ) {
		return;
	}

	$server_errors = array(
		'Please enter your name.' => __( 'Please enter your name.', 'estecapelli' ),
		'Name is required.' => __( 'Name is required.', 'estecapelli' ),
		'Please enter a valid phone number.' => __( 'Please enter a valid phone number.', 'estecapelli' ),
		'Please enter a valid email address.' => __( 'Please enter a valid email address.', 'estecapelli' ),
	);
	wp_add_inline_script(
		'estecapelli-main',
		'window.EstecapelliLeadServerErrors=' . wp_json_encode( $server_errors ) . ';',
		'before'
	);

	$phone_i18n = array(
		'required' => __( 'Please enter your phone number.', 'estecapelli' ),
		'invalid' => __( 'Please enter a valid phone number.', 'estecapelli' ),
		'countryCode' => __( 'Please select a valid country code.', 'estecapelli' ),
		'tooShort' => __( 'This number is too short for the selected country.', 'estecapelli' ),
		'tooLong' => __( 'This number is too long for the selected country.', 'estecapelli' ),
		'areaCode' => __( 'Please enter the full phone number, including the area code.', 'estecapelli' ),
		'invalidCountry' => __( 'Please enter a valid phone number for the selected country.', 'estecapelli' ),
	);
	wp_add_inline_script(
		'estecapelli-phone',
		'window.EstecapelliPhone=' . wp_json_encode( array( 'i18n' => $phone_i18n ) ) . ';',
		'before'
	);
}
