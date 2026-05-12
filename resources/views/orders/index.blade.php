@extends('layouts.app')
@section('title', 'My Orders - PageTurner')
@section('header')
    <h1 class="text-3xl font-bold text-gray-900">
        {{ auth()->user()->isAdmin() ? 'All Orders' : 'My Orders' }}
    </h1>
    <p class="text-gray-600 mt-2">Track and manage your orders</p>
@endsection

@section('content')
    @forelse($orders as $order)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-lg text-gray-900">Order #{{ $order->id }}</p>
                        @if(auth()->user()->isAdmin())
                            <p class="text-gray-500 text-sm">Customer: {{ $order->user->name }}</p>
                        @endif
                        <p class="text-gray-500 text-sm">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="text-right flex flex-col items-end gap-2">
                    <p class="font-bold text-emerald-600 text-xl">₱{{ number_format($order->total_amount, 2) }}</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                           ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' :
                           'bg-orange-100 text-orange-800')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('orders.show', $order) }}"
                   class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium transition-colors">
                    View Details
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    @empty
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-12 text-center">
            <svg class="w-20 h-20 text-blue-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No orders yet</h3>
            <p class="text-gray-600 mb-6">Start shopping to see your orders here</p>
            <a href="{{ route('books.index') }}" class="btn-primary inline-block">Browse Books</a>
        </div>
    @endforelse
    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
