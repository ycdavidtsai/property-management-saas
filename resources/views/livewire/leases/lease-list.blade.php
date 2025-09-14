<div>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-lg font-semibold leading-6 text-gray-900">Lease Management</h1>
            <p class="mt-2 text-sm text-gray-700">Manage all property leases and tenant assignments.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            @if(App\Services\RoleService::canCreateLeases(auth()->user()->role))
                <a href="{{ route('leases.create') }}"
                   class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Add Lease
                </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <input type="text"
                   wire:model.live="search"
                   placeholder="Search properties, units, or tenants..."
                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
        <div>
            <select wire:model.live="statusFilter"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="expiring_soon">Expiring Soon</option>
                <option value="expired">Expired</option>
                <option value="terminated">Terminated</option>
            </select>
        </div>
    </div>

    <!-- Leases Table -->
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide cursor-pointer"
                                wire:click="sortBy('start_date')">
                                Start Date
                                @if($sortBy === 'start_date')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Property/Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Tenants</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Rent Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">End Date</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($leases as $lease)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $lease->start_date->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $lease->unit->property->name }}</div>
                                    <div class="text-sm text-gray-500">Unit {{ $lease->unit->unit_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $lease->tenant_names }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($lease->rent_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $lease->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $lease->status === 'expiring_soon' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $lease->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $lease->status === 'terminated' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $lease->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $lease->end_date->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('leases.show', $lease->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                                    @if(App\Services\RoleService::canEditLeases(auth()->user()->role))
                                        <a href="{{ route('leases.edit', $lease->id) }}"
                                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No leases found.
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
        {{ $leases->links() }}
    </div>
</div>
