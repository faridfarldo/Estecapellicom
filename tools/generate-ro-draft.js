#!/usr/bin/env node
/** Temporary generator for Romanian drafts, always translating English seeds. */

const fs = require('fs');
const path = require('path');
const { execFileSync } = require('child_process');

const ROOT = path.resolve(__dirname, '..');
const PT = path.join(ROOT, 'inc', 'data', 'translations', 'pt');
const RO = path.join(ROOT, 'inc', 'data', 'translations', 'ro');

const SLUGS = {
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
  'privacy-policy': 'politica-de-confidentialitate',
  terms: 'termeni-si-conditii',
  'kvkk-disclosure': 'nota-de-informare-kvkk',
  'cookie-policy': 'politica-de-cookie-uri',
};

const FIXED_TITLES = {
  'privacy-policy': 'Politica de confidențialitate',
  terms: 'Termeni și condiții',
  'kvkk-disclosure': 'Notă de informare KVKK privind prelucrarea datelor',
  'cookie-policy': 'Politica de cookie-uri',
};

const TRANSLATABLE = new Set([
  'title', 'eyebrow', 'lead', 'body', 'label', 'question', 'answer', 'name',
  'position', 'bio', 'credentials', 'content', 'submit_label', 'value', 'time', 'cta',
]);

const jobs = [];
const cache = new Map();

function register(text) {
  if (!text.trim()) return { literal: text };
  if (text.length > 700) {
    const joined = [];
    let remaining = text;
    while (remaining.length > 700) {
      let cut = 700;
      while (cut > 400 && !/\s/.test(remaining[cut])) cut--;
      if (cut === 400) cut = 700;
      joined.push(register(remaining.slice(0, cut)));
      if (/\s/.test(remaining[cut] || '')) {
        joined.push({ literal: remaining[cut] });
        remaining = remaining.slice(cut + 1);
      } else {
        remaining = remaining.slice(cut);
      }
    }
    if (remaining) joined.push(register(remaining));
    return { joined };
  }
  if (!cache.has(text)) {
    cache.set(text, jobs.length);
    jobs.push(text);
  }
  return { translation: cache.get(text) };
}

function markText(text) {
  if (!text.includes('<')) return register(text);
  return {
    html: text.split(/(<[^>]+>)/g).map((part) =>
      part.startsWith('<') || !part.trim() ? { literal: part } : register(part)
    ),
  };
}

function sourceRecord(kind, slug) {
  return JSON.parse(execFileSync(
    'php',
    [path.join(ROOT, 'tools', 'export-ro-source.php'), kind, slug],
    { encoding: 'utf8' }
  ));
}

function doctorName(name) {
  return name.startsWith('Op. Dr. ') ? name.replace(/^Op\. Dr\. /, 'Dr. ') : name;
}

function project(ref, src, key, slug, folder) {
  if (Array.isArray(ref)) {
    return ref.map((value, index) => project(value, src[index], key, slug, folder));
  }
  if (ref && typeof ref === 'object') {
    const out = {};
    for (const childKey of Object.keys(ref)) {
      if (childKey === 'source_slug') out[childKey] = slug;
      else if (childKey === 'slug') out[childKey] = folder === 'doctors' ? slug : SLUGS[slug];
      else if (folder === 'legal' && childKey === 'title') out[childKey] = FIXED_TITLES[slug];
      else if (folder === 'doctors' && childKey === 'name') out[childKey] = doctorName(src[childKey]);
      else out[childKey] = project(ref[childKey], src[childKey], childKey, slug, folder);
    }
    return out;
  }
  if (typeof ref === 'string' && TRANSLATABLE.has(key)) {
    if (typeof src !== 'string') throw new Error(`Missing English text at ${folder}/${slug}:${key}`);
    return markText(src);
  }
  return ref;
}

let bingSession;
let bingRequest = 0;
const BROWSER_UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Edg/131.0.0.0';

