{{-- This is Landlord view Vendors index page --}}
<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header with Info -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">My Vendors..</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Vendors you've created (Private) or added from the global directory
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('vendors.create') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        + Create Private Vendor
                    </a>
                    <a href="{{ route('vendors.browse-global') }}"
                        class="inline-flex items-center px-4 py-2 border border-blue-600 shadow-sm text-sm font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                        🌐 Browse Global Directory
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input
                        type="text"
                        id="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search vendors..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <!-- Business Type Filter -->
                <div>
                    <label for="businessTypeFilter" class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                    <select
                        id="businessTypeFilter"
                        wire:model.live="businessTypeFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All Types</option>
                        @foreach($businessTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select
                        id="statusFilter"
                        wire:model.live="statusFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="active">Active Only</option>
                        <option value="inactive">Inactive Only</option>
                        <option value="all">All Vendors</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Vendors Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vendor
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Business Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rate
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Requests
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-gray-50">
                            {{-- <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">
                                            {{ strtoupper(substr($vendor->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $vendor->name }}
                                        </div>
                                    </div>
                                </div>
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">
                                            {{ strtoupper(substr($vendor->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $vendor->name }}
                                            </span>
                                            <!-- Global Badge -->
                                            @if($vendor->isGlobal())
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                                    🌐 Global
                                                </span>
                                            @else
                                                <!-- Private Badge -->
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-300">
                                                    🔒 Private
                                                </span>
                                                <!-- My Vendor Badge if current org created it -->
                                                @if($vendor->isOwnedBy(auth()->user()->organization_id))
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        My Vendor
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $vendor->business_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $vendor->email }}</div>
                                @if($vendor->phone)
                                    <div class="text-xs text-gray-400">{{ $vendor->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($vendor->hourly_rate)
                                    ${{ number_format($vendor->hourly_rate, 2) }}/hr
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $vendor->maintenance_requests_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    wire:click="toggleVendorStatus({{ $vendor->id }})"
                                    class="relative inline-flex items-center"
                                >
                                    @if($vendor->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </button>
                            </td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('vendors.show', $vendor) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    View
                                </a>
                                <a href="{{ route('vendors.edit', $vendor) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </a>
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('vendors.show', $vendor) }}"
                                    class="text-blue-600 hover:text-blue-900"
                                    title="View Details">
                                        View
                                    </a>
                                    {{-- <a href="{{ route('vendors.edit', $vendor) }}"
                                    class="text-indigo-600 hover:text-indigo-900"
                                    title="Edit Vendor">
                                        Edit
                                    </a> --}}
                                    @if($vendor->canBeEditedBy(auth()->user()))
                                        <a href="{{ route('vendors.edit', $vendor) }}"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        title="Edit Vendor">
                                            Edit
                                        </a>
                                    @else
                                        <span class="text-gray-400" title="Cannot edit this vendor">
                                            Edit
                                        </span>
                                    @endif

                                    @can('delete', $vendor)
                                        @if($vendor->maintenance_requests_count == 0)
                                            <form action="{{ route('vendors.destroy', $vendor) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to permanently delete {{ $vendor->name }}? This action cannot be undone.')"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Delete Vendor">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed"
                                                title="Cannot delete vendor with maintenance requests">
                                                Delete
                                            </span>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="mt-2 text-sm">No vendors found. Add your first vendor to get started.</p>
                                <a href="{{ route('vendors.create') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    Add Vendor
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($vendors->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $vendors->links() }}
            </div>
        @endif
    </div>
</div>
