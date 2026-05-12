@extends('layouts.app')
@section('title', 'Shopping Cart - PageTurner')

@section('header')
    <h1 class="font-heading text-4xl font-bold text-ink-900">Shopping Cart</h1>
    <p class="text-ink-400 mt-2">Review your items before checkout</p>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    @if(empty($cartItems))
        <div class="bg-white border border-parchment-200 rounded-xl p-12 text-center">
            <svg class="w-24 h-24 text-parchment-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="font-heading text-2xl font-bold text-ink-900 mb-3">Your cart is empty</h3>
            <p class="text-ink-400 mb-8">Start adding books to your cart to see them here</p>
            <a href="{{ route('books.index') }}" class="btn-primary inline-block">
                Browse Books
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    <div class="card">
                        <div class="p-6 flex items-center gap-6">
                            <div class="w-24 h-32 bg-gradient-to-br from-parchment-100 to-parchment-200 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden">
                                @if($item['book']->cover_image)
                                    <img src="{{ asset('storage/' . $item['book']->cover_image) }}"
                                         alt="{{ $item['book']->title }}" class="h-full object-contain">
                                @else
                                    <svg class="w-12 h-12 text-parchment-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                @endif
                            </div>

                            <div class="flex-grow">
                                <h3 class="font-heading font-bold text-lg text-ink-900 mb-1">
                                    <a href="{{ route('books.show', $item['book']) }}" class="hover:text-gold-600 transition-colors">
                                        {{ $item['book']->title }}
                                    </a>
                                </h3>
                                <p class="text-ink-400 mb-2">by {{ $item['book']->author }}</p>
                                <p class="text-gold-600 font-semibold text-lg">&#x20B1;{{ number_format($item['book']->price, 2) }}</p>

                                @if($item['book']->stock_quantity > 0)
                                    @if($item['book']->stock_quantity <= 5)
                                        <span class="badge badge-warning mt-2">Only {{ $item['book']->stock_quantity }} left!</span>
                                    @elseif($item['book']->stock_quantity <= 10)
                                        <span class="badge badge-info mt-2">{{ $item['book']->stock_quantity }} available</span>
                                    @else
                                        <span class="badge badge-success mt-2">In Stock</span>
                                    @endif
                                @else
                                    <span class="badge badge-danger mt-2">Out of stock</span>
                                @endif
                            </div>

                            <div class="flex flex-col items-end gap-4">
                                <form action="{{ route('cart.update', $item['book']) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                           min="1" max="{{ $item['book']->stock_quantity }}"
                                           class="w-20 px-3 py-2 border border-parchment-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                                    <button type="submit" class="text-gold-600 hover:text-gold-700 font-medium">
                                        Update
                                    </button>
                                </form>

                                <form action="{{ route('cart.remove', $item['book']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 flex items-center space-x-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="font-medium">Remove</span>
                                    </button>
                                </form>

                                <div class="text-right">
                                    <p class="text-sm text-ink-400">Subtotal</p>
                                    <p class="font-heading font-bold text-xl text-ink-900">&#x20B1;{{ number_format($item['subtotal'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="lg:col-span-1">
                <div class="card sticky top-24">
                    <div class="p-6">
                        <h2 class="font-heading text-2xl font-bold text-ink-900 mb-6">Order Summary</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-ink-400">
                                <span>Subtotal</span>
                                <span>&#x20B1;{{ number_format($total, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-ink-400">
                                <span>Shipping</span>
                                <span class="text-emerald-600 font-medium">FREE</span>
                            </div>
                            <div class="border-t border-parchment-200 pt-3 flex justify-between items-center">
                                <span class="font-heading text-xl font-bold text-ink-900">Total</span>
                                <span class="font-heading text-2xl font-bold text-gold-700">&#x20B1;{{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('cart.checkout') }}" class="block w-full btn-primary text-center">
                                Proceed to Checkout
                            </a>

                            <a href="{{ route('books.index') }}" class="block w-full btn-secondary text-center">
                                Continue Shopping
                            </a>

                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full text-red-600 hover:text-red-700 font-medium py-2"
                                        onclick="return confirm('Clear all items from cart?')">
                                    Clear Cart
                                </button>
                            </form>
                        </div>

                        <div class="mt-6 pt-6 border-t border-parchment-200">
                            <div class="flex items-start space-x-3 text-sm text-ink-400">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Secure checkout with encrypted payment processing</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
