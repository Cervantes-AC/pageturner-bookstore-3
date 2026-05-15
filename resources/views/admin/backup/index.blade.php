@extends('layouts.app')
@section('title', 'Backup Management - Admin - PageTurner')
@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-heading text-3xl font-bold text-ink-900">Backup Management</h2>
            <p class="text-ink-400 mt-1">Database backup monitoring and control</p>
        </div>
        <form action="{{ route('admin.backup.run') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium inline-flex items-center shadow-sm"
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
    {{-- Database Connection Status --}}
    @if(isset($dbStatus))
        @if($dbStatus['connected'])
            <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-emerald-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-emerald-700 font-medium">Database connection is healthy</span>
                </div>
            </div>
        @else
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-red-700 font-medium">Database connection failed</p>
                        <p class="text-red-600 text-sm mt-1">{{ $dbStatus['error'] }}</p>
                        <p class="text-red-600 text-sm mt-2">Backups cannot run until the database connection is restored. Please check:</p>
                        <ul class="text-red-600 text-sm mt-2 ml-4 list-disc">
                            <li>MySQL server is running</li>
                            <li>Database credentials in .env file are correct</li>
                            <li>Network connectivity to database host</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Backup Status --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Last Backup</p>
            <p class="font-heading text-2xl font-bold text-ink-900 mt-1">
                {{ $lastBackup ? $lastBackup->created_at->diffForHumans() : 'Never' }}
            </p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Total Backups</p>
            <p class="font-heading text-2xl font-bold text-ink-900 mt-1">{{ $backups->count() }}</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Schedule</p>
            <p class="font-heading text-2xl font-bold text-ink-900 mt-1">Daily @ 02:00</p>
        </div>
    </div>

    {{-- Backup History --}}
    <div class="bg-white rounded-xl shadow-sm border border-parchment-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-parchment-200">
            <h3 class="font-heading text-lg font-semibold text-ink-900">Backup History</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-parchment-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">Name</th>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">Size</th>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">Disk</th>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">Date</th>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">Download</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-parchment-200">
                @forelse($backups as $backup)
                <tr class="hover:bg-parchment-50">
                    <td class="px-4 py-3 font-medium text-ink-900">{{ $backup->name }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $backup->status === 'success' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $backup->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $backup->status === 'running' ? 'bg-blue-100 text-blue-700' : '' }}">
                            {{ $backup->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-ink-400">
                        @if($backup->size_bytes)
                            {{ round($backup->size_bytes / 1024 / 1024, 2) }} MB
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="px-4 py-3 text-ink-400">{{ $backup->disk }}</td>
                    <td class="px-4 py-3 text-ink-400">{{ $backup->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3">
                        @if($backup->status === 'success' && $backup->file_path)
                            <a href="{{ route('admin.backup.download', $backup) }}" class="text-gold-600 hover:text-gold-700 font-medium">
                                Download
                            </a>
                        @else
                            <span class="text-ink-300">N/A</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-ink-400">No backup records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Schedule Info --}}
    <div class="mt-8 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-6">
        <h3 class="font-heading text-lg font-semibold text-purple-900 mb-2">Backup Schedule Configuration</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <span class="font-medium text-ink-700">Daily:</span>
                <span class="text-ink-400"> Database backup at 2:00 AM</span>
            </div>
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <span class="font-medium text-ink-700">Retention:</span>
                <span class="text-ink-400"> 7 daily, 4 weekly, 12 monthly</span>
            </div>
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <span class="font-medium text-ink-700">Cleanup:</span>
                <span class="text-ink-400"> Old backups removed at 3:00 AM</span>
            </div>
        </div>
        <p class="text-xs text-purple-600 mt-4">
            Server cron: <code class="bg-purple-100 px-2 py-0.5 rounded">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code>
        </p>
    </div>
@endsection
