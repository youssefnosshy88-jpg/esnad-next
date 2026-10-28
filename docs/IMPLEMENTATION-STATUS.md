# Implementation status

Last updated 2026-10-29. Staging: https://green-gorilla-647751.hostingersite.com (noindex, mail blocked). Production is untouched.

## Blocker right now
Hostinger closes SSH connections ("closed by remote host"), so the latest local changes are **not deployed**. Last confirmed staging state: commit `6037e84` (fade-up reveal, new dropdown, logo wall fix). Local and pushed-later changes waiting for deploy:
Tajawal self-hosted font, Esnad mark as atlas hub, atlas nodes 7/8 spacing, finance motifs replacing stars/lattices, plain ticker, static client grid on the vision page, `docs/`.

## Stages

| Stage | Status | Notes |
|---|---|---|
| 1 Reference analysis | Done (partial evidence) | See `ANTRA-DESIGN-AUDIT.md`. Home, service list, stats and DOM of 15 pages observed. Mobile nav, lightbox, filtering unverified. |
| 2 Esnad audit | Done earlier | 59 pages/posts (AR + EN) migrated from Elementor to block content; inventory in `private/` (not in Git). |
| 3 Design foundation | Done | Theme `esnad-next`, tokens, header/mega menu, footer, forms plugin `esnad-core`. |
| 4 Homepage | Implemented, needs review | Hero + video, trust cards, ticker, about, milestones, services explorer, atlas, journey, pillars, needs, clients, compliance, articles, FAQ, CTA. |
| 5 Service pages | Implemented, needs review | Numbered chapters per service. Interactive numbered directory (Antra-style list + panel) is **next**. |
| 6 Corporate/legal | Implemented, needs review | About, board, executive, disclosure, contact, complaints, careers. |
| 7 Articles | Implemented, needs review | List + single. Category filters not yet built. |
| 8 Responsive/RTL | In progress | Desktop and mobile samples checked; tablet and 1920 px not fully checked. |
| 9 QA | In progress | See `QA-REPORT.md`. |
| 10 Documentation | In progress | This folder. |

## Open decisions for the client
- Disclosure page: 2020 and 2022–2025 buttons all point to the 2022 PDF in the original site.
- English regulatory notice wording, journey stages, pillars, FAQ answers, complaints procedure.
- Recipient e-mail for forms (currently the WordPress admin e-mail).
- Hero video licence or replacement with Riyadh footage; real photography.
- Verified figures if a statistics row is wanted.
