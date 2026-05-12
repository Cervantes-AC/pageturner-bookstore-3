@php
$currentRoute = request()->route() ? request()->route()->getName() : '';
@endphp

<div x-show="adminDrawerOpen && !isDesktop"
     @click="closeSidebar"
     x-transition:enter="transition-opacity duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-50"
     x-transition:leave="transition-opacity duration-200"
     x-transition:leave-start="opacity-50"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black z-40"
     x-cloak></div>

<div x-cloak
     :class="[
         adminDrawerOpen ? 'translate-x-0' : '-translate-x-full',
         isDesktop
             ? 'fixed top-16 left-0 h-[calc(100%-4rem)] w-72 bg-ink-800 shadow-2xl overflow-y-auto z-30 rounded-r-xl'
             : 'fixed top-0 left-0 h-full w-72 bg-ink-800 shadow-2xl overflow-y-auto z-50'
     ]"
     class="transition-all duration-300 ease-in-out"
     @keydown.escape.window="closeSidebar">
    <div class="flex items-center justify-between px-4 h-16 border-b border-ink-700">
        <div class="flex items-center space-x-2">
            <div class="w-7 h-7 bg-gradient-warm rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <span class="font-heading text-lg font-bold text-parchment-100">Admin Panel</span>
        </div>
        <button @click="closeSidebar" class="text-parchment-400 hover:text-white p-1 rounded-lg hover:bg-ink-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="p-4 space-y-1">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ $currentRoute === 'admin.dashboard' ? 'text-gold-400 bg-ink-700/50' : 'text-parchment-300 hover:text-white hover:bg-ink-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Dashboard</span>
        </a>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-ink-400">Catalog</p>
        </div>
        <a href="{{ route('admin.books.create') }}"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ $currentRoute === 'admin.books.create' ? 'text-gold-400 bg-ink-700/50' : 'text-parchment-300 hover:text-white hover:bg-ink-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Book</span>
        </a>
        <a href="{{ route('admin.categories.create') }}"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ $currentRoute === 'admin.categories.create' ? 'text-gold-400 bg-ink-700/50' : 'text-parchment-300 hover:text-white hover:bg-ink-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <span>Add Category</span>
        </a>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-ink-400">Data</p>
        </div>
        <a href="{{ route('admin.import-export.import') }}"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ $currentRoute === 'admin.import-export.import' ? 'text-gold-400 bg-ink-700/50' : 'text-parchment-300 hover:text-white hover:bg-ink-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span>Import</span>
        </a>
        <a href="{{ route('admin.import-export.export') }}"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ $currentRoute === 'admin.import-export.export' ? 'text-gold-400 bg-ink-700/50' : 'text-parchment-300 hover:text-white hover:bg-ink-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>Export</span>
        </a>
        <a href="{{ route('admin.import-export.exports') }}"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ $currentRoute === 'admin.import-export.exports' ? 'text-gold-400 bg-ink-700/50' : 'text-parchment-300 hover:text-white hover:bg-ink-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Export Logs</span>
        </a>
        <a href="{{ route('admin.import-export.imports') }}"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ $currentRoute === 'admin.import-export.imports' ? 'text-gold-400 bg-ink-700/50' : 'text-parchment-300 hover:text-white hover:bg-ink-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Import Logs</span>
        </a>

        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-ink-400">System</p>
        </div>
        <a href="{{ route('admin.audit.index') }}"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ $currentRoute === 'admin.audit.index' ? 'text-gold-400 bg-ink-700/50' : 'text-parchment-300 hover:text-white hover:bg-ink-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span>Audit Log</span>
        </a>
        <a href="{{ route('admin.backup.index') }}"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ $currentRoute === 'admin.backup.index' ? 'text-gold-400 bg-ink-700/50' : 'text-parchment-300 hover:text-white hover:bg-ink-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Backup</span>
        </a>
        <a href="{{ route('admin.rate-limits.index') }}"
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ $currentRoute === 'admin.rate-limits.index' ? 'text-gold-400 bg-ink-700/50' : 'text-parchment-300 hover:text-white hover:bg-ink-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span>Rate Limits</span>
        </a>
    </nav>
</div>
