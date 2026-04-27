@extends('layouts.app')
@section('title', 'Audit Logs')

@section('content')
<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
        <form method="POST" action="{{ route('admin.audit.export') }}" class="flex items-center gap-2">
            @csrf
            <select name="format" class="rounded border-gray-300 text-sm">
                <option value="xlsx">XLSX</option>
                <option value="csv">CSV</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded-md hover:bg-gray-800">
                Export
            </button>
        </form>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <select name="user_id" class="rounded border-gray-300 text-sm">
                <option value="">All Users</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
            <select name="event" class="rounded border-gray-300 text-sm">
                <option value="">All Events</option>
                <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Created</option>
                <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Updated</option>
                <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Deleted</option>
            </select>
            <input type="text" name="auditable_type" value="{{ request('auditable_type') }}"
                   placeholder="Model (e.g. Book)" class="rounded border-gray-300 text-sm">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded border-gray-300 text-sm">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded border-gray-300 text-sm">
        </div>
        <div class="mt-3 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">Filter</button>
            <a href="{{ route('admin.audit.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200">Clear</a>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Event</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Model</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">User</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Changes</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">IP</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Date</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $log->event === 'created' ? 'bg-green-100 text-green-800' :
                               ($log->event === 'deleted' ? 'bg-red-100 text-red-800' :
                               'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($log->event) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        @if($log->new_values)
                            {{ count($log->new_values) }} field(s) changed
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $log->ip_address }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.audit.show', $log) }}" class="text-blue-600 hover:text-blue-800 text-xs">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No audit logs found</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
