<nav class="bg-indigo-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-xl font-bold">PageTurner</a>
                <div class="hidden md:flex ml-10 space-x-4">
                    <a href="{{ route('home') }}" class="hover:bg-indigo-700 px-3 py-2 rounded-md">Home</a>
                    <a href="{{ route('books.index') }}" class="hover:bg-indigo-700 px-3 py-2 rounded-md">Books</a>
                    <a href="{{ route('categories.index') }}" class="hover:bg-indigo-700 px-3 py-2 rounded-md">Categories</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:bg-indigo-700 px-3 py-2 rounded-md">Dashboard</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.books.create') }}" class="hover:bg-indigo-700 px-3 py-2 rounded-md">+ Add Book</a>
                            <a href="{{ route('admin.categories.create') }}" class="hover:bg-indigo-700 px-3 py-2 rounded-md">+ Add Category</a>
                        @endif
                    @endauth
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="hover:bg-indigo-700 px-3 py-2 rounded-md">Login</a>
                    <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-4 py-2 rounded-md font-medium">Register</a>
                @endguest
                @auth
                    @if(!auth()->user()->hasVerifiedEmail())
                        <div class="bg-yellow-500 text-yellow-900 px-2 py-1 rounded text-xs font-medium">
                            Email Not Verified
                        </div>
                    @endif
                    
                    <a href="{{ route('cart.index') }}" class="hover:bg-indigo-700 px-3 py-2 rounded-md relative">
                        🛒 Cart
                        @php
                            $cart = session()->get('cart', []);
                            $cartCount = array_sum($cart);
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('orders.index') }}" class="hover:bg-indigo-700 px-3 py-2 rounded-md">My Orders</a>
                    
                    {{-- User Dropdown --}}
                    <div class="relative group">
                        <button class="flex items-center text-indigo-200 hover:text-white hover:bg-indigo-700 px-3 py-2 rounded-md">
                            {{ auth()->user()->name }}
                            @if(auth()->user()->two_factor_enabled)
                                <svg class="w-3 h-3 ml-1 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Profile Settings
                            </a>
                            @if(auth()->user()->two_factor_enabled)
                                <a href="{{ route('two-factor.recovery-codes') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Recovery Codes
                                </a>
                            @endif
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>