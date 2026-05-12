@extends('layouts.app')
@section('title', 'Categories - PageTurner')
@section('header')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
            <p class="text-gray-600 mt-2">Browse books by genre</p>
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-emerald-700 font-bold text-lg">📚</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 truncate">{{ $category->name }}</h3>
                        </div>
                        <p class="text-gray-500 text-sm">{{ $category->books_count }} {{ Str::plural('book', $category->books_count) }}</p>
                        @if($category->description)
                            <p class="text-gray-600 mt-2 text-sm leading-relaxed">{{ Str::limit($category->description, 100) }}</p>
                        @endif
                    </div>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="flex space-x-1 ml-4 flex-shrink-0">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                      onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
                <a href="{{ route('categories.show', $category) }}"
                   class="mt-4 block w-full text-center btn-secondary">
                    Browse Books
                </a>
            </div>
        @empty
            <div class="col-span-full">
                <x-alert type="info">No categories found.</x-alert>
            </div>
        @endforelse
    </div>
    <div class="mt-8">{{ $categories->links() }}</div>
@endsection
