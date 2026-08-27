# Automatic language detection

Sends a visitor to the site in their own language without putting the seven
non-English indexes at risk. All of it lives in `inc/geo-language.php`.

## What a visitor actually experiences

| Where they land | What happens |
|---|---|
| The homepage (`/` or `/en/`), first visit | Sent to their language's homepage, e.g. `/ro/` |
| The homepage, after they have ever picked a language | Nothing. Their choice wins forever. |
| Any other page, e.g. `/it/trapianto-di-capelli/` | No redirect. A dismissible bar offers the translated page as a link. |
| Any page, as a crawler | Nothing at all. The route answers `{"mode":"none"}`. |
| Any page, logged in | Nothing. Editors previewing a language are never moved. |

## Why deep pages are never redirected

Google crawls this site almost entirely from US IP addresses. If `/it/`, `/pl/`
or `/ro/` URLs answered a US IP with a redirect to English, Googlebot would see
the translated pages vanish — and the ~590 indexed URLs across eight languages
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

## How it survives WP Rocket

WP Rocket serves cached pages from `advanced-cache.php`, long before
`template_redirect` or `wp_footer` run. So nothing about the visitor is decided
in the page. The split is:

| Piece | Cached? | Why it is safe |
|---|---|---|
| The inline script in `<head>` | Yes | Byte-identical for every visitor |
| The empty, hidden bar skeleton in the footer | Yes | Carries no text, no link, no language |
| `GET /wp-json/estecapelli/v1/language-hint?path=…` | **No** | Sent `no-store, private`; WP Rocket does not cache REST |

The page asks the route, then redirects itself (homepage) or fills in the
skeleton (everywhere else). **Nothing needs excluding from the page cache**, and
the homepage stays fully cached.

The skeleton exists for one reason: WP Rocket's *Remove Unused CSS* strips any
selector it cannot find in the cached HTML, so a bar built entirely in JS would
arrive unstyled. Printing it hidden keeps every `.lang-suggest*` class visible
to that pass.

## Three settings to get right

**1. Cloudflare → Network → IP Geolocation: on.** Without it `CF-IPCountry` is
absent and detection quietly falls back to `Accept-Language` only — which still
works, just less often.

**2. WP Rocket → File Optimization → "Delay JavaScript execution".** If this is
on, add an exclusion for:

```
estecapelli-language-hint
```

Otherwise the script waits for the visitor's first interaction — which is
exactly when a homepage redirect is no longer wanted. (Cloudflare Rocket Loader
is already handled: the tag carries `data-cfasync="false"`.)

**3. WP Rocket → "Remove Unused CSS".** If it ever strips the bar's styles
anyway, add to the safelist:

```
.lang-suggest
```

Nothing here needs a cache exclusion, and clearing the WP Rocket cache is not
required after changing the country map — the map is only ever read by the
uncached route.

## Checking it works

The page itself never redirects, so test the route directly. A Romanian visitor
on the English homepage:

```
curl -s -H 'CF-IPCountry: RO' -H 'Accept-Language: ro-RO,ro;q=0.9' \
     -A 'Mozilla/5.0' \
     'https://estecapelli.com/wp-json/estecapelli/v1/language-hint?path=/en/'
```

Expect `{"mode":"redirect","language":"ro","url":".../ro/"}`.

The same visitor deep in the Italian site — a link, never a move:

```
curl -s -H 'CF-IPCountry: RO' -A 'Mozilla/5.0' \
     'https://estecapelli.com/wp-json/estecapelli/v1/language-hint?path=/it/trapianto-di-capelli/'
```

Expect `{"mode":"suggest",…}`.

Now the one that matters most — a crawler must always get nothing:

```
curl -s -H 'CF-IPCountry: RO' -A 'Googlebot/2.1' \
     'https://estecapelli.com/wp-json/estecapelli/v1/language-hint?path=/en/'
```

Expect `{"mode":"none"}`. Anything else is a bug and should be treated as
urgent: it is the failure mode this whole design exists to prevent.

Finally, confirm the page itself is boring for everyone:

```
curl -sI -H 'CF-IPCountry: RO' -A 'Mozilla/5.0' https://estecapelli.com/en/ \
  | grep -i '^HTTP\|^location'
```

Expect `200` and no `Location` — with or without WP Rocket serving it.
