@extends('layouts.app')
@section('title', 'Dashboard - PageTurner')

@section('content')
    {{-- Welcome Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8 mb-8">
        <div class="flex items-center space-x-5">
            <div class="w-16 h-16 bg-gold-100 rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="font-heading text-2xl font-bold text-ink-900">Welcome back, {{ auth()->user()->name }}!</h2>
                <p class="text-ink-400">You are logged in as <span class="font-semibold text-gold-600">{{ ucfirst(auth()->user()->role) }}</span>.</p>
            </div>
        </div>
    </div>

    {{-- Account Status --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200 flex items-center space-x-4">
            <div class="w-10 h-10 {{ auth()->user()->hasVerifiedEmail() ? 'bg-emerald-100' : 'bg-amber-100' }} rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 {{ auth()->user()->hasVerifiedEmail() ? 'text-emerald-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-ink-400">Email Verification</p>
                <p class="font-semibold {{ auth()->user()->hasVerifiedEmail() ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ auth()->user()->hasVerifiedEmail() ? 'Verified' : 'Not Verified' }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-parchment-200 flex items-center space-x-4">
            <div class="w-10 h-10 {{ auth()->user()->two_factor_enabled ? 'bg-emerald-100' : 'bg-ink-100' }} rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 {{ auth()->user()->two_factor_enabled ? 'text-emerald-600' : 'text-ink-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-ink-400">Two-Factor Auth</p>
                <p class="font-semibold {{ auth()->user()->two_factor_enabled ? 'text-emerald-700' : 'text-ink-400' }}">
                    {{ auth()->user()->two_factor_enabled ? 'Enabled' : 'Disabled' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Order Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading text-lg font-semibold text-ink-900">Order Summary</h3>
                    <a href="{{ route('orders.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-medium">View all</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-3 bg-parchment-100 rounded-lg">
                        <p class="font-heading text-2xl font-bold text-ink-900">{{ $totalOrders }}</p>
                        <p class="text-xs text-ink-400">Total Orders</p>
                    </div>
                    @foreach(['pending', 'processing', 'completed'] as $status)
                        <div class="text-center p-3 bg-parchment-100 rounded-lg">
                            <p class="font-heading text-2xl font-bold
                                {{ $status === 'pending' ? 'text-amber-600' : ($status === 'processing' ? 'text-blue-600' : 'text-emerald-600') }}">
                                {{ $orderStatusSummary[$status] ?? 0 }}
                            </p>
                            <p class="text-xs text-ink-400">{{ ucfirst($status) }}</p>
                        </div>
                    @endforeach
                </div>

                @if($recentOrders->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between p-3 bg-parchment-50 rounded-lg hover:bg-parchment-100 transition-colors">
                                <div>
                                    <span class="font-medium text-ink-700">Order #{{ $order->id }}</span>
                                    <span class="text-ink-400 text-sm ml-2">{{ $order->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-semibold text-gold-700">₱{{ number_format($order->total_amount, 2) }}</span>
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $order->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-ink-400 text-sm text-center py-4">No orders yet. <a href="{{ route('books.index') }}" class="text-gold-600 hover:underline">Start shopping</a></p>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Recently Purchased Books</h3>
                @if($recentOrderBooks->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($recentOrderBooks as $book)
                            <a href="{{ route('books.show', $book) }}" class="flex items-center space-x-3 p-3 bg-parchment-50 rounded-lg hover:bg-parchment-100 transition-colors">
                                <div class="w-10 h-10 bg-gold-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-ink-700 truncate">{{ $book->title }}</p>
                                    <p class="text-xs text-ink-400">{{ $book->author }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-ink-400 text-sm text-center py-4">No purchased books yet.</p>
                @endif
            </div>
        </div>

        {{-- Side Panel --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Your Reviews</h3>
                @if($recentReviews->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentReviews as $review)
                            <div class="text-sm">
                                <div class="flex items-center space-x-1 mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-gold-500' : 'text-parchment-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-ink-400 truncate">"{{ Str::limit($review->comment ?? 'No comment', 50) }}"</p>
                                <p class="text-ink-400 text-xs mt-0.5">on <a href="{{ route('books.show', $review->book) }}" class="text-gold-600 hover:underline">{{ $review->book->title ?? 'Unknown' }}</a></p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-ink-400 text-sm text-center py-4">No reviews yet.</p>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-900 mb-4">Quick Links</h3>
                <div class="space-y-3">
                    <a href="{{ route('books.index') }}" class="flex items-center space-x-3 p-3 bg-gold-50 rounded-lg hover:bg-gold-100 transition-colors">
                        <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        <span class="text-sm font-medium text-ink-700">Browse Books</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="flex items-center space-x-3 p-3 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        <span class="text-sm font-medium text-ink-700">Order History</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span class="text-sm font-medium text-ink-700">Profile & Security</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
