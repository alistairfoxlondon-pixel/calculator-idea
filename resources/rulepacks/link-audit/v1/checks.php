<?php

return [
    ['id' => 'link.broken.internal', 'category' => 'integrity', 'severity' => 'critical', 'weight' => 10, 'label' => 'No broken internal links', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/links-crawlable'],
    ['id' => 'link.broken.external', 'category' => 'integrity', 'severity' => 'medium', 'weight' => 6, 'label' => 'No broken external links', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/links-crawlable'],
    ['id' => 'link.redirect.chains', 'category' => 'integrity', 'severity' => 'medium', 'weight' => 6, 'label' => 'No redirect chains', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/301-redirects'],
    ['id' => 'link.orphans', 'category' => 'structure', 'severity' => 'medium', 'weight' => 7, 'label' => 'No orphan pages', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/site-structure'],
    ['id' => 'link.depth', 'category' => 'structure', 'severity' => 'medium', 'weight' => 6, 'label' => 'Click depth ≤3', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/site-structure'],
    ['id' => 'link.internal.count', 'category' => 'structure', 'severity' => 'low', 'weight' => 3, 'label' => 'Internal link graph healthy', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/site-structure'],
    ['id' => 'link.anchors', 'category' => 'hygiene', 'severity' => 'low', 'weight' => 4, 'label' => 'Anchor text descriptive', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/links-crawlable'],
    ['id' => 'link.rel.nofollow', 'category' => 'hygiene', 'severity' => 'low', 'weight' => 3, 'label' => 'Rel attributes correct', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/qualify-outbound-links'],
    ['id' => 'link.canonical.consistent', 'category' => 'hygiene', 'severity' => 'medium', 'weight' => 5, 'label' => 'Canonicals consistent', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls'],
];
