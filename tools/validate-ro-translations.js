#!/usr/bin/env node
/**
 * Structural validator for the Romanian translation overlays.
 *
 * This reproduces, offline, the check the WordPress importer runs on a live
 * site (estecapelli_it_hair_validate_coverage): it walks the ENGLISH SEED and
 * requires the overlay to carry, at the same position, every acf_fc_layout
 * verbatim and every text field that is non-empty in the source.
 *
 * The seed is the contract — not another language's overlay. Validating
 * against Portuguese was the earlier approach and it was wrong: pt is itself
 * behind the seed on two pages, so it would both bless a stale Romanian file
 * and reject a correct one.
 *
 * Usage:  node tools/validate-ro-translations.js [folder ...]
 * Exit 0 = clean, 1 = problems found.
 *
 * Requires php on PATH (it reads the seeds via tools/dump-english-source.php).
 */

const fs = require('fs');
const path = require('path');
const { execFileSync } = require('child_process');

const ROOT = path.resolve(__dirname, '..');
const BASE = path.join(ROOT, 'inc', 'data', 'translations');
const TARGET = 'ro';

/** Which seed backs each overlay folder. */
const SEED_OF = {
	pages: 'pages',
	'hair-transplant': 'treatments',
	'plastic-surgery': 'treatments',
	'dental-treatment': 'treatments',
	doctors: 'doctors',
	legal: null, // Prose pages: no sections to line up.
};

/**
 * Exactly the field list in the importer's coverage check. A field outside
 * this list is not required, however translatable it looks.
 */
const TEXT_FIELDS = new Set([
	'eyebrow', 'title', 'lead', 'body', 'footer', 'label', 'value',
	'question', 'answer', 'submit_label', 'caption', 'time', 'position',
	'role', 'name',
]);

/**
 * Frozen Romanian slug contract. inc/indexed-urls.php is authoritative; this
 * mirrors it so the slug check needs no routing code. Doctors keep their
 * English slug because a person's name is not translated.
 */
const RO_SLUGS = {
	'about-us': 'despre-noi',
	'before-after': 'inainte-dupa',
	contact: 'contact',
	'dental-treatment': 'tratament-dentar',
	'hair-transplant': 'transplant-de-par',
	'medical-director': 'director-medical',
	'our-doctors': 'medicii-nostri',
	'our-team': 'echipa-noastra',
	'plastic-surgery': 'chirurgie-plastica',
	'post-hair-transplant-period': 'perioada-de-dupa-transplantul-de-par',
	'pre-hair-transplant-period': 'perioada-dinainte-de-transplantul-de-par',
	tricholab: 'tricholab',

	'beard-transplant': 'transplant-de-barba',
	'dhi-hair-transplant': 'transplant-de-par-dhi',
	'exosome-fue-hair-transplant': 'transplant-de-par-exosome-fue',
	'eyebrow-transplant': 'transplant-de-sprancene',
	'female-hair-transplant': 'transplant-de-par-la-femei',
	'hair-mesotherapy': 'mezoterapie-capilara',
	'sapphire-fue-hair-transplant': 'transplant-de-par-fue-sapphire',
	'vita-treatment': 'tratament-vita',

	'abdominoplasty-tummy-tuck': 'abdominoplastie',
	bbl: 'bbl',
	'breast-aesthetics-breast-surgery': 'estetica-sanilor-chirurgia-sanilor',
	'face-and-neck-lift-surgery': 'lifting-facial-si-de-gat',
	gynecomastia: 'ginecomastie',
	liposuction: 'liposuctie',
	'obesity-surgeries-bariatric-surgery-and-gastric-balloon':
		'operatii-de-obezitate-chirurgie-bariatrica-si-balon-gastric',
	rhinoplasty: 'rinoplastie',

	'dental-implant': 'implant-dentar',
	'hollywood-smile': 'zambet-de-hollywood',

	'cookie-policy': 'politica-de-cookie-uri',
	'kvkk-disclosure': 'nota-de-informare-kvkk',
	'privacy-policy': 'politica-de-confidentialitate',
	terms: 'termeni-si-conditii',
};

const problems = [];
let checked = 0;

const fail = (file, message) => problems.push(`${file}: ${message}`);
const stripTags = (s) => String(s).replace(/<[^>]*>/g, '').trim();

/** Load one seed wholesale, keyed by slug. Cached per seed name. */
const seedCache = {};
function loadSeed(name) {
	if (seedCache[name]) return seedCache[name];
	let raw;
	try {
		raw = execFileSync(
			'php',
			[path.join(ROOT, 'tools', 'dump-english-source.php'), ROOT, name],
			{ encoding: 'utf8', maxBuffer: 64 * 1024 * 1024 }
		);
	} catch (e) {
		console.error(`Could not read the ${name} seed. Is php on PATH?\n${e.message}`);
		process.exit(2);
	}
	const byslug = {};
	for (const entry of JSON.parse(raw)) byslug[entry.slug] = entry;
	seedCache[name] = byslug;
	return byslug;
}

/**
 * Walk the seed and demand the overlay match it, exactly as the importer does.
 */
