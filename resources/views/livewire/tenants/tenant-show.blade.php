<div>
    @php
        $currentProperty = $tenant->currentProperty();
        $currentUnit = $tenant->currentUnit();
        $activeLease = $tenant->activeLease();
        $leaseHistory = $tenant->leaseHistory()->get();
    @endphp

    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ $tenant->name }}</h1>
            <p class="mt-2 text-sm text-gray-700">
                Tenant details and current property assignment
            </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none space-x-3">
            @if(App\Services\RoleService::roleHasPermission(auth()->user()->role, 'tenants.edit'))
                <a href="{{ route('tenants.edit', $tenant->id) }}" 
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Edit Tenant
                </a>
            @endif
            
            @if(App\Services\RoleService::canCreateLeases(auth()->user()->role) && !$activeLease)
                <a href="{{ route('leases.create') }}" 
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Create Lease
                </a>
            @endif
        </div>
    </div>

    <!-- Current Property Assignment (if any) -->
    @if($currentProperty && $activeLease)
        <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m-3.75-16.5h1.5m-1.5 3h1.5m-1.5 3h1.5m-1.5 3h1.5" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Currently Housed</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>
                            <strong>Property:</strong> 
                            <a href="{{ route('properties.show', $currentProperty->id) }}" 
                               class="underline hover:text-green-900">
                                {{ $currentProperty->name }}
                            </a>
                        </p>
                        <p><strong>Unit:</strong> {{ $currentUnit->unit_number }}</p>
                        <p><strong>Address:</strong> {{ $currentProperty->address }}</p>
                        <p><strong>Lease Status:</strong> 
                            <span class="capitalize">{{ str_replace('_', ' ', $activeLease->status) }}</span>
                        </p>
                        <p><strong>Lease Period:</strong> 
                            {{ $activeLease->start_date->format('M j, Y') }} - {{ $activeLease->end_date->format('M j, Y') }}
                        </p>
                        <div class="mt-2">
                            <a href="{{ route('leases.show', $activeLease->id) }}" 
                               class="inline-flex items-center text-sm text-green-700 hover:text-green-900">
                                View Lease Details →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">No Current Property Assignment</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>This tenant is not currently assigned to any property.</p>
                        @if(App\Services\RoleService::canCreateLeases(auth()->user()->role))
                            <div class="mt-2">
                                <a href="{{ route('leases.create') }}" 
                                   class="inline-flex items-center text-sm text-yellow-700 hover:text-yellow-900">
                                    Create New Lease →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tenant Information -->
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        
        <!-- Basic Information -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Tenant Information</h3>
                    <div class="mt-6 border-t border-gray-100">
                        <dl class="divide-y divide-gray-100">
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Full name</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $tenant->name }}</dd>
                            </div>
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Email address</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $tenant->email }}</dd>
                            </div>
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Phone</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $tenant->phone ?? 'Not provided' }}</dd>
                            </div>
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Status</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>
                            </div>
                            @if($tenant->tenantProfile)
                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Employment Status</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $tenant->tenantProfile->employment_status ?? 'Not provided' }}</dd>
                                </div>
                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Monthly Income</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        @if($tenant->tenantProfile->monthly_income)
                                            ${{ number_format($tenant->tenantProfile->monthly_income, 2) }}
                                        @else
                                            Not provided
                                        @endif
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions / Summary -->
        <div>
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Quick Summary</h3>
                    <div class="mt-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Current Residence</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($currentProperty)
                                    {{ $currentProperty->name }}<br>
                                    <span class="text-gray-500">Unit {{ $currentUnit->unit_number }}</span>
                                @else
                                    <span class="text-gray-400">No current residence</span>
                                @endif
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Lease Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($activeLease)
                                    <span class="capitalize">{{ str_replace('_', ' ', $activeLease->status) }}</span>
                                @else
                                    <span class="text-gray-400">No active lease</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Leases</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $leaseHistory->count() }}</dd>
                        </div>

                        <!-- Quick Actions -->
                        <div class="pt-4 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Quick Actions</dt>
                            <div class="space-y-2">
                                @if($activeLease)
                                    <a href="{{ route('leases.show', $activeLease->id) }}" 
                                       class="block text-sm text-indigo-600 hover:text-indigo-900">
                                        View Current Lease
                                    </a>
                                @endif
                                @if($currentProperty)
                                    <a href="{{ route('properties.show', $currentProperty->id) }}" 
                                       class="block text-sm text-indigo-600 hover:text-indigo-900">
                                        View Property
                                    </a>
                                @endif
                                @if(App\Services\RoleService::canViewLeases(auth()->user()->role))
                                    <a href="{{ route('leases.index') }}?search={{ urlencode($tenant->name) }}" 
                                       class="block text-sm text-indigo-600 hover:text-indigo-900">
                                        View All Leases
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lease History -->
    @if($leaseHistory->count() > 0)
        <div class="mt-6">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Lease History</h3>
                    <div class="mt-6">
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($leaseHistory as $index => $lease)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                        {{ $lease->status === 'active' ? 'bg-green-500' : '' }}
                                                        {{ $lease->status === 'expiring_soon' ? 'bg-yellow-500' : '' }}
                                                        {{ $lease->status === 'expired' ? 'bg-red-500' : '' }}
                                                        {{ $lease->status === 'terminated' ? 'bg-gray-500' : '' }}">
                                                        <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            <a href="{{ route('properties.show', $lease->unit->property->id) }}" 
                                                               class="font-medium text-gray-900 hover:text-indigo-600">
                                                                {{ $lease->unit->property->name }}
                                                            </a>
                                                            - Unit {{ $lease->unit->unit_number }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            {{ $lease->start_date->format('M j, Y') }} - {{ $lease->end_date->format('M j, Y') }}
                                                        </p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                            {{ $lease->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                                            {{ $lease->status === 'expiring_soon' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                            {{ $lease->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                                            {{ $lease->status === 'terminated' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $lease->status)) }}
                                                        </span>
                                                        <div class="mt-1">
                                                            <a href="{{ route('leases.show', $lease->id) }}" 
                                                               class="text-indigo-600 hover:text-indigo-900">View</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>