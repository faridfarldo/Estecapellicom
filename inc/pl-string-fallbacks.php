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
			// 404 page.
			'This page could not be found' => 'Nie znaleziono tej strony',
			'The page you are looking for may have been moved or no longer exists. You can head back to the homepage, or pick one of the sections below.' => 'Strona, której szukasz, mogła zostać przeniesiona lub już nie istnieje. Możesz wrócić na stronę główną albo wybrać jedną z poniższych sekcji.',
			'Back to homepage' => 'Wróć na stronę główną',
			'Contact us' => 'Skontaktuj się z nami',
			'Popular sections' => 'Najczęściej odwiedzane sekcje',
			'Site sections' => 'Sekcje serwisu',

			// Doctor profiles.
			'Résumé' => 'Życiorys',
			'Book a Free Consultation' => 'Umów bezpłatną konsultację',
			'Book a consultation' => 'Umów konsultację',
			'Book your free consultation' => 'Umów swoją bezpłatną konsultację',
			'Get a Free Consultation' => 'Uzyskaj bezpłatną konsultację',
			'Get a free consultation — leave your details and we will reach out.' => 'Uzyskaj bezpłatną konsultację — zostaw swoje dane, a my się odezwiemy.',
			'Leave your details and a medical consultant will get back to you shortly — no obligation.' => 'Zostaw swoje dane, a konsultant medyczny wkrótce się odezwie — bez zobowiązań.',
			'Speak with our medical team — free, no obligation. Get a personalized plan based on your goals.' => 'Porozmawiaj z naszym zespołem medycznym — bezpłatnie i bez zobowiązań. Otrzymaj spersonalizowany plan oparty na Twoich celach.',
			'Medical Director & Co-founder' => 'Naczelny lekarz i współzałożyciel',
			'Our Expertise' => 'Nasze specjalizacje',
			'Ready to start your transformation?' => 'Gotowy na swoją przemianę?',

			// Before & after gallery, on the landing page and inside treatments.
			'Real Results' => 'Prawdziwe rezultaty',
			'Real Patients · Real Results' => 'Prawdziwi pacjenci · Prawdziwe rezultaty',
			'Real Stories' => 'Prawdziwe historie',
			'Results will appear here soon.' => 'Rezultaty pojawią się tutaj wkrótce.',
			'View treatment' => 'Zobacz zabieg',
			'View all results' => 'Zobacz wszystkie rezultaty',
			'View result %d' => 'Zobacz rezultat %d',
			'%s services' => 'Zabiegi: %s',
			'%s grafts' => '%s graftów',
			'%1$s grafts · %2$s' => '%1$s graftów · %2$s',
			'Grafts' => 'Grafty',
			'Before &amp; After' => 'Przed i po',
			'Before and after results' => 'Rezultaty przed i po',
			'Before treatment' => 'Przed zabiegiem',
			'After treatment' => 'Po zabiegu',
			'After' => 'Po',
			'Patient from' => 'Pacjent z',
			'Outcomes from our %s patients.' => 'Rezultaty naszych pacjentów po zabiegu %s.',
			'Choose a hair-transplant technique to browse real patient results.' => 'Wybierz technikę przeszczepu włosów, aby zobaczyć prawdziwe rezultaty pacjentów.',
			'Hair transplant techniques' => 'Techniki przeszczepu włosów',
			'Treatment categories' => 'Kategorie zabiegów',
			'Technique' => 'Technika',
			'Procedure details' => 'Szczegóły zabiegu',
			'Open the gallery' => 'Otwórz galerię',
			'Open gallery: %s' => 'Otwórz galerię: %s',
			'Back to gallery' => 'Powrót do galerii',
			'Click any photo to open the gallery' => 'Kliknij dowolne zdjęcie, aby otworzyć galerię',
			'Use the arrows to browse · click any photo to enlarge' => 'Przeglądaj strzałkami · kliknij zdjęcie, aby powiększyć',
			'Enlarge photo' => 'Powiększ zdjęcie',
			'Photo viewer' => 'Przeglądarka zdjęć',
			'More patient stories' => 'Więcej historii pacjentów',
			'Their results speak louder than any ad ever could.' => 'Ich rezultaty mówią więcej niż jakakolwiek reklama.',
			'Hear, in their own words, how patients from around the world describe their Estecapelli journey — from first consultation to long-term result.' => 'Posłuchaj, jak pacjenci z całego świata własnymi słowami opisują swoją drogę z Estecapelli — od pierwszej konsultacji po długotrwały rezultat.',
			'England' => 'Anglia',
			'Ireland' => 'Irlandia',
			'Scotland' => 'Szkocja',
			'Canada' => 'Kanada',

			// Patient story narratives shown on the homepage.
			'Alexandre came to our clinic from Ireland to redesign his hairline and improve density across the frontal area, mid-scalp and crown. Based on his desired hair model and donor capacity, we prioritised a natural, face-appropriate hairline first, then a balanced, homogeneous distribution through the mid-scalp and crown. The procedure was performed with the DHI Vita technique in a single session, transplanting 5,000 grafts in total — around 3,000 in the frontal area and hairline and roughly 2,000 across the mid-scalp and crown. The operation progressed normally, with PRP support applied at the end.' => 'Alexandre przyjechał do naszej kliniki z Irlandii, aby przeprojektować linię włosów i poprawić gęstość w strefie czołowej, na środku głowy i na wierzchołku. Na podstawie oczekiwanego modelu włosów oraz możliwości obszaru dawczego w pierwszej kolejności zaplanowaliśmy naturalną linię włosów dopasowaną do rysów twarzy, a następnie równomierne, jednorodne rozłożenie graftów na środku głowy i wierzchołku. Zabieg wykonano techniką DHI Vita w jednej sesji, przeszczepiając łącznie 5000 graftów — około 3000 w strefie czołowej i linii włosów oraz około 2000 na środku głowy i wierzchołku. Zabieg przebiegł prawidłowo, a na koniec zastosowano wsparcie PRP.',
			'Craig came to our clinic from Scotland to improve his hairline and increase density across the frontal area and crown. Based on his hair model and consultation plan, we prioritised a denser, more natural frontal hairline first, then a homogeneous graft distribution through the top and crown to cover the visible gaps. The procedure was performed with the FUE Vita technique in a single session, transplanting 5,400 grafts in total. Grafts were extracted evenly from the donor area with good quality, then implanted with attention to natural direction, density balance and overall coverage. The operation progressed normally, with PRP support applied at the end.' => 'Craig przyjechał do naszej kliniki ze Szkocji, aby poprawić linię włosów i zwiększyć gęstość w strefie czołowej oraz na wierzchołku. Na podstawie modelu włosów i planu konsultacji zadbaliśmy najpierw o gęstszą, bardziej naturalną linię czołową, a następnie o jednorodne rozłożenie graftów na szczycie i wierzchołku głowy, aby pokryć widoczne ubytki. Zabieg wykonano techniką FUE Vita w jednej sesji, przeszczepiając łącznie 5400 graftów. Grafty pobrano równomiernie z obszaru dawczego, zachowując dobrą jakość, a następnie wszczepiono je z dbałością o naturalny kierunek, równowagę gęstości i całościowe pokrycie. Zabieg przebiegł prawidłowo, a na koniec zastosowano wsparcie PRP.',
			'Dale came to our clinic from England to increase density across the frontal area, mid-scalp and crown. Based on his consultation plan and donor capacity, we focused on building stronger density in the front and crown first, while balancing the mid-scalp for an even result. The procedure was performed with the Vita protocol in a single session, transplanting 4,500 grafts in total. With a good-density donor area, the grafts were distributed to the planned coverage needs — higher density prioritised in the frontal area and crown, and the mid-scalp reinforced for a more homogeneous overall look. PRP support was applied at the end of the procedure.' => 'Dale przyjechał do naszej kliniki z Anglii, aby zwiększyć gęstość w strefie czołowej, na środku głowy i na wierzchołku. Na podstawie planu konsultacji oraz możliwości obszaru dawczego skupiliśmy się najpierw na zbudowaniu większej gęstości z przodu i na wierzchołku, równoważąc jednocześnie środek głowy dla równomiernego efektu. Zabieg wykonano w protokole Vita w jednej sesji, przeszczepiając łącznie 4500 graftów. Dzięki dobrej gęstości obszaru dawczego grafty rozłożono zgodnie z zaplanowanym pokryciem — z priorytetem większej gęstości w strefie czołowej i na wierzchołku oraz wzmocnieniem środka głowy dla bardziej jednorodnego efektu całości. Na koniec zabiegu zastosowano wsparcie PRP.',
			'Danny came to our clinic from England to improve density in his frontal area and extend coverage toward the mid-scalp. Based on his consultation plan, we prioritised harvesting the maximum number of grafts and building a dense, natural-looking result in the thinning zones, aiming for good density up to the midsection. The procedure was performed with the FUE Vita technique in a single session, transplanting 5,000 grafts in total. Grafts were extracted homogeneously from the donor area with good quality, then implanted with attention to natural direction, density balance and frontal-to-mid-scalp coverage. The operation progressed normally, with PRP support applied at the end.' => 'Danny przyjechał do naszej kliniki z Anglii, aby poprawić gęstość w strefie czołowej i rozszerzyć pokrycie w kierunku środka głowy. Zgodnie z planem konsultacji priorytetem było pobranie maksymalnej liczby graftów i zbudowanie gęstego, naturalnie wyglądającego efektu w przerzedzonych strefach, z dobrą gęstością aż do środkowej części głowy. Zabieg wykonano techniką FUE Vita w jednej sesji, przeszczepiając łącznie 5000 graftów. Grafty pobrano jednorodnie z obszaru dawczego, zachowując dobrą jakość, a następnie wszczepiono je z dbałością o naturalny kierunek, równowagę gęstości oraz pokrycie od strefy czołowej po środek głowy. Zabieg przebiegł prawidłowo, a na koniec zastosowano wsparcie PRP.',
			'Pascal came to our clinic from Canada to improve density in his frontal area and extend coverage toward the mid-scalp. Based on his consultation plan and donor capacity, we planned maximum graft extraction to cover the frontal area up to the midsection with good density, with a second session planned for full coverage. The procedure was performed with the Exosome FUE technique, transplanting 5,000 grafts in this session. Grafts were extracted homogeneously from the donor area with good hair quality, then implanted with attention to natural direction, density balance and frontal-to-mid-scalp coverage. PRP support was applied at the end of the procedure.' => 'Pascal przyjechał do naszej kliniki z Kanady, aby poprawić gęstość w strefie czołowej i rozszerzyć pokrycie w kierunku środka głowy. Na podstawie planu konsultacji oraz możliwości obszaru dawczego zaplanowaliśmy maksymalne pobranie graftów, aby z dobrą gęstością pokryć strefę czołową aż do środkowej części głowy, z drugą sesją przewidzianą dla pełnego pokrycia. Zabieg wykonano techniką Exosome FUE, przeszczepiając w tej sesji 5000 graftów. Grafty pobrano jednorodnie z obszaru dawczego przy dobrej jakości włosów, a następnie wszczepiono je z dbałością o naturalny kierunek, równowagę gęstości oraz pokrycie od strefy czołowej po środek głowy. Na koniec zabiegu zastosowano wsparcie PRP.',
			'Ricardo came to our clinic from Ireland to restore a natural hairline and improve density across the frontal area, temples and a small thinning area on the crown. Based on his consultation plan and his wish for a natural-looking result, we adjusted the frontal line to his facial structure and planned dense implantation in the front, with additional grafts placed in the temples and crown for overall balance. The procedure was performed with the DHI Vita technique in a single session, transplanting approximately 4,200 grafts. The grafts were extracted with good hair quality, then implanted with attention to natural direction, symmetry, density balance and frontal-to-crown coverage. The operation progressed normally, with PRP support at the end.' => 'Ricardo przyjechał do naszej kliniki z Irlandii, aby odtworzyć naturalną linię włosów i poprawić gęstość w strefie czołowej, na skroniach oraz w niewielkim przerzedzeniu na wierzchołku. Na podstawie planu konsultacji i jego oczekiwania naturalnego efektu dopasowaliśmy linię czołową do struktury twarzy i zaplanowaliśmy gęstą implantację z przodu, a dodatkowe grafty umieściliśmy na skroniach i wierzchołku dla ogólnej równowagi. Zabieg wykonano techniką DHI Vita w jednej sesji, przeszczepiając około 4200 graftów. Grafty pobrano przy dobrej jakości włosów, a następnie wszczepiono je z dbałością o naturalny kierunek, symetrię, równowagę gęstości oraz pokrycie od strefy czołowej po wierzchołek. Zabieg przebiegł prawidłowo, a na koniec zastosowano wsparcie PRP.',
			'Sam came to our clinic from Ireland to improve his hairline and increase overall density across the frontal area, mid-scalp and crown. Based on his consultation plan and his wish for full coverage in a single session, we planned maximum graft extraction and focused on building good density in the frontal hairline first, then distributed the remaining grafts through the mid-scalp and crown for balanced coverage. The procedure was performed with the FUE Vita technique in a single session, transplanting 6,200 grafts in total. Grafts were extracted homogeneously from the donor area with good quality, then implanted with attention to natural direction, density balance and overall coverage. The operation progressed normally, with PRP support at the end.' => 'Sam przyjechał do naszej kliniki z Irlandii, aby poprawić linię włosów i zwiększyć ogólną gęstość w strefie czołowej, na środku głowy i na wierzchołku. Na podstawie planu konsultacji i jego oczekiwania pełnego pokrycia w jednej sesji zaplanowaliśmy maksymalne pobranie graftów i skupiliśmy się najpierw na zbudowaniu dobrej gęstości w czołowej linii włosów, a pozostałe grafty rozłożyliśmy na środku głowy i wierzchołku dla równomiernego pokrycia. Zabieg wykonano techniką FUE Vita w jednej sesji, przeszczepiając łącznie 6200 graftów. Grafty pobrano jednorodnie z obszaru dawczego, zachowując dobrą jakość, a następnie wszczepiono je z dbałością o naturalny kierunek, równowagę gęstości i całościowe pokrycie. Zabieg przebiegł prawidłowo, a na koniec zastosowano wsparcie PRP.',
			'Hairline redesigned, density restored from front to crown.' => 'Przeprojektowana linia włosów i odbudowana gęstość od przodu po wierzchołek.',
			'A denser frontal hairline and a fuller crown.' => 'Gęstsza linia włosów z przodu i pełniejszy wierzchołek głowy.',
			'A natural hairline with balanced front-to-crown density.' => 'Naturalna linia włosów z równomierną gęstością od przodu po wierzchołek.',
			'Frontal density extended through the mid-scalp.' => 'Gęstość z przodu rozszerzona na środek głowy.',
			'Frontal density extended toward the mid-scalp.' => 'Gęstość z przodu poprowadzona ku środkowi głowy.',
			'Full-coverage restoration in a single session.' => 'Pełne pokrycie odbudowane w jednej sesji.',
			'Stronger density across front, mid-scalp and crown.' => 'Większa gęstość z przodu, na środku głowy i na wierzchołku.',

			// Homepage: signature methods, AI analysis, comparison and stats.
			'SIGNATURE' => 'AUTORSKIE',
			'Signature Protocol · Estecapelli Exclusive' => 'Autorski protokół · wyłącznie w Estecapelli',
			'Patented · Estecapelli Exclusive' => 'Opatentowane · wyłącznie w Estecapelli',
			'Three methods that shape every treatment.' => 'Trzy metody, które kształtują każde leczenie.',
			'Two are exclusively ours. One sets the standard for personalised planning. All three power the results our patients trust us for.' => 'Dwie należą wyłącznie do nas. Jedna wyznacza standard spersonalizowanego planowania. Wszystkie trzy odpowiadają za rezultaty, którym ufają nasi pacjenci.',
			'Switch between tabs to explore the methods we are best known for in each field of care.' => 'Przełączaj zakładki, aby poznać metody, z których jesteśmy najbardziej znani w każdej dziedzinie opieki.',
			'Pick a field. See our signature treatments.' => 'Wybierz dziedzinę. Zobacz nasze autorskie zabiegi.',
			'Hover or tap to discover' => 'Najedź lub dotknij, aby odkryć',
			'On the playlist' => 'Na playliście',
			'Play %s — %s' => 'Odtwórz %s — %s',
			'Power Derived from Vitamins' => 'Siła pochodząca z witamin',
			'Vitamin-nourished grafts' => 'Grafty odżywione witaminami',
			'A vitamin-cooled bath that keeps grafts strong out of the body.' => 'Chłodzona kąpiel witaminowa, która utrzymuje siłę graftów poza organizmem.',
			'Cool-Vapor' => 'Cool-Vapor',
			'Grafts lose strength the moment they leave the body. Our VITA Protocol bathes them in a specially formulated vitamin cocktail with cool-vapor application — keeping every follicle alive, nourished, and resilient until placement.' => 'Grafty tracą siłę w chwili, gdy opuszczają organizm. Nasz protokół VITA zanurza je w specjalnie opracowanym koktajlu witaminowym z chłodzącą aplikacją cool-vapor — dzięki czemu każdy mieszek pozostaje żywy, odżywiony i odporny aż do momentu wszczepienia.',
			'Learn more about VITA' => 'Dowiedz się więcej o VITA',
			'Regenerative Stem Cell Therapy' => 'Regeneracyjna terapia komórkami macierzystymi',
			'Follicle survival over 72 hours' => 'Przeżywalność mieszków przez 72 godziny',
			'Mesenchymal stem-cell support that keeps follicles alive longer.' => 'Wsparcie mezenchymalnych komórek macierzystych, które dłużej utrzymuje mieszki przy życiu.',
			'Cell-regenerating exosomes that keep follicles alive longer.' => 'Regenerujące komórki egzosomy, które dłużej utrzymują mieszki przy życiu.',
			'Our patented Exosome Treatment is derived from mesenchymal stem cells found in the umbilical cord — designed to lift hair-follicle survival to 98% over 72 hours, with faster recovery, stronger growth, and naturally lasting results.' => 'Nasza opatentowana terapia Exosome pozyskiwana jest z mezenchymalnych komórek macierzystych obecnych w sznurze pępowinowym — zaprojektowano ją tak, aby podnieść przeżywalność mieszków włosowych do 98% w ciągu 72 godzin, zapewniając szybszą regenerację, silniejszy wzrost i naturalnie trwałe rezultaty.',
			'Exosome Treatment' => 'Terapia Exosome',
			'Learn about Exosome FUE' => 'Poznaj Exosome FUE',
			'Millimetric' => 'Milimetrowa',
			'Millimetric Hair & Scalp Analysis' => 'Milimetrowa analiza włosów i skóry głowy',
			'Precision per scalp scan' => 'Precyzja każdego skanu skóry głowy',
			'AI maps your scalp before a single graft is planned.' => 'AI mapuje Twoją skórę głowy, zanim zaplanujemy pierwszy graft.',
			'TrichoLab examines your hair and scalp with millimetric accuracy — measuring follicle density, thickness, donor capacity, and loss patterns — so every graft is planned for your unique anatomy and the result feels naturally yours.' => 'TrichoLab bada Twoje włosy i skórę głowy z milimetrową dokładnością — mierzy gęstość mieszków, ich grubość, możliwości obszaru dawczego i wzorce wypadania — dzięki czemu każdy graft planowany jest pod Twoją indywidualną anatomię, a efekt wygląda naturalnie i jest w pełni Twój.',
			'Learn more about TrichoLab' => 'Dowiedz się więcej o TrichoLab',
			'Sapphire-blade precision for natural density and faster healing.' => 'Precyzja ostrzy szafirowych dla naturalnej gęstości i szybszego gojenia.',
			'Choi-pen implantation for precise angle and direction control.' => 'Implantacja pisakiem Choi z precyzyjną kontrolą kąta i kierunku.',
			'Premium Hair Transplant Method' => 'Metoda przeszczepu włosów klasy premium',
			'AI-Powered Diagnosis' => 'Diagnostyka wspierana przez AI',
			'Your Personalised Hair Plan, Powered by Estecapelli' => 'Twój spersonalizowany plan włosowy, napędzany przez Estecapelli',
			'We take your photos and analyse your hair condition with an AI we have specially trained.' => 'Przyjmujemy Twoje zdjęcia i analizujemy stan Twoich włosów za pomocą specjalnie wytrenowanego przez nas AI.',
			'We trained our AI on the results of more than 15,000 patients. Learning from their success stories, it analyses your condition and guides you to the treatment that fits you best.' => 'Nasze AI wytrenowaliśmy na rezultatach ponad 15 000 pacjentów. Ucząc się z ich historii sukcesu, analizuje Twój przypadek i wskazuje leczenie najlepiej dopasowane do Ciebie.',
			'Analyse my photos with AI' => 'Przeanalizuj moje zdjęcia z AI',
			'I know my hair-loss area' => 'Wiem, gdzie tracę włosy',
			'Start self-assessment' => 'Rozpocznij samoocenę',
			'Tap the areas where you are losing hair' => 'Dotknij obszarów, w których tracisz włosy',
			'Mark the areas where you are losing hair and tell us about your goals.' => 'Zaznacz obszary, w których tracisz włosy, i opisz swoje cele.',
			'Scalp zones' => 'Strefy skóry głowy',
			'Selected areas' => 'Wybrane obszary',
			'No area selected yet.' => 'Nie wybrano jeszcze żadnego obszaru.',
			'Get my analysis' => 'Odbierz moją analizę',
			'Back to options' => 'Powrót do opcji',

			// Homepage: comparison table, numbers, clinic tour and trust badges.
			'The Comparison' => 'Porównanie',
			'Nine details where every choice matters.' => 'Dziewięć szczegółów, w których liczy się każdy wybór.',
			'See how our comprehensive approach and advanced techniques set us apart from other clinics.' => 'Zobacz, jak nasze kompleksowe podejście i zaawansowane techniki wyróżniają nas na tle innych klinik.',
			'Estecapelli versus other clinics — feature comparison' => 'Estecapelli a inne kliniki — porównanie',
			'Included at Estecapelli' => 'W cenie w Estecapelli',
			'Included' => 'W cenie',
			'Not included' => 'Nie w cenie',
			'Other Clinics' => 'Inne kliniki',
			'Standard Care' => 'Opieka standardowa',
			'Premium Care' => 'Opieka premium',
			'Built for patients who refuse to compromise.' => 'Stworzone dla pacjentów, którzy nie idą na kompromisy.',
			'Innovative Techniques (Exosome, FUE, DHI, VITA)' => 'Innowacyjne techniki (Exosome, FUE, DHI, VITA)',
			'Success-Oriented Treatment Planning' => 'Planowanie leczenia nastawione na wynik',
			'Latest Technology Implementation' => 'Wdrożenie najnowszych technologii',
			'Pain-Free Anaesthesia' => 'Znieczulenie bezbolesne',
			'Dedicated Post-Op Follow-Up Team' => 'Dedykowany zespół opieki pooperacyjnej',
			'5-Star Hotel Accommodation' => 'Zakwaterowanie w hotelu 5-gwiazdkowym',
			'VIP Transfer Service' => 'Transfer VIP',
			'Personal Translator' => 'Osobisty tłumacz',
			'Support Team' => 'Zespół wsparcia',
			'Our partner hotels' => 'Nasze hotele partnerskie',
			'Estecapelli in numbers' => 'Estecapelli w liczbach',
			'Patients trust us by the numbers' => 'Zaufanie pacjentów w liczbach',
			'Happy Patients' => 'Zadowolonych pacjentów',
			'Years of Experience' => 'Lat doświadczenia',
			'Countries Served' => 'Obsługiwane kraje',
			'15+ years in aesthetic medicine' => 'Ponad 15 lat w medycynie estetycznej',
			'Global standards, Turkish expertise.' => 'Światowe standardy, tureckie doświadczenie.',
			'Aesthetic Excellence, Backed by Medical Trust.' => 'Doskonałość estetyczna oparta na zaufaniu medycznym.',
			'Experience the Estecapelli difference — advanced techniques, patient-focused care.' => 'Poczuj różnicę Estecapelli — zaawansowane techniki i opieka skupiona na pacjencie.',
			'At Estecapelli, we go beyond expectations to deliver personalised, high-quality care backed by years of experience and international standards. Our expert team combines innovative methods with a patient-first approach to deliver results that look natural and feel confident.' => 'W Estecapelli wykraczamy poza oczekiwania, oferując spersonalizowaną opiekę najwyższej jakości, opartą na wieloletnim doświadczeniu i międzynarodowych standardach. Nasz zespół ekspertów łączy innowacyjne metody z podejściem stawiającym pacjenta na pierwszym miejscu, aby uzyskać rezultaty, które wyglądają naturalnie i dodają pewności siebie.',
			'EST. 2010 · ISTANBUL, TÜRKİYE' => 'OD 2010 · STAMBUŁ, TURCJA',
			'ACCREDITED CLINIC' => 'KLINIKA AKREDYTOWANA',
			'Internationally Accredited & Certified' => 'Akredytacje i certyfikaty międzynarodowe',
			'Internationally accredited & certified — Ministry of Health, Certified Medical Travel Agency, HRSA, NACo Award, ISO 13485' => 'Akredytacje i certyfikaty międzynarodowe — Ministerstwo Zdrowia, certyfikowane biuro turystyki medycznej, HRSA, nagroda NACo, ISO 13485',
			'Internationally accredited — Ministry of Health, HRSA, NACo, ISO 13485, Certified Medical Travel Agency' => 'Akredytacje międzynarodowe — Ministerstwo Zdrowia, HRSA, NACo, ISO 13485, certyfikowane biuro turystyki medycznej',
			'Internationally accredited — Ministry of Health, HRSA, NACo, ISO 13485, Certified Medical Travel Agent' => 'Akredytacje międzynarodowe — Ministerstwo Zdrowia, HRSA, NACo, ISO 13485, certyfikowany agent turystyki medycznej',
			'Ministry of Health Licensed' => 'Licencja Ministerstwa Zdrowia',
			'ISO 9001 · TURSAB Certified' => 'Certyfikaty ISO 9001 · TURSAB',
			'Our Licences' => 'Nasze licencje',
			'Google Reviews' => 'Opinie Google',
			'Trustpilot rating: 5 out of 5, Excellent' => 'Ocena Trustpilot: 5 na 5, doskonała',
			'Rated %d out of 5' => 'Ocena %d na 5',
			'Excellent' => 'Doskonała',
			'on Trustpilot' => 'na Trustpilot',

			// Clinic tour and facility rooms.
			'Our Clinic' => 'Nasza klinika',
			'Step inside our Istanbul clinic' => 'Zajrzyj do naszej kliniki w Stambule',
			'Clinic walkthrough' => 'Spacer po klinice',
			'A Ministry-of-Health licensed, hospital-grade facility in the heart of Istanbul — modern operation rooms, sterile theatres and a permanent on-site medical team.' => 'Licencjonowana przez Ministerstwo Zdrowia placówka o standardzie szpitalnym w sercu Stambułu — nowoczesne sale operacyjne, sterylne bloki i stały zespół medyczny na miejscu.',
			'Hair Transplant Surgery Room' => 'Sala zabiegowa przeszczepu włosów',
			'Hair Analysis Lab' => 'Laboratorium analizy włosów',
			'TrichoLab Room' => 'Gabinet TrichoLab',
			'Dental Clinic' => 'Klinika stomatologiczna',
			'Scroll left' => 'Przewiń w lewo',
			'Scroll right' => 'Przewiń w prawo',
			'Close' => 'Zamknij',

			// Treatment names and one-line summaries used outside the main menu.
			'What We Treat' => 'Co leczymy',
			'Medical Treatment' => 'Leczenie medyczne',
			'Previous treatments' => 'Poprzednie zabiegi',
			'Next treatments' => 'Następne zabiegi',
			'Augmentation, lift and reduction tailored to your goals.' => 'Powiększanie, podniesienie i redukcja dopasowane do Twoich celów.',
			'Brazilian Butt Lift — natural contouring with fat transfer.' => 'Brazylijski lifting pośladków — naturalne modelowanie z przeszczepem tłuszczu.',
			'Nose reshaping that refines proportions and function.' => 'Korekta nosa poprawiająca proporcje i funkcję.',
			'Facelift' => 'Lifting twarzy',
			'Botox' => 'Botoks',
			'Smooths expression lines for a refreshed, rested look.' => 'Wygładza zmarszczki mimiczne, nadając wypoczęty wygląd.',
			'Dermal Fillers' => 'Wypełniacze',
			'Restores volume to cheeks, jawline and under-eye areas.' => 'Przywracają objętość policzkom, linii żuchwy i okolicom pod oczami.',
			'Skin Rejuvenation' => 'Odmładzanie skóry',
			'Non-surgical protocols for firmer, brighter, healthier skin.' => 'Nieoperacyjne protokoły dla jędrniejszej, promiennej i zdrowszej skóry.',
			'PRP Treatment' => 'Terapia PRP',
			'Platelet-rich plasma therapy for skin and hair regeneration.' => 'Terapia osoczem bogatopłytkowym regenerująca skórę i włosy.',
			'Dental Implants' => 'Implanty zębowe',
			'Veneers' => 'Licówki',
			'Thin porcelain shells for a flawless front-of-tooth finish.' => 'Cienkie licówki porcelanowe dla nieskazitelnego wykończenia przednich zębów.',
			'Teeth Whitening' => 'Wybielanie zębów',
			'Professional bleaching for noticeably brighter teeth.' => 'Profesjonalne wybielanie dla zauważalnie jaśniejszych zębów.',
			'Smile Design' => 'Projektowanie uśmiechu',
			'A bespoke makeover that reshapes your entire smile.' => 'Spersonalizowana metamorfoza, która odmienia cały Twój uśmiech.',

			// Blog cards and archives.
			'All articles' => 'Wszystkie artykuły',
			'Articles from the Estecapelli journal.' => 'Artykuły z magazynu Estecapelli.',
			'No articles found here yet.' => 'Nie znaleziono tu jeszcze żadnych artykułów.',
			'Keep reading' => 'Czytaj dalej',

			// Lead forms: labels, placeholders and validation messages.
			'Your name' => 'Twoje imię',
			'Your email' => 'Twój e-mail',
			'Your number' => 'Twój numer',
			'Phone / WhatsApp' => 'Telefon / WhatsApp',
			'Request Call Back' => 'Zamów rozmowę',
			'Name is required.' => 'Imię jest wymagane.',
			'Please enter your name.' => 'Podaj swoje imię.',
			'Please enter a valid email address.' => 'Podaj prawidłowy adres e-mail.',
			'Please enter a valid phone number.' => 'Podaj prawidłowy numer telefonu.',

			// Footer and shared chrome.
			'Visit Us' => 'Odwiedź nas',
			'Sitemap' => 'Mapa strony',
			'Language' => 'Język',
			'Footer legal' => 'Informacje prawne w stopce',
			'All rights reserved.' => 'Wszelkie prawa zastrzeżone.',
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
