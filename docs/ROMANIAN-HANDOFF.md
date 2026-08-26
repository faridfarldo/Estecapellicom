# Romanian translation — handoff spec

This is the complete brief for the Romanian (`ro`) content that is being
produced outside this repo's main workstream. Everything here is frozen: the
slugs, the file list, and the structural rules. Do not invent alternatives to
any of it — the importer validates against these values and rejects the whole
batch if one file disagrees.

## What is already done

`inc/indexed-urls.php` carries the full Romanian URL contract. It is staged but
switched off: `estecapelli_indexed_languages()` still returns the seven existing
codes, so nothing Romanian is live yet. The switch is flipped last, after the
content lands.

`inc/data/translations/ro/` exists with all six folders, empty.

## Division of work

| Owner | Folders | Files |
|---|---|---|
| **This repo (in progress)** | `pages/`, `hair-transplant/` | 20 JSON |
| **You** | `plastic-surgery/`, `dental-treatment/`, `legal/`, `doctors/` | 20 JSON |
| **You** | `inc/data/blog/ro/` | 8 HTML |

Your 28 files are independent of each other and of the 20 in progress. Order
does not matter.

## Where the English source lives

The English original is **not** a JSON file. It lives in PHP seeds:

- treatments → `inc/data/treatments-seed.php`
- pages → `inc/data/pages-seed.php`
- doctors → `inc/data/doctors-seed.php`
- blog → `inc/data/blog/*.html`

Those seeds wrap every string in `__()`, so reading them raw is awkward. Dump
the English for one entry as JSON instead:

```
php tools/dump-english-source.php . treatments rhinoplasty
php tools/dump-english-source.php . pages about-us
```

Use `treatments` for the `plastic-surgery/` and `dental-treatment/` folders,
`pages` for the rest. Doctors come from `inc/data/doctors-seed.php`, which is
small enough to read directly.

Translate from those. Use the **Portuguese** overlay in
`inc/data/translations/pt/<same folder>/<same filename>.json` as your structural
template — every language overlays the identical seed, so the Portuguese file
tells you exactly which keys exist and in what order. Copy its shape, replace
its text.

Do not translate from Portuguese. It is the shape reference, not the source.
Some of it is visibly machine-translated (`para coordenar seu tratamento.
visita.`) — do not reproduce those errors in Romanian.

## Hard structural rules

Breaking any of these makes the importer reject the batch.

1. **`acf_fc_layout` is machinery, never copy.** Values are `hero`, `intro`,
   `steps`, `stepbook`, `faq`, `form`, `gallery`, `candidate`, `quick_stats`,
   `related`, `doctors`, `team`. They stay in English, exactly. Translating one
   breaks rendering — this is a bug the theme already had once, which is why
   `inc/acfml-layout-guard.php` exists.
2. **Never add, remove, or reorder a section.** The Romanian `sections` array
   must have the same length as the Portuguese one, with the same layout in
   each position. Section images are matched to the English post *by ordinal*,
   so a shifted array silently attaches the wrong picture.

   **An overlay is deliberately a sparse subset of the seed.** A landing page
   may have twelve sections in the seed while the overlay translates only
   seven of them — the untranslated ones are rendered from the English source
   on purpose. Do not "complete" an overlay by adding the sections it skips.
   Match the Portuguese file's section list exactly, no more and no less.
   (Note that this also means seed section *n* is usually not overlay section
   *n*: line them up by `acf_fc_layout`, in order, not by position.)
3. **Never add, remove, or rename a key.** Same keys, same nesting, same array
   lengths for `items`, `points`, `credentials`, `stats`, `faq`.
4. **`source_slug` = the filename** without `.json`. Never translated.
5. **`slug`** = exactly the value in the table below.
6. **Do not touch** `image_url`, `localized_image_url`, `video_url`,
   `media_type`, or any URL that is not an internal site link. Copy them
   verbatim from the Portuguese file.
7. **Internal links carry a language prefix.** The Portuguese files contain
   hrefs like `/pt/politica-de-cookies`. In Romanian these become `/ro/` plus
   the Romanian slug from the table. A `/pt/` or `/en/` path left in a Romanian
   file is a validator failure.
8. **Encoding is UTF-8, and Romanian diacritics belong in the text**: ă, â, î,
   ș, ț. Use the comma-below forms ș/ț (U+0219 / U+021B), not the cedilla
   ş/ţ. Slugs are the opposite — ASCII only, no diacritics.

   This rule governs *Romanian words only*. Turkish proper names keep their own
   Turkish letters: `Durmuş` and `Çelik` are spelled with the Turkish ş and Ç
   and must never be "corrected" to Romanian ș. A person's name is not text to
   be normalised.

