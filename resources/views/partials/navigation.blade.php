<nav class="bg-ink-800 border-b border-ink-700 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                    <div class="w-8 h-8 bg-gradient-warm rounded-lg flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="font-heading text-xl font-bold text-parchment-100">PageTurner</span>
                </a>
                <div class="hidden md:flex ml-10 space-x-1">
                    <a href="{{ route('home') }}" class="text-parchment-300 hover:text-white hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">Home</a>
                    <a href="{{ route('books.index') }}" class="text-parchment-300 hover:text-white hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">Books</a>
                    <a href="{{ route('categories.index') }}" class="text-parchment-300 hover:text-white hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">Categories</a>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @guest
                    <a href="{{ route('login') }}" class="text-parchment-300 hover:text-white hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="bg-gold-600 hover:bg-gold-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-md">Register</a>
                @endguest
                @auth
                    <a href="{{ route('cart.index') }}" class="text-parchment-300 hover:text-white hover:bg-ink-700 px-3 py-2 rounded-lg relative transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @php
                            $cart = session()->get('cart', []);
                            $cartCount = array_sum($cart);
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-gold-500 text-ink-900 text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow-md">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('orders.index') }}" class="text-parchment-300 hover:text-white hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">My Orders</a>
                    @if(auth()->user()->isAdmin())
                        <button @click="toggleSidebar()" class="text-gold-400 hover:text-gold-300 hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Admin</span>
                        </button>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="text-parchment-300 hover:text-white bg-ink-700/50 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        {{ auth()->user()->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-parchment-400 hover:text-red-400 hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">Logout</button>
                    </form>
                @endauth
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-parchment-300 hover:text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display:none" />
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-transition class="md:hidden pb-4 space-y-1">
            <a href="{{ route('home') }}" class="block text-parchment-300 hover:text-white hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium">Home</a>
            <a href="{{ route('books.index') }}" class="block text-parchment-300 hover:text-white hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium">Books</a>
            <a href="{{ route('categories.index') }}" class="block text-parchment-300 hover:text-white hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium">Categories</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="border-t border-ink-700 my-2"></div>
                    <button @click="toggleSidebar(); mobileMenuOpen = false" class="flex items-center space-x-2 w-full text-gold-400 hover:text-gold-300 hover:bg-ink-700 px-3 py-2 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Admin Panel</span>
                    </button>
                @endif
            @endauth
        </div>
    </div>
</nav>
