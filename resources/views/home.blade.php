@extends('layouts.app')
@section('title', 'PageTurner — Professional Bookstore')
@section('meta_description', 'Browse thousands of books across every genre at PageTurner. From bestsellers to hidden gems.')

@section('content')
{{-- Hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950">
    <div class="absolute inset-0 opacity-[0.05]"
         style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>
    <div class="absolute top-20 -left-20 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl animate-float"></div>
    <div class="absolute -bottom-10 right-20 w-80 h-80 bg-accent-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-primary-200 text-xs font-semibold mb-6 border border-white/10 backdrop-blur-sm animate-fade-in-down">
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse-soft"></span>
                Professional Bookstore Platform
            </span>
            <h1 class="text-4xl lg:text-6xl font-extrabold text-white leading-tight mb-5 tracking-tight animate-fade-in-up">
                Discover your next<br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary-300 to-accent-300">great read</span>
            </h1>
            <p class="text-lg text-primary-200 mb-8 leading-relaxed max-w-xl animate-fade-in-up" style="animation-delay: 100ms;">
                Browse thousands of titles across every genre. From bestsellers to hidden gems — find exactly what you're looking for.
            </p>
            <div class="flex flex-wrap gap-3 animate-fade-in-up" style="animation-delay: 200ms;">
                <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-white text-primary-700 font-semibold rounded-xl hover:bg-primary-50 hover:-translate-y-0.5 transition-all duration-300 shadow-xl shadow-primary-900/20 text-base">
                    Browse Books
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 border-2 border-white/20 text-white font-semibold rounded-xl hover:bg-white/10 hover:border-white/30 transition-all duration-300 text-base">
                    View Categories
                </a>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-20">

    {{-- Categories --}}
    <section class="reveal-up">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Browse by Category</h2>
                <p class="text-sm text-gray-500 mt-1">Find books in your favourite genre</p>
            </div>
            <a href="{{ route('categories.index') }}"
               class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1 transition-colors">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($categories as $category)
            <a href="{{ route('categories.show', $category) }}"
               class="group card-hover p-5 text-center">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:from-primary-100 group-hover:to-primary-200 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors leading-tight">{{ $category->name }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $category->books_count }} {{ Str::plural('book', $category->books_count) }}</p>
            </a>
            @endforeach
        </div>
    </section>

    {{-- Featured Books --}}
    <section class="reveal-up">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Featured Books</h2>
                <p class="text-sm text-gray-500 mt-1">Hand-picked titles worth reading</p>
            </div>
            <a href="{{ route('books.index') }}"
               class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1 transition-colors">
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
            <div class="card py-16 text-center">
                <div class="empty-state">
                    <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="empty-state-title">No featured books yet</p>
                    <p class="empty-state-text">Check back soon for our hand-picked selection.</p>
                </div>
            </div>
        @endif
    </section>

</div>
@endsection
