# SiteProof — Check Inventory (Phase 0)

The complete, tool-by-tool list of every check the platform performs, with
weights, severities, data availability tier, and the data path that produces
the evidence. This is the Phase 0 exit deliverable requiring owner sign-off.
Per-tool files live in `docs/checklists/{tool-slug}.md`.

## Reading a check row

Every check is identified as `{TOOL}-{CAT}-{NN}` where CAT is a 3–5 letter
category code. Columns:

- **ID** — stable for the life of the product; rulepack check IDs match.
- **Check** — plain-English name (one canonical name reused in UI, report, and methodology).
- **Cat** — scoring category, with the category weight in the tool header table. Check weights are relative within a category and normalized to 100 inside it.
- **Sev** — severity applied when the check fails: C critical, H high, M medium, L low, I info. Warn severity is one step below the listed fail severity where a warn state exists.
- **Tier** — A: always available, no keys, no cost (our own engines); B: optional free provider, graceful `not_measured` fallback. A tool's score never depends on Tier B.
- **Data path** — the concrete engine or source that produces the evidence.

## Global definitions

- Statuses: `pass`, `warn`, `fail`, `not_applicable`, `not_measured`, `unverified` (§3.1, §4.2).
- `not_measured` excludes the check's weight from the applicable denominator. `unverified` is reported and shown but never scored.
- Grades: A+ ≥ 95, A 90–94, B 80–89, C 70–79, D 60–69, F < 60. The raw score, grade, and confidence are always shown together.
- Confidence: High / Moderate / Low from coverage, evidence completeness, renderer success, measurement stability, and sample size, with the reason stated in plain English.
- Provenance labels: `Measured by us`, `Lab-measured`, `Server-measured`, `Provider data`, `Owner-reported`, `Not measured` — always with a timestamp.

## Caps registry (declared in rulepack meta, never buried in code)

| Cap ID | Condition | Effect |
| --- | --- | --- |
| CAP-SEO-01 | robots.txt disallows all crawling of content | SEO score capped at 40 |
| CAP-TSEO-01 | Content pages return 4xx/5xx or carry noindex on the homepage | Technical SEO capped at 45 |
| CAP-ADS-01 | Privacy Policy page missing or not linked site-wide | AdSense capped at 55 |
| CAP-ADS-02 | Prohibited-content hit at critical severity (per §3.13 categories) | AdSense capped at 35 |
| CAP-SEC-01 | HTTPS unavailable or certificate invalid | Security capped at 30 |
| CAP-MLW-01 | Active DNS blocklist hit on the domain | Malware capped at 40 |
| CAP-EMAIL-01 | No MX records while A-record mail is used implicitly | Email capped at 50 |
| CAP-PRIV-01 | Third-party trackers fire before any consent interaction and no banner exists | Privacy capped at 40 |

## Per-tool files

1. `docs/checklists/seo-audit.md` — SEO Audit
2. `docs/checklists/technical-seo-audit.md` — Technical SEO Audit
3. `docs/checklists/speed-audit.md` — Speed Audit
4. `docs/checklists/mobile-audit.md` — Mobile Audit
5. `docs/checklists/content-audit.md` — Content Audit
6. `docs/checklists/schema-audit.md` — Schema Audit
7. `docs/checklists/link-audit.md` — Link Audit
8. `docs/checklists/accessibility-audit.md` — Accessibility Audit
9. `docs/checklists/security-audit.md` — Security Audit
10. `docs/checklists/malware-audit.md` — Malware Audit
11. `docs/checklists/email-audit.md` — Email Audit
12. `docs/checklists/adsense-audit.md` — AdSense Audit
13. `docs/checklists/ad-network-audit.md` — Ad Network Audit
14. `docs/checklists/privacy-audit.md` — Privacy Audit
15. `docs/checklists/ai-visibility-audit.md` — AI Visibility Audit

## Tier A completeness statement

Every check in every file is achievable with Tier A alone. The data paths use
only: our HTTP client with timing instrumentation, our crawler (robots-aware,
politeness-limited), the self-hosted renderer (Playwright/Chromium: rendered
DOM, screenshots, network log, console, overflow probes), self-hosted
Lighthouse, self-hosted axe-core, self-hosted Nu HTML checker, our DNS
resolver, our TLS/openssl inspection, well-known file fetches, and local
deterministic algorithms (shingling, SimHash/MinHash, readability formulas,
link-graph math, lexicon matching with context extraction). No check requires
a key, a paid service, or a model.

Tier B additions (PSI, CrUX, Safe Browsing, URLhaus/OpenPhish, Search Console
OAuth, GA4 OAuth, RDAP, CT logs) only enrich specific panels, are labelled
with provider and timestamp, and degrade to `not_measured` with an explicit
note. The per-tool files list exactly which panels are affected.

## Shared cross-tool checks (run once per scan, surfaced by every tool)

| ID | Check | Sev | Data path |
| --- | --- | --- | --- |
| SHARED-URL-01 | URL normalizes to http(s), valid host, no credentials, IDN→punycode | — | Normalizer (fail-fast on invalid input, not scored) |
| SHARED-SSRF-01 | Target resolves to a public, scannable address | — | SSRF guard (blocks the scan with a clear message, not scored) |
| SHARED-BOT-01 | Target serves a bot-protection challenge to our renderer | — | Challenge fingerprint library; sets `unverified` on affected checks per §4.10 |
| SHARED-COV-01 | Crawl coverage fraction (crawled / discovered) feeds confidence | — | Crawler ledger |

## Change process

A check may be added, reweighted, or reworded only through a rulepack version
bump: new `v{n}` directory, `meta.json` changelog entry, updated golden
fixtures where scores change, and CI green on determinism, drift, and
portability suites. Historic reports keep their rulepack version and are never
re-evaluated.
