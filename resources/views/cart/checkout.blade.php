@extends('layouts.app')
@section('title', 'Checkout - PageTurner')

@section('header')
    <h1 class="font-heading text-4xl font-bold text-ink-900">Checkout</h1>
    <p class="text-ink-400 mt-2">Complete your order</p>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="card">
                <div class="px-6 py-5 border-b border-parchment-200">
                    <h2 class="font-heading text-2xl font-bold text-ink-900">Shipping Information</h2>
                </div>

                <form action="{{ route('orders.store') }}" method="POST" class="p-6">
                    @csrf
                    @foreach($items as $index => $item)
                        <input type="hidden" name="items[{{ $index }}][book_id]" value="{{ $item['book_id'] }}">
                        <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
                    @endforeach

                    <div class="space-y-6 mb-8">
                        <div>
                            <label for="shipping_name" class="block text-ink-700 font-semibold mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="shipping_name" name="shipping_name"
                                   value="{{ old('shipping_name', auth()->user()->name) }}"
                                   required
                                   class="input-field">
                            @error('shipping_name')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_phone" class="block text-ink-700 font-semibold mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="shipping_phone" name="shipping_phone"
                                   value="{{ old('shipping_phone') }}"
                                   required
                                   placeholder="+1234567890"
                                   class="input-field">
                            @error('shipping_phone')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_address" class="block text-ink-700 font-semibold mb-2">
                                Shipping Address <span class="text-red-500">*</span>
                            </label>
                            <textarea id="shipping_address" name="shipping_address"
                                      rows="4"
                                      required
                                      placeholder="Street address, City, State, ZIP Code, Country"
                                      class="input-field">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('cart.index') }}" class="btn-secondary">
                            Back to Cart
                        </a>
                        <button type="submit" class="flex-1 btn-primary">
                            Place Order
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="card sticky top-24">
                <div class="px-6 py-5 border-b border-parchment-200">
                    <h3 class="font-heading text-xl font-bold text-ink-900">Order Summary</h3>
                </div>

                <div class="p-6">
                    <div class="space-y-4 mb-6">
                        @foreach($cartItems as $item)
                            <div class="flex items-start gap-3">
                                <div class="w-16 h-20 bg-gradient-to-br from-parchment-100 to-parchment-200 rounded flex-shrink-0 flex items-center justify-center overflow-hidden">
                                    @if($item['book']->cover_image)
                                        <img src="{{ $item['book']->cover_url }}"
                                             alt="{{ $item['book']->title }}" class="h-full object-contain">
                                    @else
                                        <svg class="w-8 h-8 text-parchment-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-heading font-semibold text-ink-900 text-sm truncate">{{ $item['book']->title }}</p>
                                    <p class="text-xs text-ink-400 mt-1">Qty: {{ $item['quantity'] }} x &#x20B1;{{ number_format($item['book']->price, 2) }}</p>
                                    <p class="font-semibold text-gold-600 mt-1">&#x20B1;{{ number_format($item['subtotal'], 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-parchment-200 pt-4 space-y-3">
                        <div class="flex justify-between text-ink-400">
                            <span>Subtotal</span>
                            <span>&#x20B1;{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-ink-400">
                            <span>Shipping</span>
                            <span class="text-emerald-600 font-medium">FREE</span>
                        </div>
                        <div class="border-t border-parchment-200 pt-3 flex justify-between items-center">
                            <span class="font-heading text-lg font-bold text-ink-900">Total</span>
                            <span class="font-heading text-2xl font-bold text-gold-700">&#x20B1;{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-parchment-200 space-y-3">
                        <div class="flex items-start space-x-2 text-sm text-ink-400">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>Secure checkout</p>
                        </div>
                        <div class="flex items-start space-x-2 text-sm text-ink-400">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            <p>Free shipping on all orders</p>
                        </div>
                        <div class="flex items-start space-x-2 text-sm text-ink-400">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            <p>Easy returns within 30 days</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
