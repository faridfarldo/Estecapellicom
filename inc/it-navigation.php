<?php
/**
 * Italian navigation copy and exact indexed-link enforcement.
 *
 * WPML String Translation remains authoritative. The gettext values below are
 * used only when no saved Italian translation exists, while database-backed
 * menu items are localized from their canonical route instead of trusting an
 * English or guessed title/URL stored in the menu record.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Whether the current front-end request is Italian. */
function estecapelli_is_italian_request() {
	$language = (string) apply_filters( 'wpml_current_language', null );
	return 'it' === estecapelli_indexed_language_code( $language );
}

/**
 * Italian labels for every route exposed by the primary navigation.
 *
 * Keys are English source routes because the indexed URL contract uses them as
 * stable identifiers; the resulting href is always the corresponding live
 * Italian route from inc/indexed-urls.php.
 *
 * @return array<string,string>
 */
function estecapelli_it_nav_route_labels() {
	return array(
		'/en'                                                                                                  => 'Home',
		'/en/hair-transplant'                                                                                  => 'Trapianto di capelli',
		'/en/hair-transplant/sapphire-fue-hair-transplant'                                                     => 'Trapianto di capelli FUE Sapphire',
		'/en/hair-transplant/dhi-hair-transplant'                                                              => 'Trapianto di capelli DHI',
		'/en/hair-transplant/exosome-fue-hair-transplant'                                                      => 'Trapianto di capelli Exosome FUE',
		'/en/hair-transplant/vita-treatment'                                                                   => 'Trattamento VITA',
		'/en/hair-transplant/female-hair-transplant'                                                           => 'Trapianto di capelli femminile',
		'/en/hair-transplant/hair-mesotherapy'                                                                 => 'Mesoterapia per capelli',
		'/en/hair-transplant/beard-transplant'                                                                 => 'Trapianto di barba',
		'/en/hair-transplant/eyebrow-transplant'                                                               => 'Trapianto di sopracciglia',
		'/en/hair-transplant/pre-hair-transplant-period'                                                       => 'Periodo pre-trapianto di capelli',
		'/en/hair-transplant/post-hair-transplant-period'                                                      => 'Periodo post-trapianto di capelli',
		'/en/hair-transplant/tricholab'                                                                        => 'TrichoLab',
		'/en/plastic-surgery'                                                                                  => 'Chirurgia plastica',
		'/en/plastic-surgery/rhinoplasty'                                                                      => 'Rinoplastica',
		'/en/plastic-surgery/breast-aesthetics-breast-surgery'                                                 => 'Estetica del seno',
		'/en/plastic-surgery/bbl'                                                                              => 'BBL (lifting brasiliano dei glutei)',
		'/en/plastic-surgery/liposuction'                                                                      => 'Liposuzione',
		'/en/plastic-surgery/face-and-neck-lift-surgery'                                                       => 'Lifting del viso e del collo',
		'/en/plastic-surgery/abdominoplasty-tummy-tuck'                                                        => 'Addominoplastica',
		'/en/plastic-surgery/gynecomastia'                                                                     => 'Ginecomastia',
		'/en/plastic-surgery/obesity-surgeries-bariatric-surgery-and-gastric-balloon'                          => 'Chirurgia dell’obesità',
		'/en/dental-treatment'                                                                                 => 'Trattamenti dentali',
		'/en/dental-treatment/dental-implant'                                                                  => 'Impianto dentale',
		'/en/dental-treatment/hollywood-smile'                                                                 => 'Sorriso hollywoodiano',
		'/en/before-after'                                                                                     => 'Prima e dopo',
		'/en/about-us'                                                                                         => 'Chi siamo',
		'/en/about-us/our-doctors'                                                                             => 'I nostri medici',
		'/en/about-us/our-team'                                                                                => 'Il nostro team',
		'/en/about-us/medical-director'                                                                        => 'Direttore medico',
		'/en/blog'                                                                                             => 'Blog',
		'/en/contact'                                                                                          => 'Contatti',
	);
}

/**
 * Canonical routes for legacy/custom menu items whose stored URL is unusable.
 *
 * @return array<string,string>
 */
