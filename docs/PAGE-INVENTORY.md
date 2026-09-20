# SiteProof — Page Inventory (Phase 0)

Canonical URL plan. Every page carries: canonical tag, breadcrumbs, structured
data where applicable, OG/Twitter cards with generated share images, and a
defined state set (initial, running, success, partial, failure, rate-limited).
Title pattern for tool pages: `{Tool Name} — {What it checks in 4–7 words} |
SiteProof` (≤ 60 chars). Meta descriptions 150–160 characters.

## Public marketing and content pages

| Path | Page | Title pattern (example) | Notes |
| --- | --- | --- | --- |
| `/` | Home | `SiteProof — Evidence-Based Website Audits, No AI` | Positioning: shows its work, own engines, no AI, no paid keys |
| `/tools` | Tools hub | `All 15 Website Audit Tools | SiteProof` | One-line description per tool, internal links |
| `/seo-audit` … | 15 tool pages | Per §3.0 table | Scan form above the fold, what-it-checks, real weights, example report, 800+ word body, FAQ, related tools, WebApplication + BreadcrumbList schema |
| `/methodology` | Methodology hub | `Methodology — How SiteProof Measures | SiteProof` | Links to all 15 per-tool methodology pages |
| `/methodology/{tool}` | Per-tool methodology | `{Tool Name} Methodology | SiteProof` | Every check, weight, method, provenance labels, limitations, rulepack changelog |
| `/accuracy` | Accuracy and calibration | `Accuracy — Scores, Confidence, Calibration | SiteProof` | Aggregate opt-in outcome stats with sample size and date; fixture corpus description |
| `/guides` | Guides library | `Publishing Guides — AdSense, SEO, Speed | SiteProof` | Owner-authored articles; structural templates provided by the product |
| `/guides/{slug}` | Guide | `{Guide title} | SiteProof` | Launch set: why AdSense rejected my site; how to fix low value content; how to check Core Web Vitals on mobile; what is llms.txt; Mediavine vs Raptive requirements; how to add DKIM records |
| `/pricing` | Pricing | `Pricing — Free Scans, Optional Pro | SiteProof` | Rendered only when `plans.enabled`; otherwise 301 to `/tools` |
| `/about` | About | `About SiteProof — Independent, No AI | SiteProof` | |
| `/contact` | Contact | `Contact SiteProof | SiteProof` | Working form, honeypot + PoW, reply route |
| `/changelog` | Changelog | `Changelog | SiteProof` | Rulepack and product changes, dated |
| `/status` | Status | `Service Status | SiteProof` | Self-hosted uptime checks, component view |

## Trust and legal pages

| Path | Page | Notes |
| --- | --- | --- |
| `/terms` | Terms of Service | Acceptable use: scan only sites you own or are authorized to test |
| `/privacy` | Privacy Policy | Exact data collected, retention, processors; states scanned URLs are never sent to AI providers or data brokers (none exist) |
| `/cookie-policy` | Cookie Policy | First-party cookies only; self-hosted first-party analytics with a real reject option; GPC respected |
| `/disclaimers` | Disclaimers | Non-affiliation, estimate-only, no approval guarantees, accessibility ≠ legal certification, malware = indicators not verdicts |
| `/how-we-scan` | How we scan | User agent string, politeness rules, what we never do, stop-scanning request route |
| `/bot` | About SiteProofBot | User agent details, purpose, contact, block instructions; linked from the UA string |
| `/.well-known/security.txt` | security.txt | Contact and policy pointers |
| `/.well-known/llms.txt`, `/llms.txt` | llms.txt | Our own AI-visibility file describing our tools; we are crawlable without using AI |

## Product flow pages

| Path | Page | Notes |
| --- | --- | --- |
| `/scan/{public_id}` | Scan progress + report | Live SSE progress, then the full report at the same URL; state machine per §12.8 |
| `/r/{token}` | Shareable report permalink | No login; OG preview; respects `is_public` and expiry |
| `/report/{id}/pdf` | PDF export | Rendered from the real report view with print CSS |
| `/report/{id}/export/{csv\|json}` | Data exports | Account holders; chunked |
| `/compare/{idA}/{idB}` | Diff view | Score delta, resolved/new/regressed issues |

## Account area (optional accounts)

`/login`, `/register`, `/verify`, `/password/*`, two-factor setup and challenge,
`/dashboard` (history), `/dashboard/schedules`, `/dashboard/batch`, 
`/dashboard/exports`, `/dashboard/connections` (Search Console, GA4 OAuth),
`/account/profile`, `/account/security` (TOTP, passkeys), `/account/data`
(export and deletion flows), `/account/billing` (only when plans enabled).

## Admin area (two-factor required)

`/admin` overview, `/admin/users`, `/admin/scans`, `/admin/quotas`,
`/admin/rulepacks`, `/admin/network-profiles`, `/admin/providers` (free-quota
usage), `/admin/feedback-outcomes`, `/admin/abuse`, `/admin/errors`
(error_events viewer). Every action written to `audit_logs`.

## Technical routes

`/sitemap.xml` (split by section), `/robots.txt` (allows legitimate AI
crawlers), `/images/og/{tool}.png` (generated per-tool share images),
`/health` (app), `/health/{db,redis,renderer,queue}` component checks,
`/api/*` versioned JSON API (scan submit, status, report read — authenticated
or rate-limited public), `/scan-stream/{scan}` SSE endpoint.
