<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RotateLogs extends Command
{
    protected $signature = 'log:rotate';
    protected $description = 'Archive and compress old log files';

    public function handle()
    {
        $logPath = storage_path('logs');
        $files = glob($logPath . '/*.log');

        $archived = 0;
        foreach ($files as $file) {
            if (basename($file) === 'laravel.log') continue;
            $info = pathinfo($file);
            $gzFile = $info['dirname'] . '/' . $info['filename'] . '.gz';
            if (!file_exists($gzFile)) {
                $gzData = gzencode(file_get_contents($file), 9);
                file_put_contents($gzFile, $gzData);
                unlink($file);
                $archived++;
            }
        }

        $this->info("Archived {$archived} log files.");
        return Command::SUCCESS;
    }
}
