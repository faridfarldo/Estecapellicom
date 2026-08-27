# Automatic language detection

Sends a visitor to the site in their own language without putting the six
non-English indexes at risk. All of it lives in `inc/geo-language.php`.

## What a visitor actually experiences

| Where they land | What happens |
|---|---|
| The homepage (`/` or `/en/`), first visit | 302 to their language's homepage, e.g. `/ro/` |
| The homepage, after they have ever picked a language | Nothing. Their choice wins forever. |
| Any other page, e.g. `/it/trapianto-di-capelli/` | No redirect. A dismissible bar offers the translated page as a link. |
| Any page, as a crawler | Nothing at all. No redirect, no bar, no markup. |
| Any page, logged in | Nothing. Editors previewing a language are never moved. |

## Why deep pages are never redirected

Google crawls this site almost entirely from US IP addresses. If `/it/`, `/pl/`
or `/ro/` URLs answered a US IP with a redirect to English, Googlebot would see
the translated pages vanish — and the ~590 indexed URLs across seven languages
are the whole asset. Redirecting only the default-language homepage keeps every
indexed URL answering exactly as it always did, and a *link* offered on a deep
page is a pattern Google is explicitly fine with.

## How the language is chosen

1. **`Accept-Language` first.** A Romanian living in Germany wants Romanian, and
   only their browser knows that. `q`-values are honoured; `pt-BR` and `pt-PT`
   both resolve to `pt`.
2. **Country second**, from Cloudflare's `CF-IPCountry` header. `XX`
   (anonymising proxy) and `T1` (Tor) are treated as "no country", not as a
   country.
3. **Otherwise English.** Nothing is guessed.

Belgium, Switzerland and Canada are deliberately **not** in the country map.
Guessing one language from those borders is worse than letting the browser
decide, and if it has no opinion either, English is the honest answer.

Brazil **is** mapped to `pt` knowingly: our Portuguese is the European variant,
but it serves a Brazilian visitor far better than English does.

To change the mapping, edit `estecapelli_geo_country_languages()`. It is written
as language ⇒ list of countries and flipped at runtime, so adding a country is
one entry in one place.

## Cookies

| Cookie | Set when | Effect |
|---|---|---|
| `este_lang` | Any language-switcher click, or the one-time homepage redirect | Detection never runs again |
| `este_lang_bar` | The suggestion bar is dismissed | The bar stays gone |

Both are strictly functional — a remembered language preference — so they sit
outside the consent banner's scope. Neither identifies anyone.

The switcher click is caught with one delegated listener on `a[hreflang]`, which
appears only on the header and footer switchers. The bar's own "view in …" link
carries `hreflang` too, so clicking it records the choice by the same path.

## Two things to get right on the server

**1. Cloudflare must be sending the country.** Turn on *IP Geolocation* under
Cloudflare → **Network** for `estecapelli.com`. Without it `CF-IPCountry` is
absent and detection quietly falls back to `Accept-Language` only — which still
works, just less often.

**2. The homepage must not be page-cached.** Its response now varies per
visitor, so it is sent with `Cache-Control: private, no-store`. WordPress HTML
is not cached by Cloudflare by default, so nothing to do today — but if a page
cache plugin or **Cloudflare APO** is ever enabled, exclude the homepage or
every visitor will be served the first visitor's language.

## Checking it works

The redirect is a 302 and only fires without the `este_lang` cookie:

```
curl -sI -H 'CF-IPCountry: RO' -H 'Accept-Language: ro-RO,ro;q=0.9' \
     -A 'Mozilla/5.0' https://estecapelli.com/en/ | grep -i '^location\|^HTTP'
```

Expect `302` to `/ro/`. Then confirm a crawler is left alone:

```
curl -sI -H 'CF-IPCountry: RO' -A 'Googlebot/2.1' https://estecapelli.com/en/ \
  | grep -i '^HTTP'
```

Expect `200`. Any redirect here is a bug and should be treated as urgent — it
is the failure mode this whole design exists to prevent.
