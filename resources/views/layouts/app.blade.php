<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PageTurner') - Professional Bookstore</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="h-full font-sans antialiased bg-gradient-to-br from-gray-50 via-white to-primary-50/20">
<div class="min-h-full flex flex-col">

    @include('partials.navigation')

    @hasSection('header')
    <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @yield('header')
        </div>
    </header>
    @endif

    @include('partials.toast-notifications')

    <main class="flex-grow py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    @include('partials.footer')
</div>
@stack('scripts')
</body>
</html>
