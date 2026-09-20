# Tool 12 — AdSense Audit (`/adsense-audit`)

Intent: "adsense approval checker", "adsense eligibility checker".

Framing (mandatory in UI and every report): independent, evidence-based
readiness assessment derived from Google's publicly documented Publisher
Policies and program requirements. Not affiliated with, endorsed by, or
connected to Google. Does not access AdSense data, cannot see application
status, cannot guarantee any outcome.

Scan profile: deep crawl 30–100 pages (up to 250 for accounts) · full content
extraction · rendered sample 20 URLs · policy-risk classification via
deterministic lexicons with context snippets · trust-page discovery · consent
signals passed through from Tool 14 when both scans run in the same audit ·
mobile and speed signals from shared measurements.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | Content volume and depth | 20 |
| 2 | Content originality and value signals | 15 |
| 3 | Required legal and trust pages, correctly linked site-wide | 12 |
| 4 | Prohibited and restricted content scan | 15 |
| 5 | Navigation, structure, unfinished-site signals | 8 |
| 6 | Technical eligibility | 15 |
| 7 | Trust, transparency, ownership signals | 8 |
| 8 | Monetization infrastructure readiness | 7 |

Caps: CAP-ADS-01 (Privacy Policy missing or not linked site-wide → capped at
55). CAP-ADS-02 (critical prohibited-content hit → capped at 35).

Outputs required: readiness score 0–100 with grade band · separate confidence
indicator from crawl coverage and evidence completeness · readiness band
(High readiness / Moderate — fixable blockers / Low — critical blockers / Not
eligible — critical policy risk observed) with the explicit statement that it
is an estimate from public criteria, not a prediction; Google's internal
review factors are not observable from outside · blockers/warnings/opportunities
each with evidence, mapped public policy source URL, fix, effort · "fix and
re-apply" ordered checklist with 30/60/90-day plan · rescan diff ·
rejection-message decoder.

## Checks

### 1. Content volume and depth (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| ADS-VOL-01 | Sufficient crawled content pages (≥ 20 substantial pages observed within cap; fewer → warn with exact count, never invented) | C | 25 | A | Crawl census |
| ADS-VOL-02 | Median main-content words ≥ 400 on content pages | H | 20 | A | Extraction |
| ADS-VOL-03 | Word-count distribution healthy (p25 ≥ 250) | M | 15 | A | Distribution |
| ADS-VOL-04 | Section coverage: ≥ 2 distinct content sections with ≥ 3 substantial pages each | M | 15 | A | Section taxonomy + counts |
| ADS-VOL-05 | Unique-topic spread: near-duplicate template pages do not dominate (< 10% of content pages in any dup cluster) | H | 15 | A | SimHash clusters |
| ADS-VOL-06 | Page-type diversity (articles, listing/hub pages, about-type pages present as applicable) | L | 10 | A | Page-type classifier |

### 2. Content originality and value signals (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| ADS-ORIG-01 | Boilerplate ratio < 60% on content pages | H | 20 | A | Shared-block analysis |
| ADS-ORIG-02 | No exact-duplicate main content clusters | C | 25 | A | Content hashes |
| ADS-ORIG-03 | Near-duplicate share < 15% | H | 20 | A | SimHash/MinHash |
| ADS-ORIG-04 | Low-information pages (high nav-to-content ratio, link-farm-style pages) < 10% | H | 20 | A | Text/link ratio per page |
| ADS-ORIG-05 | Substantive value markers present: original phrasing depth (lexical stats), examples, tables/lists where topic invites | M | 15 | A | Structure + statistics census |

### 3. Required legal and trust pages (12)

