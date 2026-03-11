@extends('layouts.app')

@section('title', 'Two-Factor Recovery Codes - PageTurner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.414-4.414a2 2 0 00-2.828 0L9 10.172 6.414 7.586a2 2 0 00-2.828 2.828l4 4a2 2 0 002.828 0l10-10a2 2 0 00-2.828-2.828z"></path>
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-900">Two-Factor Authentication Recovery Codes</h1>
                </div>
            </div>

            <div class="p-6">
                @if(session('status'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
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

                <div class="mb-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Important Security Information</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Store these recovery codes in a secure location</li>
                                        <li>Each code can only be used once</li>
                                        <li>Use these codes if you lose access to your email</li>
                                        <li>Generate new codes if you suspect they've been compromised</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($recoveryCodes))
                    <div class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Your Recovery Codes</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($recoveryCodes as $code)
                                    <div class="bg-white border border-gray-200 rounded px-4 py-3 font-mono text-sm text-center">
                                        {{ $code }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="mt-4 flex flex-wrap gap-3">
                            <button onclick="downloadCodes()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Codes
                            </button>
                            
                            <button onclick="printCodes()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print Codes
                            </button>
                            
                            <button onclick="document.getElementById('regenerate-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-yellow-300 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Regenerate Codes
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <p class="text-gray-500 mb-4">No recovery codes available</p>
                        <p class="text-sm text-gray-400">Two-factor authentication must be enabled to generate recovery codes.</p>
                    </div>
                @endif

                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Profile
                    </a>
                    
                    @if(!empty($recoveryCodes))
                        <p class="text-sm text-gray-500">{{ count($recoveryCodes) }} codes remaining</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Regenerate Codes Modal --}}
<div id="regenerate-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Regenerate Recovery Codes</h3>
                <button onclick="document.getElementById('regenerate-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('two-factor.recovery-codes.regenerate') }}">
                @csrf
                <div class="mb-4">
                    <p class="text-sm text-red-600 mb-4">
                        <strong>Warning:</strong> Regenerating recovery codes will invalidate all existing codes. Make sure to save the new codes.
                    </p>
                    
                    <label for="regenerate-password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="password" id="regenerate-password" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('regenerate-modal').classList.add('hidden')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700">
                        Regenerate Codes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function downloadCodes() {
    const codes = @json($recoveryCodes ?? []);
    const content = 'PageTurner Bookstore - Two-Factor Authentication Recovery Codes\n' +
                   'Generated: ' + new Date().toLocaleString() + '\n\n' +
                   'IMPORTANT: Store these codes securely. Each code can only be used once.\n\n' +
                   codes.join('\n') + '\n\n' +
                   'If you lose access to your email, you can use these codes to log in.';
    
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'pageturner-recovery-codes.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function printCodes() {
    const codes = @json($recoveryCodes ?? []);
    const printContent = `
        <html>
        <head>
            <title>PageTurner - Recovery Codes</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { text-align: center; margin-bottom: 30px; }
                .warning { background: #fef3cd; border: 1px solid #fecaca; padding: 15px; margin: 20px 0; border-radius: 5px; }
                .codes { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 20px 0; }
                .code { border: 1px solid #ccc; padding: 10px; text-align: center; font-family: monospace; background: #f9f9f9; }
                .footer { margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>PageTurner Bookstore</h1>
                <h2>Two-Factor Authentication Recovery Codes</h2>
                <p>Generated: ${new Date().toLocaleString()}</p>
            </div>
            
            <div class="warning">
                <strong>IMPORTANT SECURITY INFORMATION:</strong>
                <ul>
                    <li>Store these codes in a secure location</li>
                    <li>Each code can only be used once</li>
                    <li>Use these codes if you lose access to your email</li>
                    <li>Generate new codes if you suspect they've been compromised</li>
                </ul>
            </div>
            
            <div class="codes">
                ${codes.map(code => `<div class="code">${code}</div>`).join('')}
            </div>
            
            <div class="footer">
                <p>Keep this document secure and do not share these codes with anyone.</p>
            </div>
        </body>
        </html>
    `;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endsection