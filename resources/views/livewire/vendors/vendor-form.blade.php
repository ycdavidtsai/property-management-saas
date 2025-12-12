{{-- resources/views/livewire/vendors/vendor-form.blade.php --}}
{{-- This is Landlord Create/Edit Vendors form page with invitation flow --}}
<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Pending Invitation Banner (for existing pending vendors) --}}
    @if($isPendingSetup && $invitationStatus)
        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Awaiting Vendor Registration
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>{{ $invitationStatus['message'] }}</p>
                        @if($invitationStatus['sent_at'])
                            <p class="mt-1 text-xs">
                                Invitation sent: {{ $invitationStatus['sent_at']->diffForHumans() }}
                                @if($invitationStatus['expires_at'] && !$invitationStatus['is_expired'])
                                    · Expires {{ $invitationStatus['expires_at']->diffForHumans() }}
                                @endif
                            </p>
                        @endif
                    </div>
                    @if($invitationStatus['can_resend'])
                        <div class="mt-3">
                            <button
                                type="button"
                                wire:click="resendInvitation"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-3 py-1.5 border border-yellow-600 text-sm font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                            >
                                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span wire:loading.remove wire:target="resendInvitation">Resend Invitation</span>
                                <span wire:loading wire:target="resendInvitation">Sending...</span>
                            </button>
                            <span class="ml-2 text-xs text-yellow-600">
                                {{ $invitationStatus['resend_count'] }}/{{ $invitationStatus['max_resends'] }} resends today
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Vendor Has User Account Notice --}}
        @if($hasUserAccount)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">This vendor manages their own profile</p>
                        <p class="text-sm text-blue-700 mt-1">
                            Name, email, and phone are managed by the vendor through their account. You can update business details below.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
                Name <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="name"
                wire:model="name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 {{ $hasUserAccount ? 'bg-gray-100' : '' }}"
                {{ $hasUserAccount ? 'disabled' : '' }}
                required
            >
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                Email <span class="text-red-500">*</span>
            </label>
            <input
                type="email"
                id="email"
                wire:model="email"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 {{ $hasUserAccount ? 'bg-gray-100' : '' }}"
                {{ $hasUserAccount ? 'disabled' : '' }}
                required
            >
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">
                Phone
                @if(!$isEditing && $sendInvitation)
                    <span class="text-red-500">*</span>
                @endif
            </label>
            <input
                type="text"
                id="phone"
                wire:model="phone"
                placeholder="+1 (555) 123-4567"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 {{ $hasUserAccount ? 'bg-gray-100' : '' }}"
                {{ $hasUserAccount ? 'disabled' : '' }}
            >
            @if(!$isEditing && $sendInvitation)
                <p class="mt-1 text-xs text-gray-500">Required for sending the invitation SMS</p>
            @endif
            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Business Type -->
        <div>
            <label for="business_type" class="block text-sm font-medium text-gray-700">
                Business Type <span class="text-red-500">*</span>
            </label>
            <select
                id="business_type"
                wire:model="business_type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                required
            >
                <option value="">Select a type...</option>
                @foreach($businessTypeOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('business_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Vendor Type (Admin Only) -->
        @if($is_admin)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Vendor Type <span class="text-red-500">*</span>
                </label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input
                            type="radio"
                            wire:model.live="vendor_type"
                            value="global"
                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        <span class="ml-2">
                            <span class="font-medium text-gray-900">Global Vendor</span>
                            <span class="text-sm text-gray-600 block">Available to all organizations in the platform</span>
                        </span>
                    </label>
                    <label class="flex items-center">
                        <input
                            type="radio"
                            wire:model.live="vendor_type"
                            value="private"
                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        <span class="ml-2">
                            <span class="font-medium text-gray-900">Private Vendor</span>
                            <span class="text-sm text-gray-600 block">Only visible to selected organizations</span>
                        </span>
                    </label>
                </div>
                @error('vendor_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @else
            <!-- Non-admin: Show info that vendor will be private -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Private Vendor</p>
                        <p class="text-sm text-blue-700 mt-1">
                            This vendor will be private to your organization. The vendor can request to be listed globally after creation.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Organizations (Show for private vendors or admin creating any type) -->
        @if($vendor_type === 'private' || $is_admin)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Organizations <span class="text-red-500">*</span>
                </label>
                @if($vendor_type === 'global')
                    <p class="text-sm text-gray-600 mb-2">
                        Optionally pre-add this global vendor to specific organizations. Organizations can still add them later.
                    </p>
                @else
                    <p class="text-sm text-gray-600 mb-2">
                        Select which organizations this private vendor will work for.
                    </p>
                @endif
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 rounded-md p-3">
                    @foreach($availableOrganizations as $organization)
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model="selected_organizations"
                                value="{{ $organization->id }}"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">{{ $organization->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('selected_organizations') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <!-- Specialties -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Specialties
            </label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($specialtyOptions as $specialty)
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            wire:model="specialties"
                            value="{{ $specialty }}"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">{{ $specialty }}</span>
                    </label>
                @endforeach
            </div>
            @error('specialties') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Hourly Rate -->
        <div>
            <label for="hourly_rate" class="block text-sm font-medium text-gray-700">
                Hourly Rate
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input
                    type="number"
                    id="hourly_rate"
                    wire:model="hourly_rate"
                    step="0.01"
                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="0.00"
                >
            </div>
            @error('hourly_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Notes -->
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700">
                Notes
            </label>
            <textarea
                id="notes"
                wire:model="notes"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Any additional notes about this vendor..."
            ></textarea>
            @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Portal Access / Invitation Section -->
        @if($isEditing)
            {{-- Existing Vendor --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">Vendor Portal Access</h4>
                @if($hasUserAccount)
                    <div class="flex items-center text-green-700">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Portal access enabled</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        This vendor can log in to view and update assigned maintenance requests.
                    </p>
                    @if($vendor->user)
                        <p class="text-sm text-gray-500 mt-1">
                            User account: <span class="font-medium">{{ $vendor->user->email }}</span>
                        </p>
                    @endif
                @elseif($isPendingSetup)
                    <div class="flex items-center text-yellow-700">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Invitation pending</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        Waiting for vendor to complete registration via the invitation link.
                    </p>
                @else
                    <div class="flex items-center text-gray-600">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span class="font-medium">No portal access</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        This vendor was created without sending an invitation.
                    </p>
                @endif
            </div>
        @else
            {{-- New Vendor - Invitation Option --}}
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            type="checkbox"
                            id="send_invitation"
                            wire:model.live="sendInvitation"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                        >
                    </div>
                    <div class="ml-3">
                        <label for="send_invitation" class="font-medium text-gray-900">
                            Send Registration Invitation via SMS
                        </label>
                        <p class="text-sm text-gray-600 mt-1">
                            The vendor will receive a text message with a link to complete their registration and set up their login credentials. <strong>This is the recommended approach.</strong>
                        </p>
                        @if($sendInvitation)
                            <div class="mt-3 bg-green-100 rounded p-3 text-sm">
                                <div class="flex items-center text-green-800">
                                    <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                    </svg>
                                    An SMS will be sent to the phone number provided
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(!$sendInvitation)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Vendor Record Only</p>
                            <p class="text-sm text-yellow-700 mt-1">
                                A vendor record will be created but they won't be able to log in to accept jobs or update their work. You can send an invitation later.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Active Status -->
        <div class="flex items-center">
            <input
                type="checkbox"
                id="is_active"
                wire:model="is_active"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                Active
            </label>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t">
            @if($isEditing)
                <a
                    href="{{ route('vendors.show', $vendor) }}"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Cancel
                </a>
            @else
                <a
                    href="{{ route('vendors.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Cancel
                </a>
            @endif
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="save">
                    @if($isEditing)
                        Update Vendor
                    @elseif($sendInvitation)
                        Create & Send Invitation
                    @else
                        Create Vendor
                    @endif
                </span>
                <span wire:loading wire:target="save">
                    Processing...
                </span>
            </button>
        </div>
    </form>
</div>
