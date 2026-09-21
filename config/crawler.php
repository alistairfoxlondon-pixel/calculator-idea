<?php

return [
    'discovery_order' => ['robots', 'sitemaps', 'homepage_links', 'sitemap_urls', 'internal_links'],
    'respect_robots' => true,
    'respect_crawl_delay' => true,
    'backoff_on_429' => true,
    'allowed_schemes' => ['http', 'https'],
    'allowed_ports' => [80, 443],
    'blocked_ipv4_ranges' => [
        '127.0.0.0/8',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
        '169.254.0.0/16',
        '100.64.0.0/10',
        '0.0.0.0/8',
    ],
    'blocked_ipv6_ranges' => [
        '::1/128',
        'fc00::/7',
        'fe80::/10',
    ],
    'max_redirects' => 5,
    'timeout' => 15,
];
