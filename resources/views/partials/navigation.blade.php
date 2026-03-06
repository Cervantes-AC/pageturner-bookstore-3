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
                    <a href="{{ route('profile.edit') }}" class="text-indigo-200 hover:text-white hover:bg-indigo-700 px-3 py-2 rounded-md">
                            {{ auth()->user()->name }}
                        </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:bg-indigo-700 px-3 py-2 rounded-md">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>