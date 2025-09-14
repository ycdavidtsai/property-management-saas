{{-- resources/views/livewire/tenants/tenant-list.blade.php --}}
<div>
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-lg font-bold text-gray-900">Tenant Management</h1>
        <p class="text-gray-600">Manage your tenants and their information</p>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col md:flex-row gap-4 flex-1">
                <div class="flex-1">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search tenants by name, email, or phone..."
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <select wire:model.live="statusFilter"
                            class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div>
                <a href="{{ route('tenants.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 inline-flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Tenant
                </a>
            </div>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>

                        <!-- Existing columns -->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('name')" class="flex items-center space-x-1 hover:text-gray-700 group">
                                <span>Tenant</span>
                                @if($sortField === 'name')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-gray-700 group">
                                <span>Joined</span>
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-gray-50 transition-colors">

                            <!-- Rest of your existing table columns remain the same -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            @if($tenant->profile_photo_path)
                                                <img class="h-10 w-10 rounded-full object-cover"
                                                     src="{{ Storage::url($tenant->profile_photo_path) }}"
                                                     alt="{{ $tenant->name }}">
                                            @else
                                                <span class="text-gray-600 font-medium">
                                                    {{ substr($tenant->name, 0, 1) }}{{ substr(explode(' ', $tenant->name)[1] ?? '', 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                                        <div class="text-sm text-gray-500">Tenant ID: {{ $tenant->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $tenant->email }}</div>
                                @if($tenant->phone)
                                    <div class="text-sm text-gray-500">{{ $tenant->phone }}</div>
                                @else
                                    <div class="text-sm text-gray-400 italic">No phone</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tenant->tenantProfile && $tenant->tenantProfile->employment_status)
                                    <div class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $tenant->tenantProfile->employment_status)) }}</div>
                                    @if($tenant->tenantProfile->monthly_income)
                                        <div class="text-sm text-gray-500">${{ number_format($tenant->tenantProfile->monthly_income, 0) }}/month</div>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-500 italic">No employment info</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tenant->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $tenant->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('tenants.show', $tenant) }}"
                                       class="text-blue-600 hover:text-blue-900 transition-colors">View</a>
                                    <a href="{{ route('tenants.edit', $tenant) }}"
                                       class="text-indigo-600 hover:text-indigo-900 transition-colors">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No tenants found</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    @if($search || $statusFilter)
                                        Try adjusting your search or filter criteria.
                                    @else
                                        Get started by adding your first tenant.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tenants->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>

    <!-- Loading State -->
    <div wire:loading class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                <h3 class="text-lg font-medium text-gray-900 mt-2">Loading...</h3>
            </div>
        </div>
    </div>
</div>
