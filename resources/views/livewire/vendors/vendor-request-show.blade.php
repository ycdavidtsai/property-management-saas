{{-- This is Vendor view maintenance request details page --}}
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
                            {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
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
                @if($maintenanceRequest->status === 'assigned')
                    <div class="mt-6 pt-6 border-t">
                        <button wire:click="updateStatus('in_progress')"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Start Work
                        </button>
                    </div>
                @elseif($maintenanceRequest->status === 'in_progress')
                    <div class="mt-6 pt-6 border-t">
                        <button wire:click="updateStatus('completed')"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
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
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                                        <span wire:loading.remove wire:target="photos,addUpdate">Add Update</span>
                                        <span wire:loading wire:target="photos" class="flex items-center">
                                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Processing...
                                        </span>
                                        <span wire:loading wire:target="addUpdate">Saving...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Updates List -->
                <div class="space-y-4">
                    @forelse($updates as $update)
                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $update->user->name }}</span>
                                    @if($update->update_type !== 'comment')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                            {{ ucfirst(str_replace('_', ' ', $update->update_type)) }}
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
                    @empty
                        <p class="text-gray-500 text-center py-4">No updates yet.</p>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($updates->hasPages())
                    <div class="mt-4">
                        {{ $updates->links() }}
                    </div>
                @endif
            </div>
        </main>

        <!-- Sidebar -->
        <aside class="w-full lg:w-1/4 space-y-6 lg:sticky lg:top-10">
            <!-- Property Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="font-semibold mb-3">Property Information</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Property: {{ $maintenanceRequest->property->name }}</dt>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Address: {{ $maintenanceRequest->property->address }}</dt>
                        </div>
                        @if($maintenanceRequest->unit)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Unit: {{ $maintenanceRequest->unit->unit_number }}</dt>
                                <dt class="text-sm font-medium text-gray-500">Name: {{ $maintenanceRequest->tenant->name }}</dt>
                                <dt class="text-sm font-medium text-gray-500">Phone: {{ $maintenanceRequest->tenant->phone }}</dt>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category: {{ ucfirst($maintenanceRequest->category) }}</dt>
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
                            <p class="text-sm text-gray-600">{{ $maintenanceRequest->assignedBy->phone }}</p>
                            <p class="text-sm text-gray-600">{{ $maintenanceRequest->assignedBy->email }}</p>
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
        </aside>
    </div>


    <!-- Complete Work Modal -->
    @if($showCompleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Complete Work</h3>

                <form wire:submit.prevent="completeWork">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Completion Notes *</label>
                        <textarea wire:model="completionNotes" rows="4"
                                  placeholder="Describe the work completed, parts used, etc."
                                  class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                        @error('completionNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Actual Cost</label>
                        <input type="number" step="0.01" wire:model="actualCost"
                               placeholder="0.00"
                               class="w-full border border-gray-300 rounded-md px-3 py-2">
                        @error('actualCost') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Completion Photos</label>
                        <input type="file"
                               wire:model="completionPhotos"
                               multiple
                               accept="image/*"
                               id="completion-photo-upload"
                               class="w-full border border-gray-300 rounded-md px-3 py-2">

                        <!-- Client-side error message -->
                        <div id="completion-file-size-error" class="hidden bg-red-50 border border-red-200 rounded-md p-2 text-red-800 text-xs mt-2">
                            <div class="flex items-start">
                                <svg class="h-4 w-4 text-red-400 mr-1 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div id="completion-file-size-error-message" class="flex-1"></div>
                            </div>
                        </div>

                        <div wire:loading wire:target="completionPhotos" class="flex items-center text-blue-600 text-sm mt-1">
                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading photos...
                        </div>

                        @if(is_array($completionPhotos) && count($completionPhotos) > 0)
                            <div wire:loading.remove wire:target="completionPhotos" class="text-green-600 text-sm mt-1">
                                ✓ {{ count($completionPhotos) }} photo(s) ready
                            </div>
                        @endif

                        @error('completionPhotos.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showCompleteModal', false)"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit"
                                id="complete-work-button"
                                wire:loading.attr="disabled"
                                wire:target="completionPhotos,completeWork"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                            <span wire:loading.remove wire:target="completionPhotos,completeWork">Complete Work</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<!-- Inline Script for File Validation -->
<script>
(function() {
    'use strict';

    const MAX_FILE_SIZE_MB = 5;
    const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;
    const MAX_TOTAL_SIZE_MB = 30;
    const MAX_TOTAL_SIZE_BYTES = MAX_TOTAL_SIZE_MB * 1024 * 1024;

    let hasUploadError = false;
    let isUploading = false;

    console.log('🔍 Vendor file upload validation loaded');
    console.log('📏 Limits: Individual=' + MAX_FILE_SIZE_MB + 'MB, Total=' + MAX_TOTAL_SIZE_MB + 'MB');

    function toggleButton(buttonId, disable, reason) {
        const button = document.getElementById(buttonId);
        console.log('🎯 Toggle ' + buttonId + ' - disable:', disable, 'reason:', reason);

        if (button) {
            button.disabled = disable;
            if (disable) {
                button.classList.add('opacity-50', 'cursor-not-allowed', '!bg-gray-400');
                button.classList.remove('hover:bg-blue-700', 'hover:bg-green-700');
                console.log('🔒 Button DISABLED (' + reason + ')');
            } else {
                button.classList.remove('opacity-50', 'cursor-not-allowed', '!bg-gray-400');
                button.classList.add('hover:bg-blue-700');
                console.log('🔓 Button ENABLED (' + reason + ')');
            }
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
        validateFileInput('completion-photo-upload', 'completion-file-size-error', 'completion-file-size-error-message', null, 'complete-work-button');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setup);
    } else {
        setup();
    }

    document.addEventListener('livewire:init', function() {
        console.log('🔌 Livewire init - vendor view');

        // Simpler approach: Listen for wire:upload-* events
        document.addEventListener('livewire:upload-start', function(event) {
            console.log('📤 Upload started (event)');
            isUploading = true;
            toggleButton('vendor-add-update-button', true, 'upload in progress');
            toggleButton('complete-work-button', true, 'upload in progress');
        });

        document.addEventListener('livewire:upload-finish', function(event) {
            console.log('✅ Upload finished (event)');
            isUploading = false;
            if (!hasUploadError) {
                toggleButton('vendor-add-update-button', false, 'upload complete');
                toggleButton('complete-work-button', false, 'upload complete');
            }
        });

        document.addEventListener('livewire:upload-error', function(event) {
            console.log('❌ Upload error (event):', event.detail);
            isUploading = false;
            hasUploadError = true;
            toggleButton('vendor-add-update-button', true, 'upload error');
            toggleButton('complete-work-button', true, 'upload error');
        });

        // Handle 413 errors
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 413) {
                    console.log('❌ 413 Error caught');
                    preventDefault();
                    hasUploadError = true;
                    isUploading = false;

                    ['vendor-file-size-error', 'completion-file-size-error'].forEach(errorDivId => {
                        const errorDiv = document.getElementById(errorDivId);
                        const errorMessage = document.getElementById(errorDivId + '-message');
                        if (errorDiv && errorMessage) {
                            errorMessage.innerHTML = '<p class="font-semibold mb-1">⚠️ 413: Too Large</p><p>Server rejected. Total > ' + MAX_TOTAL_SIZE_MB + 'MB</p><p class="text-xs mt-1">💡 Upload fewer files or compress</p>';
                            errorDiv.classList.remove('hidden');
                        }
                    });

                    toggleButton('vendor-add-update-button', true, '413 error');
                    toggleButton('complete-work-button', true, '413 error');
                }
            });
        });
    });
})();
</script>
