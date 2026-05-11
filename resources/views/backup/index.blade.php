@extends('layouts.app')
@section('title', 'Backup Management — PageTurner')

@section('content')
<div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Backup Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Monitor, run, and manage automated backups</p>
        </div>
        <form method="POST" action="{{ route('admin.backup.run') }}">
            @csrf
            <button type="submit"
                    onclick="return confirm('Run a manual backup now?')"
                    class="px-5 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Run Backup Now
            </button>
        </form>
    </div>

    {{-- Health Monitoring Dashboard --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon {{ $backups->firstWhere('status', 'success') ? 'bg-emerald-50' : 'bg-amber-50' }}">
                <svg class="w-6 h-6 {{ $backups->firstWhere('status', 'success') ? 'text-emerald-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Last Backup</p>
                @php $last = $backups->first(); @endphp
                @if($last)
                <p class="text-sm font-bold {{ $last->status === 'success' ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $last->status === 'success' ? 'Healthy' : 'Failed' }}
                </p>
                <p class="text-xs text-gray-400">{{ $last->completed_at?->diffForHumans() ?? 'N/A' }}</p>
                @else
                <p class="text-sm font-bold text-gray-400">No backups</p>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-blue-50">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Backups</p>
                <p class="text-2xl font-bold text-gray-900">{{ $backups->count() }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon {{ $backups->where('status', 'failed')->count() > 0 ? 'bg-red-50' : 'bg-gray-50' }}">
                <svg class="w-6 h-6 {{ $backups->where('status', 'failed')->count() > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Failed</p>
                <p class="text-2xl font-bold {{ $backups->where('status', 'failed')->count() > 0 ? 'text-red-600' : 'text-gray-400' }}">
                    {{ $backups->where('status', 'failed')->count() }}
                </p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-purple-50">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Storage</p>
                <p class="text-sm font-bold text-gray-900">
                    @php $totalSize = $backups->sum('size'); @endphp
                    {{ $totalSize > 0 ? round($totalSize / 1024 / 1024, 2) . ' MB' : 'N/A' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Schedule Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <strong>Automated Schedule:</strong><br>
                <span class="text-blue-700">Daily backup:</span> Every day at 02:00 AM<br>
                <span class="text-blue-700">Cleanup:</span> Every day at 03:00 AM<br>
                <span class="text-blue-700">Retention policy:</span> Keep all backups for 7 days, then 16 daily, 8 weekly, 4 monthly, 2 yearly<br>
                <span class="text-blue-700">Storage:</span> Local disk (encrypted) · Health check: Maximum age 1 day, max 5 GB
            </div>
        </div>
    </div>

    {{-- Backup History --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Backup History</h2>
            <span class="text-xs text-gray-400">{{ $backups->count() }} records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Disk</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Size</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Message</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Completed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($backups as $backup)
                    <tr class="{{ $backup->status === 'failed' ? 'bg-red-50/30' : ($backup->status === 'running' ? 'bg-yellow-50/30' : '') }}">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $backup->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center gap-1
                                {{ $backup->status === 'success' ? 'bg-green-100 text-green-800' :
                                   ($backup->status === 'failed' ? 'bg-red-100 text-red-800' :
                                   'bg-yellow-100 text-yellow-800') }}">
                                @if($backup->status === 'running')
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                                @endif
                                {{ ucfirst($backup->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $backup->disk }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $backup->formatted_size ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs max-w-[200px] truncate">{{ Str::limit($backup->message, 60) ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                            {{ $backup->completed_at?->format('M d, Y H:i') ?? ($backup->created_at?->format('M d, Y H:i') ?? '—') }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No backups recorded yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Retention Policy Visualization --}}
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Retention Policy</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-lg font-bold text-gray-900">7</p>
                <p class="text-xs text-gray-500">Daily Backups</p>
                <p class="text-[10px] text-gray-400">All backups kept for 7 days</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-lg font-bold text-gray-900">16</p>
                <p class="text-xs text-gray-500">Daily (after 7d)</p>
                <p class="text-[10px] text-gray-400">Most recent per day</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-lg font-bold text-gray-900">8</p>
                <p class="text-xs text-gray-500">Weekly</p>
                <p class="text-[10px] text-gray-400">Most recent per week</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-lg font-bold text-gray-900">4</p>
                <p class="text-xs text-gray-500">Monthly</p>
                <p class="text-[10px] text-gray-400">Most recent per month</p>
            </div>
        </div>
    </div>

</div>
@endsection
