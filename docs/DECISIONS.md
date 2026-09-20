# SiteProof — Decision Log (ADRs)

Format: each record states status, context, decision, and consequences.
Versions are indicative until pinned at scaffold time; the pinned value is
recorded here when Phase 1 completes.

## ADR-001 — Framework and runtime: Laravel 13.x on PHP 8.4

- Status: Accepted (2026-09-20)
- Context: The prompt targets Laravel 13.x on PHP 8.4 if the host supports it.
  Research on 2026-09-20 confirms Laravel 13 is the current major (released
  February/March 2026) and PHP 8.4 is the recommended runtime; third-party
  sources disagree on whether the hard minimum is 8.3 or 8.4, so the exact
  constraint is pinned from packagist metadata at scaffold time, not from
  memory or third-party posts.
- Decision: Laravel 13.x, PHP 8.4. The sandbox installs PHP 8.4 via a current
  package repository; the Docker image pins the same version.
- Consequences: If a deployment host cannot run PHP 8.4, the prompt requires
  falling back to the newest Laravel its PHP supports; that fallback changes
  `composer.json` only, because no Laravel-13-only APIs are used when a
  portable alternative exists.

## ADR-002 — Renderer as a separate Node sidecar service

- Status: Accepted
- Context: Rendering, Lighthouse, axe-core, and HTML validation are memory-
  and CPU-bound; embedding them in PHP workers couples crash domains.
- Decision: A Node 22 service (`renderer/`) exposing a private HTTP API:
  Playwright with bundled Chromium (render, screenshots, overflow probing,
  network log, console capture), Lighthouse (npm version pinned), axe-core
  (pinned), and the Nu HTML checker (vnu) via a local Java runtime or native
  build. Internal shared-secret auth, non-root, read-only filesystem, hard
  per-job timeouts, bounded memory (1.5–2 GB per concurrent session).
- Consequences: Deployment has one more container; crash and memory risk is
  isolated; PHP workers never block on Chromium. If the sidecar is down, tools
  degrade to raw-HTML checks with `not_measured` labels and a visible banner.

## ADR-003 — Rulepacks as versioned PHP arrays in `resources/rulepacks/`

- Status: Accepted
- Context: Weights, severities, messages, and fixes must be versioned, review-
  able, and never retroactively rewrite old reports; a database-only home makes
  review and CI hard.
- Decision: `resources/rulepacks/{tool}/v{n}/{checks.php, weights.php,
  meta.json}`. The loader validates structure, computes a content hash, and
  stamps reports with the rulepack version and hash. Changing a rule requires
  a version bump; CI fails if scores of golden fixtures change without one.
- Consequences: Rule logic is code-reviewable and diffable; historic reports
  stay reproducible; adding a check is a PR with tests, not a DB migration.

## ADR-004 — Scoring model: pure, weighted, capped, fingerprinted

- Status: Accepted
- Context: §4 requires determinism and traceability.
- Decision: `category_score = 100 × earned/applicable`; `tool_score =
  Σ(category_weight × category_score)/Σ(applicable category weights)`. Caps are
  declared in rulepack `meta.json` (never buried in code). The result
  fingerprint is `sha256(normalized inputs + rulepack version + sorted evidence
  hashes)`. Rounding never crosses a grade boundary upward.
- Consequences: Same evidence + same rulepack version = same score, proven by
  a CI test that runs each fixture twice and compares full output.

## ADR-005 — Artifact storage: S3-compatible object storage (MinIO default)

- Status: Accepted
- Context: Screenshots, PDFs, raw-HTML snapshots, and JSON bundles need
  lifecycle rules and signed access.
- Decision: S3-compatible storage via MinIO in Compose (swap to any S3
  endpoint by env). Signed URLs only; lifecycle: raw HTML ≤ 30 d, screenshots
  ≤ 90 d, evidence extracts retained with the report, deletion within 30 d of
  an account-closure request.
- Consequences: No filesystem coupling; retention is enforced by a scheduled
  job plus bucket lifecycle rules, both tested.

## ADR-006 — Queues: Redis + Horizon with isolated worker pools

- Status: Accepted
- Context: A heavy render must never starve link checking.
- Decision: Queues `crawl`, `render`, `lighthouse`, `analysis`, `reports`,
  `emails`, `maintenance`. Render and Lighthouse run on their own worker pool
  with a bounded concurrency matching renderer capacity.
- Consequences: Fair, predictable scan latency; sizing formula published in
  `docs/RUNBOOK.md` (scans/hour × jobs/scan × mean duration).

## ADR-007 — Live progress over server-sent events (SSE), not WebSockets

- Status: Accepted
- Context: §6.1 allows Reverb or SSE. Scans are one-directional progress
  streams; SSE needs no websocket upgrade, survives proxies and preview
  ingress more reliably, and needs no extra service.
