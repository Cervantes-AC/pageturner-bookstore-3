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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=playfair-display:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <style>[x-cloak] { display: none !important; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-parchment-100">
<div x-data="adminSidebar()" class="min-h-screen flex flex-col">

    @include('partials.navigation')

    @auth
        @if(auth()->user()->isAdmin())
            @include('partials.admin-drawer')
        @endif
    @endauth

    @hasSection('header')
    <header :class="isDesktop && adminDrawerOpen ? 'ml-72' : ''" class="bg-white border-b border-parchment-200 transition-all duration-300">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            @yield('header')
        </div>
    </header>
    @endif

    @include('partials.flash-messages')

    <main :class="isDesktop && adminDrawerOpen ? 'ml-72' : ''" class="flex-1 py-10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    @include('partials.footer')

    <button x-data="{ visible: false }"
            x-init="window.addEventListener('scroll', () => { visible = window.scrollY > 500 })"
            x-show="visible"
            x-transition
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-8 right-8 z-50 bg-gold-600 hover:bg-gold-700 text-white p-3.5 rounded-full shadow-lg hover:shadow-xl transition-all"
            style="display: none;">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</div>
</body>
</html>