async function getBingSession() {
  const response = await fetch('https://www.bing.com/translator?from=en&to=ro', {
    headers: { 'User-Agent': BROWSER_UA },
  });
  const html = await response.text();
  const abuse = html.match(/params_AbusePreventionHelper\s*=\s*\[(\d+),"([^"]+)/);
  const ig = html.match(/IG:"([^"]+)/);
  const iid = html.match(/data-iid="(translator\.\d+)/);
  if (!response.ok || !abuse || !ig || !iid) throw new Error('Could not start Bing translation session');
  bingSession = {
    key: abuse[1],
    token: abuse[2],
    ig: ig[1],
    iid: iid[1],
    cookie: response.headers.getSetCookie().map((value) => value.split(';', 1)[0]).join('; '),
  };
  bingRequest = 0;
}

async function bingTranslate(body) {
  if (!bingSession) await getBingSession();
  const url = new URL('https://www.bing.com/ttranslatev3');
  url.searchParams.set('isVertical', '1');
  url.searchParams.set('IG', bingSession.ig);
  url.searchParams.set('IID', `${bingSession.iid}.${++bingRequest}`);
  const form = new URLSearchParams({
    fromLang: 'en',
    text: body,
    to: 'ro',
    token: bingSession.token,
    key: bingSession.key,
  });

  let response;
  for (let attempt = 0; attempt < 5; attempt++) {
    response = await fetch(url, {
      method: 'POST',
      headers: {
        'User-Agent': BROWSER_UA,
        'Content-Type': 'application/x-www-form-urlencoded',
        Cookie: bingSession.cookie,
        Origin: 'https://www.bing.com',
        Referer: 'https://www.bing.com/translator?from=en&to=ro',
      },
      body: form,
    });
    if (response.ok) break;
    await new Promise((resolve) => setTimeout(resolve, 1000 * (attempt + 1)));
  }
  if (!response.ok) throw new Error(`Translation HTTP ${response.status}: ${(await response.text()).slice(0, 300)}`);

  const payload = await response.json();
  const translated = payload?.[0]?.translations?.[0]?.text;
  if (typeof translated !== 'string') throw new Error('Unexpected Bing translation response');
  return translated;
}

async function translateBatch(indexes) {
  const body = indexes.map((index) => `<<<ROSEP_${String(index).padStart(4, '0')}>>>\n${jobs[index]}`).join('\n');
  const translated = await bingTranslate(body);
  const parts = [...translated.matchAll(/<<<ROSEP_(\d{4})>>>\s*([\s\S]*?)(?=\n?<<<ROSEP_|$)/g)];
  if (parts.length === 0 && indexes.length === 1) {
    return [[indexes[0], translated.replace(/^<<<[^>]+>>>\s*/, '').trim()]];
  }
  if (parts.length !== indexes.length) {
    const recovered = [];
    for (const index of indexes) recovered.push([index, (await bingTranslate(jobs[index])).trim()]);
    return recovered;
  }
  return parts.map((match) => [Number(match[1]), match[2].trim()]);
}

