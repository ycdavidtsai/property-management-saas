<div>
    <form wire:submit="save">
        <!-- Basic Information -->
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Vendor Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        wire:model="name"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('email')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone Number
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        wire:model="phone"
                        placeholder="(555) 123-4567"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('phone')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Business Details -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Business Details</h3>

                <!-- Business Type -->
                <div class="mb-4">
                    <label for="business_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Business Type <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="business_type"
                        wire:model="business_type"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="">Select a business type</option>
                        @foreach($businessTypeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('business_type')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Specialties -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Specialties
                    </label>
                    <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                        @foreach($specialtyOptions as $specialty)
                            <label class="flex items-center">
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
                    @error('specialties')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Hourly Rate -->
                <div class="mb-4">
                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-1">
                        Hourly Rate (Optional)
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input
                            type="number"
                            id="hourly_rate"
                            wire:model="hourly_rate"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                    @error('hourly_rate')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>

                <!-- Notes -->
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Internal Notes
                    </label>
                    <textarea
                        id="notes"
                        wire:model="notes"
                        rows="4"
                        placeholder="Add any internal notes about this vendor..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    <p class="mt-1 text-sm text-gray-500">These notes are only visible to managers and admins.</p>
                    @error('notes')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="mb-4">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm font-medium text-gray-700">Active Vendor</span>
                    </label>
                    <p class="ml-6 text-sm text-gray-500">Only active vendors can be assigned to maintenance requests.</p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
            <a
                href="{{ $vendor && $vendor->exists ? route('vendors.show', $vendor) : route('vendors.index') }}"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                Cancel
            </a>
            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                {{ $vendor && $vendor->exists ? 'Update Vendor' : 'Create Vendor' }}
            </button>
        </div>
    </form>
</div>
