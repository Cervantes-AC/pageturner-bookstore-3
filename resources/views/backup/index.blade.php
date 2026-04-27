@extends('layouts.app')
@section('title', 'Backup Management')

@section('content')
<div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Backup Management</h1>
        <form method="POST" action="{{ route('admin.backup.run') }}">
            @csrf
            <button type="submit"
                    onclick="return confirm('Run a manual backup now?')"
                    class="px-5 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700">
                ▶ Run Backup Now
            </button>
        </form>
    </div>

    {{-- flash handled by global toast --}}

    <!-- Schedule Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-sm text-blue-800">
        <strong>Automated Schedule:</strong>
        Daily backup at 02:00 AM · Cleanup at 03:00 AM · Retention: 7 daily / 4 weekly / 12 monthly
    </div>

    <!-- Backup History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Backup History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Disk</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Size</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Message</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Completed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($backups as $backup)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $backup->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $backup->status === 'success' ? 'bg-green-100 text-green-800' :
                                   ($backup->status === 'failed' ? 'bg-red-100 text-red-800' :
                                   'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($backup->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $backup->disk }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $backup->formatted_size }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ Str::limit($backup->message, 60) }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $backup->completed_at?->format('M d, Y H:i') ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No backups recorded yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
