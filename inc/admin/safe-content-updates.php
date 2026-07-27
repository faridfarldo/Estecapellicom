<?php
/**
 * Explicit, page-scoped content patches with preview, backup and rollback.
 *
 * Unlike the retired importers, this tool never runs on admin_init and never
 * writes a complete ACF flexible-content payload. An administrator must review
 * and apply one immutable patch at a time. Every target language, layout, row
 * and existing value is verified before the first database write.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Exact operations shared by the affected translated VITA pages. */
function estecapelli_safe_patch_vita_empty_step_operations() {
	return array(
		array(
			'target' => 'layout_fields',
			'layout' => 'stepbook',
			'fields' => array(
				'items' => array( 'before' => array( '4', 4 ), 'after' => '3' ),
			),
		),
		array(
			'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 3,
			'fields' => array(
				'eyebrow'   => array( 'before' => '', 'after' => '' ),
				'title'     => array( 'before' => '', 'after' => '' ),
				'body'      => array( 'before' => '', 'after' => '' ),
				'icon_file' => array( 'before' => array( '', '0' ), 'after' => '' ),
				'image'     => array( 'before' => array( '', '0' ), 'after' => '' ),
				'video_url' => array( 'before' => '', 'after' => '' ),
			),
		),
	);
}

/** Remove the trailing Step Book row by decrementing its `items` row count. */
function estecapelli_safe_patch_remove_last_step_operation( $current_count ) {
	$current_count = (int) $current_count;
	return array(
		'target' => 'layout_fields',
		'layout' => 'stepbook',
		'fields' => array(
			'items' => array(
				'before' => array( (string) $current_count, $current_count ),
				'after'  => (string) ( $current_count - 1 ),
			),
		),
	);
}

/** Build the exact, language-specific VITA three-to-four-step migration. */
function estecapelli_safe_patch_vita_four_step_operations( $copy ) {
	$old_items = array_values( $copy['old_items'] );
	$image_url = 'https://estecapelli.com/wp-content/uploads/2026/06/prp.webp';

	return array(
		array(
			'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 0,
			'fields' => array(
				'eyebrow' => array( 'before' => $old_items[0]['eyebrow'], 'after' => $old_items[0]['eyebrow'] ),
				'title'   => array( 'before' => $old_items[0]['title'], 'after' => $copy['new_first']['title'] ),
				'body'    => array( 'before' => $old_items[0]['body'], 'after' => $copy['new_first']['body'] ),
			),
		),
		array(
			'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
			'fields' => array(
				'eyebrow' => array( 'before' => $old_items[1]['eyebrow'], 'after' => $old_items[1]['eyebrow'] ),
				'title'   => array( 'before' => $old_items[1]['title'], 'after' => $old_items[0]['title'] ),
				'body'    => array( 'before' => $old_items[1]['body'], 'after' => $old_items[0]['body'] ),
			),
		),
		array(
			'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 2,
			'fields' => array(
				'eyebrow' => array( 'before' => $old_items[2]['eyebrow'], 'after' => $old_items[2]['eyebrow'] ),
				'title'   => array( 'before' => $old_items[2]['title'], 'after' => $old_items[1]['title'] ),
				'body'    => array( 'before' => $old_items[2]['body'], 'after' => $old_items[1]['body'] ),
			),
		),
		array(
			'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 3,
			'fields' => array(
				'eyebrow'   => array( 'before' => '', 'after' => $copy['stage_4'] ),
				'title'     => array( 'before' => '', 'after' => $old_items[2]['title'] ),
				'body'      => array( 'before' => '', 'after' => $old_items[2]['body'] ),
				'icon_file' => array( 'before' => array( '', '0' ), 'after' => '' ),
				'image'     => array( 'before' => array( '', '0' ), 'after' => '' ),
				'image_url' => array( 'before' => '', 'after' => $image_url ),
				'video_url' => array( 'before' => '', 'after' => '' ),
			),
		),
		array(
			'target' => 'layout_fields',
			'layout' => 'stepbook',
			'fields' => array(
				'lead'  => array( 'before' => $copy['lead_before'], 'after' => $copy['lead_after'] ),
				'items' => array( 'before' => array( '3', 3, '4', 4 ), 'after' => '4' ),
			),
		),
	);
}

/** Build the exact Step 2 copy replacement for the female transplant page. */
function estecapelli_safe_patch_female_painless_operation( $before_title, $before_body, $after_title, $after_body ) {
	return array(
		array(
			'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
			'fields' => array(
				'title' => array( 'before' => $before_title, 'after' => $after_title ),
				'body'  => array( 'before' => $before_body, 'after' => $after_body ),
			),
		),
	);
}

/** Expand the five Turkish Exosome FUE Step Book descriptions in place. */
function estecapelli_safe_patch_exosome_rich_tr_step_operations() {
	$pairs = array(
		array(
			'before' => '<p>Sağlıklı greftler, ultra ince mikromotorlar kullanılarak güvenli donör alanından tek tek çıkarılır.</p>',
			'after'  => '<p>Sağlıklı greftler, saçların genetik olarak dökülmeye dirençli olduğu ense ve başın yan kısımlarındaki güvenli donör bölgeden tek tek alınır. Cerrahlarımız her foliküler üniteyi kesi veya dikiş olmadan çıkarmak için ultra ince mikromotor uçları kullanır; böylece donör bölge hızla iyileşir ve çizgisel bir iz kalmaz.</p>',
		),
		array(
			'before' => '<p>Hasatlanan greftler, onları besleyen ve ekime hazırlayan özel formüle edilmiş bir eksozom solüsyonuna yerleştirilir.</p>',
			'after'  => '<p>Greftler alındıktan hemen sonra sıradan bir bekletme sıvısı yerine özel olarak formüle edilmiş eksozom solüsyonunda muhafaza edilir. Bu rejeneratif sinyal parçacıkları vücut dışındaki folikülleri besler ve korur, bekleme süresinin yarattığı stresi azaltır ve nakledildikten sonra yaşamlarını sürdürüp sağlıklı biçimde gelişmeleri için hazırlar.</p>',
		),
		array(
			'before' => '<p>Alıcı alanı, en doğal açı ve yön için safir uçlu aletler kullanılarak hassas kanallarla hazırlanır.</p>',
			'after'  => '<p>Alıcı bölge, doğal saç çizginiz ve yüz oranlarınız dikkate alınarak planlanır, ardından safir uçlu aletlerle mikrokanallar açılır. Bu pürüzsüz ve hassas mikrokanallar her folikülün derinliğini, açısını ve yönünü tam olarak kontrol eder; nihai sonucun doğal yoğunlukta ve fark edilemeyecek kadar doğal görünmesini sağlayan da budur.</p>',
		),
		array(
			'before' => '<p>Eksozomla güçlendirilmiş foliküller, doğal yoğunluk ve yön için tek tek implante edilir.</p>',
			'after'  => '<p>Eksozomla güçlendirilmiş foliküller, çevredeki saçların doğal büyüme düzenine uyularak hazırlanan kanallara tek tek yerleştirilir. Açıya, yöne ve aralığa gösterilen özen, dengeli bir kapatıcılık ile uzadıkça kendi saçınız gibi görünen ve davranan bir sonuç sağlar.</p>',
		),
		array(
			'before' => '<p>Hastalar aynı gün evlerine dönebilir; ekibimiz iyileşme sürecinin her aşamasında kendilerine rehberlik eder.</p>',
			'after'  => '<p>Çoğu hasta ayrıntılı bakım talimatları ve kişisel iyileşme planıyla aynı gün evine döner. Hafif kızarıklık ve kabuklanma ilk hafta içinde azalır; ekilen saçlar yeniden uzamadan önce dökülür. Ekibimiz süreç boyunca sizinle iletişimde kalır; ilk yıkamadan yaklaşık üçüncü ayda görülen yeni çıkışlara ve on ikinci ayda tamamlanan sonuca kadar size rehberlik eder.</p>',
		),
	);

	$operations = array();
	foreach ( $pairs as $row_index => $pair ) {
		$operations[] = array(
			'layout'    => 'stepbook',
			'repeater'  => 'items',
			'row_index' => $row_index,
			'fields'    => array(
				'body' => $pair,
			),
		);
	}

	return $operations;
}

/** Target the Exosome comparison intro without touching its shared ACF image. */
function estecapelli_safe_patch_exosome_localized_image_operation( $image_path ) {
	return array(
		array(
			'target'       => 'layout_fields',
			'layout'       => 'intro',
			'layout_index' => 4,
			'fields'       => array(
				'localized_image_url' => array( 'before' => '', 'after' => $image_path ),
			),
		),
	);
}

/** Target the VITA science intro without touching its shared ACF image. */
function estecapelli_safe_patch_vita_localized_science_image_operation( $image_path ) {
	return array(
		array(
			'target'       => 'layout_fields',
			'layout'       => 'intro',
			'layout_index' => 2,
			'fields'       => array(
				'localized_image_url' => array( 'before' => '', 'after' => $image_path ),
			),
		),
	);
}

/** Update the first VITA intro from the shared English video to one locale. */
function estecapelli_safe_patch_vita_localized_video_operation( $video_url ) {
	$english_video = array(
		'8C9DLaNJynU',
		'https://youtube.com/shorts/8C9DLaNJynU',
		'https://www.youtube.com/shorts/8C9DLaNJynU',
		'https://youtube.com/shorts/8C9DLaNJynU?feature=share',
		'https://www.youtube.com/shorts/8C9DLaNJynU?feature=share',
		'https://youtu.be/8C9DLaNJynU',
	);

	return array(
		array(
			'target'       => 'layout_fields',
			'layout'       => 'intro',
			'layout_index' => 1,
			'fields'       => array(
				'video_url' => array( 'before' => $english_video, 'after' => $video_url ),
			),
		),
	);
}

/** Update the first Exosome intro from the shared English video to one locale. */
function estecapelli_safe_patch_exosome_localized_video_operation( $video_url ) {
	$english_video = array(
		'6_OK4rQ9cxE',
		'https://www.youtube.com/watch?v=6_OK4rQ9cxE',
		'https://youtube.com/watch?v=6_OK4rQ9cxE',
		'https://www.youtube.com/shorts/6_OK4rQ9cxE',
		'https://youtube.com/shorts/6_OK4rQ9cxE',
		'https://youtu.be/6_OK4rQ9cxE',
	);

	return array(
		array(
			'target'       => 'layout_fields',
			'layout'       => 'intro',
			'layout_index' => 2,
			'fields'       => array(
				'video_url' => array( 'before' => $english_video, 'after' => $video_url ),
			),
		),
	);
}

/** Build one immutable before/after field pair for the Portuguese review. */
function estecapelli_safe_patch_pt_review_pair( $before, $after ) {
	return array( 'before' => $before, 'after' => $after );
}

/** Build one exact flexible-layout field operation for the Portuguese review. */
function estecapelli_safe_patch_pt_review_layout( $layout, $layout_index, $fields ) {
	return array(
		'target'       => 'layout_fields',
		'layout'       => $layout,
		'layout_index' => $layout_index,
		'fields'       => $fields,
	);
}

/** Build one exact repeater-row operation for the Portuguese review. */
function estecapelli_safe_patch_pt_review_row( $layout, $layout_index, $row_index, $fields ) {
	return array(
		'layout'       => $layout,
		'layout_index' => $layout_index,
		'repeater'     => 'items',
		'row_index'    => $row_index,
		'fields'       => $fields,
	);
}

