<?php

namespace App\Domain\Scoring;

class Scorer
{
    /**
     * Deterministic scoring: same evidence + same rulepack = identical score
     * category = 100 * earned / applicable (unknown excluded)
     * tool = sum(cat_weight * cat_score) / sum(applicable category weights)
     */
    public static function score(array $checks, array $categoryWeights): array
    {
        $byCat = [];
        foreach ($checks as $c) {
            if ($c['status'] === 'unknown' || $c['status'] === 'not_applicable') continue;
            $cat = $c['category'];
            $byCat[$cat]['earned'] = ($byCat[$cat]['earned'] ?? 0) + ($c['status'] === 'pass' ? $c['weight'] : ($c['status'] === 'warn' ? $c['weight'] * 0.5 : 0));
            $byCat[$cat]['applicable'] = ($byCat[$cat]['applicable'] ?? 0) + $c['weight'];
        }

        $categories = [];
        $totalWeighted = 0;
        $totalWeight = 0;

        foreach ($categoryWeights as $cat => $weight) {
            $earned = $byCat[$cat]['earned'] ?? 0;
            $applicable = $byCat[$cat]['applicable'] ?? 0;
            if ($applicable === 0) {
                $score = null; // unknown category
                $status = 'unknown';
            } else {
                $score = (int) round(100 * $earned / $applicable);
                $status = self::statusForScore($score);
                $totalWeighted += $score * $weight;
                $totalWeight += $weight;
            }
            $categories[$cat] = [
                'score' => $score,
                'weight' => $weight,
                'earned' => $earned,
                'applicable' => $applicable,
                'status' => $status ?? 'unknown',
            ];
        }

        $toolScore = $totalWeight > 0 ? (int) round($totalWeighted / $totalWeight) : null;
        $grade = $toolScore !== null ? self::gradeForScore($toolScore) : '—';
        $confidence = self::confidence(count($checks), $byCat);

        return [
            'categories' => $categories,
            'tool_score' => $toolScore,
            'grade' => $grade,
            'confidence' => $confidence,
        ];
    }

    public static function gradeForScore(int $score): string
    {
        if ($score >= 95) return 'A+';
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    public static function statusForScore(int $score): string
    {
        if ($score >= 90) return 'pass';
        if ($score >= 70) return 'warn';
        return 'fail';
    }

    public static function confidence(int $totalChecks, array $byCat): array
    {
        $unknownCats = 0;
        foreach ($byCat as $cat) {
            if (($cat['applicable'] ?? 0) === 0) $unknownCats++;
        }
        if ($unknownCats === 0 && $totalChecks >= 10) {
            return ['level' => 'High', 'reason' => 'All categories measured across many pages.'];
        }
        if ($unknownCats <= 1) {
            return ['level' => 'Moderate', 'reason' => 'Most categories measured; some data missing.'];
        }
        return ['level' => 'Low', 'reason' => 'Limited pages or missing data.'];
    }
}