- Decision: An SSE endpoint streams stage/percent/heartbeat per scan; the
  client reconnects with backoff and falls back to polling.
- Consequences: One less stateful service; bi-directional needs (none known)
  would trigger a revisit.

## ADR-008 — No-AI / no-paid-API enforcement is mechanical, not promissory

- Status: Accepted
- Context: §5.3 requires verifiable enforcement.
- Decision: (a) A CI job fails on any banned provider name, SDK, endpoint, or
  env-var name in dependencies or source (blocklist file, extensible);
  (b) all outbound HTTP flows through one `HttpGateway` reading an allowlist
  from `config/outbound.php`, logging every request to `scan_request_log`;
  (c) the provider config schema has no price field; (d) a CI test boots the
  app and completes a fixture scan with zero API keys present.
- Consequences: Violations are caught mechanically before merge; the claims
  "no AI, no paid APIs" are backed by tests.

## ADR-009 — Database portability rules (MySQL 8 primary, MariaDB 10.11+ compatible)

- Status: Accepted
- Context: `{{DB_ENGINE}}` is mysql-8 with unmodified migrations on MariaDB.
- Decision: Migrations avoid MySQL-only syntax, functional indexes, generated
  columns with engine-specific expressions, and collation-specific features.
  JSON structures are validated in the application; heavy JSON columns use
  standard Laravel `json` columns (compatible with both engines) and are never
  queried with engine-specific JSON path SQL — filtering happens on dedicated
  indexed columns instead.
- Consequences: CI runs the full migration + query suite against MySQL 8 and
  MariaDB 10.11; drift fails the build.

## ADR-010 — Frontend: Blade + a minimal Alpine.js layer + Vite + Tailwind with token layer

- Status: Accepted
- Context: §6.1 and §12 require server-rendered pages, small JS, and a
  hand-authored token system with no hardcoded values.
- Decision: Blade views, Alpine.js for progressive interactivity (progress,
  accordions, tabs, focus trapping), Vite build, Tailwind configured only from
  `resources/css/tokens.css` variables. No SPA framework, no jQuery, no CDN
  assets, self-hosted subset woff2 fonts (families and weights fixed during
  design forensics), one inline SVG stroke icon sprite.
- Consequences: Predictable performance budgets (JS < 120 KB gz, CSS < 45 KB
  gz) are testable; UI work waits for design forensics (ADR-013).

## ADR-011 — SSRF defense pattern: resolve–validate–pin, re-validate per hop

- Status: Accepted
- Context: §10.1 makes SSRF the central engineering risk.
- Decision: The fetcher resolves DNS itself, validates all A/AAAA answers
  against the full special-purpose range list, pins the validated IP for the
  actual connection (curl `CURLOPT_RESOLVE` / explicit connect-to), restricts
  ports to 80/443, caps redirects at five with re-validation at every hop,
  strips auth headers on cross-origin redirects, streams bodies with hard byte
  and decompression-ratio limits, and denies internal hostnames from a config
  deny list. A mock-resolver test suite proves each bypass class blocked.
- Consequences: No library fetch path may bypass the gateway; code review and
  static analysis enforce a single outbound chokepoint.

## ADR-012 — Text similarity: deterministic shingling + SimHash/MinHash

- Status: Accepted
- Context: §5.2 requires local deterministic algorithms; embeddings are banned.
- Decision: Word 5-gram shingling with 64-bit SimHash for near-duplicate
  clustering; MinHash/Jaccard for verification within candidate pairs;
  Levenshtein for short-string comparison (titles, descriptions). Thresholds
  live in the Content rulepack, not in code.
- Consequences: Reproducible, explainable duplication findings with stored
  hashes as evidence; no model dependency.

## ADR-013 — Design system waits for the reference site

- Status: Accepted (blocking constraint)
- Context: `{{REFERENCE_SITE_URL}}` was not provided; the prompt forbids
  writing UI code before the single ask is answered, and it was answered with
  an empty URL.
- Decision: All UI work (layout shell, tokens, components, tool pages) is
  blocked. Phase 0 design deliverables (`docs/DESIGN_TOKENS.md`,
  `docs/DESIGN_SPEC.md`) are produced within the design-forensics step as soon
  as the URL arrives. Backend, engine, migrations, and docs proceed now.
- Consequences: Phase 0 exit requires owner sign-off on both the check
  inventory and the design tokens; only the former can complete now.

## ADR-014 — Working-name isolation

- Status: Accepted
- Context: A brand-name collision exists (see `docs/VARIABLES.md`); a rename
  must never require a code search.
- Decision: Product name, domain, bot user agent, and legal entity strings
  live only in `config/brand.php` and language files. CI greps for hardcoded
  brand literals outside those files (allowlisted exceptions: migrations for
  seeded labels that must match language keys, not literals).
- Consequences: A rename is a one-file change plus env update.
