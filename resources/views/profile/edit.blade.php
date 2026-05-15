@extends('layouts.app')
@section('title', 'Profile - PageTurner')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center space-x-4 mb-2">
        <div class="w-14 h-14 bg-gold-100 rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h1 class="font-heading text-3xl font-bold text-ink-900">Profile</h1>
            <p class="text-ink-400">Manage your account settings</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8">
        <div class="max-w-xl">
            <section>
                <header>
                    <h2 class="text-lg font-heading font-medium text-ink-900">{{ __('Two-Factor Authentication') }}</h2>
                    <p class="mt-1 text-sm text-ink-400">{{ __('Add an extra layer of security to your account.') }}</p>
                </header>
                <div class="mt-6">
                    <a href="{{ route('two-factor.setup') }}" class="inline-flex items-center px-4 py-2 bg-gold-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gold-700 focus:outline-none transition-colors">
                        {{ auth()->user()->two_factor_enabled ? 'Manage 2FA' : 'Enable 2FA' }}
                    </a>
                </div>
            </section>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-parchment-200 p-8">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
