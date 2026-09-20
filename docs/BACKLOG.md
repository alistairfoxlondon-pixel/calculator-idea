# SiteProof — Backlog and Deferred Work

No partial code ships in the product path. Deferred work is logged here with a
reason and an owner phase.

| ID | Item | Status | Reason / trigger | Target phase |
| --- | --- | --- | --- | --- |
| B-01 | Design forensics; `docs/DESIGN_TOKENS.md`; `docs/DESIGN_SPEC.md`; full component library; layout shell | **Blocked on owner input** | `{{REFERENCE_SITE_URL}}` not yet provided. Prompt forbids UI code before it arrives; the single ask was made 2026-09-20 and the URL slot came back empty. All non-UI work proceeds meanwhile. | 0/1 (on receipt) |
| B-02 | `network_requirement_profiles` content: verified thresholds with `source_url` and `last_verified_at` for each ad network | Planned | Thresholds must be verified from each network's own published pages at seed time, not written from memory. Table schema ships in Phase 1; rows are researched and sourced in Phase 8 with a verification date per row. | 8 |
| B-03 | Production domain | Blocked on owner | Placeholder `siteproof.example` used everywhere; real domain set via `APP_DOMAIN` env at deploy. | pre-launch (owner) |
| B-04 | Trademark counsel review of the working name "SiteProof" | Blocked on owner | Collision documented in `docs/VARIABLES.md`; name is isolated for a cheap rename (ADR-014). | pre-launch (owner) |
| B-05 | Calibration dataset and published accuracy statistics | Post-launch | Requires opt-in outcome reports from real users (§4.8). Fixture corpus with golden scores ships in Phase 2; the aggregate statistics page shows "dataset not yet sufficient" until a minimum labeled sample exists. | post-launch |
| B-06 | Guides library article bodies | Owner-authored | §13 requires human-authored editorial content with real bylines. The product ships structure, briefs, and templates; the owner writes the articles. | 9 |
| B-07 | Passkey authentication | Planned optional | TOTP two-factor ships first; passkeys add the second factor option in Phase 9 once the auth core is stable. | 9 |
| B-08 | Load test at 500 concurrent scan submissions | Planned | Requires the full job pipeline (Phases 2–3) and representative fixtures. k6 scenarios are written alongside Phase 2 and executed in Phase 10. | 10 |
| B-09 | Our own site's 15-tool self-audit with providers enabled and disabled | Planned | Meaningful only when all 15 tools exist. | 10 |
| B-10 | First-party analytics reject option + GPC handling on our own site | Planned (Phase 9) | Ships with the analytics service itself; not applicable earlier. | 9 |
