@extends('layouts.app')
@section('title', 'Order #' . $order->id . ' - PageTurner')
@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
    <p class="text-gray-600 mt-2">Order details and status</p>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
        <div>
            <p class="text-gray-500 text-sm font-medium">Date</p>
            <p class="font-semibold text-gray-900 mt-1">{{ $order->created_at->format('M d, Y') }}</p>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-medium">Status</p>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1
                {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                   ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' :
                   'bg-orange-100 text-orange-800')) }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-medium">Total</p>
            <p class="font-bold text-emerald-600 text-xl mt-1">₱{{ number_format($order->total_amount, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-medium">Items</p>
            <p class="font-semibold text-gray-900 mt-1">{{ $order->orderItems->count() }} {{ Str::plural('item', $order->orderItems->count()) }}</p>
        </div>
    </div>

    {{-- Admin Status Update --}}
    @if(auth()->user()->isAdmin())
        <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="mb-6 p-4 bg-gray-50 rounded-xl flex flex-wrap gap-4 items-center">
            @csrf
            @method('PATCH')
            <label class="text-sm font-medium text-gray-700">Update Status:</label>
            <select name="status" class="input-field max-w-xs">
                @foreach(['pending', 'processing', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Update Status</button>
        </form>
    @endif

    {{-- Shipping Information --}}
    @if($order->shipping_name || $order->shipping_phone || $order->shipping_address)
        <div class="mb-6 p-5 bg-gray-50 rounded-xl">
            <h3 class="font-semibold text-lg text-gray-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Shipping Information
            </h3>
            <div class="space-y-2 text-sm">
                @if($order->shipping_name)
                    <p><span class="text-gray-500">Name:</span> <span class="font-medium text-gray-900">{{ $order->shipping_name }}</span></p>
                @endif
                @if($order->shipping_phone)
                    <p><span class="text-gray-500">Phone:</span> <span class="font-medium text-gray-900">{{ $order->shipping_phone }}</span></p>
                @endif
                @if($order->shipping_address)
                    <p><span class="text-gray-500">Address:</span> <span class="font-medium text-gray-900">{{ $order->shipping_address }}</span></p>
                @endif
            </div>
        </div>
    @endif

    {{-- Order Items --}}
    <h3 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        Items Ordered
    </h3>
    <div class="border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3.5 text-left text-sm font-semibold text-gray-700">Book</th>
                    <th class="px-5 py-3.5 text-center text-sm font-semibold text-gray-700">Qty</th>
                    <th class="px-5 py-3.5 text-right text-sm font-semibold text-gray-700">Unit Price</th>
                    <th class="px-5 py-3.5 text-right text-sm font-semibold text-gray-700">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($order->orderItems as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('books.show', $item->book) }}"
                               class="text-emerald-600 hover:text-emerald-700 font-medium transition-colors">
                                {{ $item->book->title }}
                            </a>
                            <p class="text-gray-500 text-sm">by {{ $item->book->author }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">{{ $item->quantity }}</td>
                        <td class="px-5 py-4 text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-5 py-4 text-right font-semibold">₱{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-5 py-4 text-right font-bold text-gray-900">Total:</td>
                    <td class="px-5 py-4 text-right font-bold text-emerald-600 text-lg">
                        ₱{{ number_format($order->total_amount, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Orders
        </a>
    </div>
</div>
@endsection
