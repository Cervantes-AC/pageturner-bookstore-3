@extends('layouts.app')
@section('title', 'Audit Logs - Admin - PageTurner')
@section('header')
    <h2 class="text-3xl font-bold text-gray-900">Audit Logs</h2>
    <p class="text-gray-600 mt-1">Compliance and change tracking</p>
@endsection

@section('content')
    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Event</label>
                <select name="event" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Events</option>
                    @foreach($events as $evt)
                        <option value="{{ $evt }}" {{ request('event') === $evt ? 'selected' : '' }}>{{ $evt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">User ID</label>
                <input type="number" name="user_id" value="{{ request('user_id') }}" placeholder="User ID"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Model Type</label>
                <input type="text" name="auditable_type" value="{{ request('auditable_type') }}" placeholder="e.g. Book"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="sm:col-span-2 lg:col-span-5 flex justify-between items-center">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    Filter
                </button>
                <a href="{{ route('admin.audit.export') }}?{{ http_build_query(request()->all()) }}"
                   class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                    Export CSV
                </a>
            </div>
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Event</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">User</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Model</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">ID</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">IP</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                            {{ $log->event }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $log->user->name ?? 'System' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ class_basename($log->auditable_type) }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $log->auditable_id }}</td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $log->ip_address }}</td>
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.audit.show', $log) }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-xs">
                            View Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No audit logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
@endsection
