<?php

return [
    ['id' => 'speed.weight.total', 'category' => 'weight', 'severity' => 'high', 'weight' => 10, 'label' => 'Total weight < 1.5 MB', 'docs' => 'https://web.dev/articles/optimize-resource-loading'],
    ['id' => 'speed.weight.html', 'category' => 'weight', 'severity' => 'medium', 'weight' => 6, 'label' => 'HTML size reasonable', 'docs' => 'https://web.dev/articles/optimize-resource-loading'],
    ['id' => 'speed.requests.total', 'category' => 'weight', 'severity' => 'medium', 'weight' => 6, 'label' => 'Request count < 50', 'docs' => 'https://web.dev/articles/optimize-resource-loading'],
    ['id' => 'speed.thirdparty', 'category' => 'weight', 'severity' => 'medium', 'weight' => 5, 'label' => 'Third-party share < 40%', 'docs' => 'https://web.dev/articles/optimizing-content-efficiency'],
    ['id' => 'speed.render.blocking', 'category' => 'render', 'severity' => 'high', 'weight' => 9, 'label' => 'No excessive render-blocking', 'docs' => 'https://web.dev/articles/render-blocking-resources'],
    ['id' => 'speed.render.css', 'category' => 'render', 'severity' => 'medium', 'weight' => 6, 'label' => 'CSS not render-blocking', 'docs' => 'https://web.dev/articles/defer-non-critical-css'],
    ['id' => 'speed.render.js', 'category' => 'render', 'severity' => 'medium', 'weight' => 6, 'label' => 'JS not blocking first paint', 'docs' => 'https://web.dev/articles/optimize-javascript-execution'],
    ['id' => 'speed.ttfb', 'category' => 'network', 'severity' => 'high', 'weight' => 8, 'label' => 'TTFB < 600 ms', 'docs' => 'https://web.dev/articles/ttfb'],
    ['id' => 'speed.cache.headers', 'category' => 'network', 'severity' => 'medium', 'weight' => 5, 'label' => 'Cache headers present', 'docs' => 'https://web.dev/articles/http-cache'],
    ['id' => 'speed.compression', 'category' => 'network', 'severity' => 'low', 'weight' => 3, 'label' => 'Compression enabled', 'docs' => 'https://web.dev/articles/optimizing-content-efficiency'],
    ['id' => 'speed.wellknown.robots', 'category' => 'well_known', 'severity' => 'low', 'weight' => 3, 'label' => 'robots.txt present', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/robots/intro'],
    ['id' => 'speed.wellknown.sitemap', 'category' => 'well_known', 'severity' => 'low', 'weight' => 3, 'label' => 'sitemap.xml present', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap'],
];
