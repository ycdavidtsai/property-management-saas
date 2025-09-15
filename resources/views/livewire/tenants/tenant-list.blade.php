<div>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">Tenants</h1>
            <p class="mt-2 text-sm text-gray-700">Manage tenant information and track their property assignments.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            @if(App\Services\RoleService::roleHasPermission(auth()->user()->role, 'tenants.create'))
                <a href="{{ route('tenants.create') }}" 
                   class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Add Tenant
                </a>
            @endif
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div>
            <input type="text" 
                   wire:model.live="search" 
                   placeholder="Search tenants..."
                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
        <div>
            <select wire:model.live="statusFilter" 
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                <option value="">All Statuses</option>
                <option value="active">Active Tenants</option>
                <option value="inactive">Inactive Tenants</option>
                <option value="active_lease">With Active Lease</option>
                <option value="no_lease">No Active Lease</option>
            </select>
        </div>
        <div>
            <select wire:model.live="propertyFilter" 
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                <option value="">All Properties</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Enhanced Tenants Table -->
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide cursor-pointer" 
                                wire:click="sortBy('name')">
                                Tenant
                                @if($sortBy === 'name')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Current Property
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Unit
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Lease Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Status
                            </th>
                            <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tenants as $tenant)
                            @php
                                $currentProperty = $tenant->currentProperty();
                                $currentUnit = $tenant->currentUnit();
                                $activeLease = $tenant->activeLease();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $tenant->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($currentProperty)
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('properties.show', $currentProperty->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                {{ $currentProperty->name }}
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $currentProperty->address }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">No current property</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($currentUnit)
                                        <div class="text-sm font-medium text-gray-900">Unit {{ $currentUnit->unit_number }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $currentUnit->bedrooms }}BR / {{ $currentUnit->bathrooms }}BA
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">No unit assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($activeLease)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $activeLease->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $activeLease->status === 'expiring_soon' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $activeLease->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $activeLease->status)) }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Ends {{ $activeLease->end_date->format('M j, Y') }}
                                        </div>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            No Active Lease
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('tenants.show', $tenant->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                                    @if(App\Services\RoleService::roleHasPermission(auth()->user()->role, 'tenants.edit'))
                                        <a href="{{ route('tenants.edit', $tenant->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No tenants found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $tenants->links() }}
    </div>
</div>