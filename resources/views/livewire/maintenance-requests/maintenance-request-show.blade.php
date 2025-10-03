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
                    @error('newUpdate')
                        <div class="bg-red-50 border border-red-200 rounded-md p-2 text-red-800 text-sm mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <input type="file"
                               wire:model="updatePhotos"
                               multiple
                               accept="image/*"
                               id="update-photo-upload"
                               class="text-sm">

                        <!-- Client-side error message -->
                        <div id="update-file-size-error" class="hidden bg-red-50 border border-red-200 rounded-md p-2 text-red-800 text-xs mt-2">
                            <div class="flex items-start">
                                <svg class="h-4 w-4 text-red-400 mr-1 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div id="update-file-size-error-message" class="flex-1"></div>
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div wire:loading wire:target="updatePhotos" class="flex items-center text-blue-600 text-sm mt-1">
                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading photos...
                        </div>

                        <!-- Upload Success -->
                        @if(count($updatePhotos) > 0)
                            <div wire:loading.remove wire:target="updatePhotos" id="update-success-message" class="flex items-center text-green-600 text-sm mt-1">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ count($updatePhotos) }} photo(s) ready
                            </div>
                        @endif

                        @if($canManage)
                            <label class="flex items-center mt-2">
                                <input type="checkbox" wire:model="isInternal" class="mr-2">
                                <span class="text-sm text-gray-600">Internal note (not visible to tenant)</span>
                            </label>
                        @endif
                    </div>

                    <button type="submit"
                            id="add-update-button"
                            wire:loading.attr="disabled"
                            wire:target="updatePhotos,addUpdate"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                        <span wire:loading.remove wire:target="updatePhotos,addUpdate">Add Update</span>
                        <span wire:loading wire:target="updatePhotos" class="flex items-center">
                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                        <span wire:loading wire:target="addUpdate">Saving...</span>
                    </button>
                </div>

                <p class="text-xs text-gray-500 mt-2">
                    Maximum file size: 4MB per image
                </p>
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

