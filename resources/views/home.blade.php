@extends('layouts.app')
@section('title', 'PageTurner - Online Bookstore')

@section('content')
    {{-- Hero Section --}}
    <div class="relative bg-gradient-primary rounded-2xl overflow-hidden mb-12 shadow-xl">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-emerald-200/20 via-transparent to-transparent"></div>
        <div class="relative px-8 py-16 md:py-24">
            <div class="max-w-3xl animate-fade-in-up">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                    Discover Your Next
                    <span class="block">Great Read</span>
                </h1>
                <p class="text-xl md:text-2xl text-emerald-50 mb-8 leading-relaxed">
                    Explore thousands of books across all genres. From bestsellers to hidden gems, find your perfect story.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('books.index') }}" class="bg-white text-emerald-600 px-8 py-4 rounded-lg font-semibold hover:bg-emerald-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Browse Collection
                    </a>
                    <a href="{{ route('categories.index') }}" class="bg-white/10 backdrop-blur-sm text-white border-2 border-white/30 px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition-all">
                        View Categories
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute right-0 bottom-0 opacity-[0.08] hidden lg:block">
            <svg class="w-96 h-96 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
    </div>

    {{-- Stats Section --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 text-center group hover:-translate-y-1">
            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-emerald-200 transition-colors">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-emerald-600 mb-1">{{ \App\Models\Book::count() }}+</div>
            <div class="text-gray-600 font-medium text-sm">Books Available</div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 text-center group hover:-translate-y-1">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-indigo-200 transition-colors">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-indigo-600 mb-1">{{ \App\Models\Category::count() }}+</div>
            <div class="text-gray-600 font-medium text-sm">Categories</div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 text-center group hover:-translate-y-1">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-200 transition-colors">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-blue-600 mb-1">{{ \App\Models\User::count() }}+</div>
            <div class="text-gray-600 font-medium text-sm">Happy Readers</div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 text-center group hover:-translate-y-1">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-orange-200 transition-colors">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-orange-600 mb-1">{{ \App\Models\Review::count() }}+</div>
            <div class="text-gray-600 font-medium text-sm">Reviews</div>
        </div>
    </div>

    {{-- Categories Section --}}
    <section class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Browse by Category</h2>
                <p class="text-gray-600 mt-2">Find books in your favorite genres</p>
            </div>
            <a href="{{ route('categories.index') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center group">
                View All
                <svg class="w-5 h-5 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="group">
                    <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 text-center border-2 border-transparent hover:border-emerald-500 transform hover:-translate-y-1 h-full flex flex-col items-center justify-center">
                        <div class="text-4xl mb-3">📚</div>
                        <h3 class="font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors">{{ $category->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $category->books_count }} books</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured Books Section --}}
    <section>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Featured Books</h2>
                <p class="text-gray-600 mt-2">Handpicked selections just for you</p>
            </div>
            <a href="{{ route('books.index') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center group">
                View All
                <svg class="w-5 h-5 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        @if($featuredBooks->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($featuredBooks as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>
        @else
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-12 text-center">
                <svg class="w-20 h-20 text-blue-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <p class="text-blue-800 font-medium text-lg">No books available yet. Check back soon!</p>
            </div>
        @endif
    </section>

    {{-- CTA Section --}}
    @guest
    <section class="mt-16">
        <div class="bg-gradient-secondary rounded-2xl p-8 md:p-12 text-center text-white shadow-xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_var(--tw-gradient-stops))] from-purple-200/20 via-transparent to-transparent"></div>
            <div class="relative">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Join Our Reading Community</h2>
                <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                    Create an account to start building your personal library, write reviews, and track your orders.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-semibold hover:bg-indigo-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Get Started Free
                    </a>
                    <a href="{{ route('login') }}" class="bg-white/10 backdrop-blur-sm text-white border-2 border-white/30 px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition-all">
                        Sign In
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endguest
@endsection
