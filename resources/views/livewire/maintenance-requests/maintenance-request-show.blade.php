<div>
    <!-- Request Header Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $request->title }}</h3>
                        <span class="ml-4 px-3 py-1 text-sm font-semibold rounded-full
                            @if($request->status === 'completed') bg-green-100 text-green-800
                            @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($request->status === 'assigned') bg-yellow-100 text-yellow-800
                            @elseif($request->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </div>

                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $request->created_at->format('M d, Y') }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            {{ $request->property->name }}
                        </span>
                        @if($request->unit)
                            <span>Unit {{ $request->unit->unit_number }}</span>
                        @endif
                    </div>

                    <!-- Vendor Assignment Display for authorized roles -->

                        @if($request->assigned_vendor_id)
                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">Assigned to Vendor</p>
                                            <p class="text-lg font-semibold text-blue-700">{{ $request->vendor->name }}</p>
                                            @if($request->vendor->phone)
                                                <p class="text-sm text-blue-600">{{ $request->vendor->phone }}</p>
                                            @endif
                                            <p class="text-xs text-blue-500 mt-1">
                                                Assigned {{ $request->assigned_at->diffForHumans() }}
                                                @if($request->assignedBy)
                                                    by {{ $request->assignedBy->name }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    @if(App\Services\RoleService::roleHasPermission(auth()->user()->role, 'maintenance.view') && in_array(auth()->user()->role, ['admin', 'manager', 'landlord']))
                                        @can('update', $request)
                                            <div class="flex space-x-2">
                                                <button
                                                    wire:click="openAssignModal"
                                                    class="px-3 py-2 text-sm font-medium text-blue-700 bg-white border border-blue-300 rounded-md hover:bg-blue-50"
                                                >
                                                    Reassign
                                                </button>
                                                <button
                                                    wire:click="unassignVendor"
                                                    onclick="return confirm('Are you sure you want to unassign this vendor?')"
                                                    class="px-3 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-md hover:bg-red-50"
                                                >
                                                    Unassign
                                                </button>
                                            </div>
                                        @endcan
                                    @endif
                                </div>
                                @if($request->assignment_notes)
                                    <div class="mt-3 pt-3 border-t border-blue-200">
                                        <p class="text-sm font-medium text-blue-900">Assignment Notes:</p>
                                        <p class="text-sm text-blue-700 mt-1">{{ $request->assignment_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            @if(App\Services\RoleService::roleHasPermission(auth()->user()->role, 'maintenance.view') && in_array(auth()->user()->role, ['admin', 'manager', 'landlord']))
                                @can('update', $request)
                                    <div class="mt-4">
                                        <button
                                            wire:click="openAssignModal"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700"
                                        >
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Assign Vendor
                                        </button>
                                    </div>
                                @endcan
                            @endif
                        @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button
                    wire:click="setActiveTab('details')"
                    class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'details' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Details
                </button>
                <button
                    wire:click="setActiveTab('updates')"
                    class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'updates' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Timeline & Updates
                    @if($updates->total() > 0)
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $activeTab === 'updates' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $updates->total() }}
                        </span>
                    @endif
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'details')
                <!-- Details Tab Content -->
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Description</h4>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $request->description }}</p>
                    </div>

                    @if($request->photos && count($request->photos) > 0)
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Photos</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($request->photos as $photo)
                                    <img src="{{ Storage::url($photo) }}" alt="Request photo" class="rounded-lg shadow-md">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Request Information</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Priority</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($request->priority) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Category</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($request->category) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Submitted By</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $request->tenant->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Submitted On</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $request->created_at->format('M d, Y g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @else
                <!-- Updates/Timeline Tab -->
                <div>
                    <!-- Add Comment Form -->
                    <div class="mb-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Add Update</h4>
                        <form wire:submit.prevent="addComment">
                            <div class="space-y-3">
                                <div>
                                    <textarea
                                        wire:model="newComment"
                                        rows="3"
                                        placeholder="Add a comment or update..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    ></textarea>
                                    @error('newComment')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Photo Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Attach Photos (Optional)
                                    </label>
                                    <input
                                        type="file"
                                        wire:model="newCommentPhotos"
                                        multiple
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    >
                                    @error('newCommentPhotos.*')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror

                                    <!-- Upload Progress -->
                                    <div wire:loading wire:target="newCommentPhotos" class="flex items-center text-blue-600 text-sm mt-2">
                                        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Uploading photos...
                                    </div>

                                    <!-- Upload Success -->
                                    @if(count($newCommentPhotos) > 0)
                                        <div wire:loading.remove wire:target="newCommentPhotos" class="flex items-center text-green-600 text-sm mt-2">
                                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ count($newCommentPhotos) }} photo(s) ready
                                        </div>
                                    @endif

                                    <p class="text-xs text-gray-500 mt-1">
                                        Maximum file size: 5MB per image
                                    </p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <!-- Internal/Public Toggle (Admin/Manager/Landlord only) -->
                                    @if($canAddInternalNotes)
                                        <label class="flex items-center">
                                            <input
                                                type="checkbox"
                                                wire:model="isInternalComment"
                                                class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">
                                                Internal Note
                                                <span class="text-xs text-gray-500">(Manager only - not visible to tenant)</span>
                                            </span>
                                        </label>
                                    @else
                                        <div></div>
                                    @endif

                                    <button
                                        type="submit"
                                        wire:loading.attr="disabled"
                                        wire:target="newCommentPhotos,addComment"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <span wire:loading.remove wire:target="newCommentPhotos,addComment">Add Update</span>
                                        <span wire:loading wire:target="newCommentPhotos" class="flex items-center">
                                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Processing...
                                        </span>
                                        <span wire:loading wire:target="addComment">Saving...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Internal Updates Toggle (Admin/Manager/Landlord only) -->
                    @if($canAddInternalNotes)
                        <div class="mb-4 p-3 bg-white border border-gray-300 rounded-lg">
                            <button
                                wire:click="toggleInternalUpdates"
                                class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-md transition-colors {{ $showInternalUpdates ? 'bg-blue-50 text-blue-700 hover:bg-blue-100' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}"
                            >
                                <span class="flex items-center">
                                    @if($showInternalUpdates)
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span class="font-semibold">Showing All Updates</span>
                                        <span class="ml-2 text-xs">(including internal notes)</span>
                                    @else
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                        <span class="font-semibold">Showing Public Updates Only</span>
                                        <span class="ml-2 text-xs">(internal notes hidden)</span>
                                    @endif
                                </span>
                                <svg class="w-5 h-5 transform {{ $showInternalUpdates ? 'rotate-180' : '' }} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    <!-- Timeline -->
                    @if($updates->count() > 0)
                        <div class="space-y-4">
                            @foreach($updates->reverse() as $update)
                                <div class="flex space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">
                                                {{ strtoupper(substr($update->user->name ?? 'S', 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-gray-50 rounded-lg p-4 {{ $update->is_internal ? 'border-2 border-red-200 bg-red-50' : '' }}">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $update->user->name ?? 'System' }}
                                                        @if($update->is_internal)
                                                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                                Internal
                                                            </span>
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $update->created_at->diffForHumans() }}</p>
                                                </div>

                                                <!-- Edit/Delete Actions -->
                                                @if($editingUpdateId !== $update->id && $update->canBeEditedBy(auth()->user()))
                                                    <div class="flex space-x-2">
                                                        <button
                                                            wire:click="editUpdate('{{ $update->id }}')"
                                                            class="text-xs text-blue-600 hover:text-blue-900"
                                                        >
                                                            Edit
                                                        </button>
                                                        @if($update->canBeDeletedBy(auth()->user()))
                                                            <button
                                                                wire:click="deleteUpdate('{{ $update->id }}')"
                                                                wire:confirm="Are you sure you want to delete this update?"
                                                                class="text-xs text-red-600 hover:text-red-900"
                                                            >
                                                                Delete
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Edit Form -->
                                            @if($editingUpdateId === $update->id)
                                                <form wire:submit.prevent="saveEdit">
                                                    <textarea
                                                        wire:model="editingMessage"
                                                        rows="3"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-2"
                                                    ></textarea>
                                                    <div class="flex space-x-2">
                                                        <button
                                                            type="submit"
                                                            class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700"
                                                        >
                                                            Save
                                                        </button>
                                                        <button
                                                            type="button"
                                                            wire:click="cancelEdit"
                                                            class="px-3 py-1 bg-gray-300 text-gray-700 rounded-md text-sm hover:bg-gray-400"
                                                        >
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            @else
                                                <!-- Display Message -->
                                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $update->message }}</p>

                                                <!-- Display Photos -->
                                                @if($update->photos && count($update->photos) > 0)
                                                    <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2">
                                                        @foreach($update->photos as $photo)
                                                            <img src="{{ Storage::url($photo) }}" alt="Update photo" class="rounded-lg shadow-md">
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <!-- Update Type Badge -->
                                                @if($update->update_type)
                                                    <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ ucfirst(str_replace('_', ' ', $update->update_type)) }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($updates->hasPages())
                            <div class="mt-6">
                                {{ $updates->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No updates yet. Be the first to add one!</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Assign Vendor Modal -->
    @if($showAssignModal)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeAssignModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                {{ $request->assigned_vendor_id ? 'Reassign Vendor' : 'Assign Vendor' }}
                            </h3>
                            <div class="mt-4">
                                <!-- Vendor Selection -->
                                <div class="mb-4">
                                    <label for="selectedVendorId" class="block text-sm font-medium text-gray-700 mb-1">
                                        Select Vendor <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="selectedVendorId"
                                        wire:model="selectedVendorId"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    >
                                        <option value="">Choose a vendor...</option>
                                        @foreach($activeVendors as $vendor)
                                            <option value="{{ $vendor->id }}">
                                                {{ $vendor->name }} - {{ $vendor->business_type }}
                                                @if($vendor->hourly_rate)
                                                    (${{ number_format($vendor->hourly_rate, 2) }}/hr)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedVendorId')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if($activeVendors->count() === 0)
                                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                        <p class="text-sm text-yellow-800">
                                            No active vendors available. Please <a href="{{ route('vendors.create') }}" class="font-medium underline">add a vendor</a> first.
                                        </p>
                                    </div>
                                @endif

                                <!-- Assignment Notes -->
                                <div class="mb-4">
                                    <label for="assignmentNotes" class="block text-sm font-medium text-gray-700 mb-1">
                                        Assignment Notes (Optional)
                                    </label>
                                    <textarea
                                        id="assignmentNotes"
                                        wire:model="assignmentNotes"
                                        rows="3"
                                        placeholder="Add any special instructions or notes for this assignment..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    ></textarea>
                                    @error('assignmentNotes')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Info Message -->
                                @if(!$request->assigned_vendor_id && $request->status === 'submitted')
                                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                        <p class="text-sm text-blue-800">
                                            <strong>Note:</strong> Assigning a vendor will automatically change the status from "Submitted" to "Assigned".
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="assignVendor"
                            type="button"
                            @if($activeVendors->count() === 0) disabled @endif
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ $request->assigned_vendor_id ? 'Reassign Vendor' : 'Assign Vendor' }}
                        </button>
                        <button
                            wire:click="closeAssignModal"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