function estecapelli_it_nav_source_title_routes() {
	return array(
		'Home'                              => '/en',
		'Hair Transplant'                    => '/en/hair-transplant',
		'Sapphire FUE Hair Transplant'       => '/en/hair-transplant/sapphire-fue-hair-transplant',
		'DHI Hair Transplant'                => '/en/hair-transplant/dhi-hair-transplant',
		'Exosome FUE Hair Transplant'        => '/en/hair-transplant/exosome-fue-hair-transplant',
		'VITA Treatment'                     => '/en/hair-transplant/vita-treatment',
		'Female Hair Transplant'             => '/en/hair-transplant/female-hair-transplant',
		'Hair Mesotherapy'                   => '/en/hair-transplant/hair-mesotherapy',
		'Beard Transplant'                   => '/en/hair-transplant/beard-transplant',
		'Eyebrow Transplant'                 => '/en/hair-transplant/eyebrow-transplant',
		'Pre-Hair Transplant Period'         => '/en/hair-transplant/pre-hair-transplant-period',
		'Post-Hair Transplant Period'        => '/en/hair-transplant/post-hair-transplant-period',
		'TrichoLab'                          => '/en/hair-transplant/tricholab',
		'Plastic Surgery'                    => '/en/plastic-surgery',
		'Rhinoplasty'                        => '/en/plastic-surgery/rhinoplasty',
		'Breast Aesthetics'                  => '/en/plastic-surgery/breast-aesthetics-breast-surgery',
		'BBL (Brazilian Butt Lift)'          => '/en/plastic-surgery/bbl',
		'Liposuction'                        => '/en/plastic-surgery/liposuction',
		'Face & Neck Lift Surgery'           => '/en/plastic-surgery/face-and-neck-lift-surgery',
		'Abdominoplasty (Tummy Tuck)'        => '/en/plastic-surgery/abdominoplasty-tummy-tuck',
		'Gynecomastia'                       => '/en/plastic-surgery/gynecomastia',
		'Obesity Surgeries (Bariatric)'      => '/en/plastic-surgery/obesity-surgeries-bariatric-surgery-and-gastric-balloon',
		'Dental Treatment'                   => '/en/dental-treatment',
		'Dental Implant'                     => '/en/dental-treatment/dental-implant',
		'Hollywood Smile'                    => '/en/dental-treatment/hollywood-smile',
		'Before & After'                     => '/en/before-after',
		'About Us'                           => '/en/about-us',
		'About Estecapelli'                  => '/en/about-us',
		'Our Doctors'                        => '/en/about-us/our-doctors',
		'Our Team'                           => '/en/about-us/our-team',
		'Medical Director'                   => '/en/about-us/medical-director',
		'Blog'                               => '/en/blog',
		'Contact'                            => '/en/contact',
		'Contact Us'                         => '/en/contact',
	);
}

/**
 * Resolve a WordPress menu item to its canonical English route key.
 *
 * @param WP_Post|object $item Menu item.
 * @return string
 */
function estecapelli_it_nav_item_route_key( $item ) {
	if ( ! is_object( $item ) ) {
		return '';
	}

	$key = ! empty( $item->url ) ? estecapelli_indexed_route_key( $item->url ) : '';
	if ( $key ) {
		return $key;
	}

	if ( 'post_type' === ( $item->type ?? '' ) && ! empty( $item->object_id ) ) {
		$key = estecapelli_indexed_post_route_key( (int) $item->object_id );
		if ( $key ) {
			return $key;
		}
	}

	$title         = trim( wp_strip_all_tags( (string) ( $item->title ?? '' ) ) );
	$source_routes = estecapelli_it_nav_source_title_routes();
	if ( isset( $source_routes[ $title ] ) ) {
		return $source_routes[ $title ];
	}

	$italian_route = array_search( $title, estecapelli_it_nav_route_labels(), true );
	if ( false !== $italian_route ) {
		return $italian_route;
	}

	return '';
}

add_filter( 'nav_menu_item_title', 'estecapelli_it_nav_menu_title', 100, 4 );
/**
 * Translate database-backed menu titles by canonical destination.
 *
 * @param string $title Menu title.
 * @param object $item  Menu item.
 * @return string
 */
function estecapelli_it_nav_menu_title( $title, $item ) {
	if ( ! estecapelli_is_italian_request() ) {
		return $title;
	}

	$key    = estecapelli_it_nav_item_route_key( $item );
	$labels = estecapelli_it_nav_route_labels();
	if ( $key && isset( $labels[ $key ] ) ) {
		return $labels[ $key ];
	}

	$strings = estecapelli_it_nav_strings();
	return $strings[ $title ] ?? $title;
}

