@extends('layouts.app')
@section('title', 'AI Reports - PageTurner')
@section('header')
    <h2 class="font-heading text-3xl font-bold text-ink-900">AI Reports</h2>
    <p class="text-ink-400 mt-1">Generate intelligent business reports using natural language</p>
@endsection

@section('content')
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Total Reports</p>
            <p class="font-heading text-2xl font-bold text-ink-900">{{ $totalReports }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Completed</p>
            <p class="font-heading text-2xl font-bold text-emerald-600">{{ $completedReports }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Tokens Today</p>
            <p class="font-heading text-2xl font-bold text-ink-900">{{ number_format($usageToday) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200">
            <p class="text-sm font-medium text-ink-400">Tokens This Week</p>
            <p class="font-heading text-2xl font-bold text-ink-900">{{ number_format($usageThisWeek) }}</p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.ai-reports.create') }}"
           class="inline-flex items-center px-4 py-2.5 bg-gold-600 hover:bg-gold-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Generate New Report
        </a>
        <a href="{{ route('admin.ai-reports.usage') }}"
           class="inline-flex items-center px-4 py-2.5 bg-ink-100 hover:bg-ink-200 text-ink-700 font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Usage Analytics
        </a>
    </div>

    {{-- Reports Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-parchment-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-parchment-50 text-left">
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Title</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Query</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Status</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Provider</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Tokens</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Created</th>
                        <th class="px-6 py-4 text-sm font-semibold text-ink-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-parchment-200">
                    @forelse($reports as $report)
                        <tr class="hover:bg-parchment-50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.ai-reports.show', $report) }}" class="font-medium text-gold-600 hover:text-gold-700">
                                    {{ Str::limit($report->title, 40) }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-400 max-w-xs truncate">
                                {{ Str::limit($report->query, 50) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $report->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $report->status === 'generating' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $report->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $report->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                                    @if($report->status === 'generating')
                                        <svg class="animate-spin -ml-0.5 mr-1.5 h-3 w-3 text-blue-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                    @endif
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-400">
                                {{ $report->provider_used ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-400">
                                {{ $report->tokens_used ? number_format($report->tokens_used) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-400">
                                {{ $report->created_at->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.ai-reports.show', $report) }}"
                                       class="p-1.5 text-ink-400 hover:text-gold-600 rounded-lg hover:bg-parchment-100 transition-colors"
                                       title="View Report">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.ai-reports.destroy', $report) }}" method="POST"
                                          onsubmit="return confirm('Delete this report?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-ink-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                                title="Delete Report">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-ink-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-ink-400 font-medium">No reports generated yet</p>
                                <p class="text-ink-300 text-sm mt-1">Generate your first report to see AI-powered insights about your bookstore.</p>
                                <a href="{{ route('admin.ai-reports.create') }}"
                                   class="inline-flex items-center mt-4 px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Generate Report
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-parchment-200">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
@endsection
