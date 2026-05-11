@extends('layouts.app')
@section('title', 'Checkout - PageTurner')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        <p class="text-gray-500 mt-1">Complete your order</p>
    </div>

    <!-- Security Notice -->
    <div class="mb-6 bg-gradient-to-r from-blue-50 to-primary-50 border border-blue-200 rounded-xl p-5 animate-fade-in shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-primary-600 rounded-full flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 mb-1 flex items-center gap-2">
                    Secure Checkout with 2FA Verification
                </h3>
                <p class="text-sm text-gray-600">
                    For your security, you'll need to verify your order with a code sent to your email
                    <span class="font-semibold text-primary-600">{{ auth()->user()->email }}</span>
                    before completing the purchase.
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="card p-8 animate-slide-up">
                <h2 class="text-xl font-bold mb-6 text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Shipping Information
                </h2>

                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    @foreach($items as $index => $item)
                        <input type="hidden" name="items[{{ $index }}][book_id]" value="{{ $item['book_id'] }}">
                        <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
                    @endforeach

                    <div class="space-y-5 mb-8">
                        <div>
                            <label for="shipping_name" class="input-label">Full Name *</label>
                            <input type="text" id="shipping_name" name="shipping_name"
                                   value="{{ old('shipping_name', auth()->user()->name) }}"
                                   required
                                   class="input-field @error('shipping_name') input-field-error @enderror">
                            @error('shipping_name')
                                <p class="input-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_phone" class="input-label">Phone Number *</label>
                            <input type="tel" id="shipping_phone" name="shipping_phone"
                                   value="{{ old('shipping_phone') }}"
                                   required
                                   placeholder="+1234567890"
                                   class="input-field @error('shipping_phone') input-field-error @enderror">
                            @error('shipping_phone')
                                <p class="input-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_address" class="input-label">Shipping Address *</label>
                            <textarea id="shipping_address" name="shipping_address"
                                      rows="4"
                                      required
                                      placeholder="Street address, City, State, ZIP Code, Country"
                                      class="input-field @error('shipping_address') input-field-error @enderror">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <p class="input-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('cart.index') }}" class="btn-secondary flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Cart
                        </a>
                        <button type="submit" class="btn-primary flex-1 flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Continue to Verification
                        </button>
                    </div>

                    <p class="text-xs text-gray-500 text-center mt-6">
                        By placing this order, you agree to our terms and conditions.
                        You will receive a verification code via email to complete your purchase.
                    </p>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-24 animate-fade-in">
                <h3 class="text-xl font-bold mb-6 text-gray-900">Order Summary</h3>
                <div class="space-y-4 mb-6 pb-6 border-b border-gray-200">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 text-sm truncate">{{ $item['book']->title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Qty: {{ $item['quantity'] }} × ₱{{ number_format($item['book']->price, 2) }}</p>
                            </div>
                            <p class="font-semibold text-gray-900 text-sm">₱{{ number_format($item['subtotal'], 2) }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-2 mb-6 pb-6 border-b border-gray-200">
                    <div class="flex justify-between text-gray-600 text-sm">
                        <span>Subtotal</span>
                        <span class="font-semibold text-gray-900">₱{{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 text-sm">
                        <span>Shipping</span>
                        <span class="font-semibold text-emerald-600">Free</span>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <span class="font-bold text-lg text-gray-900">Total</span>
                    <span class="font-bold text-2xl text-primary-600">₱{{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
