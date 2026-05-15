@extends('layouts.app')
@section('title', 'Two-Factor Authentication - PageTurner')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center space-x-4 mb-2">
        <div class="w-14 h-14 {{ $user->two_factor_enabled ? 'bg-emerald-100' : 'bg-gold-100' }} rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7 {{ $user->two_factor_enabled ? 'text-emerald-600' : 'text-gold-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <div>
            <h1 class="font-heading text-3xl font-bold text-ink-900">Two-Factor Authentication</h1>
            <p class="text-ink-400">Manage your account security</p>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 text-sm">{{ session('info') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8">
        @if ($user->two_factor_enabled)
            <div class="flex items-center space-x-3 mb-6">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Enabled
                </span>
            </div>

            <h3 class="font-heading text-lg font-semibold text-ink-900 mb-3">Recovery Codes</h3>
            <p class="text-sm text-ink-400 mb-4">Save these codes somewhere safe. Each code can only be used once.</p>

            @if(count($recoveryCodes) > 0)
                <div class="bg-parchment-100 rounded-lg p-4 mb-6 font-mono text-sm space-y-1">
                    @foreach($recoveryCodes as $code)
                        <div class="text-ink-700">{{ $code }}</div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-amber-600 mb-4">No recovery codes remaining. Disable and re-enable 2FA to generate new codes.</p>
            @endif

            <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-6 pt-6 border-t border-parchment-200" onsubmit="return confirm('Are you sure you want to disable two-factor authentication?')">
                @csrf
                <div class="space-y-4">
                    <div>
                        <x-input-label for="current_password" value="Current Password" />
                        <x-text-input id="current_password" class="block mt-1 w-full" type="password" name="current_password" required autocomplete="current-password" />
                    </div>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors text-sm">
                        Disable Two-Factor Authentication
                    </button>
                </div>
            </form>
        @else
            <div class="text-center">
                <div class="w-20 h-20 bg-gold-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="font-heading text-xl font-semibold text-ink-900 mb-2">Protect Your Account</h3>
                <p class="text-ink-400 mb-6 max-w-md mx-auto">Two-factor authentication adds an extra layer of security by requiring a one-time code from your email when logging in.</p>

                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <x-primary-button class="px-6 py-3">
                        Enable Two-Factor Authentication
                    </x-primary-button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
