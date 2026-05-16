<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h1 class="font-heading text-2xl font-bold text-ink-900">Welcome Back</h1>
        <p class="text-ink-400 text-sm mt-1">Sign in to your account</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center group">
                <input id="remember_me" type="checkbox" class="rounded border-parchment-300 text-gold-600 shadow-sm focus:ring-gold-500 transition-shadow" name="remember">
                <span class="ms-2 text-sm text-ink-400 group-hover:text-ink-600 transition-colors">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-ink-400 hover:text-gold-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-parchment-100 focus:ring-gold-500 transition-colors" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-6 border-t border-parchment-200 text-center">
        <p class="text-sm text-ink-400">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-gold-600 hover:text-gold-700 font-semibold transition-colors">Sign up</a>
        </p>
    </div>
</x-guest-layout>
