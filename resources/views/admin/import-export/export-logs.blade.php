@extends('layouts.app')
@section('title', 'Export Logs - Admin - PageTurner')
@section('header')
    <h2 class="text-3xl font-bold text-gray-900">Export Logs</h2>
    <p class="text-gray-600 mt-1">Track all export operations</p>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Type</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Format</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">User</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Filters</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Download</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($exports as $export)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 capitalize font-medium">{{ $export->type }}</td>
                    <td class="px-4 py-3 uppercase">{{ $export->format }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $export->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $export->status === 'processing' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $export->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ $export->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $export->user->name ?? 'System' }}</td>
                    <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ json_encode($export->filters) }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $export->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3">
                        @if($export->file_path)
                            <a href="{{ route('admin.import-export.exports.download', $export) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                Download
                            </a>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No exports yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $exports->links() }}
    </div>
@endsection
