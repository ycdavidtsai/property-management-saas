{{-- resources/views/vendor-setup/expired.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-b from-red-50 to-white flex flex-col items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            {{-- Icon --}}
            <div class="w-24 h-24 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            {{-- Message --}}
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Link Expired or Invalid</h1>
            <p class="text-gray-600 mb-8">{{ $message ?? 'This invitation link has expired or is no longer valid.' }}</p>

            {{-- Help --}}
            <div class="bg-gray-50 rounded-xl p-6 text-left">
                <h2 class="font-medium text-gray-900 mb-3">What can you do?</h2>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Contact the property manager who invited you to request a new invitation</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>If you already registered, try logging in with your email and password</span>
                    </li>
                </ul>
            </div>

            {{-- Login Link --}}
            <a href="{{ route('login') }}" 
               class="inline-block mt-8 bg-blue-600 text-white py-3 px-8 rounded-xl font-semibold
                      hover:bg-blue-700 transition-colors">
                Go to Login
            </a>
        </div>
    </div>
</x-guest-layout>
