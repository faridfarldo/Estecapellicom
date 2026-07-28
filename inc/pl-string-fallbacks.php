<?php
/**
 * Polish fallbacks for template-level strings on the requested pages.
 *
 * WPML String Translation remains authoritative. A fallback is applied only
 * when the current value is still identical to its English source string.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Whether the current front-end request is Polish. */
function estecapelli_is_polish_request() {
	$language = apply_filters( 'wpml_current_language', null );
	if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
		return 'pl' === estecapelli_indexed_language_code( (string) $language );
	}
	if ( $language ) {
		return 'pl' === $language;
	}

	return 0 === strpos( determine_locale(), 'pl_' );
}

add_filter( 'gettext', 'estecapelli_pl_gettext_fallback', 25, 3 );
/** Supply Polish only when no earlier translation provider changed the text. */
function estecapelli_pl_gettext_fallback( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || $translation !== $text || ! estecapelli_is_polish_request() ) {
		return $translation;
	}

	static $strings = null;
	if ( null === $strings ) {
		$strings = array(
			// Shared lead forms and reusable treatment sections.
			'Free Consultation' => 'Bezpłatna konsultacja',
			'Chat on WhatsApp' => 'Napisz na WhatsApp',
			'Name and surname' => 'Imię i nazwisko',
			'Phone number' => 'Numer telefonu',
			'Full name' => 'Imię i nazwisko',
			'Phone' => 'Telefon',
			'Email' => 'E-mail',
			'Note' => 'Uwagi',
			'Message' => 'Wiadomość',
			'Tell us about your goals, or any questions you have…' => 'Opowiedz nam o swoich oczekiwaniach lub zadaj pytanie…',
			'Request a Free Consultation' => 'Poproś o bezpłatną konsultację',
			'Thank you! Your request has been received — our team will contact you shortly.' => 'Dziękujemy! Otrzymaliśmy Twoje zgłoszenie — nasz zespół wkrótce się z Tobą skontaktuje.',
			'Procedure quick facts' => 'Najważniejsze informacje o zabiegu',
			'Get Started' => 'Zacznij teraz',
			'Learn More' => 'Dowiedz się więcej',
			'Learn more' => 'Dowiedz się więcej',
			'Previous photo' => 'Poprzednie zdjęcie',
			'Next photo' => 'Następne zdjęcie',
			'Go to photo %d' => 'Przejdź do zdjęcia %d',
			'Previous step' => 'Poprzedni etap',
			'Next step' => 'Następny etap',
			'Steps' => 'Etapy',
			'Contents' => 'Spis treści',
			'step' => 'etap',
			'Previous result' => 'Poprzedni rezultat',
			'Next result' => 'Następny rezultat',
			'Before and after result' => 'Rezultat przed i po',
			'Enlarge result' => 'Powiększ rezultat',
			'grafts' => 'graftów',
			'See full gallery' => 'Zobacz pełną galerię',
			'Languages spoken' => 'Języki',
			'View Résumé' => 'Zobacz życiorys',
			'Meet the doctor' => 'Poznaj lekarza',

			// Blog landing and cards. Article content stays outside this scope.
			'From the Journal' => 'Z naszego magazynu',
			'Research, results & recovery' => 'Wiedza, rezultaty i rekonwalescencja',
			'Expert articles on hair restoration, plastic surgery, dental treatment and the journey to your transformation — written by the Estecapelli team.' => 'Eksperckie artykuły o odbudowie włosów, chirurgii plastycznej, stomatologii i drodze do przemiany — przygotowane przez zespół Estecapelli.',
			'Article categories' => 'Kategorie artykułów',
			'All' => 'Wszystkie',
			'Latest article' => 'Najnowszy artykuł',
			'Read the article' => 'Przeczytaj artykuł',
			'Read more' => 'Czytaj więcej',
			'Posts pagination' => 'Stronicowanie artykułów',
			'Newer' => 'Nowsze',
			'Older' => 'Starsze',
			'No articles have been published yet. New posts will appear here as soon as our team publishes them.' => 'Nie opublikowano jeszcze żadnych artykułów. Nowe treści pojawią się tutaj, gdy tylko nasz zespół je opublikuje.',

			// Contact page.
			'Hello Estecapelli, I would like to book a free consultation.' => 'Dzień dobry Estecapelli, chcę umówić bezpłatną konsultację.',
			'Hello Estecapelli, I would like a free analysis. Here are my photos:' => 'Dzień dobry Estecapelli, proszę o bezpłatną analizę. Oto moje zdjęcia:',
			'English' => 'Angielski',
			'Türkçe' => 'Turecki',
			'French' => 'Francuski',
			'Italian' => 'Włoski',
			'Spanish' => 'Hiszpański',
			'Polish' => 'Polski',
			'Portuguese' => 'Portugalski',
			'Contact Us' => 'Skontaktuj się z nami',
			'Let’s start your journey' => 'Rozpocznijmy Twoją drogę',
			'Reach our team by WhatsApp, phone or email — or leave your details and a medical consultant will get back to you in your own language.' => 'Skontaktuj się z nami przez WhatsApp, telefon lub e-mail albo zostaw swoje dane, a konsultant medyczny odpowie w Twoim języku.',
			'We usually reply within an hour' => 'Zwykle odpowiadamy w ciągu godziny',
			'Chat with us now' => 'Napisz do nas teraz',
			'Call' => 'Zadzwoń',
			'Request a free consultation' => 'Poproś o bezpłatną konsultację',
			'Tell us a little about you and we’ll be in touch shortly.' => 'Napisz krótko, czego potrzebujesz, a wkrótce się z Tobą skontaktujemy.',
			'Interested in' => 'Interesuje mnie',
			'Select a treatment' => 'Wybierz zabieg',
			'Hair Transplant' => 'Przeszczep włosów',
			'Plastic Surgery' => 'Chirurgia plastyczna',
			'Dental Treatment' => 'Leczenie stomatologiczne',
			'Not sure yet' => 'Jeszcze nie wiem',
			'Send Request' => 'Wyślij zgłoszenie',
			'Visit & reach us' => 'Odwiedź nas lub skontaktuj się',
			'Address' => 'Adres',
			'Working hours' => 'Godziny pracy',
			'Monday – Sunday: 09:00 – 18:00 (GMT+3)' => 'Poniedziałek–niedziela: 09:00–18:00 (GMT+3)',
			'What happens next?' => 'Co dzieje się dalej?',
			'We review your request and reach out to understand your goals.' => 'Analizujemy Twoje zgłoszenie i kontaktujemy się, aby poznać Twoje oczekiwania.',
			'You receive a free, personalised treatment plan and quote.' => 'Otrzymujesz bezpłatny, indywidualny plan leczenia i wycenę.',
			'We arrange your dates, travel and stay — and welcome you to Istanbul.' => 'Organizujemy termin, podróż i pobyt, a następnie witamy Cię w Stambule.',
			'Send your photos, get a free analysis' => 'Wyślij zdjęcia i otrzymaj bezpłatną analizę',
			'Share a few photos of your hair, face or smile and our specialists will assess your case and recommend the right approach — at no cost and with no obligation.' => 'Prześlij kilka zdjęć włosów, twarzy lub uśmiechu. Nasi specjaliści bezpłatnie i bez zobowiązań ocenią Twój przypadek oraz zalecą odpowiednie rozwiązanie.',
			'Send Photos on WhatsApp' => 'Wyślij zdjęcia przez WhatsApp',
			'Talk to us in your own language' => 'Porozmawiaj z nami w swoim języku',
			'Estecapelli clinic location' => 'Lokalizacja kliniki Estecapelli',
			// Floating WhatsApp button + fake WhatsApp chat popup.
			'Free Hair Analysis' => 'Bezpłatna analiza włosów',
			'Reply in 2 minutes' => 'Odpowiedź w 2 minuty',
			'WhatsApp chat' => 'Czat WhatsApp',
			'Close chat' => 'Zamknij czat',
			'online' => 'online',
			'Today' => 'Dzisiaj',
			'Hi! 👋 Welcome to Estecapelli. Tell us which treatment you are interested in and we will get back to you within minutes.' => 'Cześć! 👋 Witamy w Estecapelli. Napisz, który zabieg Cię interesuje, a odpowiemy w ciągu kilku minut.',
			'Type a message' => 'Napisz wiadomość',
			'Write your message first' => 'Najpierw napisz wiadomość',
			'Send' => 'Wyślij',
			'Confirm your message' => 'Potwierdź wiadomość',
			'This message will be sent to our WhatsApp:' => 'Ta wiadomość zostanie wysłana na nasz WhatsApp:',
			'Cancel' => 'Anuluj',
		);
	}

	return $strings[ $text ] ?? $translation;
}

add_filter( 'ngettext', 'estecapelli_pl_ngettext_fallback', 25, 5 );
/** Translate the reading-time label used by blog cards. */
function estecapelli_pl_ngettext_fallback( $translation, $single, $plural, $number, $domain ) {
	if (
		'estecapelli' === $domain &&
		estecapelli_is_polish_request() &&
		in_array( $translation, array( $single, $plural ), true ) &&
		'%d min read' === $single &&
		'%d min read' === $plural
	) {
		return '%d min czytania';
	}

	return $translation;
}
