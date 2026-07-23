<?php
/**
 * Navigation copy for French, Spanish, Portuguese, Polish and Turkish.
 *
 * Italian is handled separately in inc/it-navigation.php. This file mirrors that
 * system for every other language in one place: a gettext fallback for the coded
 * navbar/mega-menu, plus route-based localization of any database-backed WP menu
 * (title + exact indexed href). WPML String Translation always wins first — the
 * values here only fill a string that is still untranslated.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Current front-end language code, normalized to the indexed contract codes.
 *
 * @return string
 */
function estecapelli_nav_current_lang() {
	$language = (string) apply_filters( 'wpml_current_language', null );
	if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
		return estecapelli_indexed_language_code( $language );
	}
	return $language;
}

/**
 * Whether visitor-facing gettext fallbacks may translate this request.
 *
 * The dictionary is for visitor-facing output only. Applying it while an
 * importer builds the English PHP seed can turn just the matching seed values
 * (for example a hero title and CTA labels) into the current admin language
 * before they are written to ACF.
 *
 * Front-end AJAX remains public output, while normal wp-admin, cron and WP-CLI
 * requests must leave source strings untouched.
 *
 * @return bool
 */
function estecapelli_is_public_translation_request() {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return false;
	}
	if ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) {
		return false;
	}
	if ( is_admin() && ( ! function_exists( 'wp_doing_ajax' ) || ! wp_doing_ajax() ) ) {
		return false;
	}

	return true;
}

/**
 * English navigation route => canonical English label.
 *
 * Used to derive per-language menu labels and to resolve legacy menu items by
 * their stored English title. Keys are the stable indexed route identifiers.
 *
 * @return array<string,string>
 */
function estecapelli_nav_route_to_label() {
	return array(
		'/en'                                                                         => 'Home',
		'/en/hair-transplant'                                                          => 'Hair Transplant',
		'/en/hair-transplant/sapphire-fue-hair-transplant'                            => 'Sapphire FUE Hair Transplant',
		'/en/hair-transplant/dhi-hair-transplant'                                     => 'DHI Hair Transplant',
		'/en/hair-transplant/exosome-fue-hair-transplant'                             => 'Exosome FUE Hair Transplant',
		'/en/hair-transplant/vita-treatment'                                          => 'VITA Treatment',
		'/en/hair-transplant/female-hair-transplant'                                  => 'Female Hair Transplant',
		'/en/hair-transplant/hair-mesotherapy'                                        => 'Hair Mesotherapy',
		'/en/hair-transplant/beard-transplant'                                        => 'Beard Transplant',
		'/en/hair-transplant/eyebrow-transplant'                                      => 'Eyebrow Transplant',
		'/en/hair-transplant/pre-hair-transplant-period'                              => 'Pre-Hair Transplant Period',
		'/en/hair-transplant/post-hair-transplant-period'                             => 'Post-Hair Transplant Period',
		'/en/hair-transplant/tricholab'                                               => 'TrichoLab',
		'/en/plastic-surgery'                                                         => 'Plastic Surgery',
		'/en/plastic-surgery/rhinoplasty'                                             => 'Rhinoplasty',
		'/en/plastic-surgery/breast-aesthetics-breast-surgery'                        => 'Breast Aesthetics',
		'/en/plastic-surgery/bbl'                                                     => 'BBL (Brazilian Butt Lift)',
		'/en/plastic-surgery/liposuction'                                             => 'Liposuction',
		'/en/plastic-surgery/face-and-neck-lift-surgery'                              => 'Face & Neck Lift Surgery',
		'/en/plastic-surgery/abdominoplasty-tummy-tuck'                               => 'Abdominoplasty (Tummy Tuck)',
		'/en/plastic-surgery/gynecomastia'                                            => 'Gynecomastia',
		'/en/plastic-surgery/obesity-surgeries-bariatric-surgery-and-gastric-balloon' => 'Obesity Surgeries (Bariatric)',
		'/en/dental-treatment'                                                         => 'Dental Treatment',
		'/en/dental-treatment/dental-implant'                                          => 'Dental Implant',
		'/en/dental-treatment/hollywood-smile'                                         => 'Hollywood Smile',
		'/en/before-after'                                                             => 'Before & After',
		'/en/about-us'                                                                 => 'About Us',
		'/en/about-us/our-doctors'                                                     => 'Our Doctors',
		'/en/about-us/our-team'                                                        => 'Our Team',
		'/en/about-us/medical-director'                                                => 'Chief Physician',
		'/en/blog'                                                                     => 'Blog',
		'/en/contact'                                                                  => 'Contact Us',
	);
}

/**
 * Legacy/custom menu item title => canonical English route.
 *
 * @return array<string,string>
 */
function estecapelli_nav_source_title_routes() {
	$routes                        = array_flip( estecapelli_nav_route_to_label() );
	$routes['Treatments']          = '/en/hair-transplant';
	$routes['About Estecapelli']   = '/en/about-us';
	$routes['Contact']             = '/en/contact';
	return $routes;
}

/**
 * Per-language navigation strings, keyed by the exact English source text so the
 * gettext filter can match them. Italian is intentionally absent (see
 * inc/it-navigation.php).
 *
 * @return array<string,array<string,string>>
 */
