@extends('layouts.app')
@section('title', 'Admin Dashboard — PageTurner')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">Welcome back, {{ auth()->user()->name }} · {{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.import.form') }}" class="btn-secondary btn-sm">Import</a>
            <a href="{{ route('admin.export.form') }}" class="btn-secondary btn-sm">Export</a>
            <a href="{{ route('admin.books.create') }}" class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Book
            </a>
        </div>
    </div>

    {{-- KPI Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <div class="stat-icon bg-blue-50">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-emerald-50">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Books</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalBooks) }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-purple-50">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Categories</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalCategories) }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-amber-50">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Orders</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
            </div>
        </div>
    </div>

    {{-- Main grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Recent Orders --}}
        <div class="lg:col-span-2 card">
            <div class="card-header">
                <h2 class="section-title">Recent Orders</h2>
                <a href="{{ route('orders.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">View all</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                <div class="px-6 py-3.5 flex items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-900">#{{ $order->id }}</span>
                            <span class="status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $order->user->name }} · {{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-sm font-bold text-gray-900 flex-shrink-0">₱{{ number_format($order->total_amount, 2) }}</span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-sm text-gray-400">No orders yet</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Reviews --}}
        <div class="card">
            <div class="card-header">
                <h2 class="section-title">Recent Reviews</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentReviews as $review)
                <div class="px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-900">{{ $review->user->name }}</span>
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 truncate">{{ $review->book->title }}</p>
                    @if($review->comment)
                        <p class="text-xs text-gray-600 mt-1 line-clamp-1">{{ $review->comment }}</p>
                    @endif
                </div>
                @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">No reviews yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Data Management Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        {{-- Import/Export --}}
        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-900">Import / Export</h2>
                <div class="flex gap-2">
                    <a href="{{ route('admin.import.form') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Import</a>
                    <span class="text-gray-300">·</span>
                    <a href="{{ route('admin.export.form') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Export</a>
                </div>
            </div>
            <div class="p-4 space-y-2">
                @forelse($recentImports as $imp)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-700 truncate max-w-[140px]">{{ $imp->filename }}</span>
                    <span class="status-{{ $imp->status === 'completed' ? 'completed' : 'cancelled' }}">{{ ucfirst($imp->status) }}</span>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-3">No imports yet</p>
                @endforelse
            </div>
        </div>

        {{-- Backup --}}
        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-900">Backup</h2>
                <a href="{{ route('admin.backup.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Manage</a>
            </div>
            <div class="p-4">
                @if($lastBackup)
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                    <span class="text-xs font-medium text-emerald-700">Last backup successful</span>
                </div>
                <p class="text-xs text-gray-500">{{ $lastBackup->completed_at?->diffForHumans() }}</p>
                <p class="text-xs text-gray-400">{{ $lastBackup->formatted_size }}</p>
                @else
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0"></span>
                    <span class="text-xs font-medium text-amber-700">No backups recorded</span>
                </div>
                @endif
                <form method="POST" action="{{ route('admin.backup.run') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-primary btn-sm w-full">Run Backup Now</button>
                </form>
            </div>
        </div>

        {{-- Audit Log --}}
        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-900">Audit Events</h2>
                <a href="{{ route('admin.audit.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">View all</a>
            </div>
            <div class="p-4 space-y-2">
                @forelse($recentAuditLogs as $log)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-700">
                        {{ class_basename($log->auditable_type) }}
                        <span class="text-gray-400">{{ $log->event }}</span>
                    </span>
                    <span class="text-gray-400">{{ $log->created_at->diffForHumans(null, true) }}</span>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-3">No audit events yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- System Health --}}
    <div class="card">
        <div class="card-header">
            <h2 class="section-title">System Health</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-100">
            <div class="p-5">
                <p class="text-xs text-gray-500 font-medium mb-1">Database Size</p>
                <p class="text-lg font-bold text-gray-900">
                    {{ $dbSizeBytes > 0 ? round($dbSizeBytes / 1024 / 1024, 2) . ' MB' : 'N/A' }}
                </p>
            </div>
            <div class="p-5">
                <p class="text-xs text-gray-500 font-medium mb-1">Pending Jobs</p>
                <p class="text-lg font-bold {{ $failedJobs > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $failedJobs }}</p>
            </div>
            <div class="p-5">
                <p class="text-xs text-gray-500 font-medium mb-1">Total Books</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($totalBooks) }}</p>
            </div>
            <div class="p-5">
                <p class="text-xs text-gray-500 font-medium mb-1">Total Orders</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
            </div>
        </div>
    </div>

</div>
@endsection
