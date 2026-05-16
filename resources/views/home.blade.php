@extends('layouts.app')
@section('title', 'PageTurner - Online Bookstore')

@section('content')
    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-3xl mb-14 shadow-2xl min-h-[560px] bg-ink-900 group">
        @if($heroBook?->cover_image)
            <img src="{{ $heroBook->cover_url }}" alt="{{ $heroBook->title }}" class="absolute inset-0 h-full w-full object-cover opacity-30 group-hover:scale-105 transition-transform duration-700">
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-ink-950/80 via-ink-950/60 to-ink-950/40"></div>
        <div class="absolute inset-0 bg-dots-pattern opacity-20"></div>
        <div class="relative px-8 py-16 md:py-24 lg:py-28">
            <div class="max-w-3xl">
                <span class="inline-flex items-center px-4 py-1.5 rounded-lg text-sm font-medium bg-gold-500/20 text-gold-300 border border-gold-500/30 mb-6 animate-fade-in">Welcome to PageTurner</span>
                <h1 class="font-heading text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight animate-fade-in-up">
                    Discover Your Next
                    <span class="gradient-text-gold block">Great Read</span>
                </h1>
                <p class="text-lg md:text-xl text-parchment-300 mb-10 leading-relaxed max-w-2xl animate-fade-in-up stagger-1">
                    Explore thousands of books across all genres. From bestsellers to hidden gems, find your perfect story amidst our curated collection.
                </p>
                <div class="flex flex-wrap gap-4 animate-fade-in-up stagger-2">
                    <a href="{{ route('books.index') }}" class="bg-gradient-warm hover:opacity-90 text-white px-8 py-4 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Browse Collection
                    </a>
                    <a href="{{ route('categories.index') }}" class="bg-white/10 backdrop-blur-sm text-parchment-100 border border-parchment-400/30 px-8 py-4 rounded-xl font-semibold hover:bg-white/20 transition-all">
                        View Categories
                    </a>
                </div>
            </div>
        </div>
        {{-- Scroll indicator --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 animate-scroll-indicator">
            <svg class="w-6 h-6 text-parchment-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </div>

    {{-- Stats Section --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-14 reveal-on-scroll">
        <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 text-center group hover:-translate-y-1 border border-parchment-200">
            <div class="w-12 h-12 bg-gold-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-gold-200 transition-colors group-hover:scale-110 transition-all duration-300">
                <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div class="font-heading text-3xl font-bold text-gold-700 mb-1">{{ $stats['books'] }}+</div>
            <div class="text-ink-400 font-medium text-sm">Books Available</div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 text-center group hover:-translate-y-1 border border-parchment-200">
            <div class="w-12 h-12 bg-ink-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-ink-200 transition-colors group-hover:scale-110 transition-all duration-300">
                <svg class="w-6 h-6 text-ink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div class="font-heading text-3xl font-bold text-ink-700 mb-1">{{ $stats['categories'] }}+</div>
            <div class="text-ink-400 font-medium text-sm">Categories</div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 text-center group hover:-translate-y-1 border border-parchment-200">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-200 transition-colors group-hover:scale-110 transition-all duration-300">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div class="font-heading text-3xl font-bold text-blue-700 mb-1">{{ $stats['readers'] }}+</div>
            <div class="text-ink-400 font-medium text-sm">Happy Readers</div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 text-center group hover:-translate-y-1 border border-parchment-200">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-amber-200 transition-colors group-hover:scale-110 transition-all duration-300">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <div class="font-heading text-3xl font-bold text-amber-700 mb-1">{{ $stats['reviews'] }}+</div>
            <div class="text-ink-400 font-medium text-sm">Reviews</div>
        </div>
    </div>

    {{-- Categories Section --}}
    <section class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <div class="reveal-on-scroll">
                <h2 class="section-heading">Browse by Category</h2>
                <p class="section-subheading">Find books in your favorite genres</p>
            </div>
            <a href="{{ route('categories.index') }}" class="text-gold-600 hover:text-gold-700 font-semibold inline-flex items-center group">
                View All
                <svg class="w-5 h-5 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="group reveal-on-scroll">
                    <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 text-center border border-parchment-200 hover:border-gold-400 transform hover:-translate-y-1.5 hover:scale-[1.02] h-full flex flex-col items-center justify-center">
                        <div class="w-14 h-14 bg-gradient-to-br from-parchment-100 to-parchment-200 rounded-xl flex items-center justify-center mb-3 group-hover:bg-gradient-to-br group-hover:from-gold-100 group-hover:to-gold-200 transition-all duration-300">
                            <svg class="w-7 h-7 text-gold-600 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="font-heading font-semibold text-ink-800 group-hover:text-gold-700 transition-colors">{{ $category->name }}</h3>
                        <p class="text-sm text-ink-400 mt-1">{{ $category->books_count }} books</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured Books Section --}}
    <section class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <div class="reveal-on-scroll">
                <h2 class="section-heading">Featured Books</h2>
                <p class="section-subheading">Handpicked selections just for you</p>
            </div>
            <a href="{{ route('books.index') }}" class="text-gold-600 hover:text-gold-700 font-semibold inline-flex items-center group">
                View All
                <svg class="w-5 h-5 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        @if($featuredBooks->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($featuredBooks as $book)
                    <div class="reveal-on-scroll">
                        <x-book-card :book="$book" />
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-parchment-200 rounded-xl p-12 text-center reveal-on-scroll">
                <div class="w-20 h-20 bg-parchment-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-parchment-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <p class="text-ink-500 font-heading font-medium text-lg">No books available yet. Check back soon!</p>
            </div>
        @endif
    </section>

    {{-- Testimonial Section (dummy) --}}
    <section class="mb-16">
        <div class="text-center mb-10 reveal-on-scroll">
            <h2 class="section-heading">What Our Readers Say</h2>
            <p class="section-subheading">Join thousands of satisfied book lovers</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200 reveal-on-scroll">
                <div class="flex items-center space-x-1 mb-3">
                    @for($i = 0; $i < 5; $i++)<svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-ink-600 text-sm leading-relaxed mb-4">"PageTurner has the best collection I've ever seen. Found so many hidden gems I would have never discovered otherwise."</p>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gold-200 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-gold-700">MC</span></div>
                    <div><p class="text-sm font-medium text-ink-800">Maria C.</p><p class="text-xs text-ink-400">Avid Reader</p></div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200 reveal-on-scroll stagger-1">
                <div class="flex items-center space-x-1 mb-3">
                    @for($i = 0; $i < 5; $i++)<svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-ink-600 text-sm leading-relaxed mb-4">"Fast delivery and great prices. The book quality exceeded my expectations. Will definitely be ordering again!"</p>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-200 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-blue-700">JD</span></div>
                    <div><p class="text-sm font-medium text-ink-800">Juan D.</p><p class="text-xs text-ink-400">Book Collector</p></div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-parchment-200 reveal-on-scroll stagger-2">
                <div class="flex items-center space-x-1 mb-3">
                    @for($i = 0; $i < 5; $i++)<svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-ink-600 text-sm leading-relaxed mb-4">"The curated collections are amazing! I've discovered so many new authors. PageTurner is my go-to bookstore now."</p>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-amber-200 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-amber-700">AR</span></div>
                    <div><p class="text-sm font-medium text-ink-800">Ana R.</p><p class="text-xs text-ink-400">Literature Student</p></div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    @guest
    <section class="mt-8 mb-4 reveal-on-scroll">
        <div class="bg-gradient-secondary rounded-2xl p-8 md:p-12 text-center shadow-xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_var(--tw-gradient-stops))] from-gold-500/10 via-transparent to-transparent"></div>
            <div class="absolute inset-0 bg-dots-pattern opacity-20"></div>
            <div class="relative">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-white mb-4">Join Our Reading Community</h2>
                <p class="text-lg text-parchment-300 mb-8 max-w-2xl mx-auto">
                    Create an account to start building your personal library, write reviews, and track your orders.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-gradient-warm hover:opacity-90 text-white px-8 py-4 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Get Started Free
                    </a>
                    <a href="{{ route('login') }}" class="bg-white/10 backdrop-blur-sm text-parchment-100 border border-parchment-400/30 px-8 py-4 rounded-xl font-semibold hover:bg-white/20 transition-all">
                        Sign In
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endguest
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));
});
</script>
@endpush
