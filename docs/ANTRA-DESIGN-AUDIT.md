# Antra design audit

Reference: https://demo2.themelexus.com/antra/ (demo of the ThemeForest theme "Antra"). Inspected in the in-app browser at 1440 px on 2026-10-29.
No Antra code, CSS, images or copy is reused. Everything listed under "Application to Esnad" is an independent implementation.

## How this was verified

| Evidence type | What was done |
|---|---|
| Rendered behaviour | Loaded the home demo at 1440 px, scrolled through it, screenshotted the hero, the "Where spaces inspire" band, the numbered service list and the statistics row. |
| Interaction | Dispatched `mouseover` on the third item of the service list and confirmed that the `show` class moves from item 1 to item 3 (`.antra-service-item-titles`), i.e. the list is hover-driven and swaps an image panel. |
| DOM / assets | Fetched 15 demo pages (home-2, home-5, home-8, services-01, services-03, projects-01, projects-03, gallery-01, about-us, our-teams, virtual-tours, blog, contact-us, faqs, pricing-plan) and listed their component class names and headings. |
| Not verified | Per-page screenshots of home-3/4/6/7/9, mobile navigation, the lightbox, project filtering and the virtual tour were **not** inspected visually. They appear below as *unverified*. |

Libraries referenced by the demo (read from enqueued script names): Slider Revolution (`sr7`), GSAP + ScrollTrigger, TweenMax, jarallax (parallax), jquery-numerator (counters), Magnific Popup and Fancybox (lightboxes), three.js + Panolens (virtual tours), Lenis (smooth scroll, seen in page source). The substring scan on other pages is indicative only, because the theme enqueues the same bundle everywhere.

## Page inventory seen in the demo menu

home-1…home-9, services-01/02/03, service single, projects-01/02/03, project single, about-us, our-teams, team single, virtual-tours, pricing-plan, gallery-01/02, faqs, blog (grid, list, standard, single styles 1 and 2), contact-us, 404, coming-soon, shop (WooCommerce, not relevant to Esnad).

## Components

Status key: **Observed** = seen rendered. **DOM only** = present in markup, behaviour not watched. **Unverified**.

### 1. Cinematic hero (home-1)
- Composition: full-bleed interior photograph under a dark overlay, small eyebrow pill ("Fast and reliable"), very large two-line grotesque headline, short paragraph and a pill CTA with a round icon button, circular scroll-down button at the bottom centre.
- Behaviour: **Observed** the headline enters with a masked line reveal while an image panel clears (screenshot taken mid-load showed half-revealed lettering). Slider Revolution drives it.
- Trigger: load. Desktop: full composition. Mobile: **observed** header collapses to a hamburger plus a bottom bar (Search, Account, Cart).
- Application: our hero already has video, headline and trust cards; add a masked line reveal for the headline and a scroll cue.
- Technology: CSS `clip-path` / `translateY` inside `overflow:hidden`, IntersectionObserver or load class. No slider library.
- Performance: negligible; avoid the Slider Revolution payload.

### 2. Numbered interactive service list (home-5, home-8, services-01)
- Composition: **Observed** a two-column block. Right column is a list "01 … 06" of large titles with an arrow; the active row is gold with a round arrow button. Left column is a large rounded image with a caption card that changes per row. Classes: `antra-service-accordion-layout-1/3`, `antra-service-list-titles`, `antra-service-list-excerpts`.
- Behaviour: **Observed** the active item is switched by hover (`show` class); the image and excerpt swap.
- Trigger: hover. Mobile: **unverified** (the theme name "accordion" suggests tap-to-expand).
- Application: Esnad services directory and homepage explorer: numbered titles, a panel with the service summary and link, keyboard focus and tap support.
- Technology: `<button>`/`<a>` list, one `data-active` state, CSS transitions on opacity and transform.
- Performance: low; images are already in the media library.

### 3. Statistics row (home-1, home-2)
- Composition: **Observed** four large figures with short labels below, counted up on scroll (`jquery-numerator`).
- Application: only for *verified* numbers. Esnad has no approved client or revenue figures, so the row must use facts such as the licence date and number of service lines, or stay out until the client supplies figures.
- Status: pattern observed; Esnad content pending approval.

### 4. Dark "story" band with check list (home-1, home-5)
- Composition: **Observed** dark section with an eyebrow, a large two-tone headline ("Where spaces inspire, and *design* comes alive"), a check-list in two columns and a rounded photograph on the right.
- Application: About section on the homepage and About page, with gold on a single keyword.

### 5. Timeline / process (about-us)
- DOM only: `antra-timeline`, `antra-timeline-side-titles`, `antra-timeline-list-titles`, headings "01. Initial Consultation", "02. Design & Planning", "03. Implementation".
- Application: Esnad advisory journey (stage names pending client confirmation).
- Trigger and mobile behaviour: **unverified**.

### 6. Project grids and filtering (projects-01/02/03)
- DOM only: `antra-project`, `antra-pagination-list`, `antra-loadmore`, Isotope on the bundle list. Filtering behaviour **unverified**.
- Application: article listing with category filters (Esnad has no projects to show, and none will be invented).

### 7. Gallery and lightbox (gallery-01/02)
- DOM only: headings per room type, Magnific Popup/Fancybox loaded. **Unverified** visually.
- Application: none needed now; Esnad has no approved photography beyond the logo wall.

### 8. Team (our-teams, team single)
- DOM only: `antra-team` with social icons. Not applied: Esnad people are already presented in the board and executive pages with approved names.

### 9. Virtual tours (three.js, Panolens)
- Not applicable to a financial site. Not implemented.

