<div>
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate">
                        Edit Unit {{ $unit->unit_number }}
                    </h1>
                    <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 16.5v-13h-.25a.75.75 0 010-1.5h12.5a.75.75 0 010 1.5H16v13h.25a.75.75 0 010 1.5H3.75a.75.75 0 010-1.5H4zm1.5 0v-2.5h9v2.5h-9zm9-4v-2.5h-9v2.5h9zm-9-4v-2.5h9v2.5h-9z" clip-rule="evenodd" />
                            </svg>
                            {{ $property->name }}
                        </div>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            {{ $property->address }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex space-x-3 sm:mt-0">
                    <button wire:click="cancel" 
                            class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form wire:submit.prevent="save">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        
                        <!-- Unit Number -->
                        <div class="sm:col-span-2">
                            <label for="unit_number" class="block text-sm font-medium text-gray-700">
                                Unit Number <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input wire:model="unit_number" 
                                       type="text" 
                                       id="unit_number"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('unit_number') border-red-300 @enderror"
                                       placeholder="e.g. 101, A1, etc.">
                            </div>
                            @error('unit_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="sm:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select wire:model="status" 
                                        id="status"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('status') border-red-300 @enderror">
                                    <option value="">Select Status</option>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rent Amount -->
                        <div class="sm:col-span-2">
                            <label for="rent_amount" class="block text-sm font-medium text-gray-700">
                                Monthly Rent <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input wire:model="rent_amount" 
                                       type="number" 
                                       id="rent_amount"
                                       step="0.01"
                                       min="0"
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md @error('rent_amount') border-red-300 @enderror"
                                       placeholder="0.00">
                            </div>
                            @error('rent_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bedrooms -->
                        <div class="sm:col-span-2">
                            <label for="bedrooms" class="block text-sm font-medium text-gray-700">
                                Bedrooms <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input wire:model="bedrooms" 
                                       type="number" 
                                       id="bedrooms"
                                       min="0"
                                       max="10"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('bedrooms') border-red-300 @enderror"
                                       placeholder="0">
                            </div>
                            @error('bedrooms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bathrooms -->
                        <div class="sm:col-span-2">
                            <label for="bathrooms" class="block text-sm font-medium text-gray-700">
                                Bathrooms <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input wire:model="bathrooms" 
                                       type="number" 
                                       id="bathrooms"
                                       step="0.5"
                                       min="0"
                                       max="10"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('bathrooms') border-red-300 @enderror"
                                       placeholder="0">
                            </div>
                            @error('bathrooms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Square Feet -->
                        <div class="sm:col-span-2">
                            <label for="square_feet" class="block text-sm font-medium text-gray-700">
                                Square Feet
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input wire:model="square_feet" 
                                       type="number" 
                                       id="square_feet"
                                       min="100"
                                       max="10000"
                                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md @error('square_feet') border-red-300 @enderror"
                                       placeholder="0">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">sq ft</span>
                                </div>
                            </div>
                            @error('square_feet')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="sm:col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <div class="mt-1">
                                <textarea wire:model="description" 
                                          id="description"
                                          rows="3"
                                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('description') border-red-300 @enderror"
                                          placeholder="Brief description of the unit..."></textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Optional description of the unit (max 1000 characters)</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Features -->
                        <div class="sm:col-span-6">
                            <label for="features" class="block text-sm font-medium text-gray-700">
                                Features & Amenities
                            </label>
                            <div class="mt-1">
                                <textarea wire:model="features" 
                                          id="features"
                                          rows="2"
                                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('features') border-red-300 @enderror"
                                          placeholder="e.g. Balcony, In-unit laundry, Hardwood floors..."></textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">List any special features or amenities (max 500 characters)</p>
                            @error('features')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Save Changes</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                    <button type="button" 
                            wire:click="cancel"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Unit Info Card -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Current Unit Information</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="text-sm text-gray-900">{{ $unit->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="text-sm text-gray-900">{{ $unit->updated_at->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                        <dd class="text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $unit->status === 'occupied' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $unit->status === 'vacant' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $unit->status === 'for_lease' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $unit->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $unit->status)) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>