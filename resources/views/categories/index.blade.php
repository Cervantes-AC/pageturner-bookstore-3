@extends('layouts.app')
@section('title', 'Categories - PageTurner')
@section('header')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="font-heading text-4xl font-bold text-ink-900">Categories</h1>
            <p class="text-ink-400 mt-2">Browse books by genre</p>
        </div>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.categories.create') }}" class="btn-primary inline-flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Category</span>
                </a>
            @endif
        @endauth
    </div>
@endsection

@section('content')
    <div class="bg-white border border-parchment-200 rounded-lg shadow-sm p-5 mb-8">
        <form action="{{ route('categories.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-[1fr_220px_auto] gap-4 items-end">
            <div>
                <label class="block text-ink-700 text-sm font-semibold mb-2">Search Categories</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="input-field pl-11" placeholder="Genre or description">
                    <svg class="w-5 h-5 text-parchment-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div>
                <label class="block text-ink-700 text-sm font-semibold mb-2">Sort</label>
                <select name="sort" class="input-field">
                    <option value="">Name: A-Z</option>
                    <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                    <option value="books_desc" {{ request('sort') === 'books_desc' ? 'selected' : '' }}>Most Books</option>
                    <option value="books_asc" {{ request('sort') === 'books_asc' ? 'selected' : '' }}>Fewest Books</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Apply</button>
                @if(request()->hasAny(['search', 'sort']))
                    <a href="{{ route('categories.index') }}" class="btn-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
            <div class="bg-white rounded-lg shadow-sm border border-parchment-200 p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="font-heading text-xl font-semibold text-ink-800 truncate">{{ $category->name }}</h3>
                        </div>
                        <p class="text-ink-400 text-sm">{{ $category->books_count }} {{ Str::plural('book', $category->books_count) }}</p>
                        @if($category->description)
                            <p class="text-ink-400 mt-2 text-sm leading-relaxed">{{ Str::limit($category->description, 100) }}</p>
                        @endif
                    </div>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="flex space-x-1 ml-4 flex-shrink-0">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="p-2 text-parchment-400 hover:text-gold-600 hover:bg-gold-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                      onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-parchment-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
                <div class="mt-5 flex gap-3">
                    <a href="{{ route('categories.show', $category) }}"
                       class="flex-1 text-center btn-primary">
                        Browse Books
                    </a>
                    <a href="{{ route('books.index', ['category' => $category->id]) }}"
                       class="btn-secondary px-4" title="Open in catalog">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white border border-parchment-200 rounded-lg p-10 text-center">
                    <p class="font-heading text-xl font-semibold text-ink-900">No categories found</p>
                    <p class="text-ink-400 mt-2">Try a different search term.</p>
                </div>
            </div>
        @endforelse
    </div>
    <div class="mt-8">{{ $categories->withQueryString()->links() }}</div>
@endsection
