# GA4 measurement — configuration guide

The theme now emits every measurable interaction to `window.dataLayer`. This
document is the other half: what to build in Google Tag Manager and Google
Analytics 4 so those events become reports.

Container: `GTM-WVPFK35` (set in `functions.php`, `ESTECAPELLI_GTM_ID`).

---

## 1. How it fits together

```
inc/analytics.php ──► consent defaults + page context, printed above the
                      GTM snippet in <head>
assets/js/consent.js ► the banner; writes the choice, calls gtag('consent','update')
assets/js/analytics.js ► every event, pushed to dataLayer
       ▲
       └── main.js, footer-lead.js, hair-widget/widget.js dispatch
           `estecapelli:*` DOM events; only analytics.js knows GA4 exists
```

Two rules this design depends on:

1. **The theme names the events; GTM only maps them.** No GTM trigger should
   ever match a CSS class or a URL pattern for something the theme already
   reports. If a button moves, tracking must not care.
2. **Every push resets the full parameter vocabulary.** GTM keeps one merged
   dataLayer model, so a parameter set by one event stays readable by the next.
   `analytics.js` clears all of them on every push, which is what makes the
   single generic tag in §4 safe.

---

## 2. Page context (available on every event)

Pushed once per page, before the container loads, so it is already present for
the first `page_view`.

| Variable | Example | Notes |
|---|---|---|
| `page_type` | `treatment` | home, treatment, treatment_category, doctor, doctors, blog_post, blog_index, contact, before_after, legal, page, search, not_found |
| `page_key` | `sapphire-fue` | **English** slug, identical across all 7 languages |
| `page_language` | `fr` | en, tr, fr, it, es, pl, pt |
| `content_group` | `Treatments` | GA4 reserved param — fills the built-in Content group dimension |
| `user_type` | `visitor` \| `staff` | `staff` = logged in |
| `treatment_category` | `hair-transplant` | English term slug; treatment + result pages |
| `treatment_name` | `Sapphire FUE` | English title |
| `doctor_name` | `Binnur Kutlar` | doctor pages |
| `blog_category` | `hair-transplant` | blog posts |
| `search_term` | | search results only |
| `not_found_path` | `/pl/przed-po/` | 404s only — the path that was actually requested |

> **Why `page_key` matters.** Without it, one treatment page is seven unrelated
> rows in every report (`/en/…/sapphire-fue`, `/fr/…/fue-saphir`, …) and no
> page can be compared to any other. Group by `page_key`, break down by
> `page_language`.

---

## 3. Event reference

★ = mark as a **Key event** in GA4.

### Conversions

| Event | Fires when | Parameters |
|---|---|---|
| ★ `generate_lead` | Any of the six lead forms succeeds | `form_location`, `lead_treatment`, `lead_language` |
| ★ `whatsapp_click` | A `wa.me` link is clicked | `link_location`, `message_length` (1 = prefilled) |
| ★ `whatsapp_chat_send` | Visitor confirms a written message and is handed to WhatsApp | `message_length` |
| ★ `phone_click` | A `tel:` link is clicked | `link_location`, `link_url`, `link_text` |
| ★ `ai_analysis_complete` | The AI returns an estimate | `norwood_stage`, `graft_estimate` |

`form_location` values: `popup`, `footer`, `footer_nojs`, `contact_page`,
`treatment_section`, `hair_lab`, `ai_widget`.

### Funnel and engagement

| Event | Fires when | Parameters |
|---|---|---|
| `cta_click` | Any `.btn` is clicked | `cta_text`, `cta_location`, `cta_destination` |
| `lead_form_open` | The consultation popup opens | `form_location`, `cta_text` |
| `form_start` | First field touched in a lead form | `form_location` |
| `form_error` | A submit is rejected | `form_location`, `error_message` |
| `whatsapp_chat_open` | The chat overlay opens | `link_location` |
| `email_click` | A `mailto:` link is clicked | `link_location`, `link_url` |
| `ai_analysis_start` | The photo wizard mounts | `analysis_mode` |
| `ai_analysis_contact` | Contact details accepted, before any photo | `form_location` |
| `ai_photo_captured` | Each of the 4 poses is captured | `photo_step`, `capture_method` (auto/manual/upload), `step_index` |
| `ai_analysis_error` | Capture, upload or analysis fails | `error_message` |
| `hair_lab_select` | Photo route vs self-assessment chosen | `analysis_mode` |
| `self_assessment_zones` | A scalp zone is toggled | `zone_count` |
| `language_switch` | Language menu used | `from_language`, `to_language` |
| `story_open` | A patient story is selected | `story_name` |
| `video_start` | A patient-story video opens | `video_title`, `video_provider` |
| `before_after_navigate` | Gallery arrows used | `gallery_name`, `slide_index` (running depth) |
| `faq_open` | An FAQ item is expanded | `faq_question` |
| `tab_select` | Homepage service tabs | `tab_name`, `tab_group` |
| `toc_click` | Table-of-contents link | `link_text` |
| `scroll_depth` | 25 / 50 / 75 / 90 % | `percent` |
| `consent_update` | Banner choice made | `consent_analytics`, `consent_marketing`, `consent_method` |

