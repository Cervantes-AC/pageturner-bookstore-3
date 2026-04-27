<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RotateLogs extends Command
{
    protected $signature   = 'log:rotate';
    protected $description = 'Archive and compress old log files';

    public function handle(): int
    {
        $logPath = storage_path('logs');
        $files   = glob($logPath . '/laravel-*.log');
        $count   = 0;

        foreach ($files as $file) {
            $age = now()->diffInDays(\Carbon\Carbon::createFromTimestamp(filemtime($file)));
            if ($age >= 7) {
                $archiveName = 'log_archive_' . basename($file, '.log') . '_' . now()->format('Ymd') . '.gz';
                $gz = gzopen(storage_path('logs/' . $archiveName), 'wb9');
                gzwrite($gz, file_get_contents($file));
                gzclose($gz);
                unlink($file);
                $count++;
            }
        }

        Log::info("log:rotate: archived {$count} log files.");
        $this->info("Rotated {$count} log files.");

        return self::SUCCESS;
    }
}
