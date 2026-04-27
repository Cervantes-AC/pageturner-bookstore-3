<div class="card-hover group flex flex-col">
    <a href="{{ route('books.show', $book) }}" class="block">
        <div class="relative h-48 bg-gray-100 overflow-hidden">
            @if($book->cover_image)
                <img src="{{ asset('storage/' . $book->cover_image) }}"
                     alt="{{ $book->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
                    <svg class="w-12 h-12 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            @endif
            @if($book->is_featured)
                <span class="absolute top-2 left-2 badge bg-amber-400 text-amber-900 text-[10px]">Featured</span>
            @endif
            @if($book->stock_quantity === 0)
                <div class="absolute inset-0 bg-gray-900/40 flex items-center justify-center">
                    <span class="badge bg-gray-800 text-white text-xs">Out of Stock</span>
                </div>
            @endif
        </div>
    </a>
    <div class="p-4 flex flex-col flex-1">
        <span class="text-xs text-primary-600 font-medium mb-1">{{ $book->category?->name }}</span>
        <a href="{{ route('books.show', $book) }}"
           class="text-sm font-semibold text-gray-900 hover:text-primary-600 transition-colors leading-snug line-clamp-2 mb-1">
            {{ $book->title }}
        </a>
        <p class="text-xs text-gray-500 mb-3">{{ $book->author }}</p>
        <div class="mt-auto flex items-center justify-between">
            <span class="text-base font-bold text-gray-900">₱{{ number_format($book->price, 2) }}</span>
            @if($book->stock_quantity > 0)
                <form action="{{ route('cart.add', $book) }}" method="POST">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                            class="p-1.5 rounded-lg bg-primary-50 text-primary-600 hover:bg-primary-600 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
