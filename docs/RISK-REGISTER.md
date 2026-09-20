# SiteProof — Risk Register (Phase 0)

Risk scoring: likelihood and impact on a 1–5 scale; exposure = L × I.
Every risk has an owner phase and a named mitigation that is built, not aspirational.

| ID | Risk | L | I | Exp | Phase | Mitigation (built) |
| --- | --- | --- | --- | --- | --- | --- |
| R-01 | SSRF: user-supplied URLs used to reach internal infrastructure, cloud metadata, or private ranges | 4 | 5 | 20 | 2 | Resolve–validate–pin fetching (ADR-011), blocklist of all special-purpose ranges, port allowlist (80/443), redirect re-validation, egress firewall, dedicated automated bypass suite in CI |
| R-02 | Capacity abuse: scan farming, queue starvation by one actor | 4 | 4 | 16 | 2, 9 | Per-IP / per-account / global quotas, honeypot + proof-of-work challenge, per-host politeness, fair queueing, daily capacity guard that degrades gracefully |
| R-03 | Free-provider quota exhaustion (PSI, CrUX, Safe Browsing) degrading results | 3 | 3 | 9 | 2 | Provider driver interface with pacing, cache TTLs, hard daily caps, circuit breaker, `not_measured` fallback so no tool fails without them |
| R-04 | Legal: scanning third-party sites without authorization | 3 | 4 | 12 | 1, 10 | Terms require ownership/authorization, honest user agent, robots.txt respected, published "How we scan" page, per-host rate limits, stop-on-request route |
| R-05 | Legal: trademark and policy-claim exposure (AdSense, ad networks, Google) | 3 | 4 | 12 | 7, 8 | Nominative use only, no third-party marks in brand/domain, every claim mapped to a public source URL, visible non-affiliation disclaimers, no guarantee language (CI copy lint for banned claim phrases) |
| R-06 | Renderer memory exhaustion (Chromium) taking down scans or the host | 4 | 4 | 16 | 3, 6 | Separate sidecar container with hard memory/CPU caps, per-job timeouts, bounded concurrent render pool separate from crawl workers, queue backpressure |
| R-07 | Wrong or unverifiable scores damaging trust | 3 | 5 | 15 | 2, all | Evidence-or-nothing contract, golden-score fixture corpus in CI, determinism tests, confidence labels, provenance labels, calibration dataset post-launch |
| R-08 | Data protection: storing third-party page content, user data | 3 | 4 | 12 | 2, 9 | Retention limits (raw HTML 30 d, screenshots 90 d), account/artifact deletion within 30 d, no personal-data crawling, no third-party HTML rendered unsanitized |
| R-09 | MariaDB/MySQL drift breaking production | 2 | 4 | 8 | 1 | Portability rules (ADR-009), CI runs the migration and query suite on both engines |
| R-10 | Brand-name collision ("SiteProof" in use by a Deveras scanner and a UK utilities firm) | 3 | 3 | 9 | 0 | Working name isolated in `config/brand.php`; rename is one commit; trademark counsel review recommended before launch (see `docs/VARIABLES.md`) |
| R-11 | Hostile targets: bot-protection challenges, geo-blocks, cloaking causing false findings | 4 | 3 | 12 | 2 | Challenge fingerprint detection, raw-vs-rendered comparison, `unverified` status instead of false failure (§4.10 anti-false-positive pass) |
| R-12 | Supply-chain compromise of dependencies (Composer, npm, Chromium) | 2 | 4 | 8 | 1, 10 | Version pinning, `composer audit` + `npm audit` + Trivy in CI, lockfile review, no post-install scripts where avoidable |
| R-13 | Scoring drift over time as the web changes (new defaults, deprecated features) | 3 | 3 | 9 | ongoing | Versioned rulepacks with changelogs, network profiles with `last_verified_at` and review cadence, quarterly rulepack review in runbook |
| R-14 | Outbound scanning seen as malicious by target hosts (IP reputation damage) | 3 | 3 | 9 | 2, 10 | Honest user agent with bot info page, politeness delays, response to abuse complaints, documented crawl caps, ability to blocklist abusive requesters of our own site |
| R-15 | Free OAuth integrations (Search Console, GA4) mishandling owner tokens | 2 | 4 | 8 | 9 | Encrypted token storage (SecretBox), minimal scopes, explicit owner consent flow, revocation UI, tokens never logged |
