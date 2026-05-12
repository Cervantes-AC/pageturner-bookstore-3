@extends('layouts.app')
@section('title', 'My Orders - PageTurner')
@section('header')
    <h1 class="text-3xl font-bold text-gray-900">
        {{ auth()->user()->isAdmin() ? 'All Orders' : 'My Orders' }}
    </h1>
@endsection

@section('content')
    @forelse($orders as $order)
        <div class="bg-white rounded-lg shadow p-6 mb-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-semibold text-lg">Order #{{ $order->id }}</p>
                    @if(auth()->user()->isAdmin())
                        <p class="text-gray-500 text-sm">Customer: {{ $order->user->name }}</p>
                    @endif
                    <p class="text-gray-500 text-sm">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-indigo-600 text-lg">₱{{ number_format($order->total_amount, 2) }}</p>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' :
                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                           ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' :
                           'bg-yellow-100 text-yellow-800')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('orders.show', $order) }}"
                   class="text-indigo-600 hover:text-indigo-800 font-medium">View Details →</a>
            </div>
        </div>
    @empty
        <x-alert type="info">No orders found.</x-alert>
    @endforelse
    <div class="mt-6">{{ $orders->links() }}</div>
@endsection