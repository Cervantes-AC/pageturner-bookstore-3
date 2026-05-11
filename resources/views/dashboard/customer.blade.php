@extends('layouts.app')
@section('title', 'My Dashboard — PageTurner')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">

    <div class="mb-8 reveal-up">
        <h1 class="text-2xl font-bold text-gray-900">My Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">Welcome back, {{ auth()->user()->name }}</p>
    </div>

    {{-- Account status --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="stat-card reveal-up" style="animation-delay: 0ms;">
            <div class="stat-icon {{ auth()->user()->hasVerifiedEmail() ? 'bg-gradient-to-br from-emerald-50 to-emerald-100' : 'bg-gradient-to-br from-amber-50 to-amber-100' }}">
                <svg class="w-6 h-6 {{ auth()->user()->hasVerifiedEmail() ? 'text-emerald-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="kpi-label">Email</p>
                <p class="text-sm font-bold {{ auth()->user()->hasVerifiedEmail() ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ auth()->user()->hasVerifiedEmail() ? 'Verified' : 'Unverified' }}
                </p>
                @if(!auth()->user()->hasVerifiedEmail())
                    <a href="{{ route('verification.notice') }}" class="text-xs text-primary-600 font-medium hover:text-primary-700 transition-colors">Verify now →</a>
                @endif
            </div>
        </div>
        <div class="stat-card reveal-up" style="animation-delay: 100ms;">
            <div class="stat-icon {{ auth()->user()->two_factor_enabled ? 'bg-gradient-to-br from-emerald-50 to-emerald-100' : 'bg-gradient-to-br from-gray-50 to-gray-100' }}">
                <svg class="w-6 h-6 {{ auth()->user()->two_factor_enabled ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <p class="kpi-label">Two-Factor Auth</p>
                <p class="text-sm font-bold {{ auth()->user()->two_factor_enabled ? 'text-emerald-600' : 'text-gray-500' }}">
                    {{ auth()->user()->two_factor_enabled ? 'Enabled' : 'Disabled' }}
                </p>
                <a href="{{ route('profile.edit') }}" class="text-xs text-primary-600 font-medium hover:text-primary-700 transition-colors">Manage →</a>
            </div>
        </div>
        <div class="stat-card reveal-up" style="animation-delay: 200ms;">
            <div class="stat-icon bg-gradient-to-br from-blue-50 to-blue-100">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <p class="kpi-label">Total Orders</p>
                <p class="kpi-value text-gray-900">{{ $totalOrders }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Recent Orders --}}
        <div class="card reveal-up">
            <div class="card-header">
                <h2 class="section-title">Recent Orders</h2>
                <a href="{{ route('orders.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">View all</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                <div class="px-6 py-3.5 flex items-center justify-between gap-4 hover:bg-primary-50/40 transition-colors">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-900">#{{ $order->id }}</span>
                            <span class="status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('M d, Y') }} · {{ $order->orderItems->count() }} items</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <span class="text-sm font-bold text-gray-900">₱{{ number_format($order->total_amount, 2) }}</span>
                        <a href="{{ route('orders.show', $order) }}" class="btn-ghost btn-sm">View</a>
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-gray-400 mb-3">No orders yet</p>
                    <a href="{{ route('books.index') }}" class="btn-primary btn-sm inline-flex shadow-sm">Start Shopping</a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- My Reviews --}}
        <div class="card reveal-up" style="animation-delay: 100ms;">
            <div class="card-header">
                <h2 class="section-title">My Reviews</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($myReviews as $review)
                <div class="px-6 py-3.5 hover:bg-primary-50/40 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $review->book->title }}</p>
                        <div class="flex flex-shrink-0">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                        <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $review->comment }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->format('M d, Y') }}</p>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-sm text-gray-400">No reviews yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Actions + Data Portability --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-5 reveal-up">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-3 gap-3">
                <a href="{{ route('books.index') }}" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-primary-50 hover:text-primary-700 hover:shadow-sm transition-all duration-200 text-gray-600 text-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="text-xs font-medium">Browse</span>
                </a>
                <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-primary-50 hover:text-primary-700 hover:shadow-sm transition-all duration-200 text-gray-600 text-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="text-xs font-medium">Orders</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-primary-50 hover:text-primary-700 hover:shadow-sm transition-all duration-200 text-gray-600 text-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-xs font-medium">Profile</span>
                </a>
            </div>
        </div>

        <div class="card p-5 reveal-up" style="animation-delay: 100ms;">
            <h3 class="text-sm font-semibold text-gray-900 mb-1">My Data (GDPR)</h3>
            <p class="text-xs text-gray-500 mb-4">Download a copy of your personal data or order history.</p>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('export.my-data') }}" class="btn-secondary btn-sm shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export My Data
                </a>
                <form method="GET" action="{{ route('export.my-orders') }}" class="flex items-center gap-1.5">
                    <select name="format" class="input-field text-xs py-1.5 w-20">
                        <option value="xlsx">XLSX</option>
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                    <button type="submit" class="btn-secondary btn-sm shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Orders
                    </button>
                </form>
            </div>
        </div>

        {{-- Reading History --}}
        <div class="card p-5 lg:col-span-2 reveal-up">
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Reading & Browsing History</h3>
            <p class="text-xs text-gray-500 mb-4">Export your reading history, including books you've purchased and reviewed.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4">
                    <p class="kpi-label">Books Purchased</p>
                    <p class="kpi-value text-gray-900">
                        {{ $totalOrders > 0 ? \App\Models\OrderItem::whereIn('order_id', auth()->user()->orders()->pluck('id'))->count() : 0 }}
                    </p>
                </div>
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4">
                    <p class="kpi-label">Reviews Written</p>
                    <p class="kpi-value text-gray-900">{{ auth()->user()->reviews()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
