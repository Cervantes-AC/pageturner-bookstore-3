@extends('layouts.app')
@section('title', 'Analytics - PageTurner')
@section('header')
    <h2 class="font-heading text-3xl font-bold text-ink-900">Analytics</h2>
    <p class="text-ink-400 mt-1">Visual insights into your bookstore performance</p>
@endsection

@section('content')
    <div id="analytics-content">
        {{-- Summary Cards --}}
        <div id="summary-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8"></div>

        {{-- Charts Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Revenue Trend --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Monthly Revenue Trend</h3>
                <div class="relative" style="height: 280px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- Order Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Order Status Breakdown</h3>
                <div class="relative" style="height: 280px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Books per Category --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Books per Category</h3>
                <div class="relative" style="height: 320px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            {{-- User Growth --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">User Growth</h3>
                <div class="relative" style="height: 320px;">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Rating Distribution --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Rating Distribution</h3>
                <div class="relative" style="height: 280px;">
                    <canvas id="ratingChart"></canvas>
                </div>
            </div>

            {{-- Top Sellers --}}
            <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Top Selling Books</h3>
                <div class="relative" style="height: 320px;">
                    <canvas id="topSellersChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Daily Orders --}}
        <div class="bg-white rounded-xl shadow-sm border border-parchment-200 p-6 mb-6">
            <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Daily Orders (Last 30 Days)</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="dailyOrdersChart"></canvas>
            </div>
        </div>
    </div>

    <div class="text-center py-12 text-ink-400" id="analytics-loading">
        <svg class="w-8 h-8 mx-auto mb-3 animate-spin text-gold-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <p>Loading analytics data...</p>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/analytics.js'])
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('analytics-loading')?.remove();
    });
</script>
@endpush
