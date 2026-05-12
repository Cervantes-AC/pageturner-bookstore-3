<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneNotifications extends Command
{
    protected $signature = 'notification:prune';
    protected $description = 'Delete notification records older than 90 days';

    public function handle()
    {
        $cutoff = now()->subDays(90);
        $deleted = DB::table('notifications')->where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$deleted} old notification records.");
        return Command::SUCCESS;
    }
}
