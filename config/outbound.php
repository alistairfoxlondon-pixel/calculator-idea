<?php

return [
    'destinations' => [
        'pagespeed' => [
            'enabled' => env('PAGESPEED_ENABLED', false),
            'base_url' => 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed',
            'timeout' => 10,
            'quota_per_day' => 100,
        ],
        'crux' => [
            'enabled' => env('CRUX_ENABLED', false),
            'timeout' => 10,
        ],
        'safe_browsing' => [
            'enabled' => env('SAFE_BROWSING_ENABLED', false),
            'timeout' => 8,
        ],
    ],
];