### 10. FAQ (faqs)
- DOM only: plus/minus icons (`antra-icon-plus/minus`) indicate an accordion. Application: Esnad FAQ already exists; keep the accessible `<details>` pattern.

### 11. Contact (contact-us)
- DOM only: headline, address and phone blocks, form. Application: Esnad contact page already has info cards, social links and a working form.

### 12. Header and menu
- **Observed** at 1440 px: transparent header over the hero with logo left, menu with chevrons, phone block, gold "Get a Quote" pill, round search and grid buttons (off-canvas "canvas menu layout 2"). Mobile: hamburger plus bottom action bar.
- Application: Esnad header already has a mega panel for services; adding a persistent consultation button and an off-canvas panel is optional.

### 13. Buttons, micro-interactions
- **Observed** pill buttons with an icon circle; `transition: all` on list items. Application: already similar.

## Deep audit: Home 6 (https://demo2.themelexus.com/antra/home-6/)

Inspected at 1440 px, section by section (screenshots reviewed in the session). Page height about 13 060 px, 17 Elementor sections. Section tops measured by script.

| # | Section (top px) | Composition observed | Behaviour observed | Application to Esnad |
|---|---|---|---|---|
| 1 | Hero (0) | Dark interior photo, eyebrow pill ("Trusted design partner"), three-tone headline (white, **gold keywords**, white), short paragraph, round "Start Project" button bottom-left, an oversized cropped word along the bottom edge | Load-time reveal (masked text, Slider Revolution). Giant word is cut by the viewport edge | Keep our video hero; adopt gold-keyword headline and a round primary action. Giant decorative word: only if it is the company name, in solid (not outlined) type. |
| 2 | About "dual expertise" (951) | Collage of three overlapping rounded photographs on the left, eyebrow, headline with gold middle line, paragraph, **accordion** (active title gold with minus, others with plus), pill button "See more about us" | Accordion toggles on click (plus/minus icons present). Faint architectural line drawing as background | Esnad About: accordion for vision / mission / values with existing approved text. No photo collage until approved photography exists. |
| 3 | Projects strip (1957) | Four full-height portrait cards edge to edge, category pill top, title bottom over a gradient | **Swiper carousel** (`antra-swiper-wrapper swiper`); hover-width change was tested and did **not** occur (widths stayed equal) | Not applicable: Esnad has no projects to show. A comparable strip could carry the eight services (already built as the numbered list). |
| 4 | How we work (2837) | Eyebrow, big headline with gold keywords, photo left, 2×2 grid with huge pale ghost numerals 01–04 and bold step titles | Static layout (scroll reveal presumed, not verified) | Matches Esnad's advisory journey; our journey already has numbered stages. Ghost numerals can replace the outlined ones. |
| 5 | Services (3885) | Dark section, headline with gold keywords, white rounded cards each with number at top right, title, text and a photograph; cards **staggered vertically** | Swiper carousel (`antra-service … swiper-wrapper`), not hover | Our numbered list + panel is the better fit for eight services. |
| 6 | "What we do" (4925) | Headline with gold word, three check-mark rows, button, two overlapping photographs | Static | Optional for the About page. |
| 7 | Full-bleed video CTA (5800) | Photograph or video across the viewport, enormous headline bottom-left, short paragraph bottom-right, centred round **play** button with ring | Play opens a video (presumed lightbox; not clicked) | Our CTA band: bigger headline and a play control only if Esnad supplies a presentation video. |
| 8 | Skills (6650) | Left: three horizontal bars with percentages; right: 3D-rendered room; faint giant word behind | Bars fill on scroll (presumed) | **Not applicable**: percentages would be invented claims. |
| 9 | Statistics (7600) | Photo background, four large numbers with labels, count-up | Count-up via `jquery-numerator` | Only with client-verified figures; none supplied. |
| 10 | Testimonials (8548) | Photo, rating "4.80 (2,688 reviews)", quote, avatar | Slider | **Not applicable**: no testimonials or ratings may be invented. |
| 11 | Giant text marquee + contact form (9453) | Dark band, enormous uppercase text running off the viewport, then a two-column block: heading, address and phone left; form with dark fields right | Text marquee is horizontal motion (mechanism not inspected) | Contact section on dark with the form; skip the giant marquee (client dislikes outlined/running display type). |
| 12 | Blog (10552) | Eyebrow, headline with gold keywords, three cards with category pill, date and author, one card shown in hover state (dark overlay, title over image) | Hover state turns a card into an image-overlay variant | Article cards with category pill; hover overlay optional. |
| 13 | Newsletter + footer (11610–13060) | Newsletter band then a large footer | Static | Footer already built; newsletter needs a mailing-list decision. |

Global patterns seen on Home 6 and adopted as principles: every section header is *eyebrow pill + large headline with one or two gold keywords*; large radius (about 20 px) on all media; generous vertical rhythm of 100–140 px; alternating light and dark bands; round icon buttons for primary actions.

### Verified vs not
- Verified: the section list, order and visuals above; that projects and services are Swiper carousels; that hovering a project column does not resize it.
- Not verified: scroll reveal timings, the play-button target, the marquee mechanism, the accordion animation, mobile layout of this page.

## Ideas for Esnad that are NOT from Antra
- Financial motifs instead of architectural ones: bars, trend line and an axis of ticks as the single decorative language.
- Atlas with the Esnad mark as the hub (Madari-style geographic storytelling).
- Hijri/Gregorian date and regulatory notice in the top bar.

## Decisions taken after review by the client
- No curtain-panel reveals; content fades up instead.
- No repeating ornamental lattices behind text; no stars. Finance-related shapes only.
- Tajawal, self-hosted, three weights.
