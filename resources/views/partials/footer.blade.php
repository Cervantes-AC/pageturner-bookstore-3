<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
            <div class="col-span-2 md:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-primary-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900">PageTurner</span>
                </a>
                <p class="text-xs text-gray-500 leading-relaxed">Professional bookstore management platform for modern businesses.</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-3">Explore</p>
                <ul class="space-y-2">
                    <li><a href="{{ route('books.index') }}" class="text-xs text-gray-500 hover:text-primary-600 transition-colors">Browse Books</a></li>
                    <li><a href="{{ route('categories.index') }}" class="text-xs text-gray-500 hover:text-primary-600 transition-colors">Categories</a></li>
                    @auth
                    <li><a href="{{ route('orders.index') }}" class="text-xs text-gray-500 hover:text-primary-600 transition-colors">My Orders</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-3">Account</p>
                <ul class="space-y-2">
                    @guest
                    <li><a href="{{ route('login') }}" class="text-xs text-gray-500 hover:text-primary-600 transition-colors">Sign In</a></li>
                    <li><a href="{{ route('register') }}" class="text-xs text-gray-500 hover:text-primary-600 transition-colors">Register</a></li>
                    @endguest
                    @auth
                    <li><a href="{{ route('profile.edit') }}" class="text-xs text-gray-500 hover:text-primary-600 transition-colors">Profile</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-3">Support</p>
                <ul class="space-y-2">
                    <li><a href="#" class="text-xs text-gray-500 hover:text-primary-600 transition-colors">Help Center</a></li>
                    <li><a href="#" class="text-xs text-gray-500 hover:text-primary-600 transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="text-xs text-gray-500 hover:text-primary-600 transition-colors">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs text-gray-400">© {{ date('Y') }} PageTurner. All rights reserved.</p>
            <p class="text-xs text-gray-400">Built with Laravel & Tailwind CSS</p>
        </div>
    </div>
</footer>
