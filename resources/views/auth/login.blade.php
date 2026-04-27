<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Welcome back</h1>
        <p class="text-gray-500 text-sm mt-1">Sign in to your PageTurner account</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="input-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="input-field @error('email') border-red-400 @enderror"
                   placeholder="you@example.com" required autofocus autocomplete="username"/>
            @error('email')<p class="input-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="input-label mb-0">Password</label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                        Forgot password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password"
                   class="input-field @error('password') border-red-400 @enderror"
                   placeholder="••••••••" required autocomplete="current-password"/>
            @error('password')<p class="input-error">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <label for="remember_me" class="text-sm text-gray-600">Keep me signed in</label>
        </div>

        <button type="submit" class="btn-primary w-full btn-lg">
            Sign In
        </button>

        <p class="text-center text-sm text-gray-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 font-semibold">Create one</a>
        </p>
    </form>
</x-guest-layout>
