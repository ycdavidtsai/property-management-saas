<div>
    <!-- Request Details -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $maintenanceRequest->title }}</h1>
                <p class="text-gray-600 mt-1">Request #{{ substr($maintenanceRequest->id, 0, 8) }}</p>
            </div>
            <div class="flex space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $maintenanceRequest->priority_color }}-100 text-{{ $maintenanceRequest->priority_color }}-800">
                    {{ ucfirst($maintenanceRequest->priority) }} Priority
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $maintenanceRequest->status_color }}-100 text-{{ $maintenanceRequest->status_color }}-800">
                    {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
                </span>
            </div>
        </div>

        <!-- Request Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">Property</label>
                <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->property->name }}</p>
            </div>
            @if($maintenanceRequest->unit)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Unit</label>
                    <p class="mt-1 text-sm text-gray-900">Unit {{ $maintenanceRequest->unit->unit_number }}</p>
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-500">Submitted By</label>
                <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->tenant->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Created</label>
                <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->created_at->format('M d, Y g:i A') }}</p>
            </div>
        </div>

        @if($maintenanceRequest->assignedVendor)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Assigned Vendor</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->assignedVendor->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Assigned Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->assigned_at?->format('M d, Y g:i A') }}</p>
                </div>
                @if($maintenanceRequest->estimated_cost)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Estimated Cost</label>
                        <p class="mt-1 text-sm text-gray-900">${{ number_format($maintenanceRequest->estimated_cost, 2) }}</p>
                    </div>
                @endif
                @if($maintenanceRequest->actual_cost)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Actual Cost</label>
                        <p class="mt-1 text-sm text-gray-900">${{ number_format($maintenanceRequest->actual_cost, 2) }}</p>
                    </div>
                @endif
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
            <p class="text-gray-900">{{ $maintenanceRequest->description }}</p>
        </div>

        @if($maintenanceRequest->photos && count($maintenanceRequest->photos) > 0)
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-500 mb-2">Photos</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($maintenanceRequest->photos as $photo)
                        <img src="{{ Storage::url($photo) }}" alt="Maintenance photo"
                             class="w-full h-32 object-cover rounded-lg cursor-pointer"
                             onclick="window.open('{{ Storage::url($photo) }}', '_blank')">
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Quick Actions for Management -->
        @if($canManage)
            <div class="mt-6 pt-6 border-t flex space-x-4">
                @if($maintenanceRequest->canBeAssigned())
                    <button wire:click="$set('showAssignModal', true)"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Assign Vendor
                    </button>
                @endif

                @if($maintenanceRequest->canBeStarted() || $maintenanceRequest->canBeCompleted() || $maintenanceRequest->canBeClosed())
                    <button wire:click="$set('showStatusModal', true)"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Update Status
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Updates/Communication -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Updates & Communication</h2>

        <!-- Add New Update -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form wire:submit.prevent="addUpdate">
                <div class="mb-4">
                    <textarea wire:model="newUpdate" rows="3"
                              placeholder="Add an update or comment..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                    @error('newUpdate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <input type="file" wire:model="updatePhotos" multiple accept="image/*"
                               class="text-sm">
                        @if($canManage)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="isInternal" class="mr-2">
                                <span class="text-sm text-gray-600">Internal note (not visible to tenant)</span>
                            </label>
                        @endif
                    </div>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Update
                    </button>
                </div>
            </form>
        </div>

        <!-- Updates List -->
        <div class="space-y-4">
            @forelse($maintenanceRequest->updates as $update)
                @if(!$update->is_internal || $canManage)
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-medium text-gray-900">{{ $update->user->name }}</span>
                                @if($update->is_internal)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                                        Internal
                                    </span>
                                @endif
                                @if($update->type !== 'comment')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                        {{ ucfirst(str_replace('_', ' ', $update->type)) }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500">{{ $update->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <p class="text-gray-700 mb-2">{{ $update->message }}</p>

                        @if($update->photos && count($update->photos) > 0)
                            <div class="grid grid-cols-4 gap-2 mt-2">
                                @foreach($update->photos as $photo)
                                    <img src="{{ Storage::url($photo) }}" alt="Update photo"
                                         class="w-full h-20 object-cover rounded cursor-pointer"
                                         onclick="window.open('{{ Storage::url($photo) }}', '_blank')">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @empty
                <p class="text-gray-500 text-center py-4">No updates yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Assign Vendor Modal -->
    @if($showAssignModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Vendor</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Vendor</label>
                    <select wire:model="selectedVendorId" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">Choose a vendor...</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }} - {{ $vendor->business_type }}</option>
                        @endforeach
                    </select>
                    @error('selectedVendorId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-2">
                    <button wire:click="$set('showAssignModal', false)"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button wire:click="assignVendor"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Assign
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Status Update Modal -->
    @if($showStatusModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Update Status</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Status</label>
                    <select wire:model="newStatus" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">Select status...</option>
                        @if($maintenanceRequest->canBeStarted())
                            <option value="in_progress">In Progress</option>
                        @endif
                        @if($maintenanceRequest->canBeCompleted())
                            <option value="completed">Completed</option>
                        @endif
                        @if($maintenanceRequest->canBeClosed())
                            <option value="closed">Closed</option>
                        @endif
                    </select>
                    @error('newStatus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea wire:model="statusNotes" rows="3"
                              placeholder="Additional notes about this status change..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button wire:click="$set('showStatusModal', false)"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button wire:click="updateStatus"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Update Status
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
