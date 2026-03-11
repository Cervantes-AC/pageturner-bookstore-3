@extends('layouts.app')
@section('title', 'Shopping Cart - PageTurner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-900">Shopping Cart</h1>

        @if(empty($cartItems))
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="text-gray-900 text-lg font-medium">Your cart is empty</p>
                <p class="text-gray-600 text-sm mt-2">Add some books to get started</p>
                <a href="{{ route('books.index') }}" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                    Browse Books
                </a>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @foreach($cartItems as $item)
                    <div class="p-6 border-b border-gray-200 last:border-b-0 flex items-center gap-6">
                        <div class="w-20 h-28 bg-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden">
                            @if($item['book']->cover_image)
                                <img src="{{ asset('storage/' . $item['book']->cover_image) }}"
                                     alt="{{ $item['book']->title }}" class="h-full object-contain">
                            @else
                                <span class="text-4xl">📖</span>
                            @endif
                        </div>

                        <div class="flex-grow">
                            <h3 class="font-semibold text-lg text-gray-900">
                                <a href="{{ route('books.show', $item['book']) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $item['book']->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600">by {{ $item['book']->author }}</p>
                            <p class="text-indigo-600 font-semibold mt-1">₱{{ number_format($item['book']->price, 2) }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Stock: <span class="text-gray-700 font-medium">{{ $item['book']->stock_quantity }} available</span>
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <form action="{{ route('cart.update', $item['book']) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                       min="1" max="{{ $item['book']->stock_quantity }}"
                                       class="w-20 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <button type="submit" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">Update</button>
                            </form>

                            <form action="{{ route('cart.remove', $item['book']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <div class="text-right w-24">
                            <p class="font-semibold text-lg text-gray-900">₱{{ number_format($item['subtotal'], 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center text-xl font-bold mb-6">
                    <span class="text-gray-900">Total:</span>
                    <span class="text-indigo-600">₱{{ number_format($total, 2) }}</span>
                </div>

                <div class="flex gap-4">
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition font-medium"
                                onclick="return confirm('Clear all items from cart?')">
                            Clear Cart
                        </button>
                    </form>

                    <a href="{{ route('books.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition font-medium">
                        Continue Shopping
                    </a>

                    <a href="{{ route('cart.checkout') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition ml-auto font-medium">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
