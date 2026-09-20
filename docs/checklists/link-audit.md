# Tool 7 — Link Audit (`/link-audit`)

Intent: "broken link checker", "internal link audit", "redirect checker".

Scan profile: full link graph from the crawl (in-content and structural links
tagged separately) · every unique link target verified with a concurrency-
limited, cached checker (per-host politeness, HEAD with GET fallback,
status-only, max 5 redirects) · soft-404 heuristics on suspicious 200s.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | Internal link graph health | 25 |
| 2 | Broken links (4xx/5xx) | 25 |
| 3 | Redirect chains & loops | 15 |
| 4 | Orphans & click depth | 15 |
| 5 | Anchor quality | 10 |
| 6 | External link hygiene | 10 |

## Checks

### 1. Internal link graph health (25)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| LINK-GRAPH-01 | Mean in-content internal inlinks per content page ≥ 3 | M | 20 | A | Link graph |
| LINK-GRAPH-02 | No dead-end content pages (zero internal outlinks) | M | 15 | A | Graph out-degree |
| LINK-GRAPH-03 | Section hubs link to their children (hub-and-spoke present) | M | 15 | A | URL taxonomy × graph |
| LINK-GRAPH-04 | Navigation present on all sampled pages (consistent global nav) | H | 15 | A | Link-set comparison per page |
| LINK-GRAPH-05 | Footer/header links resolve and are consistent sitewide | L | 10 | A | Crawl |
| LINK-GRAPH-06 | Links point to canonical URLs (not to redirects, not to duplicate variants) | H | 25 | A | Graph × canonical/redirect map |

### 2. Broken links (25)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| LINK-BROK-01 | Zero broken internal links (4xx/5xx), each with source page evidence | C | 40 | A | Status checker with cache |
| LINK-BROK-02 | Zero broken images/media referenced in content | H | 20 | A | Media fetch census |
| LINK-BROK-03 | External 4xx rate < 2% of unique external targets | M | 25 | A | External checker (bounded sample for large sites, cap stated) |
| LINK-BROK-04 | External 5xx/timeouts distinguished from true 4xx (retry policy, reported separately) | L | 15 | A | Checker retry ledger |
| LINK-BROK-05 | Broken-link CSV export: source URL, target, anchor, status, error class | I | 0 | A | Report artifact |
| LINK-BROK-06 | Links verified as temporarily failing (5xx/429) flagged `unverified` rather than failed, with retry evidence | I | 0 | A | Retry outcomes |

### 3. Redirect chains & loops (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| LINK-RED-01 | No internal links routed through redirects (update to direct target) | M | 35 | A | Graph × redirect map |
| LINK-RED-02 | Chains longer than 2 hops enumerated with corrected direct target | M | 25 | A | Tracer |
| LINK-RED-03 | No redirect loops | C | 25 | A | Tracer loop detection |
| LINK-RED-04 | Redirected links to cross-domain destinations flagged for review | M | 15 | A | Tracer host diff |

### 4. Orphans & click depth (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| LINK-DEP-01 | No orphan pages in sitemap (zero internal inlinks) | M | 40 | A | Sitemap × graph |
| LINK-DEP-02 | ≥ 70% of content within 4 clicks of homepage | M | 35 | A | BFS depth census |
| LINK-DEP-03 | Deepest content paths reported with the chain | I | 0 | A | Depth tree |
| LINK-DEP-04 | Pagination depth sane (list pages reachable, not JS-only infinite scroll) | L | 25 | A | Crawl + pagination probe |
| LINK-DEP-05 | Important pages (most inlinked externals? no — most-linked internal targets) surfaced in report for nav placement advice | I | 0 | A | Graph centrality (degree + betweenness proxy) |

### 5. Anchor quality (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| LINK-ANCH-01 | Generic anchor share ("click here", "read more", bare URL) < 10% | L | 25 | A | Anchor census |
| LINK-ANCH-02 | No over-optimized pattern (single exact-match phrase dominating a target's anchors) | M | 25 | A | Anchor-target distribution |
| LINK-ANCH-03 | Anchors describe destination (anchor tokens overlap target title/topic) | L | 20 | A | Token overlap |
| LINK-ANCH-04 | Image links carry alt text | M | 15 | A | DOM audit |
| LINK-ANCH-05 | Empty anchors (no text, no alt) absent | M | 15 | A | DOM audit |

### 6. External link hygiene (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| LINK-EXT-01 | Monetized/affiliate-style external links carry rel=sponsored or nofollow | M | 25 | A | Attribute census + destination classification (pattern-based) |
| LINK-EXT-02 | User-generated content links carry rel=ugc where UGC exists | L | 15 | A | UGC section detection × attributes |
| LINK-EXT-03 | No mixed-content links (http targets from https pages) | M | 20 | A | Scheme audit |
| LINK-EXT-04 | Links to non-HTML assets declare type where relevant (downloads not surprising) | L | 10 | A | Content-type probe |
| LINK-EXT-05 | External target host reputation spot-check: no links to known threat-feed domains (Tier B when enabled; else skipped with note) | M | 15 | B (fallback: skip, `not_measured`) | Threat feed match on external target hosts |
| LINK-EXT-06 | External links use reasonable target behavior (no force-new-window on every link without signal) | L | 15 | A | Attribute census |

## Politeness rules

External verification: max 2 concurrent per target host, global cap on unique
external hosts per scan, HEAD-first with GET fallback only when status is
ambiguous, results cached sitewide for the scan, Retry-After honored.
Verification is status-level only; no content harvesting from third parties.

## Tier B panels for this tool

- **Threat-feed match on external targets** (URLhaus/OpenPhish host lists)
  when enabled; otherwise the check reports `not_measured` with a note. Score
  impact only through LINK-EXT-05's Tier B slot; with the feed disabled the
  check's weight is excluded from the denominator.

## Outputs

Full link map (interactive) · broken-link CSV with source pages · redirect
chain table with corrected targets · orphan and depth reports · anchor
distribution · external hygiene report.
