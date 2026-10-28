# Inner pages: plan and status

Inventory (staging, 2026-10-29): 38 Arabic pages, 34 English pages, 21 Arabic and 3 English articles = 96 URLs checked.
The content words are unchanged. Layout, grouping and ordering are done by theme code, so the same logic applies to Arabic and English and survives content edits.

## Groups

| # | Group | Pages | Approach | Code | Status |
|---|---|---|---|---|---|
| 1 | Core service pages | 8 AR + 8 EN | numbered chapter cards, outlined numeral in the hero | `functions.php` (`.svc-sec`), `page.php` | Implemented, checked |
| 2 | Services directory | 2 | numbered list with a panel that swaps (hover, focus, tap), clients grid after it | `template-parts/service-list.php`, `main.js` | Implemented, checked AR/EN, desktop and mobile |
| 3 | Service landing pages (Governance, Risk, Audit, Licensing, Compliance, Arrangement, Financial advisory, ...) | 9 AR + 9 EN | logo and scattered buttons removed, one action row, hero with picture, check-lists, partner wall, dark CTA with the form | `inc/landing.php` (landing mode) | Implemented, checked desktop and mobile |
| 4 | Investment, Tarh, Sanad360, feasibility landing | 11 | label/value pairs become fact tiles, heading + sentence runs become cards, dash paragraphs become lists, small labels become eyebrows, illustrations beside text | `inc/landing.php` (article mode) | Implemented, 57637 / 53239 / tarh checked visually |
| 5 | Thank-you pages | 3 | centred message card with a check mark, buttons closing the message | `inc/landing.php` (thanks mode) | Implemented, checked |
| 6 | Corporate: about, board, executive, organisation chart, disclosure, careers | AR + EN | dedicated components (vision cards, people cards, year grid) | `migrate-elementor.php` output + CSS | Implemented earlier, re-checked org chart and careers |
| 7 | Forms: consultation, contact, complaints | AR + EN | stepper form, contact layout, complaint form | `plugins/esnad-core` | Implemented earlier |
| 8 | Articles list | 2 | featured first card, 3-column grid, date, category chip, category filter buttons | `page.php`, `main.js` | Implemented, filter tested (5 of 21 shown for "Internal audit") |
| 9 | Articles | 24 | contents box from H2, dash paragraphs as lists, reading time, empty "share / related" headings removed, related articles | `inc/article.php`, `single.php` | Implemented, all 24 checked by script |

## Safety design
- `inc/landing.php` and `inc/article.php` are included through a guarded `try` in `functions.php`; a parse error there cannot take the site down.
- Every transformation runs inside `try/catch (Throwable)` and returns the original content on failure.
- The form markup is taken out before the DOM round trip and put back unchanged.

## Verified by script on all 96 URLs
No PHP warnings, no raw shortcodes, exactly one H1, no builder tokens, no empty H2.
Mobile (375 px): landing, investment page, blog list have no horizontal overflow.

## Not yet verified or open
- Contact, complaints and consultation were only spot-checked; EN variants of groups 3 to 5 were checked by script, not visually.
- The investment pages have no registration form (pending compliance approval).
- Some page pictures carry baked-in Arabic text from the original files.
- Accessibility audit and Core Web Vitals still to be measured on production hosting.
