{{-- resources/views/livewire/tenants/tenant-form.blade.php --}}
<div class="max-w-4xl mx-auto">

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- General Error -->
    @error('general')
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ $message }}
        </div>
    @enderror

    <form wire:submit.prevent="save" class="space-y-6">

        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $isEditing ? 'Edit Tenant' : 'Add New Tenant' }}
                    </h1>
                    <p class="text-gray-600">
                        {{ $isEditing ? 'Update tenant information and profile details.' : 'Create a new tenant account and profile.' }}
                    </p>
                </div>
                <a href="{{ route('tenants.index') }}"
                   class="text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Basic Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           wire:model.blur="name"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Enter full name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                           wire:model.blur="email"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                           placeholder="Enter email address">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number
                    </label>
                    <input type="text"
                           wire:model.blur="phone"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                           placeholder="Enter phone number">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Date of Birth
                    </label>
                    <input type="date"
                           wire:model.blur="date_of_birth"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('date_of_birth') border-red-500 @enderror">
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Account Status -->
            <div class="mt-6">
                <div class="flex items-center">
                    <input type="checkbox"
                           wire:model="is_active"
                           id="is_active"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Account is active (tenant can log in)
                    </label>
                </div>
            </div>
        </div>

        <!-- Account Access -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Account Access</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $isEditing ? 'New Password (leave blank to keep current)' : 'Password' }}
                        @if(!$isEditing) <span class="text-red-500">*</span> @endif
                    </label>
                    <input type="password"
                           wire:model.blur="password"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           placeholder="{{ $isEditing ? 'Enter new password (optional)' : 'Enter password' }}">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password
                        @if(!$isEditing) <span class="text-red-500">*</span> @endif
                    </label>
                    <input type="password"
                           wire:model.blur="password_confirmation"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Confirm password">
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Employment & Financial Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employment Status</label>
                    <select wire:model.blur="employment_status"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('employment_status') border-red-500 @enderror">
                        <option value="">Select Status</option>
                        <option value="employed">Employed</option>
                        <option value="self_employed">Self Employed</option>
                        <option value="unemployed">Unemployed</option>
                        <option value="retired">Retired</option>
                        <option value="student">Student</option>
                    </select>
                    @error('employment_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Income</label>
                    <input type="number"
                           wire:model.blur="monthly_income"
                           step="0.01"
                           min="0"
                           max="999999.99"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('monthly_income') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('monthly_income')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last 4 digits of SSN</label>
                    <input type="text"
                           wire:model.blur="ssn_last_four"
                           maxlength="4"
                           pattern="\d{4}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('ssn_last_four') border-red-500 @enderror"
                           placeholder="1234">
                    <p class="mt-1 text-xs text-gray-500">For identification purposes only</p>
                    @error('ssn_last_four')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Emergency Contact</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                    <input type="text"
                           wire:model.blur="emergency_contact_name"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('emergency_contact_name') border-red-500 @enderror"
                           placeholder="Enter contact name">
                    @error('emergency_contact_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                    <input type="text"
                           wire:model.blur="emergency_contact_phone"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('emergency_contact_phone') border-red-500 @enderror"
                           placeholder="Enter contact phone">
                    @error('emergency_contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                    <input type="text"
                           wire:model.blur="emergency_contact_relationship"
                           placeholder="e.g., Spouse, Parent, Friend"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('emergency_contact_relationship') border-red-500 @enderror">
                    @error('emergency_contact_relationship')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Additional Notes</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea wire:model.blur="notes"
                         rows="4"
                         maxlength="1000"
                         class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                         placeholder="Any additional notes about this tenant..."></textarea>
                <div class="flex justify-between mt-1">
                    <p class="text-xs text-gray-500">Maximum 1000 characters</p>
                    <p class="text-xs text-gray-500">{{ strlen($notes) }}/1000</p>
                </div>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('tenants.index') }}"
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditing ? 'Update' : 'Create' }} Tenant
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ $isEditing ? 'Updating...' : 'Creating...' }}
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
