@extends('layouts.app')
@section('title', 'Order #' . $order->id . ' - PageTurner')
@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div>
            <p class="text-gray-500 text-sm">Date</p>
            <p class="font-semibold">{{ $order->created_at->format('M d, Y') }}</p>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Status</p>
            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' :
                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                   ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' :
                   'bg-yellow-100 text-yellow-800')) }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Total</p>
            <p class="font-bold text-indigo-600">₱{{ number_format($order->total_amount, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Items</p>
            <p class="font-semibold">{{ $order->orderItems->count() }}</p>
        </div>
    </div>

    {{-- Admin Status Update --}}
    @if(auth()->user()->isAdmin())
        <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="mb-6 flex gap-4 items-center">
            @csrf
            @method('PATCH')
            <select name="status" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @foreach(['pending', 'processing', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                Update Status
            </button>
        </form>
    @endif

    {{-- Shipping Information --}}
    @if($order->shipping_name || $order->shipping_phone || $order->shipping_address)
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="font-semibold text-lg mb-3">Shipping Information</h3>
            <div class="space-y-2">
                @if($order->shipping_name)
                    <p><span class="text-gray-600">Name:</span> <span class="font-medium">{{ $order->shipping_name }}</span></p>
                @endif
                @if($order->shipping_phone)
                    <p><span class="text-gray-600">Phone:</span> <span class="font-medium">{{ $order->shipping_phone }}</span></p>
                @endif
                @if($order->shipping_address)
                    <p><span class="text-gray-600">Address:</span> <span class="font-medium">{{ $order->shipping_address }}</span></p>
                @endif
            </div>
        </div>
    @endif

    {{-- Order Items --}}
    <h3 class="font-semibold text-lg mb-4">Items Ordered</h3>
    <div class="border rounded-lg overflow-hidden">
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
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('books.show', $item->book) }}"
                               class="text-indigo-600 hover:underline font-medium">
                                {{ $item->book->title }}
                            </a>
                            <p class="text-gray-500 text-sm">by {{ $item->book->author }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">₱{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right font-bold">Total:</td>
                    <td class="px-4 py-3 text-right font-bold text-indigo-600">
                        ₱{{ number_format($order->total_amount, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:underline">← Back to Orders</a>
    </div>
</div>
@endsection