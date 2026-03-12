<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PageTurner') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-400/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-accent-400/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Logo -->
        <div class="mb-8 relative z-10 animate-fade-in">
            <a href="/" class="flex items-center space-x-3 group">
                <div class="w-14 h-14 bg-gradient-to-br from-primary-400 to-accent-500 rounded-2xl flex items-center justify-center shadow-2xl group-hover:scale-105 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-primary-100 bg-clip-text text-transparent">
                        PageTurner
                    </h1>
                    <p class="text-xs text-primary-200 -mt-1">Professional Bookstore</p>
                </div>
            </a>
        </div>

        <!-- Card -->
        <div class="w-full sm:max-w-md relative z-10 animate-slide-up">
            <div class="bg-white/95 backdrop-blur-xl border border-white/20 shadow-2xl rounded-2xl overflow-hidden">
                <div class="px-8 py-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center relative z-10">
            <p class="text-sm text-primary-100">
                © {{ date('Y') }} PageTurner. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>