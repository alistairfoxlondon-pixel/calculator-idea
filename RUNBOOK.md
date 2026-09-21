# Runbook

## Cron

```
* * * * * cd /home/u123456789/domains/example.com/public_html && php artisan schedule:run >> /dev/null 2>&1
```

`app/Console/Kernel.php`:
- `queue:work --stop-when-empty --max-time=50`
- `scans:tick` every minute
- `artifacts:prune` monthly

## Deploy verify

- Run real scan for each tool, check report loads, no 508/503, peak memory logged.
- Run our tools against our own site; paste scores.

## Incident

- If scan stalls >2 min, `scans:tick` resumes next minute.
- If DB >3GB, prune runs; check storage/app/artifacts.

