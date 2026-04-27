@extends('layouts.app')
@section('title', 'Audit Log Detail')

@section('content')
<div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.audit.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Audit Logs</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-gray-900">Audit Log #{{ $audit->id }}</h1>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $audit->event === 'created' ? 'bg-green-100 text-green-800' :
                   ($audit->event === 'deleted' ? 'bg-red-100 text-red-800' :
                   'bg-blue-100 text-blue-800') }}">
                {{ ucfirst($audit->event) }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div><span class="font-medium text-gray-600">Model:</span> {{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}</div>
            <div><span class="font-medium text-gray-600">User:</span> {{ $audit->user?->name ?? 'System' }}</div>
            <div><span class="font-medium text-gray-600">IP Address:</span> <code class="bg-gray-100 px-1 rounded">{{ $audit->ip_address }}</code></div>
            <div><span class="font-medium text-gray-600">Date:</span> {{ $audit->created_at->format('M d, Y H:i:s') }}</div>
            <div class="col-span-2"><span class="font-medium text-gray-600">URL:</span> <code class="bg-gray-100 px-1 rounded text-xs">{{ $audit->url }}</code></div>
            <div><span class="font-medium text-gray-600">User Agent:</span> <span class="text-xs text-gray-500">{{ Str::limit($audit->user_agent, 80) }}</span></div>
        </div>

        @if($audit->old_values || $audit->new_values)
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Changes (Diff)</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h3 class="text-sm font-medium text-red-700 mb-2">Before</h3>
                <div class="bg-red-50 border border-red-200 rounded-md p-3 text-xs font-mono overflow-auto max-h-64">
                    @if($audit->old_values)
                        @foreach($audit->old_values as $key => $val)
                            <div class="py-0.5"><span class="text-red-600 font-semibold">{{ $key }}:</span> {{ is_array($val) ? json_encode($val) : $val }}</div>
                        @endforeach
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </div>
            </div>
            <div>
                <h3 class="text-sm font-medium text-green-700 mb-2">After</h3>
                <div class="bg-green-50 border border-green-200 rounded-md p-3 text-xs font-mono overflow-auto max-h-64">
                    @if($audit->new_values)
                        @foreach($audit->new_values as $key => $val)
                            <div class="py-0.5"><span class="text-green-600 font-semibold">{{ $key }}:</span> {{ is_array($val) ? json_encode($val) : $val }}</div>
                        @endforeach
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
