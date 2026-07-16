---
name: live-url-contract
description: All multilingual internal links must use the exact live indexed URL list
metadata:
  type: feedback
---

Every multilingual internal link — navbar, mega menu, logo, CTA, footer, card,
breadcrumb, related content and importer slug — must be built from the exact
URLs in `tools/live-urls-all.txt`. Never translate, guess, prettify or normalize
a slug independently.

Use the English route only as a stable lookup key into the indexed route
contract in `inc/indexed-urls.php`; the resulting target-language path must
match `tools/live-urls-all.txt` exactly. Before finishing any navigation or
link-building change, compare every generated target URL against that file.

Bare language roots are not valid substitutes unless they are explicitly in
the live list. For example, the Italian homepage is `/it/home`, not `/it/`.
