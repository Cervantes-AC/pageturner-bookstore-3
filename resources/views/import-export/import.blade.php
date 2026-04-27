@extends('layouts.app')
@section('title', 'Import Books')

@section('content')
<div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Book Import</h1>
        <a href="{{ route('admin.import.template') }}"
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
            ⬇ Download Template
        </a>
    </div>

    @if(session('success'))
        {{-- handled by toast --}}
    @endif
    @if(session('error'))
        {{-- handled by toast --}}
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload File</h2>
        <form method="POST" action="{{ route('admin.import.books') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File (XLSX or CSV)</label>
                    <input type="file" name="file" accept=".xlsx,.csv" required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duplicate Handling</label>
                    <select name="mode" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="skip">Skip duplicates</option>
                        <option value="update">Update existing</option>
                    </select>
                </div>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4 text-sm text-yellow-800">
                <strong>Required columns:</strong> ISBN, Title, Author, Price, Stock, Category, Description
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700">
                Import Books
            </button>
        </form>
    </div>

    <!-- Import Logs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Imports</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">File</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">By</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Total</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Processed</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Failed</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $log->filename }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $log->user?->name }}</td>
                        <td class="px-4 py-3">{{ $log->total_rows }}</td>
                        <td class="px-4 py-3 text-green-600">{{ $log->processed_rows }}</td>
                        <td class="px-4 py-3 text-red-600">{{ $log->failed_rows }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $log->status === 'completed' ? 'bg-green-100 text-green-800' :
                                   ($log->status === 'failed' ? 'bg-red-100 text-red-800' :
                                   'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @if($log->errors && count($log->errors))
                    <tr class="bg-red-50">
                        <td colspan="7" class="px-4 py-2 text-xs text-red-700">
                            <strong>Errors:</strong>
                            @foreach(array_slice($log->errors, 0, 3) as $err)
                                Row {{ $err['row'] ?? '?' }}: {{ implode(', ', $err['errors'] ?? []) }} |
                            @endforeach
                            @if(count($log->errors) > 3) ... and {{ count($log->errors) - 3 }} more @endif
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No imports yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