Discovery: candidate pages found by URL/slug patterns, link text, title text,
and footer/header census. Quality checks look at content substance (word
count, section presence), not mere existence.

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| ADS-LEGAL-01 | Privacy Policy exists, substantive, mentions data collection/advertising/partners (policy page quality check) | C | 25 | A | Discovery + content checks |
| ADS-LEGAL-02 | Privacy Policy linked site-wide (footer/header/nav on sampled pages) | C | 15 | A | Link census |
| ADS-LEGAL-03 | About page states who runs the site (named entity, purpose) | C | 20 | A | Discovery + content checks |
| ADS-LEGAL-04 | Contact route real and reachable (form present or mailto/link; not a dead form) | H | 15 | A | Discovery + form/mailto probe (non-submitting) |
| ADS-LEGAL-05 | Terms/Disclaimer present where site publishes advice or endorsements | M | 10 | A | Discovery |
| ADS-LEGAL-06 | Cookie Policy present when cookies/consent observed | M | 10 | A | Cookie census × page discovery |
| ADS-LEGAL-07 | Editorial Policy present where content gives guidance (advice/health/finance) | L | 5 | A | Discovery |

### 4. Prohibited and restricted content scan (15)

Method: versioned lexicons per policy area (word/phrase lists with bounds,
URL-slug and title patterns) applied to extracted text with context windows;
every hit stores the excerpt and location. Hits are review flags with
severity, never auto-verdicts; borderline hits render as `warn` requiring
human review. Lexicons and their source mapping live in the rulepack with
sources and dates.

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| ADS-POL-01 | Adult/sexually-explicit content signals absent (or age-metas present and consistent) | C | 15 | A | Lexicon + context scan |
| ADS-POL-02 | Gambling-related signals absent or jurisdictionally framed (licenses referenced) | C | 10 | A | Lexicon + context scan |
| ADS-POL-03 | Violence/gore/shock content signals absent | H | 10 | A | Lexicon scan |
| ADS-POL-04 | Hate/discrimination signals absent | C | 10 | A | Lexicon scan |
| ADS-POL-05 | Piracy/copyright-infringement patterns absent (torrent/crack/keygen/streams patterns) | C | 15 | A | Pattern + URL scan |
| ADS-POL-06 | Hacking/cracking instruction patterns absent | C | 10 | A | Pattern scan |
| ADS-POL-07 | Drugs/tobacco/weapons sales patterns absent or appropriately restricted | H | 10 | A | Lexicon scan |
| ADS-POL-08 | Counterfeit/replica goods patterns absent | H | 5 | A | Pattern scan |
| ADS-POL-09 | Compensated-click/traffic-exchange incentives absent ("click ads", paid-to-surf patterns) | C | 10 | A | Lexicon scan |
| ADS-POL-10 | Misleading-content patterns absent (fake news-style unattributed sensational claims census — flag with excerpt, human-review framing) | M | 5 | A | Pattern + attribution census |

### 5. Navigation, structure, unfinished-site signals (8)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| ADS-NAV-01 | Breadcrumbs or clear section hierarchy present on content pages | M | 15 | A | DOM + URL taxonomy |
| ADS-NAV-02 | Footer complete (privacy, about, contact reachable from footer) | H | 20 | A | Footer link census |
| ADS-NAV-03 | Empty category/tag pages absent (list pages with zero items) | M | 15 | A | Listing-page item counts |
| ADS-NAV-04 | Placeholder/unfinished pages absent ("coming soon", "hello world" defaults, theme starter content) | C | 20 | A | Pattern matcher + fixture strings |
| ADS-NAV-05 | Internal search functional when present (returns results for a benign term; never submits external forms) | L | 10 | A | Search probe on detected search route |
| ADS-NAV-06 | No broken navigation targets in menus | H | 20 | A | Menu link checker |

