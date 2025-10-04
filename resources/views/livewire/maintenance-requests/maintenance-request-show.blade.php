<div>
    {{-- <div class="bg-red-100 p-4 mb-4">
    LIVEWIRE COMPONENT IS LOADING! Request ID: {{ $request->id }}
    </div> --}}
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

                    <!-- Vendor Assignment Display -->
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
                                            wire:confirm="Are you sure you want to unassign this vendor?"
                                            class="px-3 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-md hover:bg-red-50"
                                        >
                                            Unassign
                                        </button>
                                    </div>
                                @endcan
                            </div>
                            @if($request->assignment_notes)
                                <div class="mt-3 pt-3 border-t border-blue-200">
                                    <p class="text-sm font-medium text-blue-900">Assignment Notes:</p>
                                    <p class="text-sm text-blue-700 mt-1">{{ $request->assignment_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        @can('update', $request)
                            @if(auth()->user()->role !== 'tenant')
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
                            @endif
                        @endcan
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
                    @if($updates->count() > 0)
                        <div class="space-y-4">
                            @foreach($updates as $update)
                                <div class="flex space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">
                                                {{ strtoupper(substr($update->user->name ?? 'S', 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-sm font-medium text-gray-900">{{ $update->user->name ?? 'System' }}</p>
                                                <p class="text-xs text-gray-500">{{ $update->created_at->diffForHumans() }}</p>
                                            </div>
                                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $update->message }}</p>
                                            @if($update->update_type)
                                                <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst(str_replace('_', ' ', $update->update_type)) }}
                                                </span>
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
                            <p class="mt-2 text-sm text-gray-500">No updates yet.</p>
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
