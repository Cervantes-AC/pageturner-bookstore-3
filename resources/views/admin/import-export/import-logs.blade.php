@extends('layouts.app')
@section('title', 'Import Logs - Admin - PageTurner')
@section('header')
    <h2 class="font-heading text-3xl font-bold text-ink-900">Import Logs</h2>
    <p class="text-ink-400 mt-1">Track all import operations</p>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-parchment-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-parchment-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">File</th>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">Type</th>
                    <th class="text-center px-4 py-3 font-medium text-ink-400">Total</th>
                    <th class="text-center px-4 py-3 font-medium text-ink-400">Failed</th>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">User</th>
                    <th class="text-left px-4 py-3 font-medium text-ink-400">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-parchment-200">
                @forelse($imports as $import)
                <tr class="hover:bg-parchment-50">
                    <td class="px-4 py-3 font-medium text-ink-900">{{ $import->filename }}</td>
                    <td class="px-4 py-3 capitalize">{{ $import->type }}</td>
                    <td class="px-4 py-3 text-center">{{ $import->total_rows }}</td>
                    <td class="px-4 py-3 text-center">{{ $import->failed_rows }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $import->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $import->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $import->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $import->status === 'completed_with_errors' ? 'bg-orange-100 text-orange-700' : '' }}">
                            {{ $import->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-ink-400">{{ $import->user->name ?? 'System' }}</td>
                    <td class="px-4 py-3 text-ink-400">{{ $import->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-ink-400">No imports yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $imports->links() }}
    </div>
@endsection
