# Cloudflare rules — form spam

The theme handles spam in PHP (`inc/lead-guard.php`): a scored submission is
still stored and still emailed to `lead@estecapelli.com`, it just never gets the
BCC that would create a Kommo record. That is the safety net.

These edge rules are the layer in front of it. They stop a flood before it ever
reaches PHP, which is what keeps the site fast during an attack and keeps the AI
analysis endpoint from burning Anthropic credit on bots.

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

## The one thing to keep in sync

`estecapelli_client_ip()` in `inc/leads.php` already prefers the
`CF-Connecting-IP` header, so every per-IP limit in PHP counts the real visitor
rather than a Cloudflare edge node. If the site ever moves off Cloudflare that
header disappears and the code falls back to `REMOTE_ADDR` on its own — nothing
to change.
