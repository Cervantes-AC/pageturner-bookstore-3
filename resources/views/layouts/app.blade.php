<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PageTurner Bookstore')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-950">
<div class="min-h-screen">

    @include('partials.navigation')

    @hasSection('header')
    <header class="bg-slate-900 border-b border-slate-800 shadow-xl">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @yield('header')
        </div>
    </header>
    @endif

    @include('partials.flash-messages')

    <main class="py-8">
        @yield('content')
    </main>

    @include('partials.footer')
</div>
@stack('scripts')
</body>
</html>