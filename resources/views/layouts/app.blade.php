<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PageTurner') — Professional Bookstore</title>
    <meta name="description" content="@yield('meta_description', 'PageTurner - Your professional bookstore platform. Browse thousands of books across every genre.')">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans antialiased bg-gray-50 text-gray-900"
      x-data="{ sidebarOpen: false, mobileSearch: false }"
      :class="{ 'overflow-hidden': sidebarOpen }">

{{-- Flash messages --}}
@include('partials.toast-notifications')

<div class="flex h-full min-h-screen">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main area --}}
    <div class="flex flex-col flex-1 min-w-0 lg:pl-64 transition-all duration-300">

        {{-- Top bar --}}
        @include('partials.topbar')

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto animate-fade-in">
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('partials.footer')
    </div>
</div>

{{-- Mobile sidebar backdrop --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-20 bg-gray-900/60 backdrop-blur-sm lg:hidden"
     style="display:none;"></div>

@stack('scripts')
</body>
</html>
