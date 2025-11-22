{{-- This is Vendor view maintenance request details page --}}
<div>{{-- Beginning root Livewire div HERE --}}
    <div class="flex flex-col lg:flex-row lg:items-start gap-6">
        <!-- Main Content -->
        <main class="flex-1 space-y-6">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $maintenanceRequest->title }}</h1>
                        <p class="text-gray-600 mt-1">Request #{{ substr($maintenanceRequest->id, 0, 8) }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $maintenanceRequest->priority_color ?? 'gray' }}-100 text-{{ $maintenanceRequest->priority_color ?? 'gray' }}-800">
                            {{ ucfirst($maintenanceRequest->priority) }} Priority
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $maintenanceRequest->status_color ?? 'blue' }}-100 text-{{ $maintenanceRequest->status_color ?? 'blue' }}-800">
                            {{ $maintenanceRequest->status_label }}
                        </span>
                    </div>
                </div>

                <!-- Request Info -->
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
                        <label class="block text-sm font-medium text-gray-500">Submitted</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    @if($maintenanceRequest->estimated_cost)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Estimated Cost</label>
                            <p class="mt-1 text-sm text-gray-900">${{ number_format($maintenanceRequest->estimated_cost, 2) }}</p>
                        </div>
                    @endif
                </div>

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

                <!-- Status Actions -->
                @if($maintenanceRequest->status === 'pending_acceptance')
                    {{-- NEW: Pending Acceptance UI --}}
                    <div class="mt-6 pt-6 border-t">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-semibold text-yellow-900">Assignment Pending Your Acceptance</h3>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        Please review the request details carefully and decide if you can take this job.
                                        If you accept, you'll be able to start work. If you need to decline, please provide a reason to help the property manager.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <!-- Accept Button -->
                            <button wire:click="acceptAssignment"
                                    wire:loading.attr="disabled"
                                    onclick="return confirm('Are you sure you want to accept this assignment? You will be committed to completing this work.')"
                                    class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold py-3 px-6 rounded-lg transition-colors shadow-sm flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span wire:loading.remove wire:target="acceptAssignment">Accept Assignment</span>
                                <span wire:loading wire:target="acceptAssignment">Accepting...</span>
                            </button>

                            <!-- Reject Button -->
                            <button wire:click="$set('showRejectModal', true)"
                                    type="button"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors shadow-sm flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reject Assignment
                            </button>
                        </div>
                    </div>

                @elseif($maintenanceRequest->status === 'assigned')
                    <div class="mt-6 pt-6 border-t">
                        <button wire:click="updateStatus('in_progress')"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Start Work
                        </button>
                    </div>
                @elseif($maintenanceRequest->status === 'in_progress')
                    <div class="mt-6 pt-6 border-t">
                        <button wire:click="updateStatus('completed')"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Mark as Completed
                        </button>
                    </div>
                @endif
            </div>

            <!-- Updates/Timeline -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Updates & Timeline</h2>

                <!-- Add Update Form -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <form wire:submit.prevent="addUpdate">
                        <div class="mb-4">
                            <textarea wire:model="message" rows="3"
                                      placeholder="Add an update or comment..."
                                      class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                            @error('message')
                                <div class="bg-red-50 border border-red-200 rounded-md p-2 text-red-800 text-sm mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <input type="file"
                                       wire:model="photos"
                                       multiple
                                       accept="image/*"
                                       id="vendor-photo-upload"
                                       class="text-sm">

                                <!-- Client-side error message -->
                                <div id="vendor-file-size-error" class="hidden bg-red-50 border border-red-200 rounded-md p-2 text-red-800 text-xs mt-2">
                                    <div class="flex items-start">
                                        <svg class="h-4 w-4 text-red-400 mr-1 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        <div id="vendor-file-size-error-message" class="flex-1"></div>
                                    </div>
                                </div>

                                <!-- Upload Progress -->
                                <div wire:loading wire:target="photos" class="flex items-center text-blue-600 text-sm mt-1">
                                    <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Uploading photos...
                                </div>
                                <a class="text-xs text-gray-500 mt-2 block">
                                    Maximum file size: 5MB per image. Total limit: 30MB
                                </a>

                                <!-- Upload Success -->
                                <div wire:loading.remove wire:target="photos" id="vendor-success-indicator">
                                    @if(is_array($photos) && count($photos) > 0)
                                        <div id="vendor-success-message" class="flex items-center text-green-600 text-sm mt-1">
                                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ count($photos) }} photo(s) uploaded - Ready to add
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4">
                                    <button type="submit"
                                            id="vendor-add-update-button"
                                            wire:loading.attr="disabled"
                                            wire:target="photos,addUpdate"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:bg-gray-400 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="addUpdate">Add Update</span>
                                        <span wire:loading wire:target="addUpdate">Adding...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Timeline Events -->
                <div class="space-y-6">
                    @forelse($updates as $update)
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                @if($update->update_type === 'comment')
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-100">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </span>
                                @elseif($update->update_type === 'status_change')
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-100">
                                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="font-semibold text-gray-900">{{ $update->user->name ?? 'System' }}</p>
                                    <span class="text-xs text-gray-500">{{ $update->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $update->message }}</p>

                                @if($update->photos && count($update->photos) > 0)
                                    <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2">
                                        @foreach($update->photos as $photo)
                                            <img src="{{ Storage::url($photo) }}" alt="Update photo"
                                                 class="w-full h-24 object-cover rounded-lg cursor-pointer"
                                                 onclick="window.open('{{ Storage::url($photo) }}', '_blank')">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-6">No updates yet.</p>
                    @endforelse

                    <!-- Pagination -->
                    @if($updates->hasPages())
                        <div class="mt-6">
                            {{ $updates->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>

        <!-- Sidebar -->
        <aside class="lg:w-80 space-y-6">
            <!-- Contact Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>

                @if($maintenanceRequest->tenant)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500">Tenant</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->tenant->name }}</p>
                        @if($maintenanceRequest->tenant->phone)
                            <p class="mt-1 text-sm text-blue-600">{{ $maintenanceRequest->tenant->phone }}</p>
                        @endif
                        @if($maintenanceRequest->tenant->email)
                            <p class="mt-1 text-sm text-blue-600">{{ $maintenanceRequest->tenant->email }}</p>
                        @endif
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-500">Property Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->property->address }}</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Details</h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Category</label>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst($maintenanceRequest->category) }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Priority</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $maintenanceRequest->priority_color }}-100 text-{{ $maintenanceRequest->priority_color }}-800">
                                {{ ucfirst($maintenanceRequest->priority) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Status</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $maintenanceRequest->status_color }}-100 text-{{ $maintenanceRequest->status_color }}-800">
                                {{ $maintenanceRequest->status_label }}
                            </span>
                        </p>
                    </div>
                    @if($maintenanceRequest->assigned_at)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Assigned</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->assigned_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                    @if($maintenanceRequest->accepted_at)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Accepted</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->accepted_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </aside>
    </div>

    <!-- Rejection Modal -->
    @if($showRejectModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showRejectModal') }">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Overlay -->
            <div x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                 @click="$wire.set('showRejectModal', false)"></div>

            <!-- Modal Content -->
            <div x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full max-w-lg my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">

                <!-- Header -->
                <div class="px-6 py-4 bg-red-50 border-b border-red-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-red-900">Reject Assignment</h3>
                        <button wire:click="$set('showRejectModal', false)" class="text-red-400 hover:text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-600 mb-4">
                        Please select a reason for rejecting this assignment. This will help the property manager find another vendor quickly.
                    </p>

                    <!-- Preset Reasons -->
                    <div class="space-y-2 mb-4">
                        <label class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" wire:model="rejectionReason" value="too_busy"
                                   class="mt-1 mr-3 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Currently too busy / Fully booked</span>
                                <p class="text-xs text-gray-500">You don't have capacity to take on this work</p>
                            </div>
                        </label>

                        <label class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" wire:model="rejectionReason" value="out_of_area"
                                   class="mt-1 mr-3 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Property location outside service area</span>
                                <p class="text-xs text-gray-500">This location is too far from your service area</p>
                            </div>
                        </label>

                        <label class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" wire:model="rejectionReason" value="lacks_expertise"
                                   class="mt-1 mr-3 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Requires specialized expertise</span>
                                <p class="text-xs text-gray-500">This work requires skills you don't have</p>
                            </div>
                        </label>

                        <label class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" wire:model="rejectionReason" value="emergency_unavailable"
                                   class="mt-1 mr-3 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Cannot handle emergency priority</span>
                                <p class="text-xs text-gray-500">Unable to respond to emergency timing</p>
                            </div>
                        </label>

                        <label class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" wire:model="rejectionReason" value="insufficient_info"
                                   class="mt-1 mr-3 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Insufficient information</span>
                                <p class="text-xs text-gray-500">Need more details to assess the job</p>
                            </div>
                        </label>

                        <label class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" wire:model="rejectionReason" value="other"
                                   class="mt-1 mr-3 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Other reason</span>
                                <p class="text-xs text-gray-500">Specify in the notes below</p>
                            </div>
                        </label>
                    </div>

                    @error('rejectionReason')
                        <p class="text-red-600 text-sm mb-3 bg-red-50 border border-red-200 rounded-md p-2">{{ $message }}</p>
                    @enderror

                    <!-- Additional Notes -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea wire:model="rejectionNotes" rows="3"
                                  placeholder="Provide any additional details that might help..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Max 500 characters</p>
                        @error('rejectionNotes')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t flex gap-3">
                    <button wire:click="confirmRejection"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        <span wire:loading.remove wire:target="confirmRejection">Confirm Rejection</span>
                        <span wire:loading wire:target="confirmRejection">Rejecting...</span>
                    </button>
                    <button wire:click="$set('showRejectModal', false)"
                            type="button"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Completion Modal -->
    @if($showCompleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showCompleteModal') }">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Backdrop click to close -->
            <div class="fixed inset-0"
                 @click="$wire.set('showCompleteModal', false)"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full mx-auto z-10">
                <!-- Header -->
                <div class="bg-green-50 px-6 py-4 border-b border-green-100 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-full p-2 mr-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-green-900">Complete Work</h3>
                        </div>
                        <button wire:click="$set('showCompleteModal', false)" class="text-green-400 hover:text-green-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-4 space-y-4">
                    <p class="text-sm text-gray-600">
                        Please provide details about the completed work.
                    </p>

                    <!-- Completion Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Completion Notes <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="completionNotes" rows="4"
                                  placeholder="Describe the work completed, any parts used, and other relevant details..."
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"></textarea>
                        @error('completionNotes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actual Cost -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Actual Cost (Optional)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" wire:model="actualCost" step="0.01" min="0"
                                   placeholder="0.00"
                                   class="w-full border border-gray-300 rounded-md pl-7 pr-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        @error('actualCost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($maintenanceRequest->estimated_cost)
                            <p class="mt-1 text-xs text-gray-500">
                                Estimated cost: ${{ number_format($maintenanceRequest->estimated_cost, 2) }}
                            </p>
                        @endif
                    </div>

                    <!-- Completion Photos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Completion Photos (Optional)
                        </label>
                        <input type="file" wire:model="completionPhotos" multiple accept="image/*"
                               id="completion-photo-upload"
                               class="text-sm w-full border border-gray-300 rounded-md px-3 py-2">

                        <!-- Upload Progress -->
                        <div wire:loading wire:target="completionPhotos" class="flex items-center text-blue-600 text-sm mt-1">
                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading photos...
                        </div>

                        @error('completionPhotos.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Maximum 5MB per image</p>

                        <!-- Photo Preview -->
                        @if($completionPhotos && count($completionPhotos) > 0)
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($completionPhotos as $photo)
                                    <div class="relative">
                                        <img src="{{ $photo->temporaryUrl() }}"
                                             class="h-16 w-16 object-cover rounded-lg border">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t rounded-b-lg flex gap-3">
                    <button wire:click="completeWork"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        <span wire:loading.remove wire:target="completeWork">Mark as Completed</span>
                        <span wire:loading wire:target="completeWork">Completing...</span>
                    </button>
                    <button wire:click="$set('showCompleteModal', false)"
                            type="button"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>{{-- End root Livewire div HERE --}}

<!-- Success/Error Messages -->
@if (session()->has('message'))
    <div class="fixed bottom-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-800 font-medium">{{ session('message') }}</p>
        </div>
    </div>
@endif

@if (session()->has('error'))
    <div class="fixed bottom-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
@endif

<script>
(function() {
    'use strict';

    const MAX_FILE_SIZE_MB = 5;
    const MAX_TOTAL_SIZE_MB = 30;
    const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;
    const MAX_TOTAL_SIZE_BYTES = MAX_TOTAL_SIZE_MB * 1024 * 1024;

    let isUploading = false;
    let hasUploadError = false;

    function toggleButton(buttonId, disable, reason) {
        const button = document.getElementById(buttonId);
        if (!button) return;

        if (disable) {
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
            console.log('🔒 Button DISABLED (' + reason + ')');
        } else {
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            console.log('🔓 Button ENABLED (' + reason + ')');
        }
    }

    function validateFileInput(inputId, errorDivId, errorMessageId, successMessageId, buttonId) {
        const input = document.getElementById(inputId);
        if (!input) {
            console.log('⚠️ Input not found:', inputId);
            return;
        }

        console.log('✅ Setting up validation for:', inputId);

        input.addEventListener('change', function(e) {
            console.log('📁 Files selected:', e.target.files.length);

            const files = Array.from(e.target.files);
            let totalSize = 0;
            const oversizedFiles = [];

            files.forEach(file => {
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                console.log(`  - ${file.name}: ${sizeMB}MB`);
                totalSize += file.size;

                if (file.size > MAX_FILE_SIZE_BYTES) {
                    oversizedFiles.push({ name: file.name, size: sizeMB });
                }
            });

            const totalSizeMB = (totalSize / 1024 / 1024).toFixed(2);
            console.log('📊 Total size: ' + totalSizeMB + 'MB');

            const errorDiv = document.getElementById(errorDivId);
            const errorMessage = document.getElementById(errorMessageId);
            const successMessage = document.getElementById(successMessageId);
            let message = '';
            let hasError = false;

            if (oversizedFiles.length > 0) {
                console.log('❌ Individual files exceed ' + MAX_FILE_SIZE_MB + 'MB');
                hasError = true;
                message = '<p class="font-semibold mb-1">Files exceed ' + MAX_FILE_SIZE_MB + 'MB:</p><ul class="list-disc list-inside space-y-0.5">';
                oversizedFiles.forEach(file => {
                    message += '<li><strong>' + file.name + '</strong> (' + file.size + 'MB)</li>';
                });
                message += '</ul>';
            }

            if (totalSize > MAX_TOTAL_SIZE_BYTES) {
                console.log('❌ Total exceeds ' + MAX_TOTAL_SIZE_MB + 'MB');
                hasError = true;
                if (message) message += '<div class="my-1 border-t border-red-300"></div>';
                message += '<p class="font-semibold mb-1">⚠️ Total too large</p><p>Selected: ' + totalSizeMB + 'MB, Limit: ' + MAX_TOTAL_SIZE_MB + 'MB</p>';
            }

            if (hasError) {
                hasUploadError = true;
                e.target.value = '';
                message += '<p class="text-xs mt-1 pt-1 border-t border-red-300">💡 Compress with <a href="https://tinypng.com" target="_blank" class="underline">TinyPNG</a></p>';

                if (errorDiv && errorMessage) {
                    errorMessage.innerHTML = message;
                    errorDiv.classList.remove('hidden');
                }
                if (successMessage) successMessage.style.display = 'none';

                toggleButton(buttonId, true, 'validation failed');
                e.stopImmediatePropagation();
                return false;
            } else {
                console.log('✅ Validation passed');
                hasUploadError = false;
                isUploading = true;
                if (errorDiv) errorDiv.classList.add('hidden');
                toggleButton(buttonId, true, 'upload in progress');
            }
        }, true);
    }

    function setup() {
        validateFileInput('vendor-photo-upload', 'vendor-file-size-error', 'vendor-file-size-error-message', 'vendor-success-message', 'vendor-add-update-button');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setup);
    } else {
        setup();
    }

    document.addEventListener('livewire:init', function() {
        console.log('🔌 Livewire init - vendor view');

        document.addEventListener('livewire:upload-start', function(event) {
            console.log('📤 Upload started (event)');
            isUploading = true;
            toggleButton('vendor-add-update-button', true, 'upload in progress');
        });

        document.addEventListener('livewire:upload-finish', function(event) {
            console.log('✅ Upload finished (event)');
            isUploading = false;
            if (!hasUploadError) {
                toggleButton('vendor-add-update-button', false, 'upload complete');
            }
        });

        document.addEventListener('livewire:upload-error', function(event) {
            console.log('❌ Upload error (event):', event.detail);
            isUploading = false;
            hasUploadError = true;
            toggleButton('vendor-add-update-button', true, 'upload error');
        });

        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 413) {
                    console.log('❌ 413 Error caught');
                    preventDefault();
                    hasUploadError = true;
                    isUploading = false;

                    const errorDiv = document.getElementById('vendor-file-size-error');
                    const errorMessage = document.getElementById('vendor-file-size-error-message');
                    if (errorDiv && errorMessage) {
                        errorMessage.innerHTML = '<p class="font-semibold mb-1">⚠️ 413: Too Large</p><p>Server rejected. Total > ' + MAX_TOTAL_SIZE_MB + 'MB</p><p class="text-xs mt-1">💡 Upload fewer files or compress</p>';
                        errorDiv.classList.remove('hidden');
                    }

                    toggleButton('vendor-add-update-button', true, '413 error');
                }
            });
        });
    });
})();
</script>
