@extends('layouts.app')
@section('title', 'Import Logs - Admin - PageTurner')
@section('header')
    <h2 class="text-3xl font-bold text-gray-900">Import Logs</h2>
    <p class="text-gray-600 mt-1">Track all import operations</p>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">File</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Type</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Total</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Failed</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">User</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($imports as $import)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $import->filename }}</td>
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
                    <td class="px-4 py-3 text-gray-600">{{ $import->user->name ?? 'System' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $import->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No imports yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $imports->links() }}
    </div>
@endsection