function estecapelli_nav_translations() {
	return array(

		// ------------------------------------------------------------------ French.
		'fr' => array(
			'Skip to content'                       => 'Aller au contenu',
			'Primary'                               => 'Navigation principale',
			'Free Consultation'                     => 'Consultation gratuite',
			'Choose language. Current language: %s'  => 'Choisir la langue. Langue actuelle : %s',
			'Toggle menu'                           => 'Ouvrir ou fermer le menu',
			'Home'                                  => 'Accueil',
			'Treatments'                            => 'Traitements',
			'Blog'                                  => 'Blog',
			'Contact'                               => 'Contact',
			'Contact Us'                            => 'Contactez-nous',
			'Chat on WhatsApp'                      => 'Discuter sur WhatsApp',
			'Chief Physician'                       => 'Médecin-chef',
			'Hair Transplant'                       => 'Greffe de cheveux',
			'Plastic Surgery'                       => 'Chirurgie plastique',
			'Dental Treatment'                      => 'Traitement dentaire',
			'Before & After'                        => 'Avant & Après',
			'About Us'                              => 'À propos de nous',
			'Sapphire FUE Hair Transplant'          => 'Greffe de cheveux FUE Saphir',
			'A natural and permanent hair transplant technique where follicles are placed via sapphire blades.' => 'Une technique de greffe naturelle et permanente où les follicules sont implantés à l’aide de lames en saphir.',
			'DHI Hair Transplant'                   => 'Greffe de cheveux DHI',
			'A modern hair transplantation method performed with a Choi pen that allows precise placement.' => 'Une méthode moderne réalisée avec un stylo Choi permettant une implantation précise.',
			'Exosome FUE Hair Transplant'           => 'Greffe de cheveux Exosome FUE',
			'Supported by cell-regenerating exosomes, it keeps hair follicles alive for longer-lasting density.' => 'Soutenue par des exosomes régénérants, elle préserve la vitalité des follicules pour une densité durable.',
			'POPULAR'                               => 'POPULAIRE',
			'VITA Treatment'                        => 'Traitement VITA',
			"Estecapelli's signature method that revitalizes the scalp and strengthens hair." => 'La méthode signature d’Estecapelli qui revitalise le cuir chevelu et renforce les cheveux.',
			'Female Hair Transplant'                => 'Greffe de cheveux pour femmes',
			'Special for women, dense and natural-looking hair transplant without shaving.' => 'Spécial pour les femmes : une greffe dense et naturelle, sans rasage.',
			'Hair Mesotherapy'                      => 'Mésothérapie capillaire',
			'A vitamin and mineral injection treatment that revitalizes hair follicles.' => 'Un traitement par injection de vitamines et de minéraux qui revitalise les follicules pileux.',
			'Beard Transplant'                      => 'Greffe de barbe',
			'Natural beard and mustache transplantation for sparse or non-existing growth.' => 'Greffe naturelle de barbe et de moustache pour une pilosité clairsemée ou absente.',
			'Eyebrow Transplant'                    => 'Greffe de sourcils',
			'Eyebrow transplantation that gives a naturally curved and full eyebrow shape.' => 'Une greffe de sourcils qui redonne une forme pleine et naturellement arquée.',
			'Hair Transplant Care & Technology'     => 'Greffe de cheveux : soins et technologie',
			'Pre-Hair Transplant Period'            => 'Période pré-greffe de cheveux',
			'The preparation and analysis process before hair transplantation.' => 'Le processus de préparation et d’analyse avant la greffe de cheveux.',
			'Post-Hair Transplant Period'           => 'Période post-greffe de cheveux',
			'The post-procedure recovery and hair care period.' => 'La période de récupération et de soins des cheveux après l’intervention.',
			'Advanced AI-powered hair analysis system that examines the hair and scalp in detail.' => 'Système avancé d’analyse capillaire par IA qui examine en détail les cheveux et le cuir chevelu.',
			'AI Analysis by Estecapelli'            => 'Analyse par IA d’Estecapelli',
			'We have trained an AI to guide every patient to the best next step — get a first, personalised hair-loss assessment in seconds.' => 'Nous avons entraîné une IA pour guider chaque patient vers la meilleure étape suivante — obtenez une première évaluation personnalisée de votre perte de cheveux en quelques secondes.',
			'Start AI Analysis'                     => 'Lancer l’analyse IA',
			'Rhinoplasty'                           => 'Rhinoplastie',
			'Nose reshaping surgery that refines proportions and function.' => 'Chirurgie de remodelage du nez qui affine les proportions et la fonction.',
			'BBL (Brazilian Butt Lift)'             => 'BBL (lifting brésilien des fesses)',
			'Natural body contouring with fat transfer to the buttocks.' => 'Remodelage naturel du corps par transfert de graisse vers les fesses.',
			'Liposuction'                           => 'Liposuccion',
			'Removes localized fat deposits to reshape the body.' => 'Élimine les dépôts de graisse localisés pour remodeler le corps.',
			'Breast Aesthetics'                     => 'Esthétique mammaire',
			'Augmentation, lift, and reduction tailored to your goals.' => 'Augmentation, lifting et réduction adaptés à vos objectifs.',
			'Abdominoplasty (Tummy Tuck)'           => 'Abdominoplastie',
			'Flattens and tightens the abdomen for a smoother profile.' => 'Aplatit et raffermit l’abdomen pour un profil plus harmonieux.',
			'Gynecomastia'                          => 'Gynécomastie',
			'Surgical treatment of enlarged male breast tissue.' => 'Traitement chirurgical de l’hypertrophie mammaire masculine.',
			'Face & Neck Lift Surgery'              => 'Lifting du visage et du cou',
			'Restores facial contour and reduces visible signs of aging.' => 'Restaure les contours du visage et réduit les signes visibles du vieillissement.',
			'Obesity Surgeries (Bariatric)'         => 'Chirurgie de l’obésité',
			'Bariatric surgery and gastric balloon for sustainable weight loss.' => 'Chirurgie bariatrique et ballon gastrique pour une perte de poids durable.',
			'Dental Implant'                        => 'Implant dentaire',
			'Permanent replacement for missing teeth with titanium roots.' => 'Remplacement permanent des dents manquantes par des racines en titane.',
			'Hollywood Smile'                       => 'Sourire hollywoodien',
			'A bespoke makeover that reshapes your smile aesthetic.' => 'Une transformation sur mesure qui redessine l’esthétique de votre sourire.',
			'About Estecapelli'                     => 'À propos d’Estecapelli',
			'Who we are and what drives our clinic forward.' => 'Qui nous sommes et ce qui fait avancer notre clinique.',
			'Our Doctors'                           => 'Nos médecins',
			'Meet the surgeons leading every procedure.' => 'Rencontrez les chirurgiens qui dirigent chaque intervention.',
			'Our Team'                              => 'Notre équipe',
			'The full medical and patient-care team behind your treatment.' => 'Toute l’équipe médicale et d’accompagnement derrière votre traitement.',
		),

		// ----------------------------------------------------------------- Spanish.
		'es' => array(
			'Skip to content'                       => 'Saltar al contenido',
			'Primary'                               => 'Navegación principal',
			'Free Consultation'                     => 'Consulta gratuita',
			'Choose language. Current language: %s'  => 'Elegir idioma. Idioma actual: %s',
			'Toggle menu'                           => 'Abrir o cerrar el menú',
			'Home'                                  => 'Inicio',
			'Treatments'                            => 'Tratamientos',
			'Blog'                                  => 'Blog',
			'Contact'                               => 'Contacto',
			'Contact Us'                            => 'Contáctenos',
			'Chat on WhatsApp'                      => 'Chatear por WhatsApp',
			'Chief Physician'                       => 'Médico jefe',
			'Hair Transplant'                       => 'Trasplante capilar',
			'Plastic Surgery'                       => 'Cirugía plástica',
			'Dental Treatment'                      => 'Tratamiento dental',
			'Before & After'                        => 'Antes y después',
			'About Us'                              => 'Sobre nosotros',
			'Sapphire FUE Hair Transplant'          => 'Trasplante capilar FUE Zafiro',
			'A natural and permanent hair transplant technique where follicles are placed via sapphire blades.' => 'Una técnica de trasplante natural y permanente en la que los folículos se implantan mediante cuchillas de zafiro.',
			'DHI Hair Transplant'                   => 'Trasplante capilar DHI',
			'A modern hair transplantation method performed with a Choi pen that allows precise placement.' => 'Un método moderno realizado con un bolígrafo Choi que permite una implantación precisa.',
			'Exosome FUE Hair Transplant'           => 'Trasplante capilar Exosome FUE',
			'Supported by cell-regenerating exosomes, it keeps hair follicles alive for longer-lasting density.' => 'Con el apoyo de exosomas regeneradores, mantiene vivos los folículos para una densidad más duradera.',
			'POPULAR'                               => 'POPULAR',
			'VITA Treatment'                        => 'Tratamiento VITA',
			"Estecapelli's signature method that revitalizes the scalp and strengthens hair." => 'El método exclusivo de Estecapelli que revitaliza el cuero cabelludo y fortalece el cabello.',
			'Female Hair Transplant'                => 'Trasplante capilar femenino',
			'Special for women, dense and natural-looking hair transplant without shaving.' => 'Especial para mujeres: un trasplante denso y de aspecto natural, sin rasurado.',
			'Hair Mesotherapy'                      => 'Mesoterapia capilar',
			'A vitamin and mineral injection treatment that revitalizes hair follicles.' => 'Un tratamiento de inyección de vitaminas y minerales que revitaliza los folículos pilosos.',
			'Beard Transplant'                      => 'Trasplante de barba',
			'Natural beard and mustache transplantation for sparse or non-existing growth.' => 'Trasplante natural de barba y bigote para un crecimiento escaso o inexistente.',
			'Eyebrow Transplant'                    => 'Trasplante de cejas',
			'Eyebrow transplantation that gives a naturally curved and full eyebrow shape.' => 'Un trasplante de cejas que aporta una forma llena y de curva natural.',
			'Hair Transplant Care & Technology'     => 'Trasplante capilar: cuidado y tecnología',
			'Pre-Hair Transplant Period'            => 'Período previo al trasplante capilar',
			'The preparation and analysis process before hair transplantation.' => 'El proceso de preparación y análisis antes del trasplante capilar.',
			'Post-Hair Transplant Period'           => 'Período posterior al trasplante capilar',
			'The post-procedure recovery and hair care period.' => 'El período de recuperación y cuidado del cabello tras el procedimiento.',
			'Advanced AI-powered hair analysis system that examines the hair and scalp in detail.' => 'Sistema avanzado de análisis capilar con IA que examina en detalle el cabello y el cuero cabelludo.',
			'AI Analysis by Estecapelli'            => 'Análisis con IA de Estecapelli',
			'We have trained an AI to guide every patient to the best next step — get a first, personalised hair-loss assessment in seconds.' => 'Hemos entrenado una IA para guiar a cada paciente hacia el mejor paso siguiente: obtenga una primera evaluación personalizada de su pérdida de cabello en segundos.',
			'Start AI Analysis'                     => 'Iniciar análisis con IA',
			'Rhinoplasty'                           => 'Rinoplastia',
			'Nose reshaping surgery that refines proportions and function.' => 'Cirugía de remodelación de la nariz que afina proporciones y función.',
			'BBL (Brazilian Butt Lift)'             => 'BBL (levantamiento de glúteos brasileño)',
			'Natural body contouring with fat transfer to the buttocks.' => 'Remodelación corporal natural con transferencia de grasa a los glúteos.',
			'Liposuction'                           => 'Liposucción',
			'Removes localized fat deposits to reshape the body.' => 'Elimina los depósitos de grasa localizados para remodelar el cuerpo.',
			'Breast Aesthetics'                     => 'Estética mamaria',
			'Augmentation, lift, and reduction tailored to your goals.' => 'Aumento, elevación y reducción adaptados a sus objetivos.',
			'Abdominoplasty (Tummy Tuck)'           => 'Abdominoplastia',
			'Flattens and tightens the abdomen for a smoother profile.' => 'Aplana y tensa el abdomen para un perfil más armonioso.',
			'Gynecomastia'                          => 'Ginecomastia',
			'Surgical treatment of enlarged male breast tissue.' => 'Tratamiento quirúrgico del aumento del tejido mamario masculino.',
			'Face & Neck Lift Surgery'              => 'Lifting facial y de cuello',
			'Restores facial contour and reduces visible signs of aging.' => 'Restaura el contorno facial y reduce los signos visibles del envejecimiento.',
			'Obesity Surgeries (Bariatric)'         => 'Cirugía de la obesidad',
			'Bariatric surgery and gastric balloon for sustainable weight loss.' => 'Cirugía bariátrica y balón gástrico para una pérdida de peso duradera.',
			'Dental Implant'                        => 'Implante dental',
			'Permanent replacement for missing teeth with titanium roots.' => 'Reemplazo permanente de los dientes ausentes con raíces de titanio.',
			'Hollywood Smile'                       => 'Sonrisa de Hollywood',
			'A bespoke makeover that reshapes your smile aesthetic.' => 'Una transformación a medida que redefine la estética de su sonrisa.',
			'About Estecapelli'                     => 'Sobre Estecapelli',
			'Who we are and what drives our clinic forward.' => 'Quiénes somos y qué impulsa a nuestra clínica.',
			'Our Doctors'                           => 'Nuestros médicos',
			'Meet the surgeons leading every procedure.' => 'Conozca a los cirujanos que dirigen cada procedimiento.',
			'Our Team'                              => 'Nuestro equipo',
			'The full medical and patient-care team behind your treatment.' => 'Todo el equipo médico y de atención al paciente detrás de su tratamiento.',
		),

		// -------------------------------------------------------------- Portuguese.
		'pt' => array(
			'Skip to content'                       => 'Saltar para o conteúdo',
			'Primary'                               => 'Navegação principal',
			'Free Consultation'                     => 'Consulta gratuita',
			'Choose language. Current language: %s'  => 'Escolher idioma. Idioma atual: %s',
			'Toggle menu'                           => 'Abrir ou fechar o menu',
			'Home'                                  => 'Início',
			'Treatments'                            => 'Tratamentos',
			'Blog'                                  => 'Blog',
			'Contact'                               => 'Contacto',
			'Contact Us'                            => 'Contacte-nos',
			'Chat on WhatsApp'                      => 'Falar no WhatsApp',
			'Chief Physician'                       => 'Médico-chefe',
			'Hair Transplant'                       => 'Transplante capilar',
			'Plastic Surgery'                       => 'Cirurgia plástica',
			'Dental Treatment'                      => 'Tratamento dentário',
			'Before & After'                        => 'Antes e depois',
			'About Us'                              => 'Sobre nós',
			'Sapphire FUE Hair Transplant'          => 'Transplante capilar FUE Safira',
			'A natural and permanent hair transplant technique where follicles are placed via sapphire blades.' => 'Uma técnica de transplante natural e permanente em que os folículos são implantados com lâminas de safira.',
			'DHI Hair Transplant'                   => 'Transplante capilar DHI',
			'A modern hair transplantation method performed with a Choi pen that allows precise placement.' => 'Um método moderno realizado com uma caneta Choi que permite uma implantação precisa.',
			'Exosome FUE Hair Transplant'           => 'Transplante capilar Exosome FUE',
			'Supported by cell-regenerating exosomes, it keeps hair follicles alive for longer-lasting density.' => 'Com o apoio de exossomas regeneradores, mantém os folículos vivos para uma densidade mais duradoura.',
			'POPULAR'                               => 'POPULAR',
			'VITA Treatment'                        => 'Tratamento VITA',
			"Estecapelli's signature method that revitalizes the scalp and strengthens hair." => 'O método exclusivo da Estecapelli que revitaliza o couro cabeludo e fortalece o cabelo.',
			'Female Hair Transplant'                => 'Transplante capilar feminino',
			'Special for women, dense and natural-looking hair transplant without shaving.' => 'Especial para mulheres: um transplante denso e de aspeto natural, sem rapar.',
			'Hair Mesotherapy'                      => 'Mesoterapia capilar',
			'A vitamin and mineral injection treatment that revitalizes hair follicles.' => 'Um tratamento de injeção de vitaminas e minerais que revitaliza os folículos capilares.',
			'Beard Transplant'                      => 'Transplante de barba',
			'Natural beard and mustache transplantation for sparse or non-existing growth.' => 'Transplante natural de barba e bigode para um crescimento escasso ou inexistente.',
			'Eyebrow Transplant'                    => 'Transplante de sobrancelhas',
			'Eyebrow transplantation that gives a naturally curved and full eyebrow shape.' => 'Um transplante de sobrancelhas que confere uma forma cheia e de curva natural.',
			'Hair Transplant Care & Technology'     => 'Transplante capilar: cuidados e tecnologia',
			'Pre-Hair Transplant Period'            => 'Período pré-transplante capilar',
			'The preparation and analysis process before hair transplantation.' => 'O processo de preparação e análise antes do transplante capilar.',
			'Post-Hair Transplant Period'           => 'Período pós-transplante capilar',
			'The post-procedure recovery and hair care period.' => 'O período de recuperação e cuidados do cabelo após o procedimento.',
			'Advanced AI-powered hair analysis system that examines the hair and scalp in detail.' => 'Sistema avançado de análise capilar com IA que examina em detalhe o cabelo e o couro cabeludo.',
			'AI Analysis by Estecapelli'            => 'Análise com IA da Estecapelli',
			'We have trained an AI to guide every patient to the best next step — get a first, personalised hair-loss assessment in seconds.' => 'Treinámos uma IA para orientar cada paciente para o melhor passo seguinte — obtenha uma primeira avaliação personalizada da sua queda de cabelo em segundos.',
			'Start AI Analysis'                     => 'Iniciar análise com IA',
			'Rhinoplasty'                           => 'Rinoplastia',
			'Nose reshaping surgery that refines proportions and function.' => 'Cirurgia de remodelação do nariz que refina proporções e função.',
			'BBL (Brazilian Butt Lift)'             => 'BBL (levantamento de glúteos brasileiro)',
			'Natural body contouring with fat transfer to the buttocks.' => 'Remodelação corporal natural com transferência de gordura para os glúteos.',
			'Liposuction'                           => 'Lipoaspiração',
			'Removes localized fat deposits to reshape the body.' => 'Remove depósitos de gordura localizados para remodelar o corpo.',
			'Breast Aesthetics'                     => 'Estética mamária',
			'Augmentation, lift, and reduction tailored to your goals.' => 'Aumento, elevação e redução adaptados aos seus objetivos.',
			'Abdominoplasty (Tummy Tuck)'           => 'Abdominoplastia',
			'Flattens and tightens the abdomen for a smoother profile.' => 'Aplana e firma o abdómen para um perfil mais harmonioso.',
			'Gynecomastia'                          => 'Ginecomastia',
			'Surgical treatment of enlarged male breast tissue.' => 'Tratamento cirúrgico do aumento do tecido mamário masculino.',
			'Face & Neck Lift Surgery'              => 'Lifting do rosto e do pescoço',
			'Restores facial contour and reduces visible signs of aging.' => 'Restaura o contorno facial e reduz os sinais visíveis do envelhecimento.',
			'Obesity Surgeries (Bariatric)'         => 'Cirurgia da obesidade',
			'Bariatric surgery and gastric balloon for sustainable weight loss.' => 'Cirurgia bariátrica e balão gástrico para uma perda de peso duradoura.',
			'Dental Implant'                        => 'Implante dentário',
			'Permanent replacement for missing teeth with titanium roots.' => 'Substituição permanente de dentes em falta com raízes de titânio.',
			'Hollywood Smile'                       => 'Sorriso de Hollywood',
			'A bespoke makeover that reshapes your smile aesthetic.' => 'Uma transformação personalizada que redefine a estética do seu sorriso.',
			'About Estecapelli'                     => 'Sobre a Estecapelli',
			'Who we are and what drives our clinic forward.' => 'Quem somos e o que impulsiona a nossa clínica.',
			'Our Doctors'                           => 'Os nossos médicos',
			'Meet the surgeons leading every procedure.' => 'Conheça os cirurgiões que lideram cada procedimento.',
			'Our Team'                              => 'A nossa equipa',
			'The full medical and patient-care team behind your treatment.' => 'Toda a equipa médica e de apoio ao paciente por trás do seu tratamento.',
		),

		// ------------------------------------------------------------------ Polish.
		'pl' => array(
			'Skip to content'                       => 'Przejdź do treści',
			'Primary'                               => 'Nawigacja główna',
			'Free Consultation'                     => 'Bezpłatna konsultacja',
			'Choose language. Current language: %s'  => 'Wybierz język. Bieżący język: %s',
			'Toggle menu'                           => 'Otwórz lub zamknij menu',
			'Home'                                  => 'Strona główna',
			'Treatments'                            => 'Zabiegi',
			'Blog'                                  => 'Blog',
			'Contact'                               => 'Kontakt',
			'Contact Us'                            => 'Kontakt',
			'Chat on WhatsApp'                      => 'Napisz na WhatsApp',
			'Chief Physician'                       => 'Naczelny lekarz',
			'Hair Transplant'                       => 'Przeszczep włosów',
			'Plastic Surgery'                       => 'Chirurgia plastyczna',
			'Dental Treatment'                      => 'Leczenie stomatologiczne',
			'Before & After'                        => 'Przed i po',
			'About Us'                              => 'O nas',
			'Sapphire FUE Hair Transplant'          => 'Przeszczep włosów Sapphire FUE',
			'A natural and permanent hair transplant technique where follicles are placed via sapphire blades.' => 'Naturalna i trwała technika przeszczepu, w której mieszki umieszcza się za pomocą szafirowych ostrzy.',
			'DHI Hair Transplant'                   => 'Przeszczep włosów DHI',
			'A modern hair transplantation method performed with a Choi pen that allows precise placement.' => 'Nowoczesna metoda wykonywana piórem Choi, umożliwiająca precyzyjne umieszczenie mieszków.',
			'Exosome FUE Hair Transplant'           => 'Przeszczep włosów Exosome FUE',
			'Supported by cell-regenerating exosomes, it keeps hair follicles alive for longer-lasting density.' => 'Dzięki regenerującym egzosomom mieszki włosowe pozostają żywotne, zapewniając trwalszą gęstość.',
			'POPULAR'                               => 'POPULARNE',
			'VITA Treatment'                        => 'Zabieg VITA',
			"Estecapelli's signature method that revitalizes the scalp and strengthens hair." => 'Autorska metoda Estecapelli, która rewitalizuje skórę głowy i wzmacnia włosy.',
			'Female Hair Transplant'                => 'Przeszczep włosów u kobiet',
			'Special for women, dense and natural-looking hair transplant without shaving.' => 'Specjalnie dla kobiet: gęsty i naturalny przeszczep bez golenia.',
			'Hair Mesotherapy'                      => 'Mezoterapia włosów',
			'A vitamin and mineral injection treatment that revitalizes hair follicles.' => 'Zabieg iniekcji witamin i minerałów, który rewitalizuje mieszki włosowe.',
			'Beard Transplant'                      => 'Przeszczep brody',
			'Natural beard and mustache transplantation for sparse or non-existing growth.' => 'Naturalny przeszczep brody i wąsów przy rzadkim lub braku zarostu.',
			'Eyebrow Transplant'                    => 'Przeszczep brwi',
			'Eyebrow transplantation that gives a naturally curved and full eyebrow shape.' => 'Przeszczep brwi nadający pełny i naturalnie wygięty kształt.',
			'Hair Transplant Care & Technology'     => 'Przeszczep włosów: opieka i technologia',
			'Pre-Hair Transplant Period'            => 'Okres przed przeszczepem włosów',
			'The preparation and analysis process before hair transplantation.' => 'Proces przygotowania i analizy przed przeszczepem włosów.',
			'Post-Hair Transplant Period'           => 'Okres po przeszczepie włosów',
			'The post-procedure recovery and hair care period.' => 'Okres rekonwalescencji i pielęgnacji włosów po zabiegu.',
			'Advanced AI-powered hair analysis system that examines the hair and scalp in detail.' => 'Zaawansowany system analizy włosów oparty na SI, który szczegółowo bada włosy i skórę głowy.',
			'AI Analysis by Estecapelli'            => 'Analiza SI od Estecapelli',
			'We have trained an AI to guide every patient to the best next step — get a first, personalised hair-loss assessment in seconds.' => 'Wytrenowaliśmy SI, aby poprowadziła każdego pacjenta do najlepszego kolejnego kroku — uzyskaj pierwszą, spersonalizowaną ocenę wypadania włosów w kilka sekund.',
			'Start AI Analysis'                     => 'Rozpocznij analizę SI',
			'Rhinoplasty'                           => 'Plastyka nosa',
			'Nose reshaping surgery that refines proportions and function.' => 'Operacja korekty nosa poprawiająca proporcje i funkcję.',
			'BBL (Brazilian Butt Lift)'             => 'BBL (brazylijski lifting pośladków)',
			'Natural body contouring with fat transfer to the buttocks.' => 'Naturalne modelowanie sylwetki z przeszczepem tłuszczu do pośladków.',
			'Liposuction'                           => 'Liposukcja',
			'Removes localized fat deposits to reshape the body.' => 'Usuwa miejscowe nagromadzenia tłuszczu, modelując sylwetkę.',
			'Breast Aesthetics'                     => 'Estetyka piersi',
			'Augmentation, lift, and reduction tailored to your goals.' => 'Powiększanie, podniesienie i redukcja dopasowane do Twoich celów.',
			'Abdominoplasty (Tummy Tuck)'           => 'Abdominoplastyka',
			'Flattens and tightens the abdomen for a smoother profile.' => 'Wypłaszcza i ujędrnia brzuch, nadając gładszy profil.',
			'Gynecomastia'                          => 'Ginekomastia',
			'Surgical treatment of enlarged male breast tissue.' => 'Chirurgiczne leczenie przerośniętej tkanki piersiowej u mężczyzn.',
			'Face & Neck Lift Surgery'              => 'Lifting twarzy i szyi',
			'Restores facial contour and reduces visible signs of aging.' => 'Przywraca kontur twarzy i redukuje widoczne oznaki starzenia.',
			'Obesity Surgeries (Bariatric)'         => 'Chirurgia otyłości',
			'Bariatric surgery and gastric balloon for sustainable weight loss.' => 'Chirurgia bariatryczna i balon żołądkowy dla trwałej utraty wagi.',
			'Dental Implant'                        => 'Implant zębowy',
			'Permanent replacement for missing teeth with titanium roots.' => 'Trwałe zastąpienie brakujących zębów korzeniami tytanowymi.',
			'Hollywood Smile'                       => 'Hollywoodzki uśmiech',
			'A bespoke makeover that reshapes your smile aesthetic.' => 'Spersonalizowana metamorfoza, która odmienia estetykę Twojego uśmiechu.',
			'About Estecapelli'                     => 'O Estecapelli',
			'Who we are and what drives our clinic forward.' => 'Kim jesteśmy i co napędza naszą klinikę.',
			'Our Doctors'                           => 'Nasi lekarze',
			'Meet the surgeons leading every procedure.' => 'Poznaj chirurgów prowadzących każdy zabieg.',
			'Our Team'                              => 'Nasz zespół',
			'The full medical and patient-care team behind your treatment.' => 'Cały zespół medyczny i opieki nad pacjentem stojący za Twoim leczeniem.',
		),

		// ----------------------------------------------------------------- Turkish.
		'tr' => array(
			'Skip to content'                       => 'İçeriğe geç',
			'Primary'                               => 'Ana gezinme',
			'Free Consultation'                     => 'Ücretsiz Konsültasyon',
			'Choose language. Current language: %s'  => 'Dil seçin. Geçerli dil: %s',
			'Toggle menu'                           => 'Menüyü aç/kapat',
			'Home'                                  => 'Ana Sayfa',
			'Treatments'                            => 'Tedaviler',
			'Blog'                                  => 'Blog',
			'Contact'                               => 'İletişim',
			'Contact Us'                            => 'İletişim',
			'Chat on WhatsApp'                      => 'WhatsApp’tan yazın',
			'Chief Physician'                       => 'Başhekim',
			'Hair Transplant'                       => 'Saç Ekimi',
			'Plastic Surgery'                       => 'Estetik Cerrahi',
			'Dental Treatment'                      => 'Diş Tedavisi',
			'Before & After'                        => 'Öncesi ve Sonrası',
			'About Us'                              => 'Hakkımızda',
			'Sapphire FUE Hair Transplant'          => 'Safir FUE Saç Ekimi',
			'A natural and permanent hair transplant technique where follicles are placed via sapphire blades.' => 'Foliküllerin safir uçlarla yerleştirildiği doğal ve kalıcı bir saç ekimi tekniği.',
			'DHI Hair Transplant'                   => 'DHI Saç Ekimi',
			'A modern hair transplantation method performed with a Choi pen that allows precise placement.' => 'Choi kalemi ile yapılan, hassas yerleştirmeye olanak tanıyan modern bir yöntem.',
			'Exosome FUE Hair Transplant'           => 'Exosome FUE Saç Ekimi',
			'Supported by cell-regenerating exosomes, it keeps hair follicles alive for longer-lasting density.' => 'Hücre yenileyici eksozomlarla desteklenir; folikülleri daha uzun süre canlı tutarak kalıcı yoğunluk sağlar.',
			'POPULAR'                               => 'POPÜLER',
			'VITA Treatment'                        => 'VITA Tedavisi',
			"Estecapelli's signature method that revitalizes the scalp and strengthens hair." => 'Estecapelli’nin saçlı deriyi canlandıran ve saçları güçlendiren imza yöntemi.',
			'Female Hair Transplant'                => 'Kadınlarda Saç Ekimi',
			'Special for women, dense and natural-looking hair transplant without shaving.' => 'Kadınlara özel; tıraşsız, yoğun ve doğal görünümlü saç ekimi.',
			'Hair Mesotherapy'                      => 'Saç Mezoterapisi',
			'A vitamin and mineral injection treatment that revitalizes hair follicles.' => 'Saç foliküllerini canlandıran vitamin ve mineral enjeksiyonu tedavisi.',
			'Beard Transplant'                      => 'Sakal Ekimi',
			'Natural beard and mustache transplantation for sparse or non-existing growth.' => 'Seyrek veya hiç olmayan bölgeler için doğal sakal ve bıyık ekimi.',
			'Eyebrow Transplant'                    => 'Kaş Ekimi',
			'Eyebrow transplantation that gives a naturally curved and full eyebrow shape.' => 'Doğal kavisli ve dolgun bir kaş şekli kazandıran kaş ekimi.',
			'Hair Transplant Care & Technology'     => 'Saç Ekimi: Bakım ve Teknoloji',
			'Pre-Hair Transplant Period'            => 'Saç Ekimi Öncesi Dönem',
			'The preparation and analysis process before hair transplantation.' => 'Saç ekiminden önceki hazırlık ve analiz süreci.',
			'Post-Hair Transplant Period'           => 'Saç Ekimi Sonrası Dönem',
			'The post-procedure recovery and hair care period.' => 'İşlem sonrası iyileşme ve saç bakımı dönemi.',
			'Advanced AI-powered hair analysis system that examines the hair and scalp in detail.' => 'Saçı ve saçlı deriyi ayrıntılı inceleyen gelişmiş yapay zekâ destekli saç analiz sistemi.',
			'AI Analysis by Estecapelli'            => 'Estecapelli Yapay Zekâ Analizi',
			'We have trained an AI to guide every patient to the best next step — get a first, personalised hair-loss assessment in seconds.' => 'Her hastayı en iyi sonraki adıma yönlendirmek için bir yapay zekâ eğittik — saniyeler içinde ilk, kişiselleştirilmiş saç dökülmesi değerlendirmenizi alın.',
			'Start AI Analysis'                     => 'Yapay Zekâ Analizini Başlat',
			'Rhinoplasty'                           => 'Rinoplasti (Burun Estetiği)',
			'Nose reshaping surgery that refines proportions and function.' => 'Oranları ve işlevi iyileştiren burun şekillendirme ameliyatı.',
			'BBL (Brazilian Butt Lift)'             => 'BBL (Brezilya Popo Estetiği)',
			'Natural body contouring with fat transfer to the buttocks.' => 'Kalçaya yağ transferi ile doğal vücut şekillendirme.',
			'Liposuction'                           => 'Liposuction (Yağ Aldırma)',
			'Removes localized fat deposits to reshape the body.' => 'Vücudu yeniden şekillendirmek için bölgesel yağ birikimlerini alır.',
			'Breast Aesthetics'                     => 'Meme Estetiği',
			'Augmentation, lift, and reduction tailored to your goals.' => 'Hedeflerinize göre uyarlanmış büyütme, dikleştirme ve küçültme.',
			'Abdominoplasty (Tummy Tuck)'           => 'Abdominoplasti (Karın Germe)',
			'Flattens and tightens the abdomen for a smoother profile.' => 'Daha pürüzsüz bir görünüm için karnı düzleştirir ve sıkılaştırır.',
			'Gynecomastia'                          => 'Jinekomasti',
			'Surgical treatment of enlarged male breast tissue.' => 'Erkeklerde büyümüş meme dokusunun cerrahi tedavisi.',
			'Face & Neck Lift Surgery'              => 'Yüz ve Boyun Germe Ameliyatı',
			'Restores facial contour and reduces visible signs of aging.' => 'Yüz hatlarını yeniler ve yaşlanmanın görünür izlerini azaltır.',
			'Obesity Surgeries (Bariatric)'         => 'Obezite Cerrahisi',
			'Bariatric surgery and gastric balloon for sustainable weight loss.' => 'Kalıcı kilo kaybı için bariatrik cerrahi ve mide balonu.',
			'Dental Implant'                        => 'Diş İmplantı',
			'Permanent replacement for missing teeth with titanium roots.' => 'Eksik dişlerin titanyum köklerle kalıcı olarak yenilenmesi.',
			'Hollywood Smile'                       => 'Hollywood Gülüşü',
			'A bespoke makeover that reshapes your smile aesthetic.' => 'Gülüş estetiğinizi yeniden şekillendiren kişiye özel bir dönüşüm.',
			'About Estecapelli'                     => 'Estecapelli Hakkında',
			'Who we are and what drives our clinic forward.' => 'Kim olduğumuz ve kliniğimizi ileriye taşıyan değerler.',
			'Our Doctors'                           => 'Doktorlarımız',
			'Meet the surgeons leading every procedure.' => 'Her prosedürü yöneten cerrahlarla tanışın.',
			'Our Team'                              => 'Ekibimiz',
			'The full medical and patient-care team behind your treatment.' => 'Tedavinizin arkasındaki tüm medikal ve hasta bakım ekibi.',
		),
	);
}

