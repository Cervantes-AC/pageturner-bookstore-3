@extends('layouts.app')
@section('title', $book->title . ' - PageTurner')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-parchment-200 overflow-hidden animate-fade-in-up">
    <div class="md:flex">
        {{-- Cover --}}
        <div class="md:w-5/12 bg-gradient-to-br from-parchment-100 to-parchment-200 p-8 flex items-center justify-center min-h-[400px] relative overflow-hidden group">
            <div class="absolute inset-0 bg-grid-pattern opacity-20"></div>
            @if($book->cover_image)
                <img src="{{ $book->cover_url }}"
                     alt="{{ $book->title }}" class="max-h-96 object-contain rounded-lg shadow-lg group-hover:scale-105 transition-transform duration-500">
            @else
                <div class="text-8xl opacity-30 group-hover:scale-110 group-hover:opacity-40 transition-all duration-500">
                    <svg class="w-48 h-48 text-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="md:w-7/12 p-8 lg:p-10">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gold-100 text-gold-800">{{ $book->category->name }}</span>
            <h1 class="font-heading text-3xl lg:text-4xl font-bold text-ink-900 mt-4 leading-tight">{{ $book->title }}</h1>
            <p class="text-xl text-ink-400 mt-2">by {{ $book->author }}</p>
            @if($book->publication_year)
                <p class="text-ink-400 text-sm mt-1">Published {{ $book->publication_year }}</p>
            @endif

            <div class="flex items-center mt-4">
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($book->average_rating) ? 'text-amber-400' : 'text-parchment-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                </div>
                <span class="ml-2 text-ink-400">{{ number_format($book->average_rating, 1) }} ({{ $book->reviews->count() }} reviews)</span>
            </div>

            <div class="mt-6 flex items-baseline gap-4">
                <span class="font-heading text-4xl font-bold text-gold-700">&#x20B1;{{ number_format($book->price, 2) }}</span>
                @if($book->stock_quantity > 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        In Stock ({{ $book->stock_quantity }} available)
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Out of Stock
                    </span>
                @endif
            </div>

            <p class="text-ink-400 text-sm mt-2"><strong class="text-ink-700">ISBN:</strong> {{ $book->isbn }}</p>

            <div class="mt-6">
                <h3 class="font-heading font-semibold text-ink-900 text-lg">Description</h3>
                <div class="prose prose-parchment max-w-none mt-2">
                    <p class="text-ink-400 leading-relaxed">{{ $book->description }}</p>
                </div>
            </div>

            {{-- Add to Cart / Login --}}
            @auth
                @if($book->stock_quantity > 0)
                    <form action="{{ route('cart.add', $book) }}" method="POST" class="mt-8">
                        @csrf
                        <div class="flex items-center gap-4 flex-wrap">
                            <div>
                                <label class="block text-sm font-medium text-ink-700 mb-1">Quantity</label>
                                <input type="number" name="quantity" value="1" min="1"
                                       max="{{ $book->stock_quantity }}"
                                       class="w-24 px-4 py-2.5 border border-parchment-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition-all duration-200">
                            </div>
                            <button type="submit" class="btn-primary flex items-center space-x-2 px-8 py-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </form>
                @endif
            @else
                <div class="mt-8 p-6 bg-parchment-50 rounded-xl border border-parchment-200">
                    <p class="text-ink-500 mb-4">Sign in to purchase this book</p>
                    <a href="{{ route('login') }}" class="btn-primary inline-flex items-center space-x-2 px-8 py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Login to Purchase</span>
                    </a>
                </div>
            @endauth

            {{-- Admin Actions --}}
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="mt-8 pt-6 border-t border-parchment-200 flex flex-wrap gap-3">
                        <a href="{{ route('admin.books.edit', $book) }}" class="btn-secondary inline-flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Edit Book</span>
                        </a>
                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this book?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger inline-flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</div>

{{-- Reviews --}}
<div class="mt-10 animate-fade-in-up stagger-1">
    <h2 class="font-heading text-2xl font-bold text-ink-900 mb-6 flex items-center">
        <svg class="w-6 h-6 mr-2 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
        </svg>
        Customer Reviews ({{ $book->reviews->count() }})
    </h2>

    {{-- Write Review Form --}}
    @auth
        @if(auth()->user()->hasPurchased($book->id))
            <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-6 mb-6">
                <h3 class="font-heading font-semibold text-lg text-ink-900 mb-4">Write a Review</h3>
                <form action="{{ route('reviews.store', $book) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-ink-700 font-medium mb-2">Rating</label>
                        <select name="rating" required class="input-field max-w-xs">
                            <option value="">Select rating</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-ink-700 font-medium mb-2">Comment</label>
                        <textarea name="comment" rows="4"
                                  class="input-field"
                                  placeholder="Share your thoughts about this book..."></textarea>
                    </div>
                    <button type="submit" class="btn-primary">
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
            <a href="{{ route('login') }}" class="text-gold-600 hover:text-gold-700 font-semibold underline">Login</a> to write a review.
        </x-alert>
    @endauth

    {{-- Reviews List --}}
    @forelse($book->reviews as $review)
        <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-6 mb-4 hover:shadow-md transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-gold-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-gold-700 font-semibold">{{ substr($review->user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <p class="font-semibold text-ink-900">{{ $review->user->name }}</p>
                            <span class="text-ink-400 text-sm">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-parchment-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        @if($review->comment)
                            <p class="text-ink-400 mt-2 leading-relaxed">{{ $review->comment }}</p>
                        @endif
                    </div>
                </div>
                @auth
                    @if(auth()->id() === $review->user_id || auth()->user()->isAdmin())
                        <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="flex-shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-parchment-400 hover:text-red-500 transition-colors p-1 hover:bg-red-50 rounded-lg" title="Delete review">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <x-alert type="info">No reviews yet. Be the first to review this book!</x-alert>
    @endforelse
</div>
@endsection
