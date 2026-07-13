# Estecapelli — Live URL Map (migration master reference)

Source: the 7 language sitemaps at https://estecapelli.com/sitemap.xml
Captured: 2026-07-13. **595 indexed URLs** = 85 per language × 7 languages
(en, tr, fr, it, es, pl, pt).

**Golden rule of the migration:** every URL below must, on the new site, either
load as a real page at the *same* address, or 301-redirect to the right place.
Never a 404.

The new theme already serves English under `/en/…` with the exact EN slugs in
this file. WPML reproduces the other six languages by translating (a) the
section base slugs and (b) each page's slug to match the tables below.

---

## 1. Section base slugs  (the `/{lang}/{BASE}` part)

These are the translated bases every inner URL is built on. In WPML these are
set under **WPML → Taxonomy/Post-type slug translation** and the page slugs.

| concept | en | tr | fr | it | es | pl | pt |
|---|---|---|---|---|---|---|---|
| home | home | ana-sayfa | maison | home | inicio | strona-glowna | inicio |
| hair-transplant | hair-transplant | sac-ekimi | greffe-de-cheveux | trapianto-di-capelli | trasplante-capilar | przeszczep-wlosow | transplante-capilar |
| plastic-surgery | plastic-surgery | estetik-cerrahi | chirurgie-plastique | chirurgia-plastica | cirugia-plastica | chirurgia-plastyczna | cirurgia-plastica |
| dental-treatment | dental-treatment | dis-tedavisi | traitement-dentaire | trattamento-dentale | tratamiento-dental | leczenie-stomatologiczne | tratamento-dentario |
| before-after | before-after | oncesi-sonrasi | avant-apres | prima-dopo | antes-despues | przed-po | antes-depois |
| about-us | about-us | hakkimizda | a-propos-de-nous | chi-siamo | sobre-nosotros | o-nas | sobre-nos |
| blog | blog | blog | blog | blog | blog | blog | blog |
| contact | contact | iletisim | contact | contatto | contacto | kontakt | contato |

### about-us sub-bases

| concept | en | tr | fr | it | es | pl | pt |
|---|---|---|---|---|---|---|---|
| our-doctors | our-doctors | doktorlarimiz | nos-medecins | i-nostri-medici | nuestros-doctores | nasi-lekarze | nossos-medicos |
| medical-director | medical-director | tibbi-direktor | directeur-medical | direttore-medico | director-medico | dyrektor-medyczny | diretor-medico |
| our-team | our-team | ekibimiz | notre-equipe | il-nostro-team | nuestro-equipo | nasz-zespol | nossa-equipe |

---

## 2. Hair-transplant leaf slugs  (`/{lang}/{hair-base}/{LEAF}`)

