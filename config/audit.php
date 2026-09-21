<?php

return [
    'tools' => [
        'seo-audit' => [
            'label' => 'SEO Audit',
            'route' => '/seo-audit',
            'gate_pages' => 10,
            'description' => 'Checks titles, meta, headings, canonicals and indexability.',
        ],
        'adsense-audit' => [
            'label' => 'AdSense Audit',
            'route' => '/adsense-audit',
            'gate_pages' => 15,
            'gate_required_pages' => true,
            'description' => 'Readiness for AdSense: required pages, content depth, policy risks.',
        ],
        'speed-audit' => [
            'label' => 'Speed Audit',
            'route' => '/speed-audit',
            'gate_pages' => 3,
            'gate_homepage_plus_two' => true,
            'description' => 'Weight, request count, render-blocking and well-known files.',
        ],
        'link-audit' => [
            'label' => 'Link Audit',
            'route' => '/link-audit',
            'gate_pages' => 5,
            'description' => 'Internal graph, redirects, orphans, click depth and rel hygiene.',
        ],
    ],

    'crawl' => [
        'max_pages' => env('CRAWLER_MAX_PAGES', 50),
        'max_pages_authenticated' => 250,
        'concurrency' => env('CRAWLER_CONCURRENCY', 4),
        'per_host_concurrency' => env('CRAWLER_PER_HOST_CONCURRENCY', 2),
        'timeout' => env('CRAWLER_TIMEOUT', 15),
        'max_redirects' => env('CRAWLER_MAX_REDIRECTS', 5),
        'max_html_bytes' => env('CRAWLER_MAX_HTML_BYTES', 5 * 1024 * 1024),
        'max_scan_bytes' => env('CRAWLER_MAX_SCAN_BYTES', 100 * 1024 * 1024),
        'user_agent' => env('CRAWLER_USER_AGENT', 'AuditPlatformBot/1.0 (+https://example.com/bot)'),
    ],

    'scoring' => [
        'grades' => [
            'A+' => 95,
            'A' => 90,
            'B' => 80,
            'C' => 70,
            'D' => 60,
            'F' => 0,
        ],
    ],

    'retention' => [
        'raw_html_days' => env('RETENTION_RAW_HTML_DAYS', 30),
        'artifact_days' => env('RETENTION_ARTIFACT_DAYS', 90),
        'log_days' => env('RETENTION_LOG_DAYS', 14),
    ],

    'rate_limits' => [
        'anon_per_hour' => env('RATE_LIMIT_ANON_PER_HOUR', 5),
        'anon_per_day' => env('RATE_LIMIT_ANON_PER_DAY', 20),
    ],
];
