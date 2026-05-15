@extends('layouts.app')
@section('title', 'AI Usage Analytics - PageTurner')
@section('header')
    <h2 class="font-heading text-3xl font-bold text-ink-900">AI Usage Analytics</h2>
    <p class="text-ink-400 mt-1">Monitor AI provider usage, costs, and performance</p>
@endsection

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Total Tokens Used</p>
            <p class="font-heading text-2xl font-bold text-ink-900">{{ number_format($totalTokens) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Total Estimated Cost</p>
            <p class="font-heading text-2xl font-bold text-ink-900">${{ number_format($totalCost, 6) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Tokens Today</p>
            <p class="font-heading text-2xl font-bold text-ink-900">{{ number_format($todayTokens) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Success Rate</p>
            <p class="font-heading text-2xl font-bold {{ $successRate >= 90 ? 'text-emerald-600' : ($successRate >= 70 ? 'text-amber-600' : 'text-red-600') }}">
                {{ $successRate }}%
            </p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-parchment-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-parchment-200">
            <h3 class="font-heading text-lg font-semibold text-ink-900">Recent API Calls</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-parchment-50 text-left">
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Provider</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Feature</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Model</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Tokens</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Cost</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Response Time</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Status</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-parchment-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-parchment-50 transition-colors text-sm">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $log->provider === 'groq' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $log->provider === 'openai' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $log->provider === 'gemini' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $log->provider === 'ollama' ? 'bg-amber-100 text-amber-700' : '' }}">
                                    {{ ucfirst($log->provider) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-ink-400">{{ $log->feature }}</td>
                            <td class="px-6 py-4 text-ink-400">{{ $log->model_used ?? '—' }}</td>
                            <td class="px-6 py-4 text-ink-700">{{ number_format($log->tokens_used) }}</td>
                            <td class="px-6 py-4 text-ink-400">${{ number_format($log->cost_estimate, 8) }}</td>
                            <td class="px-6 py-4 text-ink-400">{{ $log->response_time_ms ? round($log->response_time_ms) . 'ms' : '—' }}</td>
                            <td class="px-6 py-4">
                                @if($log->success)
                                    <span class="text-emerald-600 font-medium">Success</span>
                                @else
                                    <span class="text-red-600 font-medium" title="{{ $log->error_message }}">Failed</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-ink-400">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-ink-400">
                                No AI usage data recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-parchment-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <div class="mt-6 p-4 bg-parchment-50 rounded-xl border border-parchment-200">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-ink-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-medium text-ink-700">Free Tier Usage Notice</p>
                <p class="text-xs text-ink-400 mt-0.5">
                    Groq API: 500K tokens/day | OpenAI: 200K tokens/day | Gemini: 1,500 requests/day | Ollama: Unlimited (local).
                    Usage resets daily. The system automatically falls back to Ollama when cloud limits are reached.
                </p>
            </div>
        </div>
    </div>
@endsection
