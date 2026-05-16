@extends('layouts.app')
@section('title', 'Generate AI Report - PageTurner')
@section('header')
    <h2 class="font-heading text-3xl font-bold text-ink-900">Generate AI Report</h2>
    <p class="text-ink-400 mt-1">Select a report type and filters to generate AI-powered insights</p>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('admin.ai-reports.store') }}" method="POST">
            @csrf
            <input type="hidden" name="report_type" id="report_type" value="{{ old('report_type') }}">

            {{-- Report Type Selection --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6 mb-6">
                <label class="text-sm font-semibold text-ink-700 mb-4 block">Report Type</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" id="reportTypeGrid">
                    @php
                        $types = [
                            'overview' => ['label' => 'Business Overview', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'desc' => 'Sales, users, inventory & orders at a glance', 'color' => 'indigo'],
                            'sales' => ['label' => 'Sales & Revenue', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'desc' => 'Revenue trends, order values & monthly performance', 'color' => 'emerald'],
                            'inventory' => ['label' => 'Inventory Status', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'desc' => 'Stock levels, low alerts & inventory value', 'color' => 'amber'],
                            'users' => ['label' => 'User Analytics', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z', 'desc' => 'User growth, roles & registration trends', 'color' => 'blue'],
                            'reviews' => ['label' => 'Review Analysis', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'desc' => 'Ratings, sentiment trends & distribution', 'color' => 'purple'],
                            'categories' => ['label' => 'Category Performance', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'desc' => 'Top categories & book distribution', 'color' => 'rose'],
                            'bestsellers' => ['label' => 'Bestsellers', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'desc' => 'Top-selling books by quantity sold', 'color' => 'orange'],
                            'alerts' => ['label' => 'Low Stock Alert', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z', 'desc' => 'Books needing immediate reorder', 'color' => 'red'],
                        ];
                    @endphp

                    @php
                        $colorMap = [
                            'indigo' => ['border' => 'border-indigo-500', 'bg' => 'bg-indigo-50', 'hoverBorder' => 'hover:border-indigo-300', 'hoverBg' => 'hover:bg-indigo-50', 'iconBg' => 'bg-indigo-100', 'iconColor' => 'text-indigo-600'],
                            'emerald' => ['border' => 'border-emerald-500', 'bg' => 'bg-emerald-50', 'hoverBorder' => 'hover:border-emerald-300', 'hoverBg' => 'hover:bg-emerald-50', 'iconBg' => 'bg-emerald-100', 'iconColor' => 'text-emerald-600'],
                            'amber' => ['border' => 'border-amber-500', 'bg' => 'bg-amber-50', 'hoverBorder' => 'hover:border-amber-300', 'hoverBg' => 'hover:bg-amber-50', 'iconBg' => 'bg-amber-100', 'iconColor' => 'text-amber-600'],
                            'blue' => ['border' => 'border-blue-500', 'bg' => 'bg-blue-50', 'hoverBorder' => 'hover:border-blue-300', 'hoverBg' => 'hover:bg-blue-50', 'iconBg' => 'bg-blue-100', 'iconColor' => 'text-blue-600'],
                            'purple' => ['border' => 'border-purple-500', 'bg' => 'bg-purple-50', 'hoverBorder' => 'hover:border-purple-300', 'hoverBg' => 'hover:bg-purple-50', 'iconBg' => 'bg-purple-100', 'iconColor' => 'text-purple-600'],
                            'rose' => ['border' => 'border-rose-500', 'bg' => 'bg-rose-50', 'hoverBorder' => 'hover:border-rose-300', 'hoverBg' => 'hover:bg-rose-50', 'iconBg' => 'bg-rose-100', 'iconColor' => 'text-rose-600'],
                            'orange' => ['border' => 'border-orange-500', 'bg' => 'bg-orange-50', 'hoverBorder' => 'hover:border-orange-300', 'hoverBg' => 'hover:bg-orange-50', 'iconBg' => 'bg-orange-100', 'iconColor' => 'text-orange-600'],
                            'red' => ['border' => 'border-red-500', 'bg' => 'bg-red-50', 'hoverBorder' => 'hover:border-red-300', 'hoverBg' => 'hover:bg-red-50', 'iconBg' => 'bg-red-100', 'iconColor' => 'text-red-600'],
                        ];
                    @endphp
                    @foreach($types as $key => $type)
                        @php $c = $colorMap[$type['color']]; @endphp
                        <button type="button" data-type="{{ $key }}"
                                data-color="{{ $type['color'] }}"
                                class="report-type-card text-left p-4 rounded-xl border-2 border-parchment-200 {{ $c['hoverBorder'] }} {{ $c['hoverBg'] }} transition-all {{ old('report_type') === $key ? $c['border'] . ' ' . $c['bg'] : '' }}">
                            <div class="w-9 h-9 rounded-lg {{ $c['iconBg'] }} flex items-center justify-center mb-2">
                                <svg class="w-5 h-5 {{ $c['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $type['icon'] }}" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-ink-800">{{ $type['label'] }}</p>
                            <p class="text-xs text-ink-400 mt-0.5">{{ $type['desc'] }}</p>
                        </button>
                    @endforeach
                </div>
                @error('report_type')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6 mb-6" id="filtersPanel">
                <h3 class="text-sm font-semibold text-ink-700 mb-4">Filters</h3>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1.5">Period</label>
                        <select name="period" class="w-full rounded-lg border-parchment-300 bg-parchment-50 text-ink-700 text-sm focus:border-gold-500 focus:ring-gold-500">
                            <option value="this_month" {{ old('period', 'this_month') === 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ old('period') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="last_3_months" {{ old('period') === 'last_3_months' ? 'selected' : '' }}>Last 3 Months</option>
                            <option value="this_year" {{ old('period') === 'this_year' ? 'selected' : '' }}>This Year</option>
                            <option value="all_time" {{ old('period') === 'all_time' ? 'selected' : '' }}>All Time</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1.5">Category</label>
                        <select name="category_id" class="w-full rounded-lg border-parchment-300 bg-parchment-50 text-ink-700 text-sm focus:border-gold-500 focus:ring-gold-500">
                            <option value="">All Categories</option>
                            @foreach(\App\Models\Category::orderBy('name')->get() as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1.5">Format</label>
                        <select name="format" class="w-full rounded-lg border-parchment-300 bg-parchment-50 text-ink-700 text-sm focus:border-gold-500 focus:ring-gold-500">
                            <option value="concise" {{ old('format', 'concise') === 'concise' ? 'selected' : '' }}>Concise</option>
                            <option value="detailed" {{ old('format') === 'detailed' ? 'selected' : '' }}>Detailed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1.5">AI Model</label>
                        <select name="model" class="w-full rounded-lg border-parchment-300 bg-parchment-50 text-ink-700 text-sm focus:border-gold-500 focus:ring-gold-500">
                            <option value="">Default (Groq {{ config('ai.providers.groq.model') }})</option>
                            <optgroup label="Groq">
                                @foreach(config('ai.available_models.groq') as $key => $label)
                                    <option value="groq:{{ $key }}" {{ old('model') === 'groq:'.$key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="OpenRouter">
                                @foreach(config('ai.available_models.openrouter') as $key => $label)
                                    <option value="openrouter:{{ $key }}" {{ old('model') === 'openrouter:'.$key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Generation Mode --}}
            <div class="flex items-center justify-between p-4 bg-parchment-50 rounded-lg mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-ink-700">Generation Mode</p>
                        <p class="text-xs text-ink-400 mt-0.5">Sync generates immediately. Async queues the report and you can check back later.</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="async" value="1" class="sr-only peer">
                    <div class="w-11 h-6 bg-parchment-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-gold-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-parchment-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gold-600"></div>
                    <span class="ml-3 text-sm font-medium text-ink-600">Async (Background)</span>
                </label>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.ai-reports.index') }}"
                   class="px-4 py-2.5 text-ink-600 hover:text-ink-800 font-medium rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-gold-600 hover:bg-gold-700 text-white font-semibold rounded-lg transition-colors">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Generate Report
                    </span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const colorMap = {
        overview: { border: 'border-indigo-500', bg: 'bg-indigo-50' },
        sales: { border: 'border-emerald-500', bg: 'bg-emerald-50' },
        inventory: { border: 'border-amber-500', bg: 'bg-amber-50' },
        users: { border: 'border-blue-500', bg: 'bg-blue-50' },
        reviews: { border: 'border-purple-500', bg: 'bg-purple-50' },
        categories: { border: 'border-rose-500', bg: 'bg-rose-50' },
        bestsellers: { border: 'border-orange-500', bg: 'bg-orange-50' },
        alerts: { border: 'border-red-500', bg: 'bg-red-50' },
    };
    document.querySelectorAll('.report-type-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.report-type-card').forEach(c => {
                const type = c.dataset.type;
                const colors = colorMap[type];
                if (colors) {
                    c.classList.remove(colors.border, colors.bg);
                }
                c.classList.add('border-parchment-200');
            });
            this.classList.remove('border-parchment-200');
            const colors = colorMap[this.dataset.type];
            if (colors) {
                this.classList.add(colors.border, colors.bg);
            }
            document.getElementById('report_type').value = this.dataset.type;
        });
    });

    @if(old('report_type'))
        document.querySelector(`.report-type-card[data-type="{{ old('report_type') }}"]`)?.click();
    @endif
</script>
@endpush
