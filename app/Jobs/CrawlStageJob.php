<?php

namespace App\Jobs;

use App\Domain\Crawl\Crawler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class CrawlStageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $scanId) {}

    public function middleware(): array
    {
        return [new WithoutOverlapping($this->scanId)];
    }

    public function handle(): void
    {
        $scan = DB::table('scans')->where('id', $this->scanId)->first();
        if (!$scan || $scan->status === 'cancelling') return;

        DB::table('scans')->where('id', $this->scanId)->update(['status' => 'running', 'stage' => 'crawling']);

        $crawler = new Crawler();
        $pages = $crawler->crawl($scan->url, function($stage, $progress, $msg, $log=null) {
            DB::table('scans')->where('id', $this->scanId)->update(['stage' => $stage, 'progress' => $progress]);
            if ($log) {
                // append to live_log JSON
                $s = DB::table('scans')->where('id', $this->scanId)->first();
                $logs = json_decode($s->live_log ?? '[]', true);
                array_unshift($logs, $log);
                $logs = array_slice($logs, 0, 200);
                DB::table('scans')->where('id', $this->scanId)->update(['live_log' => json_encode($logs)]);
            }
        });

        foreach ($pages as $p) {
            DB::table('pages')->insert([
                'scan_id' => $this->scanId,
                'url' => $p['url'],
                'normalized_url' => $p['url'],
                'status_code' => $p['status'],
                'bytes' => $p['bytes'],
                'total_ms' => $p['total_ms'],
                'headers' => json_encode($p['headers'] ?? []),
                'fetched_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('scans')->where('id', $this->scanId)->update(['pages_crawled' => count($pages)]);

        // Gate check
        $gates = ['seo-audit'=>10,'adsense-audit'=>15,'speed-audit'=>3,'link-audit'=>5];
        $gate = $gates[$scan->tool] ?? 10;
        if (count($pages) < $gate) {
            DB::table('scans')->where('id', $this->scanId)->update(['status'=>'failed','error_message'=>"only ".count($pages)." pages reachable; gate is $gate"]);
            return;
        }
        if (count($pages) === 0) {
            DB::table('scans')->where('id', $this->scanId)->update(['status'=>'failed','error_message'=>'Zero pages fetched — error screen, never a score']);
            return;
        }

        AnalysisJob::dispatch($this->scanId);
    }
}
