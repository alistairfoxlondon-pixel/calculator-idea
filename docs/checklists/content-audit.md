# Tool 5 — Content Audit (`/content-audit`)

Intent: "content audit", "thin content checker", "eeat checker".

Scan profile: content-page classification (by URL pattern, markup signals,
and word count), main-content extraction with boilerplate removal, full
deterministic text analysis. No AI, no model, no AI-content detector — the
tool reports observable signals as human-review flags only.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | Depth & distribution | 20 |
| 2 | Thin, duplicate & near-duplicate content | 20 |
| 3 | Readability & structure | 15 |
| 4 | Heading hierarchy & topic alignment | 12 |
| 5 | Freshness & maintenance | 10 |
| 6 | E-E-A-T signals | 15 |
| 7 | Media & alt coverage | 8 |

## Checks

### 1. Depth & distribution (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| CONT-DEPTH-01 | Median main-content words ≥ 300 (content pages) | M | 20 | A | Boilerplate-removed extraction |
| CONT-DEPTH-02 | Content-page share of crawled pages ≥ 50% (rest: utility/nav) | M | 15 | A | Page-type classifier (URL + markup signals) |
| CONT-DEPTH-03 | Word-count distribution reported (p10/p50/p90); long tail not dominated by stubs | M | 15 | A | Distribution math |
| CONT-DEPTH-04 | Topic coverage: distinct primary sections with ≥ 3 substantial pages each | M | 20 | A | Section clustering by URL taxonomy + heading terms |
| CONT-DEPTH-05 | Substantial-page share (≥ 500 words) ≥ 30% | M | 15 | A | Distribution |
| CONT-DEPTH-06 | In-page answer structure present where the page type invites it (summary/TL;DR, definitions) | L | 15 | A | Structure pattern census |

### 2. Thin, duplicate & near-duplicate content (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| CONT-DUP-01 | Exact duplicate main content across URLs | C | 25 | A | Content hash index |
| CONT-DUP-02 | Near-duplicate clusters (SimHash distance ≤ threshold) | H | 25 | A | SimHash + MinHash verification (ADR-012) |
| CONT-DUP-03 | Thin pages (< 150 words main content) share < 15% | H | 20 | A | Word counts |
| CONT-DUP-04 | Boilerplate ratio < 60% of page text on content pages | M | 15 | A | Shared-block analysis |
| CONT-DUP-05 | Repetition flag: repeated near-identical paragraphs within the same page | M | 15 | A | Intra-page shingle census |
| CONT-DUP-06 | Low lexical diversity flag (type–token ratio far below site norm) — human-review flag only, never an auto-fail, never an AI accusation | L | 0 | A | Token statistics; evidence-backed flag with distribution context |

W note: CONT-DUP-06 is advisory with weight 0 in scoring but visible as a
review flag with its evidence. Hard rule: no AI-authorship claim, ever (§3.6).

### 3. Readability & structure (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| CONT-READ-01 | Median Flesch Reading Ease within the site's plausible band (reported with distribution, not moralized) | M | 20 | A | Flesch formula on main text |
| CONT-READ-02 | Median sentence length ≤ 25 words | L | 15 | A | Sentence tokenizer + stats |
| CONT-READ-03 | Paragraph length: < 10% of paragraphs exceed 120 words | L | 15 | A | Paragraph census |
| CONT-READ-04 | Passive-construction share reported as style statistic | I | 0 | A | Deterministic pattern matcher (statistic only) |
| CONT-READ-05 | Lists/tables used where enumerations exist | L | 15 | A | Structure census |
| CONT-READ-06 | No walls of text (median paragraph ≤ 6 sentences) | L | 20 | A | Paragraph census |
| CONT-READ-07 | Spelling-consistency signal: mixed locale spellings of the same words (color/colour) counted | L | 15 | A | Dictionary-table matcher |

### 4. Heading hierarchy & topic alignment (12)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| CONT-HEAD-01 | Single H1 matching the page topic (title/H1 overlap present) | M | 20 | A | Extraction + token overlap |
| CONT-HEAD-02 | No skipped heading levels | M | 20 | A | Heading tree lint |
| CONT-HEAD-03 | H2s present on pages > 500 words | M | 20 | A | Census |
| CONT-HEAD-04 | Question-form headings present where the content answers questions (discoverability signal) | L | 10 | A | Pattern census |
| CONT-HEAD-05 | Heading text meaningful (no empty, no "Section 2", no duplicated heading across pages beyond nav) | L | 15 | A | Heading census + dup index |
| CONT-HEAD-06 | Topic focus consistency: pages stay on their section's topic (term-overlap coherence) | M | 15 | A | Section-term overlap statistics |

### 5. Freshness & maintenance (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| CONT-FRESH-01 | Published dates present on article-type pages | M | 25 | A | DOM patterns + JSON-LD dates |
| CONT-FRESH-02 | Updated dates present and ≥ published where claimed | M | 15 | A | Same |
| CONT-FRESH-03 | Site has content updated within the last 12 months | M | 25 | A | Date census |
| CONT-FRESH-04 | No stale markers ("coming soon", "under construction", future-dated posts, lorem-ipsum strings) | C | 20 | A | Pattern matcher + fixture strings |
| CONT-FRESH-05 | Copyright year current in footer | L | 15 | A | Footer extraction |

### 6. E-E-A-T signals (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| CONT-EEAT-01 | Author bylines present on article pages | H | 20 | A | Byline extraction (DOM + JSON-LD author) |
| CONT-EEAT-02 | Author identity resolvable (author page or bio with credentials) | M | 20 | A | Byline link resolution + bio page census |
| CONT-EEAT-03 | About page states who publishes the site | H | 20 | A | Discovery + content checks (named entity, purpose statement) |
| CONT-EEAT-04 | Outbound citations/sources on factual claim-heavy pages | M | 15 | A | External link census within main content |
| CONT-EEAT-05 | Internal cross-references between related articles | M | 10 | A | In-content link census |
| CONT-EEAT-06 | Editorial policy or review note present where the site publishes advice (health/finance/legal) | M | 15 | A | Policy-page discovery + link checks |

All E-E-A-T findings are framed as observable signals; the report states that
experience and expertise judgments are for human reviewers.

### 7. Media & alt coverage (8)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| CONT-MED-01 | Content images have alt text (≥ 95% of non-decorative images) | H | 35 | A | DOM attribute audit (+ decorative marking check) |
| CONT-MED-02 | Media variety (text-only pages > 2000 words flagged for illustrative media) | L | 15 | A | Media census |
| CONT-MED-03 | Images load (no broken image URLs) | H | 25 | A | Media fetch + renderer errors |
| CONT-MED-04 | Video embeds have titles/fallbacks where applicable | L | 10 | A | Embed audit |
| CONT-MED-05 | Figures/captions used where media carries information | L | 15 | A | Structure census |

## Framing rules enforced in copy

- Thin, duplicate, and repetition findings cite their evidence (counts, URLs,
  hashes, distributions).
- No statement about whether text was human- or machine-written. The tool has
  no such detector by design (§3.6 hard rule).
- Readability and lexical statistics are reported as measurements, never as
  quality verdicts.

## Tier B panels for this tool

None required. (GA4/Search Console panels could show engagement per page for
account holders; that is Phase 9 account sugar, not part of the score.)

## Outputs

Word-count distribution chart · duplicate-cluster viewer with side-by-side
evidence · per-page content table (words, readability, flags) · E-E-A-T signal
inventory · prioritized content actions (expand, consolidate, prune, add
authorship).