function coverage(file, source, translation, trail) {
	const where = trail || 'page_sections';

	for (const [key, value] of Object.entries(source)) {
		const here = `${where}/${key}`;

		if (key === 'acf_fc_layout') {
			if (translation[key] !== value) {
				fail(file, `${where}: layout must be "${value}", found "${translation[key] ?? '(absent)'}"`);
			}
			continue;
		}

		if (value && typeof value === 'object') {
			let nested = translation[key];
			// Omitting a branch is legal — the overlay merge simply leaves the
			// English in place. Recursing into an empty object mirrors the
			// importer, and still reports any required copy underneath it.
			if (!nested || typeof nested !== 'object') nested = Array.isArray(value) ? [] : {};
			// A branch that IS supplied must line up, or the merge writes the
			// wrong entry over the wrong one.
			if (Array.isArray(value) && Array.isArray(translation[key])
				&& translation[key].length !== value.length) {
				fail(file, `${here}: expected ${value.length} entries, found ${translation[key].length}`);
				continue;
			}
			coverage(file, value, nested, here);
			continue;
		}

		if (TEXT_FIELDS.has(key) && stripTags(value) !== '') {
			if (!(key in translation) || stripTags(translation[key]) === '') {
				fail(file, `${here}: Romanian copy is missing`);
			}
		}
	}
}

function validateFile(folder, name, seedName) {
	const rel = `${folder}/${name}`;
	const roPath = path.join(BASE, TARGET, folder, name);
	const sourceSlug = name.replace(/\.json$/, '');

	if (!fs.existsSync(roPath)) {
		fail(rel, 'missing — no Romanian file for this English source');
		return;
	}
	checked += 1;

	let ro;
	try {
		ro = JSON.parse(fs.readFileSync(roPath, 'utf8'));
	} catch (e) {
		fail(rel, `invalid JSON: ${e.message}`);
		return;
	}

	if (ro.source_slug !== sourceSlug) {
		fail(rel, `source_slug must be "${sourceSlug}", found "${ro.source_slug}"`);
	}

	const expectedSlug = folder === 'doctors' ? sourceSlug : RO_SLUGS[sourceSlug];
	if (!expectedSlug) {
		fail(rel, `no Romanian slug is defined for "${sourceSlug}" — check the contract`);
	} else if (ro.slug !== expectedSlug) {
		fail(rel, `slug must be "${expectedSlug}", found "${ro.slug}"`);
	}

	const displayField = folder === 'doctors' ? 'name' : 'title';
	if (!ro[displayField] || typeof ro[displayField] !== 'string') {
		fail(rel, `${displayField} is missing or not a string`);
	}

	if (seedName) {
		const seed = loadSeed(seedName)[sourceSlug];
		if (!seed) {
			fail(rel, `no English seed entry found for "${sourceSlug}"`);
		} else if (folder === 'doctors') {
			// The doctor importer compares credential counts explicitly.
			const want = (seed.credentials || []).length;
			const got = (ro.credentials || []).length;
			if (want !== got) {
				fail(rel, `credentials must have ${want} entries, found ${got}`);
			}
			for (const field of ['position', 'bio']) {
				if (stripTags(seed[field] || '') !== '' && stripTags(ro[field] || '') === '') {
					fail(rel, `${field} is missing`);
				}
			}
		} else {
			coverage(rel, seed.sections || {}, ro.sections || [], 'page_sections');
		}
	} else if (stripTags(ro.content || '') === '') {
		fail(rel, 'content is missing');
	}

	const body = JSON.stringify(ro);

	// Internal links carry a language prefix. A /pt/ path left in a Romanian
	// file points the reader at the Portuguese site.
	const stray = body.match(/\/(en|tr|fr|it|es|pl|pt)\//g);
	if (stray) {
		fail(rel, `internal links still point at another language: ${[...new Set(stray)].join(', ')}`);
	}

	// Romanian uses no ã or õ, so either one is a passage left in Portuguese.
	// Six candidate `footer` fields shipped that way once: the coverage check
	// could not see it, because Portuguese is every bit as non-empty as
	// Romanian, and comparing against English misses it too.
	const foreign = [...new Set((body.match(/[^\s"\\]*[ãõ][^\s"\\]*/g) || []))];
	if (foreign.length) {
		fail(rel, `Portuguese text left untranslated: ${foreign.slice(0, 4).join(', ')}`);
	}
}

/** The file list comes from the seeds, so a folder cannot silently go short. */
function filesFor(folder) {
	const seedName = SEED_OF[folder];
	if (folder === 'legal') {
		// Legal pages live in the page seed but carry prose, not sections.
		return ['privacy-policy', 'terms', 'kvkk-disclosure', 'cookie-policy'];
	}
	const seed = loadSeed(seedName);
	if (folder === 'pages') {
		return Object.keys(RO_SLUGS).filter(
			(s) => seed[s] && !['privacy-policy', 'terms', 'kvkk-disclosure', 'cookie-policy'].includes(s)
		);
	}
	if (folder === 'doctors') return Object.keys(seed);
	// Treatment folders: whichever treatments belong to that category.
	const category = {
		'hair-transplant': 'Hair Transplant',
		'plastic-surgery': 'Plastic Surgery',
		'dental-treatment': 'Dental Treatment',
	}[folder];
	return Object.keys(seed).filter(
		(s) => seed[s].category === category && s in RO_SLUGS
	);
}

const folders = process.argv.slice(2).length
	? process.argv.slice(2)
	: Object.keys(SEED_OF);

folders.forEach((folder) => {
	if (!(folder in SEED_OF)) {
		problems.push(`${folder}: not a known overlay folder`);
		return;
	}
	filesFor(folder).forEach((slug) => validateFile(folder, `${slug}.json`, SEED_OF[folder]));
});

console.log(`checked ${checked} file(s) in: ${folders.join(', ')}`);
if (problems.length) {
	console.error(`\n${problems.length} problem(s):\n`);
	problems.forEach((p) => console.error('  ' + p));
	process.exit(1);
}
console.log('all clean');
