@extends('layouts.app')
@section('title', 'Edit Book - PageTurner')
@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Edit Book</h1>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Title *</label>
                <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Author *</label>
                <input type="text" name="author" value="{{ old('author', $book->author) }}" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Publication Year</label>
                <input type="number" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}"
                       min="1000" max="{{ date('Y') + 1 }}" placeholder="e.g., {{ date('Y') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Category *</label>
                <select name="category_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">ISBN (Auto-generated)</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}" readonly
                           class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed">
                    <p class="text-gray-500 text-xs mt-1">ISBN cannot be changed</p>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Price (₱) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $book->price) }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Stock Quantity *</label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $book->stock_quantity) }}" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $book->description) }}</textarea>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Cover Image</label>
                @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                         class="h-24 mb-2 rounded" alt="Current cover">
                    <p class="text-sm text-gray-500 mb-2">Upload a new image to replace current one</p>
                @endif
                <input type="file" name="cover_image" accept="image/*"
                       class="w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="flex justify-end space-x-4">
                <a href="{{ route('books.show', $book) }}"
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 transition">Cancel</a>
                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">Update Book</button>
            </div>
        </form>
    </div>
</div>
@endsection