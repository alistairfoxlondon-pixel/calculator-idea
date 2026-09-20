# Tool 2 — Technical SEO Audit (`/technical-seo-audit`)

Intent: "technical seo audit", "indexability checker", "robots.txt checker",
"sitemap checker", "noindex checker".

Scan profile: robots parsing and per-crawler simulation · sitemap validation ·
raw-vs-rendered diff on 10 URLs · redirect tracing on all sampled URLs · header
inspection on every fetch · vnu validation on the rendered sample · Search
Console coverage panel only when the owner connects it (Tier B, labelled
"From your Search Console", never affects the score).

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | Crawl access & robots | 15 |
| 2 | Sitemap health | 12 |
| 3 | Canonicalization & duplication | 15 |
| 4 | Index directives | 12 |
| 5 | Status codes & redirect hygiene | 10 |
| 6 | URL structure, HTTPS, parameters | 8 |
| 7 | Rendering & JavaScript crawlability | 15 |
| 8 | Hreflang & internationalization | 8 |
| 9 | Markup validity & hygiene | 5 |

Cap: CAP-TSEO-01 (content pages 4xx/5xx or homepage noindex → capped at 45).

## Checks

### 1. Crawl access & robots (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| TSEO-ROB-01 | robots.txt returns 200 with parseable content (5xx → allow + flag) | C | 15 | A | Fetch + parser |
| TSEO-ROB-02 | Per-agent simulation: Googlebot can reach sampled content URLs | C | 25 | A | Directive matcher per user-agent token |
| TSEO-ROB-03 | Per-agent simulation: Bingbot can reach sampled content URLs | M | 10 | A | Same engine |
| TSEO-ROB-04 | No `Disallow: /` for all agents | C | 20 | A | Parser |
| TSEO-ROB-05 | Crawl-delay, if present, ≤ 10 s (or absent) | L | 5 | A | Parser |
| TSEO-ROB-06 | Clean-param/param directives sane | L | 5 | A | Parser |
| TSEO-ROB-07 | Sitemap directives point to same-origin reachable files | M | 10 | A | Parser + fetch |
| TSEO-ROB-08 | No contradictory Allow/Disallow on key paths (longest-match audit) | M | 10 | A | Match trace per URL |

### 2. Sitemap health (12)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| TSEO-SMAP-01 | Sitemap found via robots or standard path | M | 15 | A | Well-known fetches |
| TSEO-SMAP-02 | Valid XML namespace, escapes, and structure (per file, index recursion capped) | H | 20 | A | Strict XML parser |
| TSEO-SMAP-03 | URL count per file within limits; index structure correct | M | 10 | A | Parser counters |
| TSEO-SMAP-04 | No 404/5xx/redirect URLs in sitemap sample | H | 20 | A | Status sample ≤ 100 URLs |
| TSEO-SMAP-05 | lastmod values present, valid, not all identical/future | L | 10 | A | Parser + date validation |
| TSEO-SMAP-06 | No off-domain URLs unless cross-sitemap declared | M | 10 | A | Host comparison |
| TSEO-SMAP-07 | No noindex or canonical-off URLs listed | H | 15 | A | Cross-reference with crawl directives |

### 3. Canonicalization & duplication (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| TSEO-CANON-01 | Canonical present on content pages, absolute URL | H | 20 | A | DOM extraction |
| TSEO-CANON-02 | Canonical self-referencing on ≥ 95% of content pages | M | 15 | A | Extraction comparison |
| TSEO-CANON-03 | Canonical chains resolve in ≤ 1 hop (canonical → 200, not another canonical) | C | 20 | A | Canonical fetch trace |
| TSEO-CANON-04 | www/scheme/trailing-slash variants return one canonical form | H | 15 | A | Variant probe matrix |
| TSEO-CANON-05 | Parameterized variants canonicalize to clean URL (pagination, sort, utm) | M | 15 | A | Parameter probes (≤ 10 variants) |
| TSEO-CANON-06 | Near-duplicate pages carry distinct canonicals or consolidation | M | 15 | A | SimHash clusters × canonical map |

### 4. Index directives (12)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| TSEO-IDX-01 | Homepage indexable (no noindex via meta or header) | C | 20 | A | Meta robots + X-Robots-Tag extraction |
| TSEO-IDX-02 | Content pages indexable in sample | C | 25 | A | Same |
| TSEO-IDX-03 | Utility pages (search, cart, tag walls) intentionally noindexed where appropriate | L | 10 | A | Directive + page-type classification |
| TSEO-IDX-04 | robots meta syntax valid (known tokens only) | M | 10 | A | Token parser |
| TSEO-IDX-05 | X-Robots-Tag consistent with meta robots (no conflicts) | H | 15 | A | Header × meta comparison |
| TSEO-IDX-06 | noindex pages are not in sitemap and not linked sitewide as primary content | M | 10 | A | Cross-reference |
| TSEO-IDX-07 | Nofollow directives scoped and intentional | L | 10 | A | Link attribute census |

