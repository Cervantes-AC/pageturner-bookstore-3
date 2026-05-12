@extends('layouts.app')
@section('title', 'Dashboard - PageTurner')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Welcome back, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-gray-600 mb-6">You are logged in as <strong>{{ ucfirst(auth()->user()->role) }}</strong>.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('books.index') }}" class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 hover:bg-indigo-100 transition">
                <h3 class="font-semibold text-indigo-700">📚 Browse Books</h3>
                <p class="text-sm text-gray-500 mt-1">Explore our collection</p>
            </a>
            <a href="{{ route('orders.index') }}" class="bg-green-50 border border-green-200 rounded-lg p-4 hover:bg-green-100 transition">
                <h3 class="font-semibold text-green-700">🛒 My Orders</h3>
                <p class="text-sm text-gray-500 mt-1">View your order history</p>
            </a>
            <a href="{{ route('categories.index') }}" class="bg-purple-50 border border-purple-200 rounded-lg p-4 hover:bg-purple-100 transition">
                <h3 class="font-semibold text-purple-700">🗂️ Categories</h3>
                <p class="text-sm text-gray-500 mt-1">Browse by genre</p>
            </a>
        </div>
    </div>
@endsection