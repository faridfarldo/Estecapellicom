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

/** Immutable patch registry. Applied patch IDs must never be edited or reused. */
function estecapelli_safe_content_patches() {
	return array(
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
	);
}

/** Stable non-autoloaded option key for patch state. */
function estecapelli_safe_patch_option_key( $kind, $patch_id ) {
	return 'estecapelli_safe_patch_' . $kind . '_' . substr( hash( 'sha256', $patch_id ), 0, 20 );
}

/** Compare a current value with one or more allowed pre-patch values. */
function estecapelli_safe_patch_matches( $current, $allowed ) {
	$allowed = is_array( $allowed ) ? $allowed : array( $allowed );
	return in_array( (string) $current, array_map( 'strval', $allowed ), true );
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
		$current = (string) get_post_meta( $item['post_id'], $item['meta_key'], true );
		if ( $current !== (string) $item['before'] ) {
			estecapelli_safe_patch_restore_meta( $written );
			return new WP_Error( 'safe_patch_concurrent_edit', sprintf( 'Patch blocked: %s on post %d changed after preview; completed writes were rolled back.', $item['meta_key'], $item['post_id'] ) );
		}
		update_post_meta( $item['post_id'], $item['meta_key'], $item['after'] );
		$written[] = $item;
		if ( (string) get_post_meta( $item['post_id'], $item['meta_key'], true ) !== (string) $item['after'] ) {
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
		if ( ! array_key_exists( $item_key, $allowed_items ) || (string) ( $item['after'] ?? '' ) !== (string) $allowed_items[ $item_key ] ) {
			return new WP_Error( 'safe_patch_invalid_backup', 'Rollback blocked because its stored backup does not match this immutable patch.' );
		}
		$current = (string) get_post_meta( $item['post_id'], $item['meta_key'], true );
		if ( $current !== (string) $item['after'] ) {
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
		$value   = wp_strip_all_tags( (string) ( $field[ $value_key ] ?? '' ) );
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
