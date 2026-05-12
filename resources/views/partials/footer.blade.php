<footer class="bg-ink-900 text-parchment-300 mt-16 border-t border-ink-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-8 h-8 bg-gradient-warm rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="font-heading text-xl font-bold text-white">PageTurner</span>
                </div>
                <p class="text-parchment-400 text-sm leading-relaxed max-w-md">Your destination for quality books at great prices. Discover, read, and grow with our curated collection of timeless classics and modern bestsellers.</p>
            </div>
            <div>
                <h3 class="font-heading font-semibold text-white mb-4 text-lg">Quick Links</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('books.index') }}" class="hover:text-gold-400 transition-colors">Browse Books</a></li>
                    <li><a href="{{ route('categories.index') }}" class="hover:text-gold-400 transition-colors">Categories</a></li>
                    @auth
                        <li><a href="{{ route('orders.index') }}" class="hover:text-gold-400 transition-colors">My Orders</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-gold-400 transition-colors">My Profile</a></li>
                    @endauth
                    @guest
                        <li><a href="{{ route('login') }}" class="hover:text-gold-400 transition-colors">Sign In</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-gold-400 transition-colors">Create Account</a></li>
                    @endguest
                </ul>
            </div>
            <div>
                <h3 class="font-heading font-semibold text-white mb-4 text-lg">Contact</h3>
                <ul class="space-y-2.5 text-sm">
                    <li class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-parchment-400">support@pageturner.com</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="text-parchment-400">1-800-BOOKS</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-parchment-400">Manila, Philippines</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-ink-800 mt-10 pt-6 text-center text-sm text-parchment-500">
            <p>&copy; {{ date('Y') }} PageTurner Bookstore. All rights reserved.</p>
        </div>
    </div>
</footer>
