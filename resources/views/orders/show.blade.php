@extends('layouts.app')
@section('title', 'Order #' . $order->id . ' - PageTurner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
        <p class="text-gray-600 mt-2">Order placed on {{ $order->created_at->format('M d, Y') }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div>
                <p class="text-gray-600 text-sm">Date</p>
                <p class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Status</p>
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' :
                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                       ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' :
                       'bg-yellow-100 text-yellow-800')) }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Total</p>
                <p class="font-bold text-indigo-600 text-lg">₱{{ number_format($order->total_amount, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Items</p>
                <p class="font-semibold text-gray-900">{{ $order->orderItems->count() }}</p>
            </div>
        </div>

        {{-- Admin Status Update --}}
        @if(auth()->user()->isAdmin())
            <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="mb-6 flex gap-4 items-center">
                @csrf
                @method('PATCH')
                <select name="status" class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(['pending', 'processing', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                    Update Status
                </button>
            </form>
        @endif

        {{-- Shipping Information --}}
        @if($order->shipping_name || $order->shipping_phone || $order->shipping_address)
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-semibold text-lg mb-3 text-gray-900">Shipping Information</h3>
                <div class="space-y-2">
                    @if($order->shipping_name)
                        <p><span class="text-gray-600">Name:</span> <span class="font-medium text-gray-900">{{ $order->shipping_name }}</span></p>
                    @endif
                    @if($order->shipping_phone)
                        <p><span class="text-gray-600">Phone:</span> <span class="font-medium text-gray-900">{{ $order->shipping_phone }}</span></p>
                    @endif
                    @if($order->shipping_address)
                        <p><span class="text-gray-600">Address:</span> <span class="font-medium text-gray-900">{{ $order->shipping_address }}</span></p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Order Items --}}
        <h3 class="font-semibold text-lg mb-4 text-gray-900">Items Ordered</h3>
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Book</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Qty</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Unit Price</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($order->orderItems as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('books.show', $item->book) }}"
                                   class="text-indigo-600 hover:text-indigo-700 font-medium">
                                    {{ $item->book->title }}
                                </a>
                                <p class="text-gray-600 text-sm">by {{ $item->book->author }}</p>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-900">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-gray-900">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-900">Total:</td>
                        <td class="px-4 py-3 text-right font-bold text-indigo-600 text-lg">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6">
            <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-700 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Orders
            </a>
        </div>
    </div>
</div>
@endsection