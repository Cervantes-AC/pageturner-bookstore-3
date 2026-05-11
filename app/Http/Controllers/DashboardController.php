<?php

namespace App\Http\Controllers;

use App\Models\ApiRateLimit;
use App\Models\BackupMonitoring;
use App\Models\Book;
use App\Models\Category;
use App\Models\ExportLog;
use App\Models\ImportLog;
use App\Models\Order;
use App\Models\Review;
use App\Models\ScheduledTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Models\Audit;

class DashboardController extends Controller
{
    public function admin()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $totalUsers      = User::count();
        $totalBooks      = Book::count();
        $totalCategories = Category::count();
        $totalOrders     = Order::count();

        $recentOrders = Order::with(['user', 'orderItems.book'])
            ->latest()->take(10)->get();

        $orderStatusSummary = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $recentReviews = Review::with(['user', 'book'])
            ->latest()->take(5)->get();

        // Lab 6 additions
        $recentImports   = ImportLog::with('user')->latest()->take(5)->get();
        $recentExports   = ExportLog::with('user')->latest()->take(5)->get();
        $lastBackup      = BackupMonitoring::where('status', 'success')->latest()->first();
        $recentAuditLogs = Audit::with('user')->latest()->take(5)->get();

        // API Usage Statistics
        $apiStats = [
            'total_requests'     => ApiRateLimit::count(),
            'throttled_requests' => ApiRateLimit::where('throttled', true)->count(),
            'requests_today'     => ApiRateLimit::whereDate('created_at', today())->count(),
            'throttled_today'    => ApiRateLimit::whereDate('created_at', today())->where('throttled', true)->count(),
            'by_tier'            => ApiRateLimit::selectRaw('tier, COUNT(*) as count')
                                     ->groupBy('tier')->pluck('count', 'tier')->toArray(),
        ];

        // System Health
        $dbSizeBytes = 0;
        try {
            $dbPath = database_path('database.sqlite');
            if (file_exists($dbPath)) {
                $dbSizeBytes = filesize($dbPath);
            }
        } catch (\Exception $e) {}

        $failedJobs    = DB::table('failed_jobs')->count();
        $queueSize     = DB::table('jobs')->count();
        $storageUsage  = 0;
        try {
            $storagePath = storage_path('app');
            if (is_dir($storagePath)) {
                $size = 0;
                foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($storagePath)) as $file) {
                    if ($file->isFile()) $size += $file->getSize();
                }
                $storageUsage = $size;
            }
        } catch (\Exception $e) {}

        $recentScheduledTasks = ScheduledTask::latest()->take(5)->get();
        $failedScheduledTasks = ScheduledTask::where('status', 'failed')->count();

        return view('dashboard.admin', compact(
            'totalUsers', 'totalBooks', 'totalCategories', 'totalOrders',
            'recentOrders', 'orderStatusSummary', 'recentReviews',
            'recentImports', 'recentExports', 'lastBackup',
            'recentAuditLogs', 'dbSizeBytes', 'failedJobs',
            'apiStats', 'queueSize', 'storageUsage',
            'recentScheduledTasks', 'failedScheduledTasks'
        ));
    }

    public function customer()
    {
        $user = auth()->user();

        $totalOrders     = $user->orders()->count();
        $pendingOrders   = $user->orders()->where('status', 'pending')->count();
        $completedOrders = $user->orders()->where('status', 'completed')->count();

        $recentOrders = $user->orders()
            ->with(['orderItems.book'])->latest()->take(5)->get();

        $recentPurchases = Book::whereHas('orderItems.order', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', 'completed');
        })->with('category')->latest()->take(6)->get();

        $myReviews = $user->reviews()->with('book')->latest()->take(5)->get();

        return view('dashboard.customer', compact(
            'totalOrders', 'pendingOrders', 'completedOrders',
            'recentOrders', 'recentPurchases', 'myReviews'
        ));
    }
}
