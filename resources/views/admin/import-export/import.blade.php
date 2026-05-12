@extends('layouts.app')
@section('title', 'Import Data - Admin - PageTurner')
@section('header')
    <h2 class="text-3xl font-bold text-gray-900">Import Data</h2>
    <p class="text-gray-600 mt-1">Bulk import books or users</p>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Book Import --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Import Books</h3>
            <p class="text-sm text-gray-600 mb-4">Upload an Excel/CSV file with book data. Download the template first for the required format.</p>

            <div class="mb-4">
                <a href="{{ route('admin.import-export.template') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Template
                </a>
            </div>

            <form action="{{ route('admin.import-export.import.books') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Excel/CSV File</label>
                    <input type="file" name="file" accept=".xlsx,.csv" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-center space-x-2 text-sm text-gray-700">
                    <input type="checkbox" name="update_existing" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Update existing books (match by ISBN)</span>
                </label>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                    Import Books
                </button>
            </form>
        </div>

        {{-- User Import --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Import Users</h3>
            <p class="text-sm text-gray-600 mb-4">Bulk create user accounts. File must have columns: name, email, password, role.</p>

            <form action="{{ route('admin.import-export.import.users') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Excel/CSV File</label>
                    <input type="file" name="file" accept=".xlsx,.csv" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-700">
                    <strong>Required columns:</strong> name, email, password, role (admin/customer)
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                    Import Users
                </button>
            </form>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Import Logs</h3>
        @php $imports = \App\Models\ImportLog::with('user')->latest()->take(10)->get(); @endphp
        @if($imports->count())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">File</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Type</th>
                            <th class="text-center px-4 py-3 font-medium text-gray-600">Rows</th>
                            <th class="text-center px-4 py-3 font-medium text-gray-600">Failed</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">By</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($imports as $import)
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
                            <td class="px-4 py-3 text-gray-500">{{ $import->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No imports yet.</p>
        @endif
    </div>
@endsection
