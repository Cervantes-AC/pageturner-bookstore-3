<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PageTurner') }} — Professional Bookstore</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-surface flex flex-col">

    <div class="flex min-h-screen">
        {{-- Left panel --}}
        <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 relative overflow-hidden flex-col justify-between p-12">
            {{-- Animated pattern overlay --}}
            <div class="absolute inset-0 opacity-[0.08]"
                 style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>

            {{-- Animated floating orbs --}}
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl animate-float"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-accent-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-0 left-0 right-0 h-64 bg-gradient-to-t from-primary-950/60 to-transparent"></div>

            {{-- Logo --}}
            <div class="relative z-10 animate-fade-in-down">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-white/15 backdrop-blur rounded-xl flex items-center justify-center group-hover:bg-white/25 transition-all duration-300 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">PageTurner</span>
                </a>
            </div>

            {{-- Hero text --}}
            <div class="relative z-10 animate-fade-in-up">
                <h2 class="text-4xl font-bold text-white leading-tight mb-4">
                    Your professional<br>bookstore platform
                </h2>
                <p class="text-primary-200 text-lg leading-relaxed mb-8 max-w-md">
                    Manage inventory, track orders, and grow your business with enterprise-grade tools.
                </p>
                <div class="flex flex-col gap-3">
                    @foreach([
                        ['icon' => 'M5 13l4 4L19 7', 'text' => 'Bulk import & export with Excel'],
                        ['icon' => 'M5 13l4 4L19 7', 'text' => 'Automated backups & scheduling'],
                        ['icon' => 'M5 13l4 4L19 7', 'text' => 'Full audit trail & compliance'],
                        ['icon' => 'M5 13l4 4L19 7', 'text' => 'Role-based access control'],
                    ] as $feature)
                    <div class="flex items-center gap-3 group">
                        <div class="w-6 h-6 bg-emerald-400/20 rounded-full flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-400/30 transition-colors">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-primary-100 text-sm">{{ $feature['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <p class="relative z-10 text-primary-400/60 text-xs">© {{ date('Y') }} PageTurner. All rights reserved.</p>
        </div>

        {{-- Right panel --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-white relative">
            {{-- Mobile logo --}}
            <div class="lg:hidden mb-8 animate-fade-in-down">
                <a href="/" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">PageTurner</span>
                </a>
            </div>

            <div class="w-full max-w-sm animate-scale-in">
                {{ $slot }}
            </div>
        </div>
    </div>

</body>
</html>
