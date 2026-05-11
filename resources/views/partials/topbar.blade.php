{{-- Top bar (search · cart · notifications · profile) --}}
<header class="sticky top-0 z-10 glass border-b border-gray-200/80 h-16 flex items-center px-4 sm:px-6 gap-4 flex-shrink-0 shadow-sm">

    {{-- Mobile hamburger --}}
    <button @click="sidebarOpen = true"
            class="lg:hidden p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Page title --}}
    <div class="flex-1 min-w-0">
        <h1 class="text-base font-semibold text-gray-900 truncate">@yield('page-title', 'PageTurner')</h1>
    </div>

    {{-- Right actions --}}
    <div class="flex items-center gap-1">

        @guest
            <a href="{{ route('login') }}"
               class="px-3.5 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200">
                Sign in
            </a>
            <a href="{{ route('register') }}" class="btn-primary btn-sm shadow-sm">Get Started</a>
        @endguest

        @auth
            {{-- Cart --}}
            <a href="{{ route('cart.index') }}"
               class="relative p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200 group"
               title="Shopping Cart">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                @php $cartCount = array_sum(session()->get('cart', [])); @endphp
                @if($cartCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-primary-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-scale-in">
                        {{ $cartCount > 9 ? '9+' : $cartCount }}
                    </span>
                @endif
            </a>

            {{-- Notifications --}}
            @include('partials.notification-bell')

            {{-- Profile dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg hover:bg-gray-100 transition-all duration-200">
                    <div class="w-7 h-7 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-gray-700 max-w-[120px] truncate">
                        {{ auth()->user()->name }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-1.5 w-52 bg-white rounded-xl shadow-soft border border-gray-200 py-1.5 z-50"
                     style="display:none;">

                    <div class="px-3 py-2 border-b border-gray-100 mb-1">
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        <span class="mt-1 inline-block text-[10px] font-semibold px-1.5 py-0.5 rounded {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile Settings
                    </a>

                    <div class="border-t border-gray-100 mt-1 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</header>