| concept | en | tr | fr | it | es | pl | pt |
|---|---|---|---|---|---|---|---|
| overview | hair-transplant-overview | sac-ekimi-genel-bakis | apercu-de-la-greffe-de-cheveux | panoramica-sul-trapianto-di-capelli | descripcion-general-del-trasplante-capilar | przeglad-przeszczepu-wlosow | visao-geral-do-transplante-capilar |
| sapphire-fue | sapphire-fue-hair-transplant | safir-fue-sac-ekimi | greffe-de-cheveux-fue-sapphire | trapianto-di-capelli-fue-sapphire | trasplante-capilar-fue-sapphire | przeszczep-wlosow-metoda-sapphire-fue | transplante-capilar-fue-sapphire |
| dhi | dhi-hair-transplant | dhi-sac-ekimi | greffe-de-cheveux-dhi | trapianto-di-capelli-dhi | trasplante-de-cabello-dhi | przeszczep-wlosow-dhi | transplante-capilar-dhi |
| exosome-fue | exosome-fue-hair-transplant | exosome-fue-sac-ekimi | greffe-capillaire-exosome-fue | trapianto-di-capelli-exosome-fue | trasplante-capilar-exosome-fue | przeszczep-wlosow-exosome-fue | transplante-capilar-exosome-fue |
| vita | vita-treatment | vita-tedavisi | traitement-vita | trattamento-vita | tratamiento-vita | leczenie-vita | tratamento-vita |
| tricholab | tricholab | tricholab | tricholab | tricholab | tricholab | tricholab | tricholab |
| female | female-hair-transplant | kadin-sac-ekimi | greffe-de-cheveux-feminine | trapianto-di-capelli-femminile | trasplante-capilar-femenino | przeszczep-wlosow-u-kobiet | transplante-capilar-feminino |
| eyebrow | eyebrow-transplant | kas-ekimi | transplantation-de-sourcils | trapianto-di-sopracciglia | trasplante-de-cejas | przeszczep-brwi | transplante-de-sobrancelhas |
| beard | beard-transplant | sakal-ekimi | transplantation-de-barbe | beard-trapianto-di-barba | trasplante-de-barba | przeszczep-brody | transplante-de-barba |
| mesotherapy | hair-mesotherapy | sac-mezoterapisi | mesotherapie-capillaire | mesoterapia-per-capelli | mesoterapia-capilar | mezoterapia-wlosow | mesoterapia-capilar |
| pre-period | pre-hair-transplant-period | sac-ekim-oncesi-donem | periode-pre-transplantation-capillaire | periodo-pre-trapianto-di-capelli | periodo-previo-al-trasplante-capilar | okres-przed-przeszczepem-wlosow | periodo-pre-transplante-capilar |
| post-period | post-hair-transplant-period | sac-ekimi-sonrasi-donem | periode-post-greffe-de-cheveux | periodo-post-trapianto-di-capelli | periodo-posterior-al-trasplante-capilar | okres-po-przeszczepie-wlosow | periodo-pos-transplante-capilar |
| comparison (nested under dhi) | hair-transplant-techniques-comparison2 | sac-ekimi-teknikleri-karsilastirmasi-2 | comparaison-des-techniques-de-greffe-de-cheveux-2 | confronto-tra-tecniche-di-trapianto-capillare-2 | comparacion-de-tecnicas-de-trasplante-capilar-2 | porownanie-technik-przeszczepu-wlosow-2 | comparacao-das-tecnicas-de-transplante-capilar-2 |

> ⚠ IT `beard` slug carries an English `beard-` prefix leak (`beard-trapianto-di-barba`). Keep it exactly as-is — it is the indexed URL.
> ⚠ The comparison page is nested one level deeper: `/{lang}/{hair-base}/{dhi-slug}/{comparison-slug}`.

---

## 3. Plastic-surgery leaf slugs  (`/{lang}/{plastic-base}/{LEAF}`)

| concept | en | tr | fr | it | es | pl | pt |
|---|---|---|---|---|---|---|---|
| overview | plastic-surgery-overview | plastic-surgery-overview | plastic-surgery-overview | plastic-surgery-overview | plastic-surgery-overview | plastic-surgery-overview | plastic-surgery-overview |
| rhinoplasty | rhinoplasty | burun-estetigi | rhinoplastie | rinoplastica | rinoplastia | rynoplastyka | rinoplastia |
| breast | breast-aesthetics-breast-surgery | meme-estetigi-gogus-estetigi | esthetique-mammaire-chirurgie-mammaire | estetica-del-seno-chirurgia-del-seno | estetica-mamaria-cirugia-de-pecho | estetyka-piersi-chirurgia-piersi | estetica-mamaria-cirurgia-de-mama |
| bbl | bbl | bbl | bbl | bbl | bbl | bbl | bbl |
| liposuction | liposuction | liposuction | liposuccion | liposuzione | liposuccion | liposukcja | lipoaspiracao |
| face-neck-lift | face-and-neck-lift-surgery | yuz-ve-boyun-germe-ameliyati | chirurgie-de-lifting-du-visage-et-du-cou | chirurgia-di-lifting-del-viso-e-del-collo | cirugia-de-lifting-facial-y-de-cuello | chirurgia-liftingu-twarzy-i-szyi | cirurgia-de-lifting-facial-e-de-pescoco |
| abdominoplasty | abdominoplasty-tummy-tuck | karin-germe-ameliyati | abdominoplastie | addominoplastica | abdominoplastia | plastyka-brzucha | abdominoplastia |
| gynecomastia | gynecomastia | jinekomasti | gynecomastie | ginecomastia | ginecomastia | ginekomastia | ginecomastia |
| obesity | obesity-surgeries-bariatric-surgery-and-gastric-balloon | obezite-ameliyatlari-bariatrik-cerrahi-ve-mide-balonu | chirurgies-de-l-obesite-chirurgie-bariatrique-et-ballon-gastrique | chirurgie-dell-obesita-chirurgia-bariatrica-e-palloncino-gastrico | cirugias-de-obesidad-cirugia-bariatrica-y-balon-gastrico | operacje-otylosci-chirurgia-bariatryczna-i-balon-zoladkowy | cirurgias-de-obesidade-cirurgia-bariatrica-e-balao-gastrico |

