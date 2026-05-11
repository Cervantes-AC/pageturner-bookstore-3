@extends('layouts.app')
@section('title', 'Export Data')

@section('content')
<div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Export Data</h1>

    {{-- flash handled by global toast --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Export Books -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📚 Export Books</h2>
            <form method="POST" action="{{ route('admin.export.books') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Format</label>
                        <select name="format" class="block w-full rounded border-gray-300 text-sm">
                            <option value="xlsx">XLSX</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Min Price</label>
                        <input type="number" name="min_price" step="0.01" placeholder="0.00"
                               class="block w-full rounded border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Max Price</label>
                        <input type="number" name="max_price" step="0.01" placeholder="9999.99"
                               class="block w-full rounded border-gray-300 text-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="in_stock" value="1" id="in_stock" class="rounded">
                        <label for="in_stock" class="text-xs text-gray-600">In stock only</label>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Columns</label>
                        <div class="space-y-1 text-xs">
                            @foreach(['isbn','title','author','price','stock','category','description','featured','created_at'] as $col)
                            <label class="flex items-center gap-1">
                                <input type="checkbox" name="columns[]" value="{{ $col }}" checked class="rounded">
                                {{ ucfirst($col) }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Export Books
                </button>
            </form>
        </div>

        <!-- Export Orders -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🛒 Export Orders</h2>
            <form method="POST" action="{{ route('admin.export.orders') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Format</label>
                        <select name="format" class="block w-full rounded border-gray-300 text-sm">
                            <option value="xlsx">XLSX</option>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select name="status" class="block w-full rounded border-gray-300 text-sm">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Date From</label>
                        <input type="date" name="date_from" class="block w-full rounded border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Date To</label>
                        <input type="date" name="date_to" class="block w-full rounded border-gray-300 text-sm">
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700">
                    Export Orders
                </button>
            </form>
        </div>

        <!-- Export Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">👥 Export Users</h2>
            <form method="POST" action="{{ route('admin.export.users') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Format</label>
                        <select name="format" class="block w-full rounded border-gray-300 text-sm">
                            <option value="xlsx">XLSX</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="redact_pii" value="1" id="redact_pii" class="rounded">
                        <label for="redact_pii" class="text-xs text-gray-600">Redact PII (GDPR)</label>
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                    Export Users
                </button>
            </form>
        </div>
    </div>

    <!-- Export Logs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Exports</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Type</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Format</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">By</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ ucfirst($log->type) }}</td>
                        <td class="px-4 py-3 uppercase text-gray-600">{{ $log->format }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $log->user?->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No exports yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
