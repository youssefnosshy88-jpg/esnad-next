# Implementation status

Last updated 2026-10-29. Staging: https://green-gorilla-647751.hostingersite.com (noindex, mail blocked). Production is untouched.

## Deployment
Deployed and checked on staging on 2026-10-29: Tajawal self-hosted (6 files load, 200), Esnad mark as atlas hub, atlas nodes 7/8 separated, finance motifs, plain ticker, gold keywords in homepage titles (9), numbered service directory (hover swaps the panel; list now sits before the clients heading), static 62-logo grid on the vision page and services directory, values in three columns.

### Hosting limit (cause of the earlier failed deploys)
hPanel shows the Business plan (account `elwaheed.com`, 11 websites) at 100% resource usage in the last 24 h. The limiting resource is max processes (NPROC): average 100 of 120. Other resources were fine (CPU 1%, memory about 630 MB of 3 GB, PHP workers 8 of 60, disk 15 of 50 GB, inodes 320k of 600k). When processes are near the limit the host refuses new SSH commands ("exec request failed"). The client stopped the running processes from hPanel and deploys then worked. A permanent fix is a plan upgrade or moving other sites off this account; that is the client's decision.

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
