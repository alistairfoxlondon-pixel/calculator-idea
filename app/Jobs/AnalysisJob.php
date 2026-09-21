<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class AnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $scanId) {}

    public function middleware(): array { return [new WithoutOverlapping($this->scanId)]; }

    public function handle(): void
    {
        DB::table('scans')->where('id', $this->scanId)->update(['stage'=>'analysing','progress'=>75]);
        // Deterministic analysis: parse HTML, extract title, meta, headings etc.
        $pages = DB::table('pages')->where('scan_id',$this->scanId)->get();
        foreach ($pages as $page) {
            $path = storage_path('app/artifacts/'.$this->scanId.'_'.md5($page->url).'.html');
            // In real impl, analysis would populate checks + evidence
        }
        // Chunked: each page analyzed in ≤20s
        ScoringJob::dispatch($this->scanId);
    }
}
