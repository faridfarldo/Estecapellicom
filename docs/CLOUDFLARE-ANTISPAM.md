# Cloudflare rules — form spam

The theme handles spam in PHP (`inc/lead-guard.php`): a scored submission is
still stored and still emailed to `lead@estecapelli.com`, it just never gets the
BCC that would create a Kommo record. That is the safety net.

These edge rules are the layer in front of it. They stop a flood before it ever
reaches PHP, which is what keeps the site fast during an attack and keeps the AI
analysis endpoint from burning Anthropic credit on bots.

Turnstile (section 5) sits between the two: it is configured at Cloudflare but
enforced in the theme, on every lead form.

**Apply these by hand in the Cloudflare dashboard for `estecapelli.com`.** None
of them can challenge a normal visitor reading pages — every rule is scoped to
the POST endpoints the forms use.

---

## Where these go

Sign in at **dash.cloudflare.com**, pick the account, then click the
**estecapelli.com** domain — every screen below is inside that domain, not in
the account-level menu. The left sidebar has a **Security** section:

| Rule | Sidebar path |
|---|---|
| 1 and 2 | Security → **Rate limiting rules** (older dashboards: Security → WAF → *Rate limiting rules* tab) |
| 3 | Security → **Custom rules** (older: Security → WAF → *Custom rules* tab) |
| 4 | Security → **Bots** |
| 5 | **Turnstile** (top-level sidebar item, not under Security) |
| Checking results | Security → **Events** (newer dashboards: Security → *Analytics*, "Events" tab) |

Cloudflare reorganised this sidebar during 2025, so if "WAF" is not where you
expect it, look for **Security rules** — the rule editors themselves are
identical either way.

In every rule editor, choose **Custom filter expression** and then the
**Edit expression** / `</>` link to paste the expression text as-is, rather than
building it from the dropdowns field by field.

> **If you can only create one rate limiting rule,** you are on the Free plan,
> which allows exactly that. Create **rule 1** and skip rule 2 — the analysis
> endpoint is already capped at 3/hour per IP in PHP, whereas nothing but rule 1
> stands in front of the lead forms. The Free plan also restricts the counting
> period and the action list; pick the closest available values (a 10-second
> period with a proportionally lower request count is fine) and leave the action
> on **Managed Challenge** if it is offered, **Block** if it is not.

---

## 1. Rate limit the form endpoints (the important one)

**Security → WAF → Rate limiting rules → Create rule**

| Field | Value |
|---|---|
| Rule name | `Lead form flood` |
| If incoming requests match | Custom filter expression (see below) |
| Expression | `(http.request.method eq "POST" and http.request.uri.path contains "/wp-admin/admin-ajax.php") or (http.request.method eq "POST" and http.request.uri.path contains "/wp-json/estecapelli/v1/")` |
| Rate | `5` requests per `10 seconds` |
| Counting characteristic | IP |
| Action | **Managed Challenge**, or **Block** where the plan offers no challenge |
| Duration | the shortest the plan allows |

Five posts in ten seconds is far above anyone filling in a consultation form —
one person sends one — and far below what the August run did. The Free plan
caps the counting period at ten seconds, which is why the rate reads this way
rather than as a per-minute figure.

Prefer Managed Challenge where it is available: a real person who somehow trips
it sees a Cloudflare interstitial and continues, instead of a dead form.

**Applied on the live site (Free plan) on 2026-08-14** with Block, since the
Free plan offers no challenge action here.

## 2. Rate limit the AI analysis endpoint separately

Same screen, second rule. This one costs real money per request.

**Not applied — the site is on the Free plan, which allows exactly one rate
limiting rule, and rule 1 is the one worth having.** Nothing but rule 1 stands
in front of the lead forms, whereas this endpoint is already capped at 3/hour
per IP in `estecapelli_hair_analyze()`. Add this if the plan is ever upgraded.

| Field | Value |
|---|---|
| Rule name | `Hair analysis abuse` |
| Expression | `http.request.method eq "POST" and http.request.uri.path contains "/wp-json/estecapelli/v1/analyze"` |
| Rate | `5` requests per `10 minutes` |
| Counting characteristic | IP |
| Action | **Block** |
| Duration | `1 hour` |

Block is safe here: PHP already limits this endpoint to 3/hour per IP, so
anything hitting five in ten minutes is not a patient.

## 3. Block script clients from posting forms

**Security → Custom rules → Create rule** (older dashboards: Security → WAF →
*Custom rules*). The Free plan allows five of these.

| Field | Value |
|---|---|
| Rule name | `Script clients may not post forms` |
| Expression | see below |
| Action | **Block** |

```
http.request.method eq "POST" and (http.request.uri.path contains "/wp-admin/admin-ajax.php" or http.request.uri.path contains "/wp-json/estecapelli/v1/") and (http.user_agent eq "" or http.user_agent contains "python" or http.user_agent contains "curl" or http.user_agent contains "Go-http-client" or http.user_agent contains "Scrapy" or http.user_agent contains "okhttp" or http.user_agent contains "libwww")
```

This matches on the client announcing itself as a script. A browser never sends
these user agents, and a bot that bothers to forge one has already been pushed
up a tier — which is the whole game at the edge.

