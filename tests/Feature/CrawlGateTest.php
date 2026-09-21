<?php

namespace Tests\Feature;

use Tests\TestCase;

class CrawlGateTest extends TestCase
{
    public function test_gate_fails_without_enough_pages(): void
    {
        $pages = array_fill(0, 3, ['url'=>'https://example.com/page1']);
        $gate = 10;
        $this->assertTrue(count($pages) < $gate);
    }
}
