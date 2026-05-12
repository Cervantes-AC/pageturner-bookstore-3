<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;

class AuditArchive extends Command
{
    protected $signature = 'audit:archive';
    protected $description = 'Archive audit logs older than 1 year';

    public function handle()
    {
        $cutoff = now()->subYear();
        $count = AuditLog::where('created_at', '<', $cutoff)->count();

        $this->info("Found {$count} audit records older than 1 year ready for archival.");

        return Command::SUCCESS;
    }
}
