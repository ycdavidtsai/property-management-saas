{{-- resources/views/livewire/vendors/vendor-setup-wizard.blade.php --}}
{{-- Mobile-first design for vendor registration --}}

<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white flex flex-col">
    {{-- Header --}}
    <div class="bg-blue-600 text-white px-4 py-6 text-center">
        <h1 class="text-xl font-bold">{{ $organizationName }}</h1>
        <p class="text-blue-100 text-sm mt-1">Vendor Registration</p>
    </div>

    {{-- Progress Steps --}}
    <div class="bg-white border-b px-4 py-3">
        <div class="flex items-center justify-center space-x-4">
            {{-- Step 1: Verify Phone --}}
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                    {{ $currentStep === 'verify_phone' ? 'bg-blue-600 text-white' :
                       (in_array($currentStep, ['enter_otp', 'complete_profile']) ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600') }}">
                    @if(in_array($currentStep, ['enter_otp', 'complete_profile']))
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        1
                    @endif
                </div>
                <span class="ml-2 text-xs {{ $currentStep === 'verify_phone' ? 'text-blue-600 font-medium' : 'text-gray-500' }}">Verify</span>
            </div>

            <div class="w-8 h-0.5 {{ in_array($currentStep, ['enter_otp', 'complete_profile']) ? 'bg-green-500' : 'bg-gray-200' }}"></div>

            {{-- Step 2: Enter OTP --}}
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                    {{ $currentStep === 'enter_otp' ? 'bg-blue-600 text-white' :
                       ($currentStep === 'complete_profile' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600') }}">
                    @if($currentStep === 'complete_profile')
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        2
                    @endif
                </div>
                <span class="ml-2 text-xs {{ $currentStep === 'enter_otp' ? 'text-blue-600 font-medium' : 'text-gray-500' }}">Code</span>
            </div>

            <div class="w-8 h-0.5 {{ $currentStep === 'complete_profile' ? 'bg-green-500' : 'bg-gray-200' }}"></div>

            {{-- Step 3: Complete Profile --}}
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                    {{ $currentStep === 'complete_profile' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                    3
                </div>
                <span class="ml-2 text-xs {{ $currentStep === 'complete_profile' ? 'text-blue-600 font-medium' : 'text-gray-500' }}">Profile</span>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 px-4 py-6">
        {{-- Success Message --}}
        @if($successMessage)
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-800 text-sm">{{ $successMessage }}</p>
            </div>
        @endif

        {{-- Error Message --}}
        @if($errorMessage)
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800 text-sm">{{ $errorMessage }}</p>
            </div>
        @endif

        {{-- Step 1: Verify Phone --}}
        @if($currentStep === 'verify_phone')
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>

                <h2 class="text-xl font-bold text-gray-900 mb-2">Verify Your Phone</h2>
                <p class="text-gray-600 mb-6">
                    We'll send a verification code to<br>
                    <strong class="text-gray-900">{{ $vendor->phone }}</strong>
                </p>

                <button
                    wire:click="sendOtp"
                    wire:loading.attr="disabled"
                    class="w-full bg-blue-600 text-white py-4 px-6 rounded-xl font-semibold text-lg shadow-lg
                           hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200
                           disabled:opacity-50 disabled:cursor-not-allowed
                           transition-all duration-200 active:scale-95"
                >
                    <span wire:loading.remove wire:target="sendOtp">
                        Send Verification Code
                    </span>
                    <span wire:loading wire:target="sendOtp" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>

                <p class="text-gray-500 text-xs mt-4">
                    Standard SMS rates may apply
                </p>
            </div>
        @endif

        {{-- Step 2: Enter OTP --}}
        @if($currentStep === 'enter_otp')
            <div class="text-center">
                <button wire:click="goBack" class="mb-4 text-blue-600 text-sm flex items-center mx-auto">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </button>

                <h2 class="text-xl font-bold text-gray-900 mb-2">Enter Verification Code</h2>
                <p class="text-gray-600 mb-6">
                    Enter the 6-digit code sent to your phone
                </p>

                <div class="mb-6">
                    <input
                        type="text"
                        wire:model="otp"
                        maxlength="6"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        autocomplete="one-time-code"
                        placeholder="000000"
                        class="w-48 mx-auto block text-center text-3xl font-mono tracking-widest py-4 border-2 border-gray-300 rounded-xl
                               focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                    >
                    @error('otp')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    wire:click="verifyOtp"
                    wire:loading.attr="disabled"
                    class="w-full bg-blue-600 text-white py-4 px-6 rounded-xl font-semibold text-lg shadow-lg
                           hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200
                           disabled:opacity-50 disabled:cursor-not-allowed
                           transition-all duration-200 active:scale-95"
                >
                    <span wire:loading.remove wire:target="verifyOtp">Verify Code</span>
                    <span wire:loading wire:target="verifyOtp">Verifying...</span>
                </button>

                <p class="text-gray-500 text-sm mt-4">
                    Didn't receive the code?
                    <button
                        wire:click="resendOtp"
                        class="text-blue-600 font-medium {{ $resendCountdown > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $resendCountdown > 0 ? 'disabled' : '' }}
                    >
                        @if($resendCountdown > 0)
                            Resend in {{ $resendCountdown }}s
                        @else
                            Resend
                        @endif
                    </button>
                </p>
            </div>
        @endif

        {{-- Step 3: Complete Profile --}}
        @if($currentStep === 'complete_profile')
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2 text-center">Complete Your Profile</h2>
                <p class="text-gray-600 mb-6 text-center">
                    Create your account to start receiving job requests
                </p>

                <form wire:submit.prevent="completeProfile" class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input
                            type="text"
                            wire:model="name"
                            autocomplete="name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none text-lg"
                            placeholder="Enter your full name"
                        >
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input
                            type="email"
                            wire:model="email"
                            autocomplete="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none text-lg"
                            placeholder="vendor@example.com"
                        >
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input
                            type="password"
                            wire:model="password"
                            autocomplete="new-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none text-lg"
                            placeholder="Create a password"
                        >
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input
                            type="password"
                            wire:model="password_confirmation"
                            autocomplete="new-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none text-lg"
                            placeholder="Confirm your password"
                        >
                        @error('password_confirmation')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full bg-green-600 text-white py-4 px-6 rounded-xl font-semibold text-lg shadow-lg
                               hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-200
                               disabled:opacity-50 disabled:cursor-not-allowed
                               transition-all duration-200 active:scale-95 mt-6"
                    >
                        <span wire:loading.remove wire:target="completeProfile">Create Account</span>
                        <span wire:loading wire:target="completeProfile">Creating Account...</span>
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="px-4 py-4 text-center text-gray-500 text-xs">
        By creating an account, you agree to our Terms of Service and Privacy Policy
    </div>
</div>

{{-- Countdown Timer Script --}}
@if($currentStep === 'enter_otp' && $resendCountdown > 0)
    @script
    <script>
        let countdown = $wire.resendCountdown;
        const timer = setInterval(() => {
            countdown--;
            if (countdown <= 0) {
                clearInterval(timer);
                $wire.resendCountdown = 0;
            } else {
                $wire.resendCountdown = countdown;
            }
        }, 1000);
    </script>
    @endscript
@endif