add_filter( 'nav_menu_link_attributes', 'estecapelli_it_nav_menu_attributes', 1001, 4 );
/**
 * Force database-backed menu hrefs to the exact Italian live URL.
 *
 * @param array  $atts Link attributes.
 * @param object $item Menu item.
 * @return array
 */
function estecapelli_it_nav_menu_attributes( $atts, $item ) {
	if ( ! estecapelli_is_italian_request() ) {
		return $atts;
	}

	$key = estecapelli_it_nav_item_route_key( $item );
	if ( $key && estecapelli_indexed_route_path( $key, 'it' ) ) {
		$atts['href'] = estecapelli_indexed_url( $key, 'it' );
	}

	return $atts;
}

/**
 * Italian fallback copy for the navbar and its mega menus.
 *
 * @return array<string,string>
 */
function estecapelli_it_nav_strings() {
	return array(
		// Header and top level.
		'Skip to content'                                                                        => 'Vai al contenuto',
		'Primary'                                                                               => 'Navigazione principale',
		'Free Consultation'                                                                     => 'Consulenza gratuita',
		'Choose language. Current language: %s'                                                  => 'Scegli la lingua. Lingua attuale: %s',
		'Toggle menu'                                                                           => 'Apri o chiudi il menu',
		'Hair Transplant'                                                                       => 'Trapianto di capelli',
		'Plastic Surgery'                                                                       => 'Chirurgia plastica',
		'Dental Treatment'                                                                      => 'Trattamenti dentali',
		'Before & After'                                                                        => 'Prima e dopo',
		'About Us'                                                                              => 'Chi siamo',
		'Contact Us'                                                                            => 'Contatti',

		// Hair transplant mega menu.
		'Sapphire FUE Hair Transplant'                                                          => 'Trapianto di capelli FUE Sapphire',
		'A natural and permanent hair transplant technique where follicles are placed via sapphire blades.' => 'Una tecnica di trapianto naturale e permanente in cui i follicoli vengono impiantati mediante lame in zaffiro.',
		'DHI Hair Transplant'                                                                   => 'Trapianto di capelli DHI',
		'A modern hair transplantation method performed with a Choi pen that allows precise placement.' => 'Un metodo moderno eseguito con una penna Choi, che consente un impianto preciso dei follicoli.',
		'Exosome FUE Hair Transplant'                                                           => 'Trapianto di capelli Exosome FUE',
		'Supported by cell-regenerating exosomes, it keeps hair follicles alive for longer-lasting density.' => 'Il supporto degli esosomi rigenerativi favorisce la vitalità dei follicoli e una densità duratura.',
		'POPULAR'                                                                               => 'POPOLARE',
		'VITA Treatment'                                                                        => 'Trattamento VITA',
		"Estecapelli's signature method that revitalizes the scalp and strengthens hair."      => 'Il metodo esclusivo di Estecapelli che rivitalizza il cuoio capelluto e rinforza i capelli.',
		'Female Hair Transplant'                                                                => 'Trapianto di capelli femminile',
		'Special for women, dense and natural-looking hair transplant without shaving.'        => 'Un trapianto senza rasatura studiato per le donne, con risultati densi e naturali.',
		'Hair Mesotherapy'                                                                      => 'Mesoterapia per capelli',
		'A vitamin and mineral injection treatment that revitalizes hair follicles.'           => 'Un trattamento con vitamine e minerali che rivitalizza i follicoli piliferi.',
		'Beard Transplant'                                                                      => 'Trapianto di barba',
		'Natural beard and mustache transplantation for sparse or non-existing growth.'        => 'Trapianto naturale di barba e baffi per correggere una crescita rada o assente.',
		'Eyebrow Transplant'                                                                    => 'Trapianto di sopracciglia',
		'Eyebrow transplantation that gives a naturally curved and full eyebrow shape.'        => 'Un trapianto che restituisce sopracciglia piene e dalla curva naturale.',
		'Hair Transplant Care & Technology'                                                     => 'Trapianto di capelli: assistenza e tecnologia',
		'Pre-Hair Transplant Period'                                                            => 'Periodo pre-trapianto di capelli',
		'The preparation and analysis process before hair transplantation.'                    => 'Il percorso di preparazione e analisi prima del trapianto di capelli.',
		'Post-Hair Transplant Period'                                                           => 'Periodo post-trapianto di capelli',
		'The post-procedure recovery and hair care period.'                                     => 'Il periodo di recupero e cura dei capelli dopo la procedura.',
		'Advanced AI-powered hair analysis system that examines the hair and scalp in detail.' => 'Sistema avanzato di analisi con intelligenza artificiale che esamina in dettaglio capelli e cuoio capelluto.',
		'AI Analysis by Estecapelli'                                                            => 'Analisi AI di Estecapelli',
		'We have trained an AI to guide every patient to the best next step — get a first, personalised hair-loss assessment in seconds.' => 'Abbiamo sviluppato un sistema AI per guidare ogni paziente verso il passo successivo più adatto, con una prima valutazione personalizzata in pochi secondi.',
		'Start AI Analysis'                                                                      => 'Inizia l’analisi AI',

		// Plastic surgery mega menu.
		'Rhinoplasty'                                                                           => 'Rinoplastica',
		'Nose reshaping surgery that refines proportions and function.'                         => 'Intervento che rimodella il naso migliorandone proporzioni e funzionalità.',
		'BBL (Brazilian Butt Lift)'                                                             => 'BBL (lifting brasiliano dei glutei)',
		'Natural body contouring with fat transfer to the buttocks.'                            => 'Rimodellamento naturale del corpo mediante trasferimento di grasso ai glutei.',
		'Liposuction'                                                                           => 'Liposuzione',
		'Removes localized fat deposits to reshape the body.'                                   => 'Rimuove i depositi adiposi localizzati per rimodellare il corpo.',
		'Breast Aesthetics'                                                                     => 'Estetica del seno',
		'Augmentation, lift, and reduction tailored to your goals.'                             => 'Aumento, lifting e riduzione personalizzati sui Suoi obiettivi.',
		'Abdominoplasty (Tummy Tuck)'                                                           => 'Addominoplastica',
		'Flattens and tightens the abdomen for a smoother profile.'                             => 'Rende l’addome più piatto e tonico per un profilo armonioso.',
		'Gynecomastia'                                                                          => 'Ginecomastia',
		'Surgical treatment of enlarged male breast tissue.'                                   => 'Trattamento chirurgico dell’aumento del tessuto mammario maschile.',
		'Face & Neck Lift Surgery'                                                              => 'Lifting del viso e del collo',
		'Restores facial contour and reduces visible signs of aging.'                           => 'Ripristina i contorni del viso e riduce i segni visibili dell’invecchiamento.',
		'Obesity Surgeries (Bariatric)'                                                         => 'Chirurgia dell’obesità',
		'Bariatric surgery and gastric balloon for sustainable weight loss.'                   => 'Chirurgia bariatrica e palloncino gastrico per una perdita di peso duratura.',

		// Dental and about mega menus.
		'Dental Implant'                                                                        => 'Impianto dentale',
		'Permanent replacement for missing teeth with titanium roots.'                         => 'Soluzione permanente per i denti mancanti mediante radici in titanio.',
		'Hollywood Smile'                                                                       => 'Sorriso hollywoodiano',
		'A bespoke makeover that reshapes your smile aesthetic.'                                => 'Un trattamento personalizzato che rinnova l’estetica del sorriso.',
		'About Estecapelli'                                                                     => 'Estecapelli',
		'Who we are and what drives our clinic forward.'                                        => 'Chi siamo e quali valori guidano la nostra clinica.',
		'Our Doctors'                                                                           => 'I nostri medici',
		'Meet the surgeons leading every procedure.'                                            => 'Conosca i chirurghi che guidano ogni procedura.',
		'Our Team'                                                                              => 'Il nostro team',
		'The full medical and patient-care team behind your treatment.'                        => 'L’intero team medico e di assistenza che segue il Suo trattamento.',
	);
}

add_filter( 'gettext', 'estecapelli_it_nav_gettext_fallback', 30, 3 );
/** Supply Italian navigation copy only when no existing translation won. */
function estecapelli_it_nav_gettext_fallback( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || $translation !== $text || ! estecapelli_is_italian_request() ) {
		return $translation;
	}

	$strings = estecapelli_it_nav_strings();
	return $strings[ $text ] ?? $translation;
}
