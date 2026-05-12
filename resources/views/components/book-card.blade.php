@props(['book'])
<div class="group">
    <div class="card h-full flex flex-col bg-white">
        <div class="relative h-64 bg-gradient-to-br from-parchment-100 to-parchment-200 flex items-center justify-center overflow-hidden">
            @if($book->cover_image)
                <img src="{{ asset('storage/' . $book->cover_image) }}"
                     alt="{{ $book->title }}" 
                     class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                <svg class="w-24 h-24 text-parchment-400 group-hover:text-gold-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            @endif
            @if($book->stock_quantity <= 5 && $book->stock_quantity > 0)
                <div class="absolute top-3 right-3">
                    <span class="badge badge-warning shadow-lg">Only {{ $book->stock_quantity }} left</span>
                </div>
            @elseif($book->stock_quantity == 0)
                <div class="absolute top-3 right-3">
                    <span class="badge badge-danger shadow-lg">Out of Stock</span>
                </div>
            @endif
        </div>
        
        <div class="p-5 flex flex-col flex-grow">
            <div class="mb-2">
                <span class="text-xs font-semibold text-gold-700 uppercase tracking-widest">{{ $book->category->name }}</span>
            </div>
            
            <h3 class="font-heading font-bold text-lg text-ink-900 mb-1 line-clamp-2 group-hover:text-gold-700 transition-colors">
                {{ $book->title }}
            </h3>
            
            <p class="text-ink-400 text-sm mb-3">by {{ $book->author }}</p>
            
            <div class="flex items-center mb-3">
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($book->average_rating) ? 'text-amber-400' : 'text-parchment-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                </div>
                <span class="ml-2 text-sm text-ink-400">({{ $book->reviews->count() }})</span>
            </div>
            
            <div class="mt-auto">
                <div class="flex items-baseline mb-4">
                    <span class="font-heading text-2xl font-bold text-gold-700">&#x20B1;{{ number_format($book->price, 2) }}</span>
                </div>
                
                <a href="{{ route('books.show', $book) }}"
                   class="block text-center bg-parchment-100 text-ink-700 py-2.5 rounded-lg hover:bg-parchment-200 transition-all font-medium mb-2">
                    View Details
                </a>
                
                @auth
                    @if($book->stock_quantity > 0)
                        <form action="{{ route('cart.add', $book) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full btn-primary flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Add to Cart</span>
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full bg-parchment-200 text-parchment-400 py-2.5 rounded-lg font-medium cursor-not-allowed">
                            Out of Stock
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block text-center bg-gold-600 hover:bg-gold-700 text-white py-2.5 rounded-lg font-medium transition-colors">
                        Login to Purchase
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
