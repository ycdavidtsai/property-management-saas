<div>
    <!-- Tenant Management Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate">
                        Tenant Management
                    </h1>
                    <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                            Manage tenant information and property assignments
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex space-x-3 sm:mt-0">
                    @if(App\Services\RoleService::roleHasPermission(auth()->user()->role, 'tenants.create'))
                        <a href="{{ route('tenants.create') }}" 
                           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Add Tenant
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4 mb-8">
            
            <!-- Total Tenants -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Tenants</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Tenants -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['active'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Housed Tenants -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">With Leases</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['with_leases'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $stats['housed_percentage'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Unhoused Tenants -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">No Lease</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['without_leases'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Tenants</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" 
                                   wire:model.live="search" 
                                   placeholder="Name, email, or phone..."
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="statusFilter" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Lease Status Filter -->
                    <div>
                        <label for="leaseStatusFilter" class="block text-sm font-medium text-gray-700 mb-1">Lease Status</label>
                        <select wire:model.live="leaseStatusFilter" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Lease Statuses</option>
                            <option value="has_lease">With Active Lease</option>
                            <option value="no_lease">No Active Lease</option>
                            <option value="active">Active Lease</option>
                            <option value="expiring_soon">Expiring Soon</option>
                            <option value="expired">Expired Lease</option>
                        </select>
                    </div>

                    <!-- Property Filter -->
                    <div>
                        <label for="propertyFilter" class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                        <select wire:model.live="propertyFilter" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Properties</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenants Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($tenants as $tenant)
                @php
                    $activeLease = $tenant->leases->where('status', 'active')->first();
                    $currentProperty = $activeLease?->unit?->property;
                    $currentUnit = $activeLease?->unit;
                @endphp
                
                <div class="group bg-white rounded-lg shadow-sm border-2 hover:shadow-md transition-all duration-200
                    {{ $activeLease ? 'border-green-200 hover:border-green-300' : 'border-yellow-200 hover:border-yellow-300' }}">
                    
                    <!-- Tenant Header -->
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-lg font-medium text-white">
                                        {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">
                                    <a href="{{ route('tenants.show', $tenant->id) }}" 
                                       class="hover:text-indigo-600 transition-colors">
                                        {{ $tenant->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500 truncate">{{ $tenant->email }}</p>
                                @if($tenant->phone)
                                    <p class="text-sm text-gray-500">{{ $tenant->phone }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="mt-3 flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($activeLease)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $activeLease->status === 'active' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $activeLease->status === 'expiring_soon' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $activeLease->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $activeLease->status)) }} Lease
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Property Assignment -->
                    <div class="p-4">
                        @if($currentProperty && $currentUnit)
                            <div class="space-y-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Current Assignment</h4>
                                    
                                    <!-- Property Info -->
                                    <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-green-900">
                                                    <a href="{{ route('properties.show', $currentProperty->id) }}" 
                                                       class="hover:text-green-700 transition-colors">
                                                        {{ $currentProperty->name }}
                                                    </a>
                                                </p>
                                                <p class="text-xs text-green-700">Unit {{ $currentUnit->unit_number }}</p>
                                            </div>
                                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                        
                                        <!-- Lease Details -->
                                        <div class="mt-2 pt-2 border-t border-green-200">
                                            <div class="flex items-center justify-between text-xs text-green-700">
                                                <span>Rent: ${{ number_format($activeLease->rent_amount, 0) }}/mo</span>
                                                <span>Expires: {{ $activeLease->end_date->format('M j, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- No Assignment -->
                            <div class="text-center py-4">
                                <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                                    <svg class="mx-auto h-8 w-8 text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <p class="text-sm text-yellow-800 font-medium">No Current Property</p>
                                    <p class="text-xs text-yellow-700">Not assigned to any unit</p>
                                    
                                    @if(App\Services\RoleService::canCreateLeases(auth()->user()->role))
                                        <a href="{{ route('leases.create') }}?tenant={{ $tenant->id }}" 
                                           class="mt-2 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-yellow-800 bg-yellow-100 hover:bg-yellow-200 transition-colors">
                                            Create Lease
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions Footer -->
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 rounded-b-lg">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('tenants.show', $tenant->id) }}" 
                               class="text-sm text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                                View Details →
                            </a>
                            @if($activeLease)
                                <a href="{{ route('leases.show', $activeLease->id) }}" 
                                   class="text-xs text-gray-600 hover:text-gray-900 transition-colors">
                                    View Lease
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No tenants found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new tenant.</p>
                        @if(App\Services\RoleService::roleHasPermission(auth()->user()->role, 'tenants.create'))
                            <div class="mt-6">
                                <a href="{{ route('tenants.create') }}" 
                                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                    </svg>
                                    Add Tenant
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($tenants->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</div>