@extends('layouts.app')
@section('title', 'Profile - PageTurner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto space-y-6">
        <h1 class="text-3xl font-bold mb-6">Profile Settings</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Profile Information --}}
            <div class="p-6 bg-white shadow rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Profile Information</h2>
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Security Settings --}}
            <div class="p-6 bg-white shadow rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Security Settings</h2>
                @include('profile.partials.security-settings')
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Password Update --}}
            <div class="p-6 bg-white shadow rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Update Password</h2>
                @include('profile.partials.update-password-form')
            </div>

            {{-- Account Deletion --}}
            <div class="p-6 bg-white shadow rounded-lg">
                <h2 class="text-xl font-semibold mb-4 text-red-600">Danger Zone</h2>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
