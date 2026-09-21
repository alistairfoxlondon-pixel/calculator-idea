<?php

return [
    ['id' => 'seo.title.present', 'category' => 'metadata', 'severity' => 'high', 'weight' => 10, 'label' => 'Title tag present', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/special-tags'],
    ['id' => 'seo.title.length', 'category' => 'metadata', 'severity' => 'medium', 'weight' => 5, 'label' => 'Title length 30-60 chars', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/special-tags'],
    ['id' => 'seo.meta.description', 'category' => 'metadata', 'severity' => 'high', 'weight' => 10, 'label' => 'Meta description present', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/special-tags'],
    ['id' => 'seo.meta.description.length', 'category' => 'metadata', 'severity' => 'medium', 'weight' => 5, 'label' => 'Meta description 50-160 chars', 'docs' => 'https://developers.google.com/search/docs/appearance/snippet'],
    ['id' => 'seo.canonical', 'category' => 'indexability', 'severity' => 'high', 'weight' => 8, 'label' => 'Canonical present and valid', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls'],
    ['id' => 'seo.meta.robots', 'category' => 'indexability', 'severity' => 'critical', 'weight' => 10, 'label' => 'No noindex blocking', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag'],
    ['id' => 'seo.hreflang', 'category' => 'indexability', 'severity' => 'low', 'weight' => 3, 'label' => 'Hreflang valid if present', 'docs' => 'https://developers.google.com/search/docs/specialty/international/localized-versions'],
    ['id' => 'seo.h1.single', 'category' => 'content', 'severity' => 'medium', 'weight' => 7, 'label' => 'Single H1 per page', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/seo-starter-guide'],
    ['id' => 'seo.h1.present', 'category' => 'content', 'severity' => 'high', 'weight' => 8, 'label' => 'H1 present', 'docs' => 'https://developers.google.com/search/docs/crawling-indexing/seo-starter-guide'],
    ['id' => 'seo.headings.order', 'category' => 'content', 'severity' => 'medium', 'weight' => 5, 'label' => 'Heading order H1-H6', 'docs' => 'https://developer.mozilla.org/en-US/docs/Web/HTML/Element/Heading_Elements'],
    ['id' => 'seo.viewport', 'category' => 'semantics', 'severity' => 'high', 'weight' => 6, 'label' => 'Viewport meta present', 'docs' => 'https://developer.mozilla.org/en-US/docs/Web/HTML/Viewport_meta_tag'],
    ['id' => 'seo.lang', 'category' => 'semantics', 'severity' => 'medium', 'weight' => 4, 'label' => 'HTML lang attribute', 'docs' => 'https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/lang'],
    ['id' => 'seo.alt.images', 'category' => 'semantics', 'severity' => 'medium', 'weight' => 6, 'label' => 'Images have alt text', 'docs' => 'https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img'],
    ['id' => 'seo.jsonld', 'category' => 'semantics', 'severity' => 'low', 'weight' => 3, 'label' => 'Structured data present', 'docs' => 'https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data'],
];
