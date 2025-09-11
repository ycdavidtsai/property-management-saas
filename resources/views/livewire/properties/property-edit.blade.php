<div>
    <form wire:submit.prevent="save" class="bg-white rounded-lg shadow-sm border p-6">
        <div class="space-y-6">
            <!-- Property Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Property Name</label>
                <input type="text" wire:model="name" id="name"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Enter property name">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea wire:model="address" id="address" rows="3"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Enter full address"></textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Property Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                <select wire:model="type" id="type"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select property type...</option>
                    <option value="single_family">Single Family</option>
                    <option value="multi_family">Multi Family</option>
                    <option value="apartment">Apartment Complex</option>
                    <option value="commercial">Commercial</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Units -->
            <div>
                <label for="total_units" class="block text-sm font-medium text-gray-700 mb-2">Total Units</label>
                <input type="number" wire:model="total_units" id="total_units" min="1"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Enter number of units">
                @error('total_units')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @if($current_unit_count != $total_units)
                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="text-sm text-yellow-800">
                            <strong>Unit Count Change:</strong>
                            @if($total_units > $current_unit_count)
                                This will add {{ $total_units - $current_unit_count }} new vacant units.
                            @else
                                This will remove {{ $current_unit_count - $total_units }} vacant units (occupied units cannot be removed).
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <!-- Current Status Info -->
            <div class="bg-gray-50 border border-gray-200 rounded p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Current Status</h4>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Current Units</p>
                        <p class="font-semibold">{{ $current_unit_count }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Created</p>
                        <p class="font-semibold">{{ $property->created_at->format('M j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Last Updated</p>
                        <p class="font-semibold">{{ $property->updated_at->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 flex justify-between">
            <div class="flex space-x-3">
                <a href="{{ route('properties.show', $property) }}"
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                    Cancel
                </a>
                <a href="{{ route('properties.index') }}"
                   class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">
                    Back to Properties
                </a>
            </div>

            <button type="submit" wire:loading.attr="disabled"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                <span wire:loading.remove>Update Property</span>
                <span wire:loading>Updating...</span>
            </button>
        </div>
    </form>
</div>
