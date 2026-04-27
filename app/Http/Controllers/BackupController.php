<?php

namespace App\Http\Controllers;

use App\Models\BackupMonitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $backups = BackupMonitoring::latest()->take(20)->get();
        return view('backup.index', compact('backups'));
    }

    public function run()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $monitor = BackupMonitoring::create([
            'name'   => config('app.name', 'pageturner'),
            'status' => 'running',
            'disk'   => 'local',
        ]);

        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            $output = Artisan::output();

            $monitor->update([
                'status'       => 'success',
                'message'      => 'Manual backup completed successfully.',
                'completed_at' => now(),
            ]);

            return back()->with('success', 'Backup completed successfully!');
        } catch (\Exception $e) {
            $monitor->update([
                'status'       => 'failed',
                'message'      => $e->getMessage(),
                'completed_at' => now(),
            ]);

            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
}