/** Exact, page-scoped operations from the 26-page Portuguese review document. */
function estecapelli_safe_patch_pt_review_operations( $source_slug ) {
	$p = 'estecapelli_safe_patch_pt_review_pair';

	switch ( $source_slug ) {
		case 'exosome-fue-hair-transplant':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'hero', 0, array(
					'lead' => $p(
						'A terapia patenteada da Estecapelli integra exossomas regenerativos para otimizar os resultados do transplante capilar. Graças ao apoio dos exossomas, esta técnica ajuda a conservar a vitalidade dos folículos até 72 horas, favorecendo uma maior sobrevivência dos enxertos e uma recuperação mais eficaz.',
						'A terapia patenteada da Estecapelli integra exossomos regenerativos para otimizar os resultados do transplante capilar. Graças ao apoio dos exossomos, esta técnica ajuda a conservar a vitalidade dos folículos até 72 horas, favorecendo uma maior sobrevivência dos enxertos e uma recuperação mais eficaz.'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'stepbook', 3, array(
					'lead' => $p(
						'Um tratamento de vanguarda que combina a precisão do FUE com o poder regenerativo da tecnologia de exossomas. Consulte cada etapa em seguida.',
						'Um tratamento de vanguarda que combina a precisão do FUE com o poder regenerativo da tecnologia de exossomos. Consulte cada etapa em seguida.'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'stepbook', 3, 1, array(
					'title' => $p( 'Reforço na solução de exossomas', 'Exossomos' ),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'intro', 4, array(
					'body' => $p(
						'<p>O Exosome FUE representa o futuro da restauração capilar: a precisão do FUE combina-se com uma terapia de exossomas de última geração. A técnica pode elevar a sobrevivência folicular até 98 % durante 72 horas, acelerar a recuperação, favorecer um crescimento mais forte e oferecer resultados naturais e duradouros.</p>',
						'<p>O Exosome FUE representa o futuro da restauração capilar: a precisão do FUE combina-se com uma terapia de exossomos de última geração. A técnica pode elevar a sobrevivência folicular até 98 % durante 72 horas, acelerar a recuperação, favorecer um crescimento mais forte e oferecer resultados naturais e duradouros.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 7, 0, array(
					'answer' => $p(
						'<p>O Exosome FUE segue a mesma técnica FUE de referência, na qual os folículos são extraídos e implantados individualmente, mas acrescenta terapia com exossomas para favorecer a recuperação e o crescimento. Os exossomas são pequenas vesículas derivadas de células, ricas em fatores de crescimento, que enviam sinais regenerativos aos folículos e aos tecidos circundantes. Deste modo combina-se a fiabilidade comprovada do FUE com um apoio biológico dirigido à recuperação dos enxertos.</p>',
						'<p>O Exosome FUE segue a mesma técnica FUE de referência, na qual os folículos são extraídos e implantados individualmente, mas acrescenta terapia com exossomos para favorecer a recuperação e o crescimento. Os exossomos são pequenas vesículas derivadas de células, ricas em fatores de crescimento, que enviam sinais regenerativos aos folículos e aos tecidos circundantes. Deste modo combina-se a fiabilidade comprovada do FUE com um apoio biológico dirigido à recuperação dos enxertos.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 7, 2, array(
					'answer' => $p(
						'<p>A intervenção é realizada com anestesia local: permanecerá acordado mas confortável e não sentirá dor durante a sessão, que costuma durar entre seis e oito horas consoante o número de enxertos. Os folículos são extraídos da zona dadora, reforçados com a solução de exossomas e implantados seguindo uma linha frontal natural acordada previamente. Poderá regressar ao hotel ou a sua casa no mesmo dia.</p>',
						'<p>A intervenção é realizada com anestesia local: permanecerá acordado mas confortável e não sentirá dor durante a sessão, que costuma durar entre seis e oito horas consoante o número de enxertos. Os folículos são extraídos da zona dadora, reforçados com a solução de exossomos e implantados seguindo uma linha frontal natural acordada previamente. Poderá regressar ao hotel ou a sua casa no mesmo dia.</p>'
					),
				) ),
			);

		case 'vita-treatment':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'form', 7, array(
					'lead' => $p(
						'Partilhe connosco alguns dados e a nossa equipa responder-lhe-á com um plano personalizado, explicar-lhe-á como o protocolo VITA pode melhorar a sobrevivência dos enxertos e enviar-lhe-á um orçamento completo sem compromisso, normalmente em poucas horas.',
						'Partilhe connosco alguns dados e a nossa equipa responderá com um plano personalizado, explicará como o protocolo VITA pode melhorar a sobrevivência dos enxertos e enviar-lhe-á um orçamento completo sem compromisso, normalmente em poucas horas.'
					),
				) ),
			);

		case 'sapphire-fue-hair-transplant':
			return array(
				estecapelli_safe_patch_pt_review_row( 'stepbook', 3, 3, array(
					'body' => $p(
						'<p>Os canais são criados com lâminas de ponta de safira; esta é a etapa mais determinante para obter uma densidade, uma angulação e uma direção naturais. O bordo de safira, mais liso e afiado, produz incisões mais finas que permitem aproximar os enxertos, reduzir o traumatismo dos tecidos e acelerar a recuperação.</p>',
						'<p>Os canais são criados com lâminas de ponta de safira; esta é a etapa mais determinante para obter densidade, angulação e direção natural. O bordo de safira, mais liso e afiado, produz incisões mais finas que permitem aproximar os enxertos, reduzir o traumatismo dos tecidos e acelerar a recuperação.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'candidate', 4, array(
					'title' => $p( 'Porquê escolher a Estecapelli e a Turquia para o Sapphire FUE', 'Por quê escolher a Estecapelli e a Turquia para o Sapphire FUE' ),
				) ),
				estecapelli_safe_patch_pt_review_row( 'candidate', 4, 3, array(
					'label' => $p( 'Elevada densidade e aspeto natural', 'Alta densidade e aspecto natural' ),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'intro', 5, array(
					'body' => $p(
						'<p><strong>Canais limpos e regulares.</strong> Enquanto o FUE tradicional utiliza lâminas de aço, o Sapphire FUE cria canais extremamente precisos através de lâminas de ponta de safira que se mantêm afiadas durante mais tempo. Isto permite realizar incisões mais pequenas e limpas, reduzindo ao mínimo o traumatismo dos tecidos e a hemorragia. A colocação precisa dos enxertos garante um crescimento e uma densidade naturais, além de favorecer uma recuperação mais rápida e confortável.</p><p><strong>Linha frontal natural.</strong> O Sapphire FUE é mais eficaz do que os métodos tradicionais para obter um aspeto natural. As lâminas de safira criam canais que respeitam a direção de crescimento dos folículos, proporcionando uma linha frontal mais natural. Além disso, permitem alcançar uma densidade máxima e um resultado mais povoado.</p><p><strong>Menos cicatrizes.</strong> O Sapphire FUE deixa menos cicatrizes do que as técnicas tradicionais. Como as lâminas de safira conservam o seu gume, as incisões são mais pequenas e as marcas após a recuperação são mínimas. Estes sinais desaparecem rapidamente.</p>',
						'<p><strong>Canais limpos e regulares.</strong> Enquanto o FUE tradicional utiliza lâminas de aço, o Sapphire FUE cria canais extremamente precisos através de lâminas de ponta de safira que se mantêm afiadas durante mais tempo. Isto permite realizar incisões menores e mais limpas, reduzindo ao mínimo o traumatismo dos tecidos e a hemorragia. A colocação precisa dos enxertos garante crescimento e densidade naturais, além de favorecer uma recuperação mais rápida e confortável.</p><p><strong>Linha frontal natural.</strong> O Sapphire FUE é mais eficaz do que os métodos tradicionais para obter um aspeto natural. As lâminas de safira criam canais que respeitam a direção de crescimento dos folículos, proporcionando uma linha frontal mais natural. Além disso, permitem alcançar uma densidade máxima e um resultado mais povoado.</p><p><strong>Menos cicatrizes.</strong> O Sapphire FUE deixa menos cicatrizes do que as técnicas tradicionais. Como as lâminas de safira conservam o seu gume, as incisões menores e as marcas após a recuperação são mínimas. Estes sinais desaparecem rapidamente.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 8, 0, array(
					'answer' => $p(
						'<p>Ambos utilizam o mesmo método FUE, folículo a folículo; a diferença está na lâmina. O FUE padrão abre os canais recetores com lâminas de aço, enquanto o Sapphire FUE utiliza lâminas de ponta de safira que se mantêm afiadas e criam incisões mais pequenas e regulares. Isto permite colocar os enxertos mais próximos para aumentar a densidade, com menor traumatismo dos tecidos, uma linha frontal mais natural, cicatrizes mínimas e uma recuperação mais rápida.</p>',
						'<p>Ambos utilizam o mesmo método FUE, folículo a folículo; a diferença está na lâmina. O FUE padrão abre os canais receptores com lâminas de aço, enquanto o Sapphire FUE utiliza lâminas de ponta de safira que se mantêm afiadas e criam micro incisões regulares. Isto permite colocar os enxertos mais próximos para aumentar a densidade, com menor traumatismo dos tecidos, uma linha frontal mais natural, cicatrizes mínimas e uma recuperação mais rápida.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 8, 5, array(
					'answer' => $p(
						'<p>Sim. Os folículos são extraídos de zonas geneticamente resistentes à queda, pelo que continuam a crescer de forma permanente. Os canais de safira seguem a direção natural de crescimento para criar uma linha frontal e uma densidade completamente naturais; poderá cortar, lavar e pentear o seu cabelo normalmente. O cabelo existente não transplantado pode continuar a enfraquecer com o tempo, pelo que poderão ser recomendados tratamentos de apoio para conservar o resultado global.</p>',
						'<p>Sim. Os folículos são extraídos de zonas geneticamente resistentes à queda, pelo que continuam a crescer de forma permanente. Os canais de safira seguem a direção natural de crescimento para criar uma linha frontal e densidade completamente naturais; poderá cortar, lavar e pentear o seu cabelo normalmente. O cabelo existente não transplantado pode continuar a enfraquecer com o tempo, pelo que poderão ser recomendados tratamentos de apoio para conservar o resultado global.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'gallery', 9, array(
					'lead' => $p(
						'Veja a densidade e as linhas frontais naturais que os nossos pacientes alcançam com o Sapphire FUE. Cada resultado é planeado segundo o seu padrão de crescimento para se integrar harmoniosamente com o cabelo existente e oferecer um aspeto completamente natural.',
						'Veja a densidade e as linhas frontais naturais que os nossos pacientes alcançam com o Sapphire FUE. Cada resultado é planeado segundo o seu padrão de crescimento para se integrar harmoniosamente com o cabelo existente e oferecer um aspecto completamente natural.'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'form', 10, array(
					'lead' => $p(
						'Partilhe connosco alguns dados e a nossa equipa responder-lhe-á com um plano Sapphire FUE personalizado, uma estimativa de enxertos e um orçamento completo sem compromisso, normalmente em poucas horas.',
						'Partilhe conosco alguns dados e a nossa equipa responderá com um plano Sapphire FUE personalizado, uma estimativa de enxertos e um orçamento completo sem compromisso, normalmente em poucas horas.'
					),
				) ),
			);

		case 'dhi-hair-transplant':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'intro', 2, array(
					'body' => $p(
						'<p>DHI — Direct Hair Implantation ou implantação capilar direta — é uma técnica moderna e muito eficaz de transplante capilar. Os folículos extraídos são carregados num implantador Choi especial e colocados diretamente na zona recetora sem abrir previamente canais independentes. Isto permite ao cirurgião controlar com precisão a angulação, a direção e a profundidade de cada enxerto, conseguindo uma implantação densa, uma manipulação mínima e uma recuperação rápida.</p>',
						'<p>DHI — Direct Hair Implantation ou implantação capilar direta — é uma técnica moderna e muito eficaz de transplante capilar. Os folículos extraídos são carregados num implantador Choi especial e colocados diretamente na zona receptora sem abrir previamente canais independentes. Isto permite ao cirurgião controlar com precisão a angulação, a direção e a profundidade de cada enxerto, conseguindo uma implantação densa, uma manipulação mínima e uma recuperação rápida.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'stepbook', 3, 2, array(
					'body' => $p(
						'<p>Os folículos saudáveis são extraídos um a um da zona dadora segura através de micromotores ultrafinos e carregados imediatamente nos implantadores Choi. Assim reduzimos ao mínimo o tempo que os enxertos permanecem fora do couro cabeludo e conservamos a sua força e viabilidade.</p>',
						'<p>Os folículos saudáveis são extraídos um a um da zona doadora segura através de micromotores ultrafinos e carregados imediatamente nos implantadores Choi. Assim reduzimos ao mínimo o tempo que os enxertos permanecem fora do couro cabeludo e conservamos a sua força e viabilidade.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'candidate', 4, array(
					'title' => $p( 'Porque é a Estecapelli a melhor escolha para o DHI', 'Por quê é a Estecapelli a melhor escolha para o DHI' ),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 7, 0, array(
					'answer' => $p(
						'<p>Ambas as técnicas extraem os folículos do mesmo modo; a diferença está na implantação. No FUE são primeiro abertos os canais recetores e depois colocados os enxertos, enquanto o DHI utiliza um implantador Choi que abre o ponto recetor e coloca o folículo num único movimento. Isto oferece um controlo preciso sobre a angulação, a profundidade e a densidade, permite uma implantação muito densa e, com o planeamento adequado, evita muitas vezes rapar o cabelo existente.</p>',
						'<p>Ambas as técnicas extraem os folículos do mesmo modo; a diferença está na implantação. No FUE são primeiro abertos os canais receptores e depois colocados os enxertos, enquanto o DHI utiliza um implantador Choi que abre o ponto receptor e coloca o folículo num único movimento. Isto oferece um controle preciso sobre a angulação, a profundidade e a densidade, permite uma implantação muito densa e, com o planejamento adequado, evita muitas vezes rapar o cabelo existente.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 7, 1, array(
					'answer' => $p(
						'<p>O DHI é a técnica mais adequada a intervenções sem rapar e em zonas específicas. Em áreas pequenas ou para aumentar a densidade, pode frequentemente conservar-se todo o cabelo existente; em sessões mais amplas pode ser necessário um corte parcial ou oculto. O seu consultor confirmar-lhe-á exatamente o que é possível no seu caso depois de avaliar as suas fotografias.</p>',
						'<p>O DHI é a técnica mais adequada a intervenções sem raspar e em zonas específicas. Em áreas pequenas ou para aumentar a densidade, pode-se frequentemente conservar todo o cabelo existente; em sessões mais amplas pode ser necessário um corte parcial ou oculto. O seu consultor confirmará exatamente o que é possível no seu caso depois de avaliar as suas fotografias.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 7, 5, array(
					'answer' => $p(
						'<p>Sim. Os folículos são extraídos de zonas geneticamente resistentes à queda, pelo que continuam a crescer de forma permanente. A implantação direta com controlo da angulação permite criar uma linha frontal e uma densidade completamente naturais; poderá cortar, lavar e pentear o seu cabelo normalmente. O cabelo existente não transplantado pode continuar a enfraquecer com o tempo, pelo que poderão ser recomendados tratamentos de apoio para conservar o resultado global.</p>',
						'<p>Sim. Os folículos são extraídos de zonas geneticamente resistentes à queda, pelo que continuam a crescer de forma permanente. A implantação direta com controle da angulação permite criar uma linha frontal e densidade completamente naturais; poderá cortar, lavar e pentear o seu cabelo normalmente. O cabelo existente não transplantado pode continuar a enfraquecer com o tempo, pelo que poderão ser recomendados tratamentos de apoio para conservar o resultado global.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'form', 9, array(
					'lead' => $p(
						'Partilhe connosco alguns dados e a nossa equipa responder-lhe-á com um plano DHI personalizado, uma estimativa de enxertos e um orçamento completo sem compromisso, normalmente em poucas horas.',
						'Partilhe conosco alguns dados e a nossa equipa responderá com um plano DHI personalizado, uma estimativa de enxertos e um orçamento completo sem compromisso, normalmente em poucas horas.'
					),
				) ),
			);

		case 'female-hair-transplant':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'intro', 3, array(
					'body' => $p(
						'<p>Na Estecapelli, a técnica que recomendamos com maior frequência às mulheres é o transplante DHI. As nossas pacientes valorizam especialmente o DHI porque permite implantar os folículos diretamente sem rapar o cabelo existente, sendo por isso ideal para quem deseja conservar o seu aspeto durante todo o processo.</p><p><strong>Vantagens do DHI:</strong> controlo preciso da angulação e da direção, maior densidade de implantação, ausência de corte total do cabelo, cicatrizes mínimas, recuperação mais rápida e menor risco de danificar os folículos existentes.</p>',
						'<p>Na Estecapelli, a técnica que recomendamos com maior frequência às mulheres é o transplante DHI. As nossas pacientes valorizam especialmente o DHI porque permite implantar os folículos diretamente sem raspar o cabelo existente, sendo por isso ideal para quem deseja conservar o seu aspecto durante todo o processo.</p><p><strong>Vantagens do DHI:</strong> controle preciso da angulação e da direção, maior densidade de implantação, ausência de corte total do cabelo, cicatrizes mínimas, recuperação mais rápida e menor risco de danificar os folículos existentes.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'intro', 7, array(
					'body' => $p(
						'<p>Para as mulheres que apresentam queda de cabelo mas não desejam submeter-se a um procedimento cirúrgico, oferecemos também várias opções:</p><ul><li><strong>PRP</strong> — tratamento regenerativo natural que nutre e fortalece os folículos.</li><li><strong>Mesoterapia</strong> — microinjeções de vitaminas e minerais diretamente no couro cabeludo.</li><li><strong>Terapia com células estaminais</strong> — revitaliza os folículos inativos através de células regenerativas.</li><li><strong>Injeções de exossomas</strong> — tratamento de nova geração que estimula os processos naturais de reparação do couro cabeludo.</li></ul>',
						'<p>Para as mulheres que apresentam queda de cabelo mas não desejam submeter-se a um procedimento cirúrgico, oferecemos também várias opções:</p><ul><li><strong>PRP</strong> — tratamento regenerativo natural que nutre e fortalece os folículos.</li><li><strong>Mesoterapia</strong> — microinjeções de vitaminas e minerais diretamente no couro cabeludo.</li><li><strong>Terapia com células estaminais</strong> — revitaliza os folículos inativos através de células regenerativas.</li><li><strong>Injeções de Exossomos</strong> — tratamento de nova geração que estimula os processos naturais de reparação do couro cabeludo.</li></ul>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 8, 0, array(
					'answer' => $p(
						'<p>A maioria das mulheres com uma queda estabilizada e uma zona dadora saudável na parte posterior do couro cabeludo pode ser candidata. O tratamento está especialmente indicado para o enfraquecimento ao longo do risco, uma linha frontal mais larga ou têmporas pouco densas. Como a queda difusa é mais frequente nas mulheres, identificar primeiro a causa é essencial; uma consulta com fotografias nítidas permite à nossa equipa confirmar a sua elegibilidade e estimar os enxertos necessários.</p>',
						'<p>A maioria das mulheres com uma queda estabilizada e uma zona dadora saudável na parte posterior do couro cabeludo pode ser candidata. O tratamento está especialmente indicado para o enfraquecimento ao longo do fio, uma linha frontal mais ampla ou têmporas pouco densas. Como a queda difusa é mais frequente nas mulheres, identificar primeiro a causa é essencial; uma consulta com fotografias nítidas permite à nossa equipa confirmar a sua elegibilidade e estimar os enxertos necessários.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'form', 10, array(
					'lead' => $p(
						'Partilhe connosco alguns dados e a nossa equipa responder-lhe-á com um plano personalizado sem rapar o cabelo, uma estimativa de enxertos e um orçamento completo sem compromisso, normalmente em poucas horas.',
						'Partilhe conosco alguns dados e a nossa equipa responderá com um plano personalizado sem rapar o cabelo, uma estimativa de enxertos e um orçamento completo sem compromisso, normalmente em poucas horas.'
					),
				) ),
			);

		case 'hair-mesotherapy':
			return array(
				estecapelli_safe_patch_pt_review_row( 'stepbook', 3, 2, array(
					'body' => $p(
						'<p>A solução é injetada diretamente no couro cabeludo através de agulhas muito finas para levar os nutrientes até aos folículos. Se o couro cabeludo for especialmente sensível, pode ser aplicado um creme anestésico local. O procedimento é rápido, confortável e minimamente invasivo, pelo que poderá retomar de imediato as suas atividades quotidianas.</p>',
						'<p>A solução é injetada diretamente no couro cabeludo através de agulhas muito finas para levar os nutrientes até aos folículos. Se o couro cabeludo for especialmente sensível, pode ser aplicado um creme anestésico local. O procedimento é rápido, confortável e minimamente invasivo, pelo que poderá retomar de imediato as suas atividades cotidianas.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'candidate', 4, array(
					'body' => $p(
						'Um tratamento complementar não cirúrgico que se integra facilmente na vida quotidiana:',
						'Um tratamento complementar não cirúrgico que se integra facilmente na vida cotidiana:'
					),
				) ),
			);

		case 'eyebrow-transplant':
			return array(
				estecapelli_safe_patch_pt_review_row( 'stepbook', 4, 2, array(
					'body' => $p(
						'<p>Para as sobrancelhas é preferida a técnica DHI: através de implantadores Choi especiais, o ponto recetor é aberto e o folículo é implantado num único movimento. Isto permite controlar com precisão a angulação muito baixa e a direção necessárias para que cada pelo permaneça próximo da pele e cresça de forma natural.</p>',
						'<p>Para as sobrancelhas é preferida a técnica DHI: através de implantadores Choi especiais, o ponto receptor é aberto e o folículo é implantado num único movimento. Isto permite controlar com precisão a angulação muito baixa e a direção necessárias para que cada pelo permaneça próximo da pele e cresça de forma natural.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'intro', 5, array(
					'body' => $p(
						'<p>Durante o primeiro dia são normais uma ligeira vermelhidão e alguma sensibilidade. Nas duas semanas seguintes formam-se pequenas crostas que se soltam por si; durante o mesmo período, os pelos transplantados podem cair temporariamente num processo conhecido como queda de choque.</p><p>As novas sobrancelhas começam a crescer a partir do terceiro mês. Precisam de aproximadamente nove a doze meses para estabilizarem completamente e alcançarem o seu aspeto definitivo. Como são utilizados muito menos enxertos do que num transplante do couro cabeludo, a recuperação costuma ser mais rápida e confortável.</p>',
						'<p>Durante o primeiro dia é normal uma ligeira vermelhidão e alguma sensibilidade. Nas duas semanas seguintes formam-se pequenas crostas que se soltam por si; durante o mesmo período, os pelos transplantados podem cair temporariamente num processo conhecido como queda de choque.</p><p>As novas sobrancelhas começam a crescer a partir do terceiro mês. Precisam de aproximadamente nove a doze meses para estabilizarem completamente e alcançarem o seu aspecto definitivo. Como são utilizados muito menos enxertos do que num transplante de couro cabeludo, a recuperação costuma ser mais rápida e confortável.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'intro', 6, array(
					'body' => $p(
						'<p>No transplante de sobrancelhas podem ser utilizadas tanto a técnica DHI, implantação capilar direta, como a Sapphire FUE, consoante o estado das sobrancelhas, as características da zona dadora e o resultado estético pretendido.</p><p>A DHI utiliza implantadores Choi especiais que permitem inserir diretamente cada enxerto num único passo. Oferece um excelente controlo da angulação, da direção e da profundidade, especialmente útil para o alinhamento extremamente delicado, pelo a pelo, que umas sobrancelhas naturais exigem.</p><p>Com a Sapphire FUE são primeiro preparados os canais recetores através de lâminas de safira e depois introduzidos os enxertos. Deste modo, o cirurgião pode desenhar com grande precisão a forma geral, a simetria e a distribuição da densidade.</p><p>Ambas as técnicas são eficazes: a DHI costuma ser preferida pelo controlo direto da direção a nível microscópico, enquanto a Sapphire FUE pode oferecer vantagens ao desenhar de forma estruturada o arco da sobrancelha. A escolha é sempre feita de acordo com a anatomia individual e com o resultado mais natural.</p>',
						'<p>No transplante de sobrancelhas podem ser utilizadas tanto a técnica DHI, implantação capilar direta, como a Sapphire FUE, consoante o estado das sobrancelhas, as características da zona doadora e o resultado estético pretendido.</p><p>A DHI utiliza implantadores Choi especiais que permitem inserir diretamente cada enxerto num único passo. Oferece um excelente controle da angulação, da direção e da profundidade, especialmente útil para o alinhamento extremamente delicado, pelo a pelo, que umas sobrancelhas naturais exigem.</p><p>Com a Sapphire FUE são primeiro preparados os canais receptores através de lâminas de safira e depois introduzidos os enxertos. Deste modo, o cirurgião pode desenhar com grande precisão a forma geral, a simetria e a distribuição da densidade.</p><p>Ambas as técnicas são eficazes: a DHI costuma ser preferida pelo controle direto da direção a nível microscópico, enquanto a Sapphire FUE pode oferecer vantagens ao desenhar de forma estruturada o arco da sobrancelha. A escolha é sempre feita de acordo com a anatomia individual e com o resultado mais natural.</p>'
					),
				) ),
			);

		case 'beard-transplant':
			return array(
				estecapelli_safe_patch_pt_review_row( 'stepbook', 3, 0, array(
					'body' => $p(
						'<p>Através da técnica FUE, os folículos são extraídos da zona dadora segura situada na parte posterior e nos lados da cabeça, onde o cabelo é geneticamente resistente à queda. Cada folículo é recolhido individualmente com micromotores ultrafinos para reduzir ao mínimo os danos e preservar o aspeto natural da zona dadora.</p>',
						'<p>Através da técnica FUE, os folículos são extraídos da zona dadora segura situada na parte posterior e nos lados da cabeça, onde o cabelo é geneticamente resistente à queda. Cada folículo é recolhido individualmente com micromotores ultrafinos para reduzir ao mínimo os danos e preservar o aspecto natural da zona doadora.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 5, 0, array(
					'answer' => $p(
						'<p>Pode ser um bom candidato se apresentar um crescimento irregular ou pouco denso, zonas vazias causadas por cicatrizes ou traumatismos, ou se não conseguir desenvolver uma barba densa, desde que disponha de uma zona dadora saudável na parte posterior e nos lados do couro cabeludo. Uma consulta acompanhada de fotografias nítidas permite confirmar a sua elegibilidade e estimar o número de enxertos necessários.</p>',
						'<p>Pode ser um bom candidato se apresentar um crescimento irregular ou pouco denso, zonas vazias causadas por cicatrizes ou traumatismos, ou se não conseguir desenvolver uma barba densa, desde que disponha de uma zona doadora saudável na parte posterior e nos lados do couro cabeludo. Uma consulta acompanhada de fotografias nítidas permite confirmar a sua elegibilidade e estimar o número de enxertos necessários.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 5, 1, array(
					'question' => $p( 'De onde vêm os enxertos e a barba terá um aspeto natural?', 'De onde vêm os enxertos e a barba terá um aspecto natural?' ),
					'answer'   => $p(
						'<p>Os folículos saudáveis são extraídos através da técnica FUE da zona dadora segura situada na parte posterior e nos lados da cabeça. O controlo preciso da angulação, da profundidade e da direção, respeitando o crescimento mais plano e descendente próprio da barba, permite que os enxertos se integrem perfeitamente com o pelo existente e produzam um resultado completamente natural.</p>',
						'<p>Os folículos saudáveis são extraídos através da técnica FUE da zona doadora segura situada na parte posterior e nos lados da cabeça. O controle preciso da angulação, da profundidade e da direção, respeitando o crescimento mais plano e descendente próprio da barba, permite que os enxertos se integrem perfeitamente com o pelo existente e produzam um resultado completamente natural.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 5, 5, array(
					'answer' => $p(
						'<p>Sim. Os folículos provêm de uma zona dadora geneticamente resistente à queda e, uma vez estabelecidos, continuam a crescer durante toda a vida. Quando a intervenção é realizada com a técnica correta e por uma equipa experiente, oferece resultados naturais, estáveis e duradouros.</p>',
						'<p>Sim. Os folículos provêm de uma zona doadora geneticamente resistente à queda e, uma vez estabelecidos, continuam a crescer durante toda a vida. Quando a intervenção é realizada com a técnica correta e por uma equipa experiente, oferece resultados naturais, estáveis e duradouros.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'form', 7, array(
					'lead' => $p(
						'Partilhe connosco alguns dados e a nossa equipa responder-lhe-á com um plano personalizado, um desenho de barba adaptado ao seu rosto e um orçamento completo sem compromisso, normalmente em poucas horas.',
						'Partilhe conosco alguns dados e a nossa equipa responderá com um plano personalizado, um desenho de barba adaptado ao seu rosto e um orçamento completo sem compromisso, normalmente em poucas horas.'
					),
				) ),
			);

		case 'pre-hair-transplant-period':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'intro', 1, array(
					'title' => $p( 'Porque é que o período anterior ao transplante é importante', 'Por quê é que o período anterior ao transplante é importante' ),
					'body'  => $p(
						'<p>O período pré-transplante inclui todos os passos essenciais antes de iniciar o procedimento: avaliação médica completa, análise do couro cabeludo e escolha da técnica mais adequada.</p><p>A área dadora é examinada e a linha frontal é cuidadosamente planeada para alcançar um resultado natural. A preparação correta desempenha um papel decisivo na segurança da intervenção, na sobrevivência do enxerto e no resultado global.</p>',
						'<p>O período pré-transplante inclui todos os passos essenciais antes de iniciar o procedimento: avaliação médica completa, análise do couro cabeludo e escolha da técnica mais adequada.</p><p>A área doadora é examinada e a linha frontal é cuidadosamente planeada para alcançar um resultado natural. A preparação correta desempenha um papel decisivo na segurança da intervenção, na sobrevivência do enxerto e no resultado global.</p>'
					),
				) ),
			);

		case 'post-hair-transplant-period':
			return array(
				estecapelli_safe_patch_pt_review_row( 'stepbook', 2, 0, array(
					'body' => $p(
						'<p>A vermelhidão ligeira, o inchaço e a sensibilidade nas áreas dadora e recetora são completamente normais. Mantenha a cabeça elevada, durma de costas, não toque nem arranhe a zona e tome os medicamentos fornecidos exatamente de acordo com as instruções.</p>',
						'<p>A vermelhidão ligeira, o inchaço e a sensibilidade nas áreas doadora e receptora são completamente normais. Mantenha a cabeça elevada, durma de costas, não toque nem arranhe a zona e tome os medicamentos fornecidos exatamente de acordo com as instruções.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'intro', 4, array(
					'body' => $p(
						'<p>Para fortalecer os novos folículos e promover um crescimento saudável, Estecapelli pode recomendar tratamentos complementares durante a recuperação:</p><ul><li>Injeções de exossomas — para apoiar a saúde e o crescimento folicular </li><li>PRP, plasma rico em plaquetas — para estimular o couro cabeludo e fortalecer o cabelo </li><li>Mesoterapia — para nutrir os folículos com vitaminas e minerais </li><li>Terapia com células estaminais — para promover um crescimento mais forte e saudável </li></ul>',
						'<p>Para fortalecer os novos folículos e promover um crescimento saudável, Estecapelli pode recomendar tratamentos complementares durante a recuperação:</p><ul><li>Injeções de exossomos — para apoiar a saúde e o crescimento folicular</li><li>PRP, plasma rico em plaquetas — para estimular o couro cabeludo e fortalecer o cabelo</li><li>Mesoterapia — para nutrir os folículos com vitaminas e minerais</li><li>Terapia com células estaminais — para promover um crescimento mais forte e saudável</li></ul>'
					),
				) ),
			);

		case 'tricholab':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'candidate', 3, array(
					'body' => $p(
						'A análise científica protege a área dadora e promove resultados naturais e simétricos:',
						'A análise científica protege a área doadora e promove resultados naturais e simétricos:'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'intro', 4, array(
					'title' => $p( 'Porquê escolher o TrichoLab em Estecapelli?', 'Porquê escolher o TrichoLab Estecapelli?' ),
					'body'  => $p(
						'<p>O TrichoLab é um sistema avançado disponível apenas num número limitado de clínicas na Turquia. Na Estecapelli, representa o ponto de partida do nosso trabalho e oferece aos pacientes um nível de análise que poucas clínicas conseguem proporcionar.</p><p>Cada avaliação é objetiva, mensurável e transparente. Para cada paciente é desenvolvida uma estratégia verdadeiramente personalizada, baseada em dados precisos e não em suposições, com o objetivo de proteger a área dadora e maximizar os resultados a longo prazo.</p>',
						'<p>O TrichoLab é um sistema avançado disponível apenas num número limitado de clínicas na Turquia. Na Estecapelli, representa o ponto de partida do nosso trabalho e oferece aos pacientes um nível de análise que poucas clínicas conseguem proporcionar.</p><p>Cada avaliação é objetiva, mensurável e transparente. Para cada paciente é desenvolvida uma estratégia verdadeiramente personalizada, baseada em dados precisos e não em suposições, com o objetivo de proteger a área doadora e maximizar os resultados a longo prazo.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 5, 1, array(
					'answer' => $p(
						'<p>O TrichoLab mede a densidade, a qualidade dos folículos e a capacidade da área dadora, o que ajuda a calcular os enxertos necessários sem enfraquecer a área de extração. Estes dados tornam o planeamento mais preciso, natural e sustentável a longo prazo. O cirurgião combina-os com a avaliação clínica e o design da linha da frente.</p>',
						'<p>O TrichoLab mede a densidade, a qualidade dos folículos e a capacidade da área doadora, o que ajuda a calcular os enxertos necessários sem enfraquecer a área de extração. Estes dados tornam o planejamento mais preciso, natural e sustentável a longo prazo. O cirurgião combina-os com a avaliação clínica e o design da linha da frente.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 5, 3, array(
					'answer' => $p(
						'<p>O TrichoLab fornece uma estimativa científica muito precisa da quantidade necessária e pode ser extraída com segurança. No entanto, a decisão final não depende de um único número: o especialista tem também em conta a qualidade do cabelo, os objetivos de densidade, a evolução futura da queda de cabelo e o desenho da linha frontal. Assim, o plano mantém-se realista e protege a área dadora.</p>',
						'<p>O TrichoLab fornece uma estimativa científica muito precisa da quantidade necessária e pode ser extraída com segurança. No entanto, a decisão final não depende de um único número: o especialista tem também em conta a qualidade do cabelo, os objetivos de densidade, a evolução futura da queda de cabelo e o desenho da linha frontal. Assim, o plano mantém-se realista e protege a área doadora.</p>'
					),
				) ),
			);

		case 'our-team':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'hero', 0, array(
					'title' => $p( 'As pessoas por detrás de cada transformação.', 'As pessoas por trás de cada transformação.' ),
				) ),
			);

		case 'rhinoplasty':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'candidate', 2, array(
					'title' => $p( 'Porque se realiza uma rinoplastia?', 'Por quê se realiza uma rinoplastia?' ),
				) ),
				estecapelli_safe_patch_pt_review_row( 'candidate', 3, 1, array(
					'label' => $p( 'Pessoas insatisfeitas com o aspeto ou a forma do seu nariz', 'Pessoas insatisfeitas com o aspecto ou a forma do seu nariz' ),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'intro', 8, array(
					'body' => $p(
						'<p>Como qualquer intervenção cirúrgica, a rinoplastia acarreta alguns riscos potenciais, como inflamação, hemorragia temporária, infeção ou dificuldades de cicatrização. Estas complicações são pouco frequentes e podem ser consideravelmente reduzidas quando a operação é realizada por cirurgiões experientes com técnicas adequadas.</p>',
						'<p>Como qualquer intervenção cirúrgica, a rinoplastia acarreta alguns riscos potenciais, como inflamação, hemorragia temporária, infecção ou dificuldades de cicatrização. Estas complicações são pouco frequentes e podem ser consideravelmente reduzidas quando a operação é realizada por cirurgiões experientes com técnicas adequadas.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'gallery', 9, array(
					'lead' => $p(
						'Descubra os resultados refinados e equilibrados dos nossos pacientes, sempre em harmonia com o restante rosto. Cada intervenção é planeada segundo as características individuais para oferecer um aspeto natural.',
						'Descubra os resultados refinados e equilibrados dos nossos pacientes, sempre em harmonia com o restante rosto. Cada intervenção é planeada segundo as características individuais para oferecer um aspecto natural.'
					),
				) ),
			);

		case 'bbl':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'candidate', 3, array(
					'title' => $p( 'Que aspetos pode o BBL corrigir?', 'Que aspectos pode o BBL corrigir?' ),
					'body'  => $p(
						'O lifting brasileiro de glúteos pode melhorar diferentes aspetos estéticos e de proporção, entre eles:',
						'O lifting brasileiro de glúteos pode melhorar diferentes aspectos estéticos e de proporção, entre eles:'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 11, 4, array(
					'answer' => $p(
						'<p>A maioria dos pacientes internacionais permanece na Turquia cerca de 7–10 dias para concluir a intervenção, a primeira fase de recuperação e uma revisão antes do regresso. Como no início não se recomenda permanecer sentado durante muito tempo, a equipa médica indicar-lhe-á o momento mais seguro para o voo de regresso e como utilizar a almofada durante a viagem.</p>',
						'<p>A maioria dos pacientes internacionais permanece na Turquia cerca de 7–10 dias para concluir a intervenção, a primeira fase de recuperação e uma revisão antes do regresso. Como no início não se recomenda permanecer sentado durante muito tempo, a equipa médica indicará o momento mais seguro para o voo de regresso e como utilizar a almofada durante a viagem.</p>'
					),
				) ),
			);

		case 'liposuction':
			return array(
				estecapelli_safe_patch_pt_review_row( 'steps', 8, 1, array(
					'title' => $p( 'Regresso à vida quotidiana', 'Regresso à vida cotidiana' ),
				) ),
			);

		case 'breast-aesthetics-breast-surgery':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'hero', 0, array(
					'lead' => $p(
						'Obtenha um peito com volume, natural e em harmonia com o seu corpo. O nosso planeamento personalizado oferece resultados estéticos e equilibrados, adaptados à sua silhueta e aos seus objetivos.',
						'Obtenha um seio com volume, natural e em harmonia com o seu corpo. O nosso planeamento personalizado oferece resultados estéticos e equilibrados, adaptados à sua silhueta e aos seus objetivos.'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'intro', 4, array(
					'body' => $p(
						'<p>A cirurgia de aumento mamário é realizada sob anestesia geral, num bloco operatório totalmente equipado, e costuma durar entre 1,5 e 2 horas. Durante o procedimento, os implantes são cuidadosamente colocados para obter o resultado mais natural e proporcionado possível. A paciente tem normalmente alta no dia seguinte, após um controlo pós-operatório de rotina.</p>',
						'<p>A cirurgia de aumento mamário é realizada sob anestesia geral, num bloco operatório totalmente equipado, e costuma durar entre 1,5 e 2 horas. Durante o procedimento, os implantes são cuidadosamente colocados para obter o resultado mais natural e proporcionado possível. A paciente tem normalmente alta no dia seguinte, após um controle pós-operatório de rotina.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_row( 'faq', 11, 2, array(
					'question' => $p( 'Implantes redondos ou anatómicos? Por cima ou por baixo do músculo?', 'Implantes redondos ou anatômicos? Por cima ou por baixo do músculo?' ),
					'answer'   => $p(
						'<p>Os implantes redondos tendem a criar maior plenitude no polo superior, enquanto os anatómicos, em forma de lágrima, seguem um perfil mais suave e natural; ambos podem oferecer um excelente resultado na paciente adequada. A colocação por baixo do músculo costuma ser preferida pelo seu aspeto mais natural, um suporte mais duradouro dos tecidos e um menor risco de contratura capsular. A escolha final da forma, do tamanho, do perfil e do plano de colocação é feita em conjunto com a paciente segundo a sua constituição, o tecido existente e o resultado pretendido.</p>',
						'<p>Os implantes redondos tendem a criar maior plenitude no polo superior, enquanto os anatómicos, em forma de lágrima, seguem um perfil mais suave e natural; ambos podem oferecer um excelente resultado na paciente adequada. A colocação por baixo do músculo costuma ser preferida pelo seu aspecto mais natural, um suporte mais duradouro dos tecidos e um menor risco de contratura capsular. A escolha final da forma, do tamanho, do perfil e do plano de colocação é feita em conjunto com a paciente segundo a sua constituição, o tecido existente e o resultado pretendido.</p>'
					),
				) ),
				estecapelli_safe_patch_pt_review_layout( 'form', 13, array(
					'lead' => $p(
						'Faculte-nos alguns dados e a nossa equipa responder-lhe-á com um plano personalizado de cirurgia mamária e um orçamento com tudo incluído, gratuito e sem compromisso, normalmente em poucas horas.',
						'Faculte-nos alguns dados e a nossa equipa responderá com um plano personalizado de cirurgia mamária e um orçamento com tudo incluído, gratuito e sem compromisso, normalmente em poucas horas.'
					),
				) ),
			);

		case 'face-and-neck-lift-surgery':
			return array(
				estecapelli_safe_patch_pt_review_row( 'steps', 7, 1, array(
					'title' => $p( 'Regresso à vida quotidiana', 'Regresso à vida cotidiana' ),
				) ),
			);

		case 'obesity-surgeries-bariatric-surgery-and-gastric-balloon':
			return array(
				estecapelli_safe_patch_pt_review_layout( 'form', 14, array(
					'lead' => $p(
						'Faculte-nos alguns dados e a nossa equipa responder-lhe-á com um plano personalizado de perda de peso e um orçamento com tudo incluído, gratuito e sem compromisso, normalmente em poucas horas.',
						'Faculte-nos alguns dados e a nossa equipa responderá com um plano personalizado de perda de peso e um orçamento com tudo incluído, gratuito e sem compromisso, normalmente em poucas horas.'
					),
				) ),
			);

		default:
			return array();
	}
}

/** Assemble one page-scoped Portuguese review patch. */
function estecapelli_safe_patch_pt_review_definition( $source_slug, $post_type, $title ) {
	return array(
		'title'       => $title,
		'description' => 'Apply only the reviewed Portuguese fields from the 26-page language revision. Existing values are verified before writing and an exact rollback backup is retained.',
		'post_type'   => $post_type,
		'source_slug' => $source_slug,
		'schema'      => 'field_groups_v2',
		'languages'   => array(
			'pt' => estecapelli_safe_patch_pt_review_operations( $source_slug ),
		),
	);
}

/** Operations for the French copy revision (Google Doc "Sapphire FUE yanlış"). */
function estecapelli_safe_patch_fr_revision_operations( $source_slug ) {
	switch ( $source_slug ) {
		case 'about-us':
			return array(
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 3,
					'fields' => array(
						'title' => array(
							'before' => "Des résultats de classe mondiale, sans le prix gonflé",
							'after'  => "Des résultats de classe mondiale, au juste prix",
						),
						'body' => array(
							'before' => "<p>Pourquoi payer plus au Royaume-Uni, aux États-Unis ou en Europe ? Chez Estecapelli, nous associons excellence médicale, techniques accréditées à l’international et tarifs compétitifs pour offrir des résultats de greffe de cheveux de classe mondiale à une fraction du coût.</p><p>Nos installations à la pointe de la technologie en Turquie offrent la même qualité de soins que celle des meilleures cliniques du monde — sans les prix gonflés. Avec des chirurgiens expérimentés, des plans de traitement personnalisés et un suivi complet, vous bénéficiez d’une valeur exceptionnelle qui ne fait jamais de compromis sur la qualité.</p>",
							'after'  => "<p>Pourquoi payer plus cher au Royaume-Uni, aux États-Unis ou en Europe ? Chez Estecapelli, nous associons l’excellence médicale, des techniques accréditées à l’international et des tarifs compétitifs pour offrir des résultats de greffe de cheveux de classe mondiale à une fraction des coûts habituels.</p><p>Nos installations à la pointe de la technologie en Turquie offrent la même qualité de soins que celle des meilleures cliniques du monde sans les tarifs prohibitifs. Avec des chirurgiens expérimentés, des plans de traitement personnalisés et un suivi complet, vous bénéficiez d’une valeur exceptionnelle qui ne fait jamais de compromis sur la qualité.</p>",
						),
					),
				),
			);

		case 'rhinoplasty':
			return array(
				array(
					'target' => 'layout_fields', 'layout' => 'hero', 'layout_index' => 0,
					'fields' => array( 'lead' => array(
						'before' => "Une intervention chirurgicale qui remodèle le nez en améliorant sa forme, sa taille et sa fonction. Elle peut être réalisée à des fins esthétiques comme pour corriger des problèmes respiratoires.",
						'after'  => "Une intervention chirurgicale qui remodèle le nez en améliorant sa forme, sa taille et sa fonction. Elle peut être réalisée dans un but esthétique tout comme pour corriger des problèmes respiratoires.",
					) ),
				),
			);

		case 'liposuction':
			return array(
				array(
					'target' => 'layout_fields', 'layout' => 'candidate', 'layout_index' => 4,
					'fields' => array( 'title' => array(
						'before' => "Qui peut subir une liposuccion ?",
						'after'  => "Qui est le bon candidat pour une liposuccion ?",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 5,
					'fields' => array( 'title' => array(
						'before' => "Quand la liposuccion n’est pas recommandée",
						'after'  => "Dans quels cas la liposuccion est-elle déconseillée ?",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 6,
					'fields' => array( 'body' => array(
						'before' => "<p>Le processus préopératoire à Estecapelli est méticuleusement géré pour garantir une planification chirurgicale sûre.</p>",
						'after'  => "<p>Le processus préopératoire chez Estecapelli est méticuleusement géré pour garantir une planification chirurgicale sûre.</p>",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 7,
					'fields' => array(
						'title' => array(
							'before' => "Comment se déroule la chirurgie de liposuccion ?",
							'after'  => "Comment se déroule l’intervention ?",
						),
						'body' => array(
							'before' => "<p>La liposuccion est réalisée sous anesthésie locale ou générale. La technique choisie dépend de la quantité et des caractéristiques de la graisse à éliminer, ainsi que de la taille de la zone traitée.</p><p><strong>Étapes générales\u{00A0}:</strong></p><ol><li>Faire de petites incisions</li><li>Décomposer la graisse à l'aide de la technologie appropriée (Vaser, laser, etc.)</li><li>Enlever les cellules graisseuses avec des canules</li><li>Contourner et façonner la zone</li><li>Fermer les incisions avec un minimum cicatrisation</li></ol><p>Durée\u{00A0}: 30 minutes à 3 heures, selon l'ampleur de l'intervention.</p>",
							'after'  => "<p>La liposuccion est réalisée sous anesthésie locale ou générale. La technique choisie dépend de la quantité et des caractéristiques de la graisse à éliminer, ainsi que de la taille de la zone traitée.</p><p><strong>Étapes\u{00A0}:</strong></p><ol><li>Réalisation de micro-incisions stratégiques.</li><li>Ciblage et émulsion de la graisse\u{00A0}: Grâce à des technologies de pointe (Vaser Lipo, Laser), la graisse est liquéfiée en douceur, préservant ainsi les tissus environnants (vaisseaux et nerfs).</li><li>Aspiration douce des cellules graisseuses\u{00A0}: À l'aide de micro-canules de haute précision, les excès de graisse ciblés sont retirés de manière homogène et définitive.</li><li>Sculpture et harmonisation de la silhouette\u{00A0}: Le chirurgien redessine les contours du corps avec un sens du détail artistique pour un résultat naturel et sur mesure.</li><li>Sutures de précision\u{00A0}: Les micro-incisions sont refermées avec des points d'une extrême finesse, assurant une cicatrisation optimale, rapide et esthétique.</li></ol><p>La durée de l'intervention varie de 30 minutes à 3 heures, selon l'ampleur du remodelage et le nombre de zones à traiter.</p>",
						),
					),
				),
				array(
					'layout' => 'steps', 'layout_index' => 8, 'repeater' => 'items', 'row_index' => 2,
					'fields' => array( 'title' => array(
						'before' => "L’enflure diminue",
						'after'  => "Le gonflement diminue",
					) ),
				),
				array(
					'layout' => 'steps', 'layout_index' => 8, 'repeater' => 'items', 'row_index' => 3,
					'fields' => array(
						'title' => array(
							'before' => "Les contours prennent forme",
							'after'  => "Redéfinition de la silhouette",
						),
						'body' => array(
							'before' => "Les contours du corps commencent à prendre forme.",
							'after'  => "Les courbes se dessinent progressivement et les contours du corps se stabilisent pour révéler le résultat final.",
						),
					),
				),
				array(
					'layout' => 'steps', 'layout_index' => 8, 'repeater' => 'items', 'row_index' => 4,
					'fields' => array(
						'title' => array(
							'before' => "Résultats finaux",
							'after'  => "Stabilisation et résultats finaux",
						),
						'body' => array(
							'before' => "Les résultats finaux sont révélés.",
							'after'  => "Les nouveaux contours du corps se stabilisent pour laisser place au résultat esthétique final, harmonieux et naturel.",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'faq', 'layout_index' => 11,
					'fields' => array( 'title' => array(
						'before' => "Liposuccion – Foire aux questions",
						'after'  => "Liposuccion – FAQ",
					) ),
				),
			);

		case 'gynecomastia':
			return array(
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 1,
					'fields' => array( 'body' => array(
						'before' => "<p>La gynécomastie est une affection caractérisée par un développement excessif du tissu mammaire chez l’homme, donnant à la poitrine un aspect plus volumineux. Les hommes possèdent naturellement du tissu et des glandes mammaires, que la testostérone maintient généralement peu développés. Un déséquilibre hormonal, une prise de poids, certains médicaments ou un problème de santé sous-jacent peuvent perturber cet équilibre et favoriser l’apparition d’une gynécomastie.</p><p>Cette affection dépasse la seule dimension esthétique : elle peut affecter le bien-être psychologique, la confiance en soi et la vie sociale. Chez Estecapelli, la gynécomastie est prise en charge sous ses aspects médicaux et esthétiques afin de proposer à chaque patient une solution personnalisée et durable.</p>",
						'after'  => "<p>La gynécomastie désigne l’augmentation du volume de la poitrine chez l’homme, liée à un développement anormal de la glande mammaire. Ce phénomène s’explique généralement par un déséquilibre entre les hormones masculines et féminines (testostérone et œstrogènes), mais peut aussi être accentué par une prise de poids ou certains facteurs de santé.</p><p>Souvent source de complexes ou de gêne au quotidien, cette affection mérite une prise en charge experte et bienveillante. Pour les équipes d’Estecapelli, restaurer l’harmonie de votre torse est une priorité. Nous concevons un plan de traitement personnalisé afin de vous aider à retrouver une silhouette athlétique dans laquelle vous vous sentirez pleinement à l’aise.</p>",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'faq', 'layout_index' => 7,
					'fields' => array( 'title' => array(
						'before' => "Gynécomastie – Foire aux questions",
						'after'  => "Gynécomastie – FAQ",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 8,
					'fields' => array( 'title' => array(
						'before' => "Prix de la chirurgie de la gynécomastie",
						'after'  => "Tarif de la chirurgie de la gynécomastie",
					) ),
				),
			);

		case 'abdominoplasty-tummy-tuck':
			return array(
				array(
					'target' => 'layout_fields', 'layout' => 'faq', 'layout_index' => 8,
					'fields' => array( 'title' => array(
						'before' => "Abdominoplastie – Foire aux questions",
						'after'  => "Abdominoplastie – FAQ",
					) ),
				),
			);

		case 'obesity-surgeries-bariatric-surgery-and-gastric-balloon':
			return array(
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 9,
					'fields' => array(
						'title' => array(
							'before' => "Quand un ballon gastrique ne doit pas être utilisé",
							'after'  => "Quelles sont les contre-indications au ballon gastrique ?",
						),
						'body' => array(
							'before' => "<ul><li>Ulcère gastrique, gastrite et hernie hiatale</li><li>Abus de substances</li><li>Troubles alimentaires graves</li><li>Période de grossesse et d'allaitement</li></ul>",
							'after'  => "<ul><li><strong>Antécédents chirurgicaux\u{00A0}:</strong> Avoir déjà subi une chirurgie de l'estomac ou de l'œsophage (comme une Sleeve ou un Bypass).</li><li><strong>Affections digestives actives\u{00A0}:</strong> Présence d'un ulcère à l'estomac, d'une gastrite sévère ou d'une hernie hiatale importante.</li><li><strong>Grossesse\u{00A0}:</strong> L'intervention est contre-indiquée chez les femmes enceintes ou allaitantes.</li><li><strong>Troubles du comportement alimentaire\u{00A0}:</strong> Les profils souffrant d'hyperphagie ou de boulimie non stabilisée.</li></ul>",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'faq', 'layout_index' => 12,
					'fields' => array( 'title' => array(
						'before' => "Chirurgie bariatrique — Foire aux questions",
						'after'  => "Chirurgie bariatrique — FAQ",
					) ),
				),
			);

		case 'dental-implant':
			return array(
				array(
					'layout' => 'faq', 'layout_index' => 7, 'repeater' => 'items', 'row_index' => 0,
					'fields' => array(
						'question' => array(
							'before' => "Qui est un bon candidat pour un implant dentaire ?",
							'after'  => "Êtes-vous un bon candidat pour les implants dentaires ?",
						),
						'answer' => array(
							'before' => "<p>La plupart des adultes en bonne santé générale ayant perdu une ou plusieurs dents et disposant d’un os de mâchoire sain en quantité suffisante sont de bons candidats. Des affections comme un diabète non contrôlé, une maladie des gencives avancée ou une perte osseuse importante n’excluent pas nécessairement le traitement, mais elles sont d’abord évaluées et prises en charge. Un court examen avec radiographies ou scanner 3D permet à nos dentistes de confirmer votre éligibilité et de définir le bon plan.</p>",
							'after'  => "<p>En règle générale, cette intervention s’adresse à tous les adultes en bonne santé qui souhaitent remplacer une ou plusieurs dents manquantes, sous réserve de disposer d’un volume osseux suffisant au niveau de la mâchoire.</p><p>Certaines situations comme un diabète non stabilisé, une affection des gencives (parodontite) ou une perte osseuse importante ne constituent pas des barrières définitives. Elles nécessitent simplement une prise en charge préalable par nos spécialistes. Lors de votre première évaluation, un examen approfondi accompagné de radiographies ou d’un scanner 3D permettra à nos chirurgiens-dentistes de confirmer votre éligibilité et de concevoir votre plan de traitement sur mesure.</p>",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'faq', 'layout_index' => 7,
					'fields' => array( 'title' => array(
						'before' => "Implants dentaires — Foire aux questions",
						'after'  => "Implants dentaires — FAQ",
					) ),
				),
			);

		case 'hollywood-smile':
			return array(
				array(
					'target' => 'layout_fields', 'layout' => 'hero', 'layout_index' => 0,
					'fields' => array( 'title' => array(
						'before' => "Sourire hollywoodien",
						'after'  => "Hollywood Smile",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 1,
					'fields' => array(
						'title' => array(
							'before' => "Qu’est-ce qu’un sourire hollywoodien ?",
							'after'  => "Qu’est-ce qu’un Hollywood Smile ?",
						),
						'body' => array(
							'before' => "<p>Le sourire hollywoodien est un traitement complet de design du sourire qui harmonise la relation entre les dents, les gencives et les lèvres afin de créer un sourire symétrique, naturel et parfaitement adapté au visage de chacun. Plutôt que de simplement modifier l’apparence des dents, il adopte une approche globale, prenant en compte la santé des gencives, la structure du visage et les proportions esthétiques dans leur ensemble.</p><p>Chez Estecapelli, chaque sourire hollywoodien est personnalisé grâce aux technologies de conception numérique et aux principes de la dentisterie esthétique. Le résultat est un sourire non seulement visuellement remarquable, mais aussi sain, équilibré et conçu pour durer.</p>",
							'after'  => "<p>Le sourire « Hollywoodien » est un traitement complet de design du sourire qui harmonise la relation entre les dents, les gencives et les lèvres afin de créer un sourire symétrique, naturel et parfaitement adapté au visage de chacun. Plutôt que de simplement modifier l’apparence des dents, il adopte une approche globale, prenant en compte la santé des gencives, la structure du visage et les proportions esthétiques dans leur ensemble.</p><p>Chez Estecapelli, chaque sourire « Hollywoodien » est personnalisé grâce aux technologies de conception numérique et aux principes de la dentisterie esthétique. Le résultat est un sourire non seulement visuellement remarquable, mais aussi sain, équilibré et conçu pour durer.</p>",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'candidate', 'layout_index' => 2,
					'fields' => array(
						'title' => array(
							'before' => "À qui s’adresse le sourire hollywoodien ?",
							'after'  => "À qui s’adresse le Hollywood Smile ?",
						),
						'body' => array(
							'before' => "Le sourire hollywoodien est une solution idéale pour les personnes concernées par l’un des points suivants :",
							'after'  => "Le sourire « Hollywoodien » est une solution idéale pour les personnes concernées par l’un des points suivants :",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'stepbook', 'layout_index' => 3,
					'fields' => array(
						'title' => array(
							'before' => "Comment réalise-t-on un sourire hollywoodien ?",
							'after'  => "Comment réalise-t-on un Hollywood Smile ?",
						),
						'lead' => array(
							'before' => "Le sourire hollywoodien est un traitement hautement personnalisé, et la procédure est adaptée aux besoins uniques de chaque patient. Bien que les étapes précises puissent varier, le processus suit généralement ces grandes étapes.",
							'after'  => "Le sourire « Hollywoodien » est un traitement hautement personnalisé, et la procédure est adaptée aux besoins uniques de chaque patient. Bien que les étapes précises puissent varier, le processus suit généralement ces grandes étapes.",
						),
					),
				),
				array(
					'layout' => 'stepbook', 'layout_index' => 3, 'repeater' => 'items', 'row_index' => 4,
					'fields' => array( 'body' => array(
						'before' => "<p>Les matériaux les plus adaptés sont appliqués pour remodeler et transformer les dents sur le plan esthétique. Les options les plus couramment utilisées dans le traitement du sourire hollywoodien sont :</p><p><strong>Facette en céramique (laminée)</strong></p><ul><li>Aspect esthétique le plus proche des dents naturelles</li><li>Grande translucidité à la lumière pour un rendu réaliste</li><li>Nécessite une réduction minimale de la dent</li></ul><p><strong>Couronnes en zircone</strong></p><ul><li>Durables grâce à leur structure très résistante</li><li>Adaptées en cas de dents manquantes</li><li>Offrent un résultat blanc et esthétique</li></ul><p><strong>Collage composite</strong></p><ul><li>Idéal pour corriger rapidement de petites imperfections</li><li>Peut être appliqué sans endommager la structure naturelle de la dent</li></ul>",
						'after'  => "<p>Les matériaux les plus adaptés sont appliqués pour remodeler et transformer les dents sur le plan esthétique. Les options les plus couramment utilisées dans le traitement du sourire « Hollywoodien » sont :</p><p><strong>Facette en céramique (laminée)</strong></p><ul><li>Aspect esthétique le plus proche des dents naturelles</li><li>Grande translucidité à la lumière pour un rendu réaliste</li><li>Nécessite une réduction minimale de la dent</li></ul><p><strong>Couronnes en zircone</strong></p><ul><li>Durables grâce à leur structure très résistante</li><li>Adaptées en cas de dents manquantes</li><li>Offrent un résultat blanc et esthétique</li></ul><p><strong>Collage composite</strong></p><ul><li>Idéal pour corriger rapidement de petites imperfections</li><li>Peut être appliqué sans endommager la structure naturelle de la dent</li></ul>",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'gallery', 'layout_index' => 4,
					'fields' => array( 'title' => array(
						'before' => "Sourire hollywoodien — Avant et après",
						'after'  => "Hollywood Smile — Avant et après",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'faq', 'layout_index' => 5,
					'fields' => array( 'title' => array(
						'before' => "Sourire hollywoodien — Foire aux questions",
						'after'  => "Hollywood Smile — FAQ",
					) ),
				),
				array(
					'layout' => 'faq', 'layout_index' => 5, 'repeater' => 'items', 'row_index' => 0,
					'fields' => array(
						'question' => array(
							'before' => "Quelle est la différence entre facettes, couronnes et sourire hollywoodien ?",
							'after'  => "Quelle est la différence entre facettes, couronnes et sourire « Hollywoodien » ?",
						),
						'answer' => array(
							'before' => "<p>Un sourire hollywoodien n’est pas un produit unique mais un plan complet de design du sourire. Les facettes en céramique laminée sont des coques ultra-fines collées sur la face avant des dents et ne nécessitent qu’un remodelage minimal ; les couronnes en zircone recouvrent toute la dent et sont utilisées lorsqu’une dent est très usée, cassée ou dévitalisée. Votre dentiste combine les options qui conviennent à chaque dent — avec un blanchiment ou un remodelage gingival si nécessaire — afin que le résultat final paraisse équilibré sur l’ensemble du sourire.</p>",
							'after'  => "<p>Un sourire « Hollywoodien » n’est pas un produit unique mais un plan complet de design du sourire. Les facettes en céramique laminée sont des coques ultra-fines collées sur la face avant des dents et ne nécessitent qu’un remodelage minimal ; les couronnes en zircone recouvrent toute la dent et sont utilisées lorsqu’une dent est très usée, cassée ou dévitalisée. Votre dentiste combine les options qui conviennent à chaque dent — avec un blanchiment ou un remodelage gingival si nécessaire — afin que le résultat final paraisse équilibré sur l’ensemble du sourire.</p>",
						),
					),
				),
				array(
					'layout' => 'faq', 'layout_index' => 5, 'repeater' => 'items', 'row_index' => 2,
					'fields' => array( 'answer' => array(
						'before' => "<p>Un sourire hollywoodien bien conçu doit paraître naturel pour votre visage. La teinte, la forme des dents et les proportions sont choisies à partir d’un design numérique du sourire basé sur vos traits, votre âge et votre carnation — vous obtenez ainsi un sourire lumineux et régulier sans l’aspect plat et trop blanc d’un « bloc de dents ». La teinte est convenue avec vous au préalable.</p>",
						'after'  => "<p>Un sourire « Hollywoodien » bien conçu doit paraître naturel pour votre visage. La teinte, la forme des dents et les proportions sont choisies à partir d’un design numérique du sourire basé sur vos traits, votre âge et votre carnation — vous obtenez ainsi un sourire lumineux et régulier sans l’aspect plat et trop blanc d’un « bloc de dents ». La teinte est convenue avec vous au préalable.</p>",
					) ),
				),
				array(
					'layout' => 'faq', 'layout_index' => 5, 'repeater' => 'items', 'row_index' => 3,
					'fields' => array( 'answer' => array(
						'before' => "<p>La plupart des traitements de sourire hollywoodien sont réalisés en cinq à sept jours environ, en deux à trois rendez-vous — le premier pour la préparation et les provisoires, les suivants pour la pose et les ajustements des restaurations définitives. Nous vous recommandons de planifier votre séjour en conséquence et de garder une journée à la fin pour d’éventuels petits ajustements.</p>",
						'after'  => "<p>La plupart des traitements de sourire « Hollywoodien » sont réalisés en cinq à sept jours environ, en deux à trois rendez-vous — le premier pour la préparation et les provisoires, les suivants pour la pose et les ajustements des restaurations définitives. Nous vous recommandons de planifier votre séjour en conséquence et de garder une journée à la fin pour d’éventuels petits ajustements.</p>",
					) ),
				),
				array(
					'layout' => 'faq', 'layout_index' => 5, 'repeater' => 'items', 'row_index' => 7,
					'fields' => array( 'question' => array(
						'before' => "Un sourire hollywoodien peut-il corriger des dents de travers ou espacées sans appareil ?",
						'after'  => "Un sourire « Hollywoodien » peut-il corriger des dents de travers ou espacées sans appareil ?",
					) ),
				),
				array(
					'layout' => 'faq', 'layout_index' => 5, 'repeater' => 'items', 'row_index' => 9,
					'fields' => array( 'question' => array(
						'before' => "Qui est un bon candidat pour un sourire hollywoodien ?",
						'after'  => "Qui est un bon candidat pour un sourire « Hollywoodien » ?",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'form', 'layout_index' => 6,
					'fields' => array( 'lead' => array(
						'before' => "Envoyez-nous quelques détails et notre équipe vous répondra avec un plan de sourire hollywoodien personnalisé et un devis tout compris et sans engagement, généralement en quelques heures.",
						'after'  => "Envoyez-nous quelques détails et notre équipe vous répondra avec un plan de sourire « Hollywoodien » personnalisé et un devis tout compris et sans engagement, généralement en quelques heures.",
					) ),
				),
			);

		case 'breast-aesthetics-breast-surgery':
			return array(
				array(
					'target' => 'layout_fields', 'layout' => 'hero', 'layout_index' => 0,
					'fields' => array(
						'title' => array(
							'before' => "Esthétique mammaire",
							'after'  => "Chirurgie esthétique mammaire",
						),
						'lead' => array(
							'before' => "Obtenez des seins en harmonie avec votre corps, pleins et d’apparence naturelle. Notre planification personnalisée garantit des résultats esthétiques et équilibrés adaptés à votre silhouette et à vos objectifs uniques.",
							'after'  => "Obtenez une poitrine en harmonie avec votre corps, pleine et d’apparence naturelle. Notre planification personnalisée garantit des résultats esthétiques et équilibrés adaptés à votre silhouette et à vos objectifs uniques.",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 1,
					'fields' => array(
						'title' => array(
							'before' => "Qu’est-ce que l’esthétique mammaire (chirurgie mammaire) ?",
							'after'  => "Qu’est-ce que la chirurgie esthétique mammaire ?",
						),
						'body' => array(
							'before' => "<p>L'esthétique des seins est le terme général désignant les interventions chirurgicales esthétiques réalisées pour améliorer la taille, la forme et la position des seins en harmonie avec les proportions du visage et du corps. Les procédures les plus couramment pratiquées comprennent l'augmentation mammaire, la réduction mammaire et la chirurgie de lifting des seins.</p><p>Chez Estecapelli, l'esthétique mammaire ne se limite pas à l'augmentation ou à la réduction des seins. Il s’agit de créer une silhouette équilibrée et naturelle qui complète l’ensemble du corps. Chaque plan de traitement est soigneusement personnalisé en fonction de l’anatomie et des objectifs esthétiques uniques de chaque patient. Notre objectif ultime est d'aider les patients à se sentir mieux physiquement et émotionnellement, en obtenant une forme corporelle dans laquelle ils se sentent vraiment en confiance et à l'aise.</p>",
							'after'  => "<p>L'esthétique mammaire regroupe les interventions chirurgicales conçues pour sublimer le volume, la courbe et la position de la poitrine, afin de créer une harmonie parfaite avec la morphologie globale. Les procédures de référence incluent l'augmentation, la réduction et le lifting mammaire.</p><p>Chez Estecapelli, notre philosophie dépasse la simple modification de volume. Nous recherchons avant tout l'équilibre et le naturel pour sculpter une silhouette cohérente qui valorise votre corps. Parce que chaque anatomie est singulière, chaque protocole est dessiné sur mesure selon vos aspirations. Notre vocation ultime est de vous accompagner vers un épanouissement à la fois physique et personnel, en vous offrant un résultat qui restaure pleinement votre confort et votre confiance en vous.</p>",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'candidate', 'layout_index' => 3,
					'fields' => array( 'title' => array(
						'before' => "Quand une augmentation mammaire est-elle pratiquée ?",
						'after'  => "Dans quels cas envisager une augmentation mammaire ?",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'candidate', 'layout_index' => 7,
					'fields' => array(
						'title' => array(
							'before' => "Esthétique mammaire en Turquie et avantages d'Estecapelli",
							'after'  => "Pourquoi choisir la Turquie et Estecapelli pour votre chirurgie esthétique mammaire ?",
						),
						'body' => array(
							'before' => "La Turquie est l’une des destinations les plus prisées au monde pour l’esthétique mammaire. Les principales raisons incluent :",
							'after'  => "Aujourd'hui positionnée comme l'un des leaders mondiaux de la chirurgie plastique, la Turquie attire chaque année des milliers de patients pour ses traitements mammaires. Faire confiance à Estecapelli, c'est bénéficier de plusieurs atouts majeurs\u{00A0}:",
						),
						'footer' => array(
							'before' => "Chez Estecapelli, ces avantages sont combinés à une approche personnalisée, garantissant que chaque patient reçoive l'attention, l'expertise et les résultats qu'il mérite.",
							'after'  => "La clé de voûte de toute intervention réussie repose sur une compréhension mutuelle et parfaite entre le chirurgien et son patient. Choix des implants, projection du rendu esthétique, étapes post-opératoires\u{00A0}: chaque aspect est abordé en toute transparence. Chez Estecapelli, nous accordons une importance primordiale à la consultation préopératoire. Ce moment d'échange privilégié est conçu pour répondre à toutes vos questions, afin que vous abordiez votre intervention avec une clarté totale, une sérénité absolue et une parfaite confiance.",
						),
					),
				),
				array(
					'layout' => 'candidate', 'layout_index' => 7, 'repeater' => 'items', 'row_index' => 0,
					'fields' => array( 'label' => array(
						'before' => "Chirurgiens hautement expérimentés avec un solide historique de cas réussis",
						'after'  => "Chirurgiens de renom bénéficiant d'une solide expérience clinique et d'un historique remarquable de procédures couronnées de succès.",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'gallery', 'layout_index' => 10,
					'fields' => array(
						'title' => array(
							'before' => "Esthétique mammaire – Avant et après",
							'after'  => "Chirurgie esthétique mammaire – Avant & Après",
						),
						'lead' => array(
							'before' => "Découvrez les résultats naturels et équilibrés obtenus par nos patients – plus complets, plus liftés ou plus proportionnés, toujours en harmonie avec le corps. Chaque résultat est personnalisé, avec des incisions placées le plus discrètement possible.",
							'after'  => "Découvrez les transformations harmonieuses et naturelles de nos patientes. Qu'il s'agisse d'une poitrine plus galbée, regalbée par un lifting ou idéalement proportionnée, chaque projet est pensé sur mesure pour sublimer la silhouette. Nous accordons un soin extrême au détail, avec des incisions positionnées de la façon la plus discrète et invisible possible.",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'faq', 'layout_index' => 11,
					'fields' => array( 'title' => array(
						'before' => "Esthétique mammaire – Questions fréquentes",
						'after'  => "Chirurgie esthétique mammaire – FAQ",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 12,
					'fields' => array(
						'title' => array(
							'before' => "Prix de l’esthétique mammaire",
							'after'  => "Tarifs de la chirurgie mammaire",
						),
						'body' => array(
							'before' => "<p>Les tarifs d'esthétique mammaire, qu'il s'agisse d'une augmentation, d'une réduction ou d'un lifting, varient en fonction de plusieurs facteurs. Ceux-ci incluent le type d’implant sélectionné, l’étendue de la procédure, les éventuels traitements supplémentaires tels qu’un lifting, une correction d’asymétrie ou des chirurgies combinées, ainsi que les besoins individuels et l’anatomie du patient. Un devis personnalisé est fourni suite à une consultation détaillée, garantissant une totale transparence avant toute prise de décision.</p>",
							'after'  => "<p>Le coût d'une chirurgie esthétique de la poitrine, qu'il s'agisse d'une augmentation, d'une réduction ou d'un lifting, dépend de plusieurs critères précis. Le type et la marque d'implants sélectionnés, la complexité de l'intervention, l'association de différents gestes chirurgicaux (comme la correction d'une asymétrie ou un lifting combiné) ainsi que votre anatomie unique influencent le plan de traitement.</p><p>Nous privilégions une transparence totale\u{00A0}: un devis personnalisé et détaillé vous sera remis à l'issue de votre consultation préopératoire, vous permettant de prendre votre décision en toute sérénité.</p>",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'form', 'layout_index' => 13,
					'fields' => array(
						'title' => array(
							'before' => "Prêt pour une silhouette naturellement équilibrée ?",
							'after'  => "Prête à révéler une silhouette naturellement équilibrée ?",
						),
						'lead' => array(
							'before' => "Partagez quelques détails et notre équipe vous répondra avec un plan de chirurgie mammaire personnalisé et un devis tout compris sans engagement, généralement dans quelques heures.",
							'after'  => "Partagez simplement quelques détails sur votre projet et notre équipe médicale concevra pour vous un protocole de chirurgie mammaire personnalisé. Vous recevrez une proposition de traitement sur mesure ainsi qu'un devis tout compris et sans engagement, généralement sous quelques heures.",
						),
					),
				),
			);

		case 'face-and-neck-lift-surgery':
			return array(
				array(
					'layout' => 'steps', 'layout_index' => 7, 'repeater' => 'items', 'row_index' => 1,
					'fields' => array( 'body' => array(
						'before' => "L’enflure diminue considérablement et le retour aux activités quotidiennes est possible.",
						'after'  => "Le gonflement diminue considérablement et le retour aux activités quotidiennes est possible.",
					) ),
				),
				array(
					'layout' => 'steps', 'layout_index' => 7, 'repeater' => 'items', 'row_index' => 2,
					'fields' => array( 'body' => array(
						'before' => "L’enflure et les ecchymoses continuent de s’estomper et la plupart des activités normales peuvent reprendre.",
						'after'  => "Le gonflement et les ecchymoses continuent de s’estomper et la plupart des activités normales peuvent reprendre.",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'faq', 'layout_index' => 10,
					'fields' => array( 'title' => array(
						'before' => "Lifting du visage et du cou — Foire aux questions",
						'after'  => "Lifting du visage et du cou — FAQ",
					) ),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'form', 'layout_index' => 12,
					'fields' => array(
						'title' => array(
							'before' => "Prêt à rafraîchir votre apparence ?",
							'after'  => "Prêt(e) à retrouver la fraîcheur de vos traits ?",
						),
						'lead' => array(
							'before' => "Partagez quelques détails et notre équipe vous répondra avec un plan personnalisé de lifting du visage et du cou et un devis tout compris sans engagement, généralement dans quelques heures.",
							'after'  => "Partagez votre projet esthétique avec nous en quelques instants. Notre équipe médicale concevra votre protocole personnalisé de lifting du visage et du cou, et vous transmettra un devis tout compris et sans engagement, généralement sous quelques heures.",
						),
					),
				),
			);

		case 'before-after':
			return array(
				array(
					'target' => 'layout_fields', 'layout' => 'hero', 'layout_index' => 0,
					'fields' => array( 'lead' => array(
						'before' => "Parcourez les montages avant-après de nos traitements de greffe capillaire, de chirurgie plastique et dentaires. Chaque image représente un patient qui nous a confié sa transformation.",
						'after'  => "Parcourez les photos avant-après de nos traitements exclusifs. De la restauration capillaire à la chirurgie plastique, en passant par l'esthétique dentaire, chaque galerie illustre le savoir-faire de nos chirurgiens et la métamorphose unique des patients qui ont choisi de nous confier leur projet de vie.",
					) ),
				),
			);

		case 'sapphire-fue-hair-transplant':
			return array(
				array(
					'target' => 'layout_fields', 'layout' => 'hero', 'layout_index' => 0,
					'fields' => array(
						'title' => array(
							'before' => "Greffe de cheveux Sapphire FUE",
							'after'  => "Greffe de cheveux FUE Saphir",
						),
						'lead' => array(
							'before' => "L’une des méthodes de restauration capillaire les plus choisies aujourd’hui. La Sapphire FUE remplace les lames traditionnelles en acier par des instruments à pointe de saphir, qui créent des canaux extrêmement précis pour un résultat plus dense et naturel, ainsi qu’une cicatrisation plus rapide.",
							'after'  => "L’une des méthodes de restauration capillaire les plus choisies aujourd’hui. La FUE Saphir remplace les lames traditionnelles en acier par des instruments à pointe de saphir, qui créent des canaux extrêmement précis pour un résultat plus dense et naturel, ainsi qu’une cicatrisation plus rapide.",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 2,
					'fields' => array(
						'title' => array(
							'before' => "Qu’est-ce que la greffe capillaire Sapphire FUE ?",
							'after'  => "Qu’est-ce que la greffe capillaire FUE Saphir ?",
						),
						'body' => array(
							'before' => "<p>Les techniques de greffe capillaire évoluent au rythme des progrès technologiques. La Sapphire FUE est une version moderne et perfectionnée de la FUE classique : les canaux destinés à recevoir les greffons sont ouverts à l’aide de lames à pointe de saphir plutôt qu’en acier. Plus lisse et plus tranchant, le bord en saphir permet de réaliser des incisions plus fines et de rapprocher les greffons en respectant l’angle et l’orientation appropriés, pour un résultat plus dense et parfaitement naturel.</p>",
							'after'  => "<p>Les techniques de greffe capillaire évoluent au rythme des progrès technologiques. La FUE Saphir est une version moderne et perfectionnée de la FUE classique : les canaux destinés à recevoir les greffons sont ouverts à l’aide de lames à pointe de saphir plutôt qu’en acier. Plus lisse et plus tranchant, le bord en saphir permet de réaliser des incisions plus fines et de rapprocher les greffons en respectant l’angle et l’orientation appropriés, pour un résultat plus dense et parfaitement naturel.</p>",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'stepbook', 'layout_index' => 3,
					'fields' => array(
						'title' => array(
							'before' => "Comment la Sapphire FUE est-elle réalisée chez Estecapelli ?",
							'after'  => "Comment la FUE Saphir est-elle réalisée chez Estecapelli ?",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'candidate', 'layout_index' => 4,
					'fields' => array(
						'title' => array(
							'before' => "Pourquoi choisir Estecapelli et la Turquie pour une Sapphire FUE ?",
							'after'  => "Pourquoi choisir Estecapelli et la Turquie pour une FUE Saphir ?",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 5,
					'fields' => array(
						'title' => array(
							'before' => "Quelles sont les différences entre la Sapphire FUE et la FUE ?",
							'after'  => "Quelles sont les différences entre la FUE Saphir et la FUE ?",
						),
						'body' => array(
							'before' => "<p><strong>Des canaux fins et réguliers.</strong> Alors que la FUE traditionnelle utilise des lames en acier, la Sapphire FUE crée des canaux très précis avec des lames à pointe de saphir qui restent tranchantes plus longtemps. Les incisions sont ainsi plus petites et plus nettes, ce qui limite le traumatisme tissulaire et les saignements. Le placement précis des greffons favorise une pousse et une densité naturelles, ainsi qu’une récupération plus rapide et plus confortable.</p><p><strong>Une ligne frontale naturelle.</strong> La technique Sapphire FUE permet d’obtenir un aspect naturel avec une précision supérieure aux méthodes traditionnelles. Les lames en saphir créent des canaux qui respectent le sens de pousse des follicules, donnant au patient une ligne frontale plus naturelle. Elles permettent aussi d’atteindre une densité élevée pour un résultat plus fourni.</p><p><strong>Moins de cicatrices.</strong> La Sapphire FUE laisse moins de marques que les techniques traditionnelles. Comme les lames en saphir conservent leur tranchant, les incisions sont plus petites et les cicatrices après la guérison sont minimes. Ces marques s’estompent rapidement.</p>",
							'after'  => "<p><strong>Des canaux fins et réguliers.</strong> Alors que la FUE traditionnelle utilise des lames en acier, la FUE Saphir crée des canaux très précis avec des lames à pointe de saphir qui restent tranchantes plus longtemps. Les incisions sont ainsi plus petites et plus nettes, ce qui limite le traumatisme tissulaire et les saignements. Le placement précis des greffons favorise une pousse et une densité naturelles, ainsi qu’une récupération plus rapide et plus confortable.</p><p><strong>Une ligne frontale naturelle.</strong> La technique FUE Saphir permet d’obtenir un aspect naturel avec une précision supérieure aux méthodes traditionnelles. Les lames en saphir créent des canaux qui respectent le sens de pousse des follicules, donnant au patient une ligne frontale plus naturelle. Elles permettent aussi d’atteindre une densité élevée pour un résultat plus fourni.</p><p><strong>Moins de cicatrices.</strong> La FUE Saphir laisse moins de marques que les techniques traditionnelles. Comme les lames en saphir conservent leur tranchant, les incisions sont plus petites et les cicatrices après la guérison sont minimes. Ces marques s’estompent rapidement.</p>",
						),
						'eyebrow' => array(
							'before' => "Sapphire FUE ou FUE",
							'after'  => "FUE Saphir ou FUE",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 7,
					'fields' => array(
						'title' => array(
							'before' => "Récupération après une Sapphire FUE : à quoi s’attendre",
							'after'  => "Récupération après une FUE Saphir : à quoi s’attendre",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'faq', 'layout_index' => 8,
					'fields' => array(
						'title' => array(
							'before' => "Sapphire FUE — Questions fréquentes",
							'after'  => "FUE Saphir — FAQ",
						),
					),
				),
				array(
					'layout' => 'faq', 'layout_index' => 8, 'repeater' => 'items', 'row_index' => 0,
					'fields' => array(
						'question' => array(
							'before' => "Quelle est la différence entre la Sapphire FUE et la FUE standard ?",
							'after'  => "Quelle est la différence entre la FUE Saphir et la FUE standard ?",
						),
						'answer' => array(
							'before' => "<p>Les deux méthodes prélèvent les follicules un par un selon la technique FUE ; la différence réside dans la lame. La FUE standard ouvre les canaux receveurs avec des lames en acier, tandis que la Sapphire FUE utilise des lames à pointe de saphir, qui restent plus tranchantes et créent des incisions plus petites et plus régulières. Les greffons peuvent ainsi être rapprochés pour obtenir davantage de densité, avec moins de traumatisme tissulaire, une ligne frontale plus naturelle, des cicatrices minimes et une cicatrisation plus rapide.</p>",
							'after'  => "<p>Les deux méthodes prélèvent les follicules un par un selon la technique FUE ; la différence réside dans la lame. La FUE standard ouvre les canaux receveurs avec des lames en acier, tandis que la FUE Saphir utilise des lames à pointe de saphir, qui restent plus tranchantes et créent des incisions plus petites et plus régulières. Les greffons peuvent ainsi être rapprochés pour obtenir davantage de densité, avec moins de traumatisme tissulaire, une ligne frontale plus naturelle, des cicatrices minimes et une cicatrisation plus rapide.</p>",
						),
					),
				),
				array(
					'layout' => 'faq', 'layout_index' => 8, 'repeater' => 'items', 'row_index' => 1,
					'fields' => array(
						'question' => array(
							'before' => "Suis-je un bon candidat à la Sapphire FUE ?",
							'after'  => "Suis-je un bon candidat à la FUE Saphir ?",
						),
					),
				),
				array(
					'layout' => 'faq', 'layout_index' => 8, 'repeater' => 'items', 'row_index' => 2,
					'fields' => array(
						'answer' => array(
							'before' => "<p>La Sapphire FUE nécessite généralement de raser les zones donneuse et receveuse afin d’offrir au chirurgien une précision maximale, mais des options de rasage partiel peuvent être étudiées lors de la consultation. La plupart des patients restent environ trois jours en Turquie, ce qui comprend le premier lavage et les instructions de soins avant le retour.</p>",
							'after'  => "<p>La FUE Saphir nécessite généralement de raser les zones donneuse et receveuse afin d’offrir au chirurgien une précision maximale, mais des options de rasage partiel peuvent être étudiées lors de la consultation. La plupart des patients restent environ trois jours en Turquie, ce qui comprend le premier lavage et les instructions de soins avant le retour.</p>",
						),
					),
				),
				array(
					'layout' => 'faq', 'layout_index' => 8, 'repeater' => 'items', 'row_index' => 3,
					'fields' => array(
						'question' => array(
							'before' => "La Sapphire FUE est-elle douloureuse et comment se passe la récupération ?",
							'after'  => "La FUE Saphir est-elle douloureuse et comment se passe la récupération ?",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'gallery', 'layout_index' => 9,
					'fields' => array(
						'title' => array(
							'before' => "Sapphire FUE — Avant et après",
							'after'  => "FUE Saphir — Avant et après",
						),
						'lead' => array(
							'before' => "Découvrez la densité et les lignes frontales naturelles obtenues par nos patients grâce à la Sapphire FUE. Chaque intervention est planifiée selon votre propre schéma de pousse, afin que le résultat se fonde harmonieusement dans vos cheveux existants et paraisse parfaitement naturel.",
							'after'  => "Découvrez la densité et les lignes frontales naturelles obtenues par nos patients grâce à la FUE Saphir. Chaque intervention est planifiée selon votre propre schéma de pousse, afin que le résultat se fonde harmonieusement dans vos cheveux existants et paraisse parfaitement naturel.",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'form', 'layout_index' => 10,
					'fields' => array(
						'title' => array(
							'before' => "Prêt à retrouver vos cheveux grâce à la Sapphire FUE ?",
							'after'  => "Prêt à retrouver vos cheveux grâce à la FUE Saphir ?",
						),
						'lead' => array(
							'before' => "Partagez quelques informations avec nous. Notre équipe vous répondra avec un protocole Sapphire FUE personnalisé, une estimation du nombre de greffons et un devis tout compris sans engagement, généralement en quelques heures.",
							'after'  => "Partagez quelques informations avec nous. Notre équipe vous répondra avec un protocole FUE Saphir personnalisé, une estimation du nombre de greffons et un devis tout compris sans engagement, généralement en quelques heures.",
						),
					),
				),
				array(
					'target' => 'layout_fields', 'layout' => 'intro', 'layout_index' => 6,
					'fields' => array( 'body' => array(
						'before' => "<p><strong>Âge.</strong> Il n’existe pas de limite d’âge stricte pour une greffe capillaire, mais les candidats ont généralement plus de 25 ans, lorsque le schéma de la chute devient plus prévisible. Une perte importante peut toutefois être présente dès 22 à 24 ans ; son étendue compte donc autant que l’âge lors de la planification.</p><p><strong>Zone donneuse.</strong> Une greffe réussie nécessite des follicules sains pouvant être prélevés en toute sécurité. La zone donneuse principale se situe généralement à l’arrière et sur les côtés du cuir chevelu, où les cheveux sont génétiquement résistants à l’affinement. Lorsque des greffons supplémentaires sont nécessaires, les poils de la barbe ou du torse peuvent parfois être utilisés. La qualité et la densité de la zone donneuse déterminent le nombre de greffons disponibles.</p><p><strong>Type de chute.</strong> La cause de la chute est un facteur important pour déterminer votre éligibilité. Les meilleurs candidats présentent généralement une alopécie androgénétique masculine ou féminine, car elle touche habituellement certaines zones du cuir chevelu sans affecter la zone donneuse. D’autres formes d’alopécie nécessitent une évaluation plus spécialisée.</p><p><strong>État de santé.</strong> Pour que l’intervention et la cicatrisation se déroulent dans de bonnes conditions, vous ne devez pas présenter de problème médical susceptible de les perturber, par exemple :</p><ul><li>un diabète non contrôlé, qui peut ralentir la cicatrisation ;</li><li>de graves troubles cardiaques ou hépatiques, susceptibles de compliquer l’anesthésie ;</li><li>une infection active ou une maladie cutanée dans les zones donneuse ou receveuse ;</li><li>une maladie auto-immune pouvant perturber la pousse des cheveux.</li></ul>",
						'after'  => "<p><strong>Âge\u{00A0}:</strong> Il n’existe pas de limite d’âge stricte pour une greffe capillaire, mais les candidats ont généralement plus de 25 ans, lorsque le schéma de la chute devient plus prévisible. Une perte importante peut toutefois être présente dès 22 à 24 ans ; son étendue compte donc autant que l’âge lors de la planification.</p><p><strong>Zone donneuse\u{00A0}:</strong> Une greffe réussie nécessite des follicules sains pouvant être prélevés en toute sécurité. La zone donneuse principale se situe généralement à l’arrière et sur les côtés du cuir chevelu, où les cheveux sont génétiquement résistants à l’affinement. Lorsque des greffons supplémentaires sont nécessaires, les poils de la barbe ou du torse peuvent parfois être utilisés. La qualité et la densité de la zone donneuse déterminent le nombre de greffons disponibles.</p><p><strong>Type de chute\u{00A0}:</strong> La cause de la chute est un facteur important pour déterminer votre éligibilité. Les meilleurs candidats présentent généralement une alopécie androgénétique masculine ou féminine, car elle touche habituellement certaines zones du cuir chevelu sans affecter la zone donneuse. D’autres formes d’alopécie nécessitent une évaluation plus spécialisée.</p><p><strong>État de santé\u{00A0}:</strong> Pour que l’intervention et la cicatrisation se déroulent dans de bonnes conditions, vous ne devez pas présenter de problème médical susceptible de les perturber, par exemple :</p><ul><li>un diabète non contrôlé, qui peut ralentir la cicatrisation ;</li><li>de graves troubles cardiaques ou hépatiques, susceptibles de compliquer l’anesthésie ;</li><li>une infection active ou une maladie cutanée dans les zones donneuse ou receveuse ;</li><li>une maladie auto-immune pouvant perturber la pousse des cheveux.</li></ul>",
					) ),
				),
			);

		default:
			return array();
	}
}

/** Assemble one page-scoped French revision patch. */
function estecapelli_safe_patch_fr_revision_definition( $source_slug, $post_type, $title ) {
	return array(
		'title'       => $title,
		'description' => 'Apply the reviewed French copy from the language revision. Every existing value is verified before writing and an exact rollback backup is kept.',
		'post_type'   => $post_type,
		'source_slug' => $source_slug,
		'schema'      => 'field_groups_v2',
		'languages'   => array(
			'fr' => estecapelli_safe_patch_fr_revision_operations( $source_slug ),
		),
	);
}

/**
 * Repair the stale flexible-content layout map on translated Gynecomastia pages.
 *
 * The translated row fields already contain the localized gallery, FAQ, price,
 * form and related-treatment copy. Five older imports retained the pre-gallery
 * ten-row layout map, so those localized values are rendered through the wrong
 * templates. Updating this single root meta value exposes the existing copy
 * without rewriting any section field or uploaded media.
 */
function estecapelli_safe_patch_gynecomastia_layout_operation() {
	$legacy = array( 'hero', 'intro', 'candidate', 'intro', 'stepbook', 'steps', 'faq', 'intro', 'form', 'related' );
	$fixed  = array( 'hero', 'intro', 'candidate', 'intro', 'stepbook', 'steps', 'gallery', 'faq', 'intro', 'form', 'related' );

	return array(
		'target'        => 'root_meta',
		'meta_key'      => 'page_sections',
		'before_values' => array( $legacy, $fixed ),
		'after_value'   => $fixed,
	);
}

/** Build one exact root-meta operation, including optional copy-from-meta. */
function estecapelli_safe_patch_root_meta_operation( $meta_key, $before_values, $after_value, $location = '' ) {
	$operation = array(
		'target'        => 'root_meta',
		'meta_key'      => $meta_key,
		'before_values' => $before_values,
		'location'      => $location ? $location : $meta_key,
	);
	if ( is_array( $after_value ) && isset( $after_value['from_meta'] ) ) {
		$operation['after_from_meta'] = (string) $after_value['from_meta'];
	} else {
		$operation['after_value'] = $after_value;
	}

	return $operation;
}

/** English FAQ copy accepted as the exact stale source on translated rows. */
function estecapelli_safe_patch_gynecomastia_english_faq() {
	return array(
		array(
			'question' => 'What is gynecomastia?',
			'answer'   => '<p>Gynecomastia is the enlargement of breast tissue in men, giving the chest a fuller, more feminine appearance. It can be caused by a hormonal imbalance between estrogen and testosterone, weight gain, certain medications, anabolic steroid use or underlying health conditions. It is very common and, while harmless medically, can affect confidence — which is why many men choose treatment.</p>',
		),
		array(
			'question' => 'How is gynecomastia treated?',
			'answer'   => '<p>Treatment depends on whether the excess is fatty tissue, firm glandular tissue or a combination. Milder cases are corrected with liposuction (often Vaser), while cases with significant glandular tissue or loose skin require surgical removal of the gland and, where needed, skin tightening. Your surgeon confirms the right approach after examining you.</p>',
		),
		array(
			'question' => 'Is the surgery painful, and what anaesthesia is used?',
			'answer'   => '<p>The procedure is performed under general anaesthesia, so you feel nothing during surgery. Afterwards most patients describe mild soreness and tightness rather than significant pain, which is well controlled with prescribed medication and eases within a few days.</p>',
		),
		array(
			'question' => 'How long is the recovery and stay in Turkey?',
			'answer'   => '<p>Most patients stay in Turkey for around three to five days, including a follow-up check. You can return to daily life within one to two weeks, while a compression garment is worn for several weeks to support shaping. Strenuous exercise and chest workouts are resumed after about a month, with your surgeon’s approval.</p>',
		),
		array(
			'question' => 'Will there be visible scars?',
			'answer'   => '<p>Liposuction-only procedures leave tiny, barely noticeable marks. When glandular tissue or skin is removed, incisions are placed as discreetly as possible — often around the edge of the areola — and fade significantly over time with proper care.</p>',
		),
		array(
			'question' => 'Are the results permanent, and can it come back?',
			'answer'   => '<p>The removed glandular tissue does not grow back, so results are long-lasting. Recurrence is uncommon, but significant weight gain, steroid use or a new hormonal imbalance can cause changes — maintaining a stable weight and healthy lifestyle keeps your results looking their best.</p>',
		),
		array(
			'question' => 'Does the chest look natural afterwards?',
			'answer'   => '<p>Yes. Performed with the right technique, gynecomastia surgery creates a flat, firm and naturally masculine chest contour. The aim is always a result that looks like it was never operated on, in harmony with your body.</p>',
		),
	);
}

/**
 * Restore one localized FAQ plus the final image moved into the gallery row.
 *
 * The content files are hash-pinned so this immutable patch cannot silently
 * change if a translation JSON is edited in the future.
 */
function estecapelli_safe_patch_gynecomastia_faq_gallery_operations( $language ) {
	static $cache = array();
	if ( isset( $cache[ $language ] ) ) {
		return $cache[ $language ];
	}

	$hashes = array(
		'fr' => '5c3b36a41ad3a8a8c152f2159c3651f3e883737bcf2f785661ff4cbc8345f461',
		'it' => '65fcad12f844dadfd2ded1ca7b0d74657bd59820cf8c694a4e4f0efeb9788c58',
		'es' => '0f5adcadb7740071b4cdd91bcc406ffec6d9a3eb9392b6f872255ee5a77d0cad',
		'pl' => '6ea91461678452554573a4186768974cadf40450ee6385fadc46273923696932',
		'pt' => '32709375a9dbe390f0994f16db4cdc510202eee1f38ca55d3d41d2b9f78f0be8',
	);
	$file = get_template_directory() . '/inc/data/translations/' . $language . '/plastic-surgery/gynecomastia.json';
	$raw  = is_readable( $file ) ? (string) file_get_contents( $file ) : '';
	$hash = hash( 'sha256', str_replace( array( "\r\n", "\r" ), "\n", $raw ) );
	if ( ! isset( $hashes[ $language ] ) || ! $raw || $hashes[ $language ] !== $hash ) {
		return array( array( 'target' => 'configuration_error', 'message' => sprintf( 'The pinned %s Gynecomastia translation source is missing or changed.', strtoupper( $language ) ) ) );
	}

	$data = json_decode( $raw, true );
	$faq  = null;
	foreach ( (array) ( $data['sections'] ?? array() ) as $section ) {
		if ( 'faq' === ( $section['acf_fc_layout'] ?? '' ) ) {
			$faq = $section;
			break;
		}
	}
	if ( ! is_array( $faq ) || 7 !== count( $faq['items'] ?? array() ) ) {
		return array( array( 'target' => 'configuration_error', 'message' => sprintf( 'The pinned %s Gynecomastia FAQ is invalid.', strtoupper( $language ) ) ) );
	}

	$operations   = array( estecapelli_safe_patch_gynecomastia_layout_operation() );
	$operations[] = estecapelli_safe_patch_root_meta_operation( 'page_sections_6_items', array( '', '0', '1' ), '1', 'gallery item count' );
	$operations[] = estecapelli_safe_patch_root_meta_operation( '_page_sections_6_items', array( '', 'field_gal_items' ), 'field_gal_items', 'gallery field reference' );
	$operations[] = estecapelli_safe_patch_root_meta_operation( 'page_sections_6_items_0_image', array( '', '0' ), array( 'from_meta' => 'page_sections_7_image' ), 'gallery image' );
	$operations[] = estecapelli_safe_patch_root_meta_operation( '_page_sections_6_items_0_image', array( '', 'field_gal_image' ), 'field_gal_image', 'gallery image field reference' );
	$operations[] = estecapelli_safe_patch_root_meta_operation( 'page_sections_7_items', array( '', '0', '7' ), '7', 'FAQ item count' );
	$operations[] = estecapelli_safe_patch_root_meta_operation( '_page_sections_7_items', array( '', 'field_faq_items' ), 'field_faq_items', 'FAQ field reference' );

	$english = estecapelli_safe_patch_gynecomastia_english_faq();
	foreach ( $faq['items'] as $index => $item ) {
		$question_key = 'page_sections_7_items_' . $index . '_question';
		$answer_key   = 'page_sections_7_items_' . $index . '_answer';
		$operations[] = estecapelli_safe_patch_root_meta_operation(
			$question_key,
			array( '', $english[ $index ]['question'], $item['question'] ),
			$item['question'],
			'FAQ row ' . ( $index + 1 ) . ' question'
		);
		$operations[] = estecapelli_safe_patch_root_meta_operation(
			'_' . $question_key,
			array( '', 'field_faq_q' ),
			'field_faq_q',
			'FAQ row ' . ( $index + 1 ) . ' question reference'
		);
		$operations[] = estecapelli_safe_patch_root_meta_operation(
			$answer_key,
			array( '', $english[ $index ]['answer'], $item['answer'] ),
			$item['answer'],
			'FAQ row ' . ( $index + 1 ) . ' answer'
		);
		$operations[] = estecapelli_safe_patch_root_meta_operation(
			'_' . $answer_key,
			array( '', 'field_faq_a' ),
			'field_faq_a',
			'FAQ row ' . ( $index + 1 ) . ' answer reference'
		);
	}

	$cache[ $language ] = $operations;
	return $cache[ $language ];
}

/** Immutable patch registry. Applied patch IDs must never be edited or reused. */
function estecapelli_safe_content_patches() {
	return array(
		'gynecomastia-localized-faq-layout-20260724-v1' => array(
			'title'         => 'Gynecomastia — restore localized FAQ layouts',
			'description'   => 'Restore the Gallery + FAQ layout map for the five affected Gynecomastia translations. Their localized FAQ copy is already stored in its own ACF rows; this exposes it through the correct template without changing any section text or media. Turkish is already rendering its localized FAQ correctly and is intentionally left untouched.',
			'post_type'     => 'treatment',
			'source_slug'   => 'gynecomastia',
			'schema'        => 'field_groups_v2',
			'languages'     => array(
				'fr' => array( estecapelli_safe_patch_gynecomastia_layout_operation() ),
				'it' => array( estecapelli_safe_patch_gynecomastia_layout_operation() ),
				'es' => array( estecapelli_safe_patch_gynecomastia_layout_operation() ),
				'pl' => array( estecapelli_safe_patch_gynecomastia_layout_operation() ),
				'pt' => array( estecapelli_safe_patch_gynecomastia_layout_operation() ),
			),
		),
		'gynecomastia-localized-faq-and-gallery-20260724-v2' => array(
			'title'         => 'Gynecomastia — restore localized FAQ + final image (RETIRED — do not apply)',
			'description'   => 'RETIRED: this patch rewrote ACF internal field references by hand and could misalign the rows. It is kept in the registry only so an existing application can still be rolled back. Use the layout-map patch (v1) instead.',
			'post_type'     => 'treatment',
			'source_slug'   => 'gynecomastia',
			'schema'        => 'field_groups_v2',
			'superseded_by' => 'gynecomastia-localized-faq-layout-20260724-v1',
			'languages'     => array(
				'fr' => estecapelli_safe_patch_gynecomastia_faq_gallery_operations( 'fr' ),
				'it' => estecapelli_safe_patch_gynecomastia_faq_gallery_operations( 'it' ),
				'es' => estecapelli_safe_patch_gynecomastia_faq_gallery_operations( 'es' ),
				'pl' => estecapelli_safe_patch_gynecomastia_faq_gallery_operations( 'pl' ),
				'pt' => estecapelli_safe_patch_gynecomastia_faq_gallery_operations( 'pt' ),
			),
		),
		'fr-revision-rhinoplasty-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'rhinoplasty', 'treatment', 'French revision — Rhinoplastie' ),
		'fr-revision-liposuction-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'liposuction', 'treatment', 'French revision — Liposuccion' ),
		'fr-revision-gynecomastia-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'gynecomastia', 'treatment', 'French revision — Gynécomastie' ),
		'fr-revision-abdominoplasty-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'abdominoplasty-tummy-tuck', 'treatment', 'French revision — Abdominoplastie' ),
		'fr-revision-bariatric-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'obesity-surgeries-bariatric-surgery-and-gastric-balloon', 'treatment', 'French revision — Chirurgie bariatrique' ),
		'fr-revision-dental-implant-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'dental-implant', 'treatment', 'French revision — Implants dentaires' ),
		'fr-revision-hollywood-smile-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'hollywood-smile', 'treatment', 'French revision — Hollywood Smile' ),
		'fr-revision-breast-surgery-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'breast-aesthetics-breast-surgery', 'treatment', 'French revision — Chirurgie mammaire' ),
		'fr-revision-face-lift-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'face-and-neck-lift-surgery', 'treatment', 'French revision — Lifting visage/cou' ),
		'fr-revision-before-after-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'before-after', 'page', 'French revision — Avant & Après' ),
		'fr-revision-sapphire-fue-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'sapphire-fue-hair-transplant', 'treatment', 'French revision — FUE Saphir' ),
		'fr-revision-about-us-20260724-v1' => estecapelli_safe_patch_fr_revision_definition( 'about-us', 'page', 'French revision — À propos (tarifs)' ),
		'pt-review-exosome-fue-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'exosome-fue-hair-transplant', 'treatment', 'Portuguese review — Exosome FUE' ),
		'pt-review-vita-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'vita-treatment', 'treatment', 'Portuguese review — VITA Treatment' ),
		'pt-review-sapphire-fue-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'sapphire-fue-hair-transplant', 'treatment', 'Portuguese review — Sapphire FUE' ),
		'pt-review-dhi-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'dhi-hair-transplant', 'treatment', 'Portuguese review — DHI' ),
		'pt-review-female-hair-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'female-hair-transplant', 'treatment', 'Portuguese review — Female Hair Transplant' ),
		'pt-review-mesotherapy-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'hair-mesotherapy', 'treatment', 'Portuguese review — Hair Mesotherapy' ),
		'pt-review-eyebrow-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'eyebrow-transplant', 'treatment', 'Portuguese review — Eyebrow Transplant' ),
		'pt-review-beard-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'beard-transplant', 'treatment', 'Portuguese review — Beard Transplant' ),
		'pt-review-pre-transplant-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'pre-hair-transplant-period', 'page', 'Portuguese review — Pre-transplant Period' ),
		'remove-pretransplant-step7-20260724-v1' => array(
			'title'       => 'Pre-transplant — remove erroneous Step 7',
			'description' => 'Remove the extra 7th Step Book step ("Estecapelli Recommendations") from the pre-transplant page so Portuguese, Spanish, Polish and Turkish match the 6-step English page. Only the Step Book row count is changed (7 → 6); each language is verified to currently show 7 steps before writing, and an exact rollback backup is kept.',
			'post_type'   => 'page',
			'source_slug' => 'pre-hair-transplant-period',
			'schema'      => 'field_groups_v2',
			'languages'   => array(
				'pt' => array( estecapelli_safe_patch_remove_last_step_operation( 7 ) ),
				'es' => array( estecapelli_safe_patch_remove_last_step_operation( 7 ) ),
				'pl' => array( estecapelli_safe_patch_remove_last_step_operation( 7 ) ),
				'tr' => array( estecapelli_safe_patch_remove_last_step_operation( 7 ) ),
			),
		),
		'pt-review-post-transplant-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'post-hair-transplant-period', 'page', 'Portuguese review — Post-transplant Period' ),
		'pt-review-tricholab-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'tricholab', 'page', 'Portuguese review — TrichoLab' ),
		'pt-review-rhinoplasty-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'rhinoplasty', 'treatment', 'Portuguese review — Rhinoplasty' ),
		'pt-review-bbl-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'bbl', 'treatment', 'Portuguese review — BBL' ),
		'pt-review-liposuction-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'liposuction', 'treatment', 'Portuguese review — Liposuction' ),
		'pt-review-breast-surgery-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'breast-aesthetics-breast-surgery', 'treatment', 'Portuguese review — Breast Surgery' ),
		'pt-review-face-lift-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'face-and-neck-lift-surgery', 'treatment', 'Portuguese review — Face and Neck Lift' ),
		'pt-review-bariatric-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'obesity-surgeries-bariatric-surgery-and-gastric-balloon', 'treatment', 'Portuguese review — Bariatric Surgery' ),
		'pt-review-our-team-20260724-v1' => estecapelli_safe_patch_pt_review_definition( 'our-team', 'page', 'Portuguese review — Our Team' ),
		'exosome-stepbook-rich-tr-20260724-v1' => array(
			'title'       => 'Exosome FUE — Rich Turkish Step Book copy',
			'description' => 'Expand all five Turkish procedure-stage descriptions to match the fuller two-to-three-sentence copy already used in the other six languages.',
			'post_type'   => 'treatment',
			'source_slug' => 'exosome-fue-hair-transplant',
			'schema'      => 'field_groups_v2',
			'languages'   => array(
				'tr' => estecapelli_safe_patch_exosome_rich_tr_step_operations(),
			),
		),
		'exosome-quick-facts-20260723-v1' => array(
			'title'       => 'Exosome FUE — Quick Facts revision',
			'description' => 'Replace anaesthesia, procedure-time and recovery facts on the Exosome FUE page in all seven languages.',
			'post_type'   => 'treatment',
			'source_slug' => 'exosome-fue-hair-transplant',
			'layout'      => 'quick_stats',
			'languages'   => array(
				'en' => array(
					1 => array(
						'before' => array( 'value' => 'Local', 'label' => 'Anaesthesia' ),
						'after'  => array( 'value' => 'Painless', 'label' => 'Anaesthesia' ),
					),
					2 => array(
						'before' => array( 'value' => array( '6–8 hrs', '6-8 hrs' ), 'label' => 'Procedure Time' ),
						'after'  => array( 'value' => 'Grafts Can Remain Viable', 'label' => 'For 72 Hours' ),
					),
					3 => array(
						'before' => array( 'value' => array( '7–10 days', '7-10 days' ), 'label' => array( 'Recovery Time', 'Recovery Times' ) ),
						'after'  => array( 'value' => '1%', 'label' => 'Graft Loss (Decreased from 15–20%)' ),
					),
				),
				'fr' => array(
					1 => array(
						'before' => array( 'value' => 'Locale', 'label' => 'Anesthésie' ),
						'after'  => array( 'value' => 'Sans douleur', 'label' => 'Anesthésie' ),
					),
					2 => array(
						'before' => array( 'value' => array( '6–8 h', '6-8 h' ), 'label' => 'Durée de l’intervention' ),
						'after'  => array( 'value' => 'Greffons viables', 'label' => 'Jusqu’à 72 heures' ),
					),
					3 => array(
						'before' => array( 'value' => array( '7–10 jours', '7-10 jours' ), 'label' => 'Temps de récupération' ),
						'after'  => array( 'value' => '1 %', 'label' => 'Perte de greffons (contre 15–20 %)' ),
					),
				),
				'it' => array(
					1 => array(
						'before' => array( 'value' => 'Locale', 'label' => 'Anestesia' ),
						'after'  => array( 'value' => 'Indolore', 'label' => 'Anestesia' ),
					),
					2 => array(
						'before' => array( 'value' => array( '6–8 ore', '6-8 ore' ), 'label' => "Durata dell'intervento" ),
						'after'  => array( 'value' => 'Innesti vitali', 'label' => 'Fino a 72 ore' ),
					),
					3 => array(
						'before' => array( 'value' => array( '7–10 giorni', '7-10 giorni' ), 'label' => 'Tempo di recupero' ),
						'after'  => array( 'value' => '1%', 'label' => 'Perdita degli innesti (dal 15–20%)' ),
					),
				),
				'es' => array(
					1 => array(
						'before' => array( 'value' => 'Local', 'label' => 'Anestesia' ),
						'after'  => array( 'value' => 'Sin dolor', 'label' => 'Anestesia' ),
					),
					2 => array(
						'before' => array( 'value' => array( '6–8 horas', '6-8 horas' ), 'label' => 'Duración del procedimiento' ),
						'after'  => array( 'value' => 'Injertos viables', 'label' => 'Hasta 72 horas' ),
					),
					3 => array(
						'before' => array( 'value' => array( '7–10 días', '7-10 días' ), 'label' => 'Tiempo de recuperación' ),
						'after'  => array( 'value' => '1 %', 'label' => 'Pérdida de injertos (reducida del 15–20 %)' ),
					),
				),
				'pl' => array(
					1 => array(
						'before' => array( 'value' => 'Miejscowe', 'label' => 'Znieczulenie' ),
						'after'  => array( 'value' => 'Bezbolesne', 'label' => 'Znieczulenie' ),
					),
					2 => array(
						'before' => array( 'value' => array( '6–8 godz.', '6-8 godz.' ), 'label' => 'Czas zabiegu' ),
						'after'  => array( 'value' => 'Żywotne grafty', 'label' => 'Do 72 godzin' ),
					),
					3 => array(
						'before' => array( 'value' => array( '7–10 dni', '7-10 dni' ), 'label' => 'Rekonwalescencja' ),
						'after'  => array( 'value' => '1%', 'label' => 'Utrata graftów (spadek z 15–20%)' ),
					),
				),
				'pt' => array(
					1 => array(
						'before' => array( 'value' => 'Local', 'label' => 'Anestesia' ),
						'after'  => array( 'value' => 'Indolor', 'label' => 'Anestesia' ),
					),
					2 => array(
						'before' => array( 'value' => array( '6–8 horas', '6-8 horas' ), 'label' => 'Duração do procedimento' ),
						'after'  => array( 'value' => 'Enxertos viáveis', 'label' => 'Até 72 horas' ),
					),
					3 => array(
						'before' => array( 'value' => array( '7–10 dias', '7-10 dias' ), 'label' => 'Tempo de recuperação' ),
						'after'  => array( 'value' => '1 %', 'label' => 'Perda de enxertos (reduzida de 15–20 %)' ),
					),
				),
				'tr' => array(
					1 => array(
						'before' => array( 'value' => 'Yerel', 'label' => 'Anestezi' ),
						'after'  => array( 'value' => 'Ağrısız', 'label' => 'Anestezi' ),
					),
					2 => array(
						'before' => array( 'value' => array( '6–8 saat', '6-8 saat' ), 'label' => 'İşlem Süresi' ),
						'after'  => array( 'value' => 'Greftler Canlı Kalır', 'label' => '72 Saate Kadar' ),
					),
					3 => array(
						'before' => array( 'value' => array( '7–10 gün', '7-10 gün' ), 'label' => 'İyileşme Süresi' ),
						'after'  => array( 'value' => '%1', 'label' => 'Greft Kaybı (%15–20’den Düştü)' ),
					),
				),
			),
		),
		'sapphire-facts-anaesthesia-20260723-v1' => array(
			'title'       => 'Sapphire FUE — Quick Facts and painless anaesthesia',
			'description' => 'Revise two Quick Facts and Stage 2 of The Procedure on the Sapphire FUE page in all seven languages.',
			'post_type'   => 'treatment',
			'source_slug' => 'sapphire-fue-hair-transplant',
			'schema'      => 'field_groups_v2',
			'languages'   => array(
				'en' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Local', 'after' => 'Painless' ),
							'label' => array( 'before' => 'Anaesthesia', 'after' => 'Anaesthesia' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => '1 session', 'after' => 'Sapphire Blade' ),
							'label' => array( 'before' => 'Sessions', 'after' => 'Channel Opening' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Local Anaesthesia', 'after' => 'Painless Anaesthesia' ),
							'body'  => array(
								'before' => '<p>Local anaesthesia is applied to the donor and recipient areas so the entire session is comfortable and pain-free. You stay awake but relaxed throughout, and can listen to music or watch something during the procedure.</p>',
								'after'  => '<p>Painless anaesthesia is applied to the donor and recipient areas so the entire session is comfortable and pain-free. You stay awake but relaxed throughout, and can listen to music or watch something during the procedure.</p>',
							),
						),
					),
				),
				'fr' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Locale', 'after' => 'Sans douleur' ),
							'label' => array( 'before' => 'Anesthésie', 'after' => 'Anesthésie' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => '1 séance', 'after' => 'Lame en saphir' ),
							'label' => array( 'before' => 'Séances', 'after' => 'Ouverture des canaux' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Anesthésie locale', 'after' => 'Anesthésie sans douleur' ),
							'body'  => array(
								'before' => '<p>Une anesthésie locale est appliquée aux zones donneuse et receveuse afin que toute la séance soit confortable et indolore. Vous restez éveillé, mais détendu, et pouvez écouter de la musique ou regarder un programme pendant l’intervention.</p>',
								'after'  => '<p>Une anesthésie sans douleur est appliquée aux zones donneuse et receveuse afin que toute la séance se déroule confortablement. Vous restez éveillé, mais détendu, et pouvez écouter de la musique ou regarder un programme pendant l’intervention.</p>',
							),
						),
					),
				),
				'it' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Locale', 'after' => 'Indolore' ),
							'label' => array( 'before' => 'Anestesia', 'after' => 'Anestesia' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => '1 seduta', 'after' => 'Lama in zaffiro' ),
							'label' => array( 'before' => 'Sedute', 'after' => 'Apertura dei canali' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Anestesia locale', 'after' => 'Anestesia indolore' ),
							'body'  => array(
								'before' => "<p>L'anestesia locale viene applicata alle aree donatrice e ricevente affinché l'intera seduta sia confortevole e indolore. Rimane sveglio ma rilassato per tutta la durata e può ascoltare musica o guardare qualcosa durante l'intervento.</p>",
								'after'  => "<p>L'anestesia indolore viene applicata alle aree donatrice e ricevente per garantire il massimo comfort durante l'intera seduta. Rimane sveglio ma rilassato per tutta la durata e può ascoltare musica o guardare qualcosa durante l'intervento.</p>",
							),
						),
					),
				),
				'es' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Local', 'after' => 'Sin dolor' ),
							'label' => array( 'before' => 'Anestesia', 'after' => 'Anestesia' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => '1 sesión', 'after' => 'Hoja de zafiro' ),
							'label' => array( 'before' => 'Sesiones', 'after' => 'Apertura de canales' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Anestesia local', 'after' => 'Anestesia sin dolor' ),
							'body'  => array(
								'before' => '<p>Aplicamos anestesia local en las zonas donante y receptora para que toda la sesión sea cómoda y no cause dolor. Permanecerá despierto pero relajado durante el procedimiento y podrá escuchar música o ver algún contenido.</p>',
								'after'  => '<p>Aplicamos anestesia sin dolor en las zonas donante y receptora para que toda la sesión sea cómoda. Permanecerá despierto pero relajado durante el procedimiento y podrá escuchar música o ver algún contenido.</p>',
							),
						),
					),
				),
				'pl' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Miejscowe', 'after' => 'Bezbolesne' ),
							'label' => array( 'before' => 'Znieczulenie', 'after' => 'Znieczulenie' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => '1 sesja', 'after' => 'Ostrze szafirowe' ),
							'label' => array( 'before' => 'Liczba sesji', 'after' => 'Otwieranie kanałów' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Znieczulenie miejscowe', 'after' => 'Bezbolesne znieczulenie' ),
							'body'  => array(
								'before' => '<p>W obszarze dawczym i biorczym stosuje się znieczulenie miejscowe, dzięki czemu cała sesja jest komfortowa i bezbolesna. Pozostajesz przytomny i zrelaksowany przez cały czas i możesz słuchać muzyki lub oglądać film podczas zabiegu.</p>',
								'after'  => '<p>W obszarze dawczym i biorczym stosuje się bezbolesne znieczulenie, dzięki czemu cała sesja przebiega komfortowo. Pozostajesz przytomny i zrelaksowany przez cały czas i możesz słuchać muzyki lub oglądać film podczas zabiegu.</p>',
							),
						),
					),
				),
				'pt' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Local', 'after' => 'Indolor' ),
							'label' => array( 'before' => 'Anestesia', 'after' => 'Anestesia' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => '1 sessão', 'after' => 'Lâmina de safira' ),
							'label' => array( 'before' => 'Sessões', 'after' => 'Abertura dos canais' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Anestesia local', 'after' => 'Anestesia indolor' ),
							'body'  => array(
								'before' => '<p>Aplicamos anestesia local nas zonas dadora e recetora para que toda a sessão seja confortável e não cause dor. Permanecerá acordado mas relaxado durante o procedimento e poderá ouvir música ou ver algum conteúdo.</p>',
								'after'  => '<p>Aplicamos anestesia indolor nas zonas dadora e recetora para que toda a sessão seja confortável. Permanecerá acordado mas relaxado durante o procedimento e poderá ouvir música ou ver algum conteúdo.</p>',
							),
						),
					),
				),
				'tr' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Yerel', 'after' => 'Ağrısız' ),
							'label' => array( 'before' => 'Anestezi', 'after' => 'Anestezi' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => '1 seans', 'after' => 'Safir Bıçak' ),
							'label' => array( 'before' => 'Seans Sayısı', 'after' => 'Kanal Açma' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Lokal Anestezi', 'after' => 'Ağrısız Anestezi' ),
							'body'  => array(
								'before' => '<p>Donör ve alıcı bölgelere lokal anestezi uygulanarak tüm seansın konforlu ve ağrısız geçmesi sağlanır. İşlem boyunca uyanık ama rahat kalırsınız ve işlem sırasında müzik dinleyebilir veya bir şeyler izleyebilirsiniz.</p>',
								'after'  => '<p>Donör ve alıcı bölgelere ağrısız anestezi uygulanarak tüm seansın konforlu geçmesi sağlanır. İşlem boyunca uyanık ama rahat kalırsınız ve işlem sırasında müzik dinleyebilir veya bir şeyler izleyebilirsiniz.</p>',
							),
						),
					),
				),
			),
		),
		'dhi-facts-anaesthesia-20260723-v1' => array(
			'title'       => 'DHI — Quick Facts and painless anaesthesia',
			'description' => 'Revise two Quick Facts and Stage 2 of The Procedure on the DHI page in all seven languages.',
			'post_type'   => 'treatment',
			'source_slug' => 'dhi-hair-transplant',
			'schema'      => 'field_groups_v2',
			'languages'   => array(
				'en' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Local', 'after' => 'Painless' ),
							'label' => array( 'before' => 'Anaesthesia', 'after' => 'Anaesthesia' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => 'No channels', 'after' => 'No Shave' ),
							'label' => array( 'before' => 'Technique', 'after' => '' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Local Anaesthesia', 'after' => 'Painless Anaesthesia' ),
							'body'  => array(
								'before' => '<p>Local anaesthesia is applied to the donor and recipient areas for a comfortable, pain-free procedure. You stay awake but relaxed throughout, and can listen to music or watch something during the session.</p>',
								'after'  => '<p>Painless anaesthesia is applied to the donor and recipient areas for a comfortable, pain-free procedure. You stay awake but relaxed throughout, and can listen to music or watch something during the session.</p>',
							),
						),
					),
				),
				'fr' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Locale', 'after' => 'Sans douleur' ),
							'label' => array( 'before' => 'Anesthésie', 'after' => 'Anesthésie' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => 'Sans canaux', 'after' => 'Sans rasage' ),
							'label' => array( 'before' => 'Technique', 'after' => '' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Anesthésie locale', 'after' => 'Anesthésie sans douleur' ),
							'body'  => array(
								'before' => '<p>Une anesthésie locale est appliquée aux zones donneuse et receveuse pour assurer une intervention confortable et indolore. Vous restez éveillé, mais détendu, et pouvez écouter de la musique ou regarder un programme pendant la séance.</p>',
								'after'  => '<p>Une anesthésie sans douleur est appliquée aux zones donneuse et receveuse pour assurer une intervention confortable. Vous restez éveillé, mais détendu, et pouvez écouter de la musique ou regarder un programme pendant la séance.</p>',
							),
						),
					),
				),
				'it' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Locale', 'after' => 'Indolore' ),
							'label' => array( 'before' => 'Anestesia', 'after' => 'Anestesia' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => 'Senza canali', 'after' => 'Senza rasatura' ),
							'label' => array( 'before' => 'Tecnica', 'after' => '' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Anestesia locale', 'after' => 'Anestesia indolore' ),
							'body'  => array(
								'before' => "<p>L'anestesia locale viene applicata alle aree donatrice e ricevente per un intervento confortevole e indolore. Rimane sveglio ma rilassato per tutta la durata e può ascoltare musica o guardare qualcosa durante la seduta.</p>",
								'after'  => "<p>L'anestesia indolore viene applicata alle aree donatrice e ricevente per garantire un intervento confortevole. Rimane sveglio ma rilassato per tutta la durata e può ascoltare musica o guardare qualcosa durante la seduta.</p>",
							),
						),
					),
				),
				'es' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Local', 'after' => 'Sin dolor' ),
							'label' => array( 'before' => 'Anestesia', 'after' => 'Anestesia' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => 'Sin canales previos', 'after' => 'Sin rapado' ),
							'label' => array( 'before' => 'Técnica', 'after' => '' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Anestesia local', 'after' => 'Anestesia sin dolor' ),
							'body'  => array(
								'before' => '<p>Aplicamos anestesia local en las zonas donante y receptora para que el procedimiento sea cómodo y no cause dolor. Permanecerá despierto pero relajado durante toda la intervención y podrá escuchar música o ver algún contenido durante la sesión.</p>',
								'after'  => '<p>Aplicamos anestesia sin dolor en las zonas donante y receptora para que el procedimiento sea cómodo. Permanecerá despierto pero relajado durante toda la intervención y podrá escuchar música o ver algún contenido durante la sesión.</p>',
							),
						),
					),
				),
				'pl' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Miejscowe', 'after' => 'Bezbolesne' ),
							'label' => array( 'before' => 'Znieczulenie', 'after' => 'Znieczulenie' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => 'Brak kanałów', 'after' => 'Bez golenia' ),
							'label' => array( 'before' => 'Technika', 'after' => '' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Znieczulenie miejscowe', 'after' => 'Bezbolesne znieczulenie' ),
							'body'  => array(
								'before' => '<p>W obszarze dawczym i biorczym stosujemy znieczulenie miejscowe, dzięki czemu zabieg przebiega komfortowo i bez bólu. Przez całą sesję pozostajesz przytomny i zrelaksowany; możesz słuchać muzyki lub oglądać film.</p>',
								'after'  => '<p>W obszarze dawczym i biorczym stosujemy bezbolesne znieczulenie, dzięki czemu zabieg przebiega komfortowo. Przez całą sesję pozostajesz przytomny i zrelaksowany; możesz słuchać muzyki lub oglądać film.</p>',
							),
						),
					),
				),
				'pt' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Local', 'after' => 'Indolor' ),
							'label' => array( 'before' => 'Anestesia', 'after' => 'Anestesia' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => 'Sem canais prévios', 'after' => 'Sem rapar' ),
							'label' => array( 'before' => 'Técnica', 'after' => '' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Anestesia local', 'after' => 'Anestesia indolor' ),
							'body'  => array(
								'before' => '<p>Aplicamos anestesia local nas zonas dadora e recetora para que o procedimento seja confortável e não cause dor. Permanecerá acordado mas relaxado durante toda a intervenção e poderá ouvir música ou ver algum conteúdo durante a sessão.</p>',
								'after'  => '<p>Aplicamos anestesia indolor nas zonas dadora e recetora para que o procedimento seja confortável. Permanecerá acordado mas relaxado durante toda a intervenção e poderá ouvir música ou ver algum conteúdo durante a sessão.</p>',
							),
						),
					),
				),
				'tr' => array(
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 0,
						'fields' => array(
							'value' => array( 'before' => 'Yerel', 'after' => 'Ağrısız' ),
							'label' => array( 'before' => 'Anestezi', 'after' => 'Anestezi' ),
						),
					),
					array(
						'layout' => 'quick_stats', 'repeater' => 'stats', 'row_index' => 2,
						'fields' => array(
							'value' => array( 'before' => 'Kanal Açma Yok', 'after' => 'Tıraşsız' ),
							'label' => array( 'before' => 'Teknik', 'after' => '' ),
						),
					),
					array(
						'layout' => 'stepbook', 'repeater' => 'items', 'row_index' => 1,
						'fields' => array(
							'title' => array( 'before' => 'Lokal Anestezi', 'after' => 'Ağrısız Anestezi' ),
							'body'  => array(
								'before' => '<p>Konforlu ve ağrısız bir deneyim için donör ve alıcı bölgelere lokal anestezi uygulanır. İşlem boyunca uyanık ancak rahat olursunuz; seans sırasında müzik dinleyebilir veya bir şeyler izleyebilirsiniz.</p>',
								'after'  => '<p>Konforlu bir deneyim için donör ve alıcı bölgelere ağrısız anestezi uygulanır. İşlem boyunca uyanık ancak rahat olursunuz; seans sırasında müzik dinleyebilir veya bir şeyler izleyebilirsiniz.</p>',
							),
						),
					),
				),
			),
		),
		'vita-remove-empty-translated-step-20260723-v1' => array(
			'title'       => 'VITA — Remove empty Step 4 from translations',
			'description' => 'Reduce the affected translated VITA Step Books from four rows to three, only after verifying that the fourth row has no text or media. English and translations already showing three steps are intentionally excluded.',
			'post_type'   => 'treatment',
			'source_slug' => 'vita-treatment',
			'schema'      => 'field_groups_v2',
			'superseded_by' => 'vita-four-step-procedure-20260723-v1',
			'languages'   => array(
				'fr' => estecapelli_safe_patch_vita_empty_step_operations(),
				'it' => estecapelli_safe_patch_vita_empty_step_operations(),
				'es' => estecapelli_safe_patch_vita_empty_step_operations(),
				'pt' => estecapelli_safe_patch_vita_empty_step_operations(),
			),
		),
		'vita-four-step-procedure-20260723-v1' => array(
			'title'       => 'VITA — Four-step procedure with FUE/DHI choice',
			'description' => 'Add the FUE/DHI choice as Step 1, move the three existing texts forward without touching their current images, and add the supplied PRP image to Step 4 in all seven languages.',
			'post_type'   => 'treatment',
			'source_slug' => 'vita-treatment',
			'schema'      => 'field_groups_v2',
			'languages'   => array(
				'en' => estecapelli_safe_patch_vita_four_step_operations(
					array(
						'lead_before' => 'A three-stage protocol that integrates seamlessly into your Sapphire FUE or DHI transplant. Swipe through the stages below.',
						'lead_after'  => 'A four-stage protocol that integrates seamlessly into your Sapphire FUE or DHI transplant. Swipe through the stages below.',
						'new_first'   => array(
							'title' => 'Choose Between FUE and DHI',
							'body'  => '<p>Both Sapphire FUE and DHI can be seamlessly combined with the VITA Treatment Protocol. You can choose between these two methods based on your hair structure, donor area and goals, with our medical team helping you select the most suitable approach.</p>',
						),
						'stage_4'     => 'Stage 4',
						'old_items'   => array(
							array( 'eyebrow' => 'Stage 1', 'title' => 'Microneedling & Nutrient Serum', 'body' => '<p>Microchannels are created with a dermaroller massage tool, and a highly concentrated serum of vitamins, amino acids, growth factors and minerals is applied to boost blood circulation. The donor area is ideally prepared for smooth, well-fed harvesting, while the recipient area is nourished with a concentrated vitamin complex that the scalp absorbs far more effectively thanks to the micro-channels.</p>' ),
							array( 'eyebrow' => 'Stage 2', 'title' => 'Cold-Vapour Serum on Grafts', 'body' => '<p>The same nourishing serum is mixed with saline in the graft-harvesting tray and applied to the harvested grafts using a cold-vapour method. This is the most critical window — while follicles are outside the body — so protecting them here prevents deterioration, maximises survival and increases their post-transplantation regrowth potential.</p>' ),
							array( 'eyebrow' => 'Stage 3', 'title' => 'PRP After the Procedure', 'body' => '<p>PRP (Platelet-Rich Plasma) is applied as the final stage of the treatment — a regenerative therapy performed immediately after your hair transplant to support healing and improve the overall result. A small sample of your blood is collected and processed in a centrifuge to separate the platelet-rich plasma, which contains a high concentration of natural growth factors. The PRP is then carefully injected into the treated areas of the scalp, where it helps accelerate tissue repair, reduce inflammation, improve blood circulation and create an optimal environment for the newly transplanted grafts. By stimulating the healing process and nourishing the follicles, PRP contributes to stronger graft retention, faster recovery and healthier, thicker hair growth.</p>' ),
						),
					)
				),
				'fr' => estecapelli_safe_patch_vita_four_step_operations(
					array(
						'lead_before' => 'Un protocole en trois étapes qui s’intègre parfaitement à votre greffe Sapphire FUE ou DHI. Parcourez-les ci-dessous.',
						'lead_after'  => 'Un protocole en quatre étapes qui s’intègre parfaitement à votre greffe Sapphire FUE ou DHI. Parcourez-les ci-dessous.',
						'new_first'   => array(
							'title' => 'Choisir entre la FUE et la DHI',
							'body'  => '<p>La Sapphire FUE comme la DHI peuvent être facilement associées au protocole de traitement VITA. Vous pouvez choisir entre ces deux méthodes selon la structure de vos cheveux, votre zone donneuse et vos objectifs, avec l’accompagnement de notre équipe médicale pour sélectionner l’approche la plus adaptée.</p>',
						),
						'stage_4'     => 'Étape 4',
						'old_items'   => array(
							array( 'eyebrow' => 'Étape 1', 'title' => 'Microneedling et sérum nutritif', 'body' => '<p>Des microcanaux sont créés à l’aide d’un dermaroller, puis un sérum hautement concentré en vitamines, acides aminés, facteurs de croissance et minéraux est appliqué afin de stimuler la circulation sanguine. La zone donneuse est préparée pour un prélèvement fluide et bien nourri, tandis que la zone receveuse bénéficie d’un complexe vitaminé concentré, beaucoup mieux absorbé par le cuir chevelu grâce aux microcanaux.</p>' ),
							array( 'eyebrow' => 'Étape 2', 'title' => 'Sérum en vapeur froide sur les greffons', 'body' => '<p>Le même sérum nutritif est mélangé à une solution saline dans le plateau de prélèvement, puis appliqué sur les greffons à l’aide d’une méthode par vapeur froide. La période passée hors du corps est la plus critique pour les follicules : les protéger à ce moment-là prévient leur détérioration, maximise leur survie et augmente leur potentiel de repousse après la transplantation.</p>' ),
							array( 'eyebrow' => 'Étape 3', 'title' => 'PRP après l’intervention', 'body' => '<p>Le PRP, ou plasma riche en plaquettes, constitue la dernière étape du traitement. Cette thérapie régénérative est réalisée immédiatement après votre greffe afin de soutenir la cicatrisation et d’améliorer le résultat global. Une petite quantité de votre sang est prélevée, puis traitée dans une centrifugeuse pour isoler le plasma riche en plaquettes, qui contient une forte concentration de facteurs de croissance naturels. Le PRP est ensuite injecté avec précision dans les zones traitées du cuir chevelu. Il contribue à accélérer la réparation des tissus, à réduire l’inflammation, à améliorer la circulation sanguine et à créer un environnement favorable aux nouveaux greffons. En stimulant la cicatrisation et en nourrissant les follicules, il favorise leur fixation, une récupération plus rapide et la pousse de cheveux plus sains et plus épais.</p>' ),
						),
					)
				),
				'it' => estecapelli_safe_patch_vita_four_step_operations(
					array(
						'lead_before' => 'Un protocollo in tre fasi che si integra perfettamente nel trapianto Sapphire FUE o DHI. Scorra le fasi qui sotto.',
						'lead_after'  => 'Un protocollo in quattro fasi che si integra perfettamente nel trapianto Sapphire FUE o DHI. Scorra le fasi qui sotto.',
						'new_first'   => array(
							'title' => 'Scegliere tra FUE e DHI',
							'body'  => '<p>Sia la Sapphire FUE sia la DHI possono essere integrate facilmente con il protocollo di trattamento VITA. Può scegliere tra i due metodi in base alla struttura dei capelli, all’area donatrice e agli obiettivi, con il supporto del nostro team medico nella scelta dell’approccio più adatto.</p>',
						),
						'stage_4'     => 'Fase 4',
						'old_items'   => array(
							array( 'eyebrow' => 'Fase 1', 'title' => 'Microneedling e siero nutriente', 'body' => "<p>Con un dermaroller vengono creati microcanali e viene applicato un siero ad alta concentrazione di vitamine, aminoacidi, fattori di crescita e minerali per stimolare la circolazione sanguigna. L'area donatrice viene preparata in modo ottimale per un prelievo uniforme e ben nutrito, mentre l'area ricevente assorbe il complesso vitaminico concentrato con maggiore efficacia grazie ai microcanali.</p>" ),
							array( 'eyebrow' => 'Fase 2', 'title' => 'Siero a vapore freddo sugli innesti', 'body' => "<p>Lo stesso siero nutriente viene miscelato con soluzione salina nel contenitore di raccolta e applicato agli innesti mediante un sistema a vapore freddo. Il periodo in cui i follicoli si trovano fuori dal corpo è il momento più delicato: proteggerli in questa fase ne previene il deterioramento, massimizza la sopravvivenza e aumenta il potenziale di ricrescita dopo l'impianto.</p>" ),
							array( 'eyebrow' => 'Fase 3', 'title' => "PRP dopo l'intervento", 'body' => "<p>Il PRP, plasma ricco di piastrine, viene applicato come fase finale del trattamento, subito dopo il trapianto, per favorire la guarigione e migliorare il risultato complessivo. Un piccolo campione di sangue viene prelevato e lavorato in centrifuga per separare il plasma ad alta concentrazione di fattori di crescita naturali. Il PRP viene quindi iniettato con precisione nelle aree trattate del cuoio capelluto, dove aiuta ad accelerare la riparazione dei tessuti, ridurre l'infiammazione, migliorare la circolazione e creare un ambiente ottimale per i nuovi innesti. Stimolando la guarigione e nutrendo i follicoli, contribuisce a una maggiore ritenzione degli innesti, a un recupero più rapido e a una crescita più sana e folta.</p>" ),
						),
					)
				),
				'es' => estecapelli_safe_patch_vita_four_step_operations(
					array(
						'lead_before' => 'Un protocolo en tres etapas que se integra perfectamente en un trasplante Sapphire FUE o DHI. Consulte cada etapa a continuación.',
						'lead_after'  => 'Un protocolo en cuatro etapas que se integra perfectamente en un trasplante Sapphire FUE o DHI. Consulte cada etapa a continuación.',
						'new_first'   => array(
							'title' => 'Elegir entre FUE y DHI',
							'body'  => '<p>Tanto Sapphire FUE como DHI pueden combinarse fácilmente con el protocolo de tratamiento VITA. Puede elegir entre ambos métodos según la estructura de su cabello, la zona donante y sus objetivos, con la orientación de nuestro equipo médico para seleccionar el enfoque más adecuado.</p>',
						),
						'stage_4'     => 'Etapa 4',
						'old_items'   => array(
							array( 'eyebrow' => 'Etapa 1', 'title' => 'Microneedling y sérum nutritivo', 'body' => '<p>Se crean microcanales con un dermaroller y se aplica un sérum con una alta concentración de vitaminas, aminoácidos, factores de crecimiento y minerales para estimular la circulación sanguínea. La zona donante queda preparada de forma óptima para una extracción uniforme y bien nutrida, mientras que los microcanales permiten que la zona receptora absorba con mayor eficacia el complejo vitamínico concentrado.</p>' ),
							array( 'eyebrow' => 'Etapa 2', 'title' => 'Sérum de vapor frío sobre los injertos', 'body' => '<p>El mismo sérum nutritivo se mezcla con solución salina en el recipiente de recogida y se aplica a los injertos mediante un sistema de vapor frío. El periodo en el que los folículos permanecen fuera del cuerpo es el momento más delicado: protegerlos durante esta fase evita su deterioro, maximiza su supervivencia y aumenta el potencial de crecimiento después de la implantación.</p>' ),
							array( 'eyebrow' => 'Etapa 3', 'title' => 'PRP después del procedimiento', 'body' => '<p>El PRP, plasma rico en plaquetas, se aplica como etapa final inmediatamente después del trasplante para favorecer la recuperación y mejorar el resultado global. Se obtiene una pequeña muestra de sangre y se procesa en una centrífuga para separar el plasma con una alta concentración de factores de crecimiento naturales. A continuación, el PRP se inyecta con precisión en las zonas tratadas del cuero cabelludo, donde ayuda a acelerar la reparación de los tejidos, reducir la inflamación, mejorar la circulación y crear un entorno óptimo para los nuevos injertos. Al estimular la recuperación y nutrir los folículos, contribuye a una mayor retención de los injertos, una recuperación más rápida y un crecimiento más sano y denso.</p>' ),
						),
					)
				),
				'pl' => estecapelli_safe_patch_vita_four_step_operations(
					array(
						'lead_before' => 'Trzyetapowy protokół, który płynnie integruje się z przeszczepem Sapphire FUE lub DHI. Zapoznaj się z kolejnymi etapami poniżej.',
						'lead_after'  => 'Czteroetapowy protokół, który płynnie integruje się z przeszczepem Sapphire FUE lub DHI. Zapoznaj się z kolejnymi etapami poniżej.',
						'new_first'   => array(
							'title' => 'Wybór między FUE a DHI',
							'body'  => '<p>Zarówno Sapphire FUE, jak i DHI można łatwo połączyć z protokołem leczenia VITA. Możesz wybrać jedną z tych metod zależnie od struktury włosów, obszaru dawczego i swoich celów, korzystając ze wskazówek naszego zespołu medycznego przy wyborze najlepszego rozwiązania.</p>',
						),
						'stage_4'     => 'Etap 4',
						'old_items'   => array(
							array( 'eyebrow' => 'Etap 1', 'title' => 'Mikronakłuwanie i aplikacja serum odżywczego', 'body' => '<p>Za pomocą dermarollera tworzy się mikrokanały, a następnie nakłada silnie skoncentrowane serum z witaminami, aminokwasami, czynnikami wzrostu i minerałami, aby pobudzić krążenie krwi. Obszar dawczy zostaje przygotowany do sprawnego pobrania graftów, a obszar biorczy otrzymuje skoncentrowany kompleks witamin, którego wchłanianie ułatwiają mikrokanały.</p>' ),
							array( 'eyebrow' => 'Etap 2', 'title' => 'Serum i zimna para dla graftów', 'body' => '<p>To samo odżywcze serum łączy się z solą fizjologiczną w pojemniku do przechowywania graftów, które następnie poddaje się działaniu zimnej pary. Jest to kluczowy etap, ponieważ mieszki znajdują się poza organizmem. Ochrona w tym czasie pomaga zachować ich kondycję, zwiększa przeżywalność i wspiera potencjał wzrostu po przeszczepie.</p>' ),
							array( 'eyebrow' => 'Etap 3', 'title' => 'PRP po zabiegu', 'body' => '<p>PRP (osocze bogatopłytkowe) stosuje się jako ostatni etap zabiegu. Ta terapia regeneracyjna, wykonywana bezpośrednio po przeszczepie włosów, wspiera gojenie i ogólny efekt. Niewielką próbkę krwi przetwarza się w wirówce, aby oddzielić osocze bogate w płytki krwi i naturalne czynniki wzrostu. Następnie PRP ostrożnie wstrzykuje się w leczone obszary skóry głowy, gdzie wspomaga naprawę tkanek, ogranicza stan zapalny, poprawia krążenie i tworzy korzystne środowisko dla nowo przeszczepionych graftów. Wspierając gojenie i odżywiając mieszki, PRP pomaga graftom lepiej się przyjąć oraz sprzyja szybszej regeneracji i zdrowszemu wzrostowi włosów.</p>' ),
						),
					)
				),
				'pt' => estecapelli_safe_patch_vita_four_step_operations(
					array(
						'lead_before' => 'Um protocolo em três etapas que se integra perfeitamente num transplante Sapphire FUE ou DHI. Consulte cada etapa em seguida.',
						'lead_after'  => 'Um protocolo em quatro etapas que se integra perfeitamente num transplante Sapphire FUE ou DHI. Consulte cada etapa em seguida.',
						'new_first'   => array(
							'title' => 'Escolher entre FUE e DHI',
							'body'  => '<p>Tanto a Sapphire FUE como a DHI podem ser facilmente combinadas com o protocolo de tratamento VITA. Pode escolher entre os dois métodos de acordo com a estrutura do cabelo, a zona dadora e os seus objetivos, com a orientação da nossa equipa médica para selecionar a abordagem mais adequada.</p>',
						),
						'stage_4'     => 'Etapa 4',
						'old_items'   => array(
							array( 'eyebrow' => 'Etapa 1', 'title' => 'Microneedling e sérum nutritivo', 'body' => '<p>São criados microcanais com um dermaroller e é aplicado um sérum com uma elevada concentração de vitaminas, aminoácidos, fatores de crescimento e minerais para estimular a circulação sanguínea. A zona dadora fica preparada de forma ideal para uma extração uniforme e bem nutrida, enquanto os microcanais permitem que a zona recetora absorva com maior eficácia o complexo vitamínico concentrado.</p>' ),
							array( 'eyebrow' => 'Etapa 2', 'title' => 'Sérum de vapor frio sobre os enxertos', 'body' => '<p>O mesmo sérum nutritivo é misturado com solução salina no recipiente de recolha e aplicado aos enxertos através de um sistema de vapor frio. O período em que os folículos permanecem fora do corpo é o momento mais delicado: protegê-los durante esta fase evita a sua deterioração, maximiza a sua sobrevivência e aumenta o potencial de crescimento após a implantação.</p>' ),
							array( 'eyebrow' => 'Etapa 3', 'title' => 'PRP depois do procedimento', 'body' => '<p>O PRP, plasma rico em plaquetas, é aplicado como etapa final imediatamente após o transplante para favorecer a recuperação e melhorar o resultado global. É recolhida uma pequena amostra de sangue e processada numa centrifugadora para separar o plasma com uma elevada concentração de fatores de crescimento naturais. Em seguida, o PRP é injetado com precisão nas zonas tratadas do couro cabeludo, onde ajuda a acelerar a reparação dos tecidos, a reduzir a inflamação, a melhorar a circulação e a criar um ambiente ideal para os novos enxertos. Ao estimular a recuperação e nutrir os folículos, contribui para uma maior retenção dos enxertos, uma recuperação mais rápida e um crescimento mais saudável e denso.</p>' ),
						),
					)
				),
				'tr' => estecapelli_safe_patch_vita_four_step_operations(
					array(
						'lead_before' => 'Sapphire FUE veya DHI naklinize sorunsuz bir şekilde entegre olan üç aşamalı bir protokol. Aşağıdaki aşamaları kaydırarak inceleyin.',
						'lead_after'  => 'Sapphire FUE veya DHI naklinize sorunsuz bir şekilde entegre olan dört aşamalı bir protokol. Aşağıdaki aşamaları kaydırarak inceleyin.',
						'new_first'   => array(
							'title' => 'FUE ve DHI Arasında Seçim',
							'body'  => '<p>Hem Sapphire FUE hem de DHI, VITA Tedavi Protokolü ile kolayca birleştirilebilir. Saç yapınıza, donör alanınıza ve hedeflerinize göre bu iki yöntem arasında seçim yapabilir; sağlık ekibimizin yönlendirmesiyle size en uygun yaklaşımı belirleyebilirsiniz.</p>',
						),
						'stage_4'     => 'Aşama 4',
						'old_items'   => array(
							array( 'eyebrow' => 'Aşama 1', 'title' => 'Mikro İğneleme ve Besin Serumu', 'body' => '<p>Dermaroller masaj aletiyle mikrokanallar oluşturulur ve kan dolaşımını hızlandırmak için vitaminler, amino asitler, büyüme faktörleri ve minerallerden oluşan yüksek konsantrasyonlu bir serum uygulanır. Donör bölgesi sorunsuz, iyi beslenmiş bir hasat için ideal şekilde hazırlanırken, alıcı bölge mikro kanallar sayesinde saç derisinin çok daha etkili bir şekilde emdiği konsantre bir vitamin kompleksi ile beslenir.</p>' ),
							array( 'eyebrow' => 'Aşama 2', 'title' => 'Greftlere Soğuk Buhar Serumu Uygulaması', 'body' => '<p>Aynı besleyici serum, greft toplama kabında salinle karıştırılır ve soğuk buhar yöntemiyle alınan greftlere uygulanır. Saç köklerinin vücut dışında kaldığı bu kritik sürede korunması, hücresel bozulmayı azaltır, greft sağkalımını artırır ve ekim sonrası büyüme potansiyelini destekler.</p>' ),
							array( 'eyebrow' => 'Aşama 3', 'title' => 'İşlem Sonrası PRP', 'body' => '<p>PRP (Platelet Açısından Zengin Plazma) tedavinin son aşaması olarak uygulanır; iyileşmeyi desteklemek ve genel sonucu iyileştirmek için saç ekiminizden hemen sonra gerçekleştirilen yenileyici bir tedavidir. Kanınızın küçük bir örneği alınır ve yüksek konsantrasyonda doğal büyüme faktörleri içeren trombosit açısından zengin plazmayı ayırmak için bir santrifüjde işlenir. PRP daha sonra kafa derisinin tedavi edilen bölgelerine dikkatlice enjekte edilir; burada doku onarımını hızlandırmaya, iltihaplanmayı azaltmaya, kan dolaşımını iyileştirmeye ve yeni nakledilen greftler için en uygun ortamı yaratmaya yardımcı olur. PRP, iyileşme sürecini uyararak ve folikülleri besleyerek daha güçlü greft tutulmasına, daha hızlı iyileşmeye ve daha sağlıklı, daha kalın saç büyümesine katkıda bulunur.</p>' ),
						),
					)
				),
			),
		),
		'female-painless-anaesthesia-20260723-v1' => array(
			'title'       => 'Female Hair Transplant — Painless Anaesthesia Step 2',
			'description' => 'Replace the complete Step 2 title and body with painless-anaesthesia copy in all seven languages, without changing the step image or structure.',
			'post_type'   => 'treatment',
			'source_slug' => 'female-hair-transplant',
			'schema'      => 'field_groups_v2',
			'languages'   => array(
				'en' => estecapelli_safe_patch_female_painless_operation(
					'Natural Hairline Design',
					'<p>An aesthetic, feminine hairline is drawn in harmony with your facial proportions and natural growth direction. For women the goal is usually to restore density and frame the face rather than rebuild a receded line, so the design protects your existing style and keeps the result completely undetectable.</p>',
					'Painless Anaesthesia',
					'<p>Painless anaesthesia is applied to the donor and recipient areas for a comfortable, pain-free procedure. You stay awake but relaxed throughout, and can listen to music or watch something during the session.</p>'
				),
				'fr' => estecapelli_safe_patch_female_painless_operation(
					'Conception d’une ligne frontale naturelle',
					'<p>Une ligne frontale féminine et esthétique est dessinée en harmonie avec les proportions de votre visage et le sens naturel de la pousse. Chez la femme, l’objectif consiste généralement à restaurer la densité et à encadrer le visage plutôt qu’à reconstruire une ligne reculée. Le dessin préserve ainsi votre style et rend le résultat imperceptible.</p>',
					'Anesthésie sans douleur',
					'<p>Une anesthésie sans douleur est appliquée aux zones donneuse et receveuse pour assurer une intervention confortable. Vous restez éveillé, mais détendu, et pouvez écouter de la musique ou regarder un programme pendant la séance.</p>'
				),
				'it' => estecapelli_safe_patch_female_painless_operation(
					'Disegno naturale della linea frontale',
					"<p>Viene disegnata una linea frontale femminile ed elegante, in armonia con le proporzioni del viso e con la direzione naturale della crescita. Nelle donne l'obiettivo è spesso ripristinare densità e incorniciare il volto, più che ricostruire una linea arretrata: il disegno protegge quindi lo stile esistente e rende il risultato impercettibile.</p>",
					'Anestesia indolore',
					"<p>L'anestesia indolore viene applicata alle aree donatrice e ricevente per garantire un intervento confortevole. Rimane sveglio ma rilassato per tutta la durata e può ascoltare musica o guardare qualcosa durante la seduta.</p>"
				),
				'es' => estecapelli_safe_patch_female_painless_operation(
					'Diseño natural de la línea frontal',
					'<p>Diseñamos una línea frontal femenina y elegante que armoniza con las proporciones del rostro y la dirección natural del crecimiento. En las mujeres, el objetivo suele ser recuperar densidad y enmarcar el rostro más que reconstruir una línea retraída; por ello, el diseño protege el estilo existente y hace que el resultado sea imperceptible.</p>',
					'Anestesia sin dolor',
					'<p>Aplicamos anestesia sin dolor en las zonas donante y receptora para que el procedimiento sea cómodo. Permanecerá despierto pero relajado durante toda la intervención y podrá escuchar música o ver algún contenido durante la sesión.</p>'
				),
				'pl' => estecapelli_safe_patch_female_painless_operation(
					'Naturalny projekt linii włosów',
					'<p>Estetyczna, kobieca linia włosów jest narysowana w harmonii z proporcjami twarzy i naturalnym kierunkiem wzrostu. W przypadku kobiet celem jest zwykle przywrócenie gęstości i oprawienie twarzy, a nie odbudowa cofniętej linii, dzięki czemu projekt chroni istniejący styl i sprawia, że wynik jest całkowicie naturalny.</p>',
					'Bezbolesne znieczulenie',
					'<p>W obszarze dawczym i biorczym stosujemy bezbolesne znieczulenie, dzięki czemu zabieg przebiega komfortowo. Przez całą sesję pozostajesz przytomny i zrelaksowany; możesz słuchać muzyki lub oglądać film.</p>'
				),
				'pt' => estecapelli_safe_patch_female_painless_operation(
					'Desenho natural da linha frontal',
					'<p>Desenhamos uma linha frontal feminina e elegante que harmoniza com as proporções do rosto e com a direção natural do crescimento. Nas mulheres, o objetivo costuma ser recuperar densidade e emoldurar o rosto mais do que reconstruir uma linha recuada; por isso, o desenho protege o estilo existente e torna o resultado impercetível.</p>',
					'Anestesia indolor',
					'<p>Aplicamos anestesia indolor nas zonas dadora e recetora para que o procedimento seja confortável. Permanecerá acordado mas relaxado durante toda a intervenção e poderá ouvir música ou ver algum conteúdo durante a sessão.</p>'
				),
				'tr' => estecapelli_safe_patch_female_painless_operation(
					'Doğal Saç Çizgisi Tasarımı',
					'<p>Yüz oranlarınız ve doğal büyüme yönünüzle uyumlu, estetik, feminen bir saç çizgisi çizilir. Kadınlar için amaç, genellikle geri çekilmiş bir çizgiyi yeniden oluşturmak yerine yoğunluğu geri kazandırmak ve yüzü çerçevelemektir, böylece tasarım mevcut tarzınızı korur ve sonucu tamamen farkedilemez tutar.</p>',
					'Ağrısız Anestezi',
					'<p>Konforlu bir deneyim için donör ve alıcı bölgelere ağrısız anestezi uygulanır. İşlem boyunca uyanık ancak rahat olursunuz; seans sırasında müzik dinleyebilir veya bir şeyler izleyebilirsiniz.</p>'
				),
			),
		),
		'exosome-localized-infographic-20260723-v1' => array(
			'title'       => 'Exosome FUE — Localized comparison infographic',
			'description' => 'Give the Exosome-versus-Traditional-FUE comparison its matching EN, FR, IT, ES, PL, PT or TR graphic. The localized override leaves uploaded images on every other section untouched.',
			'post_type'   => 'treatment',
			'source_slug' => 'exosome-fue-hair-transplant',
			'schema'      => 'field_groups_v2',
			'languages'   => array(
				'en' => estecapelli_safe_patch_exosome_localized_image_operation( 'assets/images/treatments/exosome-vs-en.webp' ),
				'fr' => estecapelli_safe_patch_exosome_localized_image_operation( 'assets/images/treatments/exosome-vs-fr.webp' ),
				'it' => estecapelli_safe_patch_exosome_localized_image_operation( 'assets/images/treatments/exosome-vs-it.webp' ),
				'es' => estecapelli_safe_patch_exosome_localized_image_operation( 'assets/images/treatments/exosome-vs-es.webp' ),
				'pl' => estecapelli_safe_patch_exosome_localized_image_operation( 'assets/images/treatments/exosome-vs-pl.webp' ),
				'pt' => estecapelli_safe_patch_exosome_localized_image_operation( 'assets/images/treatments/exosome-vs-pt.webp' ),
				'tr' => estecapelli_safe_patch_exosome_localized_image_operation( 'assets/images/treatments/exosome-vs-tr.webp' ),
			),
		),
		'vita-localized-science-infographic-20260723-v1' => array(
			'title'       => 'VITA Treatment — Localized science infographic',
			'description' => 'Give the VITA science section its matching EN, FR, IT, ES, PL, PT or TR graphic. The localized override leaves uploaded images on every other section untouched.',
			'post_type'   => 'treatment',
			'source_slug' => 'vita-treatment',
			'schema'      => 'field_groups_v2',
			'languages'   => array(
				'en' => estecapelli_safe_patch_vita_localized_science_image_operation( 'assets/images/treatments/vita-science-en.webp' ),
				'fr' => estecapelli_safe_patch_vita_localized_science_image_operation( 'assets/images/treatments/vita-science-fr.webp' ),
				'it' => estecapelli_safe_patch_vita_localized_science_image_operation( 'assets/images/treatments/vita-science-it.webp' ),
				'es' => estecapelli_safe_patch_vita_localized_science_image_operation( 'assets/images/treatments/vita-science-es.webp' ),
				'pl' => estecapelli_safe_patch_vita_localized_science_image_operation( 'assets/images/treatments/vita-science-pl.webp' ),
				'pt' => estecapelli_safe_patch_vita_localized_science_image_operation( 'assets/images/treatments/vita-science-pt.webp' ),
				'tr' => estecapelli_safe_patch_vita_localized_science_image_operation( 'assets/images/treatments/vita-science-tr.webp' ),
			),
		),
		'vita-localized-videos-20260723-v1' => array(
			'title'       => 'VITA Treatment — Localized videos',
			'description' => 'Replace the shared English VITA Protocol video in the first intro with the matching FR, IT, ES, PL, PT or TR version. The homepage is localized at render time because its ACF option is shared.',
			'post_type'   => 'treatment',
			'source_slug' => 'vita-treatment',
			'schema'      => 'field_groups_v2',
			'languages'   => array(
				'fr' => estecapelli_safe_patch_vita_localized_video_operation( 'https://youtube.com/shorts/E3B38CyzC-M' ),
				'it' => estecapelli_safe_patch_vita_localized_video_operation( 'https://youtube.com/shorts/UI-imkHOPpM' ),
				'es' => estecapelli_safe_patch_vita_localized_video_operation( 'https://youtube.com/shorts/cVMVZMykGcM' ),
				'pl' => estecapelli_safe_patch_vita_localized_video_operation( 'https://youtube.com/shorts/EsMsyrD-0y4' ),
				'pt' => estecapelli_safe_patch_vita_localized_video_operation( 'https://youtube.com/shorts/wpbVhfLPTvk' ),
				'tr' => estecapelli_safe_patch_vita_localized_video_operation( 'https://youtube.com/shorts/hLuXikySN34' ),
			),
		),
		'exosome-localized-videos-20260723-v1' => array(
			'title'       => 'Exosome FUE — Localized videos',
			'description' => 'Replace the shared English Exosome video in the method intro with the matching FR, IT, ES, PL, PT or TR version. The homepage is localized at render time because its ACF option is shared.',
			'post_type'   => 'treatment',
			'source_slug' => 'exosome-fue-hair-transplant',
			'schema'      => 'field_groups_v2',
			'languages'   => array(
				'fr' => estecapelli_safe_patch_exosome_localized_video_operation( 'https://youtu.be/Yf1z9k5ApaI' ),
				'it' => estecapelli_safe_patch_exosome_localized_video_operation( 'https://youtu.be/hR3kr7L5oNY' ),
				'es' => estecapelli_safe_patch_exosome_localized_video_operation( 'https://youtu.be/JG9a8r66u3o' ),
				'pl' => estecapelli_safe_patch_exosome_localized_video_operation( 'https://youtu.be/IQz4wBaI8Jw' ),
				'pt' => estecapelli_safe_patch_exosome_localized_video_operation( 'https://youtu.be/IiQSAcwO5Ww' ),
				'tr' => estecapelli_safe_patch_exosome_localized_video_operation( 'https://youtu.be/rkDuNzptnL8' ),
			),
		),
	);
}

/** Stable non-autoloaded option key for patch state. */
function estecapelli_safe_patch_option_key( $kind, $patch_id ) {
	return 'estecapelli_safe_patch_' . $kind . '_' . substr( hash( 'sha256', $patch_id ), 0, 20 );
}

/** Canonicalise serialization-only differences in a rich-text HTML field. */
function estecapelli_safe_patch_normalize_html( $value, $force_html = false ) {
	$value = (string) $value;
	if ( ! $force_html && false === strpos( $value, '<' ) ) {
		return null;
	}

	// TinyMCE/WordPress may store the same visible punctuation as a literal
	// UTF-8 character or an HTML entity. French rich text also commonly carries
	// U+00A0/U+202F before punctuation while the JSON seed uses a normal space.
	// Decode those equivalent serializations, but retain all tags and text.
	$value = str_replace( array( "\r\n", "\r" ), "\n", $value );
	$value = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$value = str_replace( array( "\xC2\xA0", "\xE2\x80\xAF" ), ' ', $value );

	// Canonicalise through the same formatter used by WordPress/ACF. This makes
	// a raw blank-line paragraph and an explicitly stored <p> equivalent.
	if ( function_exists( 'wpautop' ) ) {
		$value = wpautop( $value );
	}

	// Compare the exact ordered tag structure separately from visible text.
	// Editor indentation/newlines cannot affect the structure, while a real tag
	// change (including an inserted <br>) remains a conflict.
	$tags = array();
	preg_match_all( '/<[^>]+>/s', $value, $tags );
	$structure = implode(
		'',
		array_map(
			static function ( $tag ) {
				return preg_replace( '/\s+/u', ' ', trim( $tag ) );
			},
			$tags[0] ?? array()
		)
	);

	// Block boundaries and explicit line breaks are visible whitespace. Add a
	// separator before stripping tags, then collapse HTML whitespace exactly as
	// a browser does. Inline spacing is retained, so "un mot" cannot match
	// "unmot" and genuine copy edits still fail closed.
	$text = preg_replace(
		'#</?(?:address|article|aside|blockquote|div|figcaption|figure|footer|h[1-6]|header|hr|li|main|nav|ol|p|section|table|tbody|td|tfoot|th|thead|tr|ul)\b[^>]*>#i',
		"\n",
		$value
	);
	$text = preg_replace( '#<br\b[^>]*>#i', "\n", $text );
	$text = function_exists( 'wp_strip_all_tags' ) ? wp_strip_all_tags( $text ) : strip_tags( $text );
	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$text = str_replace( array( "\xC2\xA0", "\xE2\x80\xAF" ), ' ', $text );
	$text = trim( preg_replace( '/[\p{Z}\s]+/u', ' ', $text ) );

	return $structure . "\0" . $text;
}

/** Compare scalar meta as stored strings and structured meta by exact shape. */
function estecapelli_safe_patch_values_equal( $left, $right ) {
	if ( is_array( $left ) || is_array( $right ) || is_object( $left ) || is_object( $right ) ) {
		return $left === $right;
	}

	return (string) $left === (string) $right;
}

/** Compare a current value with one or more allowed pre-patch values. */
function estecapelli_safe_patch_matches( $current, $allowed ) {
	$allowed = is_array( $allowed ) ? $allowed : array( $allowed );
	$allowed = array_map( 'strval', $allowed );
	if ( in_array( (string) $current, $allowed, true ) ) {
		return true;
	}

	// Only comparisons where at least one side contains HTML receive rich-text
	// normalization. Plain text retains exact matching, including whitespace.
	foreach ( $allowed as $candidate ) {
		if ( false === strpos( (string) $current, '<' ) && false === strpos( $candidate, '<' ) ) {
			continue;
		}
		$current_norm   = estecapelli_safe_patch_normalize_html( $current, true );
		$candidate_norm = estecapelli_safe_patch_normalize_html( $candidate, true );
		if ( $current_norm === $candidate_norm ) {
			return true;
		}
	}

	// YouTube stores the same video in several equivalent URL forms. Treat a
	// watch, Shorts, youtu.be or bare-ID value as equal when its video ID is the
	// same, while preserving strict comparison for every non-video field.
	if ( function_exists( 'estecapelli_youtube_id' ) ) {
		$current_id = estecapelli_youtube_id( $current );
		if ( $current_id ) {
			foreach ( $allowed as $candidate ) {
				if ( $current_id === estecapelli_youtube_id( $candidate ) ) {
					return true;
				}
			}
		}
	}

	return false;
}

/**
 * Normalise the original quick-stat schema and the general field-group schema.
 *
 * @return array<int,array<string,mixed>>
 */
function estecapelli_safe_patch_operations( $patch, $language ) {
	$language_changes = $patch['languages'][ $language ] ?? array();
	if ( 'field_groups_v2' === ( $patch['schema'] ?? '' ) ) {
		return is_array( $language_changes ) ? array_values( $language_changes ) : array();
	}

	$operations = array();
	foreach ( $language_changes as $row_index => $change ) {
		$fields = array();
		foreach ( array( 'value', 'label' ) as $field_name ) {
			$fields[ $field_name ] = array(
				'before' => $change['before'][ $field_name ],
				'after'  => $change['after'][ $field_name ],
			);
		}
		$operations[] = array(
			'layout'    => $patch['layout'],
			'repeater'  => 'stats',
			'row_index' => (int) $row_index,
			'fields'    => $fields,
		);
	}
	return $operations;
}

/** Resolve and validate one WPML language target. */
function estecapelli_safe_patch_target_post_id( $source_id, $post_type, $language ) {
	if ( 'en' === $language ) {
		$target_id = (int) $source_id;
	} else {
		$wpml_language = function_exists( 'estecapelli_wpml_language_code' )
			? estecapelli_wpml_language_code( $language )
			: $language;
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, $post_type, false, $wpml_language );
		if ( ! $target_id || $target_id === (int) $source_id ) {
			return new WP_Error( 'safe_patch_missing_translation', sprintf( 'No distinct %s translation is linked to English post %d.', $language, $source_id ) );
		}
	}

	$post = get_post( $target_id );
	if ( ! $post || $post_type !== $post->post_type || 'trash' === $post->post_status ) {
		return new WP_Error( 'safe_patch_invalid_target', sprintf( 'Invalid %s target post for language %s.', $post_type, $language ) );
	}

	if ( defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'WPML_VERSION' ) ) {
		$details = apply_filters(
			'wpml_element_language_details',
			null,
			array(
				'element_id'   => $target_id,
				'element_type' => $post_type,
			)
		);
		$actual  = is_object( $details ) ? (string) ( $details->language_code ?? '' ) : (string) ( $details['language_code'] ?? '' );
		if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
			$actual = estecapelli_indexed_language_code( $actual );
		}
		if ( $language !== $actual ) {
			return new WP_Error( 'safe_patch_language_mismatch', sprintf( 'Post %d is %s, expected %s.', $target_id, $actual ?: 'unknown', $language ) );
		}
	}

	return $target_id;
}

/**
 * Build a read-only preview and refuse positional writes unless old values fit.
 *
 * @return array<string,mixed>|WP_Error
 */
function estecapelli_safe_patch_preview( $patch_id ) {
	$patches = estecapelli_safe_content_patches();
	if ( empty( $patches[ $patch_id ] ) ) {
		return new WP_Error( 'safe_patch_unknown', 'Unknown content patch.' );
	}
	$patch = $patches[ $patch_id ];

	if ( ! function_exists( 'estecapelli_source_post_id' ) ) {
		return new WP_Error( 'safe_patch_source_resolver_missing', 'The safe English source resolver is unavailable.' );
	}
	$source_id = (int) estecapelli_source_post_id( $patch['source_slug'], $patch['post_type'] );
	if ( ! $source_id ) {
		return new WP_Error( 'safe_patch_source_missing', sprintf( 'English source not found: %s.', $patch['source_slug'] ) );
	}

	$rows      = array();
	$conflicts = 0;
	$pending   = 0;
	$seen_keys = array();
	foreach ( array_keys( $patch['languages'] ) as $language ) {
		$target_id = estecapelli_safe_patch_target_post_id( $source_id, $patch['post_type'], $language );
		if ( is_wp_error( $target_id ) ) {
			return $target_id;
		}

		$layouts = get_post_meta( $target_id, 'page_sections', true );
		if ( ! is_array( $layouts ) ) {
			return new WP_Error( 'safe_patch_layout_index_missing', sprintf( '%s post %d has no readable page_sections index.', $language, $target_id ) );
		}
		$operations = estecapelli_safe_patch_operations( $patch, $language );
		if ( ! $operations ) {
			return new WP_Error( 'safe_patch_operations_missing', sprintf( 'No field operations are registered for language %s.', $language ) );
		}
		foreach ( $operations as $operation ) {
			$target    = (string) ( $operation['target'] ?? 'row_fields' );
			if ( 'configuration_error' === $target ) {
				return new WP_Error( 'safe_patch_configuration_error', (string) ( $operation['message'] ?? 'Invalid patch configuration.' ) );
			}
			if ( 'root_meta' === $target ) {
				$meta_key       = (string) ( $operation['meta_key'] ?? '' );
				$allowed        = $operation['before_values'] ?? array();
				$after_from_key = (string) ( $operation['after_from_meta'] ?? '' );
				$has_after      = array_key_exists( 'after_value', $operation ) || '' !== $after_from_key;
				if ( ! preg_match( '/^[a-z0-9_]+$/', $meta_key ) || ! is_array( $allowed ) || ! $allowed || ! $has_after ) {
					return new WP_Error( 'safe_patch_root_meta_invalid', sprintf( 'Invalid root-meta operation registered for language %s.', $language ) );
				}
				if ( $after_from_key && ! preg_match( '/^[a-z0-9_]+$/', $after_from_key ) ) {
					return new WP_Error( 'safe_patch_root_meta_source_invalid', sprintf( 'Invalid root-meta source registered for language %s.', $language ) );
				}
				$after = $after_from_key
					? get_post_meta( $target_id, $after_from_key, true )
					: $operation['after_value'];
				if ( $after_from_key && ( '' === $after || '0' === (string) $after ) ) {
					return new WP_Error( 'safe_patch_root_meta_source_empty', sprintf( '%s post %d has no source value in %s.', $language, $target_id, $after_from_key ) );
				}

				$seen_key = $target_id . ':' . $meta_key;
				if ( isset( $seen_keys[ $seen_key ] ) ) {
					return new WP_Error( 'safe_patch_duplicate_field', sprintf( 'Duplicate patch target: %s on post %d.', $meta_key, $target_id ) );
				}
				$seen_keys[ $seen_key ] = true;
				$current                = get_post_meta( $target_id, $meta_key, true );
				$is_after               = estecapelli_safe_patch_values_equal( $current, $after );
				$is_valid               = $is_after;
				foreach ( $allowed as $candidate ) {
					if ( estecapelli_safe_patch_values_equal( $current, $candidate ) ) {
						$is_valid = true;
						break;
					}
				}
				$status = $is_after ? 'already' : ( $is_valid ? 'pending' : 'conflict' );
				if ( 'pending' === $status ) {
					$pending++;
				} elseif ( 'conflict' === $status ) {
					$conflicts++;
				}
				$rows[] = array(
					'language' => $language,
					'post_id'  => $target_id,
					'location' => (string) ( $operation['location'] ?? 'root meta' ),
					'fields'   => array(
						$meta_key => array(
							'meta_key' => $meta_key,
							'current'  => $current,
							'after'    => $after,
						),
					),
					'status'   => $status,
				);
				continue;
			}

			$layout    = (string) ( $operation['layout'] ?? '' );
			$repeater  = (string) ( $operation['repeater'] ?? '' );
			$row_index = isset( $operation['row_index'] ) ? (int) $operation['row_index'] : -1;
			$fields    = $operation['fields'] ?? array();
			$row_target_valid = 'row_fields' === $target && preg_match( '/^[a-z0-9_]+$/', $repeater ) && $row_index >= 0;
			if ( ! preg_match( '/^[a-z0-9_]+$/', $layout ) || ( 'layout_fields' !== $target && ! $row_target_valid ) || ! is_array( $fields ) || ! $fields ) {
				return new WP_Error( 'safe_patch_operation_invalid', sprintf( 'Invalid field operation registered for language %s.', $language ) );
			}

			if ( array_key_exists( 'layout_index', $operation ) ) {
				$layout_index = (int) $operation['layout_index'];
				if ( $layout_index < 0 || ( $layouts[ $layout_index ] ?? '' ) !== $layout ) {
					return new WP_Error( 'safe_patch_layout_index_mismatch', sprintf( '%s post %d does not have %s at registered layout index %d.', $language, $target_id, $layout, $layout_index ) );
				}
			} else {
				$indices = array_keys( $layouts, $layout, true );
				if ( 1 !== count( $indices ) ) {
					return new WP_Error( 'safe_patch_layout_ambiguous', sprintf( '%s post %d has %d matching %s layouts; expected exactly one.', $language, $target_id, count( $indices ), $layout ) );
				}
				$layout_index = (int) reset( $indices );
			}
			if ( 'layout_fields' === $target ) {
				$prefix   = 'page_sections_' . $layout_index . '_';
				$location = $layout . ' fields';
			} else {
				$prefix   = 'page_sections_' . $layout_index . '_' . $repeater . '_' . $row_index . '_';
				$location = sprintf( '%s / %s row %d', $layout, $repeater, $row_index + 1 );
			}
			$row_fields   = array();
			$all_after    = true;
			$all_valid    = true;
			foreach ( $fields as $field_name => $change ) {
				if ( ! preg_match( '/^[a-z0-9_]+$/', $field_name ) || ! is_array( $change ) || ! array_key_exists( 'before', $change ) || ! array_key_exists( 'after', $change ) ) {
					return new WP_Error( 'safe_patch_field_invalid', sprintf( 'Invalid %s field operation registered for language %s.', $field_name, $language ) );
				}
				$meta_key = $prefix . $field_name;
				$seen_key = $target_id . ':' . $meta_key;
				if ( isset( $seen_keys[ $seen_key ] ) ) {
					return new WP_Error( 'safe_patch_duplicate_field', sprintf( 'Duplicate patch target: %s on post %d.', $meta_key, $target_id ) );
				}
				$seen_keys[ $seen_key ] = true;
				$current                = (string) get_post_meta( $target_id, $meta_key, true );
				$after                  = (string) $change['after'];
				$is_after               = $current === $after;
				$is_valid               = $is_after || estecapelli_safe_patch_matches( $current, $change['before'] );
				$all_after              = $all_after && $is_after;
				$all_valid              = $all_valid && $is_valid;
				$row_fields[ $field_name ] = array(
					'meta_key' => $meta_key,
					'current'  => $current,
					'after'    => $after,
				);
			}
			$status = $all_after ? 'already' : ( $all_valid ? 'pending' : 'conflict' );

			if ( 'pending' === $status ) {
				$pending++;
			} elseif ( 'conflict' === $status ) {
				$conflicts++;
			}

			$rows[] = array(
				'language'  => $language,
				'post_id'   => $target_id,
				'location'  => $location,
				'fields'    => $row_fields,
				'status'    => $status,
			);
		}
	}

	return array(
		'patch'     => $patch,
		'rows'      => $rows,
		'pending'   => $pending,
		'conflicts' => $conflicts,
		'applied'   => get_option( estecapelli_safe_patch_option_key( 'applied', $patch_id ), false ),
		'backup'    => get_option( estecapelli_safe_patch_option_key( 'backup', $patch_id ), false ),
	);
}

/** Restore a list of post-meta snapshots. */
function estecapelli_safe_patch_restore_meta( $items ) {
	$post_ids = array();
	foreach ( array_reverse( $items ) as $item ) {
		if ( ! empty( $item['existed'] ) ) {
			update_post_meta( $item['post_id'], $item['meta_key'], $item['before'] );
		} else {
			delete_post_meta( $item['post_id'], $item['meta_key'] );
		}
		$post_ids[ (int) $item['post_id'] ] = true;
	}
	foreach ( array_keys( $post_ids ) as $post_id ) {
		clean_post_cache( $post_id );
	}
}

/** Apply one fully validated patch after recording an exact rollback snapshot. */
function estecapelli_safe_patch_apply( $patch_id ) {
	$preview = estecapelli_safe_patch_preview( $patch_id );
	if ( is_wp_error( $preview ) ) {
		return $preview;
	}
	if ( $preview['applied'] ) {
		return new WP_Error( 'safe_patch_already_applied', 'This patch is already applied.' );
	}
	if ( ! empty( $preview['patch']['superseded_by'] ) ) {
		return new WP_Error( 'safe_patch_superseded', sprintf( 'This patch is superseded by %s and can no longer be applied.', $preview['patch']['superseded_by'] ) );
	}
	if ( $preview['conflicts'] ) {
		return new WP_Error( 'safe_patch_conflict', sprintf( 'Patch blocked: %d rows differ from both the expected old and new copy.', $preview['conflicts'] ) );
	}
	if ( ! $preview['pending'] ) {
		return new WP_Error( 'safe_patch_nothing_pending', 'No pending values were found.' );
	}

	$backup = array();
	foreach ( $preview['rows'] as $row ) {
		if ( 'pending' !== $row['status'] ) {
			continue;
		}
		foreach ( $row['fields'] as $field ) {
			$key    = $field['meta_key'];
			$before = $field['current'];
			$after  = $field['after'];
			if ( $before === $after ) {
				continue;
			}
			$backup[] = array(
				'post_id'  => (int) $row['post_id'],
				'meta_key' => $key,
				'existed'  => metadata_exists( 'post', $row['post_id'], $key ),
				'before'   => $before,
				'after'    => $after,
			);
		}
	}

	$backup_option  = estecapelli_safe_patch_option_key( 'backup', $patch_id );
	$backup_payload = array(
		'patch_id'   => $patch_id,
		'created_at' => current_time( 'mysql', true ),
		'user_id'    => get_current_user_id(),
		'items'      => $backup,
	);
	if ( false === get_option( $backup_option, false ) ) {
		add_option( $backup_option, $backup_payload, '', false );
	} else {
		update_option( $backup_option, $backup_payload, false );
	}
	$stored_backup = get_option( $backup_option, false );
	if ( ! is_array( $stored_backup ) || $patch_id !== ( $stored_backup['patch_id'] ?? '' ) || $backup !== ( $stored_backup['items'] ?? null ) ) {
		return new WP_Error( 'safe_patch_backup_failed', 'Patch blocked because its exact rollback backup could not be verified.' );
	}

	$written  = array();
	$post_ids = array();
	foreach ( $backup as $item ) {
		$current = get_post_meta( $item['post_id'], $item['meta_key'], true );
		if ( ! estecapelli_safe_patch_values_equal( $current, $item['before'] ) ) {
			estecapelli_safe_patch_restore_meta( $written );
			return new WP_Error( 'safe_patch_concurrent_edit', sprintf( 'Patch blocked: %s on post %d changed after preview; completed writes were rolled back.', $item['meta_key'], $item['post_id'] ) );
		}
		update_post_meta( $item['post_id'], $item['meta_key'], $item['after'] );
		$written[] = $item;
		if ( ! estecapelli_safe_patch_values_equal( get_post_meta( $item['post_id'], $item['meta_key'], true ), $item['after'] ) ) {
			estecapelli_safe_patch_restore_meta( $written );
			return new WP_Error( 'safe_patch_write_failed', sprintf( 'Verification failed for %s on post %d; completed writes were rolled back.', $item['meta_key'], $item['post_id'] ) );
		}
		$post_ids[ (int) $item['post_id'] ] = true;
	}
	foreach ( array_keys( $post_ids ) as $post_id ) {
		clean_post_cache( $post_id );
	}

	$applied_option  = estecapelli_safe_patch_option_key( 'applied', $patch_id );
	$applied_payload = array(
		'applied_at' => current_time( 'mysql', true ),
		'user_id'    => get_current_user_id(),
		'writes'     => count( $backup ),
	);
	update_option( $applied_option, $applied_payload, false );
	if ( $applied_payload !== get_option( $applied_option, false ) ) {
		estecapelli_safe_patch_restore_meta( $written );
		delete_option( $applied_option );
		return new WP_Error( 'safe_patch_state_failed', 'Patch writes were rolled back because the applied-state record could not be verified.' );
	}

	return count( $backup );
}

/** Roll a patch back only while every patched value still equals its result. */
function estecapelli_safe_patch_rollback( $patch_id ) {
	$applied = get_option( estecapelli_safe_patch_option_key( 'applied', $patch_id ), false );
	$backup  = get_option( estecapelli_safe_patch_option_key( 'backup', $patch_id ), false );
	if ( ! $applied || empty( $backup['items'] ) || $patch_id !== ( $backup['patch_id'] ?? '' ) ) {
		return new WP_Error( 'safe_patch_not_applied', 'No applied patch backup is available.' );
	}

	$preview = estecapelli_safe_patch_preview( $patch_id );
	if ( is_wp_error( $preview ) ) {
		return $preview;
	}
	if ( $preview['conflicts'] ) {
		return new WP_Error( 'safe_patch_rollback_conflict', sprintf( 'Rollback blocked: %d patch rows were edited after the patch.', $preview['conflicts'] ) );
	}
	$allowed_items = array();
	foreach ( $preview['rows'] as $row ) {
		foreach ( $row['fields'] as $field ) {
			$allowed_items[ $row['post_id'] . ':' . $field['meta_key'] ] = $field['after'];
		}
	}
	foreach ( $backup['items'] as $item ) {
		$item_key = ( $item['post_id'] ?? '' ) . ':' . ( $item['meta_key'] ?? '' );
		if ( ! array_key_exists( $item_key, $allowed_items ) || ! estecapelli_safe_patch_values_equal( $item['after'] ?? '', $allowed_items[ $item_key ] ) ) {
			return new WP_Error( 'safe_patch_invalid_backup', 'Rollback blocked because its stored backup does not match this immutable patch.' );
		}
		$current = get_post_meta( $item['post_id'], $item['meta_key'], true );
		if ( ! estecapelli_safe_patch_values_equal( $current, $item['after'] ) ) {
			return new WP_Error( 'safe_patch_rollback_conflict', sprintf( 'Rollback blocked: %s on post %d was edited after the patch.', $item['meta_key'], $item['post_id'] ) );
		}
	}

	estecapelli_safe_patch_restore_meta( $backup['items'] );
	$applied_option = estecapelli_safe_patch_option_key( 'applied', $patch_id );
	delete_option( $applied_option );
	if ( false !== get_option( $applied_option, false ) ) {
		foreach ( $backup['items'] as $item ) {
			update_post_meta( $item['post_id'], $item['meta_key'], $item['after'] );
		}
		return new WP_Error( 'safe_patch_rollback_state_failed', 'Rollback could not clear its state record, so the patched values were restored.' );
	}
	update_option(
		estecapelli_safe_patch_option_key( 'rolled_back', $patch_id ),
		array(
			'rolled_back_at' => current_time( 'mysql', true ),
			'user_id'        => get_current_user_id(),
		),
		false
	);

	return count( $backup['items'] );
}

/** Register the manual-only page under Tools. */
add_action( 'admin_menu', 'estecapelli_register_safe_content_updates' );
function estecapelli_register_safe_content_updates() {
	add_management_page(
		__( 'Safe Content Updates', 'estecapelli' ),
		__( 'Safe Content Updates', 'estecapelli' ),
		'manage_options',
		'estecapelli-safe-content-updates',
		'estecapelli_render_safe_content_updates'
	);
}

/** Human-readable summary of an exact field group for the admin preview. */
function estecapelli_safe_patch_field_summary( $fields, $value_key ) {
	$parts = array();
	foreach ( $fields as $field_name => $field ) {
		$value = $field[ $value_key ] ?? '';
		if ( is_array( $value ) ) {
			$value = implode( ' → ', array_map( 'strval', $value ) );
		} else {
			$value = wp_strip_all_tags( (string) $value );
		}
		$parts[] = $field_name . ': ' . $value;
	}
	return implode( ' | ', $parts );
}

/** Render previews and process explicit nonce-protected Apply/Rollback actions. */
function estecapelli_render_safe_content_updates() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$message = null;
	if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['estecapelli_safe_patch_action'], $_POST['patch_id'] ) ) {
		$patch_id = sanitize_key( wp_unslash( $_POST['patch_id'] ) );
		$action   = sanitize_key( wp_unslash( $_POST['estecapelli_safe_patch_action'] ) );
		if ( ! in_array( $action, array( 'apply', 'rollback' ), true ) ) {
			$result = new WP_Error( 'safe_patch_invalid_action', 'Unknown content-patch action.' );
		} else {
			check_admin_referer( 'estecapelli_safe_patch_' . $action . '_' . $patch_id );
			$result = 'rollback' === $action
				? estecapelli_safe_patch_rollback( $patch_id )
				: estecapelli_safe_patch_apply( $patch_id );
		}
		$message  = array(
			'type' => is_wp_error( $result ) ? 'error' : 'success',
			'text' => is_wp_error( $result )
				? $result->get_error_message()
				: sprintf( '%s completed successfully: %d exact meta values written.', ucfirst( $action ), (int) $result ),
		);
	}

	$patches = estecapelli_safe_content_patches();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Safe Content Updates', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Nothing on this screen runs automatically. Review every language and apply one immutable page patch at a time.', 'estecapelli' ); ?></p>
		<?php if ( $message ) : ?>
			<div class="notice notice-<?php echo esc_attr( $message['type'] ); ?>"><p><?php echo esc_html( $message['text'] ); ?></p></div>
		<?php endif; ?>

		<?php foreach ( $patches as $patch_id => $patch ) : ?>
			<?php $preview = estecapelli_safe_patch_preview( $patch_id ); ?>
			<?php $superseded_by = (string) ( $patch['superseded_by'] ?? '' ); ?>
			<div class="card" style="max-width:none;margin-top:20px;">
				<h2><?php echo esc_html( $patch['title'] ); ?></h2>
				<p><code><?php echo esc_html( $patch_id ); ?></code> — <?php echo esc_html( $patch['description'] ); ?></p>
				<?php if ( is_wp_error( $preview ) ) : ?>
					<div class="notice notice-error inline"><p><?php echo esc_html( $preview->get_error_message() ); ?></p></div>
					<?php continue; ?>
				<?php endif; ?>

				<p>
					<strong><?php esc_html_e( 'Status:', 'estecapelli' ); ?></strong>
					<?php
					if ( $preview['applied'] ) {
						esc_html_e( 'Applied — rollback available', 'estecapelli' );
					} elseif ( $superseded_by ) {
						echo esc_html( sprintf( 'Superseded by %s — cannot be applied', $superseded_by ) );
					} else {
						esc_html_e( 'Not applied', 'estecapelli' );
					}
					?>
					· <?php echo esc_html( sprintf( '%d pending, %d conflicts', (int) $preview['pending'], (int) $preview['conflicts'] ) ); ?>
				</p>
				<table class="widefat striped">
					<thead><tr><th><?php esc_html_e( 'Language', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Current', 'estecapelli' ); ?></th><th><?php esc_html_e( 'New', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Check', 'estecapelli' ); ?></th></tr></thead>
					<tbody>
					<?php foreach ( $preview['rows'] as $row ) : ?>
						<tr>
							<td><strong><?php echo esc_html( strtoupper( $row['language'] ) ); ?></strong> · <?php echo esc_html( $row['location'] ); ?></td>
							<td style="max-width:520px;overflow-wrap:anywhere;"><?php echo esc_html( estecapelli_safe_patch_field_summary( $row['fields'], 'current' ) ); ?></td>
							<td style="max-width:520px;overflow-wrap:anywhere;"><?php echo esc_html( estecapelli_safe_patch_field_summary( $row['fields'], 'after' ) ); ?></td>
							<td><code><?php echo esc_html( $row['status'] ); ?></code></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<div style="margin-top:16px;">
				<?php if ( $preview['applied'] ) : ?>
					<form method="post" style="display:inline;">
						<?php wp_nonce_field( 'estecapelli_safe_patch_rollback_' . $patch_id ); ?>
						<input type="hidden" name="patch_id" value="<?php echo esc_attr( $patch_id ); ?>">
						<button class="button" type="submit" name="estecapelli_safe_patch_action" value="rollback"><?php esc_html_e( 'Rollback this patch', 'estecapelli' ); ?></button>
					</form>
				<?php elseif ( ! $superseded_by ) : ?>
					<form method="post" style="display:inline;">
						<?php wp_nonce_field( 'estecapelli_safe_patch_apply_' . $patch_id ); ?>
						<input type="hidden" name="patch_id" value="<?php echo esc_attr( $patch_id ); ?>">
						<button class="button button-primary" type="submit" name="estecapelli_safe_patch_action" value="apply" <?php disabled( $preview['conflicts'] || ! $preview['pending'] ); ?>><?php esc_html_e( 'Apply this reviewed patch', 'estecapelli' ); ?></button>
					</form>
				<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}
