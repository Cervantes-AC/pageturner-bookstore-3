@extends('layouts.app')
@section('title', 'Admin Dashboard - PageTurner')
@section('header')
    <h2 class="text-3xl font-bold text-gray-900">Admin Dashboard</h2>
    <p class="text-gray-600 mt-1">System overview and data management</p>
@endsection

@section('content')
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Books</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $data['totalBooks'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $data['totalOrders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">{{ $data['pendingOrders'] }} pending</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $data['totalUsers'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Backup Health</p>
                    <p class="text-3xl font-bold {{ $data['backupHealth'] === 'healthy' ? 'text-emerald-600' : 'text-red-600' }}">
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
                <p class="text-xs text-gray-500 mt-2">Last: {{ $data['lastBackup']->created_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Quick Actions --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <a href="{{ route('admin.import-export.import') }}" class="p-4 bg-indigo-50 rounded-lg text-center hover:bg-indigo-100 transition-colors">
                        <svg class="w-6 h-6 text-indigo-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span class="text-sm font-medium text-indigo-700">Import Books</span>
                    </a>
                    <a href="{{ route('admin.import-export.export') }}" class="p-4 bg-emerald-50 rounded-lg text-center hover:bg-emerald-100 transition-colors">
                        <svg class="w-6 h-6 text-emerald-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-sm font-medium text-emerald-700">Export Books</span>
                    </a>
                    <a href="{{ route('admin.backup.index') }}" class="p-4 bg-purple-50 rounded-lg text-center hover:bg-purple-100 transition-colors">
                        <svg class="w-6 h-6 text-purple-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="text-sm font-medium text-purple-700">Run Backup</span>
                    </a>
                    <a href="{{ route('admin.audit.index') }}" class="p-4 bg-amber-50 rounded-lg text-center hover:bg-amber-100 transition-colors">
                        <svg class="w-6 h-6 text-amber-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="text-sm font-medium text-amber-700">Audit Logs</span>
                    </a>
                    <a href="{{ route('admin.rate-limits.index') }}" class="p-4 bg-rose-50 rounded-lg text-center hover:bg-rose-100 transition-colors">
                        <svg class="w-6 h-6 text-rose-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-sm font-medium text-rose-700">Rate Limits</span>
                    </a>
                    <a href="{{ route('admin.import-export.exports') }}" class="p-4 bg-teal-50 rounded-lg text-center hover:bg-teal-100 transition-colors">
                        <svg class="w-6 h-6 text-teal-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-sm font-medium text-teal-700">Export Logs</span>
                    </a>
                </div>
            </div>

            {{-- Recent Imports/Exports --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Imports</h3>
                    <div class="space-y-3">
                        @forelse($data['recentImports'] as $import)
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">{{ $import->filename }}</span>
                                    <span class="text-gray-500 ml-2">by {{ $import->user->name ?? 'System' }}</span>
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
                            <p class="text-gray-500 text-sm">No imports yet</p>
                        @endforelse
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Exports</h3>
                    <div class="space-y-3">
                        @forelse($data['recentExports'] as $export)
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <span class="font-medium capitalize text-gray-700">{{ $export->type }} export</span>
                                    <span class="text-gray-500 ml-2">({{ strtoupper($export->format) }})</span>
                                </div>
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $export->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $export->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $export->status === 'processing' ? 'bg-blue-100 text-blue-700' : '' }}">
                                    {{ $export->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No exports yet</p>
                        @endforelse
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Total exports: {{ $data['exportCount'] }}</p>
                </div>
            </div>
        </div>

        {{-- Side Panel --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Health</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Import Success Rate</span>
                        <span class="text-sm font-semibold text-emerald-600">{{ $data['importSuccessRate'] }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Database Size</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $data['dbSize'] }} MB</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Backup Status</span>
                        <span class="text-sm font-semibold {{ $data['backupHealth'] === 'healthy' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ ucfirst($data['backupHealth']) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Audit Events</h3>
                <div class="space-y-3">
                    @forelse($data['recentAudits'] as $audit)
                        <div class="text-sm">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $audit->event }}</span>
                            <span class="text-gray-600 ml-1">{{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}</span>
                            <p class="text-gray-400 text-xs mt-0.5">{{ $audit->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No audit events yet</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.audit.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:text-indigo-700 font-medium">View all logs</a>
            </div>

            @if($data['criticalAudits']->count() > 0)
            <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 p-6">
                <h3 class="text-lg font-semibold text-red-800 mb-4">Security Alerts</h3>
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
