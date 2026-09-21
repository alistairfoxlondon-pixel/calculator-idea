# Deploy — Hostinger Business

1. Upload release to `domains/example.com/public_html` (or subdir), set web root to `public/`
2. PHP 8.2 selected in hPanel → Advanced → PHP Configuration (Hostinger supports 8.2/8.3; Laravel 11.44 requires ^8.2)
3. Create MySQL DB + user (hPanel → Databases), import env, run:
   `composer install --no-dev --optimize-autoloader`
   `php artisan migrate --force`
   `php artisan config:cache route:cache view:cache`
4. `.env`: APP_ENV=production, APP_DEBUG=false, APP_URL=https://example.com, DB_*, QUEUE_CONNECTION=database, CACHE_DRIVER=database, SESSION_DRIVER=database
5. Cron: `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`
6. Verify: `php artisan tinker --execute="echo app()->version();"` and run full scan.

## Verification after deploy

- Each tool scan completes, report loads, CSV/Print work, SSE streams, fallback polling works.
- Peak memory <512 MB, runtime <120s for 50 pages.

