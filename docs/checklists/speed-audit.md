# Tool 3 — Speed Audit (`/speed-audit`)

Intent: "website speed test", "core web vitals test", "page speed checker".

Scan profile: self-hosted Lighthouse, mobile preset primary + desktop
secondary, 3 runs per URL, median reported, run-to-run variance shown · TTFB =
median of 5 instrumented requests · scripted interaction latency (click,
scroll, input) median of 3 runs in the renderer · network waterfall from the
render log. Fixed emulation profile stated in the report (device, network and
CPU throttling, cold cache). CrUX panel optional (Tier B), clearly labelled
field data with its collection period; absence → "below CrUX reporting
threshold" or `not_measured`.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | LCP (lab) | 25 |
| 2 | Responsiveness (lab TBT + scripted interaction) | 25 |
| 3 | CLS (lab) | 15 |
| 4 | TTFB (server-measured) | 10 |
| 5 | Resource efficiency | 15 |
| 6 | Render-blocking & third-party cost | 10 |

## Checks

Metric thresholds (lab, mobile emulation): LCP good < 2.5 s, warn < 4.0 s ·
TBT good < 200 ms, warn < 600 ms · CLS good < 0.1, warn < 0.25 · TTFB good
< 800 ms, warn < 1800 ms. Thresholds live in the rulepack.

### 1. LCP (lab) (25)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SPEED-LCP-01 | Median lab LCP within threshold | H | 75 | A | Lighthouse trace (3 runs, median) |
| SPEED-LCP-02 | LCP element identified and classified (image, text, background) | I | 0 | A | Lighthouse + trace element |
| SPEED-LCP-03 | LCP breakdown: TTFB, load delay, load time, render delay phases | I | 0 | A | Trace phase analysis |
| SPEED-LCP-04 | LCP image efficiently delivered (priority, preload or fetchpriority, right size/format) | M | 25 | A | Network log + trace |
| SPEED-LCP-05 | Run-to-run variance within 20% (else confidence lowered, never averaged silently) | I | 0 | A | 3-run spread |

W note: SPEED-LCP-05 is a confidence modifier, not scored; weight stays 0 and
it never changes the score, only the stated confidence.

### 2. Responsiveness (lab) (25)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SPEED-RES-01 | Lab TBT within threshold | H | 40 | A | Lighthouse |
| SPEED-RES-02 | Scripted click latency (median of 3) within budget | H | 20 | A | Renderer scripted interaction (§3.4) |
| SPEED-RES-03 | Scripted scroll smoothness (long-frame count during scripted scroll) | M | 15 | A | Renderer frame monitor |
| SPEED-RES-04 | Scripted input latency (focus + keystroke echo) | M | 15 | A | Renderer scripted interaction |
| SPEED-RES-05 | Long-task census (count and total time > 50 ms) | M | 10 | A | Trace long tasks |

### 3. CLS (lab) (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SPEED-CLS-01 | Lab CLS within threshold | H | 40 | A | Lighthouse |
| SPEED-CLS-02 | Shift sources identified (elements with reserved-size failures) | M | 30 | A | Trace shift attribution |
| SPEED-CLS-03 | Media elements declare width/height or aspect-ratio | M | 20 | A | DOM geometry probe |
| SPEED-CLS-04 | No late-loading fonts causing reflow (font-display, size-adjust) | M | 10 | A | Font load trace + CSS audit |

### 4. TTFB (server-measured) (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SPEED-TTFB-01 | Median TTFB of 5 direct requests within threshold | H | 50 | A | Instrumented HTTP client, per-phase timings |
| SPEED-TTFB-02 | Redirect cost before first byte (extra DNS/TLS/roundtrips) | M | 20 | A | Timing waterfall |
| SPEED-TTFB-03 | Caching headers allow reuse (Cache-Control on HTML sane, static assets far-future) | M | 20 | A | Header inspection across asset classes |
| SPEED-TTFB-04 | Compression active (br preferred, gzip minimum) on text assets | M | 10 | A | Accept-Encoding probe |

### 5. Resource efficiency (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SPEED-RESF-01 | Total transfer within budget (per page class) | M | 20 | A | Network log byte totals |
| SPEED-RESF-02 | Request count within budget (< 100) | M | 10 | A | Network log |
| SPEED-RESF-03 | Images: modern formats (AVIF/WebP), sized to render box, lazy below fold | H | 25 | A | Image response + geometry analysis |
| SPEED-RESF-04 | Fonts: ≤ 4 files, woff2, subset, font-display swap | M | 15 | A | Font response census |
| SPEED-RESF-05 | Unused JS estimate (executed vs transferred) | M | 15 | A | Coverage profile from renderer |
| SPEED-RESF-06 | Unused CSS estimate | L | 15 | A | Coverage profile |

### 6. Render-blocking & third-party cost (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SPEED-3P-01 | Render-blocking scripts/styles in head minimized | H | 30 | A | Waterfall + head audit |
| SPEED-3P-02 | Third-party host count and bytes share reported; blocking third parties flagged | M | 30 | A | Network log host split |
| SPEED-3P-03 | Third-party scripts load async/defer where possible | M | 20 | A | Script attribute audit |
| SPEED-3P-04 | No synchronous document.write | M | 10 | A | Console + behavior probe |
| SPEED-3P-05 | Preconnect/dns-prefetch hints for critical origins | L | 10 | A | Head audit |

## Accuracy rules enforced in the UI

- Every metric carries provenance: Lab-measured vs Server-measured vs
  Provider data (CrUX) vs Measured by us (scripted interactions), with
  timestamps.
- Lab numbers are never displayed as field data; the CrUX panel is visually
  separated with its collection period.
- Variance beyond 20% across the 3 Lighthouse runs lowers confidence and
  states the spread; the median is still reported.
- The fixed emulation profile (device, throttling, cache state) is printed on
  every speed report.

## Tier B panels for this tool

- **CrUX field-data panel** (LCP, INP, CLS, FCP distributions with collection
  window), origin-level fallback when URL-level data is absent. No CrUX data →
  explicit below-threshold message. Provider disabled/unreachable →
  `not_measured` note. Score unaffected in all cases.

## Outputs

Median scorecard with run spread · phase-by-phase LCP waterfall · long-task
census · third-party cost table · resource efficiency table with biggest
offenders · prioritized fix list with estimated effect ranges from rulepack
templates (evidence-based, no invented percentages of "score gain").
