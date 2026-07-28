<?php
/**
 * Small French fallbacks for theme UI strings used around treatment listings.
 *
 * WPML String Translation remains authoritative: a saved WPML translation is
 * never replaced. These values only prevent English UI copy from leaking onto
 * French pages while a string is still untranslated in WPML.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current request is being rendered in French.
 *
 * @return bool
 */
function estecapelli_is_french_request() {
	$language = apply_filters( 'wpml_current_language', null );
	if ( $language ) {
		return 'fr' === $language;
	}

	return 0 === strpos( determine_locale(), 'fr_' );
}

add_filter( 'gettext', 'estecapelli_fr_gettext_fallback', 20, 3 );
/**
 * Supply French only when no plugin or language file translated the string.
 *
 * @param string $translation Current translated value.
 * @param string $text        English source value.
 * @param string $domain      Text domain.
 * @return string
 */
function estecapelli_fr_gettext_fallback( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || $translation !== $text || ! estecapelli_is_french_request() ) {
		return $translation;
	}

	$strings = array(
		// Journal / blog UI.
		'From the Journal'                        => 'Le journal',
		'Research, results & recovery'             => 'Recherche, résultats et récupération',
		'Expert articles on hair restoration, plastic surgery, dental treatment and the journey to your transformation — written by the Estecapelli team.' => 'Des articles d’experts sur la restauration capillaire, la chirurgie plastique, les soins dentaires et votre parcours de transformation — rédigés par l’équipe Estecapelli.',
		'Article categories'                       => 'Catégories d’articles',
		'All'                                      => 'Tous',
		'Latest article'                           => 'Dernier article',
		'Read the article'                         => 'Lire l’article',
		'Articles from the Estecapelli journal.' => 'Articles du journal Estecapelli.',
		'All articles'                            => 'Tous les articles',
		'Read more'                               => 'En savoir plus',
		'Posts pagination'                        => 'Pagination des articles',
		'Newer'                                   => 'Plus récents',
		'Older'                                   => 'Plus anciens',
		'No articles found here yet.'             => 'Aucun article trouvé pour le moment.',
		'No articles have been published yet. New posts will appear here as soon as our team publishes them.' => 'Aucun article n’a encore été publié. Les nouveaux articles apparaîtront ici dès leur publication par notre équipe.',

		// Contact page (page-contact.php) — hardcoded UI, not ACF sections.
		'Hello Estecapelli, I would like to book a free consultation.' => 'Bonjour Estecapelli, je souhaite réserver une consultation gratuite.',
		'Hello Estecapelli, I would like a free analysis. Here are my photos:' => 'Bonjour Estecapelli, je souhaite une analyse gratuite. Voici mes photos :',
		'Contact Us'                              => 'Contactez-nous',
		'Let’s start your journey'                => 'Commençons votre parcours',
		'Reach our team by WhatsApp, phone or email — or leave your details and a medical consultant will get back to you in your own language.' => 'Contactez notre équipe par WhatsApp, téléphone ou e-mail — ou laissez-nous vos coordonnées et un consultant médical vous répondra dans votre langue.',
		'We usually reply within an hour'         => 'Nous répondons généralement en moins d’une heure',
		'Chat with us now'                        => 'Discutez avec nous maintenant',
		'Call'                                    => 'Appeler',
		'Email'                                   => 'E-mail',
		'Request a free consultation'             => 'Demandez une consultation gratuite',
		'Tell us a little about you and we’ll be in touch shortly.' => 'Parlez-nous un peu de vous et nous vous recontacterons rapidement.',
		'Thank you! Your request has been received — our team will contact you shortly.' => 'Merci ! Votre demande a bien été reçue — notre équipe vous contactera sous peu.',
		'Full name'                               => 'Nom complet',
		'Name and surname'                        => 'Nom et prénom',
		'Phone'                                   => 'Téléphone',
		'Phone number'                            => 'Numéro de téléphone',
		'Interested in'                           => 'Intéressé(e) par',
		'Select a treatment'                      => 'Sélectionnez un traitement',
		'Hair Transplant'                         => 'Greffe de cheveux',
		'Plastic Surgery'                         => 'Chirurgie plastique',
		'Dental Treatment'                        => 'Traitement dentaire',
		'Not sure yet'                            => 'Je ne sais pas encore',
		'Tell us about your goals, or any questions you have…' => 'Parlez-nous de vos objectifs ou de vos questions…',
		'Send Request'                            => 'Envoyer la demande',
		'Visit & reach us'                        => 'Nous rendre visite',
		'Address'                                 => 'Adresse',
		'Working hours'                           => 'Horaires d’ouverture',
		'Monday – Sunday: 09:00 – 18:00 (GMT+3)'  => 'Lundi – Dimanche : 09h00 – 18h00 (GMT+3)',
		'What happens next?'                      => 'Que se passe-t-il ensuite ?',
		'We review your request and reach out to understand your goals.' => 'Nous étudions votre demande et vous contactons pour comprendre vos objectifs.',
		'You receive a free, personalised treatment plan and quote.' => 'Vous recevez un plan de traitement personnalisé et un devis, gratuitement.',
		'We arrange your dates, travel and stay — and welcome you to Istanbul.' => 'Nous organisons vos dates, votre voyage et votre séjour — et vous accueillons à Istanbul.',
		'Send your photos, get a free analysis'   => 'Envoyez vos photos, recevez une analyse gratuite',
		'Share a few photos of your hair, face or smile and our specialists will assess your case and recommend the right approach — at no cost and with no obligation.' => 'Partagez quelques photos de vos cheveux, de votre visage ou de votre sourire : nos spécialistes évalueront votre cas et vous recommanderont la meilleure approche — gratuitement et sans engagement.',
		'Send Photos on WhatsApp'                 => 'Envoyer des photos sur WhatsApp',
		'Talk to us in your own language'         => 'Parlez-nous dans votre langue',
		'Estecapelli clinic location'             => 'Emplacement de la clinique Estecapelli',

		// Doctor profile / roster UI (single-doctor.php, sections/doctors.php).
		'Book a Free Consultation'                => 'Réserver une consultation gratuite',
		'View Résumé'                             => 'Voir le CV',

		// Before & After gallery (page-before-after.php).
		'Real Results'                            => 'Résultats réels',
		'Results will appear here soon.'          => 'Les résultats apparaîtront bientôt ici.',
		'Treatment categories'                    => 'Catégories de traitement',
		'%s services'                             => '%s — services',
		'View treatment'                          => 'Voir le traitement',
		'grafts'                                  => 'greffons',
		'Enlarge result'                          => 'Agrandir le résultat',
		'Before and after result'                => 'Résultat avant/après',

		// Language chip labels shown on the contact page.
		'English'                                 => 'Anglais',
		'Türkçe'                                  => 'Turc',
		'French'                                  => 'Français',
		'Italian'                                 => 'Italien',
		'Spanish'                                 => 'Espagnol',
		'Polish'                                  => 'Polonais',
		'Portuguese'                              => 'Portugais',
		// Floating WhatsApp button + fake WhatsApp chat popup.
		'Chat on WhatsApp' => 'Discuter sur WhatsApp',
		'Free Hair Analysis' => 'Analyse capillaire gratuite',
		'Reply in 2 minutes' => 'Réponse en 2 minutes',
		'WhatsApp chat' => 'Chat WhatsApp',
		'Close chat' => 'Fermer le chat',
		'online' => 'en ligne',
		'Today' => 'Aujourd’hui',
		'Hi! 👋 Welcome to Estecapelli. Tell us which treatment you are interested in and we will get back to you within minutes.' => 'Bonjour ! 👋 Bienvenue chez Estecapelli. Dites-nous quel traitement vous intéresse et nous vous répondrons en quelques minutes.',
		'Type a message' => 'Écrivez un message',
		'Write your message first' => 'Écrivez d’abord votre message',
		'Send' => 'Envoyer',
		'Confirm your message' => 'Confirmez votre message',
		'This message will be sent to our WhatsApp:' => 'Ce message sera envoyé sur notre WhatsApp :',
		'Cancel' => 'Annuler',
		// 404 page.
		'This page could not be found' => 'Cette page est introuvable',
		'The page you are looking for may have been moved or no longer exists. You can head back to the homepage, or pick one of the sections below.' => 'La page que vous recherchez a peut-être été déplacée ou n’existe plus. Vous pouvez revenir à l’accueil ou choisir l’une des sections ci-dessous.',
		'Back to homepage' => 'Retour à l’accueil',
		'Contact us' => 'Nous contacter',
		'Popular sections' => 'Sections principales',
		'Site sections' => 'Sections du site',
	);

	return $strings[ $text ] ?? $translation;
}

add_filter( 'ngettext', 'estecapelli_fr_ngettext_fallback', 20, 5 );
/**
 * Translate the reading-time label shown on archive cards.
 *
 * @param string $translation Current translated value.
 * @param string $single      Singular English source.
 * @param string $plural      Plural English source.
 * @param int    $number      Quantity.
 * @param string $domain      Text domain.
 * @return string
 */
function estecapelli_fr_ngettext_fallback( $translation, $single, $plural, $number, $domain ) {
	if (
		'estecapelli' === $domain &&
		estecapelli_is_french_request() &&
		in_array( $translation, array( $single, $plural ), true ) &&
		'%d min read' === $single &&
		'%d min read' === $plural
	) {
		return '%d min de lecture';
	}

	return $translation;
}
