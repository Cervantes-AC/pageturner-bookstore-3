<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-16 h-16 bg-gold-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h2 class="font-heading text-2xl font-bold text-ink-900 mb-2">Two-Factor Authentication</h2>
        <p class="text-ink-400">Enter the code sent to your email</p>
    </div>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="code" value="Authentication Code" />
            <x-text-input id="code" class="block mt-1 w-full text-center text-2xl tracking-widest" type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autocomplete="off" autofocus placeholder="000000" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Verify Code
        </x-primary-button>
    </form>

    <div class="mt-6 flex items-center justify-between">
        <form method="POST" action="{{ route('two-factor.resend') }}">
            @csrf
            <button type="submit" class="text-sm text-gold-600 hover:text-gold-700 underline">
                Resend Code
            </button>
        </form>

        <a href="{{ route('two-factor.recovery') }}" class="text-sm text-ink-400 hover:text-ink-600 underline">
            Use Recovery Code
        </a>
    </div>
</x-guest-layout>