### 6. Technical eligibility (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| ADS-TECH-01 | HTTPS valid on all sampled pages, no mixed content | C | 15 | A | Fetch + network log |
| ADS-TECH-02 | Key pages indexable (homepage, top sections, sample content: no noindex/robots block) | C | 15 | A | Directive intersection |
| ADS-TECH-03 | Mobile usability clean (overflow/tap/viewport from Tool 4 signals) | H | 15 | A | Shared mobile measurements |
| ADS-TECH-04 | Speed sane (lab LCP < 4 s warn band; TTFB) | M | 10 | A | Shared speed measurements |
| ADS-TECH-05 | No intrusive interstitials on content pages | H | 10 | A | Overlay detector |
| ADS-TECH-06 | No 5xx on key URLs during crawl | H | 10 | A | Status census |
| ADS-TECH-07 | Valid robots.txt not blocking content | M | 10 | A | Parser + simulation |
| ADS-TECH-08 | Sitemap present and valid | M | 10 | A | Sitemap validator |
| ADS-TECH-09 | No noindex on content pages | C | 5 | A | Directive census |

### 7. Trust, transparency, ownership signals (8)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| ADS-TRUST-01 | Author bylines on content pages | H | 20 | A | Byline extraction |
| ADS-TRUST-02 | Author credentials/about linkage present | M | 15 | A | Byline link resolution |
| ADS-TRUST-03 | Published/updated dates on content | M | 15 | A | Date extraction |
| ADS-TRUST-04 | Real contact routes (email or form; postal address where business-type site) | M | 15 | A | Discovery + content checks |
| ADS-TRUST-05 | Current copyright year | L | 5 | A | Footer extraction |
| ADS-TRUST-06 | Clear site purpose stated (homepage tagline/about summary articulates niche) | M | 15 | A | Homepage/about text classification by patterns + term coherence |
| ADS-TRUST-07 | Niche focus: content concentrates on a coherent topic set (term-overlap coherence across sections) | M | 15 | A | Topic coherence statistics |

### 8. Monetization infrastructure readiness (7)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| ADS-MON-01 | ads.txt present at root and syntactically valid (when ad code observed; absence without ad code = informational) | M | 25 | A | Fetch + parser |
| ADS-MON-02 | No ad-only or near-empty pages (pages whose main content is advertising) | H | 25 | A | Ad-slot-to-content ratio analysis |
| ADS-MON-03 | No auto-refresh or pop-under/redirect-ad patterns | H | 20 | A | Behavior probes + script signatures |
| ADS-MON-04 | Existing ad placements (if any) do not overlay content or mimic UI deceptively | M | 15 | A | Geometry + overlap probe |
| ADS-MON-05 | Consent readiness for future monetization (CMP/Consent Mode signals from Tool 14 measurement) | M | 15 | A | Shared privacy measurements |

## Rejection-message decoder (deterministic mapping table)

Maps user-pasted rejection wording to the checks most likely implicated. The
decoder states clearly: Google does not publish per-site reasons; this maps
common wording to the checks that most often correlate. Mapping table lives in
the rulepack with review dates:

| Rejection wording (normalized) | Checks surfaced | First fixes |
| --- | --- | --- |
| "low value content" / "no sufficient content" | ADS-VOL-01..03, ADS-ORIG-01..05, ADS-VOL-05 | Expand/consolidate plan from Content Audit |
| "site not ready" / "under construction" | ADS-NAV-04, ADS-VOL-01, ADS-TECH-* | Remove placeholders, finish sections |
| "policy violation" | ADS-POL-01..10 hits | Remove/review flagged excerpts |
| "navigation issues" | ADS-NAV-01..06 | Fix menus/breadcrumbs/empties |
| "no content" | ADS-VOL-01, ADS-NAV-04 | Publish plan + minimum viable content set |
| "adult content" / specific policy name | Matching ADS-POL check | Per-policy remediation |

## Hard rules

- No guaranteed-approval language anywhere; banned-phrase list in CI copy lint
  ("guaranteed approval", "will be approved", "100% approval", "pass AdSense
  review", and equivalents).
- No invented Google internals; every mapped requirement cites a public
  source URL collected at rulepack build time.
- Confidence drops visibly when crawl coverage < 80% of sitemap or when the
  site served challenges.

## Outputs

Readiness score + band + confidence · category bars · blockers/warnings/
opportunities with evidence and policy citations · ordered fix-and-reapply
checklist with 30/60/90-day plan · rejection decoder · rescan diff (score
delta, resolved/new/regressed).
