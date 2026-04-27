@extends('layouts.app')
@section('title', 'PageTurner — Professional Bookstore')

@section('content')
{{-- Hero --}}
<section class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-50 text-primary-700 text-xs font-semibold mb-6 border border-primary-100">
                <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
                Professional Bookstore Platform
            </span>
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-5">
                Discover your next<br>
                <span class="text-primary-600">great read</span>
            </h1>
            <p class="text-lg text-gray-500 mb-8 leading-relaxed">
                Browse thousands of titles across every genre. From bestsellers to hidden gems — find exactly what you're looking for.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('books.index') }}" class="btn-primary btn-lg">
                    Browse Books
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="{{ route('categories.index') }}" class="btn-secondary btn-lg">
                    View Categories
                </a>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">

    {{-- Categories --}}
    <section>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Browse by Category</h2>
                <p class="text-sm text-gray-500 mt-0.5">Find books in your favourite genre</p>
            </div>
            <a href="{{ route('categories.index') }}"
               class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($categories as $category)
            <a href="{{ route('categories.show', $category) }}"
               class="card-hover p-4 text-center group">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center mx-auto mb-2.5 group-hover:bg-primary-100 transition-colors">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors leading-tight">{{ $category->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $category->books_count }} books</p>
            </a>
            @endforeach
        </div>
    </section>

    {{-- Featured Books --}}
    <section>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Featured Books</h2>
                <p class="text-sm text-gray-500 mt-0.5">Hand-picked titles worth reading</p>
            </div>
            <a href="{{ route('books.index') }}"
               class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1">
                All books
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        @if($featuredBooks->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
                @foreach($featuredBooks as $book)
                    <x-book-card :book="$book"/>
                @endforeach
            </div>
        @else
            <div class="card p-10 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-sm">No featured books yet.</p>
            </div>
        @endif
    </section>

</div>
@endsection

@section('content')
    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 via-primary-700 to-accent-600 text-white p-12 mb-12 shadow-2xl animate-fade-in">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDE0YzMuMzEgMCA2IDIuNjkgNiA2cy0yLjY5IDYtNiA2LTYtMi42OS02LTYgMi42OS02IDYtNnpNNiAzNGMzLjMxIDAgNiAyLjY5IDYgNnMtMi42OSA2LTYgNi02LTIuNjktNi02IDIuNjktNiA2LTZ6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-30"></div>
        <div class="relative z-10 max-w-3xl">
            <h1 class="text-5xl font-bold mb-4 leading-tight">Welcome to PageTurner</h1>
            <p class="text-xl text-primary-100 mb-8 leading-relaxed">Discover your next favorite book from our extensive collection of carefully curated titles.</p>
            <a href="{{ route('books.index') }}"
               class="inline-flex items-center gap-2 bg-white text-primary-700 px-8 py-4 rounded-xl font-semibold hover:bg-primary-50 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                Browse Books
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>

    {{-- Categories Section --}}
    <section class="mb-16 animate-slide-up">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Browse by Category</h2>
            <a href="{{ route('categories.index') }}" class="text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1 group">
                View All
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}"
                   class="card-hover p-6 text-center group">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-100 to-accent-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $category->books_count }} books</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured Books Section --}}
    <section class="animate-slide-up">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Featured Books</h2>
        </div>
        @if($featuredBooks->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($featuredBooks as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>
        @else
            <div class="card p-8 text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <p class="text-gray-600">No books available yet. Check back soon!</p>
            </div>
        @endif
    </section>
@endsection