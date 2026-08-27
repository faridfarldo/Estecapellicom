<?php
/**
 * Per-language title + SEO metadata for every blog article.
 *
 * Keyed by the English source slug, then by indexed language code. Each entry
 * carries the localized title, a Rank Math meta description (~150 chars) and the
 * focus keyphrase. Slugs are NOT stored here — they come from the frozen
 * contract in estecapelli_indexed_blog_slugs() so nothing is invented twice.
 *
 * The live pages ship EMPTY meta descriptions, so these are authored fresh.
 * A handful of live <h1> titles were cross-language copy errors (Italian title
 * on the Spanish diabetic page, Spanish title on the Polish HIV-in-Turkey page,
 * French title on the Italian unshaven page); those are corrected here.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string,array<string,array{title:string,description:string,focus_keyword:string}>>
 */
function estecapelli_blog_i18n_meta() {
	return array(

		'hair-transplant-turkey-complete-expert-guide' => array(
			'en' => array(
				'title'         => 'Hair Transplant Turkey : Complete Expert Guide',
				'description'   => 'The complete expert guide to hair transplant in Turkey: techniques, graft counts, recovery, results and how to choose the right clinic in Istanbul.',
				'focus_keyword' => 'hair transplant Turkey',
			),
			'it' => array(
				'title'         => 'Trapianto Capelli in Turchia: La Guida Completa',
				'description'   => 'La guida completa al trapianto di capelli in Turchia: tecniche, numero di graft, recupero, risultati e come scegliere la clinica giusta a Istanbul.',
				'focus_keyword' => 'trapianto capelli Turchia',
			),
		),

		'unshaven-hair-transplant-for-women' => array(
			'en' => array(
				'title'         => 'Unshaven Hair Transplant for Women',
				'description'   => 'Unshaven hair transplant for women lets you restore density without cutting your hair. Discover how the no-shave method works and who it suits.',
				'focus_keyword' => 'unshaven hair transplant for women',
			),
			'tr' => array(
				'title'         => 'Kadınlarda Traşsız Saç Ekimi',
				'description'   => 'Kadınlarda traşsız saç ekimi, saçlarınızı kesmeden sıklık kazandırır. Tıraşsız yöntemin nasıl uygulandığını ve kimlere uygun olduğunu keşfedin.',
				'focus_keyword' => 'kadınlarda traşsız saç ekimi',
			),
			'fr' => array(
				'title'         => 'Greffe de cheveux sans rasage chez la femme',
				'description'   => 'La greffe de cheveux sans rasage chez la femme redonne de la densité sans couper les cheveux. Découvrez comment fonctionne cette méthode et à qui elle convient.',
				'focus_keyword' => 'greffe de cheveux sans rasage femme',
			),
			'it' => array(
				'title'         => 'Trapianto di Capelli Senza Rasatura nelle Donne',
				'description'   => 'Il trapianto di capelli senza rasatura nelle donne ridona densità senza tagliare i capelli. Scopri come funziona il metodo no-shave e a chi è adatto.',
				'focus_keyword' => 'trapianto capelli senza rasatura donne',
			),
			'es' => array(
				'title'         => 'Trasplante Capilar Sin Rasurado en Mujeres',
				'description'   => 'El trasplante capilar sin rasurado en mujeres devuelve densidad sin cortar el cabello. Descubre cómo funciona el método sin afeitar y para quién es ideal.',
				'focus_keyword' => 'trasplante capilar sin rasurar mujeres',
			),
			'pl' => array(
				'title'         => 'Przeszczep Włosów Bez Golenia u Kobiet',
				'description'   => 'Przeszczep włosów bez golenia u kobiet przywraca gęstość bez ścinania włosów. Dowiedz się, jak działa metoda bez golenia i dla kogo jest przeznaczona.',
				'focus_keyword' => 'przeszczep włosów bez golenia u kobiet',
			),
			'pt' => array(
				'title'         => 'Transplante Capilar Sem Raspagem em Mulheres',
				'description'   => 'O transplante capilar sem raspagem em mulheres devolve densidade sem cortar o cabelo. Descubra como funciona o método sem raspar e para quem é indicado.',
				'focus_keyword' => 'transplante capilar sem raspagem mulheres',
			),
			'ro' => array(
				'title'         => 'Transplant de Păr Fără Bărbierire la Femei',
				'description'   => 'Transplantul de păr fără bărbierire la femei redă densitatea fără să îți tai părul. Află cum funcționează metoda fără ras și cui i se potrivește.',
				'focus_keyword' => 'transplant de păr fără bărbierire femei',
			),
		),

		'unshaven-hair-transplant' => array(
			'en' => array(
				'title'         => 'Unshaven Hair Transplant',
				'description'   => 'An unshaven hair transplant restores your hairline without shaving your head. Learn how the no-shave FUE method works, its benefits and its limits.',
				'focus_keyword' => 'unshaven hair transplant',
			),
			'tr' => array(
				'title'         => 'Tıraşsız Saç Ekimi',
				'description'   => 'Tıraşsız saç ekimi, kafanızı kazımadan saç çizginizi yeniden oluşturur. Tıraşsız FUE yönteminin nasıl çalıştığını, avantajlarını ve sınırlarını öğrenin.',
				'focus_keyword' => 'tıraşsız saç ekimi',
			),
			'fr' => array(
				'title'         => 'Greffe de cheveux sans rasage',
				'description'   => 'La greffe de cheveux sans rasage restaure votre ligne capillaire sans raser la tête. Découvrez comment fonctionne la méthode FUE sans rasage et ses limites.',
				'focus_keyword' => 'greffe de cheveux sans rasage',
			),
			'it' => array(
				'title'         => 'Trapianto di Capelli Senza Rasatura',
				'description'   => 'Il trapianto di capelli senza rasatura ricostruisce la linea frontale senza rasare la testa. Scopri come funziona la FUE senza rasatura e i suoi limiti.',
				'focus_keyword' => 'trapianto capelli senza rasatura',
			),
			'es' => array(
				'title'         => 'Trasplante de Cabello Sin Afeitar',
				'description'   => 'El trasplante de cabello sin afeitar reconstruye tu línea capilar sin rasurar la cabeza. Descubre cómo funciona la FUE sin rasurado y cuáles son sus límites.',
				'focus_keyword' => 'trasplante de cabello sin afeitar',
			),
			'pl' => array(
				'title'         => 'Przeszczep Włosów Bez Golenia',
				'description'   => 'Przeszczep włosów bez golenia odtwarza linię włosów bez golenia głowy. Poznaj zasadę działania metody FUE bez golenia, jej zalety i ograniczenia.',
				'focus_keyword' => 'przeszczep włosów bez golenia',
			),
			'pt' => array(
				'title'         => 'Transplante Capilar Sem Barbear',
				'description'   => 'O transplante capilar sem barbear recompõe a sua linha capilar sem raspar a cabeça. Saiba como funciona o método FUE sem raspagem e quais são os seus limites.',
				'focus_keyword' => 'transplante capilar sem barbear',
			),
			'ro' => array(
				'title'         => 'Transplant de Păr Fără Bărbierire',
				'description'   => 'Transplantul de păr fără bărbierire îți reface linia frontală fără să îți razi capul. Află cum funcționează metoda FUE fără ras și care sunt limitele ei.',
				'focus_keyword' => 'transplant de păr fără bărbierire',
			),
		),

		'can-diabetic-patients-undergo-a-hair-transplant' => array(
			'en' => array(
				'title'         => 'Can Diabetic Patients Undergo a Hair Transplant?',
				'description'   => 'Can diabetic patients have a hair transplant? Yes, with controlled blood sugar and the right clinic. Learn the conditions, risks and precautions.',
				'focus_keyword' => 'hair transplant for diabetics',
			),
			'tr' => array(
				'title'         => 'Diyabet Hastaları Saç Ekimi Yaptırabilir Mi?',
				'description'   => 'Diyabet hastaları saç ekimi yaptırabilir mi? Kan şekeri kontrol altındaysa evet. Koşulları, riskleri ve alınması gereken önlemleri öğrenin.',
				'focus_keyword' => 'diyabet hastaları saç ekimi',
			),
			'fr' => array(
				'title'         => 'Les patients diabétiques peuvent-ils subir une greffe de cheveux ?',
				'description'   => 'Un patient diabétique peut-il subir une greffe de cheveux ? Oui, avec une glycémie contrôlée. Découvrez les conditions, les risques et les précautions.',
				'focus_keyword' => 'greffe de cheveux et diabète',
			),
			'it' => array(
				'title'         => 'I Pazienti Diabetici Possono Sottoporsi a un Trapianto di Capelli?',
				'description'   => 'I pazienti diabetici possono sottoporsi a un trapianto di capelli? Sì, con la glicemia controllata. Scopri condizioni, rischi e precauzioni.',
				'focus_keyword' => 'trapianto capelli diabetici',
			),
			'es' => array(
				'title'         => '¿Los Pacientes Diabéticos Pueden Someterse a un Trasplante Capilar?',
				'description'   => '¿Los pacientes diabéticos pueden hacerse un trasplante capilar? Sí, con el azúcar controlado. Conoce las condiciones, los riesgos y las precauciones.',
				'focus_keyword' => 'trasplante capilar diabéticos',
			),
			'pl' => array(
				'title'         => 'Czy Pacjenci z Cukrzycą Mogą Poddać się Przeszczepowi Włosów?',
				'description'   => 'Czy pacjenci z cukrzycą mogą poddać się przeszczepowi włosów? Tak, przy kontrolowanym poziomie cukru. Poznaj warunki, ryzyko i środki ostrożności.',
				'focus_keyword' => 'przeszczep włosów a cukrzyca',
			),
			'pt' => array(
				'title'         => 'Pacientes com Diabetes Podem Realizar Transplante Capilar?',
				'description'   => 'Pacientes com diabetes podem fazer transplante capilar? Sim, com a glicemia controlada. Conheça as condições, os riscos e os cuidados necessários.',
				'focus_keyword' => 'transplante capilar diabéticos',
			),
			'ro' => array(
				'title'         => 'Pot Pacienții Diabetici Să Facă Transplant de Păr?',
				'description'   => 'Pot pacienții diabetici să facă transplant de păr? Da, cu glicemia sub control și clinica potrivită. Află condițiile, riscurile și măsurile de precauție.',
				'focus_keyword' => 'transplant de păr diabetici',
			),
		),

		'is-hair-transplant-a-painful-procedure' => array(
			'en' => array(
				'title'         => 'Is Hair Transplant a Painful Procedure?',
				'description'   => 'Is a hair transplant painful? Thanks to modern local anesthesia the procedure is nearly painless. Learn what to expect during and after surgery.',
				'focus_keyword' => 'is hair transplant painful',
			),
			'tr' => array(
				'title'         => 'Saç Ekimi Ağrılı Bir İşlem Midir?',
				'description'   => 'Saç ekimi ağrılı mıdır? Modern lokal anestezi sayesinde işlem neredeyse ağrısızdır. Operasyon sırasında ve sonrasında neler beklediğinizi öğrenin.',
				'focus_keyword' => 'saç ekimi ağrılı mı',
			),
			'fr' => array(
				'title'         => 'La greffe de cheveux est-elle une procédure douloureuse ?',
				'description'   => 'La greffe de cheveux est-elle douloureuse ? Grâce à l\'anesthésie locale moderne, l\'intervention est presque indolore. Découvrez ce qui vous attend.',
				'focus_keyword' => 'greffe de cheveux douleur',
			),
			'it' => array(
				'title'         => 'Il Trapianto di Capelli È una Procedura Dolorosa?',
				'description'   => 'Il trapianto di capelli è doloroso? Grazie all\'anestesia locale moderna l\'intervento è quasi indolore. Scopri cosa aspettarti durante e dopo.',
				'focus_keyword' => 'trapianto capelli doloroso',
			),
			'es' => array(
				'title'         => '¿Es Doloroso el Trasplante Capilar?',
				'description'   => '¿Es doloroso el trasplante capilar? Gracias a la anestesia local moderna es casi indoloro. Descubre qué esperar durante y después de la cirugía.',
				'focus_keyword' => 'trasplante capilar dolor',
			),
			'pl' => array(
				'title'         => 'Czy Przeszczep Włosów Jest Bolesnym Zabiegiem?',
				'description'   => 'Czy przeszczep włosów boli? Dzięki nowoczesnemu znieczuleniu miejscowemu zabieg jest niemal bezbolesny. Dowiedz się, czego się spodziewać.',
				'focus_keyword' => 'czy przeszczep włosów boli',
			),
			'pt' => array(
				'title'         => 'O Transplante Capilar É um Procedimento Doloroso?',
				'description'   => 'O transplante capilar dói? Graças à anestesia local moderna, o procedimento é quase indolor. Saiba o que esperar durante e após a cirurgia.',
				'focus_keyword' => 'transplante capilar dói',
			),
			'ro' => array(
				'title'         => 'Este Transplantul de Păr o Procedură Dureroasă?',
				'description'   => 'Doare transplantul de păr? Datorită anesteziei locale moderne, procedura este aproape nedureroasă. Află la ce să te aștepți în timpul și după operație.',
				'focus_keyword' => 'doare transplantul de păr',
			),
		),

		'will-my-hair-fall-out-again-after-a-hair-transplant' => array(
			'en' => array(
				'title'         => 'Will My Hair Fall Out Again After a Hair Transplant?',
				'description'   => 'Will transplanted hair fall out again? After shock loss, hair from the donor area is permanent and genetically resistant to loss. Here is why.',
				'focus_keyword' => 'will transplanted hair fall out',
			),
			'tr' => array(
				'title'         => 'Saç Ekimi Sonrasında Saçlarım Tekrar Dökülür Mü?',
				'description'   => 'Ekilen saçlar tekrar dökülür mü? Şok dökülmeden sonra donör bölgeden alınan saçlar kalıcıdır ve dökülmeye genetik olarak dirençlidir. Nedenini öğrenin.',
				'focus_keyword' => 'ekilen saçlar dökülür mü',
			),
			'fr' => array(
				'title'         => 'Mes cheveux vont-ils retomber après une greffe de cheveux ?',
				'description'   => 'Les cheveux greffés retombent-ils ? Après la chute de choc, les cheveux du site donneur sont permanents et génétiquement résistants à la chute.',
				'focus_keyword' => 'cheveux greffés retombent',
			),
			'it' => array(
				'title'         => 'I Miei Capelli Ricadranno Dopo un Trapianto di Capelli?',
				'description'   => 'I capelli trapiantati ricadono? Dopo la caduta da shock i capelli della zona donatrice sono permanenti e geneticamente resistenti alla caduta.',
				'focus_keyword' => 'capelli trapiantati ricadono',
			),
			'es' => array(
				'title'         => '¿Se Volverá a Caer Mi Cabello Después de un Trasplante Capilar?',
				'description'   => '¿El cabello trasplantado se vuelve a caer? Tras la caída de shock, el pelo de la zona donante es permanente y resistente a la caída. Aquí te explicamos.',
				'focus_keyword' => 'cabello trasplantado se cae',
			),
			'pl' => array(
				'title'         => 'Czy Moje Włosy Wypadną Ponownie po Przeszczepie Włosów?',
				'description'   => 'Czy przeszczepione włosy znów wypadną? Po utracie szokowej włosy z obszaru dawczego są trwałe i genetycznie odporne na wypadanie. Wyjaśniamy dlaczego.',
				'focus_keyword' => 'przeszczepione włosy wypadają',
			),
			'pt' => array(
				'title'         => 'Meu Cabelo Vai Cair Novamente Após o Transplante Capilar?',
				'description'   => 'O cabelo transplantado cai de novo? Após a queda de choque, os fios da área doadora são permanentes e resistentes à queda. Saiba por quê.',
				'focus_keyword' => 'cabelo transplantado cai',
			),
			'ro' => array(
				'title'         => 'Îmi Va Cădea Părul Din Nou După Transplantul de Păr?',
				'description'   => 'Cade din nou părul transplantat? După căderea de șoc, firele din zona donatoare sunt permanente și rezistente genetic la cădere. Iată de ce.',
				'focus_keyword' => 'cade părul transplantat',
			),
		),

		'hair-transplant-with-the-fue-vita-technique' => array(
			'en' => array(
				'title'         => 'Hair Transplant with the FUE Vita Technique',
				'description'   => 'FUE Vita is an advanced FUE hair transplant enriched with vitamins, minerals and amino acids that nourish grafts for stronger, more reliable results.',
				'focus_keyword' => 'FUE Vita hair transplant',
			),
			'tr' => array(
				'title'         => 'FUE Vita Tekniği ile Saç Ekimi',
				'description'   => 'FUE Vita, greftleri besleyen vitamin, mineral ve amino asitlerle zenginleştirilmiş gelişmiş bir FUE saç ekimidir. Daha güçlü ve güvenilir sonuçlar sağlar.',
				'focus_keyword' => 'FUE Vita saç ekimi',
			),
			'fr' => array(
				'title'         => 'Greffe de cheveux avec la technique FUE Vita',
				'description'   => 'La FUE Vita est une greffe FUE avancée enrichie en vitamines, minéraux et acides aminés qui nourrissent les greffons pour des résultats plus solides.',
				'focus_keyword' => 'technique FUE Vita',
			),
			'it' => array(
				'title'         => 'Trapianto di Capelli con la Tecnica FUE Vita',
				'description'   => 'La FUE Vita è un trapianto FUE avanzato arricchito con vitamine, minerali e amminoacidi che nutrono i graft per risultati più forti e affidabili.',
				'focus_keyword' => 'tecnica FUE Vita',
			),
			'es' => array(
				'title'         => 'Trasplante Capilar con la Técnica FUE Vita',
				'description'   => 'La FUE Vita es un trasplante FUE avanzado enriquecido con vitaminas, minerales y aminoácidos que nutren los injertos para resultados más sólidos.',
				'focus_keyword' => 'técnica FUE Vita',
			),
			'pl' => array(
				'title'         => 'Przeszczep Włosów Techniką FUE Vita',
				'description'   => 'FUE Vita to zaawansowany przeszczep FUE wzbogacony o witaminy, minerały i aminokwasy, które odżywiają grafty dla silniejszych i trwalszych efektów.',
				'focus_keyword' => 'technika FUE Vita',
			),
			'pt' => array(
				'title'         => 'Transplante Capilar com a Técnica FUE Vita',
				'description'   => 'A FUE Vita é um transplante FUE avançado enriquecido com vitaminas, minerais e aminoácidos que nutrem os enxertos para resultados mais fortes.',
				'focus_keyword' => 'técnica FUE Vita',
			),
			'ro' => array(
				'title'         => 'Transplant de Păr cu Tehnica FUE Vita',
				'description'   => 'FUE Vita este un transplant de păr FUE avansat, îmbogățit cu vitamine, minerale și aminoacizi care hrănesc grefele pentru rezultate mai puternice.',
				'focus_keyword' => 'tehnica FUE Vita',
			),
		),

		'hair-transplant-for-hiv-positive-patients-in-turkey' => array(
			'en' => array(
				'title'         => 'Hair Transplant for HIV Positive Patients in Turkey',
				'description'   => 'HIV positive patients can have a safe hair transplant in Turkey. With a strong immune system and strict protocols, natural results are achievable.',
				'focus_keyword' => 'hair transplant HIV positive Turkey',
			),
			'tr' => array(
				'title'         => 'Hıv Pozitif Hastalarına Türkiye’de Saç Ekimi',
				'description'   => 'HIV pozitif hastalar Türkiye\'de güvenle saç ekimi yaptırabilir. Güçlü bir bağışıklık sistemi ve sıkı protokollerle doğal sonuçlar elde edilebilir.',
				'focus_keyword' => 'HIV pozitif saç ekimi Türkiye',
			),
			'fr' => array(
				'title'         => 'Greffe de cheveux pour patients séropositifs en Turquie',
				'description'   => 'Les patients séropositifs peuvent bénéficier d\'une greffe de cheveux sûre en Turquie. Avec un système immunitaire fort et des protocoles stricts.',
				'focus_keyword' => 'greffe de cheveux séropositif Turquie',
			),
			'it' => array(
				'title'         => 'Trapianto di Capelli per Pazienti HIV Positivi in Turchia',
				'description'   => 'I pazienti HIV positivi possono sottoporsi a un trapianto di capelli sicuro in Turchia. Con un sistema immunitario forte e protocolli rigorosi.',
				'focus_keyword' => 'trapianto capelli HIV positivi Turchia',
			),
			'es' => array(
				'title'         => 'Trasplante Capilar para Pacientes VIH Positivos en Turquía',
				'description'   => 'Los pacientes VIH positivos pueden hacerse un trasplante capilar seguro en Turquía. Con un sistema inmune fuerte y protocolos estrictos, hay buenos resultados.',
				'focus_keyword' => 'trasplante capilar VIH positivo Turquía',
			),
			'pl' => array(
				'title'         => 'Przeszczep Włosów u Pacjentów HIV-Pozytywnych w Turcji',
				'description'   => 'Pacjenci HIV-pozytywni mogą bezpiecznie poddać się przeszczepowi włosów w Turcji. Przy silnym układzie odpornościowym i rygorystycznych protokołach.',
				'focus_keyword' => 'przeszczep włosów HIV Turcja',
			),
			'pt' => array(
				'title'         => 'Transplante Capilar para Pacientes HIV Positivos na Turquia',
				'description'   => 'Pacientes HIV positivos podem fazer um transplante capilar seguro na Turquia. Com um sistema imunológico forte e protocolos rigorosos há bons resultados.',
				'focus_keyword' => 'transplante capilar HIV positivo Turquia',
			),
			'ro' => array(
				'title'         => 'Transplant de Păr pentru Pacienții HIV Pozitivi în Turcia',
				'description'   => 'Pacienții HIV pozitivi pot face un transplant de păr în siguranță în Turcia. Cu un sistem imunitar puternic și protocoale stricte, rezultatele sunt naturale.',
				'focus_keyword' => 'transplant de păr HIV pozitiv Turcia',
			),
		),

		'hair-transplant-for-hiv-positive-patients' => array(
			'en' => array(
				'title'         => 'Hair Transplant for HIV-Positive Patients',
				'description'   => 'Hair transplant is possible for HIV-positive patients when the immune system is strong. Learn about safety, infection control and the treatment stages.',
				'focus_keyword' => 'hair transplant HIV-positive',
			),
			'tr' => array(
				'title'         => 'HIV Pozitif Hastalara Saç Ekimi',
				'description'   => 'Bağışıklık sistemi güçlü olduğunda HIV pozitif hastalara saç ekimi mümkündür. Güvenlik, enfeksiyon kontrolü ve tedavi aşamalarını öğrenin.',
				'focus_keyword' => 'HIV pozitif saç ekimi',
			),
			'fr' => array(
				'title'         => 'Greffe de cheveux pour les patients séropositifs (VIH)',
				'description'   => 'La greffe de cheveux est possible pour les patients séropositifs si le système immunitaire est solide. Sécurité, contrôle des infections et étapes.',
				'focus_keyword' => 'greffe de cheveux séropositif',
			),
			'it' => array(
				'title'         => 'Trapianto di Capelli per Pazienti Sieropositivi (HIV)',
				'description'   => 'Il trapianto di capelli è possibile per i pazienti sieropositivi con un sistema immunitario forte. Scopri sicurezza, controllo delle infezioni e fasi.',
				'focus_keyword' => 'trapianto capelli sieropositivi',
			),
			'es' => array(
				'title'         => 'Trasplante Capilar para Pacientes VIH Positivos',
				'description'   => 'El trasplante capilar es posible para pacientes VIH positivos con un sistema inmune fuerte. Conoce la seguridad, el control de infecciones y las fases.',
				'focus_keyword' => 'trasplante capilar VIH positivo',
			),
			'pl' => array(
				'title'         => 'Przeszczep Włosów u Pacjentów HIV-Pozytywnych',
				'description'   => 'Przeszczep włosów jest możliwy u pacjentów HIV-pozytywnych przy silnym układzie odpornościowym. Poznaj bezpieczeństwo, kontrolę zakażeń i etapy.',
				'focus_keyword' => 'przeszczep włosów HIV',
			),
			'pt' => array(
				'title'         => 'Transplante Capilar para Pacientes HIV Positivos',
				'description'   => 'O transplante capilar é possível para pacientes HIV positivos com sistema imunológico forte. Saiba sobre segurança, controlo de infeções e etapas.',
				'focus_keyword' => 'transplante capilar HIV positivo',
			),
			'ro' => array(
				'title'         => 'Transplant de Păr pentru Pacienții HIV Pozitivi',
				'description'   => 'Transplantul de păr este posibil pentru pacienții HIV pozitivi când sistemul imunitar este puternic. Află despre siguranță, controlul infecțiilor și etape.',
				'focus_keyword' => 'transplant de păr HIV pozitiv',
			),
		),

	);
}
