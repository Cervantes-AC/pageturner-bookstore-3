@extends('layouts.app')
@section('title', 'All Books - PageTurner')
@section('header')
    <h1 class="text-3xl font-bold text-gray-900">All Books</h1>
@endsection

@section('content')
    {{-- Search & Filter --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Filter Books</h2>
        </div>
        
        <form action="{{ route('books.index') }}" method="GET" class="p-6">
            {{-- Search --}}
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by title, author, or ISBN..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Category --}}
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Category</label>
                    <select name="category" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Year --}}
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Publication Year</label>
                    <select name="year" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sort --}}
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Sort By</label>
                    <select name="sort" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Default (Newest)</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title: A to Z</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title: Z to A</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
            </div>

            {{-- Price Range --}}
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2">Price Range (₱)</label>
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                           step="0.01" min="0" placeholder="Min (₱0.00)"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                           step="0.01" min="0" placeholder="Max (₱9999.99)"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-2.5 rounded-md hover:bg-indigo-700 transition font-medium">
                    Apply Filters
                </button>
                @if(request()->hasAny(['search', 'category', 'year', 'min_price', 'max_price', 'sort']))
                    <a href="{{ route('books.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-md hover:bg-gray-300 transition font-medium">
                        Clear All
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Active Filters Summary --}}
    @if(request()->hasAny(['search', 'category', 'year', 'min_price', 'max_price']))
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-indigo-800 font-medium">Active Filters:</span>
                    @if(request('search'))
                        <span class="bg-white text-indigo-800 px-3 py-1 rounded-full text-sm border border-indigo-300">
                            🔍 "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="bg-white text-indigo-800 px-3 py-1 rounded-full text-sm border border-indigo-300">
                            📚 {{ $categories->find(request('category'))->name ?? 'Unknown' }}
                        </span>
                    @endif
                    @if(request('year'))
                        <span class="bg-white text-indigo-800 px-3 py-1 rounded-full text-sm border border-indigo-300">
                            📅 {{ request('year') }}
                        </span>
                    @endif
                    @if(request('min_price'))
                        <span class="bg-white text-indigo-800 px-3 py-1 rounded-full text-sm border border-indigo-300">
                            Min: ₱{{ number_format(request('min_price'), 2) }}
                        </span>
                    @endif
                    @if(request('max_price'))
                        <span class="bg-white text-indigo-800 px-3 py-1 rounded-full text-sm border border-indigo-300">
                            Max: ₱{{ number_format(request('max_price'), 2) }}
                        </span>
                    @endif
                </div>
                <span class="text-indigo-800 font-semibold">{{ $books->total() }} result(s)</span>
            </div>
        </div>
    @endif

    {{-- Books Grid --}}
    @if($books->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
        <div class="mt-8">{{ $books->withQueryString()->links() }}</div>
    @else
        <x-alert type="info">No books found matching your criteria.</x-alert>
    @endif
@endsection