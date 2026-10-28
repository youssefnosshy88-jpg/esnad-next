# Page redesign report

Base URL: https://green-gorilla-647751.hostingersite.com . Screenshots are not stored in the repo; the in-app browser captures were reviewed during the session. "Verified" below means seen rendered in the browser or checked by script on staging; nothing is marked complete without that.

Status vocabulary: Complete and tested / Implemented but needs review / In progress / Blocked.

## Homepage `/` and `/en/`
- Original: Elementor home with slider, boxes, counters.
- Antra inspiration: hero headline reveal (observed), dark story band with check list (observed), numbered service list (observed, hover-driven).
- Implemented: video hero with trust cards, ticker, about, milestones, services explorer, atlas with services inside the map and the Esnad mark at the hub, journey, pillars, needs, client marquee, compliance documents incl. FATCA, articles, FAQ, CTA.
- Files: `front-page.php`, `assets/css/main.css`, `assets/js/main.js`, `inc/ksa.php`, `inc/helpers.php`.
- Verification: desktop and mobile sampled; atlas 7/8 spacing and hub logo coded, **not yet deployed**.
- Status: Implemented but needs review.

## Services directory `/our-services-4/` and eight service pages
- Original: Elementor lists. Antra inspiration: numbered service list with image panel (services-01, home-5).
- Implemented: directory grid, per-service hero with outlined numeral, numbered chapter cards, CTA. Interactive list + panel: planned next.
- Status: Implemented but needs review.

## About: vision, mission, values `/about-esnad/`
- Implemented: two dark cards, values in three columns, static grid of all 62 client logos (no motion).
- Verification: logo wall measured (62 items) on staging before the latest CSS; new layout **not yet deployed**.
- Status: Implemented but needs review.

## Board, executive management, organisation chart, careers
- Implemented: people cards, year grid, org chart, careers (CV by e-mail line). Status: Implemented but needs review.

## Regulatory disclosure
- Implemented: year boxes and documents; PDF links per year need client input. Status: Implemented but needs review.

## Contact, complaints, consultation
- Implemented: info cards, social links, working forms (`esnad-core`), consultation tested end-to-end earlier; test lead deleted. Status: Complete and tested for the consultation form; others need review.

## Articles
- Implemented: list and single layout, section-aware menu state. Category filters not built. Status: Implemented but needs review.

## Other pages
Sanad360 (forms replaced, stray headings removed), investment landing pages (no registration forms until approved), archive/search/404. Status: Implemented but needs review.

## English
23 pages checked by script (no shortcodes, one H1, no mojibake). Visual review limited to home and about.
