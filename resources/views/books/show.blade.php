@extends('layouts.app')
@section('title', $book->title . ' — PageTurner')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('books.index') }}" class="hover:text-primary-600 transition-colors">Books</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium truncate max-w-xs">{{ $book->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">

        {{-- Cover --}}
        <div class="lg:col-span-1 reveal-left">
            <div class="card overflow-hidden aspect-[3/4] flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 group">
                @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                         alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="flex flex-col items-center gap-3 text-gray-300">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="text-sm font-medium">No cover image</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Details --}}
        <div class="lg:col-span-2 reveal-right">
            <div class="flex items-start justify-between gap-4 mb-3">
                <a href="{{ route('categories.show', $book->category) }}"
                   class="badge-primary text-xs hover:bg-primary-200 transition-colors">{{ $book->category->name }}</a>
                @if($book->is_featured)
                    <span class="badge bg-amber-100 text-amber-700 text-xs">Featured</span>
                @endif
            </div>

            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-1 leading-tight">{{ $book->title }}</h1>
            <p class="text-lg text-gray-500 mb-1">by <span class="font-semibold text-gray-700">{{ $book->author }}</span></p>
            @if($book->publication_year)
                <p class="text-sm text-gray-400 mb-4">Published {{ $book->publication_year }}</p>
            @endif

            {{-- Rating --}}
            <div class="flex items-center gap-2 mb-6">
                <div class="flex">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($book->average_rating) ? 'text-amber-400' : 'text-gray-200' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-sm font-medium text-gray-700">{{ number_format($book->average_rating, 1) }}</span>
                <span class="text-sm text-gray-400">({{ $book->reviews->count() }} {{ Str::plural('review', $book->reviews->count()) }})</span>
            </div>

            {{-- Price & Stock --}}
            <div class="flex items-baseline gap-4 mb-6">
                <span class="text-4xl font-bold text-gray-900">₱{{ number_format($book->price, 2) }}</span>
                @if($book->stock_quantity > 0)
                    <span class="badge-success">In Stock · {{ $book->stock_quantity }} left</span>
                @else
                    <span class="badge-danger">Out of Stock</span>
                @endif
            </div>

            <p class="text-xs text-gray-400 mb-6 font-mono">ISBN: {{ $book->isbn }}</p>

            @if($book->description)
            <div class="prose prose-sm text-gray-600 mb-8 max-w-none leading-relaxed">
                {{ $book->description }}
            </div>
            @endif

            {{-- Add to cart --}}
            @auth
                @if($book->stock_quantity > 0)
                <form action="{{ route('cart.add', $book) }}" method="POST" class="flex items-center gap-3 mb-4">
                    @csrf
                    <div class="flex items-center border border-gray-300 rounded-lg bg-white">
                        <button type="button" onclick="this.parentElement.querySelector('input').stepDown(); this.parentElement.querySelector('input').dispatchEvent(new Event('change'))"
                                class="px-3 py-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors rounded-l-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <input type="number" name="quantity" value="1" min="1" max="{{ $book->stock_quantity }}"
                               class="w-16 text-center text-sm font-medium border-x border-gray-300 py-2.5 bg-white focus:outline-none"/>
                        <button type="button" onclick="this.parentElement.querySelector('input').stepUp(); this.parentElement.querySelector('input').dispatchEvent(new Event('change'))"
                                class="px-3 py-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors rounded-r-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    <button type="submit" class="btn-primary btn-lg flex-1 sm:flex-none shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Add to Cart
                    </button>
                </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-primary btn-lg inline-flex shadow-sm mb-4">
                    Sign in to Order
                </a>
            @endauth

            {{-- Admin actions --}}
            @auth
                @if(auth()->user()->isAdmin())
                <div class="flex gap-2 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.books.edit', $book) }}" class="btn-secondary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST"
                          onsubmit="return confirm('Delete this book permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
                @endif
            @endauth
        </div>
    </div>

    {{-- Reviews --}}
    <div class="border-t border-gray-100 pt-10 reveal-up">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Reviews</h2>

        @auth
            @if(auth()->user()->hasPurchased($book->id))
            <div class="card p-6 mb-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Write a Review</h3>
                <form action="{{ route('reviews.store', $book) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label">Rating</label>
                            <select name="rating" required class="input-field">
                                <option value="">Select rating</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="input-label">Comment</label>
                        <textarea name="comment" rows="3" class="input-field"
                                  placeholder="Share your thoughts about this book…"></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Submit Review</button>
                </form>
            </div>
            @else
            <div class="alert-info mb-6">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Purchase this book to leave a review.</span>
            </div>
            @endif
        @else
        <div class="alert-info mb-6">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><a href="{{ route('login') }}" class="font-semibold underline">Sign in</a> to write a review.</span>
        </div>
        @endauth

        <div class="space-y-4">
            @forelse($book->reviews as $review)
            <div class="card p-5 hover-lift">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-100 to-primary-200 rounded-full flex items-center justify-center text-primary-700 font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $review->user->name }}</p>
                            <div class="flex items-center gap-1 mt-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                        @auth
                            @if(auth()->id() === $review->user_id || auth()->user()->isAdmin())
                            <form action="{{ route('reviews.destroy', $review) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">Delete</button>
                            </form>
                            @endif
                        @endauth
                    </div>
                </div>
                @if($review->comment)
                    <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ $review->comment }}</p>
                @endif
            </div>
            @empty
            <div class="card py-12 text-center">
                <p class="text-sm text-gray-400">No reviews yet. Be the first to review this book!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