**The funnel worth watching most:**
`ai_analysis_start → ai_analysis_contact → ai_photo_captured ×4 → ai_analysis_complete → generate_lead`.
Four camera steps is a lot to ask; `photo_step` shows exactly which pose loses
people, and `capture_method` shows whether the automatic face gate is working
or everyone is falling back to the shutter.

---

## 4. Google Tag Manager

### 4.1 Variables

Create a **Data Layer Variable** (version 2, default empty) for each name in
§2 and §3. Name them `DLV - <name>`, e.g. `DLV - form_location`.

Then create one **Google Tag: Event Settings** variable named
`GA4 - Shared params` holding the page context, so it never has to be repeated:

| Parameter | Value |
|---|---|
| `page_type` | `{{DLV - page_type}}` |
| `page_key` | `{{DLV - page_key}}` |
| `page_language` | `{{DLV - page_language}}` |
| `content_group` | `{{DLV - content_group}}` |
| `user_type` | `{{DLV - user_type}}` |
| `treatment_category` | `{{DLV - treatment_category}}` |
| `treatment_name` | `{{DLV - treatment_name}}` |
| `doctor_name` | `{{DLV - doctor_name}}` |
| `blog_category` | `{{DLV - blog_category}}` |

Attach it to the existing Google tag (GA4 config) as its Event Settings
variable, so `page_view` carries the context too.

### 4.2 Triggers

**`CE - theme events`** — Custom Event, "use regex matching", event name:

```
^(cta_click|lead_form_open|form_start|generate_lead|form_error|whatsapp_click|whatsapp_chat_open|whatsapp_chat_send|phone_click|email_click|ai_analysis_start|ai_analysis_contact|ai_photo_captured|ai_analysis_complete|ai_analysis_error|hair_lab_select|self_assessment_zones|language_switch|story_open|video_start|video_complete|before_after_navigate|faq_open|tab_select|toc_click|scroll_depth|consent_update)$
```

**`Exception - staff`** — Custom Event, event name matches regex `.*`, with the
condition `{{DLV - user_type}}` **equals** `staff`. Add it as an *exception* on
both the Google tag and the tag below, so staff browsing the live site never
enters the funnels.

> Testing as an administrator: log out or use a private window. In GTM Preview
> the tags will still appear, marked as blocked by this exception — which is
> itself the confirmation that it works.

### 4.3 The tag

One **GA4 Event** tag covers everything:

- **Name:** `GA4 - Theme events`
- **Event Name:** `{{Event}}` (the built-in variable — it holds the pushed name)
- **Event Settings Variable:** `GA4 - Shared params`
- **Event Parameters:** add a row for every parameter in §3, each mapped to its
  `DLV - …` variable.
- **Trigger:** `CE - theme events`
- **Exception:** `Exception - staff`

This is safe *only* because `analytics.js` clears unused parameters on every
push; GA4 drops parameters whose value is undefined, so each event sends just
its own.

If a separate tag is ever wanted for one event (for example to give
`generate_lead` a monetary value), add it with its own Custom Event trigger and
exclude that name from the regex above so it does not fire twice.

---

## 5. Google Analytics 4

### 5.1 Custom definitions

**Admin → Custom definitions → Custom dimensions**, all **event-scoped**.
`content_group` is built in and needs no registration.

```
page_type            page_key            page_language       user_type
treatment_category   treatment_name      doctor_name         blog_category
form_location        lead_treatment      lead_language
cta_text             cta_location        link_location
analysis_mode        photo_step          capture_method      norwood_stage
error_message        from_language       to_language
tab_name             story_name          video_title         gallery_name
faq_question         consent_analytics   consent_marketing
```

Useful **custom metrics** (event-scoped, Standard unit): `graft_estimate`,
`step_index`, `percent`, `zone_count`.

> Custom dimensions are **not retroactive** — they only populate from the
> moment they are registered. Do this before, or on, the day the container goes
> live, not after the first month of data.

### 5.2 Key events

Mark as key events: `generate_lead`, `whatsapp_chat_send`, `phone_click`,
`ai_analysis_complete`, `whatsapp_click`.

`whatsapp_click` is the loosest of the five — it counts intent, not contact.
Keep it separate from `whatsapp_chat_send` in reporting rather than summing
them.

### 5.3 Enhanced measurement

**Admin → Data streams → the web stream → Enhanced measurement:**