{{-- <!-- Inline Script - No @push/@stack needed --> --}}
<script>
(function() {
    'use strict';

    const MAX_FILE_SIZE_MB = 5;
    const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;
    const MAX_TOTAL_SIZE_MB = 30; // Total upload limit
    const MAX_TOTAL_SIZE_BYTES = MAX_TOTAL_SIZE_MB * 1024 * 1024;

    //console.log('🔍 Update photo validation script loaded');
    //console.log('📏 Limits: Individual=' + MAX_FILE_SIZE_MB + 'MB, Total=' + MAX_TOTAL_SIZE_MB + 'MB');

    function setupValidation() {
        const input = document.getElementById('update-photo-upload');

        if (!input) {
            //console.log('⚠️ Update photo input not found');
            return;
        }

        //console.log('✅ Update photo input found, setting up validation');

        // Validate files BEFORE Livewire processes them
        input.addEventListener('change', function(e) {
            //console.log('📁 Update files selected:', e.target.files.length);

            const files = Array.from(e.target.files);
            let totalSize = 0;
            const oversizedFiles = [];

            files.forEach(file => {
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                //console.log(`  - ${file.name}: ${sizeMB}MB`);
                totalSize += file.size;

                if (file.size > MAX_FILE_SIZE_BYTES) {
                    oversizedFiles.push({ name: file.name, size: sizeMB });
                }
            });

            const totalSizeMB = (totalSize / 1024 / 1024).toFixed(2);
            //console.log('📊 Total size: ' + totalSizeMB + 'MB');

            const errorDiv = document.getElementById('update-file-size-error');
            const errorMessage = document.getElementById('update-file-size-error-message');
            const successMessage = document.getElementById('update-success-message');
            let message = '';
            let hasError = false;

            // Check individual file sizes
            if (oversizedFiles.length > 0) {
                //console.log('❌ Individual files exceed ' + MAX_FILE_SIZE_MB + 'MB:', oversizedFiles.length);
                hasError = true;
                message = '<p class="font-semibold mb-1">The following file(s) exceed ' + MAX_FILE_SIZE_MB + 'MB:</p>';
                message += '<ul class="list-disc list-inside space-y-0.5">';
                oversizedFiles.forEach(file => {
                    message += '<li><strong>' + file.name + '</strong> (' + file.size + 'MB)</li>';
                });
                message += '</ul>';
            }

            // Check total size
            if (totalSize > MAX_TOTAL_SIZE_BYTES) {
                //console.log('❌ Total size exceeds ' + MAX_TOTAL_SIZE_MB + 'MB limit');
                hasError = true;
                if (message) message += '<div class="my-2 border-t border-red-300"></div>';
                message += '<p class="font-semibold mb-1">⚠️ Total upload size too large</p>';
                message += '<p class="mb-1">Selected: <strong>' + totalSizeMB + 'MB</strong>, Limit: <strong>' + MAX_TOTAL_SIZE_MB + 'MB</strong></p>';
                message += '<p class="text-xs">Please upload fewer files or compress them.</p>';
            }

            if (hasError) {
                // Clear input to prevent Livewire from processing
                e.target.value = '';

                message += '<p class="text-xs mt-2 pt-2 border-t border-red-300"><strong>💡 Tip:</strong> Use <a href="https://tinypng.com" target="_blank" class="underline">TinyPNG</a> to compress images.</p>';

                if (errorDiv && errorMessage) {
                    errorMessage.innerHTML = message;
                    errorDiv.classList.remove('hidden');
                }

                // Hide success message
                if (successMessage) {
                    successMessage.style.display = 'none';
                }

                // Stop propagation
                e.stopImmediatePropagation();
                return false;
            } else {
                //console.log('✅ All update files are valid');
                // Hide error on valid files
                if (errorDiv) {
                    errorDiv.classList.add('hidden');
                }
            }
        }, true); // Capture phase
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupValidation);
    } else {
        setupValidation();
    }

    // Handle 413 errors - Multiple approaches for better compatibility

    // Approach 1: Livewire hook (Livewire 3 style)
    document.addEventListener('livewire:init', function() {
        //console.log('🔌 Livewire initialized for updates, setting up 413 error handler');

        Livewire.hook('request', ({ fail }) => {
            fail(({ status, content, preventDefault }) => {
                //console.log('🚨 Livewire update request failed - Status:', status);

                if (status === 413) {
                    //console.log('❌ 413 Error caught by Livewire hook (updates)');
                    preventDefault();
                    show413ErrorUpdate();
                }
            });
        });

        //console.log('✅ Livewire hook registered for updates');
    });

    // Approach 2: Intercept fetch globally (backup method)
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args)
            .then(response => {
                if (response.status === 413) {
                    //console.log('❌ 413 Error caught by fetch interceptor (updates)');
                    show413ErrorUpdate();
                }
                return response;
            })
            .catch(error => {
                //console.log('❌ Fetch error:', error);
                throw error;
            });
    };

    // Approach 3: Monitor Livewire file uploads specifically
    document.addEventListener('livewire:upload-error', function(event) {
        //console.log('❌ Livewire upload error event (updates):', event.detail);
        if (event.detail && event.detail.status === 413) {
            show413ErrorUpdate();
        }
    });

    // Function to display 413 error
    function show413ErrorUpdate() {
        const errorDiv = document.getElementById('update-file-size-error');
        const errorMessage = document.getElementById('update-file-size-error-message');
        const successMessage = document.getElementById('update-success-message');

        if (errorDiv && errorMessage) {
            errorMessage.innerHTML =
                '<p class="font-semibold mb-1">⚠️ Server Error 413: Request Too Large</p>' +
                '<p class="mb-1">The server rejected the upload. Total size is too large.</p>' +
                '<ul class="list-disc list-inside text-xs space-y-0.5 mb-1">' +
                '<li>Total must be under ' + MAX_TOTAL_SIZE_MB + 'MB</li>' +
                '<li>Individual files under ' + MAX_FILE_SIZE_MB + 'MB</li>' +
                '</ul>' +
                '<p class="text-xs"><strong>💡 Solution:</strong> Upload fewer files or compress with <a href="https://tinypng.com" target="_blank" class="underline">TinyPNG</a>.</p>';

            errorDiv.classList.remove('hidden');
            //console.log('✅ 413 Error message displayed for updates');
        }

        // Hide success message
        if (successMessage) {
            successMessage.style.display = 'none';
        }

        // Clear file input
        const fileInput = document.getElementById('update-photo-upload');
        if (fileInput) {
            fileInput.value = '';
            //console.log('🧹 Update file input cleared');
        }

        // Try to clear Livewire component property
        try {
            const wireElement = document.querySelector('[wire\\:id]');
            if (wireElement) {
                const wireId = wireElement.getAttribute('wire:id');
                const component = Livewire.find(wireId);
                if (component) {
                    component.set('updatePhotos', []);
                    //console.log('🧹 Livewire updatePhotos property cleared');
                } else {
                    //console.log('⚠️ Livewire component not found');
                }
            } else {
                //console.log('⚠️ No element with wire:id found');
            }
        } catch (e) {
            //console.log('⚠️ Could not clear Livewire property:', e.message);
        }
    }
})();
</script>
