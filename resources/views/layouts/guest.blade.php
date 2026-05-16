<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'PageTurner Bookstore') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=playfair-display:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink-900 antialiased min-h-screen bg-gradient-to-br from-parchment-100 via-white to-parchment-200">
        {{-- Decorative background elements --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gold-200/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gold-300/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/4 left-1/4 w-4 h-4 bg-gold-400/20 rounded-full animate-float" style="animation-delay: 0s;"></div>
            <div class="absolute top-1/3 right-1/3 w-3 h-3 bg-gold-400/20 rounded-full animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-1/4 right-1/4 w-5 h-5 bg-gold-500/15 rounded-full animate-float" style="animation-delay: 4s;"></div>
        </div>

        <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <div class="mb-8 animate-fade-in">
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="w-14 h-14 bg-gradient-primary rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-105 group-hover:-translate-y-0.5">
                        <svg class="w-8 h-8 text-gold-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-2xl font-heading font-bold text-gradient">PageTurner</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white/95 backdrop-blur-sm shadow-2xl shadow-ink-900/10 rounded-2xl overflow-hidden border border-parchment-200 animate-fade-in-up">
                <div class="px-8 py-6">
                    {{ $slot }}
                </div>
            </div>

            <div class="mt-6 text-center text-sm text-ink-400 animate-fade-in">
                <a href="{{ route('home') }}" class="hover:text-gold-600 transition-colors inline-flex items-center font-medium group">
                    <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </body>
</html>
