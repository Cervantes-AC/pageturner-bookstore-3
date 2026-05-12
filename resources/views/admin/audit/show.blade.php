@extends('layouts.app')
@section('title', 'Audit Log Detail - Admin - PageTurner')
@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-heading text-3xl font-bold text-ink-900">Audit Log Detail</h2>
            <p class="text-ink-400 mt-1">Event: {{ $auditLog->event }}</p>
        </div>
        <a href="{{ route('admin.audit.index') }}" class="px-4 py-2 bg-parchment-100 text-ink-700 rounded-lg hover:bg-parchment-200 transition-colors text-sm font-medium">
            Back to Logs
        </a>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Event Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Event Information</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-ink-400">Event</dt>
                    <dd class="text-sm font-medium text-ink-900">{{ $auditLog->event }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-ink-400">User</dt>
                    <dd class="text-sm font-medium text-ink-900">{{ $auditLog->user->name ?? 'System' }} (ID: {{ $auditLog->user_id ?? 'N/A' }})</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-ink-400">Model</dt>
                    <dd class="text-sm font-medium text-ink-900">{{ $auditLog->auditable_type }} #{{ $auditLog->auditable_id }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-ink-400">Timestamp</dt>
                    <dd class="text-sm font-medium text-ink-900">{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-ink-400">Checksum</dt>
                    <dd class="text-sm font-mono text-ink-400 break-all max-w-xs">{{ $auditLog->checksum ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Request Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Request Information</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-ink-400">Method</dt>
                    <dd class="text-sm font-medium">
                        <span class="px-2 py-1 rounded text-xs font-bold
                            {{ $auditLog->method === 'POST' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $auditLog->method === 'PUT' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $auditLog->method === 'DELETE' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ $auditLog->method }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-ink-400">URL</dt>
                    <dd class="text-sm text-ink-400 break-all max-w-sm">{{ $auditLog->url ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-ink-400">IP Address</dt>
                    <dd class="text-sm font-mono text-ink-900">{{ $auditLog->ip_address ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-ink-400">User Agent</dt>
                    <dd class="text-sm text-ink-400 break-all max-w-sm">{{ $auditLog->user_agent ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Diff View --}}
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        @if($auditLog->old_values)
        <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Old Values</h3>
            <div class="bg-red-50 rounded-lg p-4">
                <pre class="text-sm text-red-800 overflow-x-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif
        @if($auditLog->new_values)
        <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">New Values</h3>
            <div class="bg-emerald-50 rounded-lg p-4">
                <pre class="text-sm text-emerald-800 overflow-x-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif
    </div>
@endsection
