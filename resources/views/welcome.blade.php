<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'PageTurner Bookstore') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=playfair-display:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            h1, h2, h3 {
                font-family: 'Playfair Display', serif;
            }
        </style>
    </head>
    <body class="bg-[#faf6f0] text-[#1e1e2d] min-h-screen flex flex-col">
        <header class="w-full border-b border-[#e8dac4] bg-white/80 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-[#b8860b] to-[#d48f1f] rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="font-serif text-xl font-bold bg-gradient-to-r from-[#b8860b] to-[#d48f1f] bg-clip-text text-transparent">PageTurner</span>
                    </a>
                    @if (Route::has('login'))
                        <nav class="flex items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-[#4a4a5a] hover:text-[#1e1e2d] transition-colors">
                                    Dashboard
                                </a>
                                <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-medium bg-gradient-to-br from-[#b8860b] to-[#d48f1f] text-white rounded-lg hover:shadow-md transition-all">
                                    Get Started
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-[#4a4a5a] hover:text-[#1e1e2d] transition-colors">
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium bg-gradient-to-br from-[#b8860b] to-[#d48f1f] text-white rounded-lg hover:shadow-md transition-all">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center p-6">
            <div class="max-w-3xl text-center">
                <div class="mb-8">
                    <div class="w-20 h-20 bg-gradient-to-br from-[#b8860b] to-[#d48f1f] rounded-2xl flex items-center justify-center mx-auto shadow-xl shadow-amber-200/50">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-5xl md:text-6xl font-serif font-bold text-[#1e1e2d] mb-4 leading-tight">
                    Welcome to <span class="bg-gradient-to-r from-[#b8860b] to-[#d48f1f] bg-clip-text text-transparent">PageTurner</span>
                </h1>
                <p class="text-lg text-[#6b6b80] mb-8 max-w-xl mx-auto leading-relaxed">
                    Your destination for quality books at great prices. Discover, read, and grow with our curated collection.
                </p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('home') }}" class="px-8 py-4 bg-gradient-to-br from-[#b8860b] to-[#d48f1f] text-white font-semibold rounded-xl hover:shadow-lg transition-all shadow-md">
                        Enter Bookstore
                    </a>
                    <a href="{{ route('books.index') }}" class="px-8 py-4 bg-white text-[#1e1e2d] font-semibold rounded-xl border border-[#e8dac4] hover:border-[#b8860b] hover:shadow-md transition-all">
                        Browse Books
                    </a>
                </div>
            </div>
        </main>

        <footer class="border-t border-[#e8dac4] py-6 text-center text-sm text-[#8a8a9e]">
            <p>&copy; {{ date('Y') }} PageTurner Bookstore. All rights reserved.</p>
        </footer>
    </body>
</html>
