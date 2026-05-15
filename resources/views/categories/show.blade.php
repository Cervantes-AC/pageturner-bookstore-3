@extends('layouts.app')
@section('title', $category->name . ' - PageTurner')
@section('header')
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
        <div>
            <a href="{{ route('categories.index') }}" class="text-sm font-semibold text-gold-700 hover:text-gold-800">Categories</a>
            <h1 class="font-heading text-4xl font-bold text-ink-900 mt-2">{{ $category->name }}</h1>
            <p class="text-ink-400 mt-2">{{ $category->description ?: 'Browse every book in this category.' }}</p>
        </div>
        <a href="{{ route('books.index', ['category' => $category->id]) }}" class="btn-secondary inline-flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            Open in Catalog
        </a>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white border border-parchment-200 rounded-lg p-5 shadow-sm">
            <div class="text-sm text-ink-400">Books Found</div>
            <div class="font-heading text-3xl font-bold text-ink-900 mt-1">{{ $books->total() }}</div>
        </div>
        <div class="bg-white border border-parchment-200 rounded-lg p-5 shadow-sm">
            <div class="text-sm text-ink-400">Years Available</div>
            <div class="font-heading text-3xl font-bold text-ink-900 mt-1">{{ $years->count() }}</div>
        </div>
        <div class="bg-white border border-parchment-200 rounded-lg p-5 shadow-sm">
            <div class="text-sm text-ink-400">Category</div>
            <div class="font-heading text-3xl font-bold text-gold-700 mt-1">{{ $category->name }}</div>
        </div>
    </div>

    @include('books.partials.filter-panel', [
        'action' => route('categories.show', $category),
        'clearUrl' => route('categories.show', $category),
        'showCategory' => false,
        'books' => $books,
        'categories' => $categories,
        'years' => $years,
    ])

    @if($books->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
        <div class="mt-8">{{ $books->withQueryString()->links() }}</div>
    @else
        <div class="bg-white border border-parchment-200 rounded-lg p-10 text-center">
            <p class="font-heading text-xl font-semibold text-ink-900">No books found</p>
            <p class="text-ink-400 mt-2">Try clearing a filter or searching with a different term.</p>
            <a href="{{ route('categories.show', $category) }}" class="btn-primary inline-block mt-5">Clear Filters</a>
        </div>
    @endif
@endsection
