<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Main Content -->
        <div class="flex-1 space-y-6">
            <!-- Request Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $maintenanceRequest->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Created {{ $maintenanceRequest->created_at->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>
                        @php
                            $statusColors = [
                                'assigned' => 'bg-blue-100 text-blue-800',
                                'in_progress' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-green-100 text-green-800',
                            ];
                            $priorityColors = [
                                'low' => 'bg-gray-100 text-gray-800',
                                'medium' => 'bg-blue-100 text-blue-800',
                                'high' => 'bg-yellow-100 text-yellow-800',
                                'urgent' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <div class="flex gap-2">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$maintenanceRequest->status] ?? 'bg-gray-100' }}">
                                {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
                            </span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $priorityColors[$maintenanceRequest->priority] ?? 'bg-gray-100' }}">
                                {{ ucfirst($maintenanceRequest->priority) }}
                            </span>
                        </div>
                    </div>

                    <div class="prose max-w-none">
                        <p class="text-gray-700">{{ $maintenanceRequest->description }}</p>
                    </div>

                    @if($maintenanceRequest->photos && count($maintenanceRequest->photos) > 0)
                        <div class="mt-4">
                            <h4 class="font-semibold mb-2">Request Photos</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($maintenanceRequest->photos as $photo)
                                    <img src="{{ Storage::url($photo) }}" alt="Request photo" class="rounded-lg w-full h-32 object-cover cursor-pointer hover:opacity-75">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($maintenanceRequest->assignment_notes)
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-semibold text-blue-900 mb-2">Assignment Notes</h4>
                            <p class="text-blue-800">{{ $maintenanceRequest->assignment_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Update Actions -->
            @if($maintenanceRequest->status !== 'completed')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="font-semibold mb-4">Update Status</h4>
                        <div class="flex gap-3">
                            @if($maintenanceRequest->status === 'assigned')
                                <button
                                    wire:click="updateStatus('in_progress')"
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                >
                                    Start Work
                                </button>
                            @endif

                            @if($maintenanceRequest->status === 'in_progress')
                                <button
                                    wire:click="updateStatus('completed')"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                                >
                                    Complete Work
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Add Update Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="font-semibold mb-4">Add Progress Update</h4>
                    <form wire:submit.prevent="addUpdate">
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Update Message</label>
                            <textarea
                                id="message"
                                wire:model="message"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Describe your progress..."
                            ></textarea>
                            @error('message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Add Photos (optional)</label>
                            <input
                                type="file"
                                wire:model="photos"
                                multiple
                                accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            >
                            @error('photos.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500 mt-1">Max 5MB per file</p>
                        </div>

                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Add Update</span>
                            <span wire:loading>Adding...</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="font-semibold mb-4">Timeline</h4>
                    <div class="space-y-4">
                        @forelse($updates as $update)
                            <div class="border-l-4 border-gray-300 pl-4 py-2">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-medium text-gray-900">{{ $update->user->name }}</span>
                                    <span class="text-sm text-gray-500">{{ $update->created_at->diffForHumans() }}</span>
                                </div>

                                @if($update->update_type === 'status_change')
                                    <div class="text-sm text-blue-600 font-medium mb-1">Status Update</div>
                                @endif

                                <p class="text-gray-700">{{ $update->message }}</p>

                                @if($update->photos && count($update->photos) > 0)
                                    <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2">
                                        @foreach($update->photos as $photo)
                                            <img src="{{ Storage::url($photo) }}" alt="Update photo" class="rounded-lg w-full h-24 object-cover">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No updates yet.</p>
                        @endforelse
                    </div>

                    @if($updates->hasPages())
                            <div class="mt-6">
                                {{ $updates->links() }}
                            </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="w-full lg:w-1/4 space-y-6">
            <!-- Property Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="font-semibold mb-3">Property Information</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Property</dt>
                            <dd class="text-sm text-gray-900">{{ $maintenanceRequest->property->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="text-sm text-gray-900">{{ $maintenanceRequest->property->address }}</dd>
                        </div>
                        @if($maintenanceRequest->unit)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Unit</dt>
                                <dd class="text-sm text-gray-900">{{ $maintenanceRequest->unit->unit_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tenant</dt>
                                <dd class="text-sm text-gray-900">Name: {{ $maintenanceRequest->tenant->name }}<br>Phone: {{ $maintenanceRequest->tenant->phone }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="text-sm text-gray-900">{{ ucfirst($maintenanceRequest->category) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="font-semibold mb-3">Contact Information</h4>
                    @if($maintenanceRequest->assignedBy)
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Assigned by</p>
                            <p class="text-sm font-medium text-gray-900">{{ $maintenanceRequest->assignedBy->name }}</p>
                            <p class="text-sm text-gray-600">Phone: {{ $maintenanceRequest->assignedBy->phone }}</p>
                            <p class="text-sm text-gray-600">Email: {{ $maintenanceRequest->assignedBy->email }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cost Info -->
            @if($maintenanceRequest->estimated_cost || $maintenanceRequest->actual_cost)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="font-semibold mb-3">Cost Information</h4>
                        @if($maintenanceRequest->estimated_cost)
                            <div class="mb-2">
                                <span class="text-sm text-gray-500">Estimated:</span>
                                <span class="text-sm font-medium text-gray-900">${{ number_format($maintenanceRequest->estimated_cost, 2) }}</span>
                            </div>
                        @endif
                        @if($maintenanceRequest->actual_cost)
                            <div>
                                <span class="text-sm text-gray-500">Actual:</span>
                                <span class="text-sm font-medium text-gray-900">${{ number_format($maintenanceRequest->actual_cost, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Complete Work Modal -->
    @if($showCompleteModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Complete Work</h3>

                <form wire:submit.prevent="completeWork">
                    <div class="mb-4">
                        <label for="completionNotes" class="block text-sm font-medium text-gray-700 mb-2">Completion Notes *</label>
                        <textarea
                            id="completionNotes"
                            wire:model="completionNotes"
                            rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe the work completed..."
                        ></textarea>
                        @error('completionNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="actualCost" class="block text-sm font-medium text-gray-700 mb-2">Actual Cost</label>
                        <input
                            type="number"
                            id="actualCost"
                            wire:model="actualCost"
                            step="0.01"
                            placeholder="0.00"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('actualCost') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Completion Photos</label>
                        <input
                            type="file"
                            wire:model="completionPhotos"
                            multiple
                            accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                        @error('completionPhotos.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            wire:click="$set('showCompleteModal', false)"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Complete</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
