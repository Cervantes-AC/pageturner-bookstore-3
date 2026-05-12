@extends('layouts.app')
@section('title', 'Edit Book - PageTurner')
@section('header')
    <h1 class="font-heading text-3xl font-bold text-ink-900">Edit Book</h1>
    <p class="text-ink-400 mt-2">Update book details</p>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8">
        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-ink-700 font-medium mb-2">Title *</label>
                    <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                           class="input-field @error('title') border-red-500 @enderror">
                    @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Author *</label>
                    <input type="text" name="author" value="{{ old('author', $book->author) }}" required
                           class="input-field @error('author') border-red-500 @enderror">
                    @error('author')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Publication Year</label>
                    <input type="number" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}"
                           min="1000" max="{{ date('Y') + 1 }}" placeholder="e.g., {{ date('Y') }}"
                           class="input-field">
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Category *</label>
                    <select name="category_id" required class="input-field">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">ISBN</label>
                    <input type="text" value="{{ $book->isbn }}" readonly
                           class="input-field bg-parchment-100 cursor-not-allowed">
                    <p class="text-ink-400 text-xs mt-1">ISBN cannot be changed</p>
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Price (&amp;#x20B1;) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $book->price) }}" required
                           class="input-field @error('price') border-red-500 @enderror">
                    @error('price')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $book->stock_quantity) }}" required
                           class="input-field @error('stock_quantity') border-red-500 @enderror">
                    @error('stock_quantity')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-ink-700 font-medium mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="input-field">{{ old('description', $book->description) }}</textarea>
            </div>

            <div class="mt-6">
                <label class="block text-ink-700 font-medium mb-2">Cover Image</label>
                @if($book->cover_image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $book->cover_image) }}"
                             class="h-32 object-cover rounded-xl shadow-sm" alt="Current cover">
                        <p class="text-sm text-ink-400 mt-2">Upload a new image to replace the current one</p>
                    </div>
                @endif
                <input type="file" name="cover_image" accept="image/*"
                       class="block w-full text-sm text-ink-400 file:mr-4 file:py-2.5 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gold-100 file:text-gold-700 hover:file:bg-gold-200 transition-colors">
                @error('cover_image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-parchment-200">
                <a href="{{ route('books.show', $book) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update Book</button>
            </div>
        </form>
    </div>
</div>
@endsection
