# Tool 4 — Mobile Audit (`/mobile-audit`)

Intent: "mobile friendly test", "mobile usability checker".

Scan profile: renders at 320, 375, 414, 768 px (DPR per device class); full
rendered-sample pipeline; orientation-change probe (portrait → landscape →
portrait); keyboard-reflow probe (focus an input, measure viewport and layout
stability); mobile-vs-desktop content parity diff on the rendered sample.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | Viewport & scaling | 20 |
| 2 | Overflow & tap targets | 25 |
| 3 | Typography legibility | 15 |
| 4 | Intrusive interstitials | 15 |
| 5 | Mobile-first parity | 15 |
| 6 | Responsive media | 10 |

## Checks

### 1. Viewport & scaling (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| MOB-VP-01 | Viewport meta present with width=device-width (or equivalent sane width) | C | 35 | A | Head extraction |
| MOB-VP-02 | No user-scalable=no / maximum-scale=1 blocking zoom | H | 25 | A | Meta token parse |
| MOB-VP-03 | No fixed-width viewport values | H | 20 | A | Meta token parse |
| MOB-VP-04 | Visual viewport behaves under keyboard reflow (focused input remains visible, no viewport jump) | M | 20 | A | Keyboard-reflow probe |

### 2. Overflow & tap targets (25)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| MOB-OVF-01 | No horizontal overflow at 320 px | C | 20 | A | Renderer overflow probe, per-URL per-width |
| MOB-OVF-02 | No horizontal overflow at 375 px | H | 15 | A | Same |
| MOB-OVF-03 | No horizontal overflow at 414 px | H | 10 | A | Same |
| MOB-OVF-04 | No horizontal overflow at 768 px | M | 5 | A | Same |
| MOB-OVF-05 | Offending elements identified with selector + measured width | I | 0 | A | Probe evidence (attribution, not scored) |
| MOB-TAP-01 | Tap targets ≥ 24 × 24 px with ≥ 8 px separation (links, buttons, inputs) | H | 25 | A | Geometry probe on interactive elements |
| MOB-TAP-02 | Primary actions reachable within 4 thumb-zone screenfuls | L | 10 | A | Element position audit |
| MOB-TAP-03 | No hover-only interactions hiding essential actions | M | 15 | A | Hover-dependence probe (reveal-on-hover content) |

### 3. Typography legibility (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| MOB-TYPE-01 | Body text ≥ 12 px computed at 375 px | H | 30 | A | Computed style probe |
| MOB-TYPE-02 | Line length within readable measure (≤ ~90 chars) on mobile | L | 20 | A | Layout probe |
| MOB-TYPE-03 | Tap-target labels not truncated (no clipped text) | M | 20 | A | Overflow-clip probe |
| MOB-TYPE-04 | Contrast of body text at mobile sizes (shared with Tool 8 measurement) | M | 30 | A | Rendered pixel contrast measurement |

### 4. Intrusive interstitials (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| MOB-INT-01 | No full-screen popups/overlays blocking content on first navigation | H | 40 | A | Overlay detector: coverage, scroll lock, close affordance |
| MOB-INT-02 | Consent walls present content after rejection (reject ≠ dead end) | H | 25 | A | Scripted consent interaction probe |
| MOB-INT-03 | No app-install banners covering > 15% viewport | M | 15 | A | Banner geometry probe |
| MOB-INT-04 | Overlays dismissible by keyboard and back gesture | M | 20 | A | Renderer interaction probe |
| MOB-INT-05 | Interstitial inventory reported with selectors and screenshots | I | 0 | A | Evidence capture |

### 5. Mobile-first parity (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| MOB-PAR-01 | Main text content parity: mobile main-text ≥ 95% of desktop main-text | H | 30 | A | Content extraction diff at both widths |
| MOB-PAR-02 | Heading structure parity | M | 15 | A | Heading tree diff |
| MOB-PAR-03 | Navigation parity (primary nav reachable, hamburger functional) | H | 25 | A | DOM + interaction probe |
| MOB-PAR-04 | Images/media parity (no content images stripped on mobile) | M | 15 | A | Media census diff |
| MOB-PAR-05 | No desktop-only notices ("view desktop version") | L | 15 | A | Text pattern census |

### 6. Responsive media (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| MOB-MED-01 | No image rendered wider than viewport (overflow or shrink) | H | 25 | A | Geometry probe |
| MOB-MED-02 | Images use responsive sizing (srcset/sizes or fluid CSS) | M | 20 | A | Attribute + computed style audit |
| MOB-MED-03 | Embedded iframes (video/maps) fit container | M | 20 | A | Geometry probe |
| MOB-MED-04 | Mobile fetches not desktop-byte images (delivered vs rendered size ratio ≤ 2) | M | 20 | A | Network log + rendered geometry |
| MOB-MED-05 | Orientation change keeps layout stable (no overlap/loss) | M | 15 | A | Orientation probe |

## Designed edge behaviors

- Keyboard-reflow and orientation probes run only when the page exposes the
  needed inputs; otherwise the affected checks are `not_applicable`, not
  failed.
- Consent-wall probing interacts only with visible consent UI (accept/reject
  buttons); it never submits forms or authenticates.

## Tier B panels for this tool

- **CrUX mobile field data** (phone form-factor distribution) where available;
  labelled with collection period; `not_measured` otherwise.

## Outputs

Per-width overflow matrix with element-level evidence · tap-target heat list ·
interstitial inventory with screenshots · parity diff viewer · prioritized fix
list with selectors.
