#!/usr/bin/env node
/**
 * Structural validator for the Romanian translation overlays.
 *
 * The WordPress importer refuses anything whose shape does not match the
 * English seed, but that check only runs on a live site. This runs the same
 * idea offline, against the Portuguese overlay as the reference: every
 * language overlays the identical seed, so pt and ro must agree on every key,
 * every array length, and every acf_fc_layout in every position.
 *
 * Usage:  node tools/validate-ro-translations.js [folder ...]
 * Exit 0 = clean, 1 = problems found.
 */

const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const BASE = path.join(ROOT, 'inc', 'data', 'translations');
const REFERENCE = 'pt';
const TARGET = 'ro';

/**
 * Frozen Romanian slug contract. inc/indexed-urls.php is authoritative; this
 * mirrors it so the validator needs no PHP. Doctors keep their English slug
 * because a person's name is not translated.
 */
const RO_SLUGS = {
	// pages/
	'about-us': 'despre-noi',
	'before-after': 'inainte-dupa',
	'contact': 'contact',
	'dental-treatment': 'tratament-dentar',
	'hair-transplant': 'transplant-de-par',
	'medical-director': 'director-medical',
	'our-doctors': 'medicii-nostri',
	'our-team': 'echipa-noastra',
	'plastic-surgery': 'chirurgie-plastica',
	'post-hair-transplant-period': 'perioada-de-dupa-transplantul-de-par',
	'pre-hair-transplant-period': 'perioada-dinainte-de-transplantul-de-par',
	'tricholab': 'tricholab',

	// hair-transplant/
	'beard-transplant': 'transplant-de-barba',
	'dhi-hair-transplant': 'transplant-de-par-dhi',
	'exosome-fue-hair-transplant': 'transplant-de-par-exosome-fue',
	'eyebrow-transplant': 'transplant-de-sprancene',
	'female-hair-transplant': 'transplant-de-par-la-femei',
	'hair-mesotherapy': 'mezoterapie-capilara',
	'sapphire-fue-hair-transplant': 'transplant-de-par-fue-sapphire',
	'vita-treatment': 'tratament-vita',

	// plastic-surgery/
	'abdominoplasty-tummy-tuck': 'abdominoplastie',
	'bbl': 'bbl',
	'breast-aesthetics-breast-surgery': 'estetica-sanilor-chirurgia-sanilor',
	'face-and-neck-lift-surgery': 'lifting-facial-si-de-gat',
	'gynecomastia': 'ginecomastie',
	'liposuction': 'liposuctie',
	'obesity-surgeries-bariatric-surgery-and-gastric-balloon':
		'operatii-de-obezitate-chirurgie-bariatrica-si-balon-gastric',
	'rhinoplasty': 'rinoplastie',

	// dental-treatment/
	'dental-implant': 'implant-dentar',
	'hollywood-smile': 'zambet-de-hollywood',

	// legal/
	'cookie-policy': 'politica-de-cookie-uri',
	'kvkk-disclosure': 'nota-de-informare-kvkk',
	'privacy-policy': 'politica-de-confidentialitate',
	'terms': 'termeni-si-conditii',
};

/** Doctors keep the English slug: a person's name is not translated. */
const DOCTOR_SLUG_IS_SOURCE = true;

const problems = [];
let checked = 0;

function fail(file, message) {
	problems.push(`${file}: ${message}`);
}

/** Describe a value's shape without caring about the text inside it. */
function shape(value) {
	if (Array.isArray(value)) return 'array';
	if (value === null) return 'null';
	return typeof value;
}

/**
 * Walk reference and target together. Text may differ; structure may not.
 */
