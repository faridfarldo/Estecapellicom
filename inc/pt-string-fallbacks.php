<?php
/**
 * Portuguese fallbacks for front-end theme strings.
 *
 * WPML String Translation remains authoritative. These values only fill text
 * that is still identical to its English source, including when WPML exposes
 * Portuguese internally as pt-pt while public URLs use /pt/.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Whether the current request is the indexed Portuguese language. */
function estecapelli_is_portuguese_request() {
	// The URL is the contract, and it has to be checked first. WPML's own
	// directory is pt-pt, so it does not recognise /pt/ and answers with the
	// site's DEFAULT language wherever it cannot resolve the request — most
	// visibly on a 404, which rendered the Portuguese error page in English
	// while all six other languages translated correctly.
	if ( function_exists( 'estecapelli_request_language_code' ) ) {
		$requested = estecapelli_request_language_code();
		if ( '' !== $requested ) {
			return 'pt' === $requested;
		}
	}

	$language = (string) apply_filters( 'wpml_current_language', null );
	if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
		return 'pt' === estecapelli_indexed_language_code( $language );
	}

	return in_array( strtolower( str_replace( '_', '-', $language ) ), array( 'pt', 'pt-pt' ), true );
}

add_filter( 'gettext', 'estecapelli_pt_gettext_authoritative', 999, 3 );
/**
 * Force the reviewed Portuguese for theme strings that WPML String Translation
 * holds a stale, auto-translated value for. Runs after WPML (unlike the fallback
 * below) so the homepage always shows the approved copy. Keep this list short
 * and homepage-scoped — every entry here permanently overrides WPML for that
 * string in Portuguese.
 */
function estecapelli_pt_gettext_authoritative( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || ! estecapelli_is_portuguese_request() ) {
		return $translation;
	}

	static $overrides = null;
	if ( null === $overrides ) {
		$overrides = array(
			'I know my hair-loss area' => 'Conheço a minha área de perda capilar',
			'We trained our AI on the results of more than 15,000 patients. Learning from their success stories, it analyses your condition and guides you to the treatment that fits you best.' => 'Treinamos nossa Inteligência Artificial com os resultados de mais de 15.000 pacientes. Com base na análise de milhares de casos de sucesso, ela avalia o seu perfil capilar e indica o tratamento mais adequado para alcançar os melhores resultados.',
		);
	}

	return array_key_exists( $text, $overrides ) ? $overrides[ $text ] : $translation;
}