An earlier version of this rule used `cf.client.bot or cf.threat_score gt 14`.
That was wrong for this site: `cf.client.bot` is true for *verified* bots such
as Googlebot, which are precisely the ones that should not be challenged, and it
says nothing at all about the unverified scripts that actually send this spam.
Bot Fight Mode (rule 4) is the free tier's answer to those; real bot scoring
needs a paid Bot Management plan.

**Applied on the live site on 2026-08-14.**

## 4. Turn on Bot Fight Mode

**Security → Bots → Bot Fight Mode: On** — **applied on 2026-08-14.**

Free, and it drops the crudest scripted clients before any rule is evaluated.
If the site is on a paid plan, use **Super Bot Fight Mode** and set *Definitely
automated* to **Block**, *Likely automated* to **Managed Challenge**.

## 5. Turnstile on the lead forms

Rules 1–4 judge the request. Turnstile judges the *browser* that sent it, which
is the one thing an edge rule cannot see — and it does so invisibly: the widget
solves itself, and a real visitor never sees a checkbox, a puzzle or a delay.

**Cloudflare → Turnstile → Add widget**

| Field | Value |
|---|---|
| Widget name | `estecapelli lead forms` |
| Hostnames | `estecapelli.com` (add `www.estecapelli.com` and the staging host if there is one) |
| Widget Mode | **Invisible** |
| Pre-clearance | Off |

That produces a **site key** and a **secret key**. Both go in `wp-config.php`,
above the `/* That's all, stop editing! */` line, exactly like the SMTP
credentials — **never** in the repo:

```php
define( 'ESTECAPELLI_TURNSTILE_SITE_KEY', '0x4AAAAAAA...' );  // public, printed in the page
define( 'ESTECAPELLI_TURNSTILE_SECRET',   '0x4AAAAAAA...' );  // private, server-side only
```

| Constant | Where it is used |
|---|---|
| `ESTECAPELLI_TURNSTILE_SITE_KEY` | `estecapelli_turnstile_field()` — the `data-sitekey` on the widget `<div>` inside every lead form |
| `ESTECAPELLI_TURNSTILE_SECRET` | `estecapelli_lead_turnstile_signal()` — the server-side POST to `challenges.cloudflare.com/turnstile/v0/siteverify` |

### What it covers

The widget is printed by `estecapelli_lead_antispam_fields()`, so it is on every
form that helper feeds: the popup, the contact page, the footer quick-form, the
in-page form section and the hair-analysis form. The classic POST path and the
AJAX path both carry the token, because the AJAX forms are submitted with
`new FormData( form )` and the widget writes its token into a hidden input
inside the form. Cloudflare's script is loaded only on pages that actually
printed a form, and is marked `data-nowprocket` so WP Rocket's delayed-JS pass
cannot hold it back past the visitor's first submit.

The AI photo widget is the exception: it posts JSON to the REST API and is never
shown a challenge, so it is never scored for missing one.

### What a failure does — and does not do

**It does not reject.** A failed or absent challenge is worth **4 points** in
`inc/lead-guard.php` (`failed Turnstile challenge` / `missing Turnstile
challenge`), against a quarantine threshold of 5. So on its own it never even
quarantines a lead, let alone loses one; combined with a second signal it holds
the lead back from the CRM, and one click in **wp-admin → Leads → Quarantined**
releases it.

Everything else fails open, deliberately:

| Situation | What happens |
|---|---|
| Constants missing or empty | The whole layer switches off — no script, no widget, no scoring |
| `challenges.cloudflare.com` unreachable or slow (5s timeout) | Logged, and the submission is scored as if Turnstile did not exist |
| Visitor has JavaScript off | Already charged 3 points for the missing interaction token; Turnstile does **not** charge again for the same fact |
| Visitor resubmits after a validation error | The verdict is cached per token for 10 minutes, so a single-use token is not re-verified into a `timeout-or-duplicate` failure |

That is the same rule the rest of the anti-spam code follows: a spam email costs
the clinic ten seconds, a lost enquiry costs it a patient.

**Not yet applied on the live site** — the widget has to be created and the two
constants added to `wp-config.php`. Until then the theme behaves exactly as it
did before.

---

## Why not just block the attacking IPs

The August run rotated addresses; by the time an IP is in a block list it is
finished with. Rate limiting by behaviour survives rotation, which is why it is
rule 1 and an IP list is not on this page at all.

## Checking it works

Cloudflare → **Security → Events**, filter by the rule name. Legitimate leads
should never appear there. If one does, loosen the rate on rule 1 rather than
deleting it — and check `wp-admin → Leads → Quarantined` at the same time, since
a lead held back in PHP is recoverable and one blocked at the edge is not.

For Turnstile, the widget's own **Analytics** tab shows solve/fail rates, and
every rejection is written to the PHP error log as
`[estecapelli] Turnstile rejected a submission (<error codes>) — scored, not
blocked.` A sudden run of `missing Turnstile challenge` on real leads means the
script is not loading (check WP Rocket and any script-blocking consent rules),
not that the site is under attack.

## The one thing to keep in sync

`estecapelli_client_ip()` in `inc/leads.php` already prefers the
`CF-Connecting-IP` header, so every per-IP limit in PHP counts the real visitor
rather than a Cloudflare edge node. If the site ever moves off Cloudflare that
header disappears and the code falls back to `REMOTE_ADDR` on its own — nothing
to change.