function compare(file, refValue, roValue, trail) {
	const where = trail || '(root)';

	if (shape(refValue) !== shape(roValue)) {
		fail(file, `${where}: expected ${shape(refValue)}, found ${shape(roValue)}`);
		return;
	}

	if (Array.isArray(refValue)) {
		if (refValue.length !== roValue.length) {
			fail(file, `${where}: expected ${refValue.length} entries, found ${roValue.length}`);
			return;
		}
		refValue.forEach((item, i) => compare(file, item, roValue[i], `${where}[${i}]`));
		return;
	}

	if (refValue && typeof refValue === 'object') {
		const refKeys = Object.keys(refValue).sort();
		const roKeys = Object.keys(roValue).sort();

		refKeys.filter((k) => !roKeys.includes(k))
			.forEach((k) => fail(file, `${where}: missing key "${k}"`));
		roKeys.filter((k) => !refKeys.includes(k))
			.forEach((k) => fail(file, `${where}: unexpected key "${k}"`));

		refKeys.filter((k) => roKeys.includes(k))
			.forEach((k) => compare(file, refValue[k], roValue[k], `${where}.${k}`));
		return;
	}

	// A layout selector is machinery, not copy. Translating one silently breaks
	// rendering, which is why acfml-layout-guard.php exists.
	if (trail && trail.endsWith('.acf_fc_layout') && refValue !== roValue) {
		fail(file, `${where}: layout must stay "${refValue}", found "${roValue}"`);
	}
}

function validateFile(folder, name) {
	const rel = `${folder}/${name}`;
	const refPath = path.join(BASE, REFERENCE, folder, name);
	const roPath = path.join(BASE, TARGET, folder, name);

	if (!fs.existsSync(roPath)) {
		fail(rel, 'missing — no Romanian file for this English source');
		return;
	}
	checked += 1;

	let ref;
	let ro;
	try {
		ref = JSON.parse(fs.readFileSync(refPath, 'utf8'));
	} catch (e) {
		fail(rel, `reference ${REFERENCE} file is unreadable: ${e.message}`);
		return;
	}
	try {
		ro = JSON.parse(fs.readFileSync(roPath, 'utf8'));
	} catch (e) {
		fail(rel, `invalid JSON: ${e.message}`);
		return;
	}

	const sourceSlug = name.replace(/\.json$/, '');
	if (ro.source_slug !== sourceSlug) {
		fail(rel, `source_slug must be "${sourceSlug}", found "${ro.source_slug}"`);
	}

	const expectedSlug = folder === 'doctors' && DOCTOR_SLUG_IS_SOURCE
		? sourceSlug
		: RO_SLUGS[sourceSlug];
	if (!expectedSlug) {
		fail(rel, `no Romanian slug is defined for "${sourceSlug}" — check the contract`);
	} else if (ro.slug !== expectedSlug) {
		fail(rel, `slug must be "${expectedSlug}", found "${ro.slug}"`);
	}

	if (!ro.title || typeof ro.title !== 'string') {
		fail(rel, 'title is missing or not a string');
	}

	compare(rel, ref, ro, '');

	// Internal links carry a language prefix. A /pt/ path left in a Romanian
	// file points the reader at the Portuguese site.
	const body = JSON.stringify(ro);
	const strayPrefix = body.match(/\/(en|tr|fr|it|es|pl|pt)\//g);
	if (strayPrefix) {
		const unique = [...new Set(strayPrefix)].join(', ');
		fail(rel, `internal links still point at another language: ${unique}`);
	}
}

const folders = process.argv.slice(2).length
	? process.argv.slice(2)
	: fs.readdirSync(path.join(BASE, REFERENCE)).filter(
		(f) => fs.statSync(path.join(BASE, REFERENCE, f)).isDirectory()
	);

folders.forEach((folder) => {
	const refDir = path.join(BASE, REFERENCE, folder);
	if (!fs.existsSync(refDir)) {
		problems.push(`${folder}: no such folder under ${REFERENCE}/`);
		return;
	}
	fs.readdirSync(refDir)
		.filter((n) => n.endsWith('.json'))
		.forEach((name) => validateFile(folder, name));
});

console.log(`checked ${checked} file(s) in: ${folders.join(', ')}`);
if (problems.length) {
	console.error(`\n${problems.length} problem(s):\n`);
	problems.forEach((p) => console.error('  ' + p));
	process.exit(1);
}
console.log('all clean');
