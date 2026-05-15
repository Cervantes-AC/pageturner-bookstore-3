<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalOrders = Order::where('user_id', $user->id)->count();
        $recentOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();
        $orderStatusSummary = Order::where('user_id', $user->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $recentReviews = Review::with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentOrderBooks = Order::where('user_id', $user->id)
            ->with('orderItems.book')
            ->latest()
            ->take(3)
            ->get()
            ->flatMap(fn($o) => $o->orderItems->pluck('book'))
            ->unique('id')
            ->take(5);

        return view('dashboard', compact(
            'totalOrders',
            'recentOrders',
            'orderStatusSummary',
            'recentReviews',
            'recentOrderBooks'
        ));
    }
}
