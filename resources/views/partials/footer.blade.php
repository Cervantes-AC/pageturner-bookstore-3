<footer class="bg-ink-900 text-parchment-300 mt-16 border-t border-ink-800 relative overflow-hidden">
    {{-- Subtle pattern overlay --}}
    <div class="absolute inset-0 bg-dots-pattern opacity-30 pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Newsletter --}}
        <div class="py-12 border-b border-ink-800">
            <div class="max-w-2xl mx-auto text-center">
                <h3 class="font-heading text-2xl font-bold text-white mb-2">Stay Updated</h3>
                <p class="text-parchment-400 text-sm mb-6">Get notified about new arrivals, exclusive deals, and reading recommendations.</p>
                <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    @csrf
                    <input type="email" placeholder="Enter your email"
                           class="flex-1 px-4 py-3 bg-ink-800 text-parchment-100 placeholder-parchment-400 border border-ink-700 rounded-xl focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition-all text-sm">
                    <button type="submit" class="bg-gradient-warm hover:opacity-90 text-white px-6 py-3 rounded-xl font-medium text-sm transition-all shadow-lg whitespace-nowrap">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        {{-- Main footer content --}}
        <div class="py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
            <div class="lg:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-8 h-8 bg-gradient-warm rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="font-heading text-xl font-bold text-white">PageTurner</span>
                </div>
                <p class="text-parchment-400 text-sm leading-relaxed max-w-md mb-6">Your destination for quality books at great prices. Discover, read, and grow with our curated collection of timeless classics and modern bestsellers.</p>
                <div class="flex items-center space-x-3">
                    <a href="#" class="w-9 h-9 bg-ink-800 hover:bg-gold-600 rounded-lg flex items-center justify-center transition-colors group" aria-label="Facebook">
                        <svg class="w-4 h-4 text-parchment-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-ink-800 hover:bg-gold-600 rounded-lg flex items-center justify-center transition-colors group" aria-label="Twitter">
                        <svg class="w-4 h-4 text-parchment-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-ink-800 hover:bg-gold-600 rounded-lg flex items-center justify-center transition-colors group" aria-label="Instagram">
                        <svg class="w-4 h-4 text-parchment-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678a6.162 6.162 0 100 12.324 6.162 6.162 0 100-12.324zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405a1.441 1.441 0 11-2.882 0 1.441 1.441 0 012.882 0z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-ink-800 hover:bg-gold-600 rounded-lg flex items-center justify-center transition-colors group" aria-label="GitHub">
                        <svg class="w-4 h-4 text-parchment-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="font-heading font-semibold text-white mb-4 text-lg">Quick Links</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('books.index') }}" class="hover:text-gold-400 transition-colors flex items-center space-x-1.5 group"><svg class="w-3 h-3 text-gold-500 opacity-0 group-hover:opacity-100 transition-opacity -ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg><span>Browse Books</span></a></li>
                    <li><a href="{{ route('categories.index') }}" class="hover:text-gold-400 transition-colors flex items-center space-x-1.5 group"><svg class="w-3 h-3 text-gold-500 opacity-0 group-hover:opacity-100 transition-opacity -ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg><span>Categories</span></a></li>
                    @auth
                        <li><a href="{{ route('orders.index') }}" class="hover:text-gold-400 transition-colors flex items-center space-x-1.5 group"><svg class="w-3 h-3 text-gold-500 opacity-0 group-hover:opacity-100 transition-opacity -ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg><span>My Orders</span></a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-gold-400 transition-colors flex items-center space-x-1.5 group"><svg class="w-3 h-3 text-gold-500 opacity-0 group-hover:opacity-100 transition-opacity -ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg><span>My Profile</span></a></li>
                    @endauth
                    @guest
                        <li><a href="{{ route('login') }}" class="hover:text-gold-400 transition-colors flex items-center space-x-1.5 group"><svg class="w-3 h-3 text-gold-500 opacity-0 group-hover:opacity-100 transition-opacity -ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg><span>Sign In</span></a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-gold-400 transition-colors flex items-center space-x-1.5 group"><svg class="w-3 h-3 text-gold-500 opacity-0 group-hover:opacity-100 transition-opacity -ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg><span>Create Account</span></a></li>
                    @endguest
                </ul>
            </div>
            <div>
                <h3 class="font-heading font-semibold text-white mb-4 text-lg">Support</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-gold-400 transition-colors">FAQ</a></li>
                    <li><a href="#" class="hover:text-gold-400 transition-colors">Shipping Info</a></li>
                    <li><a href="#" class="hover:text-gold-400 transition-colors">Return Policy</a></li>
                    <li><a href="#" class="hover:text-gold-400 transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-gold-400 transition-colors">Terms of Service</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-heading font-semibold text-white mb-4 text-lg">Contact</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 bg-ink-800 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-parchment-400">support@pageturner.com</span>
                    </li>
                    <li class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 bg-ink-800 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <span class="text-parchment-400">1-800-BOOKS</span>
                    </li>
                    <li class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 bg-ink-800 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-parchment-400">Manila, Philippines</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-ink-800 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-parchment-500">
            <p>&copy; {{ date('Y') }} PageTurner Bookstore. All rights reserved.</p>
            <div class="flex items-center space-x-4">
                <span>We accept</span>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 bg-ink-800 rounded text-xs font-mono text-parchment-400">Visa</span>
                    <span class="px-2 py-1 bg-ink-800 rounded text-xs font-mono text-parchment-400">MC</span>
                    <span class="px-2 py-1 bg-ink-800 rounded text-xs font-mono text-parchment-400">PayPal</span>
                    <span class="px-2 py-1 bg-ink-800 rounded text-xs font-mono text-parchment-400">GCash</span>
                </div>
            </div>
        </div>
    </div>
</footer>
