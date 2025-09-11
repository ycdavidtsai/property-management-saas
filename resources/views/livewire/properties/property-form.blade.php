<div>
    <form wire:submit.prevent="save" class="bg-white rounded-lg shadow-sm border p-6">
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Property Name</label>
                <input type="text" wire:model="name" class="w-full border border-gray-300 rounded-md px-3 py-2">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea wire:model="address" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                <select wire:model="type" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Select property type...</option>
                    <option value="single_family">Single Family</option>
                    <option value="multi_family">Multi Family</option>
                    <option value="apartment">Apartment Complex</option>
                    <option value="commercial">Commercial</option>
                </select>
                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Units</label>
                <input type="number" wire:model="total_units" min="1" class="w-full border border-gray-300 rounded-md px-3 py-2">
                @error('total_units') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('properties.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Create Property
            </button>
        </div>
    </form>
</div>
