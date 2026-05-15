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

        if ($files === false) {
            $this->error('Failed to read log directory.');
            return Command::FAILURE;
        }

        $archived = 0;
        foreach ($files as $file) {
            if (basename($file) === 'laravel.log') continue;
            $info = pathinfo($file);
            $gzFile = $info['dirname'] . '/' . $info['filename'] . '.gz';
            if (!file_exists($gzFile)) {
                $content = file_get_contents($file);
                if ($content === false) {
                    $this->warn("Could not read: {$file}");
                    continue;
                }
                $gzData = gzencode($content, 9);
                if (file_put_contents($gzFile, $gzData) === false) {
                    $this->warn("Could not write: {$gzFile}");
                    continue;
                }
                if (!unlink($file)) {
                    $this->warn("Could not remove: {$file}");
                }
                $archived++;
            }
        }

        $this->info("Archived {$archived} log files.");
        return Command::SUCCESS;
    }
}