> ⚠ `plastic-surgery-overview` is **untranslated in every language** — the exact same English slug appears under all 7. Keep it that way.
> ⚠ `bbl` and `liposuction` (en+tr) are shared/untranslated too.

---

## 4. Dental leaf slugs  (`/{lang}/{dental-base}/{LEAF}`)

| concept | en | tr | fr | it | es | pl | pt |
|---|---|---|---|---|---|---|---|
| overview | dental-treatment-overview | dis-tedavisi-genel-bakis | apercu-du-traitement-dentaire | panoramica-del-trattamento-dentale | resumen-del-tratamiento-dental | przeglad-leczenia-stomatologicznego | visao-geral-do-tratamento-dentario |
| implant | dental-implant | dis-implanti | implant-dentaire | impianto-dentale | implante-dental | implant-zebowy | implante-dentario |
| hollywood-smile | hollywood-smile | hollywood-gulusu | sourire-hollywoodien | sorriso-hollywoodiano | sonrisa-hollywoodense | hollywoodzki-usmiech | sorriso-hollywoodiano |

---

## 5. Doctors  (`/{lang}/{about-base}/{sub-base}/{DOCTOR}`)

Doctor leaf slugs are **identical across all languages** — only the parent base
translates (our-doctors / medical-director tables in §1).

| doctor | parent sub-base | leaf slug (all langs) |
|---|---|---|
| Mehmet Hanifi Kutlar | medical-director | mehmet-hanifi-kutlar |
| Op. Dr. Hasan Çelik | our-doctors | op-dr-hasan-celik |
| Op. Dr. Mehmet Palalı | our-doctors | op-dr-mehmet-palali |
| Op. Dr. Necdet Derici | our-doctors | op-dr-necdet-derici |
| Op. Dr. Ali Durmuş | our-doctors | op-dr-ali-durmus |

---

## 6. Before/After gallery  (`/{lang}/{before-after-base}/{ITEM}`)

40 items. Item slugs are **identical across all languages** (language-neutral):

```
beforeafter-hairtransplant-vitatreatment-1 … -10   (10)
beforeafter-hairtransplant-exosomefue-1 … -12      (12)
beforeafter-hairtransplant-exosome-1 … -5          (5)
beforeafter-hairtransplant-dhi-1 … -3              (3)
beforeafte_13   beforeafter_15   beforeafter_16   beforeafter_17   beforeafter_18   (5)
```
(= 35 named + 5 numbered = 40)

---

## 7. Blog posts  (`/{lang}/blog/{POST}`)

Blog slugs are per-language translated. **EN & IT have 9 posts; TR/FR/ES/PL/PT
have 8** (they lack the "complete expert guide" pillar). Slugs verbatim:

**en (9):** hair-transplant-turkey-complete-expert-guide · hair-transplant-for-hiv-positive-patients-in-turkey · unshaven-hair-transplant · can-diabetic-patients-undergo-a-hair-transplant · is-hair-transplant-a-painful-procedure · will-my-hair-fall-out-again-after-a-hair-transplant · hair-transplant-with-the-fue-vita-technique · hair-transplant-for-hiv-positive-patients · unshaven-hair-transplant-for-women

**tr (8):** hiv-pozitif-hastalarina-turkiye-de-sac-ekimi · tirassiz-sac-ekimi · diyabet-hastalari-sac-ekimi-yaptirabilir-mi · sac-ekimi-agrili-bir-islem-midir · sac-ekimi-sonrasinda-saclarim-tekrar-dokulur-mu · fue-vita-teknigi-ile-sac-ekimi · hiv-pozitif-hastalara-sac-ekimi · kadinlarda-trassiz-sac-ekimi

