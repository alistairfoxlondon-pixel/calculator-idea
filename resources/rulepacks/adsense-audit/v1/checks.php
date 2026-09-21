<?php

return [
    ['id' => 'adsense.privacy', 'category' => 'required_pages', 'severity' => 'critical', 'weight' => 10, 'label' => 'Privacy policy present', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.about', 'category' => 'required_pages', 'severity' => 'high', 'weight' => 8, 'label' => 'About page present', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.contact', 'category' => 'required_pages', 'severity' => 'high', 'weight' => 8, 'label' => 'Contact page present', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.terms', 'category' => 'required_pages', 'severity' => 'medium', 'weight' => 4, 'label' => 'Terms present or linked', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.required_pages.linked', 'category' => 'navigation', 'severity' => 'high', 'weight' => 7, 'label' => 'Required pages linked in nav/footer', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.wordcount.median', 'category' => 'content_depth', 'severity' => 'high', 'weight' => 10, 'label' => 'Median word count ≥300', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.wordcount.thin', 'category' => 'content_depth', 'severity' => 'high', 'weight' => 9, 'label' => 'Few thin pages (<300 words)', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.duplicate', 'category' => 'content_depth', 'severity' => 'medium', 'weight' => 6, 'label' => 'No duplicate clusters', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.boilerplate', 'category' => 'content_depth', 'severity' => 'medium', 'weight' => 5, 'label' => 'Boilerplate ratio low', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.headings', 'category' => 'content_depth', 'severity' => 'low', 'weight' => 3, 'label' => 'Heading structure valid', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.policy.prohibited', 'category' => 'policy', 'severity' => 'critical', 'weight' => 10, 'label' => 'No prohibited content', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.policy.copyright', 'category' => 'policy', 'severity' => 'high', 'weight' => 8, 'label' => 'No copyrighted scraping signals', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
    ['id' => 'adsense.ads.txt', 'category' => 'policy', 'severity' => 'low', 'weight' => 3, 'label' => 'ads.txt present', 'docs' => 'https://support.google.com/adsense/answer/1217164'],
    ['id' => 'adsense.navigation', 'category' => 'navigation', 'severity' => 'medium', 'weight' => 6, 'label' => 'Navigation usable', 'docs' => 'https://support.google.com/adsense/answer/1346295'],
];
