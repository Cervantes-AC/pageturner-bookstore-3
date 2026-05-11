<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PageTurner') }} — Professional Bookstore Platform</title>
    <meta name="description" content="PageTurner is a professional-grade bookstore management platform. Browse, manage inventory, track orders, and grow your business.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            *,:after,:before{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-scroll-snap-strictness:proximity;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgba(59,130,246,.5);--tw-ring-offset-shadow:0 0 transparent;--tw-ring-shadow:0 0 transparent;--tw-shadow:0 0 transparent;--tw-shadow-colored:0 0 transparent}::backdrop{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-scroll-snap-strictness:proximity;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgba(59,130,246,.5);--tw-ring-offset-shadow:0 0 transparent;--tw-ring-shadow:0 0 transparent;--tw-shadow:0 0 transparent;--tw-shadow-colored:0 0 transparent}
        </style>
    @endif
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 overflow-x-hidden">
    <div class="min-h-screen flex flex-col"
         x-data="{ mobileNavOpen: false }">
        {{-- Nav --}}
        <nav class="relative z-30 bg-white/80 backdrop-blur-xl border-b border-gray-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-3">
                        <button @click="mobileNavOpen = true"
                                class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <a href="/" class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-lg shadow-primary-500/20">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-gray-900 tracking-tight">PageTurner</span>
                        </a>
                    </div>

                    {{-- Desktop nav links --}}
                    <div class="hidden lg:flex items-center gap-6">
                        <a href="#features" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Features</a>
                        <a href="{{ route('books.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Browse Books</a>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary btn-sm">
                                    Dashboard
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn-ghost btn-sm">Sign in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-primary btn-sm shadow-sm">Get Started</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        {{-- Mobile nav drawer --}}
        <div x-show="mobileNavOpen"
             class="fixed inset-0 z-40 lg:hidden"
             style="display:none;">
            <div x-show="mobileNavOpen"
                 x-transition:enter="transition-opacity ease-linear duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileNavOpen = false"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            <div x-show="mobileNavOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="fixed inset-y-0 left-0 w-72 max-w-[80vw] bg-white shadow-xl z-50 flex flex-col">
                <div class="flex items-center justify-between px-4 h-16 border-b border-gray-200">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <span class="font-bold text-gray-900">PageTurner</span>
                    </div>
                    <button @click="mobileNavOpen = false"
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    <a href="/" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-900 bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Home
                    </a>
                    <a href="#features" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Features
                    </a>
                    <a href="{{ route('books.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Browse Books
                    </a>
                    <div class="border-t border-gray-200 my-3"></div>
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</p>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Sign In
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors mt-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
                <div class="px-3 py-4 border-t border-gray-200">
                    <p class="text-xs text-gray-400 text-center">&copy; {{ date('Y') }} PageTurner</p>
                </div>
            </div>
        </div>

        {{-- Hero --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950">
            <div class="absolute inset-0 opacity-[0.05]"
                 style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>
            <div class="absolute top-20 -left-20 w-72 h-72 bg-primary-400/20 rounded-full blur-3xl animate-float"></div>
            <div class="absolute -bottom-10 right-20 w-96 h-96 bg-accent-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
                <div class="max-w-3xl mx-auto text-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-primary-200 text-xs font-semibold mb-6 border border-white/10 backdrop-blur-sm">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse-soft"></span>
                        Enterprise-Grade Bookstore Platform
                    </span>
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-white leading-tight mb-6 tracking-tight">
                        Discover your next<br>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary-300 to-accent-300">great read</span>
                    </h1>
                    <p class="text-xl text-primary-200 max-w-2xl mx-auto mb-10 leading-relaxed">
                        Browse thousands of titles across every genre. Manage your inventory, track orders, and grow your business — all in one place.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary-700 font-semibold rounded-xl hover:bg-primary-50 hover:-translate-y-0.5 transition-all duration-300 shadow-xl shadow-primary-900/30 text-lg">
                            Browse Books
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-4 border-2 border-white/20 text-white font-semibold rounded-xl hover:bg-white/10 hover:border-white/30 transition-all duration-300 text-lg">
                                Create Account
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section id="features" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 reveal-up">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Everything you need to succeed</h2>
                    <p class="text-lg text-gray-500 max-w-2xl mx-auto">Built for modern bookstores, from independent shops to enterprise distributors.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach([
                        ['title' => 'Smart Inventory', 'desc' => 'Track stock levels, manage categories, and get low-stock alerts with real-time updates.', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['title' => 'Order Management', 'desc' => 'Process orders from checkout to delivery. Real-time status updates for you and your customers.', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ['title' => 'Analytics & Reports', 'desc' => 'Understand your business with detailed sales reports, popular titles, and customer insights.', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ['title' => 'Bulk Import/Export', 'desc' => 'Import and export your catalog via Excel spreadsheets. Save time with batch operations.', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'],
                        ['title' => 'Audit Trail', 'desc' => 'Full audit logging for compliance. Every change is tracked with who did it and when.', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['title' => 'Secure & Scalable', 'desc' => 'Built on Laravel 12 with 2FA, role-based access, and automated backups for peace of mind.', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                    ] as $feature)
                    <div class="group card-hover p-8 reveal-up">
                        <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary-100 group-hover:scale-110 transition-all duration-300">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="py-20 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 relative overflow-hidden">
            <div class="absolute inset-0 opacity-[0.05]"
                 style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>
            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl font-bold text-white mb-4 reveal-up">Ready to get started?</h2>
                <p class="text-xl text-primary-200 mb-10 reveal-up">Join PageTurner and transform your bookstore management today.</p>
                <div class="flex flex-wrap justify-center gap-4 reveal-up">
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition-all duration-300 shadow-xl text-lg">
                            Create Free Account
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @endguest
                    <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 px-8 py-4 border-2 border-white/20 text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300 text-lg">
                        Browse Books
                    </a>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-200 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 bg-gradient-to-br from-primary-500 to-primary-600 rounded flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-400">© {{ date('Y') }} PageTurner. All rights reserved.</span>
                    </div>
                    <div class="flex items-center gap-6">
                        <a href="#" class="text-xs text-gray-400 hover:text-primary-600 transition-colors">Privacy Policy</a>
                        <a href="#" class="text-xs text-gray-400 hover:text-primary-600 transition-colors">Terms of Service</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
