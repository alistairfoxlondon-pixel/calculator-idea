# Architecture

## Overview

Laravel HTTP-based, real deterministic crawl → queued chunked stages → scoring → report. Designed for Hostinger shared Business: cron (1-min), MySQL/MariaDB ≤3GB, 256-512 MB PHP, no Redis, no headless.

## Structure

```
app/Domain/{Engine,Crawl,Analysis,Scoring,Reports,Rules,Security}
app/Http/{Controllers,Middleware,Requests}
app/Jobs/ (CrawlStageJob, AnalysisJob, ScoringJob)
app/Console/Commands (TickCommand scans:tick, PruneCommand artifacts:prune)
resources/views/{layouts,components,tools,report,marketing,legal}
resources/rulepacks/{tool}/v1/{checks.php,weights.php,meta.json}
config/{audit,rulepacks,crawler,outbound,limits}.php
database/migrations
tests/{Unit,Feature,Fixtures}
```

## Engine

Fetch: Laravel HTTP client + timing (DNS, connect, TLS, TTFB, total), status, headers, bytes; ≤5 redirects; 15s timeout; 5 MB per HTML; 100 MB per scan.

Crawl: same-origin BFS; 50 pages (250 authed); concurrency 4 / 2 per host; respect robots.txt + Crawl-delay; backoff 429; UA AuditPlatformBot/1.0.

Discovery: robots.txt → sitemaps (robots + /sitemap.xml) → homepage links → sitemap URLs → internal links.

Analysis: deterministic only (HTML, CSS same-origin size-capped, JS dependency raw vs DOM, Text shingling/SimHash, Network weight, DNS/TLS, well-known, Links graph). Optional drivers (PSI, CrUX, Safe Browsing) with flag/quota/timeout/retry/cache/TTL and Not measured fallback.

## Jobs & Runtime

- Queue: database driver (jobs, failed_jobs)
- Each stage ≤20s chunked
- One cron: `php artisan schedule:run` every minute → `queue:work --stop-when-empty --max-time=50` + `scans:tick` resumes stalled (persisted stage/progress)
- WithoutOverlapping + idempotency key prevents duplicates
- Retention: artifacts:prune monthly (raw HTML 30d, artifacts 90d, logs 14d)

## Portability

Migrations: InnoDB, utf8mb4, no MySQL-only syntax, TEXT indexes with prefix lengths, JSON as LONGTEXT validated.

## Node preview

Sandbox lacks PHP; Node `server.js` mirrors same contracts for preview (SSRF guard, SSE `/scans/{id}/stream` fallback to 1s polling, deterministic scoring golden fixtures).

