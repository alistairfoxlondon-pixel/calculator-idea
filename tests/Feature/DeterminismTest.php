<?php

namespace Tests\Feature;

use App\Domain\Scoring\Scorer;
use Tests\TestCase;

class DeterminismTest extends TestCase
{
    public function test_same_evidence_same_score(): void
    {
        $checks = [
            ['category'=>'indexability','status'=>'pass','weight'=>10],
            ['category'=>'indexability','status'=>'fail','weight'=>10],
        ];
        $weights = ['indexability'=>30];
        $a = Scorer::score($checks, $weights);
        $b = Scorer::score($checks, $weights);
        $this->assertEquals($a, $b);
    }
}