function polishRomanian(text) {
  return text
    .replace(/Medic Sef/g, 'Medic-șef')
    .replace(/Medic Șef/g, 'Medic-șef')
    .replace(/medic șef/g, 'medic-șef')
    .replace(/medicul șef/g, 'medicul-șef')
    .replace(/zona donatorului/gi, (m) => m[0] === 'Z' ? 'Zona donatoare' : 'zona donatoare')
    .replace(/zona destinatară/gi, (m) => m[0] === 'Z' ? 'Zona receptoare' : 'zona receptoare')
    .replace(/linia părului/gi, (m) => m[0] === 'L' ? 'Linia frontală a părului' : 'linia frontală a părului')
    .replace(/îngrijire ulterioară/gi, (m) => m[0] === 'Î' ? 'Îngrijire post-operatorie' : 'îngrijire post-operatorie')
    .replace(/Turcia’s/g, 'Turciei')
    .replace(/Estecapelli's/g, 'Estecapelli');
}

function resolve(value, translations) {
  if (Array.isArray(value)) return value.map((item) => resolve(item, translations));
  if (value && typeof value === 'object') {
    if (Object.hasOwn(value, 'literal')) return value.literal;
    if (Object.hasOwn(value, 'translation')) return polishRomanian(translations[value.translation]);
    if (Object.hasOwn(value, 'joined')) return value.joined.map((item) => resolve(item, translations)).join('');
    if (Object.hasOwn(value, 'html')) {
      return value.html.map((item) => resolve(item, translations)).join('')
        .replace(/href="\/en\/kvkk-disclosure"/g, 'href="/ro/nota-de-informare-kvkk"')
        .replace(/href="\/en\/cookie-policy"/g, 'href="/ro/politica-de-cookie-uri"')
        .replace(/href="\/en\/privacy-policy"/g, 'href="/ro/politica-de-confidentialitate"')
        .replace(/href="\/en\/terms"/g, 'href="/ro/termeni-si-conditii"');
    }
    return Object.fromEntries(Object.entries(value).map(([k, v]) => [k, resolve(v, translations)]));
  }
  return value;
}

function addJsonGroup(group, drafts) {
  const folder = group;
  for (const name of fs.readdirSync(path.join(PT, folder)).filter((name) => name.endsWith('.json'))) {
    const slug = name.replace(/\.json$/, '');
    const kind = folder === 'doctors' ? 'doctor' : (folder === 'legal' ? 'page' : 'treatment');
    const ref = JSON.parse(fs.readFileSync(path.join(PT, folder, name), 'utf8'));
    const src = sourceRecord(kind, slug);
    drafts.push({
      path: path.relative(ROOT, path.join(RO, folder, name)).replace(/\\/g, '/'),
      value: project(ref, src, '', slug, folder),
      type: 'json',
    });
  }
}

function addBlogs(drafts) {
  const names = [
    'hair-transplant-for-hiv-positive-patients-in-turkey.html',
    'hair-transplant-for-hiv-positive-patients.html',
    'unshaven-hair-transplant.html',
    'unshaven-hair-transplant-for-women.html',
    'can-diabetic-patients-undergo-a-hair-transplant.html',
    'is-hair-transplant-a-painful-procedure.html',
    'will-my-hair-fall-out-again-after-a-hair-transplant.html',
    'hair-transplant-with-the-fue-vita-technique.html',
  ];
  for (const name of names) {
    drafts.push({
      path: `inc/data/blog/ro/${name}`,
      value: markText(fs.readFileSync(path.join(ROOT, 'inc', 'data', 'blog', name), 'utf8')),
      type: 'html',
    });
  }
}

function makePatch(files, translations) {
  let patch = '*** Begin Patch\n';
  for (const file of files) {
    let content = file.type === 'json'
      ? JSON.stringify(resolve(file.value, translations), null, 2) + '\n'
      : resolve(file.value, translations).replace(/\r\n/g, '\n');
    patch += `*** Add File: ${file.path}\n`;
    patch += content.split('\n').map((line) => `+${line}`).join('\n') + '\n';
  }
  return patch + '*** End Patch\n';
}

async function main() {
  const group = process.argv[2];
  const drafts = [];
  if (group === 'plastic-surgery') addJsonGroup(group, drafts);
  else if (group === 'supporting') ['dental-treatment', 'legal', 'doctors'].forEach((name) => addJsonGroup(name, drafts));
  else if (group === 'blog') addBlogs(drafts);
  else throw new Error('Use plastic-surgery, supporting, or blog');

  const translations = new Array(jobs.length);
  let batch = [];
  let length = 0;
  const batches = [];
  for (let index = 0; index < jobs.length; index++) {
    const extra = jobs[index].length + 24;
    if (batch.length && length + extra > 850) {
      batches.push(batch);
      batch = [];
      length = 0;
    }
    batch.push(index);
    length += extra;
  }
  if (batch.length) batches.push(batch);

  for (let index = 0; index < batches.length; index++) {
    for (const [job, translated] of await translateBatch(batches[index])) translations[job] = translated;
    await new Promise((resolve) => setTimeout(resolve, 250));
  }
  process.stdout.write(makePatch(drafts, translations));
}

main().catch((error) => {
  console.error(error.stack || error.message);
  process.exit(1);
});