**fr (8):** greffe-de-cheveux-pour-patients-seropositifs-en-turquie · greffe-de-cheveux-sans-rasage · les-patients-diabetiques-peuvent-ils-subir-une-greffe-de-cheveux · la-greffe-de-cheveux-est-elle-une-procedure-douloureuse · mes-cheveux-vont-ils-retomber-apres-une-greffe-de-cheveux · greffe-de-cheveux-avec-la-technique-fue-vita · greffe-de-cheveux-pour-les-patients-seropositifs-vih · greffe-de-cheveux-sans-rasage-chez-la-femme

**it (9):** trapianto-capelli-in-turchia-la-guida-completa · trapianto-di-capelli-per-pazienti-hiv-positivi-in-turchia · **greffe-de-cheveux-sans-rasage** (⚠ FR-slug leak) · i-pazienti-diabetici-possono-sottoporsi-a-un-trapianto-di-capelli · il-trapianto-di-capelli-e-una-procedura-dolorosa · i-miei-capelli-ricadranno-dopo-un-trapianto-di-capelli · trapianto-di-capelli-con-la-tecnica-fue-vita · trapianto-di-capelli-per-pazienti-sieropositivi-hiv · trapianto-di-capelli-senza-rasatura-nelle-donne

**es (8):** trasplante-capilar-para-pacientes-vih-positivos-en-turquia · trasplante-de-cabello-sin-afeitar · **i-pazienti-diabetici-possono-sottoporsi-a-un-trapianto-di-capelli** (⚠ IT-slug leak) · -es-doloroso-el-trasplante-capilar · -se-volvera-a-caer-mi-cabello-despues-de-un-trasplante-capilar · trasplante-capilar-con-la-tecnica-fue-vita · trasplante-capilar-para-pacientes-vih-positivos · trasplante-capilar-sin-rasurado-en-mujeres

**pl (8):** **trasplante-capilar-para-pacientes-vih-positivos-en-turquia** (⚠ ES-slug leak) · przeszczep-wlosow-bez-golenia · czy-pacjenci-z-cukrzyca-moga-poddac-sie-przeszczepowi-wlosow · czy-przeszczep-wlosow-jest-bolesnym-zabiegiem · czy-moje-wlosy-wypadna-ponownie-po-przeszczepie-wlosow · przeszczep-wlosow-technika-fue-vita · przeszczep-wlosow-u-pacjentow-hiv-pozytywnych · przeszczep-wlosow-bez-golenia-u-kobiet

**pt (8):** transplante-capilar-para-pacientes-hiv-positivos-na-turquia · transplante-capilar-sem-barbear · pacientes-com-diabetes-podem-realizar-transplante-capilar · o-transplante-capilar-e-um-procedimento-doloroso · meu-cabelo-vai-cair-novamente-apos-o-transplante-capilar · transplante-capilar-com-a-tecnica-fue-vita · transplante-capilar-para-pacientes-hiv-positivos · transplante-capilar-sem-raspagem-em-mulheres

> ⚠ The three slug-leaks (it/es/pl) are real indexed URLs. When creating those
> translations in WPML, set the slug to exactly the leaked value so the URL does
> not 404. (Or 301 them — see redirect plan.)

---

## 8. Special cases & redirect notes

1. **German `/de/`** is NOT in any sitemap → the old `/de/` pages were demo/dummy
   (`dienstleistung-1`, `kategorie-c`, `nachrichten`). Blanket redirect `/de/*` → `/en/`.
2. **Untranslated slugs to preserve exactly:** `plastic-surgery-overview` (all
   langs), `bbl` (all), `tricholab` (all), `liposuction` (en+tr), all doctor
   leaves, all before/after items.
3. **Slug leaks to preserve or redirect:** it/es/pl blog (§7), it beard (§2).
4. **www ↔ non-www and trailing-slash** redirects: handled automatically by
   WordPress + WPML canonicalization — no manual rules needed.
5. **Home:** live English inner home is `/en/home`; the bare `/` is the site root.
   Keep `/en/` — do NOT strip it (stripping 404s the indexed English tree).
