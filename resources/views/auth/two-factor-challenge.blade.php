@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Two-Factor Authentication
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please enter the 6-digit code sent to your email address
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('two-factor.verify') }}" method="POST">
            @csrf
            
            <div>
                <label for="code" class="sr-only">Verification Code</label>
                <input id="code" name="code" type="text" maxlength="6" required 
                       class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm text-center text-2xl tracking-widest"
                       placeholder="000000" 
                       autocomplete="one-time-code"
                       inputmode="numeric"
                       pattern="[0-9]{6}">
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    Verify Code
                </button>
            </div>

            <div class="flex items-center justify-between">
                <form action="{{ route('two-factor.resend') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Resend Code
                    </button>
                </form>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-500 text-sm font-medium">
                        Sign Out
                    </button>
                </form>
            </div>
        </form>

        @if(session('status'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('status') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gray-50 text-gray-500">Or use a recovery code</span>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-center text-sm text-gray-600">
                    Lost your device? You can use one of your recovery codes instead of the verification code.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('code').addEventListener('input', function(e) {
    // Only allow numbers
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Auto-submit when 6 digits are entered
    if (this.value.length === 6) {
        this.form.submit();
    }
});
</script>
@endsection