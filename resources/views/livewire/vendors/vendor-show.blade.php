{{-- This is Landlord view Vendors detail page --}}
<div>
    <!-- Vendor Header Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-bold text-xl">
                            {{ strtoupper(substr($vendor->name, 0, 2)) }}
                        </span>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $vendor->name }}</h3>
                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $vendor->email }}
                            </span>
                            @if($vendor->phone)
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $vendor->phone }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <button
                        wire:click="toggleVendorStatus"
                        class="px-4 py-2 rounded-md text-sm font-medium {{ $vendor->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}"
                    >
                        {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                    </button>

                    @can('delete', $vendor)
                        <button
                            wire:click="confirmDelete"
                            class="px-4 py-2 rounded-md text-sm font-medium bg-red-100 text-red-800 hover:bg-red-200"
                            title="Delete Vendor"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="cancelDelete"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Delete Vendor
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to permanently delete <strong>{{ $vendor->name }}</strong>?
                                </p>
                                @if($vendor->maintenanceRequests()->count() > 0)
                                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                        <p class="text-sm text-yellow-800">
                                            <strong>Warning:</strong> This vendor has {{ $vendor->maintenanceRequests()->count() }} maintenance request(s).
                                            You cannot delete vendors with existing requests. Please deactivate instead.
                                        </p>
                                    </div>
                                @else
                                    <p class="mt-2 text-sm text-gray-500">
                                        This action cannot be undone. All vendor information will be permanently removed.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        @if($vendor->maintenanceRequests()->count() > 0)
                            <button
                                wire:click="cancelDelete"
                                type="button"
                                class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm"
                            >
                                Close
                            </button>
                        @else
                            <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    Delete Vendor
                                </button>
                            </form>
                            <button
                                wire:click="cancelDelete"
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

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
                    wire:click="setActiveTab('requests')"
                    class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'requests' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Maintenance Requests
                    <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $activeTab === 'requests' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $stats['total'] }}
                    </span>
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'details')
                <!-- Details Tab -->
                <div class="space-y-6">
                    <!-- Business Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Business Type</dt>
                                <dd class="mt-1">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $vendor->business_type }}
                                    </span>
                                </dd>
                            </div>
                            @if($vendor->hourly_rate)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Hourly Rate</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${{ number_format($vendor->hourly_rate, 2) }}/hour</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Specialties -->
                    {{-- @if($vendor->specialties && count(json_decode($vendor->specialties, true) ?? []) > 0) --}}
                    @if(!empty($vendor->specialties) && is_array($vendor->specialties))
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Specialties</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($vendor->specialties as $specialty)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">
                                        {{ $specialty }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Internal Notes -->
                    @if($vendor->notes)
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Internal Notes</h4>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $vendor->notes }}</p>
                        </div>
                    @endif

                    <!-- Statistics -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Request Statistics</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                                <div class="text-sm text-gray-500">Total Requests</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-900">{{ $stats['assigned'] }}</div>
                                <div class="text-sm text-yellow-700">Assigned</div>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-900">{{ $stats['in_progress'] }}</div>
                                <div class="text-sm text-blue-700">In Progress</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-900">{{ $stats['completed'] }}</div>
                                <div class="text-sm text-green-700">Completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Maintenance Requests Tab -->
                <div>
                    <!-- Status Filter -->
                    <div class="mb-4">
                        <select
                            wire:model.live="requestStatusFilter"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">All Requests</option>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Requests List -->
                    @if($maintenanceRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($maintenanceRequests as $request)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <a href="{{ route('maintenance-requests.show', $request) }}"
                                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                               <h5 class="text-lg font-medium ">{{ $request->title }}</h5>
                                            </a>
                                            <p class="mt-1 text-sm text-gray-600">{{ Str::limit($request->description, 150) }}</p>
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                                <span>{{ $request->property->name }}</span>
                                                @if($request->unit)
                                                    <span>{{ $request->unit->unit_number }}</span>
                                                @endif
                                                <span>{{ $request->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex flex-col items-end space-y-2">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                @if($request->status === 'completed') bg-green-100 text-green-800
                                                @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                                @elseif($request->status === 'assigned') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                            <a href="{{ route('maintenance-requests.show', $request) }}"
                                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                View Details →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($maintenanceRequests->hasPages())
                            <div class="mt-6">
                                {{ $maintenanceRequests->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No maintenance requests found for this vendor.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
