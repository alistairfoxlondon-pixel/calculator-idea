# Tool 1 — SEO Audit (`/seo-audit`)

Intent: "seo audit", "seo checker", "website seo score".

Scan profile: BFS crawl, 50 pages anonymous / 250 accounts, same-origin,
politeness-limited · rendered sample: 15 representative URLs (homepage, section
roots, newest, deepest, highest inlinks) · Lighthouse: 3 representative URLs,
3 runs each, median · DNS/TLS inspection once per host · Tier B panels: PSI and
CrUX shown in a labelled Performance field-data panel; Safe Browsing flag on
the trust basics panel. Absent providers → panel states `not_measured`; no
category weight is lost.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | Crawlability & indexability | 15 |
| 2 | Technical foundations | 12 |
| 3 | On-page metadata | 15 |
| 4 | Content depth & uniqueness | 15 |
| 5 | Internal linking & architecture | 10 |
| 6 | Structured data coverage | 8 |
| 7 | Performance proxies | 15 |
| 8 | Mobile & UX | 5 |
| 9 | Security & trust basics | 5 |

Cap: CAP-SEO-01 (robots disallows all content crawling → score capped at 40).

## Checks

Check weights (W) are relative inside a category and sum to 100 per category.

### 1. Crawlability & indexability (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEO-CRAWL-01 | robots.txt reachable and parseable (5xx → assume allow, flag) | C | 10 | A | Fetch `/robots.txt`, own parser, status+bytes evidence |
| SEO-CRAWL-02 | robots.txt does not disallow content sections | H | 15 | A | Parser × section-root sample simulation |
| SEO-CRAWL-03 | XML sitemap discoverable (robots directive, `/sitemap.xml`, index recursion ≤ 2 levels) | H | 15 | A | Well-known fetches, sitemap parser |
| SEO-CRAWL-04 | Sitemap URLs resolve 200 without redirect | M | 10 | A | HEAD/GET sample ≤ 100 sitemap URLs |
| SEO-CRAWL-05 | Sitemap lists canonical URLs (matches rendered canonical, no noindex) | M | 10 | A | Sitemap × crawl cross-reference |
| SEO-CRAWL-06 | Homepage indexable: 200, no noindex, sane canonical | C | 15 | A | Fetch + render homepage, directive intersection |
| SEO-CRAWL-07 | Sampled section roots crawlable (200, follow) | M | 10 | A | Crawler |
| SEO-CRAWL-08 | No orphan high-value pages (in sitemap, zero internal inlinks) | M | 5 | A | Link graph × sitemap diff |
| SEO-CRAWL-09 | Crawl depth sane (≥ 70% of crawled pages within depth 4) | L | 5 | A | BFS depth ledger |
| SEO-CRAWL-10 | Sitemap within limits (≤ 50,000 URLs per file, ≤ 50 MB) | L | 5 | A | Sitemap parser counters |

### 2. Technical foundations (12)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEO-TECH-01 | Sampled pages return 200 (5xx rate < 1%) | C | 25 | A | Crawler status census |
| SEO-TECH-02 | HTTPS sitewide; HTTP 301s to HTTPS | C | 20 | A | Fetcher scheme test, redirect trace |
| SEO-TECH-03 | No mixed content (http subresources on https pages) | H | 10 | A | Rendered network log |
| SEO-TECH-04 | Redirect chains ≤ 2 hops, no loops | M | 15 | A | Redirect tracer (cap 5) |
| SEO-TECH-05 | One canonical host form (www, scheme, case, trailing slash all resolve to one) | H | 15 | A | Variant probe matrix |
| SEO-TECH-06 | Charset declared and bytes decode cleanly | L | 5 | A | Header + meta charset + decoder errors |
| SEO-TECH-07 | URLs clean (lowercase, no spaces or unsafe characters) | L | 5 | A | Crawl URL lint |
| SEO-TECH-08 | Pagination stable (self-canonical per page, finite crawl) | M | 5 | A | Crawler pagination probe (≤ 20 pages) |

### 3. On-page metadata (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEO-META-01 | Unique title on every sampled page | H | 20 | A | DOM extraction + Levenshtein/dup hash |
| SEO-META-02 | Title fits visible width (≤ ~65 chars; rendered truncation check) | M | 10 | A | Title string + rendered pixel measure (renderer) |
| SEO-META-03 | Meta description present on content pages | M | 15 | A | DOM extraction |
| SEO-META-04 | Descriptions unique, 70–160 chars | M | 10 | A | Extraction + dup hash |
| SEO-META-05 | Exactly one non-empty H1 aligned with title | H | 15 | A | DOM extraction |
| SEO-META-06 | Self-referencing absolute canonical on ≥ 95% of content pages | H | 15 | A | DOM extraction |
| SEO-META-07 | No conflicting signals (canonical vs noindex vs redirect vs hreflang) | C | 10 | A | Directive intersection engine |
| SEO-META-08 | OpenGraph title/image present | L | 5 | A | DOM extraction |

