@extends('layouts.app')
@section('title', 'Add User - PageTurner')
@section('header')
    <h2 class="font-heading text-3xl font-bold text-ink-900">Add User</h2>
    <p class="text-ink-400 mt-1">Create a new user account</p>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-ink-700 font-medium mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="input-field @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="input-field @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Password *</label>
                    <input type="password" name="password" required
                           class="input-field @error('password') border-red-500 @enderror">
                    @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="input-field">
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Role *</label>
                    <select name="role" required class="input-field @error('role') border-red-500 @enderror">
                        <option value="">Select Role</option>
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="premium" {{ old('role') === 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-parchment-200">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>
@endsection
