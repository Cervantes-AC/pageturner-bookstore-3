@extends('layouts.app')
@section('title', 'My Orders - PageTurner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            {{ auth()->user()->isAdmin() ? 'All Orders' : 'My Orders' }}
        </h1>
        <p class="text-gray-600 mt-2">View and manage your order history</p>
    </div>

    @forelse($orders as $order)
        <div class="bg-white rounded-lg shadow p-6 mb-4 hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-semibold text-lg text-gray-900">Order #{{ $order->id }}</p>
                    @if(auth()->user()->isAdmin())
                        <p class="text-gray-600 text-sm">Customer: {{ $order->user->name }}</p>
                    @endif
                    <p class="text-gray-500 text-sm">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900 text-lg">₱{{ number_format($order->total_amount, 2) }}</p>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
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
                   class="text-indigo-600 hover:text-indigo-700 font-semibold inline-flex items-center">
                   View Details 
                   <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                   </svg>
                </a>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <p class="text-gray-900 text-lg font-medium">No orders found</p>
            <p class="text-gray-600 text-sm mt-2">Start shopping to see your orders here</p>
            <a href="{{ route('books.index') }}" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                Browse Books
            </a>
        </div>
    @endforelse
    
    @if($orders->count() > 0)
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
