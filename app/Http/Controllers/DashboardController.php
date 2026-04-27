<?php

namespace App\Http\Controllers;

use App\Models\BackupMonitoring;
use App\Models\Book;
use App\Models\Category;
use App\Models\ExportLog;
use App\Models\ImportLog;
use App\Models\Order;
use App\Models\Review;
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

        $dbSizeBytes = 0;
        try {
            $dbPath = database_path('database.sqlite');
            if (file_exists($dbPath)) {
                $dbSizeBytes = filesize($dbPath);
            }
        } catch (\Exception $e) {}

        $failedJobs = DB::table('jobs')->count();

        return view('dashboard.admin', compact(
            'totalUsers', 'totalBooks', 'totalCategories', 'totalOrders',
            'recentOrders', 'orderStatusSummary', 'recentReviews',
            'recentImports', 'recentExports', 'lastBackup',
            'recentAuditLogs', 'dbSizeBytes', 'failedJobs'
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
