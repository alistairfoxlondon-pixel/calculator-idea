# SiteProof

A Laravel website-audit platform: paste a URL, get a scored, prioritized,
evidence-linked audit that an expert can verify line by line. Fifteen deep
scanning tools, one shared audit engine, zero AI, zero paid APIs.

> Working name — see `docs/VARIABLES.md` for the brand-name risk note and the
> rename path. Production domain pending; `siteproof.example` is the
> placeholder.

## Status

Phase 0 (discovery and specification) in progress. No product code yet by
design: UI code is blocked until the reference site for the design system is
provided, and Phase 0 sign-off is required first.

## The 15 tools

SEO Audit · Technical SEO Audit · Speed Audit · Mobile Audit · Content Audit ·
Schema Audit · Link Audit · Accessibility Audit · Security Audit · Malware
Audit · Email Audit · AdSense Audit · Ad Network Audit · Privacy Audit ·
AI Visibility Audit.

All 15 share one audit engine (crawler, renderer, DNS/TLS inspection, rulepack
scoring) and differ in rulepack, depth, renderer profile, and weights. Every
score is derived from stored evidence; missing evidence is reported as
`not_measured` or `unverified`, never guessed. The platform contains no AI or
model of any kind and no paid API — enforcement is mechanical (CI banned-
provider scan, outbound allowlist gateway), not promissory.

## Documentation

- [Variables](docs/VARIABLES.md) — owner variables, status, brand-name note
- [Decisions (ADRs)](docs/DECISIONS.md) — stack, engine, and policy decisions
- [Check inventory](docs/CHECK-INVENTORY.md) — every check, weight, severity, data path (index; per-tool files in `docs/checklists/`)
- [Page inventory](docs/PAGE-INVENTORY.md) — full URL and content plan
- [Risk register](docs/RISK-REGISTER.md) — SSRF, abuse, legal, capacity
- [Backlog](docs/BACKLOG.md) — deferred/blocked items with reasons

## Hard constraints (non-negotiable)

1. No AI anywhere: no LLMs, no embeddings, no generated copy; all analysis is
   deterministic code and versioned rulepacks.
2. No paid APIs, ever; optional free public providers only, always with
   graceful fallback, and the product must run with zero keys configured.
3. Every number traceable to stored evidence; nothing measured is faked.
4. No emoji, no placeholder content, no stub features in production paths.

## Contributing

Keep changes small and tested. Rulepack changes require a version bump and
golden-fixture updates; see `docs/CHECK-INVENTORY.md` for the change process.
