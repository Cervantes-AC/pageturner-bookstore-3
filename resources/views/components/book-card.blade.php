@props(['book'])
<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
    <div class="h-48 bg-gray-200 flex items-center justify-center">
        @if($book->cover_image)
            <img src="{{ asset('storage/' . $book->cover_image) }}"
                 alt="{{ $book->title }}" class="h-full w-full object-cover">
        @else
            <div class="text-6xl">📖</div>
        @endif
    </div>
    <div class="p-4">
        <h3 class="font-semibold text-lg text-gray-800 truncate">{{ $book->title }}</h3>
        <p class="text-gray-600 text-sm">by {{ $book->author }}</p>
        <p class="text-indigo-600 font-bold mt-2">₱{{ number_format($book->price, 2) }}</p>
        <div class="flex items-center mt-2">
            @for($i = 1; $i <= 5; $i++)
                <span class="{{ $i <= round($book->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
            @endfor
            <span class="ml-1 text-sm text-gray-500">({{ $book->reviews->count() }})</span>
        </div>
        <a href="{{ route('books.show', $book) }}"
           class="mt-4 block text-center bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">
            View Details
        </a>
        @auth
            @if($book->stock_quantity > 0)
                <form action="{{ route('cart.add', $book) }}" method="POST" class="mt-2">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition text-sm">
                        🛒 Add to Cart
                    </button>
                </form>
            @else
                <div class="mt-2 text-center text-red-600 text-sm font-medium">Out of Stock</div>
            @endif
        @endauth
    </div>
</div>