add_filter( 'gettext', 'estecapelli_pt_gettext_fallback', 25, 3 );
/** Supply Portuguese only when no earlier translation provider changed text. */
function estecapelli_pt_gettext_fallback( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || $translation !== $text || ! estecapelli_is_portuguese_request() ) {
		return $translation;
	}

	static $strings = null;
	if ( null === $strings ) {
		$strings = array(
			// Journal / blog UI.
			'From the Journal' => 'Do nosso blog',
			'Research, results and recovery — written by our team.' => 'Investigação, resultados e recuperação — escritos pela nossa equipa.',
			'View all articles' => 'Ver todos os artigos',
			'Write the first article' => 'Escrever o primeiro artigo',
			'No articles have been published yet. New posts will appear here as soon as your team publishes them.' => 'Ainda não foi publicado nenhum artigo. Os novos artigos aparecerão aqui assim que a sua equipa os publicar.',
			'Research, results & recovery' => 'Investigação, resultados e recuperação',
			'Expert articles on hair restoration, plastic surgery, dental treatment and the journey to your transformation — written by the Estecapelli team.' => 'Artigos especializados sobre restauração capilar, cirurgia plástica, tratamentos dentários e o percurso da sua transformação — escritos pela equipa Estecapelli.',
			'Article categories' => 'Categorias de artigos',
			'All' => 'Todos',
			'Latest article' => 'Artigo mais recente',
			'Read the article' => 'Leia o artigo',
			'Keep reading' => 'Continue lendo',
			'Articles from the Estecapelli journal.' => 'Artigos do blog Estecapelli.',
			'All articles' => 'Todos os artigos',
			'Share' => 'Partilhar',
			'Share on Facebook' => 'Partilhar no Facebook',
			'Share on WhatsApp' => 'Partilhar no WhatsApp',
			'Copy link' => 'Copiar o link',
			'Pages:' => 'Páginas:',
			'Read more' => 'Ler mais',
			'Posts pagination' => 'Paginação dos artigos',
			'Newer' => 'Mais recentes',
			'Older' => 'Mais antigos',
			'No articles found here yet.' => 'Ainda não foram encontrados artigos.',
			'No articles have been published yet. New posts will appear here as soon as our team publishes them.' => 'Ainda não foi publicado nenhum artigo. Os novos artigos aparecerão aqui assim que a nossa equipa os publicar.',

			// Shared navigation, footer and lead forms.
			'Skip to content' => 'Saltar para o conteúdo',
			'Primary' => 'Navegação principal',
			'Toggle menu' => 'Abrir ou fechar o menu',
			'Choose language. Current language: %s' => 'Escolher idioma. Idioma atual: %s',
			'Free Consultation' => 'Consulta gratuita',
			'Book a consultation' => 'Marcar uma consulta',
			'Ready to start your transformation?' => 'Está pronto para iniciar a sua transformação?',
			'Speak with our medical team — free, no obligation. Get a personalized plan based on your goals.' => 'Fale gratuitamente e sem compromisso com a nossa equipa médica. Receba um plano personalizado com base nos seus objetivos.',
			'Get a Free Consultation' => 'Solicitar uma consulta gratuita',
			'Chat on WhatsApp' => 'Falar no WhatsApp',
			// WhatsApp hand-off notice.
			'Please leave the message already prepared in WhatsApp exactly as it is — it tells our team which page you are writing from, so we can reply to you faster. You are very welcome to add your question right after it.' => 'Pedimos que mantenha tal como está a mensagem já preparada no WhatsApp: indica à nossa equipa a partir de que página nos escreve e permite-nos responder-lhe mais depressa. Pode acrescentar a sua pergunta logo a seguir.',
			'Continue to WhatsApp' => 'Continuar para o WhatsApp',
			'Here are my photos:' => 'Aqui estão as minhas fotos:',
			'Our Licences' => 'As nossas certificações',
			'Sitemap' => 'Mapa do site',
			'Language' => 'Idioma',
			'Get a free consultation — leave your details and we will reach out.' => 'Solicite uma consulta gratuita: deixe os seus dados e entraremos em contacto.',
			'Name and surname' => 'Nome e apelido',
			'Phone number' => 'Número de telefone',
			'Email address' => 'Endereço de e-mail',
			'Request Call Back' => 'Pedir para ser contactado',
			'All rights reserved.' => 'Todos os direitos reservados.',
			'Footer legal' => 'Ligações legais no rodapé',
			'Privacy Policy' => 'Política de privacidade',
			'Terms' => 'Termos e condições',
			'Cookie Policy' => 'Política de cookies',
			'KVKK Notice' => 'Aviso KVKK',
			'Free Hair Analysis' => 'Análise capilar gratuita',
			'Reply in 2 minutes' => 'Resposta em 2 minutos',
			'Photo viewer' => 'Visualizador de fotografias',
			'Close' => 'Fechar',
			'Previous photo' => 'Fotografia anterior',
			'Next photo' => 'Fotografia seguinte',
			'Book your free consultation' => 'Marque a sua consulta gratuita',
			'Leave your details and a medical consultant will get back to you shortly — no obligation.' => 'Deixe os seus dados e um consultor médico entrará em contacto consigo em breve, sem compromisso.',
			'Full name' => 'Nome completo',
			'Phone' => 'Telefone',
			'Email' => 'E-mail',
			'Note' => 'Nota',
			'Tell us about your goals, or any questions you have…' => 'Fale-nos dos seus objetivos ou coloque as suas questões…',
			'Request a Free Consultation' => 'Solicitar uma consulta gratuita',
			'Thank you! Your request has been received — our team will contact you shortly.' => 'Obrigado! Recebemos o seu pedido e a nossa equipa entrará em contacto consigo em breve.',
			'Hello Estecapelli, I would like to book a free consultation.' => 'Olá Estecapelli, gostaria de marcar uma consulta gratuita.',

			// Contact and reusable form copy.
			'Hello Estecapelli, I would like a free analysis. Here are my photos:' => 'Olá Estecapelli, gostaria de receber uma análise gratuita. Estas são as minhas fotografias:',
			'English' => 'Inglês',
			'French' => 'Francês',
			'Italian' => 'Italiano',
			'Spanish' => 'Espanhol',
			'Polish' => 'Polaco',
			'Portuguese' => 'Português',
			'Contact Us' => 'Contacte-nos',
			'Let’s start your journey' => 'Vamos iniciar o seu percurso',
			'Reach our team by WhatsApp, phone or email — or leave your details and a medical consultant will get back to you in your own language.' => 'Contacte a nossa equipa por WhatsApp, telefone ou e-mail, ou deixe os seus dados para que um consultor médico lhe responda no seu idioma.',
			'We usually reply within an hour' => 'Normalmente respondemos no prazo de uma hora',
			'Chat with us now' => 'Fale connosco agora',
			'Call' => 'Ligar',
			'Request a free consultation' => 'Solicitar uma consulta gratuita',
			'Tell us a little about you and we’ll be in touch shortly.' => 'Fale-nos um pouco sobre si e entraremos em contacto em breve.',
			'Interested in' => 'Tratamento de interesse',
			'Select a treatment' => 'Selecione um tratamento',
			'Hair Transplant' => 'Transplante capilar',
			'Plastic Surgery' => 'Cirurgia plástica',
			'Dental Treatment' => 'Tratamento dentário',
			'Not sure yet' => 'Ainda não tenho a certeza',
			'Message' => 'Mensagem',
			'Send Request' => 'Enviar pedido',
			'Please enter a valid phone number.' => 'Introduza um número de telefone válido.',
			'Please enter a valid email address.' => 'Introduza um endereço de e-mail válido.',
			'Please enter your name.' => 'Introduza o seu nome.',
			'Name is required.' => 'O nome é obrigatório.',
			'Please enter your phone number.' => 'Introduza o seu número de telefone.',
			'Please complete the security check and try again.' => 'Conclua a verificação de segurança e tente novamente.',
			'The security check is temporarily unavailable. Please try again.' => 'A verificação de segurança está temporariamente indisponível. Tente novamente.',
			'Please refresh the page and submit the form again.' => 'Atualize a página e envie novamente o formulário.',
			'Too many requests. Please wait a few minutes and try again.' => 'Demasiados pedidos. Aguarde alguns minutos e tente novamente.',
			'Please select a valid country code.' => 'Selecione um indicativo de país válido.',
			'This number is too short for the selected country.' => 'Este número é demasiado curto para o país selecionado.',
			'This number is too long for the selected country.' => 'Este número é demasiado longo para o país selecionado.',
			'Please enter the full phone number, including the area code.' => 'Introduza o número de telefone completo, incluindo o indicativo.',
			'Please enter a valid phone number for the selected country.' => 'Introduza um número de telefone válido para o país selecionado.',
			'Visit & reach us' => 'Visite-nos ou contacte-nos',
			'Address' => 'Morada',
			'Working hours' => 'Horário de funcionamento',
			'Monday – Sunday: 09:00 – 18:00 (GMT+3)' => 'Segunda-feira – domingo: 09:00 – 18:00 (GMT+3)',
			'What happens next?' => 'O que acontece a seguir?',
			'We review your request and reach out to understand your goals.' => 'Analisamos o seu pedido e entramos em contacto para compreender os seus objetivos.',
			'You receive a free, personalised treatment plan and quote.' => 'Recebe gratuitamente um plano de tratamento e um orçamento personalizados.',
			'We arrange your dates, travel and stay — and welcome you to Istanbul.' => 'Organizamos as datas, a viagem e a estadia e recebemo-lo em Istambul.',
			'Send your photos, get a free analysis' => 'Envie as suas fotografias e receba uma análise gratuita',
			'Share a few photos of your hair, face or smile and our specialists will assess your case and recommend the right approach — at no cost and with no obligation.' => 'Partilhe algumas fotografias do cabelo, rosto ou sorriso e os nossos especialistas avaliarão o seu caso e recomendarão a abordagem adequada, gratuitamente e sem compromisso.',
			'Send Photos on WhatsApp' => 'Enviar fotografias por WhatsApp',
			'Talk to us in your own language' => 'Fale connosco no seu idioma',
			'Estecapelli clinic location' => 'Localização da clínica Estecapelli',

			// Before/After, doctor pages and treatment controls.
			'Before' => 'Antes',
			'After' => 'Depois',
			'Before & After' => 'Antes e depois',
			'Before treatment' => 'Antes do tratamento',
			'After treatment' => 'Depois do tratamento',
			'%s grafts' => '%s enxertos',
			'Before and after results' => 'Resultados antes e depois',
			'Real Results' => 'Resultados reais',
			'Before &amp; After' => 'Antes e depois',
			'Outcomes from our %s patients.' => 'Resultados dos nossos pacientes de %s.',
			'Previous result' => 'Resultado anterior',
			'Next result' => 'Resultado seguinte',
			'Results will appear here soon.' => 'Os resultados estarão disponíveis aqui em breve.',
			'Treatment categories' => 'Categorias de tratamento',
			'%s services' => 'Serviços de %s',
			'View treatment' => 'Ver tratamento',
			'Enlarge result' => 'Ampliar resultado',
			'Before and after result' => 'Resultado antes e depois',
			'grafts' => 'enxertos',
			'See full gallery' => 'Ver a galeria completa',
			'Résumé' => 'Currículo',
			'Book a Free Consultation' => 'Marcar uma consulta gratuita',
			'View Résumé' => 'Ver currículo',
			'Meet the doctor' => 'Conheça o médico',
			'Languages spoken' => 'Idiomas falados',
			'Procedure quick facts' => 'Informação essencial sobre o procedimento',
			'Get Started' => 'Começar',
			'Learn More' => 'Saber mais',
			'Learn more' => 'Saber mais',
			'Previous slide' => 'Diapositivo anterior',
			'Next slide' => 'Diapositivo seguinte',
			'Choose slide' => 'Escolher diapositivo',
			'Slide %d' => 'Diapositivo %d',
			'Next step' => 'Passo seguinte',
			'Previous step' => 'Passo anterior',
			'Steps' => 'Passos',
			'step' => 'passo',
			'Procedure details' => 'Detalhes do procedimento',

			// Homepage hero and service discovery.
			'Highlights' => 'Destaques',
			'Signature Methods' => 'Métodos exclusivos',
			'Select a method to play' => 'Selecione um método para reproduzir',
			'World-Class Experience' => 'Experiência de nível mundial',
			'Estecapelli patient result' => 'Resultado de um paciente Estecapelli',
			'Previous patient' => 'Paciente anterior',
			'Next patient' => 'Paciente seguinte',
			'Estecapelli Dental' => 'Estecapelli Dental',
			'Estecapelli dental patient smiling' => 'Paciente da Estecapelli Dental a sorrir',
			'A Smile Designed Around You' => 'Um sorriso concebido para si',
			'Hollywood smiles, veneers and full-mouth restorations — crafted by our specialists for a natural, confident result that lasts.' => 'Sorrisos de Hollywood, facetas e reabilitações completas, realizados pelos nossos especialistas para um resultado natural, confiante e duradouro.',
			'Hollywood Smile & porcelain veneers' => 'Sorriso de Hollywood e facetas de porcelana',
			'Implants & full-mouth restoration' => 'Implantes e reabilitação oral completa',
			'One trusted clinic, all-inclusive care' => 'Uma clínica de confiança com cuidados completos',
			'Explore Dental Treatments' => 'Conhecer os tratamentos dentários',
			'OUR SIGNATURE METHODS' => 'OS NOSSOS MÉTODOS EXCLUSIVOS',
			'Two techniques. Registered to our name.' => 'Duas técnicas registradas em nosso nome.',
			'Two exclusive, trademarked protocols — developed in-house.' => 'Dois protocolos exclusivos e registados, desenvolvidos pela nossa equipa.',
			'Treatment' => 'Tratamento',
			'Explore VITA®' => 'Conhecer VITA®',
			'Explore Exosome®' => 'Conhecer Exosome®',
			'EST. 2010 · ISTANBUL' => 'DESDE 2010 · ISTAMBUL',
			'EST. 2010 · ISTANBUL, TÜRKİYE' => 'DESDE 2010 · ISTAMBUL, TURQUIA',
			'One of the World’s Most Experienced Hair Restoration Teams' => 'Uma das equipas de restauração capilar mais experientes do mundo',
			'Join the thousands who have come to Estecapelli to regain their confidence.' => 'Junte-se aos milhares de pacientes que escolheram a Estecapelli para recuperar a confiança.',
			'Schedule a Free Consultation' => 'Marcar uma consulta gratuita',
			'Over 50,000+' => 'Mais de 50.000',
			'5-Star Results' => 'Resultados de 5 estrelas',
			'Actual Estecapelli patient. Individual results may vary.' => 'Paciente real da Estecapelli. Os resultados individuais podem variar.',
			'FIFTEEN YEARS' => 'QUINZE ANOS',
			'FOR WOMEN · DISCREET & NATURAL' => 'PARA MULHERES · DISCRETO E NATURAL',
			'Women’s Hair Transplant in Istanbul' => 'Transplante capilar feminino em Istambul',
			'Thinning along the parting, a receding hairline or overall loss of density affects women too. Our female-focused approach restores fullness with unshaven techniques, complete privacy and natural, permanent results.' => 'O enfraquecimento junto à risca, o recuo da linha capilar ou a perda geral de densidade também afetam as mulheres. A nossa abordagem especializada recupera o volume com técnicas sem rapar, total privacidade e resultados naturais e permanentes.',
			'Unshaven, discreet techniques' => 'Técnicas discretas sem rapar',
			'Natural hairline & density design' => 'Desenho natural da linha capilar e da densidade',
			'Female patient privacy at every step' => 'Privacidade da paciente em todas as etapas',
			'Years of Experience' => 'Anos de experiência',
			'Happy Patients' => 'Pacientes satisfeitos',
			'Countries Served' => 'Países atendidos',
			'Support Team' => 'Equipa de apoio',
			'What We Treat' => 'O que tratamos',
			'Pick a field. See our signature treatments.' => 'Escolha uma área e conheça os nossos tratamentos de referência.',
			'Switch between tabs to explore the methods we are best known for in each field of care.' => 'Alterne entre os separadores para conhecer os métodos pelos quais somos reconhecidos em cada área de cuidados.',
			'Previous treatments' => 'Tratamentos anteriores',
			'Next treatments' => 'Tratamentos seguintes',

			// Homepage supporting sections.
			'The Comparison' => 'A comparação',
			'Global standards, Turkish expertise.' => 'Padrões internacionais, experiência turca.',
			'See how our comprehensive approach and advanced techniques set us apart from other clinics.' => 'Descubra como a nossa abordagem completa e as técnicas avançadas nos distinguem de outras clínicas.',
			'Built for patients who refuse to compromise.' => 'Pensado para pacientes que não aceitam compromissos.',
			'At Estecapelli, we go beyond expectations to deliver personalised, high-quality care backed by years of experience and international standards. Our expert team combines innovative methods with a patient-first approach to deliver results that look natural and feel confident.' => 'Na Estecapelli superamos as expectativas com cuidados personalizados e de alta qualidade, apoiados por anos de experiência e padrões internacionais. A nossa equipa combina métodos inovadores com uma abordagem centrada no paciente para alcançar resultados naturais e devolver a confiança.',
			'Experience the Estecapelli difference — advanced techniques, patient-focused care.' => 'Descubra a diferença Estecapelli: técnicas avançadas e cuidados centrados no paciente.',
			'Other Clinics' => 'Outras clínicas',
			'Premium Care' => 'Cuidados premium',
			'Standard Care' => 'Cuidados padrão',
			'5-Star Hotel Accommodation' => 'Alojamento em hotel de 5 estrelas',
			'VIP Transfer Service' => 'Serviço de transporte VIP',
			'Personal Translator' => 'Intérprete pessoal',
			'Latest Technology Implementation' => 'Tecnologia de última geração',
			'Success-Oriented Treatment Planning' => 'Planeamento orientado para o resultado',
			'Innovative Techniques (Exosome, FUE, DHI, VITA)' => 'Técnicas inovadoras (Exosome, FUE, DHI, VITA)',
			'Regenerative Stem Cell Therapy' => 'Terapia regenerativa com células estaminais',
			'Dedicated Post-Op Follow-Up Team' => 'Equipa dedicada ao acompanhamento pós-operatório',
			'Pain-Free Anaesthesia' => 'Anestesia sem dor',
			'Nine details where every choice matters.' => 'Nove detalhes em que cada escolha importa.',
			'Our Expertise' => 'A nossa experiência',
			'Three methods that shape every treatment.' => 'Três métodos que orientam cada tratamento.',
			'Two are exclusively ours. One sets the standard for personalised planning. All three power the results our patients trust us for.' => 'Dois são exclusivamente nossos e um define o padrão do planeamento personalizado. Os três sustentam os resultados pelos quais os pacientes confiam em nós.',
			'Patented · Estecapelli Exclusive' => 'Patenteado · Exclusivo Estecapelli',
			'Premium Hair Transplant Method' => 'Método premium de transplante capilar',
			'Mesenchymal stem-cell support that keeps follicles alive longer.' => 'Apoio com células estaminais mesenquimais que mantém os folículos vivos durante mais tempo.',
			'Follicle survival over 72 hours' => 'Sobrevivência folicular superior a 72 horas',
			'Learn about Exosome FUE' => 'Conhecer Exosome FUE',
			'AI-Powered Diagnosis' => 'Diagnóstico assistido por IA',
			'Millimetric Hair & Scalp Analysis' => 'Análise milimétrica do cabelo e couro cabeludo',
			'AI maps your scalp before a single graft is planned.' => 'A IA analisa o couro cabeludo antes de ser planeado um único enxerto.',
			'Millimetric' => 'Milimétrica',
			'Precision per scalp scan' => 'Precisão em cada análise do couro cabeludo',
			'Learn more about TrichoLab' => 'Saber mais sobre TrichoLab',
			'Signature Protocol · Estecapelli Exclusive' => 'Protocolo exclusivo · Exclusivo Estecapelli',
			'Power Derived from Vitamins' => 'A força das vitaminas',
			'A vitamin-cooled bath that keeps grafts strong out of the body.' => 'Uma solução vitamínica refrigerada que mantém os enxertos fortes fora do corpo.',
			'Cool-Vapor' => 'Vapor frio',
			'Vitamin-nourished grafts' => 'Enxertos nutridos com vitaminas',
			'Learn more about VITA' => 'Saber mais sobre VITA',
			'Real Stories' => 'Histórias reais',
			'Their results speak louder than any ad ever could.' => 'Os resultados falam mais alto do que qualquer anúncio.',
			'Hear, in their own words, how patients from around the world describe their Estecapelli journey — from first consultation to long-term result.' => 'Ouça pacientes de todo o mundo descreverem, pelas suas próprias palavras, o percurso na Estecapelli, desde a primeira consulta até ao resultado a longo prazo.',
			'Grafts' => 'Enxertos',
			'Technique' => 'Técnica',
			'Patient from' => 'Paciente de',
			'More patient stories' => 'Mais histórias de pacientes',
			'On the playlist' => 'Na lista de reprodução',
			'Our Clinic' => 'A nossa clínica',
			'Step inside our Istanbul clinic' => 'Conheça a nossa clínica em Istambul',
			'A Ministry-of-Health licensed, hospital-grade facility in the heart of Istanbul — modern operation rooms, sterile theatres and a permanent on-site medical team.' => 'Uma instalação de nível hospitalar autorizada pelo Ministério da Saúde no coração de Istambul, com salas de operações modernas, ambientes estéreis e uma equipa médica permanente no local.',
			'Clinic walkthrough' => 'Visita à clínica',
			'Hair Transplant Surgery Room' => 'Sala de transplante capilar',
			'TrichoLab Room' => 'Sala TrichoLab',
			'Lobby' => 'Receção',
			'Dental Clinic' => 'Clínica dentária',
			'Our partner hotels' => 'Os nossos hotéis parceiros',
			'Hair Analysis Lab' => 'Laboratório de análise capilar',
			'Your Personalised Hair Plan, Powered by Estecapelli' => 'O seu plano capilar personalizado, desenvolvido pela Estecapelli',
			'I know my hair-loss area' => 'Conheço a minha área de perda capilar',
			'Start self-assessment' => 'Iniciar autoavaliação',
			'Analyse my photos with AI' => 'Analisar as minhas fotografias com IA',
			'Start AI Analysis' => 'Iniciar análise com IA',
			'Back to options' => 'Voltar às opções',
			'Tap the areas where you are losing hair' => 'Toque nas zonas onde está a perder cabelo',
			'Selected areas' => 'Zonas selecionadas',
			'No area selected yet.' => 'Ainda não foi selecionada nenhuma zona.',
			'Your name' => 'O seu nome',
			'Phone / WhatsApp' => 'Telefone / WhatsApp',
			'Your number' => 'O seu número',
			'Your email' => 'O seu e-mail',
			'Get my analysis' => 'Receber a minha análise',
			'Real Patients · Real Results' => 'Pacientes reais · Resultados reais',
			'Choose a hair-transplant technique to browse real patient results.' => 'Escolha uma técnica de transplante capilar para ver resultados de pacientes reais.',
			'Hair transplant techniques' => 'Técnicas de transplante capilar',
			'View all results' => 'Ver todos os resultados',

			// Shared controls, accessibility and utility labels.
			'%1$s grafts · %2$s' => '%1$s enxertos · %2$s',
			'Close video' => 'Fechar vídeo',
			'Back to gallery' => 'Voltar à galeria',
			'Click any photo to open the gallery' => 'Clique numa fotografia para abrir a galeria',
			'Contents' => 'Conteúdo',
			'Enlarge photo' => 'Ampliar fotografia',
			'Go to photo %d' => 'Ir para a fotografia %d',
			'Hover or tap to discover' => 'Passe o cursor ou toque para descobrir',
			'Included' => 'Incluído',
			'Included at Estecapelli' => 'Incluído na Estecapelli',
			'Not included' => 'Não incluído',
			'Open gallery: %s' => 'Abrir galeria: %s',
			'Open the gallery' => 'Abrir a galeria',
			'Play %s video' => 'Reproduzir vídeo de %s',
			'Play %s — %s' => 'Reproduzir %s — %s',
			'Rated %d out of 5' => 'Classificação: %d em 5',
			'Scalp zones' => 'Zonas do couro cabeludo',
			'Scroll left' => 'Deslocar para a esquerda',
			'Scroll right' => 'Deslocar para a direita',
			'Treatments' => 'Tratamentos',
			'Trustpilot rating: 5 out of 5, Excellent' => 'Classificação Trustpilot: 5 em 5, Excelente',
			'Use the arrows to browse · click any photo to enlarge' => 'Utilize as setas para navegar · clique numa fotografia para ampliar',
			'View result %d' => 'Ver resultado %d',
			'Excellent' => 'Excelente',
			'on Trustpilot' => 'no Trustpilot',
			'Google Reviews' => 'Avaliações Google',
			'Estecapelli in numbers' => 'A Estecapelli em números',
			'Patients trust us by the numbers' => 'A confiança dos nossos pacientes em números',
			'Estecapelli versus other clinics — feature comparison' => 'Estecapelli comparada com outras clínicas — comparação de serviços',
			'Internationally Accredited & Certified' => 'Acreditada e certificada internacionalmente',
			'Internationally accredited & certified — Ministry of Health, Certified Medical Travel Agency, HRSA, NACo Award, ISO 13485' => 'Acreditada e certificada internacionalmente — Ministério da Saúde, agência certificada de turismo médico, HRSA, prémio NACo e ISO 13485',
			'Internationally accredited — Ministry of Health, HRSA, NACo, ISO 13485, Certified Medical Travel Agency' => 'Acreditação internacional — Ministério da Saúde, HRSA, NACo, ISO 13485 e agência certificada de turismo médico',
			'Internationally accredited — Ministry of Health, HRSA, NACo, ISO 13485, Certified Medical Travel Agent' => 'Acreditação internacional — Ministério da Saúde, HRSA, NACo, ISO 13485 e agente certificado de turismo médico',

			// About flyout and treatment cards.
			'Aesthetic Excellence, Backed by Medical Trust.' => 'Excelência estética, apoiada pela confiança médica.',
			"From hair restoration to plastic, dental and non-surgical aesthetics — Estecapelli's board-certified doctors deliver hospital-grade care with the precision your transformation deserves." => 'Da restauração capilar à cirurgia plástica, medicina dentária e estética não cirúrgica, os médicos qualificados da Estecapelli prestam cuidados de nível hospitalar com a precisão que a sua transformação merece.',
			'Dr. Mehmet Hanifi Kutlar' => 'Dr. Mehmet Hanifi Kutlar',
			'Medical Director & Co-founder' => 'Diretor médico e cofundador',
			'15+ years in aesthetic medicine' => 'Mais de 15 anos em medicina estética',
			'ACCREDITED CLINIC' => 'CLÍNICA ACREDITADA',
			'Ministry of Health Licensed' => 'Licenciada pelo Ministério da Saúde',
			'ISO 9001 · TURSAB Certified' => 'Certificada pela ISO 9001 · TURSAB',
			'About Estecapelli' => 'Sobre a Estecapelli',
			'About Us' => 'Sobre nós',
			'Who we are and what drives our clinic forward.' => 'Quem somos e o que impulsiona a nossa clínica.',
			'Our Doctors' => 'Os nossos médicos',
			'Meet the surgeons leading every procedure.' => 'Conheça os cirurgiões responsáveis por cada procedimento.',
			'Our Team' => 'A nossa equipa',
			'The full medical and patient-care team behind your treatment.' => 'Toda a equipa médica e de acompanhamento responsável pelo seu tratamento.',
			'Visit Us' => 'Visite-nos',
			'Medical Treatment' => 'Tratamento médico',
			'Hair Transplant Care & Technology' => 'Cuidados e tecnologia de transplante capilar',
			'Micro Sapphire FUE' => 'Micro Sapphire FUE',
			'Sapphire FUE Hair Transplant' => 'Transplante capilar Sapphire FUE',
			'Sapphire-blade precision for natural density and faster healing.' => 'Precisão das lâminas de safira para uma densidade natural e recuperação mais rápida.',
			'A natural and permanent hair transplant technique where follicles are placed via sapphire blades.' => 'Uma técnica de transplante capilar natural e permanente em que os folículos são implantados com lâminas de safira.',
			'DHI Hair Transplant' => 'Transplante capilar DHI',
			'A modern hair transplantation method performed with a Choi pen that allows precise placement.' => 'Um método moderno de transplante capilar realizado com uma caneta Choi, que permite uma implantação precisa.',
			'Choi-pen implantation for precise angle and direction control.' => 'Implantação com caneta Choi para um controlo preciso do ângulo e da direção.',
			'Exosome FUE Hair Transplant' => 'Transplante capilar Exosome FUE',
			'Exosome Treatment' => 'Tratamento Exosome',
			'Cell-regenerating exosomes that keep follicles alive longer.' => 'Exossomas regeneradores que mantêm os folículos vivos durante mais tempo.',
			'Supported by cell-regenerating exosomes, it keeps hair follicles alive for longer-lasting density.' => 'Com o apoio de exossomas regeneradores, mantém os folículos vivos para uma densidade mais duradoura.',
			'VITA Treatment' => 'Tratamento VITA',
			"Estecapelli's signature protocol that revitalises scalp & strands." => 'O protocolo exclusivo da Estecapelli que revitaliza o couro cabeludo e os fios.',
			"Estecapelli's signature method that revitalizes the scalp and strengthens hair." => 'O método exclusivo da Estecapelli que revitaliza o couro cabeludo e fortalece o cabelo.',
			'POPULAR' => 'POPULAR',
			'SIGNATURE' => 'EXCLUSIVO',
			'Rhinoplasty' => 'Rinoplastia',
			'Nose reshaping that refines proportions and function.' => 'Remodelação nasal que aperfeiçoa as proporções e a função.',
			'Nose reshaping surgery that refines proportions and function.' => 'Cirurgia de remodelação nasal que aperfeiçoa as proporções e a função.',
			'BBL (Brazilian Butt Lift)' => 'BBL (lifting brasileiro de glúteos)',
			'Brazilian Butt Lift — natural contouring with fat transfer.' => 'Lifting brasileiro de glúteos: contorno natural com transferência de gordura.',
			'Natural body contouring with fat transfer to the buttocks.' => 'Contorno corporal natural com transferência de gordura para os glúteos.',
			'Facelift' => 'Lifting facial',
			'Face & Neck Lift Surgery' => 'Lifting facial e cervical',
			'Restores facial contour and reduces visible signs of aging.' => 'Restaura o contorno facial e reduz os sinais visíveis de envelhecimento.',
			'Breast Aesthetics' => 'Cirurgia mamária',
			'Augmentation, lift and reduction tailored to your goals.' => 'Aumento, elevação e redução mamária adaptados aos seus objetivos.',
			'Augmentation, lift, and reduction tailored to your goals.' => 'Aumento, elevação e redução mamária adaptados aos seus objetivos.',
			'Abdominoplasty (Tummy Tuck)' => 'Abdominoplastia',
			'Flattens and tightens the abdomen for a smoother profile.' => 'Achata e reafirma o abdómen para um perfil mais harmonioso.',
			'Liposuction' => 'Lipoaspiração',
			'Removes localized fat deposits to reshape the body.' => 'Remove depósitos de gordura localizada para remodelar o corpo.',
			'Gynecomastia' => 'Ginecomastia',
			'Surgical treatment of enlarged male breast tissue.' => 'Tratamento cirúrgico do aumento do tecido mamário masculino.',
			'Obesity Surgeries (Bariatric)' => 'Cirurgia da obesidade (bariátrica)',
			'Bariatric surgery and gastric balloon for sustainable weight loss.' => 'Cirurgia bariátrica e balão gástrico para uma perda de peso sustentável.',
			'Dental Implant' => 'Implante dentário',
			'Dental Implants' => 'Implantes dentários',
			'Permanent replacement for missing teeth with titanium roots.' => 'Substituição permanente de dentes perdidos com raízes de titânio.',
			'Smile Design' => 'Design do sorriso',
			'A bespoke makeover that reshapes your entire smile.' => 'Uma transformação personalizada que redesenha todo o seu sorriso.',
			'A bespoke makeover that reshapes your smile aesthetic.' => 'Uma transformação personalizada que redesenha a estética do seu sorriso.',
			'Hollywood Smile' => 'Sorriso Hollywood',
			'Veneers' => 'Facetas dentárias',
			'Thin porcelain shells for a flawless front-of-tooth finish.' => 'Finas lâminas de porcelana para um acabamento impecável na parte visível dos dentes.',
			'Teeth Whitening' => 'Branqueamento dentário',
			'Professional bleaching for noticeably brighter teeth.' => 'Branqueamento profissional para dentes visivelmente mais claros.',
			'Female Hair Transplant' => 'Transplante capilar feminino',
			'Special for women, dense and natural-looking hair transplant without shaving.' => 'Transplante sem rapar, específico para mulheres, com um resultado denso e natural.',
			'Eyebrow Transplant' => 'Transplante de sobrancelhas',
			'Eyebrow transplantation that gives a naturally curved and full eyebrow shape.' => 'Transplante que proporciona às sobrancelhas uma forma naturalmente curva e preenchida.',
			'Beard Transplant' => 'Transplante de barba',
			'Natural beard and mustache transplantation for sparse or non-existing growth.' => 'Transplante natural de barba e bigode para zonas com crescimento reduzido ou inexistente.',
			'Hair Mesotherapy' => 'Mesoterapia capilar',
			'A vitamin and mineral injection treatment that revitalizes hair follicles.' => 'Tratamento com injeções de vitaminas e minerais que revitaliza os folículos capilares.',
			'Pre-Hair Transplant Period' => 'Período pré-transplante capilar',
			'The preparation and analysis process before hair transplantation.' => 'O processo de preparação e análise antes do transplante capilar.',
			'Post-Hair Transplant Period' => 'Período pós-transplante capilar',
			'The post-procedure recovery and hair care period.' => 'O período de recuperação e cuidados capilares após o procedimento.',
			'AI Analysis by Estecapelli' => 'Análise por IA da Estecapelli',
			'Advanced AI-powered hair analysis system that examines the hair and scalp in detail.' => 'Sistema avançado de análise capilar por IA que examina detalhadamente o cabelo e o couro cabeludo.',
			'We have trained an AI to guide every patient to the best next step — get a first, personalised hair-loss assessment in seconds.' => 'Treinámos uma IA para orientar cada paciente para o melhor passo seguinte: receba em segundos uma primeira avaliação personalizada da queda de cabelo.',
			'Skin Rejuvenation' => 'Rejuvenescimento da pele',
			'Non-surgical protocols for firmer, brighter, healthier skin.' => 'Protocolos não cirúrgicos para uma pele mais firme, luminosa e saudável.',
			'Botox' => 'Botox',
			'Smooths expression lines for a refreshed, rested look.' => 'Suaviza as linhas de expressão para uma aparência renovada e descansada.',
			'Dermal Fillers' => 'Preenchimentos dérmicos',
			'Restores volume to cheeks, jawline and under-eye areas.' => 'Restaura o volume nas maçãs do rosto, linha mandibular e zona abaixo dos olhos.',
			'PRP Treatment' => 'Tratamento PRP',
			'Platelet-rich plasma therapy for skin and hair regeneration.' => 'Terapia com plasma rico em plaquetas para regeneração da pele e do cabelo.',

			// Hair-analysis details and patient-story summaries.
			'We trained our AI on the results of more than 15,000 patients. Learning from their success stories, it analyses your condition and guides you to the treatment that fits you best.' => 'Treinamos nossa Inteligência Artificial com os resultados de mais de 15.000 pacientes. Com base na análise de milhares de casos de sucesso, ela avalia o seu perfil capilar e indica o tratamento mais adequado para alcançar os melhores resultados.',
			'Mark the areas where you are losing hair and tell us about your goals.' => 'Assinale as zonas onde está a perder cabelo e fale-nos dos seus objetivos.',
			'We take your photos and analyse your hair condition with an AI we have specially trained.' => 'Analisamos as suas fotografias e a condição do seu cabelo com uma IA especialmente treinada.',
			'A denser frontal hairline and a fuller crown.' => 'Uma linha capilar frontal mais densa e uma coroa mais preenchida.',
			'A natural hairline with balanced front-to-crown density.' => 'Uma linha capilar natural com densidade equilibrada da frente à coroa.',
			'Frontal density extended through the mid-scalp.' => 'Densidade frontal prolongada pela zona média do couro cabeludo.',
			'Frontal density extended toward the mid-scalp.' => 'Densidade frontal prolongada até à zona média do couro cabeludo.',
			'Full-coverage restoration in a single session.' => 'Restauração com cobertura total numa única sessão.',
			'Hairline redesigned, density restored from front to crown.' => 'Linha capilar redesenhada e densidade restaurada da frente à coroa.',
			'Stronger density across front, mid-scalp and crown.' => 'Maior densidade nas zonas frontal, média e na coroa.',
			'Ireland' => 'Irlanda',
			'Scotland' => 'Escócia',
			'England' => 'Inglaterra',
			'Canada' => 'Canadá',
			'Alexandre came to our clinic from Ireland to redesign his hairline and improve density across the frontal area, mid-scalp and crown. Based on his desired hair model and donor capacity, we prioritised a natural, face-appropriate hairline first, then a balanced, homogeneous distribution through the mid-scalp and crown. The procedure was performed with the DHI Vita technique in a single session, transplanting 5,000 grafts in total — around 3,000 in the frontal area and hairline and roughly 2,000 across the mid-scalp and crown. The operation progressed normally, with PRP support applied at the end.' => 'Alexandre veio da Irlanda para redesenhar a linha capilar e melhorar a densidade nas zonas frontal, média e na coroa. De acordo com o modelo pretendido e a capacidade dadora, priorizámos uma linha capilar natural e adequada ao rosto, seguida de uma distribuição equilibrada e homogénea. A técnica DHI Vita foi realizada numa única sessão com 5.000 enxertos: cerca de 3.000 na zona frontal e linha capilar e aproximadamente 2.000 nas zonas média e coroa. O procedimento decorreu normalmente e terminou com apoio PRP.',
			'Craig came to our clinic from Scotland to improve his hairline and increase density across the frontal area and crown. Based on his hair model and consultation plan, we prioritised a denser, more natural frontal hairline first, then a homogeneous graft distribution through the top and crown to cover the visible gaps. The procedure was performed with the FUE Vita technique in a single session, transplanting 5,400 grafts in total. Grafts were extracted evenly from the donor area with good quality, then implanted with attention to natural direction, density balance and overall coverage. The operation progressed normally, with PRP support applied at the end.' => 'Craig veio da Escócia para melhorar a linha capilar e aumentar a densidade na zona frontal e na coroa. De acordo com o seu modelo capilar e plano de consulta, priorizámos uma linha frontal mais densa e natural e distribuímos depois os enxertos de forma homogénea pela zona superior e coroa. A técnica FUE Vita foi realizada numa única sessão com 5.400 enxertos de boa qualidade, implantados respeitando a direção natural, o equilíbrio da densidade e a cobertura geral. O procedimento terminou com apoio PRP.',
			'Dale came to our clinic from England to increase density across the frontal area, mid-scalp and crown. Based on his consultation plan and donor capacity, we focused on building stronger density in the front and crown first, while balancing the mid-scalp for an even result. The procedure was performed with the Vita protocol in a single session, transplanting 4,500 grafts in total. With a good-density donor area, the grafts were distributed to the planned coverage needs — higher density prioritised in the frontal area and crown, and the mid-scalp reinforced for a more homogeneous overall look. PRP support was applied at the end of the procedure.' => 'Dale veio de Inglaterra para aumentar a densidade nas zonas frontal, média e na coroa. De acordo com o plano e a capacidade dadora, reforçámos primeiro a zona frontal e a coroa e equilibrámos a zona média para obter um resultado uniforme. O protocolo Vita foi realizado numa única sessão com 4.500 enxertos, distribuídos segundo as necessidades de cobertura e com maior densidade nas zonas frontal e coroa. No final foi aplicado apoio PRP.',
			'Danny came to our clinic from England to improve density in his frontal area and extend coverage toward the mid-scalp. Based on his consultation plan, we prioritised harvesting the maximum number of grafts and building a dense, natural-looking result in the thinning zones, aiming for good density up to the midsection. The procedure was performed with the FUE Vita technique in a single session, transplanting 5,000 grafts in total. Grafts were extracted homogeneously from the donor area with good quality, then implanted with attention to natural direction, density balance and frontal-to-mid-scalp coverage. The operation progressed normally, with PRP support applied at the end.' => 'Danny veio de Inglaterra para melhorar a densidade frontal e prolongar a cobertura até à zona média. De acordo com o plano, priorizámos a extração do maior número possível de enxertos e um resultado denso e natural nas zonas de rarefação. A técnica FUE Vita foi realizada numa única sessão com 5.000 enxertos de boa qualidade, extraídos de forma homogénea e implantados respeitando a direção natural, o equilíbrio da densidade e a cobertura frontal e média. O procedimento terminou com apoio PRP.',
			'Pascal came to our clinic from Canada to improve density in his frontal area and extend coverage toward the mid-scalp. Based on his consultation plan and donor capacity, we planned maximum graft extraction to cover the frontal area up to the midsection with good density, with a second session planned for full coverage. The procedure was performed with the Exosome FUE technique, transplanting 5,000 grafts in this session. Grafts were extracted homogeneously from the donor area with good hair quality, then implanted with attention to natural direction, density balance and frontal-to-mid-scalp coverage. PRP support was applied at the end of the procedure.' => 'Pascal veio do Canadá para melhorar a densidade frontal e prolongar a cobertura até à zona média. De acordo com o plano e a capacidade dadora, planeámos a extração máxima para cobrir a zona frontal até à parte média com boa densidade, ficando prevista uma segunda sessão para completar a cobertura. Nesta sessão foram implantados 5.000 enxertos com a técnica Exosome FUE, respeitando a direção natural e o equilíbrio da densidade. No final foi aplicado apoio PRP.',
			'Ricardo came to our clinic from Ireland to restore a natural hairline and improve density across the frontal area, temples and a small thinning area on the crown. Based on his consultation plan and his wish for a natural-looking result, we adjusted the frontal line to his facial structure and planned dense implantation in the front, with additional grafts placed in the temples and crown for overall balance. The procedure was performed with the DHI Vita technique in a single session, transplanting approximately 4,200 grafts. The grafts were extracted with good hair quality, then implanted with attention to natural direction, symmetry, density balance and frontal-to-crown coverage. The operation progressed normally, with PRP support at the end.' => 'Ricardo veio da Irlanda para restaurar uma linha capilar natural e melhorar a densidade na zona frontal, nas têmporas e numa pequena área da coroa. Adaptámos a linha frontal à estrutura do rosto e planeámos uma implantação densa à frente, com enxertos adicionais nas têmporas e na coroa. A técnica DHI Vita foi realizada numa única sessão com cerca de 4.200 enxertos, implantados respeitando a direção natural, a simetria e o equilíbrio da densidade. O procedimento terminou com apoio PRP.',
			'Sam came to our clinic from Ireland to improve his hairline and increase overall density across the frontal area, mid-scalp and crown. Based on his consultation plan and his wish for full coverage in a single session, we planned maximum graft extraction and focused on building good density in the frontal hairline first, then distributed the remaining grafts through the mid-scalp and crown for balanced coverage. The procedure was performed with the FUE Vita technique in a single session, transplanting 6,200 grafts in total. Grafts were extracted homogeneously from the donor area with good quality, then implanted with attention to natural direction, density balance and overall coverage. The operation progressed normally, with PRP support at the end.' => 'Sam veio da Irlanda para melhorar a linha capilar e aumentar a densidade geral nas zonas frontal, média e na coroa. Para obter cobertura total numa única sessão, planeámos a extração máxima, priorizámos uma boa densidade na linha frontal e distribuímos os restantes enxertos pelas zonas média e coroa. A técnica FUE Vita foi realizada com 6.200 enxertos de boa qualidade, implantados respeitando a direção natural e o equilíbrio da densidade. O procedimento terminou com apoio PRP.',

			// Signature-card long descriptions.
			'Our patented Exosome Treatment is derived from mesenchymal stem cells found in the umbilical cord — designed to lift hair-follicle survival to 98% over 72 hours, with faster recovery, stronger growth, and naturally lasting results.' => 'O nosso tratamento Exosome patenteado deriva de células estaminais mesenquimais do cordão umbilical e foi concebido para elevar a sobrevivência folicular a 98% durante mais de 72 horas, com recuperação mais rápida, crescimento mais forte e resultados naturais e duradouros.',
			'TrichoLab examines your hair and scalp with millimetric accuracy — measuring follicle density, thickness, donor capacity, and loss patterns — so every graft is planned for your unique anatomy and the result feels naturally yours.' => 'O TrichoLab examina o cabelo e o couro cabeludo com precisão milimétrica, medindo a densidade folicular, a espessura, a capacidade dadora e os padrões de queda, para que cada enxerto seja planeado de acordo com a sua anatomia e o resultado pareça naturalmente seu.',
			'Grafts lose strength the moment they leave the body. Our VITA Protocol bathes them in a specially formulated vitamin cocktail with cool-vapor application — keeping every follicle alive, nourished, and resilient until placement.' => 'Os enxertos começam a perder vitalidade assim que saem do corpo. O nosso protocolo VITA envolve-os num cocktail vitamínico especialmente formulado, aplicado com vapor frio, mantendo cada folículo vivo, nutrido e resistente até à implantação.',
			// Floating WhatsApp button + fake WhatsApp chat popup.
			'WhatsApp chat' => 'Chat do WhatsApp',
			'Close chat' => 'Fechar o chat',
			'online' => 'online',
			'Today' => 'Hoje',
			'Hi! 👋 Welcome to Estecapelli. Tell us which treatment you are interested in and we will get back to you within minutes.' => 'Olá! 👋 Bem-vindo à Estecapelli. Diga-nos qual o tratamento que lhe interessa e responderemos em poucos minutos.',
			'Type a message' => 'Escreva uma mensagem',
			'Write your message first' => 'Escreva primeiro a sua mensagem',
			'Send' => 'Enviar',
			'Confirm your message' => 'Confirme a sua mensagem',
			'This message will be sent to our WhatsApp:' => 'Esta mensagem será enviada para o nosso WhatsApp:',
			'Cancel' => 'Cancelar',
			// 404 page.
			'This page could not be found' => 'Não foi possível encontrar esta página',
			'The page you are looking for may have been moved or no longer exists. You can head back to the homepage, or pick one of the sections below.' => 'A página que procura pode ter sido movida ou já não existe. Pode voltar à página inicial ou escolher uma das secções abaixo.',
			'Back to homepage' => 'Voltar à página inicial',
			'Contact us' => 'Contacte-nos',
			'Popular sections' => 'Secções principais',
			'Site sections' => 'Secções do site',
		);
	}

	return $strings[ $text ] ?? $translation;
}

add_filter( 'ngettext', 'estecapelli_pt_ngettext_fallback', 25, 5 );
/** Translate pluralised template labels without changing article content. */
function estecapelli_pt_ngettext_fallback( $translation, $single, $plural, $number, $domain ) {
	if (
		'estecapelli' === $domain &&
		estecapelli_is_portuguese_request() &&
		in_array( $translation, array( $single, $plural ), true )
	) {
		if ( '%d min read' === $single && '%d min read' === $plural ) {
			return '%d min de leitura';
		}
		if ( '%d star' === $single && '%d stars' === $plural ) {
			return 1 === (int) $number ? '%d estrela' : '%d estrelas';
		}
	}

	return $translation;
}
