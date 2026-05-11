<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SessionCleanup extends Command
{
    protected $signature   = 'session:cleanup';
    protected $description = 'Clear expired sessions from the database';

    public function handle(): int
    {
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', now()->subMinutes(config('session.lifetime', 120))->getTimestamp())
            ->delete();

        Log::info("session:cleanup: deleted {$deleted} expired sessions.");
        $this->info("Deleted {$deleted} expired sessions.");

        return self::SUCCESS;
    }
}
