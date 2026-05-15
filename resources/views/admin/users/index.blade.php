@extends('layouts.app')
@section('title', 'Manage Users - PageTurner')
@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-heading text-3xl font-bold text-ink-900">Manage Users</h2>
            <p class="text-ink-400 mt-1">View and manage all registered users</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Add User
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-parchment-200">
        <div class="p-4 border-b border-parchment-200">
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name or email..."
                           class="input-field">
                </div>
                <div class="w-48">
                    <select name="role" class="input-field">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="premium" {{ request('role') === 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Filter</button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-parchment-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Verified</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">2FA</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-xs font-semibold text-ink-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-parchment-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-parchment-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-gold-100 flex items-center justify-center">
                                        <span class="text-sm font-bold text-gold-700">{{ substr($user->name, 0, 2) }}</span>
                                    </div>
                                    <span class="font-medium text-ink-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="badge
                                    @if($user->role === 'admin') badge-danger
                                    @elseif($user->role === 'premium') badge-warning
                                    @else badge-info @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->email_verified_at)
                                    <span class="badge badge-success">Verified</span>
                                @else
                                    <span class="badge badge-danger">Unverified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($user->two_factor_enabled)
                                    <span class="text-emerald-600 font-medium">Enabled</span>
                                @else
                                    <span class="text-ink-400">Disabled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-400">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="p-2 text-ink-400 hover:text-gold-600 hover:bg-gold-50 rounded-lg transition-colors"
                                       title="Edit user">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-ink-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete user">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-ink-400">No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-parchment-200">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
