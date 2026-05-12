@extends('layouts.app')
@section('title', 'All Books - PageTurner')
@section('header')
    <h1 class="font-heading text-4xl font-bold text-ink-900">Discover Books</h1>
    <p class="text-ink-400 mt-2">Browse our complete collection</p>
@endsection

@section('content')
    {{-- Search & Filter --}}
    <div class="card mb-8">
        <div class="px-6 py-5 border-b border-parchment-200">
            <div class="flex items-center justify-between">
                <h2 class="font-heading text-xl font-bold text-ink-900">Filter & Search</h2>
                <svg class="w-6 h-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
        </div>

        <form action="{{ route('books.index') }}" method="GET" class="p-6">
            <div class="mb-6">
                <label class="block text-ink-700 text-sm font-semibold mb-3">Search Books</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by title, author, or ISBN..."
                        class="input-field pl-12">
                    <svg class="w-5 h-5 text-parchment-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-ink-700 text-sm font-semibold mb-3">Category</label>
                    <select name="category" class="input-field">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-ink-700 text-sm font-semibold mb-3">Sort By</label>
                    <select name="sort" class="input-field">
                        <option value="">Default</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title: A-Z</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title: Z-A</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>

                <div>
                    <label class="block text-ink-700 text-sm font-semibold mb-3">Publication Year</label>
                    <select name="year" class="input-field">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-ink-700 text-sm font-semibold mb-3">Price Range (&#x20B1;)</label>
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                           step="0.01" min="0" placeholder="Min (&#x20B1;0.00)"
                           class="input-field">
                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                           step="0.01" min="0" placeholder="Max (&#x20B1;9999.99)"
                           class="input-field">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 btn-primary">
                    Apply Filters
                </button>
                @if(request()->hasAny(['search', 'category', 'year', 'min_price', 'max_price', 'sort']))
                    <a href="{{ route('books.index') }}" class="btn-secondary">
                        Clear All
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Active Filters Summary --}}
    @if(request()->hasAny(['search', 'category', 'year', 'min_price', 'max_price']))
        <div class="bg-gold-50 border border-gold-200 rounded-xl p-5 mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-gold-800 font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Active Filters:
                    </span>
                    @if(request('search'))
                        <span class="badge bg-white text-gold-800 border border-gold-300">
                            "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="badge bg-white text-gold-800 border border-gold-300">
                            {{ $categories->find(request('category'))->name ?? 'Unknown' }}
                        </span>
                    @endif
                    @if(request('year'))
                        <span class="badge bg-white text-gold-800 border border-gold-300">
                            {{ request('year') }}
                        </span>
                    @endif
                    @if(request('min_price'))
                        <span class="badge bg-white text-gold-800 border border-gold-300">
                            Min: &#x20B1;{{ number_format(request('min_price'), 2) }}
                        </span>
                    @endif
                    @if(request('max_price'))
                        <span class="badge bg-white text-gold-800 border border-gold-300">
                            Max: &#x20B1;{{ number_format(request('max_price'), 2) }}
                        </span>
                    @endif
                </div>
                <span class="badge badge-gold text-base">{{ $books->total() }} result(s)</span>
            </div>
        </div>
    @endif

    {{-- Books Grid --}}
    @if($books->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @foreach($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
        <div class="mt-10">{{ $books->withQueryString()->links() }}</div>
    @else
        <div class="bg-white border border-parchment-200 rounded-xl p-12 text-center">
            <svg class="w-20 h-20 text-parchment-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="font-heading text-xl font-bold text-ink-900 mb-2">No books found</h3>
            <p class="text-ink-400 mb-6">Try adjusting your filters or search terms</p>
            <a href="{{ route('books.index') }}" class="btn-primary inline-block">
                Clear Filters
            </a>
        </div>
    @endif
@endsection
