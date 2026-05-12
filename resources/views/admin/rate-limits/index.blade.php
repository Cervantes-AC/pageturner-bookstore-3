@extends('layouts.app')
@section('title', 'Rate Limits - Admin - PageTurner')
@section('header')
    <h2 class="text-3xl font-bold text-gray-900">API Rate Limiting</h2>
    <p class="text-gray-600 mt-1">Monitor and configure API rate limits</p>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Today's API Hits</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_hits_today'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Currently Throttled</p>
            <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['throttled_requests'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Rate Limit Tiers</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ count($stats['limits']) }}</p>
        </div>
    </div>

    {{-- Tier Limits Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Rate Limit Tiers</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Tier</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Users</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Requests/Minute</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Scope</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($stats['limits'] as $tier => $config)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $tier === 'auth' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $tier === 'public' ? 'bg-gray-100 text-gray-700' : '' }}
                            {{ $tier === 'standard' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $tier === 'premium' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $tier === 'admin' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                            {{ ucfirst($tier) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $config['users'] }}</td>
                    <td class="px-4 py-3 text-center font-bold text-gray-900">{{ $config['limit'] }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $config['description'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Top Endpoints --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Top Endpoints Today</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Endpoint</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Total Hits</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stats['top_endpoints'] as $endpoint)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-sm text-gray-700">{{ $endpoint->endpoint }}</td>
                    <td class="px-4 py-3 text-center font-medium text-gray-900">{{ $endpoint->total_hits }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-4 py-8 text-center text-gray-500">No API hit data yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{── 429 Response Example --}}
    <div class="mt-8 bg-gradient-to-r from-rose-50 to-red-50 border border-red-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-red-800 mb-2">Rate Limit Exceeded Response</h3>
        <pre class="bg-white rounded-lg p-4 text-sm text-gray-800 overflow-x-auto">
HTTP/1.1 429 Too Many Requests
Content-Type: application/json
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
Retry-After: 42

{
    "message": "Too many requests.",
    "retry_after": 42,
    "limit": 60
}
        </pre>
    </div>
@endsection
