<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('admin.analytics.index');
    }

    public function data(): JsonResponse
    {
        return response()->json([
            'revenue' => $this->getMonthlyRevenue(),
            'orderStatus' => $this->getOrderStatusBreakdown(),
            'categoryBooks' => $this->getBooksPerCategory(),
            'userGrowth' => $this->getUserGrowth(),
            'ratings' => $this->getRatingDistribution(),
            'topSellers' => $this->getTopSellers(),
            'dailyOrders' => $this->getDailyOrders(),
            'summary' => $this->getSummary(),
            'inventory' => $this->getInventorySummary(),
        ]);
    }

    protected function getMonthlyRevenue(): array
    {
        return Order::whereIn('status', ['completed', 'processing'])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as revenue, COUNT(*) as orders")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get()
            ->toArray();
    }

    protected function getOrderStatusBreakdown(): array
    {
        return Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    protected function getBooksPerCategory(): array
    {
        return Category::withCount(['books' => fn($q) => $q->where('is_active', true)])
            ->orderByDesc('books_count')
            ->get(['name', 'books_count'])
            ->map(fn($c) => ['category' => $c->name, 'count' => $c->books_count])
            ->toArray();
    }

    protected function getUserGrowth(): array
    {
        return User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get()
            ->toArray();
    }

    protected function getRatingDistribution(): array
    {
        $ratings = Review::selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $result = [];
        for ($i = 1; $i <= 5; $i++) {
            $result[] = ['rating' => $i, 'count' => $ratings[$i] ?? 0];
        }
        return $result;
    }

    protected function getTopSellers(): array
    {
        return OrderItem::selectRaw('book_id, SUM(quantity) as total_sold')
            ->groupBy('book_id')
            ->orderByDesc('total_sold')
            ->with('book:id,title,author,price')
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'title' => $item->book?->title ?? 'Unknown',
                'author' => $item->book?->author ?? 'Unknown',
                'sold' => (int) $item->total_sold,
            ])
            ->toArray();
    }

    protected function getDailyOrders(): array
    {
        return Order::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    protected function getSummary(): array
    {
        $completed = Order::whereIn('status', ['completed', 'processing']);
        return [
            'total_revenue' => (float) $completed->sum('total_amount'),
            'total_orders' => Order::count(),
            'total_books' => Book::count(),
            'total_users' => User::count(),
            'total_reviews' => Review::count(),
            'avg_order' => (float) $completed->avg('total_amount') ?? 0,
            'revenue_this_month' => (float) (clone $completed)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            'orders_this_month' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    protected function getInventorySummary(): array
    {
        return [
            'total_stock' => (int) Book::sum('stock_quantity'),
            'low_stock' => Book::where('stock_quantity', '>', 0)->where('stock_quantity', '<', 10)->count(),
            'out_of_stock' => Book::where('stock_quantity', '=', 0)->count(),
            'active_books' => Book::where('is_active', true)->count(),
            'inventory_value' => (float) Book::where('is_active', true)
                ->select(DB::raw('SUM(price * stock_quantity) as total'))->value('total') ?? 0,
        ];
    }
}
