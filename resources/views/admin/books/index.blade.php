@extends('layouts.app')
@section('title', 'Manage Books - PageTurner')
@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-heading text-3xl font-bold text-ink-900">Manage Books</h2>
            <p class="text-ink-400 mt-1">View and manage your book catalog</p>
        </div>
        <a href="{{ route('admin.books.create') }}" class="btn-primary">
            <svg class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Book
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-parchment-200">
        <div class="p-4 border-b border-parchment-200">
            <form action="{{ route('admin.books.index') }}" method="GET" class="flex gap-4 flex-wrap">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by title, author, or ISBN..."
                           class="input-field">
                </div>
                <div class="w-48">
                    <select name="category" class="input-field">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <select name="status" class="input-field">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Filter</button>
                @if(request()->hasAny(['search', 'category', 'status']))
                    <a href="{{ route('admin.books.index') }}" class="btn-secondary">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-parchment-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Book</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">ISBN</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-parchment-200">
                    @forelse($books as $book)
                        <tr class="hover:bg-parchment-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($book->cover_image)
                                        <img src="{{ $book->cover_url }}"
                                             class="w-10 h-14 object-cover rounded" alt="">
                                    @else
                                        <div class="w-10 h-14 bg-parchment-200 rounded flex items-center justify-center">
                                            <svg class="w-5 h-5 text-parchment-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('books.show', $book) }}" class="font-medium text-ink-900 hover:text-gold-600">
                                            {{ $book->title }}
                                        </a>
                                        <p class="text-sm text-ink-400">{{ $book->author }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-600">{{ $book->category->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-ink-400 font-mono">{{ $book->isbn }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-ink-900">₱{{ number_format($book->price, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($book->stock_quantity > 10)
                                    <span class="text-emerald-600 font-medium">{{ $book->stock_quantity }}</span>
                                @elseif($book->stock_quantity > 0)
                                    <span class="text-amber-600 font-medium">{{ $book->stock_quantity }}</span>
                                @else
                                    <span class="text-red-600 font-medium">Out of stock</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($book->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.books.edit', $book) }}"
                                       class="p-2 text-ink-400 hover:text-gold-600 hover:bg-gold-50 rounded-lg transition-colors"
                                       title="Edit book">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this book?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-ink-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete book">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-parchment-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="text-ink-400">No books found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($books->hasPages())
            <div class="p-4 border-t border-parchment-200">
                {{ $books->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
