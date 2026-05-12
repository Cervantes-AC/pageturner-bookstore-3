<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiRateLimit;
use Illuminate\Http\Request;

class RateLimitController extends Controller
{
    public function index()
    {
        $stats = [
            'total_hits_today' => ApiRateLimit::whereDate('created_at', today())->sum('hits'),
            'throttled_requests' => ApiRateLimit::whereDate('expires_at', '>=', now())->count(),
            'top_endpoints' => ApiRateLimit::selectRaw('endpoint, SUM(hits) as total_hits')
                ->whereDate('created_at', today())
                ->groupBy('endpoint')
                ->orderByDesc('total_hits')
                ->take(10)
                ->get(),
            'limits' => config('rate-limiting.tiers'),
        ];

        return view('admin.rate-limits.index', compact('stats'));
    }
}
