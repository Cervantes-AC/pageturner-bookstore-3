<footer class="bg-gray-800 text-gray-300 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-semibold text-white mb-3">📚 PageTurner</h3>
                <p class="text-sm">Your destination for quality books at great prices. Discover, read, and grow with our curated collection.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-white mb-3">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('books.index') }}" class="hover:text-white transition-colors">Browse Books</a></li>
                    <li><a href="{{ route('categories.index') }}" class="hover:text-white transition-colors">Categories</a></li>
                    @auth
                        <li><a href="{{ route('orders.index') }}" class="hover:text-white transition-colors">My Orders</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-white transition-colors">My Profile</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-white mb-3">Contact</h3>
                <p class="text-sm">support@pageturner.com</p>
                <p class="text-sm">1-800-BOOKS</p>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm">
            <p>&copy; {{ date('Y') }} PageTurner Bookstore. All rights reserved.</p>
        </div>
    </div>
</footer>
