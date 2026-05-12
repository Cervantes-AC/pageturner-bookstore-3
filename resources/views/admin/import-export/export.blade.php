@extends('layouts.app')
@section('title', 'Export Data - Admin - PageTurner')
@section('header')
    <h2 class="text-3xl font-bold text-gray-900">Export Data</h2>
    <p class="text-gray-600 mt-1">Export books, orders, or users</p>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{── Book Export --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Export Books</h3>
            <form action="{{ route('admin.import-export.export.books') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                        <input type="number" step="0.01" name="min_price" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                        <input type="number" step="0.01" name="max_price" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                    <select name="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">All</option>
                        <option value="in_stock">In Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                    <select name="format" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="xlsx">Excel (XLSX)</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                    Export Books
                </button>
            </form>
        </div>

        {{── Order Export --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Export Orders</h3>
            <form action="{{ route('admin.import-export.export.orders') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                    <select name="format" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="xlsx">Excel (XLSX)</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                    Export Orders
                </button>
            </form>
        </div>

        {{── User Export --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Export Users</h3>
            <form action="{{ route('admin.import-export.export.users') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                    <select name="format" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="xlsx">Excel (XLSX)</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <label class="flex items-center space-x-2 text-sm text-gray-700">
                    <input type="checkbox" name="redact_pii" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Redact PII (GDPR compliance)</span>
                </label>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-700">
                    <strong>GDPR Note:</strong> Enabling PII redaction replaces names and emails with [REDACTED] in the export.
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                    Export Users
                </button>
            </form>
        </div>
    </div>
@endsection
