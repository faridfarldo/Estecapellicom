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
		);
	}

	return $strings[ $text ] ?? $translation;
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
