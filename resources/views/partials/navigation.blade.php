<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                    <svg class="w-8 h-8 text-emerald-500 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="text-xl font-bold text-gradient">PageTurner</span>
                </a>
                <div class="hidden md:flex ml-10 space-x-1">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-lg transition-all font-medium">Home</a>
                    <a href="{{ route('books.index') }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-lg transition-all font-medium">Books</a>
                    <a href="{{ route('categories.index') }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-lg transition-all font-medium">Categories</a>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="relative group">
                                <button class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-lg transition-all font-medium flex items-center">
                                    Admin
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <a href="{{ route('admin.books.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-t-lg">Add Book</a>
                                    <a href="{{ route('admin.categories.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-b-lg">Add Category</a>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 px-4 py-2 rounded-lg font-medium transition-all">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary">Get Started</a>
                @endguest
                @auth
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @php
                            $cart = session()->get('cart', []);
                            $cartCount = array_sum($cart);
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold animate-pulse">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-lg transition-all font-medium">Orders</a>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-lg transition-all font-medium">
                            <span>{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-t-lg">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-b-lg">Logout</button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>