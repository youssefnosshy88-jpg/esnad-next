# Esnad design system

## Principles
Institutional, precise, minimal. Motifs are financial (bars, trend line, axis ticks, numerals), never ornamental stars or tiled lattices. Gold only for accents, active states and primary actions.

## Colour
| Token | Value | Use |
|---|---|---|
| `--esnad-dark` | #222826 | dark sections, text on light |
| `--esnad-green` | #415247 | gradients, headings accents |
| `--esnad-gold` | #C7901D | accents, active, CTA |
| `--esnad-gold-dark` | #7F570E | gold text on light and on sand (AA) |
| `--esnad-light` / `--esnad-ivory` | #EFE8D8 / #E6DDC8 | warm sand page background and alternate sections (cards stay white) |

## Typography
Tajawal, self-hosted (`assets/fonts`, weights 400/500/700, Arabic and Latin subsets, `font-display:swap`). Only 400 and 700 are preloaded. Body 17.5 px / 1.75.

English pages use Inter (self-hosted `inter-latin.woff2`, variable 400-700, about 48 KB) with Tajawal as the Arabic fallback; body 16.5 px, headings with slight negative tracking. American spelling is the standard (organization, specialized, analyze); the company name in English is always "Esnad Advisory Company"; dates are written "1 September 2019" next to the Hijri date.

## Services display
Services appear as cards (picture, number, title, summary, "Read more") on the home page and the services directory. The numbered list with a swapping panel (`template-parts/service-list.php`) is kept in the repository but not used, by the client's choice.

## Layout
RTL-first with logical properties. Container width token `--container-width`. Alternating dark/light sections.

## Motion
- Reveal: children of a block fade up (`fadeup`, 22 px, 0.75 s, 70 ms stagger) once, via IntersectionObserver. No curtains, no scroll hijacking.
- Hover: small lift or a 3 px gold marker; no heavy shadows.
- Everything is disabled under `prefers-reduced-motion`.
- Hero video loads after `load` and is skipped for reduced motion or save-data.

## Components
Header with mega panel (intro card + numbered services), cards (service, article, doc, info), numbered chapters (`.svc-sec`), vision/values cards, static client grid (inside content) and marquee (homepage only), atlas with Esnad-mark hub, journey, FAQ (`<details>`), forms (`esnad-core`), CTA band, footer.

## Decorative language
Bars glyph (`star.svg`, used as bullet), rising bars + trend (`chart.svg`) for page-hero and menu card, axis-tick band above the footer and under page heroes.

## Institutional layer (2026-10-29)
Direction from the client: look like the best financial firms worldwide, with freedom to change the site. Applied as the last block of `main.css`:
- Square-edged: radius tokens 3 / 6 / 10 px (were 12 / 20 / 32); rectangular buttons; no pill navigation.
- Hairlines instead of shadows; cards stay still (the 3D tilt was removed).
- Headlines 700 weight, larger and tighter; one gold accent (thin rules, one keyword per title).
- Hero facts (authority, licence number, licensed activity, headquarters) sit in a flat dark bar under the hero.
- Process steps are plain numbered columns with a top rule.
- Official logo kept as supplied; the header logo eases from 58 px to 46 px on scroll. New logo concepts were shown to the client and none was approved yet.
