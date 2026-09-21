<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\CrawlStageJob;

class TickCommand extends Command
{
    protected $signature = 'scans:tick';
    protected $description = 'Resume stalled scans (cron-driven, no persistent worker)';

    public function handle(): int
    {
        $stalled = DB::table('scans')
            ->whereIn('status', ['queued','running'])
            ->where('updated_at', '<', now()->subMinutes(2))
            ->get();
        foreach ($stalled as $scan) {
            $this->info("Resuming stalled scan {$scan->id}");
            CrawlStageJob::dispatch($scan->id);
        }
        return 0;
    }
}
