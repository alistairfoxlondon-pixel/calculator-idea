# Methodology — per tool

See `resources/rulepacks/{tool}/v1/{checks.php,weights.php,meta.json}`

- SEO Audit: title, meta desc/canonical, robots, hreflang, H1-H6 order, viewport, lang, alt, JSON-LD — 14 checks, 4 categories.
- AdSense Audit: required pages, content depth (median/mean, thin <300, duplicates via SimHash, boilerplate), policy (prohibited hits + excerpt), navigation — 14 checks. Rejection decoder maps Google wording to responsible checks. 30/60/90 fix checklist.
- Speed Audit: weight, requests, render-blocking tags, third-party split, TTFB, cache headers, compression, well-known — 12 checks.
- Link Audit: internal/external broken, redirect chains, orphans, click depth BFS, anchors, rel hygiene, canonical consistent — 9 checks.

All checks have evidence rows (URL, selector, snippet, measured_value, captured_at) and docs_url. Scoring per SCORING.md.