| Setting | Do |
|---|---|
| Page views | Keep on |
| Scrolls | **Turn off** — duplicates our `scroll_depth` at 90% and gives nothing at 25/50/75 |
| Outbound clicks | Keep on |
| Site search | Keep on |
| Video engagement | Keep off — the patient-story embeds are reported by the theme |
| File downloads | Keep on |
| Form interactions | **Turn off** — it fires on forms our own `form_start` / `generate_lead` already cover, with worse labels |

---

## 6. Consent Mode v2

Defaults are set inline in `<head>`, above the container, by
`estecapelli_analytics_bootstrap()`:

| Signal | Default |
|---|---|
| `analytics_storage`, `ad_storage`, `ad_user_data`, `ad_personalization`, `personalization_storage` | `denied` |
| `functionality_storage`, `security_storage` | `granted` |

`ads_data_redaction` and `url_passthrough` are on; `wait_for_update` is 500 ms
so a returning visitor's stored choice lands before the first hit.

**Denied everywhere, not only in the EEA.** Five of the seven languages are EU
markets and Turkish traffic is covered by KVKK, so a region-scoped default
would be the exception rather than the rule. To narrow it later, filter
`estecapelli_consent_defaults` and add a `region` key — see the docblock in
`inc/analytics.php`.

In GTM, under **Admin → Container Settings**, enable *Consent Overview*, then
confirm every Google tag requires `analytics_storage`. Nothing else in the
container may fire before consent.

### What this changes in the reports

This is **advanced** consent mode: Google tags still load and send *cookieless*
pings while consent is denied, and full hits once it is granted. That is the
mode Google's behavioural modelling needs, and it is what Consent Mode v2 asks
for — but expect the numbers to move when it goes live:

- Sessions and users drop relative to an unconsented setup, because anyone who
  rejects or ignores the banner is counted without an identifier.
- GA4 backfills part of the gap with modelled data, but only once the property
  has enough consented traffic (Google's threshold is roughly 1,000 events a
  day for seven days). Until then, treat absolute totals as a floor.
- **Trends and ratios stay honest** — conversion rate per `form_location`, or
  `page_key` against `page_language` — so read those, not raw totals, for the
  first few weeks.

Set a clear "before" marker: note the date the banner goes live, and don't
compare across it.

The choice is stored in the `estecapelli_consent` cookie as
`1|analytics:1|marketing:0`. Bump `ESTECAPELLI_CONSENT_VERSION` to re-ask
everyone — required if the set of cookies or purposes materially changes.

Withdrawal: the footer's **Cookie settings** button (`[data-consent-open]`)
reopens the banner pre-filled, on every page.

---

## 7. QA checklist

Run in GTM Preview, logged out, in a fresh private window.

- [ ] Before any click: `gtag('consent','default')` is the **first** Google
      call, and the GA4 tag reports `analytics_storage: denied`.
- [ ] Banner appears. **Reject all** → `consent_update` with
      `consent_analytics: denied`; no GA4 hit is sent.
- [ ] Reload → banner does not reappear, consent stays denied.
- [ ] Clear the cookie, **Accept all** → `consent_update` granted, `page_view`
      fires with `page_type`, `page_key`, `page_language`, `content_group`.
- [ ] Open a French treatment page: `page_key` is the **English** slug and
      `page_language` is `fr`.
- [ ] Footer form → `form_start`, then `generate_lead` with
      `form_location: footer`.
- [ ] Contact page form (a real POST + redirect) → `generate_lead` with
      `form_location: contact_page`. **Refresh the `?sent=1` URL — no second
      `generate_lead`.**
- [ ] Popup CTA → `cta_click` then `lead_form_open`; submit → `generate_lead`
      with `form_location: popup`.
- [ ] Floating WhatsApp → `whatsapp_chat_open`; write a message and confirm →
      `whatsapp_chat_send`. Footer WhatsApp link → `whatsapp_click`.
- [ ] A `tel:` link → `phone_click` with the right `link_location`.
- [ ] AI widget: `ai_analysis_start` → `ai_analysis_contact` →
      `ai_photo_captured` ×4 with distinct `photo_step` →
      `ai_analysis_complete` → `generate_lead` (`ai_widget`).
- [ ] **Parameter bleed:** click a header CTA, then submit the footer form. The
      `generate_lead` must have **no** `cta_location`. This is the single most
      important check — if it fails, the reset list in `analytics.js` is out of
      date.
- [ ] Log in as an administrator: `user_type` is `staff` and tags are blocked.
- [ ] A 404 URL carries `not_found_path`.

---

## 8. When adding a new event

1. Dispatch `estecapelli:<name>` from the controller that owns the state, or
   add a delegated listener in `analytics.js`.
2. Map it to a GA4 event in `analytics.js`.
3. **Add every new parameter to the `PARAMS` array at the top of
   `analytics.js`.** A parameter missing from that list will leak into
   unrelated events.
4. Add the event name to the `CE - theme events` regex, the parameter to the
   tag, and register the custom dimension in GA4.