/**
 * Localized short labels for every navigation route in one language.
 *
 * @param string $lang Language code.
 * @return array<string,string> Route => localized label.
 */
function estecapelli_nav_labels_for( $lang ) {
	$all = estecapelli_nav_translations();
	if ( ! isset( $all[ $lang ] ) ) {
		return array();
	}
	$strings = $all[ $lang ];
	$labels  = array();
	foreach ( estecapelli_nav_route_to_label() as $route => $en_label ) {
		if ( isset( $strings[ $en_label ] ) ) {
			$labels[ $route ] = $strings[ $en_label ];
		}
	}
	return $labels;
}

/**
 * Resolve a WordPress menu item to its canonical English route key.
 *
 * @param WP_Post|object $item Menu item.
 * @return string
 */
function estecapelli_nav_item_route_key( $item ) {
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

	$title = trim( wp_strip_all_tags( (string) ( $item->title ?? '' ) ) );
	$src   = estecapelli_nav_source_title_routes();
	return $src[ $title ] ?? '';
}

add_filter( 'gettext', 'estecapelli_nav_gettext_fallback', 30, 3 );
/**
 * Supply navigation copy in the current language when nothing else translated it.
 *
 * @param string $translation Current translated value.
 * @param string $text        English source value.
 * @param string $domain      Text domain.
 * @return string
 */
