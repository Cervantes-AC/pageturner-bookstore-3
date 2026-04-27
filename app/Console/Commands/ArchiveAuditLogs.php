<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArchiveAuditLogs extends Command
{
    protected $signature   = 'audit:archive';
    protected $description = 'Archive audit logs older than 1 year to storage with tamper-proof checksums';

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

        // Attach checksum to each record for tamper-proof verification
        $records = $old->map(function ($record) {
            $data = (array) $record;
            // Compute checksum over the core fields (excluding the checksum itself)
            $data['_archive_checksum'] = $this->computeChecksum($data);
            return $data;
        })->toArray();

        $filename = 'audit_archive_' . now()->format('Ymd_His') . '.json';
        $content  = json_encode([
            'archived_at'    => now()->toIso8601String(),
            'record_count'   => count($records),
            'archive_hash'   => hash('sha256', json_encode($records)),
            'records'        => $records,
        ], JSON_PRETTY_PRINT);

        Storage::disk('local')->put('archives/' . $filename, $content);

        $deleted = DB::table('audits')
            ->where('created_at', '<', $cutoff)
            ->delete();

        Log::info("audit:archive: archived and deleted {$deleted} audit records to {$filename}.");
        $this->info("Archived {$deleted} audit records to {$filename}.");

        return self::SUCCESS;
    }

    /**
     * Compute a SHA-256 checksum over the audit record's immutable fields.
     */
    private function computeChecksum(array $record): string
    {
        $fields = [
            'id', 'user_type', 'user_id', 'event',
            'auditable_type', 'auditable_id',
            'old_values', 'new_values',
            'url', 'ip_address', 'user_agent',
            'created_at',
        ];

        $payload = [];
        foreach ($fields as $field) {
            $payload[$field] = $record[$field] ?? null;
        }

        return hash('sha256', json_encode($payload));
    }
}
