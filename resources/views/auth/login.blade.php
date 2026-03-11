<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Welcome Back</h2>
        <p class="text-gray-400 text-sm mt-1">Sign in to your account to continue</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded bg-slate-700 border-slate-600 text-blue-600 shadow-sm focus:ring-blue-500 focus:ring-offset-slate-800" name="remember">
                <span class="ms-2 text-sm text-gray-300">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-400 hover:text-blue-300 transition" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <div class="space-y-3">
            <x-primary-button class="w-full justify-center">
                {{ __('Sign In') }}
            </x-primary-button>

            <div class="text-center text-sm text-gray-400">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-medium transition">
                    Create one
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
