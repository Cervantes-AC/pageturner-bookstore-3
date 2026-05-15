<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupMonitoring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $this->syncBackupFiles();

        $backups = BackupMonitoring::orderBy('created_at', 'desc')->take(20)->get();
        $lastBackup = BackupMonitoring::where('status', 'success')->latest()->first();
        $dbStatus = $this->checkDatabaseConnection();
        return view('admin.backup.index', compact('backups', 'lastBackup', 'dbStatus'));
    }

    public function run()
    {
        // Check database connection before attempting backup
        $dbCheck = $this->checkDatabaseConnection();
        if (!$dbCheck['connected']) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Database connection failed: ' . $dbCheck['error']);
        }

        $monitor = BackupMonitoring::create([
            'name' => 'Manual backup - ' . now()->format('Y-m-d H:i:s'),
            'status' => 'running',
        ]);

        try {
            $startedAt = now()->subSecond()->timestamp;
            $exitCode = Artisan::call('backup:run', ['--only-db' => true]);
            $output = Artisan::output();
            $latestBackup = $this->latestBackupFile($startedAt);
            $succeeded = $exitCode === 0 && $latestBackup;

            $monitor->update([
                'status' => $succeeded ? 'success' : 'failed',
                'file_path' => $latestBackup['path'] ?? null,
                'size_bytes' => $latestBackup['size'] ?? null,
                'disk' => 'local',
                'output' => $output ?: 'Backup command exited with code ' . $exitCode,
            ]);
        } catch (\Exception $e) {
            $monitor->update([
                'status' => 'failed',
                'output' => $e->getMessage(),
            ]);
        }

        if ($monitor->status === 'failed') {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Backup failed. Check the latest backup history entry for details.');
        }

        return redirect()->route('admin.backup.index')
            ->with('success', 'Backup completed successfully!');
    }

    public function show(BackupMonitoring $backupMonitoring)
    {
        return response()->json($backupMonitoring);
    }

    public function download(BackupMonitoring $backupMonitoring)
    {
        abort_if($backupMonitoring->status !== 'success' || !$backupMonitoring->file_path, 404);
        abort_if(!Storage::disk($backupMonitoring->disk)->exists($backupMonitoring->file_path), 404);

        return Storage::disk($backupMonitoring->disk)->download($backupMonitoring->file_path);
    }

    /**
     * Check if database connection is working
     */
    private function checkDatabaseConnection(): array
    {
        try {
            DB::connection()->getPdo();
            return ['connected' => true, 'error' => null];
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function latestBackupFile(?int $modifiedAfter = null): ?array
    {
        $backupName = config('backup.backup.name');

        return collect(Storage::disk('local')->files($backupName))
            ->filter(fn ($path) => str_ends_with(strtolower($path), '.zip'))
            ->map(fn ($path) => [
                'path' => $path,
                'size' => Storage::disk('local')->size($path),
                'modified' => Storage::disk('local')->lastModified($path),
            ])
            ->when($modifiedAfter, fn ($files) => $files->filter(
                fn ($file) => $file['modified'] >= $modifiedAfter
            ))
            ->sortByDesc('modified')
            ->first();
    }

    private function syncBackupFiles(): void
    {
        $backupName = config('backup.backup.name');

        collect(Storage::disk('local')->files($backupName))
            ->filter(fn ($path) => str_ends_with(strtolower($path), '.zip'))
            ->each(function ($path) {
                BackupMonitoring::firstOrCreate(
                    ['file_path' => $path],
                    [
                        'name' => basename($path),
                        'status' => 'success',
                        'size_bytes' => Storage::disk('local')->size($path),
                        'disk' => 'local',
                        'output' => 'Discovered existing backup file.',
                    ]
                );
            });
    }
}
