<div>
    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($canManage)
                <!-- Management users can select property/unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property *</label>
                    <select wire:model.live="property_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">Select Property</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                    @error('property_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <select wire:model.live="unit_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">Select Unit (Optional)</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">Unit {{ $unit->unit_number }}</option>
                        @endforeach
                    </select>
                    @error('unit_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            @else
                <!-- Tenants see their property/unit as read-only -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                    <div class="w-full border border-gray-200 bg-gray-50 rounded-md px-3 py-2 text-gray-700">
                        @if($property_id && $selectedProperty)
                            {{ $selectedProperty->name }}
                        @else
                            <span class="text-gray-500">No active lease found</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <div class="w-full border border-gray-200 bg-gray-50 rounded-md px-3 py-2 text-gray-700">
                        @if($unit_id && $selectedUnit)
                            Unit {{ $selectedUnit->unit_number }}
                        @else
                            <span class="text-gray-500">No unit assigned</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
            <input type="text" wire:model="title"
                   placeholder="Brief description of the issue"
                   class="w-full border border-gray-300 rounded-md px-3 py-2">
            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
            <textarea wire:model="description" rows="4"
                      placeholder="Detailed description of the maintenance issue"
                      class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                <select wire:model="priority" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="emergency">Emergency</option>
                    <option value="high">High</option>
                    <option value="normal">Normal</option>
                    <option value="low">Low</option>
                </select>
                @error('priority') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <input type="text" wire:model="category"
                       placeholder="e.g., Plumbing, Electrical, Appliance"
                       class="w-full border border-gray-300 rounded-md px-3 py-2">
                @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Date</label>
                <input type="date" wire:model="preferred_date"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2">
                @error('preferred_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Management Fields (only shown for non-tenant users editing) -->
        @if($canManage && $isEditing)
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Management Options</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model="status" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="submitted">Submitted</option>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="closed">Closed</option>
                        </select>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Vendor</label>
                        <select wire:model="assigned_vendor_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_vendor_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Cost</label>
                        <input type="number" step="0.01" wire:model="estimated_cost"
                               placeholder="0.00"
                               class="w-full border border-gray-300 rounded-md px-3 py-2">
                        @error('estimated_cost') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Completion Notes</label>
                    <textarea wire:model="completion_notes" rows="3"
                              placeholder="Notes about completion, resolution, etc."
                              class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                    @error('completion_notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        @endif

        <!-- Photos -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Photos</label>

            <!-- Existing Photos -->
            @if(count($photos) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    @foreach($photos as $index => $photo)
                        <div class="relative">
                            <img src="{{ Storage::url($photo) }}" alt="Maintenance photo"
                                 class="w-full h-24 object-cover rounded-lg">
                            <button type="button" wire:click="removePhoto({{ $index }})"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- New Photos Upload -->
            <input type="file" wire:model="newPhotos" multiple accept="image/*"
                   class="w-full border border-gray-300 rounded-md px-3 py-2">
            @error('newPhotos.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <!-- Loading indicator -->
            <div wire:loading wire:target="newPhotos" class="text-sm text-gray-500 mt-1">
                Uploading photos...
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('maintenance-requests.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ $isEditing ? 'Update Request' : 'Submit Request' }}
            </button>
        </div>
    </form>
</div>
