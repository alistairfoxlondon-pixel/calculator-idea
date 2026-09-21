<?php

namespace App\Http\Controllers;

use App\Domain\Crawl\Crawler;
use App\Domain\Security\SsrfGuard;
use App\Domain\Scoring\Scorer;
use App\Jobs\CrawlStageJob;
use App\Jobs\AnalysisJob;
use App\Jobs\ScoringJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'tool' => 'required|in:seo-audit,adsense-audit,speed-audit,link-audit',
        ]);

        $url = $request->input('url');
        $tool = $request->input('tool');

        // Rate limit: 5 per hour, 20 per day per IP
        $ip = $request->ip();
        $hourCount = DB::table('scans')->where('ip_address', $ip)->where('created_at', '>', now()->subHour())->count();
        $dayCount = DB::table('scans')->where('ip_address', $ip)->where('created_at', '>', now()->subDay())->count();
        if ($hourCount >= 5) return response()->json(['error' => 'Rate limited', 'reason' => '5 per hour'], 429);
        if ($dayCount >= 20) return response()->json(['error' => 'Rate limited', 'reason' => '20 per day'], 429);

        // SSRF check
        $ownHost = parse_url(config('app.url'), PHP_URL_HOST);
        if (!SsrfGuard::isAllowedUrl($url, $ownHost)) {
            return response()->json(['error' => 'Invalid URL', 'reason' => 'SSRF blocked or invalid scheme'], 422);
        }

        $scanId = (string) Str::uuid();
        DB::table('scans')->insert([
            'id' => $scanId,
            'tool' => $tool,
            'url' => $url,
            'normalized_url' => $url,
            'status' => 'queued',
            'stage' => 'resolving',
            'progress' => 0,
            'ip_address' => $ip,
            'idempotency_key' => Str::random(32),
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dispatch chunked jobs
        CrawlStageJob::dispatch($scanId);

        return response()->json(['id' => $scanId, 'status' => 'queued']);
    }

    public function show(string $scan)
    {
        $s = DB::table('scans')->where('id', $scan)->first();
        if (!$s) abort(404);
        return response()->json($s);
    }

    public function progress(string $scan)
    {
        $s = DB::table('scans')->where('id', $scan)->first();
        if (!$s) abort(404);
        return view('report.progress', ['scan' => $s]);
    }

    public function stream(string $scan)
    {
        $s = DB::table('scans')->where('id', $scan)->first();
        if (!$s) abort(404);
        return response()->stream(function () use ($scan) {
            // SSE: StreamedResponse with fallback noted in Blade JS
            $lastProgress = -1;
            for ($i=0; $i<120; $i++) {
                $cur = DB::table('scans')->where('id', $scan)->first();
                if ($cur->progress !== $lastProgress) {
                    echo "data: " . json_encode($cur) . "\n\n";
                    ob_flush(); flush();
                    $lastProgress = $cur->progress;
                }
                if (in_array($cur->status, ['success','partial','failed'])) break;
                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function cancel(string $scan)
    {
        DB::table('scans')->where('id', $scan)->update(['status' => 'cancelling']);
        return response()->json(['status' => 'cancelling']);
    }
}
