<?php

namespace Database\Seeders;

use App\Models\BackupMonitoring;
use App\Models\ExportLog;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Lab6Seeder — Seeds mock data for Lab Activity 6 features:
 *   - Import logs (simulating past book imports)
 *   - Export logs (simulating past exports)
 *   - Backup monitoring records
 *   - Mock audit log entries
 */
class Lab6Seeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('role', 'admin')->first();
        $customer = User::where('role', 'customer')->first();

        if (!$admin) {
            $this->command->warn('No admin user found. Run DatabaseSeeder first.');
            return;
        }

        // ── Import Logs ───────────────────────────────────────────────────────
        $importLogs = [
            [
                'user_id'        => $admin->id,
                'filename'       => 'books_bulk_jan2026.xlsx',
                'type'           => 'books',
                'total_rows'     => 500,
                'processed_rows' => 498,
                'failed_rows'    => 2,
                'status'         => 'completed',
                'mode'           => 'skip',
                'errors'         => json_encode([
                    ['row' => 45,  'attribute' => 'isbn',  'errors' => ['ISBN already exists'], 'values' => []],
                    ['row' => 312, 'attribute' => 'price', 'errors' => ['Price must be positive'], 'values' => []],
                ]),
                'created_at'     => now()->subDays(30),
                'updated_at'     => now()->subDays(30),
            ],
            [
                'user_id'        => $admin->id,
                'filename'       => 'books_feb2026.csv',
                'type'           => 'books',
                'total_rows'     => 1200,
                'processed_rows' => 1200,
                'failed_rows'    => 0,
                'status'         => 'completed',
                'mode'           => 'update',
                'errors'         => null,
                'created_at'     => now()->subDays(15),
                'updated_at'     => now()->subDays(15),
            ],
            [
                'user_id'        => $admin->id,
                'filename'       => 'books_malformed.xlsx',
                'type'           => 'books',
                'total_rows'     => 0,
                'processed_rows' => 0,
                'failed_rows'    => 0,
                'status'         => 'failed',
                'mode'           => 'skip',
                'errors'         => json_encode([
                    ['row' => 1, 'attribute' => 'file', 'errors' => ['File format not recognized'], 'values' => []],
                ]),
                'created_at'     => now()->subDays(5),
                'updated_at'     => now()->subDays(5),
            ],
        ];

        foreach ($importLogs as $log) {
            ImportLog::create($log);
        }

        $this->command->info('✓ Seeded ' . count($importLogs) . ' import logs');

        // ── Export Logs ───────────────────────────────────────────────────────
        $exportLogs = [
            [
                'user_id'       => $admin->id,
                'type'          => 'books',
                'format'        => 'xlsx',
                'filters'       => json_encode(['category_id' => 1, 'in_stock' => true]),
                'status'        => 'completed',
                'download_link' => null,
                'created_at'    => now()->subDays(20),
                'updated_at'    => now()->subDays(20),
            ],
            [
                'user_id'       => $admin->id,
                'type'          => 'orders',
                'format'        => 'csv',
                'filters'       => json_encode(['status' => 'completed', 'date_from' => '2026-01-01']),
                'status'        => 'completed',
                'download_link' => null,
                'created_at'    => now()->subDays(10),
                'updated_at'    => now()->subDays(10),
            ],
            [
                'user_id'       => $admin->id,
                'type'          => 'users',
                'format'        => 'xlsx',
                'filters'       => json_encode(['redact_pii' => true]),
                'status'        => 'completed',
                'download_link' => null,
                'created_at'    => now()->subDays(7),
                'updated_at'    => now()->subDays(7),
            ],
            [
                'user_id'       => $customer ? $customer->id : $admin->id,
                'type'          => 'my_orders',
                'format'        => 'xlsx',
                'filters'       => json_encode([]),
                'status'        => 'completed',
                'download_link' => null,
                'created_at'    => now()->subDays(3),
                'updated_at'    => now()->subDays(3),
            ],
        ];

        foreach ($exportLogs as $log) {
            ExportLog::create($log);
        }

        $this->command->info('✓ Seeded ' . count($exportLogs) . ' export logs');

        // ── Backup Monitoring ─────────────────────────────────────────────────
        $backupRecords = [
            [
                'name'         => config('app.name', 'PageTurner'),
                'status'       => 'success',
                'disk'         => 'local',
                'size'         => 5242880, // 5 MB
                'path'         => 'PageTurner/2026-04-20-02-00-00.zip',
                'message'      => 'Backup completed successfully.',
                'completed_at' => now()->subDays(7),
                'created_at'   => now()->subDays(7),
                'updated_at'   => now()->subDays(7),
            ],
            [
                'name'         => config('app.name', 'PageTurner'),
                'status'       => 'failed',
                'disk'         => 'local',
                'size'         => null,
                'path'         => null,
                'message'      => 'Backup failed: Disk space insufficient.',
                'completed_at' => now()->subDays(6),
                'created_at'   => now()->subDays(6),
                'updated_at'   => now()->subDays(6),
            ],
            [
                'name'         => config('app.name', 'PageTurner'),
                'status'       => 'success',
                'disk'         => 'local',
                'size'         => 5505024, // ~5.25 MB
                'path'         => 'PageTurner/2026-04-21-02-00-00.zip',
                'message'      => 'Backup completed successfully.',
                'completed_at' => now()->subDays(1),
                'created_at'   => now()->subDays(1),
                'updated_at'   => now()->subDays(1),
            ],
        ];

        foreach ($backupRecords as $record) {
            BackupMonitoring::create($record);
        }

        $this->command->info('✓ Seeded ' . count($backupRecords) . ' backup monitoring records');

        // ── Mock Audit Logs ───────────────────────────────────────────────────
        // Simulate audit entries for various models and events
        $auditEntries = [
            [
                'user_type'      => 'App\\Models\\User',
                'user_id'        => $admin->id,
                'event'          => 'created',
                'auditable_type' => 'App\\Models\\Book',
                'auditable_id'   => 1,
                'old_values'     => json_encode([]),
                'new_values'     => json_encode(['title' => 'Atomic Habits', 'price' => 549, 'stock_quantity' => 88]),
                'url'            => '/admin/books',
                'ip_address'     => '127.0.0.1',
                'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'tags'           => null,
                'created_at'     => now()->subDays(25),
                'updated_at'     => now()->subDays(25),
            ],
            [
                'user_type'      => 'App\\Models\\User',
                'user_id'        => $admin->id,
                'event'          => 'updated',
                'auditable_type' => 'App\\Models\\Book',
                'auditable_id'   => 1,
                'old_values'     => json_encode(['price' => 549, 'stock_quantity' => 88]),
                'new_values'     => json_encode(['price' => 599, 'stock_quantity' => 75]),
                'url'            => '/admin/books/1',
                'ip_address'     => '127.0.0.1',
                'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'tags'           => null,
                'created_at'     => now()->subDays(20),
                'updated_at'     => now()->subDays(20),
            ],
            [
                'user_type'      => 'App\\Models\\User',
                'user_id'        => $admin->id,
                'event'          => 'created',
                'auditable_type' => 'App\\Models\\Category',
                'auditable_id'   => 1,
                'old_values'     => json_encode([]),
                'new_values'     => json_encode(['name' => 'Fiction', 'description' => 'Fictional stories']),
                'url'            => '/admin/categories',
                'ip_address'     => '127.0.0.1',
                'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'tags'           => null,
                'created_at'     => now()->subDays(30),
                'updated_at'     => now()->subDays(30),
            ],
            [
                'user_type'      => 'App\\Models\\User',
                'user_id'        => $customer ? $customer->id : $admin->id,
                'event'          => 'created',
                'auditable_type' => 'App\\Models\\Order',
                'auditable_id'   => 1,
                'old_values'     => json_encode([]),
                'new_values'     => json_encode(['status' => 'pending', 'total_amount' => 549]),
                'url'            => '/orders',
                'ip_address'     => '192.168.1.100',
                'user_agent'     => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0)',
                'tags'           => null,
                'created_at'     => now()->subDays(10),
                'updated_at'     => now()->subDays(10),
            ],
            [
                'user_type'      => 'App\\Models\\User',
                'user_id'        => $admin->id,
                'event'          => 'updated',
                'auditable_type' => 'App\\Models\\Order',
                'auditable_id'   => 1,
                'old_values'     => json_encode(['status' => 'pending']),
                'new_values'     => json_encode(['status' => 'completed']),
                'url'            => '/orders/1/status',
                'ip_address'     => '127.0.0.1',
                'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'tags'           => null,
                'created_at'     => now()->subDays(9),
                'updated_at'     => now()->subDays(9),
            ],
            [
                'user_type'      => 'App\\Models\\User',
                'user_id'        => $customer ? $customer->id : $admin->id,
                'event'          => 'created',
                'auditable_type' => 'App\\Models\\Review',
                'auditable_id'   => 1,
                'old_values'     => json_encode([]),
                'new_values'     => json_encode(['rating' => 5, 'comment' => 'Excellent book!']),
                'url'            => '/books/1/reviews',
                'ip_address'     => '192.168.1.100',
                'user_agent'     => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0)',
                'tags'           => null,
                'created_at'     => now()->subDays(8),
                'updated_at'     => now()->subDays(8),
            ],
            [
                'user_type'      => 'App\\Models\\User',
                'user_id'        => $admin->id,
                'event'          => 'deleted',
                'auditable_type' => 'App\\Models\\Book',
                'auditable_id'   => 99,
                'old_values'     => json_encode(['title' => 'Old Book', 'price' => 199, 'stock_quantity' => 0]),
                'new_values'     => json_encode([]),
                'url'            => '/admin/books/99',
                'ip_address'     => '127.0.0.1',
                'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'tags'           => null,
                'created_at'     => now()->subDays(3),
                'updated_at'     => now()->subDays(3),
            ],
        ];

        // Compute checksums for each mock audit entry
        foreach ($auditEntries as &$entry) {
            $payload = [
                'user_id'        => $entry['user_id'],
                'event'          => $entry['event'],
                'auditable_type' => $entry['auditable_type'],
                'auditable_id'   => $entry['auditable_id'],
                'old_values'     => $entry['old_values'],
                'new_values'     => $entry['new_values'],
                'ip_address'     => $entry['ip_address'],
                'url'            => $entry['url'],
            ];
            $entry['checksum'] = hash('sha256', json_encode($payload));
        }
        unset($entry);

        DB::table('audits')->insert($auditEntries);

        $this->command->info('✓ Seeded ' . count($auditEntries) . ' mock audit log entries');
    }
}
