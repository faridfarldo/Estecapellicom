<?php
/**
 * Translations for the cookie consent banner.
 *
 * Same contract as the other *-string-fallbacks.php files: WPML String
 * Translation stays authoritative and a saved translation is never replaced.
 * These values only stop English copy leaking onto a translated page while the
 * string is still untranslated in WPML.
 *
 * The banner is the one place where that leak would be more than cosmetic. A
 * consent request has to be understood to be valid, so an English banner on
 * /pl/ or /es/ is not a rough edge — it is consent that was never really given.
 * That is why these ship with the code rather than waiting for a WPML pass.
 *
 * Unlike the per-language files, this one is keyed by language: the strings
 * belong to a single component and are far easier to keep in step side by side.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Banner copy per language, keyed by the English source string.
 *
 * @return array<string,array<string,string>>
 */
function estecapelli_consent_strings() {
	return array(
		'tr' => array(
			'We value your privacy' => 'Gizliliğinize önem veriyoruz',
			'We use cookies to keep the site working, to understand how it is used and to improve it. You choose what we may store — your choice can be changed at any time.' => 'Sitenin çalışması, nasıl kullanıldığını anlamamız ve siteyi geliştirmemiz için çerezler kullanıyoruz. Neleri saklayabileceğimize siz karar verirsiniz — seçiminizi istediğiniz zaman değiştirebilirsiniz.',
			'Privacy Policy'        => 'Gizlilik Politikası',
			'Cookie Policy'         => 'Çerez Politikası',
			'Strictly necessary'    => 'Zorunlu çerezler',
			'Required for the site, its forms and the security check to work. Always on.' => 'Sitenin, formlarının ve güvenlik doğrulamasının çalışması için gereklidir. Her zaman açıktır.',
			'Analytics'             => 'Analitik',
			'Anonymous statistics about which pages are read and where visitors struggle, so we can improve them.' => 'Hangi sayfaların okunduğuna ve ziyaretçilerin nerede zorlandığına dair anonim istatistikler — bunları geliştirmek için kullanırız.',
			'Marketing'             => 'Pazarlama',
			'Lets us measure our advertising and show you more relevant information.' => 'Reklamlarımızın etkisini ölçmemizi ve size daha uygun bilgiler göstermemizi sağlar.',
			'Accept all'            => 'Tümünü kabul et',
			'Reject all'            => 'Tümünü reddet',
			'Customise'             => 'Özelleştir',
			'Save my choices'       => 'Seçimlerimi kaydet',
			'Cookie settings'       => 'Çerez ayarları',
		),
		'fr' => array(
			'We value your privacy' => 'Nous respectons votre vie privée',
			'We use cookies to keep the site working, to understand how it is used and to improve it. You choose what we may store — your choice can be changed at any time.' => 'Nous utilisons des cookies pour faire fonctionner le site, comprendre comment il est utilisé et l’améliorer. Vous choisissez ce que nous pouvons enregistrer — votre choix peut être modifié à tout moment.',
			'Privacy Policy'        => 'Politique de confidentialité',
			'Cookie Policy'         => 'Politique relative aux cookies',
			'Strictly necessary'    => 'Strictement nécessaires',
			'Required for the site, its forms and the security check to work. Always on.' => 'Nécessaires au fonctionnement du site, de ses formulaires et du contrôle de sécurité. Toujours actifs.',
			'Analytics'             => 'Mesure d’audience',
			'Anonymous statistics about which pages are read and where visitors struggle, so we can improve them.' => 'Des statistiques anonymes sur les pages consultées et les difficultés rencontrées, afin de les améliorer.',
			'Marketing'             => 'Marketing',
			'Lets us measure our advertising and show you more relevant information.' => 'Nous permet de mesurer notre publicité et de vous montrer des informations plus pertinentes.',
			'Accept all'            => 'Tout accepter',
			'Reject all'            => 'Tout refuser',
			'Customise'             => 'Personnaliser',
			'Save my choices'       => 'Enregistrer mes choix',
			'Cookie settings'       => 'Paramètres des cookies',
		),
		'it' => array(
			'We value your privacy' => 'Teniamo alla tua privacy',
			'We use cookies to keep the site working, to understand how it is used and to improve it. You choose what we may store — your choice can be changed at any time.' => 'Utilizziamo i cookie per far funzionare il sito, capire come viene utilizzato e migliorarlo. Sei tu a scegliere cosa possiamo memorizzare — puoi modificare la tua scelta in qualsiasi momento.',
			'Privacy Policy'        => 'Informativa sulla privacy',
			'Cookie Policy'         => 'Informativa sui cookie',
			'Strictly necessary'    => 'Strettamente necessari',
			'Required for the site, its forms and the security check to work. Always on.' => 'Necessari al funzionamento del sito, dei suoi moduli e del controllo di sicurezza. Sempre attivi.',
			'Analytics'             => 'Statistiche',
			'Anonymous statistics about which pages are read and where visitors struggle, so we can improve them.' => 'Statistiche anonime su quali pagine vengono lette e dove i visitatori incontrano difficoltà, per poterle migliorare.',
			'Marketing'             => 'Marketing',
			'Lets us measure our advertising and show you more relevant information.' => 'Ci permette di misurare la nostra pubblicità e di mostrarti informazioni più pertinenti.',
			'Accept all'            => 'Accetta tutti',
			'Reject all'            => 'Rifiuta tutti',
			'Customise'             => 'Personalizza',
			'Save my choices'       => 'Salva le mie scelte',
			'Cookie settings'       => 'Impostazioni dei cookie',
		),
		'es' => array(
			'We value your privacy' => 'Nos importa tu privacidad',
			'We use cookies to keep the site working, to understand how it is used and to improve it. You choose what we may store — your choice can be changed at any time.' => 'Utilizamos cookies para que el sitio funcione, para entender cómo se usa y para mejorarlo. Tú decides qué podemos guardar: puedes cambiar tu elección en cualquier momento.',
			'Privacy Policy'        => 'Política de privacidad',
			'Cookie Policy'         => 'Política de cookies',
			'Strictly necessary'    => 'Estrictamente necesarias',
			'Required for the site, its forms and the security check to work. Always on.' => 'Necesarias para que el sitio, sus formularios y la verificación de seguridad funcionen. Siempre activas.',
			'Analytics'             => 'Analítica',
			'Anonymous statistics about which pages are read and where visitors struggle, so we can improve them.' => 'Estadísticas anónimas sobre qué páginas se leen y dónde encuentran dificultades los visitantes, para poder mejorarlas.',
			'Marketing'             => 'Marketing',
			'Lets us measure our advertising and show you more relevant information.' => 'Nos permite medir nuestra publicidad y mostrarte información más relevante.',
			'Accept all'            => 'Aceptar todas',
			'Reject all'            => 'Rechazar todas',
			'Customise'             => 'Personalizar',
			'Save my choices'       => 'Guardar mis preferencias',
			'Cookie settings'       => 'Configuración de cookies',
		),
		'pl' => array(
			'We value your privacy' => 'Szanujemy Twoją prywatność',
			'We use cookies to keep the site working, to understand how it is used and to improve it. You choose what we may store — your choice can be changed at any time.' => 'Używamy plików cookie, aby strona działała, aby rozumieć, jak jest używana, i aby ją ulepszać. To Ty decydujesz, co możemy zapisywać — swój wybór możesz zmienić w każdej chwili.',
			'Privacy Policy'        => 'Polityka prywatności',
			'Cookie Policy'         => 'Polityka plików cookie',
			'Strictly necessary'    => 'Niezbędne',
			'Required for the site, its forms and the security check to work. Always on.' => 'Wymagane do działania strony, jej formularzy i weryfikacji bezpieczeństwa. Zawsze włączone.',
			'Analytics'             => 'Analityka',
			'Anonymous statistics about which pages are read and where visitors struggle, so we can improve them.' => 'Anonimowe statystyki dotyczące tego, które strony są czytane i gdzie odwiedzający napotykają trudności, abyśmy mogli je ulepszyć.',
			'Marketing'             => 'Marketing',
			'Lets us measure our advertising and show you more relevant information.' => 'Pozwala nam mierzyć skuteczność reklam i pokazywać Ci bardziej trafne informacje.',
			'Accept all'            => 'Zaakceptuj wszystkie',
			'Reject all'            => 'Odrzuć wszystkie',
			'Customise'             => 'Dostosuj',
			'Save my choices'       => 'Zapisz moje wybory',
			'Cookie settings'       => 'Ustawienia plików cookie',
		),
		'pt' => array(
			'We value your privacy' => 'A sua privacidade é importante',
			'We use cookies to keep the site working, to understand how it is used and to improve it. You choose what we may store — your choice can be changed at any time.' => 'Utilizamos cookies para que o site funcione, para percebermos como é utilizado e para o melhorarmos. É você que decide o que podemos guardar — pode alterar a sua escolha a qualquer momento.',
			'Privacy Policy'        => 'Política de Privacidade',
			'Cookie Policy'         => 'Política de Cookies',
			'Strictly necessary'    => 'Estritamente necessários',
			'Required for the site, its forms and the security check to work. Always on.' => 'Necessários para o funcionamento do site, dos seus formulários e da verificação de segurança. Sempre ativos.',
			'Analytics'             => 'Estatísticas',
			'Anonymous statistics about which pages are read and where visitors struggle, so we can improve them.' => 'Estatísticas anónimas sobre as páginas lidas e sobre onde os visitantes têm dificuldades, para as podermos melhorar.',
			'Marketing'             => 'Marketing',
			'Lets us measure our advertising and show you more relevant information.' => 'Permite-nos medir a nossa publicidade e mostrar-lhe informação mais relevante.',
			'Accept all'            => 'Aceitar tudo',
			'Reject all'            => 'Rejeitar tudo',
			'Customise'             => 'Personalizar',
			'Save my choices'       => 'Guardar as minhas escolhas',
			'Cookie settings'       => 'Definições de cookies',
		),
	);
}

add_filter( 'gettext', 'estecapelli_consent_gettext_fallback', 20, 3 );
/**
 * Supply banner copy only when nothing else has translated the string.
 *
 * The URL is read before WPML is asked, for the reason documented on
 * estecapelli_request_language_code(): on a request WPML cannot resolve it
 * answers with the default language, which would put an English banner on a
 * page the visitor is reading in Portuguese.
 *
 * @param string $translation Current translated value.
 * @param string $text        English source value.
 * @param string $domain      Text domain.
 * @return string
 */
function estecapelli_consent_gettext_fallback( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || $translation !== $text ) {
		return $translation;
	}

	$language = estecapelli_request_language_code();
	if ( ! $language ) {
		$language = estecapelli_indexed_language_code();
	}
	if ( 'en' === $language ) {
		return $translation;
	}

	$strings = estecapelli_consent_strings();
	return $strings[ $language ][ $text ] ?? $translation;
}
