@extends('layouts.app')
@section('title', 'Admin Dashboard - PageTurner')
@section('header')
    <h2 class="font-heading text-3xl font-bold text-ink-900">Admin Dashboard</h2>
    <p class="text-ink-400 mt-1">System overview and data management</p>
@endsection

@section('content')
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-ink-400">Total Books</p>
                    <p class="font-heading text-3xl font-bold text-ink-900">{{ $data['totalBooks'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-ink-400">Total Orders</p>
                    <p class="font-heading text-3xl font-bold text-ink-900">{{ $data['totalOrders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-ink-400 mt-2">{{ $data['pendingOrders'] }} pending</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-ink-400">Total Revenue</p>
                    <p class="font-heading text-3xl font-bold text-emerald-700">₱{{ number_format($data['totalRevenue'], 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-ink-400 mt-2">₱{{ number_format($data['revenueThisMonth'], 0) }} this month</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-ink-400">Total Users</p>
                    <p class="font-heading text-3xl font-bold text-ink-900">{{ $data['totalUsers'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-ink-400">Backup Health</p>
                    <p class="font-heading text-3xl font-bold {{ $data['backupHealth'] === 'healthy' ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ ucfirst($data['backupHealth']) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
            </div>
            @if($data['lastBackup'])
                <p class="text-xs text-ink-400 mt-2">Last: {{ $data['lastBackup']->created_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <a href="{{ route('admin.import-export.import') }}" class="p-4 bg-indigo-50 rounded-lg text-center hover:bg-indigo-100 transition-colors">
                        <svg class="w-6 h-6 text-indigo-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        <span class="text-sm font-medium text-indigo-700">Import Books</span>
                    </a>
                    <a href="{{ route('admin.import-export.export') }}" class="p-4 bg-emerald-50 rounded-lg text-center hover:bg-emerald-100 transition-colors">
                        <svg class="w-6 h-6 text-emerald-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span class="text-sm font-medium text-emerald-700">Export Books</span>
                    </a>
                    <a href="{{ route('admin.backup.index') }}" class="p-4 bg-purple-50 rounded-lg text-center hover:bg-purple-100 transition-colors">
                        <svg class="w-6 h-6 text-purple-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <span class="text-sm font-medium text-purple-700">Run Backup</span>
                    </a>
                    <a href="{{ route('admin.audit.index') }}" class="p-4 bg-amber-50 rounded-lg text-center hover:bg-amber-100 transition-colors">
                        <svg class="w-6 h-6 text-amber-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        <span class="text-sm font-medium text-amber-700">Audit Logs</span>
                    </a>
                    <a href="{{ route('admin.rate-limits.index') }}" class="p-4 bg-rose-50 rounded-lg text-center hover:bg-rose-100 transition-colors">
                        <svg class="w-6 h-6 text-rose-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        <span class="text-sm font-medium text-rose-700">Rate Limits</span>
                    </a>
                    <a href="{{ route('admin.import-export.exports') }}" class="p-4 bg-teal-50 rounded-lg text-center hover:bg-teal-100 transition-colors">
                        <svg class="w-6 h-6 text-teal-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span class="text-sm font-medium text-teal-700">Export Logs</span>
                    </a>
                </div>
            </div>

            {{-- Revenue Overview --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Revenue Overview</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div class="bg-emerald-50 rounded-lg p-4 text-center">
                        <p class="text-xs font-medium text-ink-400 uppercase tracking-wide">Total Revenue</p>
                        <p class="font-heading text-2xl font-bold text-emerald-700 mt-1">₱{{ number_format($data['totalRevenue'], 0) }}</p>
                    </div>
                    <div class="bg-gold-50 rounded-lg p-4 text-center">
                        <p class="text-xs font-medium text-ink-400 uppercase tracking-wide">This Month</p>
                        <p class="font-heading text-2xl font-bold text-gold-700 mt-1">₱{{ number_format($data['revenueThisMonth'], 0) }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <p class="text-xs font-medium text-ink-400 uppercase tracking-wide">Avg Order Value</p>
                        <p class="font-heading text-2xl font-bold text-blue-700 mt-1">₱{{ number_format($data['avgOrderValue'], 2) }}</p>
                    </div>
                </div>
                @if($data['monthlyRevenue']->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-parchment-200">
                                    <th class="text-left py-2 text-ink-500 font-medium">Month</th>
                                    <th class="text-right py-2 text-ink-500 font-medium">Orders</th>
                                    <th class="text-right py-2 text-ink-500 font-medium">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['monthlyRevenue'] as $row)
                                    <tr class="border-b border-parchment-100">
                                        <td class="py-2 text-ink-700">{{ $row->month }}</td>
                                        <td class="py-2 text-right text-ink-700">{{ $row->orders }}</td>
                                        <td class="py-2 text-right text-ink-700 font-medium">₱{{ number_format($row->revenue, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-ink-400 text-sm">No revenue data yet</p>
                @endif
            </div>

            {{-- Recent Orders --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading text-lg font-semibold text-ink-900">Recent Orders</h3>
                    <a href="{{ route('orders.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-medium">View all</a>
                </div>
                <div class="space-y-3">
                    @forelse($data['recentOrders'] as $order)
                        <div class="flex items-center justify-between text-sm p-3 bg-parchment-50 rounded-lg">
                            <div>
                                <span class="font-medium text-ink-700">Order #{{ $order->id }}</span>
                                <span class="text-ink-400 ml-2">by {{ $order->user->name ?? 'N/A' }}</span>
                                <span class="text-ink-400 ml-2">₱{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-medium
                                {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $order->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-ink-400 text-sm">No orders yet</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Reviews --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Recent Reviews</h3>
                <div class="space-y-3">
                    @forelse($data['recentReviews'] as $review)
                        <div class="text-sm p-3 bg-parchment-50 rounded-lg">
                            <div class="flex items-center space-x-1 mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-gold-500' : 'text-parchment-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                @endfor
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-medium text-ink-700">{{ $review->user->name ?? 'N/A' }}</span>
                                    <span class="text-ink-400"> on </span>
                                    <a href="{{ route('books.show', $review->book) }}" class="text-gold-600 hover:underline">{{ $review->book->title ?? 'Unknown' }}</a>
                                </div>
                                <span class="text-ink-400 text-xs">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            @if($review->comment)
                                <p class="text-ink-400 mt-1">"{{ Str::limit($review->comment, 80) }}"</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-ink-400 text-sm">No reviews yet</p>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                    <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Recent Imports</h3>
                    <div class="space-y-3">
                        @forelse($data['recentImports'] as $import)
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <span class="font-medium text-ink-700">{{ $import->filename }}</span>
                                    <span class="text-ink-400 ml-2">by {{ $import->user->name ?? 'System' }}</span>
                                </div>
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $import->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $import->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $import->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $import->status === 'completed_with_errors' ? 'bg-orange-100 text-orange-700' : '' }}">
                                    {{ $import->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-ink-400 text-sm">No imports yet</p>
                        @endforelse
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                    <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Recent Exports</h3>
                    <div class="space-y-3">
                        @forelse($data['recentExports'] as $export)
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <span class="font-medium capitalize text-ink-700">{{ $export->type }} export</span>
                                    <span class="text-ink-400 ml-2">({{ strtoupper($export->format) }})</span>
                                </div>
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $export->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $export->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $export->status === 'processing' ? 'bg-blue-100 text-blue-700' : '' }}">
                                    {{ $export->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-ink-400 text-sm">No exports yet</p>
                        @endforelse
                    </div>
                    <p class="text-xs text-ink-400 mt-3">Total exports: {{ $data['exportCount'] }}</p>
                </div>
            </div>
        </div>

        {{-- Side Panel --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Order Status Summary</h3>
                <div class="space-y-3">
                    @foreach(['pending' => 'bg-amber-100 text-amber-700', 'processing' => 'bg-blue-100 text-blue-700', 'completed' => 'bg-emerald-100 text-emerald-700', 'cancelled' => 'bg-red-100 text-red-700'] as $status => $classes)
                        <div class="flex justify-between items-center">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $classes }}">{{ ucfirst($status) }}</span>
                            <span class="text-sm font-semibold text-ink-900">{{ $data['orderStatusSummary'][$status] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Revenue Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-ink-400">Avg Order Value</span>
                        <span class="text-sm font-semibold text-ink-900">₱{{ number_format($data['avgOrderValue'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-ink-400">Orders This Month</span>
                        <span class="text-sm font-semibold text-ink-900">{{ $data['ordersThisMonth'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-ink-400">Revenue This Month</span>
                        <span class="text-sm font-semibold text-emerald-600">₱{{ number_format($data['revenueThisMonth'], 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">System Health</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-ink-400">Import Success Rate</span>
                        <span class="text-sm font-semibold text-emerald-600">{{ $data['importSuccessRate'] }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-ink-400">Database Size</span>
                        <span class="text-sm font-semibold text-ink-900">{{ $data['dbSize'] }} MB</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-ink-400">Backup Status</span>
                        <span class="text-sm font-semibold {{ $data['backupHealth'] === 'healthy' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ ucfirst($data['backupHealth']) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Recent Audit Events</h3>
                <div class="space-y-3">
                    @forelse($data['recentAudits'] as $audit)
                        <div class="text-sm">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-parchment-100 text-ink-700">{{ $audit->event }}</span>
                            <span class="text-ink-400 ml-1">{{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}</span>
                            <p class="text-ink-400 text-xs mt-0.5">{{ $audit->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-ink-400 text-sm">No audit events yet</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.audit.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:text-indigo-700 font-medium">View all logs</a>
            </div>

            @if($data['criticalAudits']->count() > 0)
            <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-red-800 mb-4">Security Alerts</h3>
                <div class="space-y-3">
                    @foreach($data['criticalAudits'] as $audit)
                        <div class="text-sm">
                            <span class="font-medium text-red-700">{{ $audit->event }}</span>
                            <p class="text-red-600">{{ $audit->user->name ?? 'Unknown' }} - {{ $audit->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
