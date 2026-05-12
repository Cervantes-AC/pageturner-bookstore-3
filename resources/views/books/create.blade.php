@extends('layouts.app')
@section('title', 'Add New Book - PageTurner')

@section('header')
<h1 class="text-3xl font-bold text-gray-900">Add New Book</h1>
<p class="text-gray-600 mt-2">Add a new book to your collection</p>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('admin.books.store') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="input-field @error('title') border-red-500 @enderror">
                    @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Author *</label>
                    <input type="text" name="author" value="{{ old('author') }}" required
                           class="input-field @error('author') border-red-500 @enderror">
                    @error('author')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Publication Year</label>
                    <input type="number" name="publication_year" value="{{ old('publication_year') }}"
                           min="1000" max="{{ date('Y') + 1 }}" placeholder="e.g., {{ date('Y') }}"
                           class="input-field @error('publication_year') border-red-500 @enderror">
                    @error('publication_year')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Category *</label>
                    <select name="category_id" required
                            class="input-field @error('category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Price (₱) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required
                           class="input-field @error('price') border-red-500 @enderror">
                    @error('price')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required
                           class="input-field @error('stock_quantity') border-red-500 @enderror">
                    @error('stock_quantity')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <p class="text-sm text-blue-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>ISBN will be automatically generated when you create the book.</span>
                </p>
            </div>

            <div class="mt-6">
                <label class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="input-field">{{ old('description') }}</textarea>
            </div>

            <div class="mt-6">
                <label class="block text-gray-700 font-medium mb-2">Cover Image</label>
                <div class="mb-4">
                    <img id="coverPreview"
                         src="https://via.placeholder.com/150x200?text=Cover"
                         class="h-40 object-cover rounded-xl border shadow-sm">
                </div>
                <input type="file" name="cover_image" accept="image/*" id="coverImageInput"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-colors">
                @error('cover_image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('books.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Add Book</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('coverImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('coverPreview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
