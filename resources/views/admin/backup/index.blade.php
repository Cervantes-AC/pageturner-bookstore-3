@extends('layouts.app')
@section('title', 'Backup Management - Admin - PageTurner')
@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Backup Management</h2>
            <p class="text-gray-600 mt-1">Automated backup monitoring and control</p>
        </div>
        <form action="{{ route('admin.backup.run') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium inline-flex items-center"
                    onclick="return confirm('Run a manual backup now?')">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Run Backup Now
            </button>
        </form>
    </div>
@endsection

@section('content')
    {{-- Backup Status --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Last Backup</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">
                {{ $lastBackup ? $lastBackup->created_at->diffForHumans() : 'Never' }}
            </p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Backups</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $backups->count() }}</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Schedule</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">Daily @ 02:00</p>
        </div>
    </div>

    {{-- Backup History --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Backup History</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Size</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Disk</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($backups as $backup)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $backup->name }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $backup->status === 'success' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $backup->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $backup->status === 'running' ? 'bg-blue-100 text-blue-700' : '' }}">
                            {{ $backup->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        @if($backup->size_bytes)
                            {{ round($backup->size_bytes / 1024 / 1024, 2) }} MB
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $backup->disk }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $backup->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No backup records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Schedule Info --}}
    <div class="mt-8 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-2">Backup Schedule Configuration</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div class="bg-white rounded-lg p-3">
                <span class="font-medium text-gray-700">Daily:</span>
                <span class="text-gray-600"> Full backup at 2:00 AM</span>
            </div>
            <div class="bg-white rounded-lg p-3">
                <span class="font-medium text-gray-700">Retention:</span>
                <span class="text-gray-600"> 7 daily, 4 weekly, 12 monthly</span>
            </div>
            <div class="bg-white rounded-lg p-3">
                <span class="font-medium text-gray-700">Cleanup:</span>
                <span class="text-gray-600"> Old backups removed at 3:00 AM</span>
            </div>
        </div>
        <p class="text-xs text-purple-600 mt-4">
            Server cron: <code class="bg-purple-100 px-2 py-0.5 rounded">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code>
        </p>
    </div>
@endsection
