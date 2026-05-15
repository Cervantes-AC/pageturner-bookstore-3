@extends('layouts.app')
@section('title', 'Edit User - PageTurner')
@section('header')
    <h2 class="font-heading text-3xl font-bold text-ink-900">Edit User</h2>
    <p class="text-ink-400 mt-1">Update user details</p>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-ink-700 font-medium mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="input-field @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="input-field @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">New Password</label>
                    <input type="password" name="password"
                           class="input-field @error('password') border-red-500 @enderror"
                           placeholder="Leave blank to keep current">
                    @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="input-field"
                           placeholder="Leave blank to keep current">
                </div>

                <div>
                    <label class="block text-ink-700 font-medium mb-2">Role *</label>
                    <select name="role" required class="input-field @error('role') border-red-500 @enderror">
                        <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="premium" {{ old('role', $user->role) === 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-parchment-200">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection
