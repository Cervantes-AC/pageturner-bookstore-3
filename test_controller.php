<?php
// Exactly mimic BackupController::run()
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BackupMonitoring;
use Illuminate\Support\Facades\Artisan;

$monitor = BackupMonitoring::create([
    'name' => 'Manual backup - ' . now()->format('Y-m-d H:i:s'),
    'status' => 'running',
]);

try {
    $exitCode = Artisan::call('backup:run');
    $output = Artisan::output();
    $monitor->update([
        'status' => $exitCode === 0 ? 'success' : 'failed',
        'output' => $output ?: 'Backup command exited with code ' . $exitCode,
    ]);
} catch (\Exception $e) {
    $monitor->update([
        'status' => 'failed',
        'output' => $e->getMessage(),
    ]);
}

echo "Record ID: " . $monitor->id . "\n";
echo "Status: " . $monitor->status . "\n";
echo "Output:\n" . $monitor->output . "\n";
