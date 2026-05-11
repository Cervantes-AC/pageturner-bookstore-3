@extends('layouts.app')
@section('title', 'Books — PageTurner')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Sidebar Filters --}}
        <aside class="lg:w-64 flex-shrink-0">
            <div class="card sticky top-20 reveal-left">
                <div class="card-header">
                    <h2 class="text-sm font-semibold text-gray-900">Filters</h2>
                    @if(request()->hasAny(['search','category','year','min_price','max_price','sort']))
                        <a href="{{ route('books.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">Clear all</a>
                    @endif
                </div>
                <div class="p-4 space-y-5">
                    <form action="{{ route('books.index') }}" method="GET" class="space-y-5">
                        <div>
                            <label class="input-label">Search</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Title, author, ISBN…"
                                       class="input-field pl-9 text-sm"/>
                            </div>
                        </div>

                        <div>
                            <label class="input-label">Category</label>
                            <select name="category" class="input-field text-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="input-label">Publication Year</label>
                            <select name="year" class="input-field text-sm">
                                <option value="">All Years</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="input-label">Price Range (₱)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}"
                                       placeholder="Min" step="0.01" min="0" class="input-field text-sm"/>
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                       placeholder="Max" step="0.01" min="0" class="input-field text-sm"/>
                            </div>
                        </div>

                        <div>
                            <label class="input-label">Sort By</label>
                            <select name="sort" class="input-field text-sm">
                                <option value="">Newest First</option>
                                <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Price: Low → High</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                                <option value="title_asc"  {{ request('sort') === 'title_asc'  ? 'selected' : '' }}>Title: A → Z</option>
                                <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Title: Z → A</option>
                                <option value="oldest"     {{ request('sort') === 'oldest'     ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-primary w-full">Apply Filters</button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-5 animate-fade-in-down">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">All Books</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $books->total() }} {{ Str::plural('result', $books->total()) }}</p>
                </div>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.books.create') }}" class="btn-primary btn-sm shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Book
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Active filters --}}
            @if(request()->hasAny(['search','category','year','min_price','max_price']))
            <div class="flex flex-wrap gap-2 mb-5 animate-fade-in">
                @if(request('search'))
                    <span class="badge-gray">Search: "{{ request('search') }}"</span>
                @endif
                @if(request('category'))
                    <span class="badge-gray">{{ $categories->find(request('category'))?->name }}</span>
                @endif
                @if(request('year'))
                    <span class="badge-gray">Year: {{ request('year') }}</span>
                @endif
                @if(request('min_price'))
                    <span class="badge-gray">Min: ₱{{ number_format(request('min_price'), 2) }}</span>
                @endif
                @if(request('max_price'))
                    <span class="badge-gray">Max: ₱{{ number_format(request('max_price'), 2) }}</span>
                @endif
            </div>
            @endif

            @if($books->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($books as $book)
                        <x-book-card :book="$book"/>
                    @endforeach
                </div>
                <div class="mt-8">{{ $books->withQueryString()->links() }}</div>
            @else
                <div class="card py-16 text-center animate-scale-in">
                    <div class="empty-state">
                        <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="empty-state-title">No books found</p>
                        <p class="empty-state-text">No books match your filters. Try adjusting your search criteria.</p>
                        <a href="{{ route('books.index') }}" class="btn-secondary btn-sm">Clear filters</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
