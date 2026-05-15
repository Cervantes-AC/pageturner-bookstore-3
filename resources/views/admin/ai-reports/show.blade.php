@extends('layouts.app')
@section('title', $report->title . ' - PageTurner')
@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-heading text-3xl font-bold text-ink-900">{{ $report->title }}</h2>
            <p class="text-ink-400 mt-1">Generated {{ $report->completed_at?->diffForHumans() ?? 'pending' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($report->status === 'completed')
                <a href="{{ route('admin.ai-reports.print', $report) }}" target="_blank"
                   class="inline-flex items-center px-3 py-2 bg-ink-100 hover:bg-ink-200 text-ink-700 font-medium rounded-lg text-sm transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print
                </a>
                <a href="{{ route('admin.ai-reports.word', $report) }}"
                   class="inline-flex items-center px-3 py-2 bg-ink-100 hover:bg-ink-200 text-ink-700 font-medium rounded-lg text-sm transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Word
                </a>
                <form action="{{ route('admin.ai-reports.regenerate', $report) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-ink-100 hover:bg-ink-200 text-ink-700 font-medium rounded-lg text-sm transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Regenerate
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.ai-reports.index') }}"
               class="inline-flex items-center px-3 py-2 bg-parchment-100 hover:bg-parchment-200 text-ink-600 font-medium rounded-lg text-sm transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Status Banner --}}
        @if($report->status === 'generating' || $report->status === 'pending')
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
                <div class="flex items-center space-x-3">
                    <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-blue-700">Report is being generated...</p>
                        <p class="text-sm text-blue-600 mt-0.5">This page will automatically refresh in 5 seconds.</p>
                    </div>
                </div>
            </div>
            <meta http-equiv="refresh" content="5">
        @endif

        @if($report->status === 'failed')
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-medium text-red-700">Report Generation Failed</p>
                        <p class="text-sm text-red-600 mt-0.5">{{ $report->error_message }}</p>
                    </div>
                </div>
                <form action="{{ route('admin.ai-reports.regenerate', $report) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Try Again
                    </button>
                </form>
            </div>
        @endif

        @if($report->status === 'completed')
            @php
                $introduction = $report->data['_introduction'] ?? null;
                $conclusion = $report->data['_conclusion'] ?? null;
            @endphp

            {{-- Report Header --}}
            <div class="bg-gradient-to-r from-ink-800 to-ink-700 rounded-xl p-8 mb-6 text-white">
                <p class="text-gold-400 text-sm font-semibold uppercase tracking-wider mb-2">PageTurner Bookstore &mdash; AI-Generated Report</p>
                <h1 class="font-heading text-2xl font-bold mb-2">{{ $report->title }}</h1>
                <div class="flex items-center space-x-4 text-sm text-parchment-300">
                    <span>Generated: {{ $report->completed_at?->format('F j, Y') }}</span>
                    <span>&middot;</span>
                    <span>Provider: {{ ucfirst($report->provider_used ?? 'N/A') }}</span>
                    <span>&middot;</span>
                    <span>Query: "{{ Str::limit($report->query, 60) }}"</span>
                </div>
            </div>

            {{-- 1.0 Executive Summary --}}
            @if($report->summary)
                <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6 mb-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-gold-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-gold-700 font-bold text-sm">1</span>
                        </div>
                        <h3 class="font-heading text-lg font-semibold text-ink-900">Executive Summary</h3>
                    </div>
                    <div class="text-ink-600 leading-relaxed whitespace-pre-line text-sm">
                        {{ $report->summary }}
                    </div>
                </div>
            @endif

            {{-- 2.0 Introduction --}}
            @if($introduction)
                <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6 mb-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-700 font-bold text-sm">2</span>
                        </div>
                        <h3 class="font-heading text-lg font-semibold text-ink-900">Introduction</h3>
                    </div>
                    <div class="text-ink-600 leading-relaxed whitespace-pre-line text-sm">
                        {{ $introduction }}
                    </div>
                </div>
            @endif

            {{-- 3.0 Findings --}}
            @if(!empty($report->insights))
                <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6 mb-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-purple-700 font-bold text-sm">3</span>
                        </div>
                        <h3 class="font-heading text-lg font-semibold text-ink-900">Key Findings</h3>
                    </div>
                    <div class="space-y-4">
                        @foreach($report->insights as $finding)
                            @php $section = $finding['section'] ?? $finding['finding'] ?? ''; @endphp
                            <div class="border-l-4 rounded-r-lg p-4
                                {{ ($finding['status'] ?? '') === 'critical' ? 'border-l-red-500 bg-red-50' : '' }}
                                {{ ($finding['status'] ?? '') === 'warning' ? 'border-l-amber-500 bg-amber-50' : '' }}
                                {{ ($finding['status'] ?? '') === 'positive' ? 'border-l-emerald-500 bg-emerald-50' : '' }}
                                {{ !isset($finding['status']) || $finding['status'] === 'info' ? 'border-l-blue-500 bg-blue-50' : '' }}">
                                @if($section)
                                    <h4 class="font-semibold text-ink-800 text-sm mb-1">{{ $section }}</h4>
                                @endif
                                <p class="text-sm text-ink-600">{{ $finding['content'] ?? $finding['finding'] ?? '' }}</p>
                                @if(isset($finding['status']))
                                    <span class="inline-block mt-2 text-xs font-medium px-2 py-0.5 rounded
                                        {{ $finding['status'] === 'critical' ? 'bg-red-200 text-red-800' : '' }}
                                        {{ $finding['status'] === 'warning' ? 'bg-amber-200 text-amber-800' : '' }}
                                        {{ $finding['status'] === 'positive' ? 'bg-emerald-200 text-emerald-800' : '' }}
                                        {{ !in_array($finding['status'] ?? '', ['critical','warning','positive']) ? 'bg-blue-200 text-blue-800' : '' }}">
                                        {{ ucfirst($finding['status']) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 4.0 Data Reference --}}
            @if($report->data)
                <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6 mb-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-teal-700 font-bold text-sm">4</span>
                        </div>
                        <h3 class="font-heading text-lg font-semibold text-ink-900">Data Reference</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($report->data as $key => $value)
                            @if(is_array($value) && isset($value['total_revenue']))
                                <div class="p-4 bg-parchment-50 rounded-lg border border-parchment-200">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-2">Sales Performance</p>
                                    <div class="space-y-1">
                                        <p class="text-sm text-ink-700">Revenue: <span class="font-semibold">₱{{ number_format($value['total_revenue'] ?? 0, 2) }}</span></p>
                                        <p class="text-sm text-ink-700">Orders: <span class="font-semibold">{{ $value['total_orders'] ?? 0 }}</span></p>
                                        <p class="text-sm text-ink-700">Avg Order: <span class="font-semibold">₱{{ number_format($value['average_order_value'] ?? 0, 2) }}</span></p>
                                    </div>
                                </div>
                            @elseif(is_array($value) && isset($value['total_books']))
                                <div class="p-4 bg-parchment-50 rounded-lg border border-parchment-200">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-2">Inventory</p>
                                    <div class="space-y-1">
                                        <p class="text-sm text-ink-700">Total Books: <span class="font-semibold">{{ number_format($value['total_books'] ?? 0) }}</span></p>
                                        <p class="text-sm text-ink-700">Active: <span class="font-semibold">{{ number_format($value['active_books'] ?? 0) }}</span></p>
                                        <p class="text-sm text-ink-700">Low Stock: <span class="font-semibold {{ ($value['low_stock_count'] ?? 0) > 0 ? 'text-red-600' : '' }}">{{ $value['low_stock_count'] ?? 0 }}</span></p>
                                    </div>
                                </div>
                            @elseif(is_array($value) && isset($value['total_users']))
                                <div class="p-4 bg-parchment-50 rounded-lg border border-parchment-200">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-2">Users</p>
                                    <div class="space-y-1">
                                        <p class="text-sm text-ink-700">Total: <span class="font-semibold">{{ $value['total_users'] ?? 0 }}</span></p>
                                        <p class="text-sm text-ink-700">New (Month): <span class="font-semibold">{{ $value['new_this_month'] ?? 0 }}</span></p>
                                        <p class="text-sm text-ink-700">Verified: <span class="font-semibold">{{ $value['verified'] ?? 0 }}</span></p>
                                    </div>
                                </div>
                            @elseif(is_array($value) && isset($value['total_reviews']))
                                <div class="p-4 bg-parchment-50 rounded-lg border border-parchment-200">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-2">Reviews</p>
                                    <div class="space-y-1">
                                        <p class="text-sm text-ink-700">Total: <span class="font-semibold">{{ $value['total_reviews'] ?? 0 }}</span></p>
                                        <p class="text-sm text-ink-700">Avg Rating: <span class="font-semibold">{{ number_format($value['average_rating'] ?? 0, 1) }}</span> / 5</p>
                                    </div>
                                </div>
                            @elseif(is_array($value) && isset($value['monthly_revenue']))
                                <div class="p-4 bg-parchment-50 rounded-lg border border-parchment-200">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-2">Trends</p>
                                    <p class="text-sm text-ink-700">{{ count($value['monthly_revenue']) }} months tracked</p>
                                </div>
                            @elseif(is_array($value) && isset($value['status_breakdown']))
                                <div class="p-4 bg-parchment-50 rounded-lg border border-parchment-200">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-2">Orders</p>
                                    <p class="text-sm text-ink-700">Shipped: <span class="font-semibold">{{ $value['total_shipped'] ?? 0 }}</span></p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 5.0 Recommendations --}}
            @if(!empty($report->recommendations))
                <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6 mb-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-amber-700 font-bold text-sm">5</span>
                        </div>
                        <h3 class="font-heading text-lg font-semibold text-ink-900">Recommendations</h3>
                    </div>
                    <div class="space-y-4">
                        @foreach($report->recommendations as $rec)
                            <div class="border rounded-lg p-4 border-parchment-200">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white
                                        {{ ($rec['priority'] ?? '') === 'high' ? 'bg-red-500' : '' }}
                                        {{ ($rec['priority'] ?? '') === 'medium' ? 'bg-amber-500' : '' }}
                                        {{ ($rec['priority'] ?? '') === 'low' ? 'bg-blue-500' : '' }}
                                        {{ !in_array($rec['priority'] ?? '', ['high','medium','low']) ? 'bg-ink-400' : '' }}">
                                        {{ strtoupper(substr($rec['priority'] ?? 'm', 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-ink-800">{{ $rec['action'] ?? $rec['recommendation'] ?? '' }}</p>
                                        @if(!empty($rec['rationale']))
                                            <p class="text-xs text-ink-500 mt-1">{{ $rec['rationale'] }}</p>
                                        @endif
                                        @if(isset($rec['priority']))
                                            <span class="inline-block mt-2 text-xs font-medium px-2 py-0.5 rounded
                                                {{ $rec['priority'] === 'high' ? 'bg-red-100 text-red-700' : '' }}
                                                {{ $rec['priority'] === 'medium' ? 'bg-amber-100 text-amber-700' : '' }}
                                                {{ $rec['priority'] === 'low' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ !in_array($rec['priority'] ?? '', ['high','medium','low']) ? 'bg-parchment-200 text-ink-600' : '' }}">
                                                {{ ucfirst($rec['priority']) }} Priority
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 6.0 Conclusion --}}
            @if($conclusion)
                <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6 mb-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-indigo-700 font-bold text-sm">6</span>
                        </div>
                        <h3 class="font-heading text-lg font-semibold text-ink-900">Conclusion</h3>
                    </div>
                    <div class="text-ink-600 leading-relaxed whitespace-pre-line text-sm">
                        {{ $conclusion }}
                    </div>
                </div>
            @endif

            {{-- Metadata --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h4 class="font-heading text-base font-semibold text-ink-900 mb-3">Report Metadata</h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-ink-400">Status</p>
                        <p class="font-medium text-ink-700">{{ ucfirst($report->status) }}</p>
                    </div>
                    <div>
                        <p class="text-ink-400">AI Provider</p>
                        <p class="font-medium text-ink-700">{{ ucfirst($report->provider_used ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-ink-400">AI Model</p>
                        <p class="font-medium text-ink-700">{{ $report->model_used ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-ink-400">Tokens Used</p>
                        <p class="font-medium text-ink-700">{{ $report->tokens_used ? number_format($report->tokens_used) : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-ink-400">Generated By</p>
                        <p class="font-medium text-ink-700">{{ $report->user->name ?? 'System' }}</p>
                    </div>
                </div>
                @if($report->provider_used === 'ollama')
                    <div class="mt-4 p-3 bg-amber-50 rounded-lg text-sm text-amber-700">
                        This report was generated using the local Ollama fallback provider.
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<style>
    @media print {
        body { background: white !important; }
        nav, footer, .admin-drawer, .no-print { display: none !important; }
        main { margin-left: 0 !important; }
        .max-w-7xl { max-width: 100% !important; }
        .bg-gradient-to-r { background: #2d3748 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .bg-gold-100, .bg-blue-100, .bg-purple-100, .bg-teal-100, .bg-amber-100, .bg-indigo-100 { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .border-l-red-500, .border-l-amber-500, .border-l-emerald-500, .border-l-blue-500 { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .bg-red-50, .bg-amber-50, .bg-emerald-50, .bg-blue-50, .bg-parchment-50 { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .shadow-sm { box-shadow: none !important; }
        .rounded-xl { border-radius: 4px !important; }
        a { text-decoration: none !important; }
    }
</style>
@endpush
