<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupMonitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function index()
    {
        $backups = BackupMonitoring::orderBy('created_at', 'desc')->take(20)->get();
        $lastBackup = BackupMonitoring::where('status', 'success')->latest()->first();
        return view('admin.backup.index', compact('backups', 'lastBackup'));
    }

    public function run()
    {
        $monitor = BackupMonitoring::create([
            'name' => 'Manual backup - ' . now()->format('Y-m-d H:i:s'),
            'status' => 'running',
        ]);

        try {
            Artisan::call('backup:run');
            $output = Artisan::output();
            $monitor->update([
                'status' => 'success',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            $monitor->update([
                'status' => 'failed',
                'output' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.backup.index')
            ->with('success', 'Backup ' . $monitor->status . '!');
    }

    public function show(BackupMonitoring $backupMonitoring)
    {
        return response()->json($backupMonitoring);
    }
}
