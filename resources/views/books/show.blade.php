@extends('layouts.app')
@section('title', $book->title . ' - PageTurner')

@section('content')
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="md:flex">
        {{-- Cover --}}
        <div class="md:w-1/3 bg-gray-200 p-8 flex items-center justify-center">
            @if($book->cover_image)
                <img src="{{ asset('storage/' . $book->cover_image) }}"
                     alt="{{ $book->title }}" class="max-h-96 object-contain">
            @else
                <div class="text-9xl">📖</div>
            @endif
        </div>

        {{-- Details --}}
        <div class="md:w-2/3 p-8">
            <span class="text-indigo-600 text-sm font-medium">{{ $book->category->name }}</span>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $book->title }}</h1>
            <p class="text-xl text-gray-600 mt-1">by {{ $book->author }}</p>
            @if($book->publication_year)
                <p class="text-gray-500 text-sm mt-1">Published: {{ $book->publication_year }}</p>
            @endif

            <div class="flex items-center mt-4">
                @for($i = 1; $i <= 5; $i++)
                    <span class="text-2xl {{ $i <= round($book->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                @endfor
                <span class="ml-2 text-gray-600">{{ number_format($book->average_rating, 1) }} ({{ $book->reviews->count() }} reviews)</span>
            </div>

            <p class="text-3xl font-bold text-indigo-600 mt-4">₱{{ number_format($book->price, 2) }}</p>

            <div class="mt-4">
                @if($book->stock_quantity > 0)
                    <span class="text-green-600 font-medium">✅ In Stock ({{ $book->stock_quantity }} available)</span>
                @else
                    <span class="text-red-600 font-medium">❌ Out of Stock</span>
                @endif
            </div>

            <p class="text-gray-500 text-sm mt-2"><strong>ISBN:</strong> {{ $book->isbn }}</p>

            <div class="mt-6">
                <h3 class="font-semibold text-gray-800">Description</h3>
                <p class="text-gray-600 mt-2">{{ $book->description }}</p>
            </div>

            {{-- Add to Cart Button --}}
            @auth
                @if($book->stock_quantity > 0)
                    <form action="{{ route('cart.add', $book) }}" method="POST" class="mt-6">
                        @csrf
                        <div class="flex items-center gap-4">
                            <input type="number" name="quantity" value="1" min="1"
                                   max="{{ $book->stock_quantity }}"
                                   class="w-20 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="submit"
                                    class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
                                🛒 Add to Cart
                            </button>
                        </div>
                    </form>
                @endif
            @else
                <div class="mt-6">
                    <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
                        Login to Order
                    </a>
                </div>
            @endauth

            {{-- Admin Actions --}}
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="mt-6 flex space-x-4">
                        <a href="{{ route('admin.books.edit', $book) }}"
                           class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">
                            ✏️ Edit Book
                        </a>
                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST"
                              onsubmit="return confirm('Delete this book?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</div>

{{-- Reviews --}}
<div class="mt-8">
    <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>

    @auth
        @if(auth()->user()->hasPurchased($book->id))
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="font-semibold text-lg mb-4">Write a Review</h3>
                <form action="{{ route('reviews.store', $book) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Rating</label>
                        <select name="rating" required
                                class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select rating</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Comment</label>
                        <textarea name="comment" rows="4"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Share your thoughts..."></textarea>
                    </div>
                    <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
                        Submit Review
                    </button>
                </form>
            </div>
        @else
            <x-alert type="info" class="mb-6">
                You must purchase this book before you can write a review.
            </x-alert>
        @endif
    @else
        <x-alert type="info" class="mb-6">
            <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Login</a> to write a review.
        </x-alert>
    @endauth

    @forelse($book->reviews as $review)
        <div class="bg-white rounded-lg shadow p-6 mb-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-semibold">{{ $review->user->name }}</p>
                    <div class="flex items-center mt-1">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                        @endfor
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-gray-500 text-sm">{{ $review->created_at->diffForHumans() }}</span>
                    @auth
                        @if(auth()->id() === $review->user_id || auth()->user()->isAdmin())
                            <form action="{{ route('reviews.destroy', $review) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
            @if($review->comment)
                <p class="text-gray-600 mt-3">{{ $review->comment }}</p>
            @endif
        </div>
    @empty
        <x-alert type="info">No reviews yet. Be the first to review this book!</x-alert>
    @endforelse
</div>
@endsection