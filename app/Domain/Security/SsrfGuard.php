<?php

namespace App\Domain\Security;

use InvalidArgumentException;

class SsrfGuard
{
    private const BLOCKED_RANGES = [
        '127.0.0.0/8',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
        '169.254.0.0/16',
        '100.64.0.0/10',
        '0.0.0.0/8',
    ];

    private const BLOCKED_IPV6_RANGES = [
        '::1/128',
        'fc00::/7',
        'fe80::/10',
    ];

    public static function isAllowedUrl(string $url, ?string $ownHost = null): bool
    {
        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['scheme'], $parsed['host'])) {
            return false;
        }

        $scheme = strtolower($parsed['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $port = $parsed['port'] ?? ($scheme === 'https' ? 443 : 80);
        if (!in_array($port, [80, 443], true)) {
            return false;
        }

        $host = strtolower($parsed['host']);

        if ($ownHost && $host === strtolower($ownHost)) {
            return false;
        }

        // Decimal/octal/hex IP forms blocked
        if (self::isObfuscatedIp($host)) {
            return false;
        }

        $ips = self::resolveHost($host);
        if (empty($ips)) {
            return false;
        }

        foreach ($ips as $ip) {
            if (self::isBlockedIp($ip)) {
                return false;
            }
        }

        return true;
    }

    public static function isObfuscatedIp(string $host): bool
    {
        // Check for decimal, octal, hex encodings
        if (preg_match('/^0x[0-9a-f]+$/i', $host)) return true;
        if (preg_match('/^0[0-7]+$/', $host)) return true;
        if (preg_match('/^\d+$/', $host) && ctype_digit($host) && strlen($host) > 4) return true;
        if (preg_match('/^0x/i', $host)) return true;
        // IPv4 dotted hex/octal
        if (preg_match('/0x[0-9a-f]/i', $host)) return true;
        return false;
    }

    public static function resolveHost(string $host): array
    {
        // In production, use dns_get_record with local resolver
        // Here we use gethostbynamel for illustration - must pin IP on next hop validation
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }

        $records = @dns_get_record($host, DNS_A + DNS_AAAA);
        if (!$records) {
            $ip = gethostbyname($host);
            if ($ip !== $host) {
                return [$ip];
            }
            return [];
        }

        $ips = [];
        foreach ($records as $r) {
            if (isset($r['ip'])) $ips[] = $r['ip'];
            if (isset($r['ipv6'])) $ips[] = $r['ipv6'];
        }
        return $ips;
    }

    public static function isBlockedIp(string $ip): bool
    {
        // Check for IPv4-mapped IPv6
        if (str_starts_with(strtolower($ip), '::ffff:')) {
            $ip = substr($ip, 7);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            foreach (self::BLOCKED_RANGES as $range) {
                if (self::ipInRange($ip, $range)) return true;
            }
            // Cloud metadata
            if ($ip === '169.254.169.254') return true;
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ipLower = strtolower($ip);
            if (in_array($ipLower, ['::1', '0:0:0:0:0:0:0:1'], true)) return true;
            foreach (self::BLOCKED_IPV6_RANGES as $range) {
                if (self::ipv6InRange($ip, $range)) return true;
            }
            return false;
        }

        return true; // unknown format -> block
    }

    private static function ipInRange(string $ip, string $range): bool
    {
        [$subnet, $bits] = explode('/', $range);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        if ($ipLong === false || $subnetLong === false) return false;
        $mask = -1 << (32 - (int)$bits);
        return ($ipLong & $mask) === ($subnetLong & $mask);
    }

    private static function ipv6InRange(string $ip, string $range): bool
    {
        [$subnet, $bits] = explode('/', $range);
        $ipBin = inet_pton($ip);
        $subnetBin = inet_pton($subnet);
        if ($ipBin === false || $subnetBin === false) return false;
        $bytes = intdiv((int)$bits, 8);
        $rem = (int)$bits % 8;
        if (strncmp($ipBin, $subnetBin, $bytes) !== 0) return false;
        if ($rem === 0) return true;
        $mask = 0xFF << (8 - $rem) & 0xFF;
        return (ord($ipBin[$bytes]) & $mask) === (ord($subnetBin[$bytes]) & $mask);
    }

    /**
     * Validate redirect target and re-resolve DNS to prevent rebinding
     */
    public static function validateRedirect(string $fromUrl, string $toUrl, string $ownHost): bool
    {
        return self::isAllowedUrl($toUrl, $ownHost);
    }
}
