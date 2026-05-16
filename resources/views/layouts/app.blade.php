<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PageTurner Bookstore - Your destination for quality books at great prices. Discover, read, and grow with our curated collection.">
    <meta property="og:title" content="@yield('title', 'PageTurner Bookstore')">
    <meta property="og:description" content="Browse thousands of books across all genres. From bestsellers to hidden gems, find your perfect story.">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#151524">
    <title>@yield('title', 'PageTurner Bookstore')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=playfair-display:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <style>[x-cloak] { display: none !important; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-parchment-100">
{{-- Page loading overlay --}}
<div x-data="{ loading: false }"
     x-on:beforeunload.window="loading = true"
     x-show="loading"
     class="fixed inset-0 z-[200] bg-ink-900/60 backdrop-blur-sm flex items-center justify-center transition-opacity duration-300"
     x-cloak>
    <div class="bg-white rounded-2xl p-8 shadow-2xl flex flex-col items-center">
        <div class="w-14 h-14 bg-gradient-warm rounded-xl flex items-center justify-center animate-heartbeat mb-4">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <p class="font-heading text-lg font-semibold text-ink-900">Loading...</p>
        <div class="mt-3 w-32 h-1.5 bg-parchment-200 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-warm rounded-full animate-shimmer bg-size-200" style="background-size: 200% 100%;"></div>
        </div>
    </div>
</div>

<div x-data="app()" class="min-h-screen flex flex-col" :class="{ 'animate-page-load': pageLoaded }">

    @include('partials.navigation')

    @auth
        @if(auth()->user()->isAdmin())
            @include('partials.admin-drawer')
        @endif
    @endauth

    @hasSection('header')
    <header :class="isDesktop && adminDrawerOpen ? 'ml-72' : ''" class="bg-white border-b border-parchment-200 transition-all duration-300 sticky top-16 z-20">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            @yield('header')
        </div>
    </header>
    @endif

    @include('partials.flash-messages')

    {{-- Toast notifications container --}}
    <div x-data="toastManager()" x-init="init()" class="toast-container" x-cloak>
        <template x-for="(toast, index) in toasts" :key="index">
            <div class="toast"
                 :class="'toast-' + toast.type"
                 x-transition:enter="transition-all duration-300 ease-out"
                 x-transition:enter-start="opacity-0 translate-x-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                 x-transition:leave="transition-all duration-200 ease-in"
                 x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-x-8 scale-95">
                <div class="flex-shrink-0">
                    <svg x-show="toast.type === 'success'" class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="toast.type === 'error'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="toast.type === 'warning'" class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <svg x-show="toast.type === 'info'" class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 text-sm font-medium" x-text="toast.message"></div>
                <button @click="removeToast(index)" class="flex-shrink-0 ml-2 opacity-60 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <main :class="isDesktop && adminDrawerOpen ? 'ml-72' : ''" class="flex-1 py-10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    @include('partials.footer')

    {{-- Scroll to top with progress ring --}}
    <div x-data="{ visible: false, scrollProgress: 0 }"
         x-init="window.addEventListener('scroll', () => {
             visible = window.scrollY > 500;
             const scrollable = document.documentElement.scrollHeight - window.innerHeight;
             scrollProgress = scrollable > 0 ? Math.min((window.scrollY / scrollable) * 100, 100) : 0;
         })"
         x-show="visible"
         class="fixed bottom-8 right-8 z-50"
         style="display: none;">
        <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="relative w-14 h-14 bg-white hover:bg-gold-50 rounded-full shadow-lg hover:shadow-xl border border-parchment-200 transition-all hover:-translate-y-1 group flex items-center justify-center">
            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="16" fill="none" class="stroke-parchment-200" stroke-width="1.5"/>
                <circle cx="18" cy="18" r="16" fill="none"
                        class="stroke-gold-500 transition-all duration-200"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        :stroke-dasharray="100.48"
                        :stroke-dashoffset="100.48 - (scrollProgress / 100) * 100.48"/>
            </svg>
            <svg class="w-5 h-5 text-ink-600 group-hover:text-gold-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
        </button>
    </div>

    {{-- Keyboard shortcut hint --}}
    <div x-data="{ show: false }"
         x-init="window.addEventListener('keydown', (e) => {
             if (e.key === '?' && !e.ctrlKey && !e.metaKey) {
                 show = !show;
             }
         })"
         x-show="show"
         @click.away="show = false"
         class="fixed bottom-8 left-8 z-50 bg-white rounded-2xl shadow-2xl border border-parchment-200 p-6 max-w-xs"
         x-cloak>
        <h3 class="font-heading font-semibold text-ink-900 mb-3">Keyboard Shortcuts</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-ink-400">Search</span><kbd class="px-2 py-0.5 bg-parchment-100 rounded text-xs font-mono">Ctrl + K</kbd></div>
            <div class="flex justify-between"><span class="text-ink-400">Home</span><kbd class="px-2 py-0.5 bg-parchment-100 rounded text-xs font-mono">G + H</kbd></div>
            <div class="flex justify-between"><span class="text-ink-400">Books</span><kbd class="px-2 py-0.5 bg-parchment-100 rounded text-xs font-mono">G + B</kbd></div>
            <div class="flex justify-between"><span class="text-ink-400">Cart</span><kbd class="px-2 py-0.5 bg-parchment-100 rounded text-xs font-mono">G + C</kbd></div>
            <div class="flex justify-between"><span class="text-ink-400">Help</span><kbd class="px-2 py-0.5 bg-parchment-100 rounded text-xs font-mono">?</kbd></div>
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
