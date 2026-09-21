# Scoring — Deterministic

## Math

- Each check: check_id, tool, category, severity, status (pass|fail|warn|unknown|not_applicable), weight, provenance, evidence_count
- Category score: `100 × earned / applicable` over evaluated weights only. `warn` counts as 0.5 weight. `unknown`/`not_applicable` excluded from denominator.
- Tool score: `Σ(cat_weight × cat_score) / Σ(applicable cat weights)`
- Grade: A+≥95 A90-94 B80-89 C70-79 D60-69 F<60 — no rounding across boundary.
- Confidence: High (all cats measured, ≥10 checks), Moderate (≤1 unknown), Low (limited pages) plus one-sentence reason.
- Provenance: Measured by us / Lab data / Field data / Owner-reported / Not measured
- Unknown rows: grey, "Not measured", excluded.

Same evidence + same rulepack_version = identical score. CI scans same URL twice and asserts identical output. No randomness, no clock.

## Per-tool categories

- SEO: indexability 30, content 25, metadata 25, semantics 20
- AdSense: required_pages 30, content_depth 30, policy 20, navigation 20
- Speed: weight 35, render 30, network 20, well_known 15
- Link: integrity 35, structure 35, hygiene 30

## AdSense note

Readiness score+grade+confidence; Blockers/Warnings/Opportunities; required-page checks record URL/status/word count/where linked; content checks numeric; prohibited hits list URL+excerpt+severity; rejection decoder; 30/60/90 checklist; rescan diff. Never approval %.

