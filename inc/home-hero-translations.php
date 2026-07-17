<?php
/**
 * Version-controlled translations for the homepage hero carousel.
 *
 * Homepage media and video settings are shared through an ACF Options page.
 * Text saved there may still be English, so hero payloads are translated after
 * the ACF overlay. The gettext fallback also covers strings rendered directly
 * by template-parts/hero-home.php.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return the active public language code used by the indexed URL contract. */
function estecapelli_home_hero_language() {
	$language = (string) apply_filters( 'wpml_current_language', null );
	if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
		$language = estecapelli_indexed_language_code( $language );
	}

	if ( ! $language && function_exists( 'determine_locale' ) ) {
		$language = strtolower( substr( (string) determine_locale(), 0, 2 ) );
	}

	return $language;
}

/**
 * Hero strings missing from the broader French, Polish and Turkish fallbacks.
 * Italian, Spanish and Portuguese already own complete hero dictionaries.
 *
 * @return array<string,array<string,string>>
 */
function estecapelli_home_hero_fallback_strings() {
	static $strings = null;
	if ( null !== $strings ) {
		return $strings;
	}

	$strings = array(
		'tr' => array(
			'Highlights' => 'Öne Çıkanlar',
			'Signature Methods' => 'Bize Özel Yöntemler',
			'OUR SIGNATURE METHODS' => 'BİZE ÖZEL YÖNTEMLER',
			'Two techniques. Registered to our name.' => 'Adımıza tescilli iki teknik.',
			'Two exclusive, trademarked protocols — developed in-house.' => 'Kendi bünyemizde geliştirdiğimiz, tescilli iki özel protokol.',
			'Select a method to play' => 'Oynatmak için bir yöntem seçin',
			'Treatment' => 'Tedavi',
			'Explore VITA®' => 'VITA®’yı Keşfedin',
			'Explore Exosome®' => 'Exosome®’u Keşfedin',
			'Close video' => 'Videoyu kapat',
			'Play %s video' => '%s videosunu oynat',
			'World-Class Experience' => 'Dünya Standartlarında Deneyim',
			'EST. 2010 · ISTANBUL' => '2010’DAN BERİ · İSTANBUL',
			'One of the World’s Most Experienced Hair Restoration Teams' => 'Dünyanın En Deneyimli Saç Restorasyonu Ekiplerinden Biri',
			'Join the thousands who have come to Estecapelli to regain their confidence.' => 'Özgüvenini yeniden kazanmak için Estecapelli’yi tercih eden binlerce kişiye katılın.',
			'Schedule a Free Consultation' => 'Ücretsiz Konsültasyon Planlayın',
			'Over 50,000+' => '50.000’den Fazla',
			'5-Star Results' => '5 Yıldızlı Sonuçlar',
			'Actual Estecapelli patient. Individual results may vary.' => 'Gerçek Estecapelli hastası. Sonuçlar kişiden kişiye değişebilir.',
			'FIFTEEN YEARS' => 'ON BEŞ YIL',
			'Estecapelli patient result' => 'Estecapelli hasta sonucu',
			'Previous patient' => 'Önceki hasta',
			'Before' => 'Öncesi',
			'Next patient' => 'Sonraki hasta',
			'Estecapelli Dental' => 'Estecapelli Dental',
			'Estecapelli dental patient smiling' => 'Gülümseyen Estecapelli diş hastası',
			'A Smile Designed Around You' => 'Size Özel Tasarlanmış Bir Gülüş',
			'Hollywood smiles, veneers and full-mouth restorations — crafted by our specialists for a natural, confident result that lasts.' => 'Hollywood Gülüşü, lamina veneer ve tam ağız restorasyonları — uzmanlarımız tarafından doğal, özgüvenli ve kalıcı bir sonuç için planlanır.',
			'Hollywood Smile & porcelain veneers' => 'Hollywood Gülüşü ve porselen lamina veneerler',
			'Implants & full-mouth restoration' => 'İmplantlar ve tam ağız restorasyonu',
			'One trusted clinic, all-inclusive care' => 'Güvenilir tek klinik, her şey dahil bakım',
			'Explore Dental Treatments' => 'Diş Tedavilerini Keşfedin',
			'Previous slide' => 'Önceki slayt',
			'Next slide' => 'Sonraki slayt',
			'Choose slide' => 'Slayt seçin',
			'Slide %d' => 'Slayt %d',
			'FOR WOMEN · DISCREET & NATURAL' => 'KADINLAR İÇİN · GİZLİLİK ODAKLI VE DOĞAL',
			'Women’s Hair Transplant in Istanbul' => 'İstanbul’da Kadın Saç Ekimi',
			'Thinning along the parting, a receding hairline or overall loss of density affects women too. Our female-focused approach restores fullness with unshaven techniques, complete privacy and natural, permanent results.' => 'Saç ayrımında seyrelme, gerileyen saç çizgisi veya genel yoğunluk kaybı kadınları da etkiler. Kadınlara özel yaklaşımımız, tıraşsız teknikler, tam mahremiyet ve doğal, kalıcı sonuçlarla yoğunluğu geri kazandırır.',
			'Unshaven, discreet techniques' => 'Tıraşsız ve gizlilik odaklı teknikler',
			'Natural hairline & density design' => 'Doğal saç çizgisi ve yoğunluk tasarımı',
			'Female patient privacy at every step' => 'Her aşamada kadın hastalara özel mahremiyet',
			'Free Consultation' => 'Ücretsiz Konsültasyon',
		),
		'fr' => array(
			'Highlights' => 'À la une',
			'Signature Methods' => 'Méthodes exclusives',
			'OUR SIGNATURE METHODS' => 'NOS MÉTHODES EXCLUSIVES',
			'Two techniques. Registered to our name.' => 'Deux techniques déposées à notre nom.',
			'Two exclusive, trademarked protocols — developed in-house.' => 'Deux protocoles exclusifs et déposés, développés en interne.',
			'Select a method to play' => 'Sélectionnez une méthode pour lancer la vidéo',
			'Treatment' => 'Traitement',
			'Explore VITA®' => 'Découvrir VITA®',
			'Explore Exosome®' => 'Découvrir Exosome®',
			'Close video' => 'Fermer la vidéo',
			'Play %s video' => 'Lire la vidéo %s',
			'World-Class Experience' => 'Une expérience de classe mondiale',
			'EST. 2010 · ISTANBUL' => 'DEPUIS 2010 · ISTANBUL',
			'One of the World’s Most Experienced Hair Restoration Teams' => 'L’une des équipes de restauration capillaire les plus expérimentées au monde',
			'Join the thousands who have come to Estecapelli to regain their confidence.' => 'Rejoignez les milliers de patients qui ont choisi Estecapelli pour retrouver confiance en eux.',
			'Schedule a Free Consultation' => 'Planifier une consultation gratuite',
			'Over 50,000+' => 'Plus de 50 000',
			'5-Star Results' => 'Résultats 5 étoiles',
			'Actual Estecapelli patient. Individual results may vary.' => 'Patient réel Estecapelli. Les résultats peuvent varier d’une personne à l’autre.',
			'FIFTEEN YEARS' => 'QUINZE ANS',
			'Estecapelli patient result' => 'Résultat d’un patient Estecapelli',
			'Previous patient' => 'Patient précédent',
			'Before' => 'Avant',
			'Next patient' => 'Patient suivant',
			'Estecapelli Dental' => 'Estecapelli Dental',
			'Estecapelli dental patient smiling' => 'Patient dentaire Estecapelli souriant',
			'A Smile Designed Around You' => 'Un sourire conçu pour vous',
			'Hollywood smiles, veneers and full-mouth restorations — crafted by our specialists for a natural, confident result that lasts.' => 'Hollywood Smile, facettes et restaurations complètes — réalisés par nos spécialistes pour un résultat naturel, harmonieux et durable.',
			'Hollywood Smile & porcelain veneers' => 'Hollywood Smile et facettes en porcelaine',
			'Implants & full-mouth restoration' => 'Implants et restauration complète',
			'One trusted clinic, all-inclusive care' => 'Une clinique de confiance, prise en charge tout compris',
			'Explore Dental Treatments' => 'Découvrir les soins dentaires',
			'Previous slide' => 'Diapositive précédente',
			'Next slide' => 'Diapositive suivante',
			'Choose slide' => 'Choisir une diapositive',
			'Slide %d' => 'Diapositive %d',
			'FOR WOMEN · DISCREET & NATURAL' => 'POUR LES FEMMES · DISCRET ET NATUREL',
			'Women’s Hair Transplant in Istanbul' => 'Greffe de cheveux pour femmes à Istanbul',
			'Thinning along the parting, a receding hairline or overall loss of density affects women too. Our female-focused approach restores fullness with unshaven techniques, complete privacy and natural, permanent results.' => 'L’éclaircissement de la raie, le recul de la ligne capillaire ou la perte globale de densité concernent également les femmes. Notre approche dédiée restaure la densité grâce à des techniques sans rasage, dans le respect total de votre intimité, avec des résultats naturels et durables.',
			'Unshaven, discreet techniques' => 'Techniques discrètes sans rasage',
			'Natural hairline & density design' => 'Ligne capillaire et densité naturelles',
			'Female patient privacy at every step' => 'Intimité des patientes respectée à chaque étape',
			'Free Consultation' => 'Consultation gratuite',
		),
		'pl' => array(
			'Highlights' => 'Najważniejsze',
			'Signature Methods' => 'Autorskie metody',
			'OUR SIGNATURE METHODS' => 'NASZE AUTORSKIE METODY',
			'Two techniques. Registered to our name.' => 'Dwie techniki zarejestrowane pod naszą marką.',
			'Two exclusive, trademarked protocols — developed in-house.' => 'Dwa ekskluzywne, zastrzeżone protokoły opracowane przez nasz zespół.',
			'Select a method to play' => 'Wybierz metodę, aby odtworzyć film',
			'Treatment' => 'Zabieg',
			'Explore VITA®' => 'Poznaj VITA®',
			'Explore Exosome®' => 'Poznaj Exosome®',
			'Close video' => 'Zamknij film',
			'Play %s video' => 'Odtwórz film: %s',
			'World-Class Experience' => 'Doświadczenie na światowym poziomie',
			'EST. 2010 · ISTANBUL' => 'OD 2010 · STAMBUŁ',
			'One of the World’s Most Experienced Hair Restoration Teams' => 'Jeden z najbardziej doświadczonych zespołów odbudowy włosów na świecie',
			'Join the thousands who have come to Estecapelli to regain their confidence.' => 'Dołącz do tysięcy pacjentów, którzy wybrali Estecapelli, aby odzyskać pewność siebie.',
			'Schedule a Free Consultation' => 'Umów bezpłatną konsultację',
			'Over 50,000+' => 'Ponad 50 000',
			'5-Star Results' => 'Rezultaty ocenione na 5 gwiazdek',
			'Actual Estecapelli patient. Individual results may vary.' => 'Rzeczywisty pacjent Estecapelli. Rezultaty mogą się różnić w zależności od osoby.',
			'FIFTEEN YEARS' => 'PIĘTNAŚCIE LAT',
			'Estecapelli patient result' => 'Rezultat leczenia pacjenta Estecapelli',
			'Previous patient' => 'Poprzedni pacjent',
			'Before' => 'Przed',
			'Next patient' => 'Następny pacjent',
			'Estecapelli Dental' => 'Estecapelli Dental',
			'Estecapelli dental patient smiling' => 'Uśmiechnięty pacjent Estecapelli Dental',
			'A Smile Designed Around You' => 'Uśmiech zaprojektowany dla Ciebie',
			'Hollywood smiles, veneers and full-mouth restorations — crafted by our specialists for a natural, confident result that lasts.' => 'Hollywood Smile, licówki i pełna odbudowa uzębienia — wykonywane przez naszych specjalistów z myślą o naturalnym, pewnym i trwałym rezultacie.',
			'Hollywood Smile & porcelain veneers' => 'Hollywood Smile i licówki porcelanowe',
			'Implants & full-mouth restoration' => 'Implanty i pełna odbudowa uzębienia',
			'One trusted clinic, all-inclusive care' => 'Jedna zaufana klinika, kompleksowa opieka',
			'Explore Dental Treatments' => 'Poznaj zabiegi stomatologiczne',
			'Previous slide' => 'Poprzedni slajd',
			'Next slide' => 'Następny slajd',
			'Choose slide' => 'Wybierz slajd',
			'Slide %d' => 'Slajd %d',
			'FOR WOMEN · DISCREET & NATURAL' => 'DLA KOBIET · DYSKRETNIE I NATURALNIE',
			'Women’s Hair Transplant in Istanbul' => 'Przeszczep włosów dla kobiet w Stambule',
			'Thinning along the parting, a receding hairline or overall loss of density affects women too. Our female-focused approach restores fullness with unshaven techniques, complete privacy and natural, permanent results.' => 'Przerzedzenie wzdłuż przedziałka, cofająca się linia włosów lub ogólna utrata gęstości dotyczy również kobiet. Nasze podejście przywraca gęstość za pomocą technik bez golenia, z pełnym poszanowaniem prywatności oraz naturalnymi i trwałymi rezultatami.',
			'Unshaven, discreet techniques' => 'Dyskretne techniki bez golenia',
			'Natural hairline & density design' => 'Naturalna linia włosów i projekt gęstości',
			'Female patient privacy at every step' => 'Prywatność pacjentek na każdym etapie',
			'Free Consultation' => 'Bezpłatna konsultacja',
		),
	);

	return $strings;
}

add_filter( 'gettext', 'estecapelli_home_hero_gettext_fallback', 40, 3 );
/** Supply hero translations only when no earlier translation provider did. */
function estecapelli_home_hero_gettext_fallback( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || $translation !== $text ) {
		return $translation;
	}

	$language = estecapelli_home_hero_language();
	$strings  = estecapelli_home_hero_fallback_strings();

	return $strings[ $language ][ $text ] ?? $translation;
}

/**
 * Re-run gettext over a hero payload after English ACF option values overlay it.
 * Shared URLs, media paths, video IDs and numeric data pass through unchanged.
 *
 * @param mixed $value Hero payload or nested value.
 * @return mixed
 */
function estecapelli_localize_home_hero_payload( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = estecapelli_localize_home_hero_payload( $item );
		}
		return $value;
	}

	if ( is_string( $value ) ) {
		return translate( $value, 'estecapelli' );
	}

	return $value;
}