function estecapelli_nav_gettext_fallback( $translation, $text, $domain ) {
	if ( ! estecapelli_is_public_translation_request() || 'estecapelli' !== $domain || $translation !== $text ) {
		return $translation;
	}
	$all  = estecapelli_nav_translations();
	$lang = estecapelli_nav_current_lang();
	if ( ! isset( $all[ $lang ] ) ) {
		return $translation;
	}
	return $all[ $lang ][ $text ] ?? $translation;
}

add_filter( 'gettext', 'estecapelli_preserve_source_gettext_outside_public_requests', PHP_INT_MAX, 3 );
/**
 * Keep the theme's PHP seed strings in their authored English form.
 *
 * Several visitor-facing locale fallbacks run before this filter. Resetting the
 * theme domain to its source text outside public rendering prevents any of them
 * from contaminating an importer executed in a translated admin language.
 *
 * @param string $translation Current translated value.
 * @param string $text        English source value.
 * @param string $domain      Text domain.
 * @return string
 */
function estecapelli_preserve_source_gettext_outside_public_requests( $translation, $text, $domain ) {
	if ( 'estecapelli' === $domain && ! estecapelli_is_public_translation_request() ) {
		return $text;
	}

	return $translation;
}

add_filter( 'nav_menu_item_title', 'estecapelli_nav_menu_title', 100, 4 );
/**
 * Translate database-backed menu titles by canonical destination.
 *
 * @param string $title Menu title.
 * @param object $item  Menu item.
 * @return string
 */
