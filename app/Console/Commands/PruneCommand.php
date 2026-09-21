<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PruneCommand extends Command
{
    protected $signature = 'artifacts:prune';
    protected $description = 'Prune raw HTML (30d), artifacts (90d), logs (14d) to stay inside 3GB';

    public function handle(): int
    {
        DB::table('pages')->where('created_at', '<', now()->subDays(30))->delete();
        DB::table('scans')->where('created_at', '<', now()->subDays(90))->delete();
        Storage::disk('local')->deleteDirectory('artifacts/old');
        $this->info('Pruned.');
        return 0;
    }
}
