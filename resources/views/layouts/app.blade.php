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
    <title>@yield('title', 'PageTurner Bookstore')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
<div x-data="{ mobileMenuOpen: false }" class="min-h-screen flex flex-col">

    @include('partials.navigation')

    @hasSection('header')
    <header class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            @yield('header')
        </div>
    </header>
    @endif

    @include('partials.flash-messages')

    <main class="flex-1 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    @include('partials.footer')

    {{-- Back to Top Button --}}
    <button x-data="{ visible: false }"
            x-init="window.addEventListener('scroll', () => { visible = window.scrollY > 500 })"
            x-show="visible"
            x-transition
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-8 right-8 z-50 bg-emerald-500 hover:bg-emerald-600 text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all"
            style="display: none;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</div>
</body>
</html>
