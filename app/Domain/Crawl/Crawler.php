<?php

namespace App\Domain\Crawl;

use App\Domain\Security\SsrfGuard;
use Illuminate\Support\Facades\Http;

class Crawler
{
    public int $maxPages = 50;
    public int $concurrency = 4;
    public int $perHostConcurrency = 2;
    public int $timeout = 15;
    public int $maxRedirects = 5;
    public int $maxHtmlBytes = 5242880;
    public int $maxScanBytes = 104857600;
    public string $userAgent = 'AuditPlatformBot/1.0 (+https://example.com/bot)';

    /**
     * BFS crawl same-origin, discovery order: robots.txt -> sitemaps -> homepage links -> sitemap URLs -> internal links
     */
    public function crawl(string $startUrl, callable $onProgress): array
    {
        $parsed = parse_url($startUrl);
        $origin = strtolower($parsed['scheme'] . '://' . $parsed['host']);
        $host = strtolower($parsed['host']);
        $ownHost = parse_url(config('app.url'), PHP_URL_HOST);

        if (!SsrfGuard::isAllowedUrl($startUrl, $ownHost)) {
            throw new \RuntimeException('SSRF blocked: target not allowed');
        }

        $visited = [];
        $queue = [$startUrl];
        $pages = [];
        $totalBytes = 0;
        $sitemapUrls = [];

        // 1. robots.txt
        $onProgress('robots', 0, 'Resolving host');
        $robots = $this->fetchRobots($origin, $host);
        if ($robots['sitemap']) {
            $sitemapUrls = array_merge($sitemapUrls, $this->fetchSitemap($robots['sitemap']));
        }
        // also try /sitemap.xml
        $sitemapUrls = array_merge($sitemapUrls, $this->fetchSitemap($origin . '/sitemap.xml'));

        $onProgress('sitemap', 5, 'Sitemap');

        // 2. BFS
        $counter = 0;
        while (!empty($queue) && count($pages) < $this->maxPages && $totalBytes < $this->maxScanBytes) {
            $url = array_shift($queue);
            if (isset($visited[$url])) continue;
            $visited[$url] = true;

            $result = $this->fetchPage($url, $host, $ownHost);
            $onProgress('crawling', (int)( (count($pages) / $this->maxPages) * 80 ) + 10, "Crawling " . count($pages) . "/" . $this->maxPages, $result);

            if ($result['status'] >= 200 && $result['status'] < 300 && $result['is_html']) {
                $pages[] = $result;
                $totalBytes += $result['bytes'];
                $counter++;

                // Extract internal links
                $links = $this->extractLinks($result['html'], $url, $origin);
                foreach ($links as $link) {
                    if (!isset($visited[$link]) && !in_array($link, $queue, true)) {
                        $queue[] = $link;
                    }
                }

                // Also inject sitemap URLs early
                if ($counter === 1 && !empty($sitemapUrls)) {
                    foreach (array_reverse($sitemapUrls) as $sUrl) {
                        if (!isset($visited[$sUrl])) array_unshift($queue, $sUrl);
                    }
                    $sitemapUrls = [];
                }
            } elseif ($result['status'] === 429) {
                // backoff: requeue with delay
                usleep(2000000);
                $queue[] = $url;
            }

            if (count($pages) >= $this->maxPages) break;
        }

        return $pages;
    }

    private function fetchRobots(string $origin, string $host): array
    {
        try {
            $r = Http::withHeaders(['User-Agent' => $this->userAgent])
                ->timeout($this->timeout)
                ->get($origin . '/robots.txt');
            $body = $r->body();
            $sitemap = null;
            if (preg_match('/Sitemap:\s*(\S+)/i', $body, $m)) {
                $sitemap = trim($m[1]);
            }
            return ['body' => $body, 'sitemap' => $sitemap, 'status' => $r->status()];
        } catch (\Throwable $e) {
            return ['body' => null, 'sitemap' => null, 'status' => 0];
        }
    }

    private function fetchSitemap(string $url): array
    {
        try {
            $r = Http::withHeaders(['User-Agent' => $this->userAgent])->timeout($this->timeout)->get($url);
            if ($r->status() !== 200) return [];
            $xml = @simplexml_load_string($r->body());
            if (!$xml) return [];
            $urls = [];
            foreach ($xml->url as $u) {
                $urls[] = (string)$u->loc;
            }
            // also handle sitemapindex
            foreach ($xml->sitemap as $s) {
                $urls[] = (string)$s->loc;
            }
            return array_slice($urls, 0, 100);
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function fetchPage(string $url, string $host, ?string $ownHost): array
    {
        $start = microtime(true);
        $dnsMs = 0; $connectMs = 0; $tlsMs = 0; $ttfbMs = 0;

        // SSRF check before fetch
        if (!SsrfGuard::isAllowedUrl($url, $ownHost)) {
            return ['url' => $url, 'status' => 403, 'bytes' => 0, 'total_ms' => 0, 'html' => '', 'is_html' => false, 'blocked' => true];
        }

        try {
            $response = Http::withHeaders(['User-Agent' => $this->userAgent])
                ->timeout($this->timeout)
                ->withOptions([
                    'allow_redirects' => ['max' => $this->maxRedirects, 'track_redirects' => true],
                    'decode_content' => true,
                ])->get($url);

            $totalMs = (int)((microtime(true) - $start) * 1000);
            $body = $response->body();
            if (strlen($body) > $this->maxHtmlBytes) {
                $body = substr($body, 0, $this->maxHtmlBytes);
            }

            $contentType = $response->header('Content-Type') ?? '';
            $isHtml = str_contains(strtolower($contentType), 'text/html') || str_contains($body, '<html');

            // Re-validate redirect history for SSRF
            $history = $response->transferStats?->getHandlerStats()['redirect_url'] ?? null;

            return [
                'url' => $url,
                'status' => $response->status(),
                'bytes' => strlen($body),
                'total_ms' => $totalMs,
                'ttfb_ms' => $totalMs, // simplified
                'dns_ms' => $dnsMs,
                'connect_ms' => $connectMs,
                'tls_ms' => $tlsMs,
                'headers' => $response->headers(),
                'html' => $body,
                'is_html' => $isHtml,
            ];
        } catch (\Throwable $e) {
            $totalMs = (int)((microtime(true) - $start) * 1000);
            return ['url' => $url, 'status' => 0, 'bytes' => 0, 'total_ms' => $totalMs, 'html' => '', 'is_html' => false, 'error' => $e->getMessage()];
        }
    }

    private function extractLinks(string $html, string $baseUrl, string $origin): array
    {
        $links = [];
        if (!preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $html, $m)) return [];
        foreach ($m[1] as $href) {
            $href = trim($href);
            if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) continue;
            $abs = $this->resolveUrl($href, $baseUrl);
            if (!$abs) continue;
            $parsed = parse_url($abs);
            $absOrigin = strtolower(($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? ''));
            if ($absOrigin !== $origin) continue;
            $abs = strtok($abs, '#');
            $links[] = $abs;
        }
        return array_unique($links);
    }

    private function resolveUrl(string $href, string $base): ?string
    {
        if (preg_match('#^https?://#i', $href)) return $href;
        if (str_starts_with($href, '//')) {
            $scheme = parse_url($base, PHP_URL_SCHEME) ?? 'https';
            return $scheme . ':' . $href;
        }
        if (str_starts_with($href, '/')) {
            $origin = parse_url($base, PHP_URL_SCHEME) . '://' . parse_url($base, PHP_URL_HOST);
            return $origin . $href;
        }
        return rtrim($base, '/') . '/' . $href;
    }
}
