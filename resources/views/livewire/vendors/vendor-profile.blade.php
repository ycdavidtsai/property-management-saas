<div class="max-w-4xl mx-auto space-y-6">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- ========== PENDING APPROVAL STATE ========== --}}
    @if($this->isPendingApproval())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-amber-800">Application Under Review</h2>
                    <p class="mt-2 text-amber-700">
                        Your vendor application is currently being reviewed by our team.
                        You'll receive an SMS and email notification once a decision is made.
                    </p>
                    <p class="mt-3 text-sm text-amber-600">
                        Review typically takes 1-2 business days.
                    </p>
                </div>
            </div>
        </div>

        {{-- Profile Preview (Read Only) --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Application Details</h3>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Business Name</dt>
                    <dd class="mt-1 text-gray-900">{{ $vendor->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Contact Name</dt>
                    <dd class="mt-1 text-gray-900">{{ $vendor->contact_name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-gray-900">{{ $vendor->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-gray-900">{{ $vendor->phone }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Services</dt>
                    <dd class="mt-1">
                        @if($vendor->specialties && count($vendor->specialties) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($vendor->specialties as $specialty)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded">{{ $specialty }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </dd>
                </div>
                @if($vendor->description)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-gray-900">{{ $vendor->description }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- What happens next --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="font-semibold text-blue-900 mb-3">What happens after approval?</h3>
            <ul class="space-y-2 text-blue-800">
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Your profile becomes visible to <strong>all landlords</strong> on the platform</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>You can receive maintenance job assignments</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Track and complete jobs through your dashboard</span>
                </li>
            </ul>
        </div>

    {{-- ========== REJECTED STATE ========== --}}
    @elseif($this->isRejected())
        <div class="bg-red-50 border border-red-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-red-800">Application Not Approved</h2>
                    <p class="mt-2 text-red-700">
                        We're sorry, but we were unable to approve your vendor application at this time.
                    </p>
                    @if($vendor->rejection_reason)
                        <div class="mt-4 p-4 bg-red-100 rounded-lg">
                            <p class="text-sm font-semibold text-red-800">Reason:</p>
                            <p class="text-red-700">{{ $vendor->rejection_reason }}</p>
                        </div>
                    @endif
                    <p class="mt-4 text-sm text-red-600">
                        If you believe this was an error or have questions, please contact our support team.
                    </p>
                </div>
            </div>
        </div>

    {{-- ========== ACTIVE VENDOR STATE ========== --}}
    @elseif($this->isActive())
        {{-- Header with Status Badge --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $vendor->name }}</h1>
                    @if($vendor->contact_name)
                        <p class="text-gray-600 mt-1">{{ $vendor->contact_name }}</p>
                    @endif
                </div>
                <div class="mt-4 sm:mt-0">
                    @if($this->isGlobal())
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
                            </svg>
                            Global Vendor
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd"/>
                            </svg>
                            Private Vendor
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Profile Details --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h3>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-gray-900">{{ $vendor->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-gray-900">{{ $vendor->phone }}</dd>
                </div>
                @if($vendor->business_type)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Business Type</dt>
                        <dd class="mt-1 text-gray-900">{{ $vendor->business_type }}</dd>
                    </div>
                @endif
                @if($vendor->hourly_rate)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Hourly Rate</dt>
                        <dd class="mt-1 text-gray-900">${{ number_format($vendor->hourly_rate, 2) }}</dd>
                    </div>
                @endif
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Services Offered</dt>
                    <dd class="mt-2">
                        @if($vendor->specialties && count($vendor->specialties) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($vendor->specialties as $specialty)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">{{ $specialty }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400">No services listed</span>
                        @endif
                    </dd>
                </div>
                @if($vendor->description)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">About</dt>
                        <dd class="mt-1 text-gray-900">{{ $vendor->description }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Promotion Section - Only for INVITED PRIVATE vendors --}}
        @if($this->canRequestPromotion())
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-indigo-900">Expand Your Reach</h3>
                        <p class="text-indigo-700 mt-1">
                            Request to become a Global Vendor and be visible to all landlords on the platform.
                        </p>
                    </div>
                    <button
                        wire:click="openPromotionModal"
                        class="mt-4 sm:mt-0 inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                        Request Global Listing
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

        {{-- Pending Promotion Request --}}
        @elseif($hasRequestedPromotion && $promotionRequest)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-amber-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-amber-800">Global Listing Request Pending</p>
                        <p class="text-sm text-amber-700 mt-1">
                            Your request to become a global vendor is being reviewed.
                            Submitted {{ $promotionRequest->created_at->diffForHumans() }}.
                        </p>
                    </div>
                </div>
            </div>

        {{-- Already Global Info --}}
        @elseif($this->isGlobal())
            <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-green-800">Global Vendor Status Active</p>
                        <p class="text-sm text-green-700 mt-1">
                            Your profile is visible to all landlords on the platform. You can receive job assignments from any property manager.
                        </p>
                    </div>
                </div>
            </div>
        @endif

    @else
        {{-- Unknown Status --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-600">Account status: <strong>{{ $vendor->setup_status ?? 'unknown' }}</strong></p>
            <p class="text-sm text-gray-500 mt-2">Please contact support if you need assistance.</p>
        </div>
    @endif

    {{-- Promotion Request Modal --}}
    @if($showPromotionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closePromotionModal"></div>

                {{-- Modal panel --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">
                                    Request Global Listing
                                </h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Tell us why you'd like to become a global vendor. This helps our team review your request.
                                    </p>
                                    <textarea
                                        wire:model="promotionReason"
                                        rows="4"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Describe your experience, certifications, and why you want to expand your reach..."
                                    ></textarea>
                                    @error('promotionReason')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="submitPromotionRequest"
                            wire:loading.attr="disabled"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-white font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="submitPromotionRequest">Submit Request</span>
                            <span wire:loading wire:target="submitPromotionRequest">Submitting...</span>
                        </button>
                        <button
                            wire:click="closePromotionModal"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 font-semibold hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
