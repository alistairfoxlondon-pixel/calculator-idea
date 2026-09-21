# Audit Platform — Free Website Audit

A free, no-AI website audit platform. Paste a URL → real crawl over HTTP → live progress → score with evidence and fix list.

**Design reference mirrored:** sproutgigs.com — design language only, never brand, logos, or copy.

## Stack

- **Laravel 11.44** (verified 2026-09: requires PHP ^8.2) · PHP 8.2+ (Hostinger Business supports 8.2/8.3)
- MySQL 8 / MariaDB 10.11+ · Blade + Alpine.js + Tailwind + Vite
- Laravel HTTP client (Guzzle) for fetches · database queue/cache/session · Storage local disk
- No Redis, no persistent workers, no Node on host, no headless browser.

## Quick start (Hostinger-compatible)

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan config:cache route:cache view:cache
# set web root to public/
# cron every minute:
# * * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
```

Schedule (`app/Console/Kernel.php`):
- `queue:work --stop-when-empty --max-time=50`
- `scans:tick` (resumes stalled scans)

## Tools (V1) — ship order

| Tool | Route | Gate |
|------|-------|------|
| SEO Audit | `/seo-audit` | ≥10 pages |
| AdSense Audit | `/adsense-audit` | ≥15 pages + required pages |
| Speed Audit | `/speed-audit` | homepage +2 |
| Link Audit | `/link-audit` | ≥5 pages |

Gate failure → error screen, never a score.

## Node preview

The sandbox has no PHP but supports Node. The working preview runs via `node server.js` (Express) on port 3000, implementing the same contracts (real crawl with SSRF guard, SSE, deterministic scoring, evidence, design tokens). All Laravel files remain authoritative for production deploy.

```bash
npm install
node server.js  # http://0.0.0.0:3000
```

## Docs

- `ARCHITECTURE.md` — engine, jobs, retention
- `SCORING.md` — deterministic math, grades, confidence
- `METHODOLOGY.md` (per tool) — checks, evidence
- `SECURITY.md` — SSRF guard, CSP, headers
- `RUNBOOK.md` — host runbook
- `DEPLOY.md` — Hostinger deploy steps
- `docs/IDEA.md` — original calculator idea (archived)

## Verification (§12)

See `VERIFICATION.md` for build, crawl proof on 15 URLs, anti-fake audit, UI sweep, design review, security suite, hosting proof, and report screenshots.