### 4. Content depth & uniqueness (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEO-CONT-01 | Median main-content word count ≥ 300 on content pages | M | 20 | A | Boilerplate-removed text extraction |
| SEO-CONT-02 | Thin pages (< 150 words) < 15% of content pages | H | 20 | A | Word-count distribution |
| SEO-CONT-03 | No exact duplicate main content across pages | C | 20 | A | Content hash index |
| SEO-CONT-04 | Near-duplicate share low (SimHash ≥ threshold) | H | 15 | A | SimHash/MinHash (ADR-012) |
| SEO-CONT-05 | Boilerplate ratio sane (template text < 60% of page text) | M | 10 | A | Shared-block analysis across pages |
| SEO-CONT-06 | Content pages use H2/H3 section structure | L | 10 | A | Heading tree |
| SEO-CONT-07 | Main content present in raw HTML (not injected only by JS) | H | 5 | A | Raw vs rendered text delta |

### 5. Internal linking & architecture (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEO-LINK-01 | Mean in-content internal inlinks per content page ≥ 3 | M | 20 | A | Link graph (in-content only) |
| SEO-LINK-02 | Zero broken internal links (4xx/5xx) in sample | C | 25 | A | Link checker with cache, concurrency cap |
| SEO-LINK-03 | No internal links routed through redirect chains | M | 15 | A | Link graph × redirect map |
| SEO-LINK-04 | Primary navigation crawlable in raw HTML | H | 20 | A | Raw DOM link extraction vs rendered |
| SEO-LINK-05 | Generic anchor share ("click here", "read more" bare) < 10% | L | 10 | A | Anchor text census |
| SEO-LINK-06 | Breadcrumbs present on deep pages | L | 10 | A | DOM breadcrumb detection |

### 6. Structured data coverage (8)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEO-SCHEMA-01 | Organization + WebSite markup sitewide | M | 25 | A | JSON-LD/microdata extraction |
| SEO-SCHEMA-02 | Article/BlogPosting on content pages | M | 25 | A | Extraction per page type |
| SEO-SCHEMA-03 | BreadcrumbList on deep pages | L | 15 | A | Extraction |
| SEO-SCHEMA-04 | Markup parses and required properties present (proxy for Tool 6 depth) | M | 25 | A | Bundled Schema.org vocabulary validation |
| SEO-SCHEMA-05 | No conflicting duplicate formats for same entity | L | 10 | A | Entity merge analysis |

### 7. Performance proxies (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEO-PERF-01 | TTFB median of 5 requests < 800 ms | H | 20 | A | Instrumented HTTP timing |
| SEO-PERF-02 | Lab LCP (median of 3 runs) < 2.5 s | H | 25 | A | Self-hosted Lighthouse, fixed emulation profile |
| SEO-PERF-03 | Lab CLS < 0.1 | M | 15 | A | Lighthouse |
| SEO-PERF-04 | Total page weight and request count within budget | M | 15 | A | Network log totals |
| SEO-PERF-05 | Render-blocking resources minimal in head | M | 10 | A | Rendered network waterfall |
| SEO-PERF-06 | Images appropriately sized and formatted | L | 15 | A | Image response analysis (bytes vs rendered size, format) |

### 8. Mobile & UX (5)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEO-MOB-01 | Valid viewport meta | C | 30 | A | DOM extraction |
| SEO-MOB-02 | No horizontal overflow at 390 px | H | 25 | A | Renderer overflow probe (§3.5) |
| SEO-MOB-03 | Tap targets ≥ 24 × 24 px | M | 20 | A | Renderer geometry probe |
| SEO-MOB-04 | Body font ≥ 12 px | L | 15 | A | Computed styles probe |
| SEO-MOB-05 | No intrusive interstitials on content pages | M | 10 | A | Overlay/interstitial detector (§3.5) |

### 9. Security & trust basics (5)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEO-SEC-01 | HSTS with max-age ≥ 15552000 | M | 25 | A | Response header inspection |
| SEO-SEC-02 | Baseline headers (X-Content-Type-Options, Referrer-Policy, frame-ancestors/XFO) | M | 25 | A | Header grader (shared with Tool 9) |
| SEO-SEC-03 | No Server/X-Powered-By version disclosure | L | 15 | A | Header inspection |
| SEO-SEC-04 | Contact/about route discoverable and linked | M | 20 | A | Crawl + link heuristics |
| SEO-SEC-05 | No verbose errors or directory listings on sampled paths | L | 15 | A | Probe + status/body signature check (non-destructive) |

## Tier B panels for this tool

- **PSI + CrUX panel**: lab and field LCP/INP/CLS with collection window; absent CrUX data → "below CrUX reporting threshold"; disabled key → `not_measured` note.
- **Safe Browsing flag**: positive/negative/not_measured badge in trust basics.

## Outputs

Site score with grade and confidence · category bars · issue table by severity ·
page-level sortable/filterable table with CSV export · duplicate title and
description reports · crawl-graph statistics (orphans, depth histogram) ·
prioritized 30-day action plan.
