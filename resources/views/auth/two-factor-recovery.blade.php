<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <h2 class="font-heading text-2xl font-bold text-ink-900 mb-2">Recovery Code</h2>
        <p class="text-ink-400">Enter one of your backup recovery codes</p>
    </div>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('two-factor.recovery.verify') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="recovery_code" value="Recovery Code" />
            <x-text-input id="recovery_code" class="block mt-1 w-full text-center tracking-widest" type="text" name="recovery_code" required autocomplete="off" autofocus placeholder="XXXXXXXXXX" />
            <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Verify Recovery Code
        </x-primary-button>
    </form>
</x-guest-layout>
