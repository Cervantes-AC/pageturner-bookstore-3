<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="PageTurner Bookstore - Your destination for quality books at great prices.">
        <title>PageTurner Bookstore - Discover Your Next Great Read</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=playfair-display:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-ink-900 text-parchment-100 min-h-screen flex flex-col overflow-x-hidden">
        {{-- Ambient background --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-1/4 -left-32 w-96 h-96 bg-gold-500/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 -right-32 w-[30rem] h-[30rem] bg-gold-600/5 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[50rem] h-[50rem] bg-ink-800/50 rounded-full blur-3xl"></div>
        </div>

        {{-- Floating particles --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute w-2 h-2 bg-gold-500/20 rounded-full animate-float" style="top:15%;left:10%;animation-delay:0s;animation-duration:7s;"></div>
            <div class="absolute w-1.5 h-1.5 bg-gold-400/20 rounded-full animate-float" style="top:25%;right:15%;animation-delay:1.5s;animation-duration:9s;"></div>
            <div class="absolute w-3 h-3 bg-gold-500/10 rounded-full animate-float" style="bottom:30%;left:8%;animation-delay:3s;animation-duration:8s;"></div>
            <div class="absolute w-1 h-1 bg-gold-300/20 rounded-full animate-float" style="top:60%;right:10%;animation-delay:4.5s;animation-duration:6s;"></div>
            <div class="absolute w-2.5 h-2.5 bg-gold-400/15 rounded-full animate-float" style="bottom:15%;right:30%;animation-delay:2s;animation-duration:10s;"></div>
            <div class="absolute w-1.5 h-1.5 bg-gold-500/25 rounded-full animate-float" style="top:40%;left:5%;animation-delay:5s;animation-duration:7.5s;"></div>
        </div>

        {{-- Navigation --}}
        <header class="relative z-10 w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                        <div class="w-8 h-8 bg-gradient-warm rounded-lg flex items-center justify-center shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="font-heading text-xl font-bold text-white">PageTurner</span>
                    </a>
                    @if (Route::has('login'))
                        <nav class="flex items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-parchment-300 hover:text-white transition-colors">Dashboard</a>
                                <a href="{{ url('/dashboard') }}" class="px-5 py-2 text-sm font-medium bg-gradient-warm text-white rounded-lg hover:opacity-90 transition-all shadow-lg">Get Started</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-parchment-300 hover:text-white transition-colors">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-medium bg-gradient-warm text-white rounded-lg hover:opacity-90 transition-all shadow-lg shadow-gold-500/20">Register</a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </div>
        </header>

        {{-- Hero --}}
        <main class="relative z-10 flex-1 flex items-center justify-center px-6 py-20">
            <div class="max-w-4xl text-center">
                <div class="mb-10 animate-fade-in">
                    <div class="w-24 h-24 bg-gradient-warm rounded-3xl flex items-center justify-center mx-auto shadow-2xl shadow-gold-500/20 group hover:scale-105 transition-transform duration-300">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>

                <h1 class="font-heading text-5xl md:text-7xl font-bold text-white mb-6 leading-tight animate-fade-in-up">
                    Welcome to
                    <span class="gradient-text-gold">PageTurner</span>
                </h1>

                <p class="text-lg md:text-xl text-parchment-400 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up stagger-1">
                    Your destination for quality books at great prices. Discover, read, and grow with our curated collection of timeless classics and modern bestsellers.
                </p>

                <div class="flex flex-wrap justify-center gap-4 animate-fade-in-up stagger-2">
                    <a href="{{ route('home') }}" class="group px-8 py-4 bg-gradient-to-r from-gold-600 to-amber-600 text-white font-semibold rounded-xl hover:from-gold-700 hover:to-amber-700 transition-all shadow-xl shadow-gold-500/20 hover:shadow-2xl hover:-translate-y-0.5">
                        Enter Bookstore
                        <svg class="w-5 h-5 inline ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="{{ route('books.index') }}" class="px-8 py-4 bg-white/10 backdrop-blur-sm text-parchment-100 font-semibold rounded-xl border border-parchment-400/30 hover:bg-white/20 transition-all">
                        Browse Books
                    </a>
                </div>

                {{-- Feature highlights --}}
                <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-6 text-left animate-fade-in-up stagger-3">
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all group">
                        <div class="w-10 h-10 bg-gold-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-gold-500/30 transition-colors">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h3 class="font-heading font-semibold text-white mb-2">Curated Collection</h3>
                        <p class="text-sm text-parchment-400 leading-relaxed">Handpicked books across every genre, from bestsellers to hidden gems waiting to be discovered.</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all group">
                        <div class="w-10 h-10 bg-gold-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-gold-500/30 transition-colors">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-heading font-semibold text-white mb-2">Fast Delivery</h3>
                        <p class="text-sm text-parchment-400 leading-relaxed">Get your books delivered right to your doorstep with our reliable and speedy shipping service.</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all group">
                        <div class="w-10 h-10 bg-gold-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-gold-500/30 transition-colors">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </div>
                        <h3 class="font-heading font-semibold text-white mb-2">Reader Reviews</h3>
                        <p class="text-sm text-parchment-400 leading-relaxed">Real reviews from real readers to help you find your next favorite book with confidence.</p>
                    </div>
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="relative z-10 border-t border-white/10 py-8 text-center text-sm text-parchment-500">
            <div class="max-w-7xl mx-auto px-4">
                <p>&copy; {{ date('Y') }} PageTurner Bookstore. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>
