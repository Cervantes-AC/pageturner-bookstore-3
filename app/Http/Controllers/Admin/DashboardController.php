<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BackupMonitoring;
use App\Models\ImportLog;
use App\Models\ExportLog;
use App\Models\Book;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalBooks' => Book::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => (float) Order::whereIn('status', ['completed', 'processing'])->sum('total_amount'),
            'revenueThisMonth' => (float) Order::whereIn('status', ['completed', 'processing'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            'ordersThisMonth' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'avgOrderValue' => (float) Order::whereIn('status', ['completed', 'processing'])->avg('total_amount') ?? 0,
            'totalUsers' => User::count(),
            'recentOrders' => Order::with('user')->latest()->take(5)->get(),
            'orderStatusSummary' => Order::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'recentReviews' => Review::with('user', 'book')->latest()->take(5)->get(),
            'recentImports' => ImportLog::with('user')->latest()->take(5)->get(),
            'recentExports' => ExportLog::with('user')->latest()->take(5)->get(),
            'lastBackup' => BackupMonitoring::where('status', 'success')->latest()->first(),
            'backupHealth' => BackupMonitoring::where('created_at', '>=', now()->subDays(7))->where('status', 'success')->exists() ? 'healthy' : 'unhealthy',
            'recentAudits' => AuditLog::with('user')->latest()->take(5)->get(),
            'criticalAudits' => AuditLog::whereIn('event', ['permission_changed', 'role_assigned', 'admin_action'])->latest()->take(5)->get(),
            'importSuccessRate' => $this->getImportSuccessRate(),
            'exportCount' => ExportLog::where('status', 'completed')->count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'dbSize' => $this->getDatabaseSize(),
            'monthlyRevenue' => Order::whereIn('status', ['completed', 'processing'])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as revenue, COUNT(*) as orders")
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get(),
        ];

        return view('admin.dashboard', compact('data'));
    }

    protected function getImportSuccessRate()
    {
        $total = ImportLog::count();
        if ($total === 0) return 100;
        $failed = ImportLog::where('status', 'failed')->count();
        return round((($total - $failed) / $total) * 100);
    }

    protected function getDatabaseSize()
    {
        try {
            $result = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.tables WHERE table_schema = ?", [DB::connection()->getDatabaseName()]);
            return $result[0]->size_mb ?? 0;
        } catch (\Exception $e) {
            try {
                $path = database_path('database.sqlite');
                if (file_exists($path)) {
                    return round(filesize($path) / 1024 / 1024, 2);
                }
            } catch (\Exception $e2) {}
            return 0;
        }
    }
}
