# SiteProof — Variable Register (Section 0)

Status date: 2026-09-20. This file is the single record of the owner-supplied
variables from the master build prompt. All values are also reflected in
`config/brand.php` (name, domain placeholder) so a change is a one-file edit
plus environment variables, never a code search.

## Filled variable table

| Variable | Value | Status | Notes |
| --- | --- | --- | --- |
| `{{PRODUCT_NAME}}` | SiteProof | Locked (working name) | Owner-selected 2026-09-20 from proposed candidates. See name-risk note below. |
| `{{PRIMARY_DOMAIN}}` | `siteproof.example` | Placeholder | Owner chose "placeholder until the real domain is registered". Replace via `APP_DOMAIN` env at deploy; no code references a literal production domain. The `.example` TLD is reserved and can never be shipped accidentally. |
| `{{REFERENCE_SITE_URL}}` | PENDING | **Blocks all UI code** | Owner indicated they would paste the URL; it was not received. Per the protocol the question is not repeated. Design forensics, `docs/DESIGN_TOKENS.md`, `docs/DESIGN_SPEC.md`, and all UI code stay blocked until the URL arrives. Non-UI phases proceed (tracked in `docs/BACKLOG.md` B-01). |
| `{{REFERENCE_SCREENSHOTS}}` | Not provided | Optional | Will be captured from the reference site during design forensics once the URL is supplied. |
| `{{DB_ENGINE}}` | mysql-8 | Locked (default confirmed) | Migrations must run unmodified on MariaDB 10.11+. Portability rules: ADR-009. CI runs both engines on the migration suite. |
| `{{HOSTING_TARGET}}` | VPS + Docker Compose | Locked (default confirmed) | Compose files cover local, staging, production sizing per `docs/RUNBOOK.md`. |
| `{{MONETIZATION}}` | Free tier shipped; entitlement layer built; paid tiers behind a config flag | Locked (default confirmed) | `config/plans.php` gates Pro features. No paid API dependency of any kind regardless of tier. |
| `{{ACCOUNT_MODEL}}` | Anonymous scans with strict rate limits + optional accounts | Locked (default confirmed) | Anonymous: 50-page crawl cap, per-IP quotas. Accounts: history, schedules, batch audits, exports. |
| `{{OPTIONAL_FREE_PROVIDERS}}` | All enabled by default: PageSpeed Insights, CrUX, Safe Browsing, URLhaus/OpenPhish feeds, Search Console OAuth, GA4 OAuth, RDAP, Certificate Transparency | Locked (default confirmed) | Every provider is config-flagged in `config/providers.php`, quota-guarded, cached, and has a documented `not_measured` fallback. The product must fully function with every provider disabled and zero keys present (CI-tested). |

## Brand-name risk acknowledgment (owner action recommended)

A collision search on 2026-09-20 found active software products named "SiteProof" / "Siteproof":

1. **SiteProof by Deveras** — a website scanner (WCAG accessibility, AI-engine optimization, AI-agent security compliance). This is the **same or adjacent product category** as SiteProof, the website audit platform. Confusion risk is real.
2. **Siteproof Ltd (siteproof.dev)** — a field-operations platform for utilities contractors. Different category, same name.
3. A GitHub project ("SiteOps HQ") that retains "SiteProof" code internally.

Consequences and mitigation:

- The owner selected this name; it remains the **working name**. No final trademark clearance has been performed.
- Recommendation: before public launch, have trademark counsel check classes 9/42 in the target markets. If a rename is needed, all brand strings live in `config/brand.php` and the language files, so a rename is a contained, one-commit change with no logic edits.
- No logo or visual mark is created until the design phase (blocked on the reference site), so no design rework is at risk.

This risk is also recorded as R-10 in `docs/RISK-REGISTER.md`.
