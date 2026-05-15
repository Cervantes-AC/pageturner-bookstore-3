@extends('layouts.app')
@section('title', 'All Books - PageTurner')
@section('header')
    <h1 class="font-heading text-4xl font-bold text-ink-900">Discover Books</h1>
    <p class="text-ink-400 mt-2">Browse our complete collection</p>
@endsection

@section('content')
    <div class="mb-8 bg-white border border-parchment-200 rounded-lg p-5 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-gold-700 uppercase tracking-wider">Catalog</p>
                <h2 class="font-heading text-2xl font-bold text-ink-900 mt-1">Browse the Shelf</h2>
                <p class="text-ink-400 mt-1">Search, narrow by category, and sort the collection without leaving the page.</p>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="px-4 py-3 bg-parchment-50 rounded-lg border border-parchment-200">
                    <div class="font-heading text-xl font-bold text-ink-900">{{ $books->total() }}</div>
                    <div class="text-xs text-ink-400">Results</div>
                </div>
                <div class="px-4 py-3 bg-parchment-50 rounded-lg border border-parchment-200">
                    <div class="font-heading text-xl font-bold text-ink-900">{{ $categories->count() }}</div>
                    <div class="text-xs text-ink-400">Genres</div>
                </div>
                <div class="px-4 py-3 bg-parchment-50 rounded-lg border border-parchment-200">
                    <div class="font-heading text-xl font-bold text-ink-900">{{ $years->count() }}</div>
                    <div class="text-xs text-ink-400">Years</div>
                </div>
            </div>
        </div>
    </div>

    @include('books.partials.filter-panel', [
        'action' => route('books.index'),
        'clearUrl' => route('books.index'),
        'showCategory' => true,
        'books' => $books,
        'categories' => $categories,
        'years' => $years,
    ])

    {{-- Active Filters Summary --}}
    @if(request()->hasAny(['search', 'category', 'year', 'min_price', 'max_price', 'stock_status']))
        <div class="bg-gold-50 border border-gold-200 rounded-lg p-5 mb-8">
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
                    @if(request('stock_status'))
                        <span class="badge bg-white text-gold-800 border border-gold-300">
                            {{ request('stock_status') === 'in_stock' ? 'In Stock' : 'Out of Stock' }}
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
        <div class="bg-white border border-parchment-200 rounded-lg p-12 text-center">
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