function estecapelli_nav_menu_title( $title, $item ) {
	$all  = estecapelli_nav_translations();
	$lang = estecapelli_nav_current_lang();
	if ( ! isset( $all[ $lang ] ) ) {
		return $title;
	}

	$key    = estecapelli_nav_item_route_key( $item );
	$labels = estecapelli_nav_labels_for( $lang );
	if ( $key && isset( $labels[ $key ] ) ) {
		return $labels[ $key ];
	}

	return $all[ $lang ][ $title ] ?? $title;
}

add_filter( 'nav_menu_link_attributes', 'estecapelli_nav_menu_attributes', 1001, 4 );
/**
 * Force database-backed menu hrefs to the exact live URL for the language.
 *
 * @param array  $atts Link attributes.
 * @param object $item Menu item.
 * @return array
 */
function estecapelli_nav_menu_attributes( $atts, $item ) {
	$all  = estecapelli_nav_translations();
	$lang = estecapelli_nav_current_lang();
	if ( ! isset( $all[ $lang ] ) ) {
		return $atts;
	}

	$key = estecapelli_nav_item_route_key( $item );
	if ( $key && estecapelli_indexed_route_path( $key, $lang ) ) {
		$atts['href'] = estecapelli_indexed_url( $key, $lang );
	}

	return $atts;
}
