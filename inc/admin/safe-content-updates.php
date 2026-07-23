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
			'languages'   => array(
				'fr' => estecapelli_safe_patch_vita_empty_step_operations(),
				'it' => estecapelli_safe_patch_vita_empty_step_operations(),
				'es' => estecapelli_safe_patch_vita_empty_step_operations(),
				'pt' => estecapelli_safe_patch_vita_empty_step_operations(),
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

			$indices = array_keys( $layouts, $layout, true );
			if ( 1 !== count( $indices ) ) {
				return new WP_Error( 'safe_patch_layout_ambiguous', sprintf( '%s post %d has %d matching %s layouts; expected exactly one.', $language, $target_id, count( $indices ), $layout ) );
			}
			$layout_index = (int) reset( $indices );
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
			<div class="card" style="max-width:none;margin-top:20px;">
				<h2><?php echo esc_html( $patch['title'] ); ?></h2>
				<p><code><?php echo esc_html( $patch_id ); ?></code> — <?php echo esc_html( $patch['description'] ); ?></p>
				<?php if ( is_wp_error( $preview ) ) : ?>
					<div class="notice notice-error inline"><p><?php echo esc_html( $preview->get_error_message() ); ?></p></div>
					<?php continue; ?>
				<?php endif; ?>

				<p>
					<strong><?php esc_html_e( 'Status:', 'estecapelli' ); ?></strong>
					<?php echo $preview['applied'] ? esc_html__( 'Applied — rollback available', 'estecapelli' ) : esc_html__( 'Not applied', 'estecapelli' ); ?>
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
				<?php else : ?>
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
