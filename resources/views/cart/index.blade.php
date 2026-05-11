@extends('layouts.app')
@section('title', 'Shopping Cart - PageTurner')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    @if(empty($cartItems))
        <div class="card py-16 text-center">
            <div class="empty-state">
                <div class="w-20 h-20 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <p class="empty-state-title">Your cart is empty</p>
                <p class="empty-state-text">Add some books to get started</p>
                <a href="{{ route('books.index') }}" class="btn-primary inline-flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Browse Books
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $index => $item)
                    <div class="card p-6 hover-lift" style="animation: slideUp 0.4s ease-out {{ $index * 0.05 }}s both;">
                        <div class="flex items-center gap-6">
                            <div class="w-24 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex-shrink-0 flex items-center justify-center overflow-hidden shadow-sm">
                                @if($item['book']->cover_image)
                                    <img src="{{ asset('storage/' . $item['book']->cover_image) }}"
                                         alt="{{ $item['book']->title }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="flex-grow min-w-0">
                                <h3 class="font-semibold text-lg text-gray-900 truncate">
                                    <a href="{{ route('books.show', $item['book']) }}" class="hover:text-primary-600 transition-colors">
                                        {{ $item['book']->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-500 text-sm">by {{ $item['book']->author }}</p>
                                <p class="text-primary-600 font-bold text-lg mt-2">₱{{ number_format($item['book']->price, 2) }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    <span class="badge-success">{{ $item['book']->stock_quantity }} in stock</span>
                                </p>
                            </div>

                            <div class="flex flex-col items-end gap-3">
                                <form action="{{ route('cart.update', $item['book']) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex items-center border border-gray-300 rounded-lg bg-white">
                                        <button type="button" onclick="const inp = this.parentElement.querySelector('input'); inp.stepDown(); inp.form.submit()"
                                                class="px-2 py-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors rounded-l-lg text-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                               min="1" max="{{ $item['book']->stock_quantity }}"
                                               class="w-12 text-center text-xs font-medium border-x border-gray-300 py-1.5 bg-white focus:outline-none"/>
                                        <button type="button" onclick="const inp = this.parentElement.querySelector('input'); inp.stepUp(); inp.form.submit()"
                                                class="px-2 py-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors rounded-r-lg text-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </form>

                                <form action="{{ route('cart.remove', $item['book']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors p-2 hover:bg-red-50 rounded-lg text-xs font-medium">
                                        Remove
                                    </button>
                                </form>

                                <div class="text-right">
                                    <p class="font-bold text-xl text-gray-900">₱{{ number_format($item['subtotal'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="lg:col-span-1">
                <div class="card p-6 sticky top-24 animate-fade-in">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Order Summary</h2>

                    <div class="space-y-3 mb-6 pb-6 border-b border-gray-200">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-900">₱{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="font-semibold text-emerald-600">Free</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-2xl font-bold mb-8">
                        <span class="text-gray-900">Total</span>
                        <span class="text-primary-600">₱{{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('cart.checkout') }}" class="btn-primary w-full mb-3 flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Proceed to Checkout
                    </a>

                    <a href="{{ route('books.index') }}" class="btn-outline w-full mb-3">
                        Continue Shopping
                    </a>

                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ghost w-full text-red-600 hover:bg-red-50"
                                onclick="return confirm('Clear all items from cart?')">
                            Clear Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
