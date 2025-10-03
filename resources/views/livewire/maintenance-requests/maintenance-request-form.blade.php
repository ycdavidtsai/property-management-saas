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
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-700">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- New Photos Upload -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                <input type="file"
                       wire:model="newPhotos"
                       multiple
                       accept="image/*"
                       id="photo-upload"
                       class="w-full border border-gray-300 rounded-md px-3 py-2">

                <!-- Client-side error message -->
                <div id="file-size-error" class="hidden bg-red-50 border border-red-200 rounded-md p-3 text-red-800 text-sm mt-3">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-red-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div id="file-size-error-message" class="flex-1"></div>
                    </div>
                </div>

                <!-- Upload Status Messages -->
                <div class="mt-3 space-y-2">
                    <!-- Loading indicator with spinner -->
                    <div wire:loading wire:target="newPhotos" class="flex items-center justify-center text-blue-600">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="font-medium">Uploading files, please wait...</span>
                    </div>

                    <!-- Success indicator -->
                    @if(count($newPhotos) > 0)
                        <div wire:loading.remove wire:target="newPhotos" id="upload-success-message" class="flex items-center justify-center text-green-600">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">{{ count($newPhotos) }} file(s) ready to upload</span>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-md p-3 text-red-800 text-sm">
                            <div class="font-semibold mb-1">Upload Error:</div>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @error('newPhotos.*')
                        <div class="bg-red-50 border border-red-200 rounded-md p-3 text-red-800 text-sm">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Help Text -->
                <p class="text-xs text-gray-500 mt-3">
                    Maximum file size: 4MB per image. Supported formats: JPG, PNG, GIF
                </p>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('maintenance-requests.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
            <button type="submit"
                    id="submit-button"
                    wire:loading.attr="disabled"
                    wire:target="newPhotos,save"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="newPhotos,save">
                    {{ $isEditing ? 'Update Request' : 'Submit Request' }}
                </span>
                <span wire:loading wire:target="newPhotos" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing files...
                </span>
                <span wire:loading wire:target="save">
                    Saving...
                </span>
            </button>
        </div>
    </form>
</div>

{{-- <!-- Inline Script - No @push/@stack needed --> --}}
<script>
(function() {
    'use strict';

    const MAX_FILE_SIZE_MB = 5;
    const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;
    const MAX_TOTAL_SIZE_MB = 30; // Total upload limit
    const MAX_TOTAL_SIZE_BYTES = MAX_TOTAL_SIZE_MB * 1024 * 1024;

    //console.log('🔍 File upload validation script loaded');
    //console.log('📏 Limits: Individual=' + MAX_FILE_SIZE_MB + 'MB, Total=' + MAX_TOTAL_SIZE_MB + 'MB');

    function setupValidation() {
        const input = document.getElementById('photo-upload');

        if (!input) {
            //console.log('⚠️ Photo upload input not found');
            return;
        }

        //console.log('✅ Photo upload input found, setting up validation');

        // Validate files BEFORE Livewire processes them
        input.addEventListener('change', function(e) {
            //console.log('📁 Files selected:', e.target.files.length);

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

            const errorDiv = document.getElementById('file-size-error');
            const errorMessage = document.getElementById('file-size-error-message');
            let message = '';
            let hasError = false;

            // Check individual file sizes
            if (oversizedFiles.length > 0) {
                //console.log('❌ Individual files exceed ' + MAX_FILE_SIZE_MB + 'MB:', oversizedFiles.length);
                hasError = true;
                message = '<p class="font-semibold mb-2">The following file(s) exceed the ' + MAX_FILE_SIZE_MB + 'MB limit:</p>';
                message += '<ul class="list-disc list-inside space-y-1 mb-2">';
                oversizedFiles.forEach(file => {
                    message += '<li><strong>' + file.name + '</strong> (' + file.size + 'MB)</li>';
                });
                message += '</ul>';
            }

            // Check total size
            if (totalSize > MAX_TOTAL_SIZE_BYTES) {
                //console.log('❌ Total size exceeds ' + MAX_TOTAL_SIZE_MB + 'MB limit');
                hasError = true;
                if (message) message += '<div class="mt-3 pt-3 border-t border-red-300"></div>';
                message += '<p class="font-semibold mb-2">⚠️ Total upload size too large</p>';
                message += '<p class="mb-2">You selected <strong>' + totalSizeMB + 'MB</strong> but the limit is <strong>' + MAX_TOTAL_SIZE_MB + 'MB</strong>.</p>';
                message += '<p class="text-xs">Please upload fewer files or compress them first.</p>';
            }

            if (hasError) {
                // Clear the input to prevent Livewire from processing
                e.target.value = '';

                message += '<p class="text-xs mt-3 pt-3 border-t border-red-300"><strong>💡 Tip:</strong> Compress your images using <a href="https://tinypng.com" target="_blank" class="underline font-medium">TinyPNG</a> to reduce file sizes.</p>';

                if (errorDiv && errorMessage) {
                    errorMessage.innerHTML = message;
                    errorDiv.classList.remove('hidden');
                }

                // Stop event propagation to prevent Livewire from seeing this
                e.stopImmediatePropagation();
                return false;
            } else {
                //console.log('✅ All validations passed');
                // Hide error if validation passes
                if (errorDiv) {
                    errorDiv.classList.add('hidden');
                }
            }
        }, true); // Use capture phase to intercept before Livewire
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupValidation);
    } else {
        setupValidation();
    }

    // Also run when Livewire initializes
    document.addEventListener('livewire:init', function() {
        //console.log('🔌 Livewire initialized, setting up 413 error handler');

        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                //console.log('📡 Request failed with status:', status);

                if (status === 413) {
                    //console.log('❌ 413 Error detected - Request Entity Too Large');
                    preventDefault();

                    const errorDiv = document.getElementById('file-size-error');
                    const errorMessage = document.getElementById('file-size-error-message');
                    const successMessage = document.getElementById('upload-success-message');

                    if (errorDiv && errorMessage) {
                        errorMessage.innerHTML =
                            '<p class="font-semibold mb-2">⚠️ Server Error 413: Request Too Large</p>' +
                            '<p class="mb-2">The server rejected your upload because the file(s) are too large.</p>' +
                            '<p class="text-xs mb-1"><strong>This can happen when:</strong></p>' +
                            '<ul class="list-disc list-inside text-xs mb-2">' +
                            '<li>Individual files exceed 5MB</li>' +
                            '<li>Total upload size exceeds server limits</li>' +
                            '<li>Server configuration is more restrictive</li>' +
                            '</ul>' +
                            '<p class="text-xs"><strong>💡 Solution:</strong> Compress your images before uploading using <a href="https://tinypng.com" target="_blank" class="underline font-medium">TinyPNG</a>.</p>';

                        errorDiv.classList.remove('hidden');
                        //console.log('✅ Error message displayed');
                    }

                    // Hide success message if visible
                    if (successMessage) {
                        successMessage.style.display = 'none';
                    }

                    // Clear file input
                    const fileInput = document.getElementById('photo-upload');
                    if (fileInput) {
                        fileInput.value = '';
                        //console.log('🧹 File input cleared');
                    }

                    // Try to clear Livewire component property
                    try {
                        const wireElement = document.querySelector('[wire\\:id]');
                        if (wireElement) {
                            const wireId = wireElement.getAttribute('wire:id');
                            const component = Livewire.find(wireId);
                            if (component) {
                                component.set('newPhotos', []);
                                //console.log('🧹 Livewire newPhotos property cleared');
                            }
                        }
                    } catch (e) {
                        //console.log('⚠️ Could not clear Livewire property:', e.message);
                    }
                }
            });
        });
    });
})();
</script>
