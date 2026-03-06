@extends('layouts.app')
@section('title', 'Checkout - PageTurner')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Checkout</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>

        <form action="{{ route('orders.store') }}" method="POST">
            @csrf
            @foreach($items as $index => $item)
                <input type="hidden" name="items[{{ $index }}][book_id]" value="{{ $item['book_id'] }}">
                <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
            @endforeach

            <div class="space-y-4 mb-6">
                <div>
                    <label for="shipping_name" class="block text-gray-700 font-medium mb-2">Full Name *</label>
                    <input type="text" id="shipping_name" name="shipping_name" 
                           value="{{ old('shipping_name', auth()->user()->name) }}" 
                           required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('shipping_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="shipping_phone" class="block text-gray-700 font-medium mb-2">Phone Number *</label>
                    <input type="tel" id="shipping_phone" name="shipping_phone" 
                           value="{{ old('shipping_phone') }}" 
                           required
                           placeholder="+1234567890"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('shipping_phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="shipping_address" class="block text-gray-700 font-medium mb-2">Shipping Address *</label>
                    <textarea id="shipping_address" name="shipping_address" 
                              rows="4" 
                              required
                              placeholder="Street address, City, State, ZIP Code, Country"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('shipping_address') }}</textarea>
                    @error('shipping_address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t pt-4 mb-6">
                <h3 class="font-semibold text-lg mb-4">Order Summary</h3>
                <div class="space-y-3">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <p class="font-medium">{{ $item['book']->title }}</p>
                                <p class="text-sm text-gray-600">Qty: {{ $item['quantity'] }} × ₱{{ number_format($item['book']->price, 2) }}</p>
                            </div>
                            <p class="font-semibold">₱{{ number_format($item['subtotal'], 2) }}</p>
                        </div>
                    @endforeach
                    <div class="border-t pt-3 flex justify-between items-center">
                        <p class="font-bold text-lg">Total:</p>
                        <p class="font-bold text-lg text-indigo-600">₱{{ number_format($total, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('cart.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 transition">
                    Back to Cart
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
                    Place Order
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
