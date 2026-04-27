<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PageTurner') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased min-h-screen bg-gray-50 flex flex-col">

    <div class="flex min-h-screen">
        {{-- Left panel --}}
        <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 bg-primary-900 relative overflow-hidden flex-col justify-between p-12">
            {{-- Pattern overlay --}}
            <div class="absolute inset-0 opacity-10"
                 style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>
            <div class="absolute bottom-0 left-0 right-0 h-64 bg-gradient-to-t from-primary-950/80 to-transparent"></div>

            {{-- Logo --}}
            <div class="relative z-10">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">PageTurner</span>
                </a>
            </div>

            {{-- Hero text --}}
            <div class="relative z-10">
                <h2 class="text-4xl font-bold text-white leading-tight mb-4">
                    Your professional<br>bookstore platform
                </h2>
                <p class="text-primary-200 text-lg leading-relaxed mb-8">
                    Manage inventory, track orders, and grow your business with enterprise-grade tools.
                </p>
                <div class="flex flex-col gap-3">
                    @foreach(['Bulk import & export with Excel', 'Automated backups & scheduling', 'Full audit trail & compliance', 'Role-based access control'] as $feature)
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 bg-emerald-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-primary-100 text-sm">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <p class="relative z-10 text-primary-400 text-xs">© {{ date('Y') }} PageTurner. All rights reserved.</p>
        </div>

        {{-- Right panel --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-white">
            {{-- Mobile logo --}}
            <div class="lg:hidden mb-8">
                <a href="/" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">PageTurner</span>
                </a>
            </div>

            <div class="w-full max-w-sm">
                {{ $slot }}
            </div>
        </div>
    </div>

</body>
</html>
