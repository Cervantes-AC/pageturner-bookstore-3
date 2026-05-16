<nav x-data="{ mobileOpen: false, searchOpen: false, scrolled: false, booksDropdown: false, userDropdown: false }"
     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })"
     @keydown.escape.window="booksDropdown = false; userDropdown = false; mobileOpen = false"
     :class="scrolled ? 'bg-ink-900/95 backdrop-blur-md shadow-lg' : 'bg-ink-800'"
     class="sticky top-0 z-50 border-b border-ink-700/50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Left: Logo + Nav --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                    <div class="w-8 h-8 bg-gradient-warm rounded-lg flex items-center justify-center shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="font-heading text-xl font-bold text-parchment-100">PageTurner</span>
                </a>
                <div class="hidden md:flex ml-10 space-x-1">
                    <a href="{{ route('home') }}"
                       class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}">
                        Home
                    </a>
                    {{-- Books dropdown --}}
                    <div class="relative" @mouseenter="booksDropdown = true" @mouseleave="booksDropdown = false">
                        <a href="{{ route('books.index') }}"
                           class="nav-link {{ request()->routeIs('books.*') ? 'nav-link-active' : '' }} inline-flex items-center space-x-1">
                            <span>Books</span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="booksDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                        <div x-show="booksDropdown"
                             x-transition:enter="transition-all duration-200 ease-out"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all duration-150 ease-in"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             @click.away="booksDropdown = false"
                             class="absolute top-full left-0 mt-1 w-56 bg-ink-800 border border-ink-700 rounded-xl shadow-2xl py-2 z-50"
                             x-cloak>
                            <a href="{{ route('books.index') }}" class="block px-4 py-2.5 text-sm text-parchment-300 hover:text-white hover:bg-ink-700/70 transition-colors">
                                <span class="font-medium">All Books</span>
                                <p class="text-xs text-parchment-500 mt-0.5">Browse our full collection</p>
                            </a>
                            <a href="{{ route('books.index', ['sort' => 'newest']) }}" class="block px-4 py-2.5 text-sm text-parchment-300 hover:text-white hover:bg-ink-700/70 transition-colors">
                                <span class="font-medium">New Arrivals</span>
                                <p class="text-xs text-parchment-500 mt-0.5">Recently added books</p>
                            </a>
                            <a href="{{ route('books.index', ['sort' => 'popular']) }}" class="block px-4 py-2.5 text-sm text-parchment-300 hover:text-white hover:bg-ink-700/70 transition-colors">
                                <span class="font-medium">Best Sellers</span>
                                <p class="text-xs text-parchment-500 mt-0.5">Most popular books</p>
                            </a>
                            <div class="border-t border-ink-700 my-1"></div>
                            <a href="{{ route('categories.index') }}" class="block px-4 py-2.5 text-sm text-parchment-300 hover:text-white hover:bg-ink-700/70 transition-colors">
                                <span class="font-medium">Browse by Category</span>
                                <p class="text-xs text-parchment-500 mt-0.5">Find books by genre</p>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'nav-link-active' : '' }}">Categories</a>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center space-x-1 sm:space-x-2">
                {{-- Search Toggle --}}
                <button @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput?.focus())"
                        x-on:open-search.window="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())"
                        class="text-parchment-300 hover:text-white hover:bg-ink-700/70 p-2 rounded-lg transition-all duration-200" title="Search (Ctrl+K)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>

                @guest
                    <a href="{{ route('login') }}" class="text-parchment-300 hover:text-white hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">Login</a>
                    <a href="{{ route('register') }}" class="bg-gradient-warm hover:opacity-90 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">Register</a>
                @endguest
                @auth
                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="text-parchment-300 hover:text-white hover:bg-ink-700/70 p-2 rounded-lg relative transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @php
                            $cart = session()->get('cart', []);
                            $cartCount = array_sum($cart);
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-gold-500 text-ink-900 text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow-md animate-scale-in">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    {{-- Orders --}}
                    <a href="{{ route('orders.index') }}" class="hidden lg:inline-flex text-parchment-300 hover:text-white hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">Orders</a>

                    @if(auth()->user()->isAdmin())
                        <button @click="toggleSidebar()" class="text-gold-400 hover:text-gold-300 hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="hidden lg:inline">Admin</span>
                        </button>
                    @endif

                    {{-- User dropdown --}}
                    <div class="relative" @mouseenter="userDropdown = true" @mouseleave="userDropdown = false">
                        <button class="flex items-center space-x-2 text-parchment-300 hover:text-white bg-ink-700/30 hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                            <div class="w-5 h-5 bg-gradient-warm rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <span class="hidden xl:inline">{{ auth()->user()->name }}</span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="userDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="userDropdown"
                             x-transition:enter="transition-all duration-200 ease-out"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all duration-150 ease-in"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             @click.away="userDropdown = false"
                             class="absolute top-full right-0 mt-1 w-56 bg-ink-800 border border-ink-700 rounded-xl shadow-2xl py-2 z-50"
                             x-cloak>
                            <div class="px-4 py-2 border-b border-ink-700">
                                <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-parchment-400">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-parchment-300 hover:text-white hover:bg-ink-700/70 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>My Profile</span>
                            </a>
                            <a href="{{ route('orders.index') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-parchment-300 hover:text-white hover:bg-ink-700/70 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>My Orders</span>
                            </a>
                            @if(auth()->user()->isAdmin())
                                <div class="border-t border-ink-700 my-1"></div>
                                <button @click="toggleSidebar(); userDropdown = false" class="flex items-center space-x-3 w-full px-4 py-2.5 text-sm text-gold-400 hover:text-gold-300 hover:bg-ink-700/70 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Admin Panel</span>
                                </button>
                            @endif
                            <div class="border-t border-ink-700 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center space-x-3 w-full px-4 py-2.5 text-sm text-parchment-400 hover:text-red-400 hover:bg-ink-700/70 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth

                {{-- Mobile hamburger --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden text-parchment-300 hover:text-white p-2 rounded-lg hover:bg-ink-700/70 transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display:none" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Search Bar --}}
        <div x-show="searchOpen"
             x-transition:enter="transition-all duration-200 ease-out"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition-all duration-150 ease-in"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="pb-4"
             x-cloak>
            <form action="{{ route('books.index') }}" method="GET" class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-parchment-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" placeholder="Search by title, author, or ISBN..." x-ref="searchInput"
                       class="nav-search-input pl-10"
                       value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('books.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-parchment-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </form>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen"
             x-transition:enter="transition-all duration-200 ease-out"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition-all duration-150 ease-in"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden pb-4 space-y-1"
             x-cloak>
            <a href="{{ route('home') }}" class="block text-parchment-300 hover:text-white hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">Home</a>
            <a href="{{ route('books.index') }}" class="block text-parchment-300 hover:text-white hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">Books</a>
            <a href="{{ route('categories.index') }}" class="block text-parchment-300 hover:text-white hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">Categories</a>
            @auth
                <div class="border-t border-ink-700/50 my-2"></div>
                <a href="{{ route('orders.index') }}" class="block text-parchment-300 hover:text-white hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">My Orders</a>
                <a href="{{ route('profile.edit') }}" class="block text-parchment-300 hover:text-white hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">Profile</a>
                @if(auth()->user()->isAdmin())
                    <button @click="toggleSidebar(); mobileOpen = false" class="flex items-center space-x-2 w-full text-gold-400 hover:text-gold-300 hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Admin Panel</span>
                    </button>
                @endif
                <div class="border-t border-ink-700/50 my-2"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-parchment-400 hover:text-red-400 hover:bg-ink-700/70 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</nav>
