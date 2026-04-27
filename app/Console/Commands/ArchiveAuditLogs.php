<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArchiveAuditLogs extends Command
{
    protected $signature   = 'audit:archive';
    protected $description = 'Archive audit logs older than 1 year to storage';

    public function handle(): int
    {
        $cutoff = now()->subYear();

        $old = DB::table('audits')
            ->where('created_at', '<', $cutoff)
            ->get();

        if ($old->isEmpty()) {
            $this->info('No audit logs to archive.');
            return self::SUCCESS;
        }

        $filename = 'audit_archive_' . now()->format('Ymd_His') . '.json';
        Storage::disk('local')->put(
            'archives/' . $filename,
            $old->toJson(JSON_PRETTY_PRINT)
        );

        $deleted = DB::table('audits')
            ->where('created_at', '<', $cutoff)
            ->delete();

        Log::info("audit:archive: archived and deleted {$deleted} audit records to {$filename}.");
        $this->info("Archived {$deleted} audit records to {$filename}.");

        return self::SUCCESS;
    }
}
