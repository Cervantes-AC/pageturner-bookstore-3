@extends('layouts.app')
@section('title', 'Add New Book - PageTurner')

@section('header')
<h1 class="text-3xl font-bold text-gray-900">Add New Book</h1>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">

        <form action="{{ route('admin.books.store') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <!-- Title -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Title *</label>

                <input type="text"
                       name="title"
                       value="{{ old('title') }}"
                       required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">

                @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Author -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Author *</label>

                <input type="text"
                       name="author"
                       value="{{ old('author') }}"
                       required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">

                @error('author')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Publication Year -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Publication Year</label>

                <input type="number"
                       name="publication_year"
                       value="{{ old('publication_year') }}"
                       min="1000"
                       max="{{ date('Y') + 1 }}"
                       placeholder="e.g., {{ date('Y') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">

                @error('publication_year')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Category *</label>

                <select name="category_id"
                        required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">

                    <option value="">Select Category</option>

                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach

                </select>

                @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- ISBN Info -->
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                <p class="text-sm text-blue-800">
                    <strong>📚 Note:</strong> ISBN will be automatically generated when you create the book.
                </p>
            </div>

            <!-- Price + Stock -->
            <div class="grid grid-cols-2 gap-4 mb-4">

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Price (₱) *</label>

                    <input type="number"
                           step="0.01"
                           name="price"
                           value="{{ old('price') }}"
                           required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">

                    @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Stock Quantity *</label>

                    <input type="number"
                           name="stock_quantity"
                           value="{{ old('stock_quantity', 0) }}"
                           required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">

                    @error('stock_quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Description</label>

                <textarea name="description"
                          rows="4"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <!-- Image Picker With Preview -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Cover Image</label>

                <div class="mb-3 flex justify-center">
                    <img id="coverPreview"
                         src="https://via.placeholder.com/150x200?text=Cover"
                         class="h-40 object-cover rounded border shadow">
                </div>

                <input type="file"
                       name="cover_image"
                       accept="image/*"
                       id="coverImageInput"
                       class="w-full border-gray-300 rounded-md shadow-sm">

                @error('cover_image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">

                <a href="{{ route('books.index') }}"
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 transition">
                    Cancel
                </a>

                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
                    Add Book
                </button>

            </div>

        </form>
    </div>
</div>

<!-- Image Preview Script -->
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