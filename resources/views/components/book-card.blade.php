<div class="card-hover group flex flex-col relative">
    <a href="{{ route('books.show', $book) }}" class="block">
        <div class="relative h-52 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
            @if($book->cover_image)
                <img src="{{ asset('storage/' . $book->cover_image) }}"
                     alt="{{ $book->title }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 group-hover:from-primary-100 group-hover:to-primary-200 transition-colors duration-300">
                    <svg class="w-14 h-14 text-primary-300 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            @endif
            @if($book->is_featured)
                <span class="featured-ribbon">Featured</span>
            @endif
            @if($book->stock_quantity === 0)
                <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-[2px] flex items-center justify-center transition-opacity">
                    <span class="px-3 py-1.5 bg-gray-800/90 text-white text-xs font-semibold rounded-full">Out of Stock</span>
                </div>
            @endif
            {{-- Hover overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/0 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>
    </a>
    <div class="p-4 flex flex-col flex-1">
        <span class="text-xs font-medium text-primary-600 mb-1.5">{{ $book->category?->name }}</span>
        <a href="{{ route('books.show', $book) }}"
           class="text-sm font-semibold text-gray-900 hover:text-primary-600 transition-colors leading-snug line-clamp-2 mb-1 flex-1">
            {{ $book->title }}
        </a>
        <p class="text-xs text-gray-500 mb-3">{{ $book->author }}</p>
        <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100">
            <span class="text-base font-bold text-gray-900">₱{{ number_format($book->price, 2) }}</span>
            @if($book->stock_quantity > 0)
                <form action="{{ route('cart.add', $book) }}" method="POST">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary-50 text-primary-600 hover:bg-primary-600 hover:text-white hover:shadow-sm transition-all duration-200 active:scale-90">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
