# Tool 15 — AI Visibility Audit (`/ai-visibility-audit`)

Intent: "geo audit", "aeo audit", "llms.txt checker", "ai crawler checker".

Scope note (shown on the page): this tool audits how other companies' AI
systems can reach, read, and cite the site. It performs no AI inference, calls
no model, and costs nothing to run. Being crawlable by AI systems and using AI
are different things; the product does the former, never the latter.

Profile: robots.txt parsed against a bundled, versioned bot list
(`config/bots.php` with per-bot names, tokens, and operator URLs); llms.txt
discovery and validation; raw-vs-rendered extraction comparisons; answer-first
structure census; entity analysis from structured data; freshness from dates
and sitemap lastmod.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | AI crawler access | 20 |
| 2 | llms.txt & well-known signals | 12 |
| 3 | Entity & structured-data clarity | 18 |
| 4 | Extractability & answer-first structure | 20 |
| 5 | Citation & authority signals | 15 |
| 6 | No-JavaScript renderability | 10 |
| 7 | Freshness | 5 |

## Checks

### 1. AI crawler access (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| AIV-BOT-01 | Per-bot access matrix computed for every bot in the versioned list (GPTBot, OAI-SearchBot, ChatGPT-User, ClaudeBot, Claude-SearchBot, Claude-User, PerplexityBot, Perplexity-User, Google-Extended, Applebot-Extended, CCBot, Bytespider, Amazonbot, Meta-ExternalAgent, cohere-ai, MistralAI-User, and maintained additions) with the exact matched directive and line number | I | 0 | A | robots parser × bot list simulation |
| AIV-BOT-02 | Key content paths reachable by at least the major search-AI crawlers (per matrix, per path class) | H | 40 | A | Matrix × path-class rules |
| AIV-BOT-03 | Intentional blocks declared clearly (site owner's choice is legitimate; the audit reports effect, not morality) | I | 0 | A | Matrix |
| AIV-BOT-04 | No blanket `Disallow: /` blocking all AI crawlers unintentionally (distinguish user-agent-specific vs global rules) | C | 30 | A | Parser structure analysis |
| AIV-BOT-05 | Crawl-delay/rate directives for AI bots noted (not scored; informational with effect explanation) | I | 0 | A | Parser |
| AIV-BOT-06 | Server-level blocks (403/429 to our crawler on sampled paths) distinguished from robots-level blocks, with both reported | M | 30 | A | Fetch status census per path class |

### 2. llms.txt & well-known signals (12)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| AIV-LLM-01 | `/llms.txt` present | M | 35 | A | Well-known fetch |
| AIV-LLM-02 | llms.txt structure valid (H1 site name, blockquote summary, sections with link lists per the draft convention; versioned validator) | M | 25 | A | Validator |
| AIV-LLM-03 | Linked resources in llms.txt resolve 200 | M | 20 | A | Link checker |
| AIV-LLM-04 | `/llms-full.txt` present and consistent with llms.txt links (when llms.txt declares it) | L | 20 | A | Fetch + consistency check |
| AIV-LLM-05 | Markdown variants discoverable where served (`.md` routes for content) — informational, growing convention | I | 0 | A | HEAD probes on content URLs (bounded) |

### 3. Entity & structured-data clarity (18)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| AIV-ENT-01 | Organization/Brand entity with name, url, logo, sameAs | M | 25 | A | Structured-data extraction |
| AIV-ENT-02 | Author entities (Person) with name, url, sameAs on content | M | 20 | A | Extraction |
| AIV-ENT-03 | Product/Service entities where applicable with coherent properties | M | 15 | A | Extraction + applicability rules |
| AIV-ENT-04 | sameAs targets resolve and point to authoritative profiles | M | 15 | A | Link checker |
| AIV-ENT-05 | Site name unambiguous (consistent WebSite name, og:site_name, title suffix) | M | 15 | A | Cross-surface name comparison |
| AIV-ENT-06 | About/contact pages reinforce the entity (matching names, addresses) | L | 10 | A | Content comparison |

### 4. Extractability & answer-first structure (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| AIV-EXT-01 | Main content present in raw HTML (raw/rendered main-text ratio ≥ 0.9 — shared measurement with TSEO-REND-01) | C | 30 | A | Raw vs rendered extraction |
| AIV-EXT-02 | Answer-first openings: content pages open with a definitional/summary paragraph (first N sentences standalone-parse; sentence-subject/topic overlap with title) | M | 20 | A | Deterministic sentence-structure census (definition patterns "X is/are/refers to", summary positioning) |
| AIV-EXT-03 | Question-form headings present where content answers questions | L | 10 | A | Heading census |
| AIV-EXT-04 | Lists and tables carry extractable structure (real `<ul>/<ol>/<table>` semantics, not images) | M | 15 | A | Structure census |
| AIV-EXT-05 | Content-to-boilerplate ratio favorable for extraction (shared boilerplate analysis) | M | 15 | A | Extraction stats |
| AIV-EXT-06 | No crawler traps on content paths (infinite parameter spaces, session-dependent content) | M | 10 | A | Parameter probe + content-hash stability |

### 5. Citation & authority signals (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| AIV-CIT-01 | Original data/statistics present with dates (tables, figures with source notes) | M | 25 | A | Structure + date census |
| AIV-CIT-02 | Sources cited via links on factual pages | M | 25 | A | External-link census in main content |
| AIV-CIT-03 | Author expertise signals (credentials, about links) | M | 20 | A | Shared E-E-A-T measurements |
| AIV-CIT-04 | Canonical and indexability sanity for citable pages | M | 20 | A | Shared directive engine |
| AIV-CIT-05 | Named-entity consistency (site/person/product names stable across pages) | L | 10 | A | Entity-name frequency/stability census |

### 6. No-JavaScript renderability (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| AIV-NOJS-01 | Main text readable with JavaScript disabled (raw HTML carries it; shared measurement) | H | 40 | A | Raw text extraction |
| AIV-NOJS-02 | Navigation links present without JS (crawlability without rendering) | H | 25 | A | Raw link extraction |
| AIV-NOJS-03 | Critical media has non-JS fallbacks (img with src, video with poster/source) | M | 20 | A | Media attribute audit |
| AIV-NOJS-04 | No JS-required content walls on content pages (empty shells without scripts) | M | 15 | A | Raw-vs-rendered ratio + empty-shell detector |

### 7. Freshness (5)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| AIV-FRESH-01 | Sitemap lastmod values present, valid, and recent where content is active | M | 40 | A | Sitemap parser + date math |
| AIV-FRESH-02 | Visible/structured dates present and consistent (published/updated) | M | 35 | A | Shared date extraction |
| AIV-FRESH-03 | Stale-content ratio sane (share of content untouched > 24 months, where dates exist) | L | 25 | A | Date distribution |

## Deterministic generation deliverables

- **llms.txt draft**: generated from the site's own structure (title, purpose
  sentence from about/home extraction, section list, top content URLs with
  their own titles) by rule templates. Original composition; never copied from
  any reference site. Delivered as a copyable artifact with placement notes.
- **Per-bot access table**: full matrix with exact directive evidence, ready
  to act on (the fix is the site's robots.txt edit, shown as a diff template
  from their current file).

## Tier B panels for this tool

None required. Everything runs on our own fetcher, parser, and renderer.

## Outputs

Per-bot access matrix with directives · llms.txt status and generated draft ·
extractability report with raw/rendered ratios per page · answer-first census
· entity report · freshness report · prioritized fixes (robots edits, llms.txt
placement, raw-content moves, entity markup additions).
