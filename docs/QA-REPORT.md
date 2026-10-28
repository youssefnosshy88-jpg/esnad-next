# QA report

## Automated checks run on staging
- 59 pages/posts (AR) and 23 EN pages: no raw shortcodes, exactly one H1, no mojibake, no stray builder tokens (checked after the fix).
- Tablet 768 px: no horizontal overflow on the English home.
- Desktop 1440 px: no horizontal overflow after the curtain removal.

## Manual checks
Desktop and mobile samples of home, about, executive, disclosure, careers, Sanad360, investment, blog, article and a service page; EN home and about.

## Not yet done
- Core Web Vitals (LCP ≤ 2.5 s, CLS ≤ 0.1, INP ≤ 200 ms): not measured; the shared staging host is slow and intermittently refuses connections, so measure on production hosting.
- WCAG 2.2 AA audit: not run. Known good: skip link, focus-visible styles, labelled forms, reduced-motion honoured. Contrast of gold on light uses the darker gold token.
- Widths 1024 and 1920 px; mobile review of every page; English pages beyond home/about.
- Cross-browser (only the in-app Chromium).

## Open defects
- Latest local changes not deployed (SSH blocked).
- Disclosure PDFs: wrong target for 2020 and 2022–2025 in source data.
- Rank Math prints no head tags on staging; theme fallback in use.
