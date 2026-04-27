<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create your account</h1>
        <p class="text-gray-500 text-sm mt-1">Join PageTurner and start your reading journey</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="input-label">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="input-field @error('name') border-red-400 @enderror"
                   placeholder="John Doe" required autofocus autocomplete="name"/>
            @error('name')<p class="input-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="input-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="input-field @error('email') border-red-400 @enderror"
                   placeholder="you@example.com" required autocomplete="username"/>
            @error('email')<p class="input-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="input-label">Password</label>
            <input id="password" type="password" name="password"
                   class="input-field @error('password') border-red-400 @enderror"
                   placeholder="Min. 8 characters" required autocomplete="new-password"/>
            @error('password')<p class="input-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="input-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="input-field" placeholder="••••••••" required autocomplete="new-password"/>
        </div>

        <button type="submit" class="btn-primary w-full btn-lg">
            Create Account
        </button>

        <p class="text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-semibold">Sign in</a>
        </p>
    </form>
</x-guest-layout>
