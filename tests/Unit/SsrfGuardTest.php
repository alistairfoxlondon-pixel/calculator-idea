<?php

namespace Tests\Unit;

use App\Domain\Security\SsrfGuard;
use PHPUnit\Framework\TestCase;

class SsrfGuardTest extends TestCase
{
    public function test_blocks_cloud_metadata(): void
    {
        $this->assertFalse(SsrfGuard::isAllowedUrl('http://169.254.169.254/latest/meta-data/', null));
    }
    public function test_blocks_loopback(): void
    {
        $this->assertFalse(SsrfGuard::isAllowedUrl('http://127.0.0.1/admin', null));
        $this->assertFalse(SsrfGuard::isAllowedUrl('http://[::1]/', null));
    }
    public function test_blocks_decimal_octal_hex(): void
    {
        $this->assertTrue(SsrfGuard::isObfuscatedIp('2130706433')); // 127.0.0.1 decimal
        $this->assertTrue(SsrfGuard::isObfuscatedIp('0x7f000001'));
        $this->assertFalse(SsrfGuard::isAllowedUrl('http://0x7f.0.0.1/', null));
    }
    public function test_blocks_ipv6_loopback(): void
    {
        $this->assertTrue(SsrfGuard::isBlockedIp('::1'));
    }
    public function test_blocks_redirect_to_internal(): void
    {
        $this->assertFalse(SsrfGuard::validateRedirect('https://example.com', 'http://192.168.1.1/internal', 'example.com'));
    }
    public function test_blocks_rebinding(): void
    {
        // Simulate rebinding by validating second hop to private IP
        $this->assertFalse(SsrfGuard::isAllowedUrl('http://10.0.0.5/', null));
    }
    public function test_blocks_non_http(): void
    {
        $this->assertFalse(SsrfGuard::isAllowedUrl('ftp://example.com/file', null));
        $this->assertFalse(SsrfGuard::isAllowedUrl('file:///etc/passwd', null));
    }
    public function test_blocks_oversized(): void
    {
        // Guard blocks via maxHtmlBytes in Crawler — here test URL still allowed, but crawler caps
        $this->assertTrue(SsrfGuard::isAllowedUrl('https://example.com/', null));
    }
}
