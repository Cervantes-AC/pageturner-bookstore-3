import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const COLORS = {
    emerald: ['#059669', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5'],
    gold: ['#b8860b', '#d4a017', '#e6b800', '#f5d742', '#fcee8f'],
    blue: ['#2563eb', '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe'],
    purple: ['#7c3aed', '#a78bfa', '#c4b5fd', '#ddd6fe', '#ede9fe'],
    rose: ['#e11d48', '#fb7185', '#fda4af', '#fecdd3', '#ffe4e6'],
    amber: ['#d97706', '#f59e0b', '#fbbf24', '#fcd34d', '#fde68a'],
    teal: ['#0d9488', '#2dd4bf', '#5eead4', '#99f6e4', '#ccfbf1'],
    orange: ['#ea580c', '#f97316', '#fb923c', '#fdba74', '#fed7aa'],
};

function getCssVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

function formatMoney(n) {
    return '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

async function loadAnalytics() {
    try {
        const res = await fetch('/admin/analytics/data');
        const data = await res.json();
        renderSummaryCards(data.summary, data.inventory);
        renderRevenueChart(data.revenue);
        renderOrderStatusChart(data.orderStatus);
        renderCategoryChart(data.categoryBooks);
        renderUserGrowthChart(data.userGrowth);
        renderRatingChart(data.ratings);
        renderTopSellersChart(data.topSellers);
        renderDailyOrdersChart(data.dailyOrders);
    } catch (e) {
        document.getElementById('analytics-content').innerHTML =
            '<div class="text-center py-12 text-red-500"><p>Failed to load analytics data.</p></div>';
    }
}

function renderSummaryCards(summary, inventory) {
    const cards = [
        { label: 'Total Revenue', value: formatMoney(summary.total_revenue), color: 'emerald', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        { label: 'Total Orders', value: summary.total_orders, color: 'amber', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
        { label: 'Total Books', value: summary.total_books, color: 'blue', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
        { label: 'Total Users', value: summary.total_users, color: 'purple', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
        { label: 'Reviews', value: summary.total_reviews, color: 'rose', icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z' },
        { label: 'Avg Order', value: formatMoney(summary.avg_order), color: 'teal', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    ];

    const grid = document.getElementById('summary-cards');
    grid.innerHTML = cards.map(c => `
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-ink-400 uppercase tracking-wide">${c.label}</p>
                    <p class="font-heading text-2xl font-bold text-ink-900 mt-1">${c.value}</p>
                </div>
                <div class="w-11 h-11 bg-${c.color}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-${c.color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${c.icon}" />
                    </svg>
                </div>
            </div>
        </div>
    `).join('');
}

function renderRevenueChart(data) {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    const labels = data.map(r => r.month);
    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Revenue (₱)',
                    data: data.map(r => r.revenue),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: 'Orders',
                    data: data.map(r => r.orders),
                    borderColor: '#d97706',
                    backgroundColor: 'rgba(217, 119, 6, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top' },
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => '₱' + v.toLocaleString() } },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } },
            },
        },
    });
}

function renderOrderStatusChart(data) {
    const ctx = document.getElementById('orderStatusChart');
    if (!ctx) return;
    const labels = Object.keys(data);
    const values = Object.values(data);
    const colors = { pending: '#f59e0b', processing: '#3b82f6', completed: '#10b981', cancelled: '#ef4444' };
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: labels.map(l => colors[l] || '#9ca3af'),
                borderWidth: 2,
                borderColor: '#fff',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
            },
        },
    });
}

function renderCategoryChart(data) {
    const ctx = document.getElementById('categoryChart');
    if (!ctx) return;
    const labels = data.map(c => c.category);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Books',
                data: data.map(c => c.count),
                backgroundColor: ['#059669', '#d97706', '#2563eb', '#7c3aed', '#e11d48', '#0d9488', '#ea580c', '#b8860b'],
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: { beginAtZero: true, ticks: { precision: 0 } },
            },
        },
    });
}

function renderUserGrowthChart(data) {
    const ctx = document.getElementById('userGrowthChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(r => r.month),
            datasets: [{
                label: 'New Users',
                data: data.map(r => r.count),
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                fill: true,
                tension: 0.4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });
}

function renderRatingChart(data) {
    const ctx = document.getElementById('ratingChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(r => r.rating + ' Star' + (r.rating > 1 ? 's' : '')),
            datasets: [{
                label: 'Reviews',
                data: data.map(r => r.count),
                backgroundColor: ['#ef4444', '#f97316', '#f59e0b', '#22c55e', '#059669'],
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });
}

function renderTopSellersChart(data) {
    const ctx = document.getElementById('topSellersChart');
    if (!ctx) return;
    const labels = data.map(b => b.title.length > 25 ? b.title.substring(0, 25) + '...' : b.title);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Sold',
                data: data.map(b => b.sold),
                backgroundColor: '#d97706',
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });
}

function renderDailyOrdersChart(data) {
    const ctx = document.getElementById('dailyOrdersChart');
    if (!ctx) return;
    const labels = data.map(d => d.date);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Orders',
                    data: data.map(d => d.count),
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                    yAxisID: 'y',
                },
                {
                    label: 'Revenue (₱)',
                    data: data.map(d => d.revenue),
                    backgroundColor: '#059669',
                    borderRadius: 4,
                    yAxisID: 'y1',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, position: 'left' },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { callback: v => '₱' + v.toLocaleString() } },
            },
        },
    });
}

document.addEventListener('DOMContentLoaded', loadAnalytics);