Translatable keys are: `title`, `eyebrow`, `lead`, `body`, `label`, `question`,
`answer`, `name` (section names only, never a doctor's name), `position`,
`bio`, `credentials`, `content`, `submit_label`, `value`, `time`, `cta`.

## The frozen slug contract

`inc/indexed-urls.php` is authoritative; this table mirrors it.

### plastic-surgery/ → `/ro/chirurgie-plastica/…`

| file | `slug` |
|---|---|
| `abdominoplasty-tummy-tuck.json` | `abdominoplastie` |
| `bbl.json` | `bbl` |
| `breast-aesthetics-breast-surgery.json` | `estetica-sanilor-chirurgia-sanilor` |
| `face-and-neck-lift-surgery.json` | `lifting-facial-si-de-gat` |
| `gynecomastia.json` | `ginecomastie` |
| `liposuction.json` | `liposuctie` |
| `obesity-surgeries-bariatric-surgery-and-gastric-balloon.json` | `operatii-de-obezitate-chirurgie-bariatrica-si-balon-gastric` |
| `rhinoplasty.json` | `rinoplastie` |

### dental-treatment/ → `/ro/tratament-dentar/…`

| file | `slug` |
|---|---|
| `dental-implant.json` | `implant-dentar` |
| `hollywood-smile.json` | `zambet-de-hollywood` |

### legal/ → `/ro/…` (no parent folder in the URL)

| file | `slug` | `title` |
|---|---|---|
| `privacy-policy.json` | `politica-de-confidentialitate` | Politica de confidențialitate |
| `terms.json` | `termeni-si-conditii` | Termeni și condiții |
| `kvkk-disclosure.json` | `nota-de-informare-kvkk` | Notă de informare KVKK privind prelucrarea datelor |
| `cookie-policy.json` | `politica-de-cookie-uri` | Politica de cookie-uri |

The `title` column here is fixed, because these four titles also appear in
`estecapelli_legal_pages_manifest()`.

### doctors/ → slug is the source slug, unchanged

`mehmet-hanifi-kutlar`, `op-dr-ali-durmus`, `op-dr-hasan-celik`,
`op-dr-mehmet-palali`, `op-dr-necdet-derici`, `prof-dr-binnur-ustun`.

The `name` field holds an honorific plus the person's name, e.g.
`Op. Dr. Hasan Çelik`. **Localize the honorific, never the name.** The spelling
and diacritics of the name itself are fixed: `Çelik`, `Üstün`, `Palalı` stay
exactly as written. Portuguese does the same — it renders Binnur Üstün as
`Prof.ª Dra. Binnur Üstün`.

- `Prof. Dr.` → `Prof. Dr.`
- `Op. Dr.` → `Dr.` — Romanian has no equivalent of the Turkish surgeon prefix,
  and `Dr.` is the idiomatic form. The specialty is already stated in
  `position`.

`position`, `bio` and `credentials` become Romanian. **`credentials` must keep
the same number of entries** as the English source — the doctor importer checks
the count explicitly and fails the batch on a mismatch.

### blog → `/ro/blog/…`

HTML files go in `inc/data/blog/ro/`, keeping the **English filename**.

| English filename | `ro` URL slug |
|---|---|
| `hair-transplant-for-hiv-positive-patients-in-turkey.html` | `transplant-de-par-pentru-pacientii-hiv-pozitivi-in-turcia` |
| `hair-transplant-for-hiv-positive-patients.html` | `transplant-de-par-pentru-pacientii-hiv-pozitivi` |
| `unshaven-hair-transplant.html` | `transplant-de-par-fara-barbierire` |
| `unshaven-hair-transplant-for-women.html` | `transplant-de-par-fara-barbierire-la-femei` |
| `can-diabetic-patients-undergo-a-hair-transplant.html` | `pot-pacientii-diabetici-sa-faca-transplant-de-par` |
| `is-hair-transplant-a-painful-procedure.html` | `este-transplantul-de-par-o-procedura-dureroasa` |
| `will-my-hair-fall-out-again-after-a-hair-transplant.html` | `imi-va-cadea-parul-din-nou-dupa-transplantul-de-par` |
| `hair-transplant-with-the-fue-vita-technique.html` | `transplant-de-par-cu-tehnica-fue-vita` |

`hair-transplant-turkey-complete-expert-guide.html` is **not** in scope — it
exists only in English and Italian, and Romanian follows the other five
languages in skipping it.

Match the HTML structure of the existing per-language files exactly: same tags,
same heading levels, same order. Translate text nodes only.

## Terminology

Fixed choices. Be consistent — these appear hundreds of times and inconsistency
reads as sloppiness to a native speaker.

| English | Romanian |
|---|---|
| hair transplant | transplant de păr |
| graft | grefă (pl. grefe) |
| follicle / follicular unit | folicul / unitate foliculară |
| donor area | zona donatoare |
| recipient area | zona receptoare |
| hairline | linia frontală a părului |
| crown | creștet |
| shedding | căderea părului |
| density | densitate |
| local anaesthesia | anestezie locală |
| sedation | sedare |
| consultation | consultație |
| free consultation | consultație gratuită |
| treatment plan | plan de tratament |
| aftercare | îngrijire post-operatorie |
| scarring | cicatrizare |
| session | ședință |
| our doctors | medicii noștri |
| patient | pacient |
| clinic | clinică |
| before / after | înainte / după |
| **Chief Physician** | **Medic-șef** |

`Chief Physician` → **Medic-șef** everywhere. Do not write "Director medical"
in body copy: that title was deliberately retired site-wide and must not come
back. (`director-medical` survives only as a frozen URL slug, never as prose.)

Keep untranslated: brand and technique names — Estecapelli, FUE, DHI, Sapphire
FUE, Exosome FUE, Vita, TrichoLab, BBL, PRP, Norwood, KVKK, GDPR.

## Tone

Address the reader as **tu** (informal singular), matching the other languages.
Medical claims must stay exactly as strong or as hedged as the English — do not
strengthen a hedged claim ("can help reduce" ≠ "elimină"). This is regulated
medical advertising.

## Validate before handing back

```
node tools/validate-ro-translations.js plastic-surgery dental-treatment legal doctors
```

It compares each Romanian file against the Portuguese one and reports: invalid
JSON, wrong `source_slug` or `slug`, missing/extra/renamed keys, mismatched
array lengths, translated `acf_fc_layout`, and internal links still pointing at
another language. Exit code 0 means clean.

Run it with no arguments to check every folder. Files not yet written report as
`missing`, which is expected until the batch is complete.

A clean run is required before handing the work back. It does not judge the
Romanian itself — only that the shape is importable.
