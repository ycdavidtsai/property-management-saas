<div class="min-h-screen bg-gray-50">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-6">
        <div class="max-w-lg mx-auto">
            <h1 class="text-2xl font-bold">Join as a Vendor</h1>
            <p class="text-blue-100 mt-1">Register your business to receive job opportunities</p>
        </div>
    </div>

    {{-- Progress Steps --}}
    <div class="bg-white border-b shadow-sm">
        <div class="max-w-lg mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                @foreach([1 => 'Business Info', 2 => 'Verify Phone', 3 => 'Create Account'] as $step => $label)
                    <div class="flex items-center {{ $step < $totalSteps ? 'flex-1' : '' }}">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold
                                {{ $currentStep > $step ? 'bg-green-500 text-white' : ($currentStep === $step ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                                @if($currentStep > $step)
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    {{ $step }}
                                @endif
                            </div>
                            <span class="text-xs mt-1 text-gray-600 hidden sm:block">{{ $label }}</span>
                        </div>
                        @if($step < $totalSteps)
                            <div class="flex-1 h-1 mx-2 {{ $currentStep > $step ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Form Content --}}
    <div class="max-w-lg mx-auto px-4 py-6">

        {{-- Flash Messages --}}
        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start">
                <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-800 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        {{-- General Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-red-800 text-sm font-medium">Please fix the following errors:</p>
                        <ul class="mt-1 text-red-700 text-sm list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Step 1: Business Information --}}
        @if($currentStep === 1)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h2>

                {{-- Debug: Test if Livewire is working --}}
                {{-- <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm">
                    <strong>Debug Info:</strong> Step {{ $currentStep }} |
                    Company: {{ $company_name ?: '(empty)' }} |
                    Specialties: {{ count($specialties) }}
                    <button type="button" wire:click="$refresh" class="ml-2 text-blue-600 underline">Refresh</button>
                </div> --}}

                <form wire:submit="submitBusinessInfo" class="space-y-5">
                    {{-- Company Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Business Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.live="company_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                               placeholder="Your company name">
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contact Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Your Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.live="contact_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                               placeholder="Full name">
                        @error('contact_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model.live="email"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                               placeholder="you@company.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" wire:model.live="phone"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                               placeholder="(555) 123-4567">
                        <p class="mt-1 text-xs text-gray-500">We'll send a verification code to this number</p>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Specialties --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Specialties <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            @foreach($availableSpecialties as $key => $label)
                                <label class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" wire:model.live="specialties" value="{{ $key }}"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('specialties')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            About Your Business <span class="text-gray-400">(optional)</span>
                        </label>
                        <textarea wire:model="description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                                  placeholder="Tell us about your experience and services..."></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-colors">
                        <span wire:loading.remove wire:target="submitBusinessInfo">Continue</span>
                        <span wire:loading wire:target="submitBusinessInfo">
                            <svg class="animate-spin inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-500">
                    Already registered?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Sign in</a>
                </p>
            </div>
        @endif

        {{-- Step 2: Phone Verification --}}
        @if($currentStep === 2)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Verify Your Phone</h2>
                    <p class="text-gray-600 mt-1">
                        We sent a 6-digit code to<br>
                        <span class="font-medium text-gray-900">{{ $vendor?->phone ?? $phone }}</span>
                    </p>
                </div>

                @if($otpError)
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-700 text-sm">{{ $otpError }}</p>
                    </div>
                @endif

                <form wire:submit="verifyOtp" class="space-y-5">
                    {{-- OTP Input --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 sr-only">
                            Verification Code
                        </label>
                        <input type="text" wire:model="otp"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               maxlength="6"
                               autofocus
                               class="w-full px-4 py-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-2xl tracking-widest font-mono"
                               placeholder="000000">
                        @error('otp')
                            <p class="mt-1 text-sm text-red-600 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Verify Button --}}
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-colors">
                        <span wire:loading.remove wire:target="verifyOtp">Verify Code</span>
                        <span wire:loading wire:target="verifyOtp">Verifying...</span>
                    </button>
                </form>

                {{-- Resend OTP --}}
                <div class="mt-6 text-center" x-data="{ cooldown: @entangle('otpCooldown') }" x-init="
                    setInterval(() => {
                        if (cooldown > 0) {
                            cooldown--;
                            $wire.decrementCooldown();
                        }
                    }, 1000)
                ">
                    <p class="text-sm text-gray-600 mb-2">Didn't receive the code?</p>
                    <template x-if="cooldown > 0">
                        <p class="text-sm text-gray-500">Resend available in <span x-text="cooldown"></span>s</p>
                    </template>
                    <template x-if="cooldown === 0">
                        <button wire:click="resendOtp"
                                wire:loading.attr="disabled"
                                class="text-blue-600 hover:underline text-sm font-medium disabled:opacity-50">
                            <span wire:loading.remove wire:target="resendOtp">Resend Code</span>
                            <span wire:loading wire:target="resendOtp">Sending...</span>
                        </button>
                    </template>
                    @if($otpResendCount > 0)
                        <p class="text-xs text-gray-400 mt-1">{{ 3 - $otpResendCount }} resends remaining</p>
                    @endif
                </div>

                {{-- Back Button --}}
                <button wire:click="previousStep"
                        class="mt-4 w-full py-2 text-gray-600 hover:text-gray-800 text-sm">
                    ← Back to business info
                </button>
            </div>
        @endif

        {{-- Step 3: Create Account --}}
        @if($currentStep === 3)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Phone Verified!</h2>
                    <p class="text-gray-600 mt-1">Now let's secure your account</p>
                </div>

                <form wire:submit="completeRegistration" class="space-y-5">
                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Create Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" wire:model="password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                               placeholder="Minimum 8 characters">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" wire:model="password_confirmation"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                               placeholder="Confirm your password">
                    </div>

                    {{-- Terms --}}
                    <div class="flex items-start">
                        <input type="checkbox" wire:model="agree_terms" id="agree_terms"
                               class="w-4 h-4 mt-1 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="agree_terms" class="ml-2 text-sm text-gray-600">
                            I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a>
                            and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                        </label>
                    </div>
                    @error('agree_terms')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Submit --}}
                    <button type="submit"
                            wire:loading.attr="disabled"
                            :disabled="$wire.isSubmitting"
                            class="w-full py-3 px-4 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 transition-colors">
                        <span wire:loading.remove wire:target="completeRegistration">Complete Registration</span>
                        <span wire:loading wire:target="completeRegistration">
                            <svg class="animate-spin inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating Account...
                        </span>
                    </button>
                </form>
            </div>
        @endif

        {{-- Security Notice --}}
        <div class="mt-6 text-center text-xs text-gray-500">
            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Your information is secured with 256-bit encryption
        </div>
    </div>
</div>
