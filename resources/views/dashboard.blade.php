@extends('layouts.app')
@section('title', 'Dashboard - PageTurner')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8">
        <div class="flex items-center space-x-5 mb-6">
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

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('books.index') }}" class="group bg-gradient-to-br from-gold-50 to-amber-50 border border-gold-200 rounded-xl p-5 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="w-10 h-10 bg-gold-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-gold-200 transition-colors">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="font-heading font-semibold text-gold-800">Browse Books</h3>
                <p class="text-sm text-gold-600 mt-1">Explore our collection</p>
            </a>
            <a href="{{ route('orders.index') }}" class="group bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-5 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-amber-200 transition-colors">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h3 class="font-heading font-semibold text-amber-800">My Orders</h3>
                <p class="text-sm text-amber-600 mt-1">View your order history</p>
            </a>
            <a href="{{ route('categories.index') }}" class="group bg-gradient-to-br from-ink-50 to-parchment-200 border border-ink-200 rounded-xl p-5 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="w-10 h-10 bg-ink-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-ink-200 transition-colors">
                    <svg class="w-5 h-5 text-ink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="font-heading font-semibold text-ink-800">Categories</h3>
                <p class="text-sm text-ink-600 mt-1">Browse by genre</p>
            </a>
        </div>
    </div>
@endsection
