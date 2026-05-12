@extends('layouts.app')
@section('title', 'Edit Category - PageTurner')
@section('header')
    <h1 class="font-heading text-3xl font-bold text-ink-900">Edit Category</h1>
    <p class="text-ink-400 mt-2">Update category details</p>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-6">
                <label class="block text-ink-700 font-medium mb-2">Name *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                       class="input-field @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-ink-700 font-medium mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="input-field">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t border-parchment-200">
                <a href="{{ route('categories.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update Category</button>
            </div>
        </form>
    </div>
</div>
@endsection
