<nav class="bg-white/95 backdrop-blur-md border-b border-gray-200/50 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and Primary Navigation -->
            <div class="flex items-center">
                <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-accent-600 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">PageTurner</span>
                </a>
                
                <div class="hidden md:flex ml-10 space-x-1">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">Home</a>
                    <a href="{{ route('books.index') }}" class="text-gray-700 hover:text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">Books</a>
                    <a href="{{ route('categories.index') }}" class="text-gray-700 hover:text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">Categories</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">Dashboard</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.books.create') }}" class="text-primary-600 hover:text-primary-700 hover:bg-primary-50 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Book
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side Navigation -->
            <div class="flex items-center space-x-3">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">Sign in</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm">Get Started</a>
                @else
                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-primary-600 p-2.5 rounded-lg hover:bg-primary-50 transition-all duration-200 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="absolute -top-1 -right-1 bg-gradient-to-br from-accent-500 to-accent-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow-lg animate-scale-in">{{ count(session('cart')) }}</span>
                        @endif
                    </a>

                    <!-- Notifications -->
                    @include('partials.notification-bell')

                    <!-- Orders -->
                    <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-primary-600 p-2.5 rounded-lg hover:bg-primary-50 transition-all duration-200 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </a>

                    <!-- Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 px-3 py-2 rounded-lg hover:bg-primary-50 transition-all duration-200">
                            <div class="w-9 h-9 bg-gradient-to-br from-primary-600 to-accent-600 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden md:block text-sm font-medium">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-1 overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-accent-50">
                                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-600 truncate mt-0.5">{{ auth()->user()->email }}</p>
                            </div>
                            
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile Settings
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>