### 5. Status codes & redirect hygiene (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| TSEO-STAT-01 | Content pages 200 in sample | C | 25 | A | Crawler census |
| TSEO-STAT-02 | 404s return 404 (no soft-404 signatures: thin/boilerplate-only success pages) | H | 15 | A | Probe random paths + content classifier |
| TSEO-STAT-03 | Redirect chains ≤ 2 hops | M | 20 | A | Redirect tracer |
| TSEO-STAT-04 | No redirect loops | C | 15 | A | Tracer loop detection |
| TSEO-STAT-05 | HTTP→HTTPS and host variants are single-hop 301/308 | M | 15 | A | Variant probes |
| TSEO-STAT-06 | No 302/307 used for permanent moves | L | 10 | A | Tracer status audit |

### 6. URL structure, HTTPS, parameters (8)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| TSEO-URL-01 | HTTPS everywhere including subresources | H | 25 | A | Fetch + network log |
| TSEO-URL-02 | HSTS present | M | 15 | A | Header inspection |
| TSEO-URL-03 | URLs readable (no session IDs, no excess depth, sane length ≤ ~120 chars for content) | L | 20 | A | URL lint census |
| TSEO-URL-04 | Tracking parameters not indexed (canonical/noindex behavior on utm variants) | M | 15 | A | Parameter probes |
| TSEO-URL-05 | Case sensitivity handled (case variants redirect or canonicalize) | L | 15 | A | Variant probes |
| TSEO-URL-06 | Trailing-slash policy consistent across sections | L | 10 | A | Variant probes |

### 7. Rendering & JavaScript crawlability (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| TSEO-REND-01 | Primary content present in raw HTML (raw/main-text vs rendered/main-text ratio ≥ 0.9) | C | 30 | A | Raw fetch vs rendered DOM text diff |
| TSEO-REND-02 | Navigation links present in raw HTML | H | 20 | A | Raw vs rendered link-set diff |
| TSEO-REND-03 | Meta title/description identical raw vs rendered (no JS rewrite) | M | 15 | A | Head diff |
| TSEO-REND-04 | No JS-only infinite-scroll content without pagination URLs | M | 15 | A | Scroll probe + URL census |
| TSEO-REND-05 | Client-side redirects not required for entry URLs | H | 10 | A | Render-time navigation monitor |
| TSEO-REND-06 | Rendered page reaches contentful state < 10 s (no perpetual spinners) | M | 10 | A | Renderer stability probe |

### 8. Hreflang & internationalization (8)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| TSEO-HREF-01 | If hreflang present: codes valid (language[-region]) | M | 20 | A | Attribute/XML parser + registry table |
| TSEO-HREF-02 | Return links exist (each hreflang pair is reciprocal) | H | 25 | A | Cross-page extraction |
| TSEO-HREF-03 | x-default present when multiple languages | L | 15 | A | Extraction |
| TSEO-HREF-04 | Hreflang targets resolve 200 and are canonical | H | 25 | A | Target fetches |
| TSEO-HREF-05 | Single implementation channel (link tags or sitemap, not conflicting both) | L | 15 | A | Source diff |

Not applicable (N/A) when the site is single-language: category excluded from
the denominator, stated in the report.

### 9. Markup validity & hygiene (5)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| TSEO-VAL-01 | No vnu errors on the rendered sample (warnings informational) | M | 40 | A | Self-hosted vnu |
| TSEO-VAL-02 | Duplicate IDs absent | M | 20 | A | vnu + DOM lint |
| TSEO-VAL-03 | Malformed nesting absent (unclosed tags causing dropped content) | H | 25 | A | Parser error census |
| TSEO-VAL-04 | Encoding declared and consistent (no mixed encodings) | L | 15 | A | Byte-level inspection |

## Tier B panels for this tool

- **Search Console coverage panel**: owner-consented OAuth; clearly labelled
  "From your Search Console"; shown alongside, never merged into the score;
  absent connection → panel explains how to connect.

## Outputs

Indexability verdict per sampled URL (robots × meta × header × canonical ×
status intersection, with the exact directive evidence) · raw-vs-rendered diff
viewer · redirect map · sitemap audit table · vnu error list · hreflang
reciprocity table.
