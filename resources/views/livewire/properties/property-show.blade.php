<div>
    <!-- Property Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate">
                        {{ $property->name }}
                    </h1>
                    <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            {{ $property->address }}
                        </div>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 16.5v-13h-.25a.75.75 0 010-1.5h12.5a.75.75 0 010 1.5H16v13h.25a.75.75 0 010 1.5H3.75a.75.75 0 010-1.5H4zm1.5 0v-2.5h9v2.5h-9zm9-4v-2.5h-9v2.5h9zm-9-4v-2.5h9v2.5h-9z" clip-rule="evenodd" />
                            </svg>
                            {{ ucfirst($property->type) }} • {{ $property->total_units }} Units
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex space-x-3 sm:mt-0">
                    @if(App\Services\RoleService::roleHasPermission(auth()->user()->role, 'properties.edit'))
                        <a href="{{ route('properties.edit', $property->id) }}" 
                           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                            </svg>
                            Edit Property
                        </a>
                    @endif
                    <a href="{{ route('properties.index') }}" 
                       class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.793 2.232a.75.75 0 01-.025 1.06L3.622 7.25h10.003a5.375 5.375 0 010 10.75H10.75a.75.75 0 010-1.5h2.875a3.875 3.875 0 000-7.75H3.622l4.146 3.957a.75.75 0 01-1.036 1.085l-5.5-5.25a.75.75 0 010-1.085l5.5-5.25a.75.75 0 011.06.025z" clip-rule="evenodd" />
                        </svg>
                        Back to Properties
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4 mb-8">
            
            <!-- Occupancy Rate Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Occupancy Rate</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $occupancySummary['occupancy_rate'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $occupancySummary['occupancy_rate'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Occupied Units Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Occupied</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $occupancySummary['occupied'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vacant Units Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Vacant</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $occupancySummary['vacant'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Units Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Units</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $occupancySummary['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            
            <!-- Main Content - Units Grid -->
            <div class="lg:col-span-9">
                <!-- Units Section Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Units ({{ $property->total_units }})</h2>
                        <p class="text-sm text-gray-500">Manage individual units and their tenant assignments</p>
                    </div>
                    @if(App\Services\RoleService::canCreateLeases(auth()->user()->role))
                        <a href="{{ route('leases.create') }}" 
                           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Create Lease
                        </a>
                    @endif
                </div>

                <!-- Units Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($unitsWithLeases as $unit)
                        <div class="group relative bg-white rounded-lg shadow-sm border-2 hover:shadow-md transition-all duration-200
                            {{ $unit->status === 'occupied' ? 'border-green-200 hover:border-green-300' : '' }}
                            {{ $unit->status === 'vacant' ? 'border-gray-200 hover:border-gray-300' : '' }}
                            {{ $unit->status === 'for_lease' ? 'border-blue-200 hover:border-blue-300' : '' }}
                            {{ $unit->status === 'maintenance' ? 'border-yellow-200 hover:border-yellow-300' : '' }}">
                            
                            <!-- Unit Header -->
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Unit {{ $unit->unit_number }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $unit->status === 'occupied' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $unit->status === 'vacant' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $unit->status === 'for_lease' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $unit->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $unit->status)) }}
                                    </span>
                                </div>
                                
                                <!-- Unit Specs -->
                                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v2z"></path>
                                        </svg>
                                        {{ $unit->bedrooms }}BR
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                        </svg>
                                        {{ $unit->bathrooms }}BA
                                    </span>
                                    <span class="flex items-center font-medium text-gray-900">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        ${{ number_format($unit->rent_amount, 0) }}/mo
                                    </span>
                                </div>
                            </div>

                            <!-- Tenant Information -->
                            <div class="p-4">
                                @if($unit->currentTenants && $unit->currentTenants->count() > 0)
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900">
                                                Current Tenant{{ $unit->currentTenants->count() > 1 ? 's' : '' }}
                                            </h4>
                                            @if($unit->activeLease)
                                                <a href="{{ route('leases.show', $unit->activeLease->id) }}" 
                                                   class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">
                                                    View Lease →
                                                </a>
                                            @endif
                                        </div>
                                        
                                        <div class="space-y-2">
                                            @foreach($unit->currentTenants as $tenant)
                                                <div class="flex items-center p-2 bg-gray-50 rounded-lg group/tenant hover:bg-gray-100 transition-colors">
                                                    <div class="flex-shrink-0">
                                                        <div class="h-8 w-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                                            <span class="text-xs font-medium text-white">
                                                                {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3 flex-1 min-w-0">
                                                        <a href="{{ route('tenants.show', $tenant->id) }}" 
                                                           class="block">
                                                            <p class="text-sm font-medium text-gray-900 group-hover/tenant:text-indigo-600 transition-colors">
                                                                {{ $tenant->name }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 truncate">{{ $tenant->email }}</p>
                                                        </a>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-4 w-4 text-gray-400 group-hover/tenant:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if($unit->activeLease)
                                            <div class="pt-2 border-t border-gray-100">
                                                <div class="text-xs text-gray-500">
                                                    <span class="flex items-center">
                                                        <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Lease expires: {{ $unit->activeLease->end_date->format('M j, Y') }}
                                                        @if($unit->activeLease->end_date <= now()->addDays(60))
                                                            <span class="ml-1 text-yellow-600 font-medium">(Expiring Soon)</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <!-- Empty Unit State -->
                                    <div class="text-center py-4">
                                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No current tenant</p>
                                        @if(App\Services\RoleService::canCreateLeases(auth()->user()->role))
                                            <a href="{{ route('leases.create') }}?unit={{ $unit->id }}" 
                                               class="mt-3 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors">
                                                <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Create Lease
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Unit Actions Footer -->
                            <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 rounded-b-lg">
                                <div class="flex items-center justify-between">
                                    <button wire:click="toggleUnitDetails('{{ $unit->id }}')" 
                                            class="text-xs text-gray-600 hover:text-gray-900 flex items-center transition-colors">
                                        <svg class="mr-1 h-3 w-3 transition-transform {{ isset($showUnitDetails[$unit->id]) && $showUnitDetails[$unit->id] ? 'rotate-180' : '' }}" 
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        {{ isset($showUnitDetails[$unit->id]) && $showUnitDetails[$unit->id] ? 'Hide' : 'Show' }} Quick Info
                                    </button>

                                    @if(\App\Services\RoleService::roleHasPermission(auth()->user()->role, 'units.view'))
                                        <a href="{{ route('units.show', $unit->id) }}" 
                                        class="text-xs text-indigo-600 hover:text-indigo-900 font-medium flex items-center transition-colors">
                                            View Details
                                            <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>

                                @if(isset($showUnitDetails[$unit->id]) && $showUnitDetails[$unit->id])
                                    <div class="mt-3 pt-3 border-t border-gray-200 space-y-1 text-xs text-gray-600">
                                        @if($unit->square_feet)
                                            <p><span class="font-medium">Square Feet:</span> {{ number_format($unit->square_feet) }} sq ft</p>
                                        @endif
                                        <p><span class="font-medium">Added:</span> {{ $unit->created_at->format('M j, Y') }}</p>
                                        @if($unit->description)
                                            <p><span class="font-medium">Description:</span> {{ Str::limit($unit->description, 80) }}</p>
                                        @endif
                                        
                                        <!-- Quick Actions -->
                                        <div class="pt-2 flex space-x-3">
                                            @if(\App\Services\RoleService::roleHasPermission(auth()->user()->role, 'units.edit'))
                                                <a href="{{ route('units.edit', $unit->id) }}" 
                                                class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                    Edit Unit
                                                </a>
                                            @endif
                                            @if(!$unit->currentTenants->count() && \App\Services\RoleService::canCreateLeases(auth()->user()->role))
                                                <a href="{{ route('leases.create') }}?unit={{ $unit->id }}" 
                                                class="text-green-600 hover:text-green-900 font-medium">
                                                    Create Lease
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-3">
                <div class="space-y-6">
                    
                    <!-- Property Details Card -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Property Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($property->type) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Units</dt>
                                    <dd class="text-sm text-gray-900">{{ $property->total_units }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                                    <dd class="text-sm text-gray-900">{{ $property->address }}</dd>
                                </div>
                                @if($property->description)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                                        <dd class="text-sm text-gray-900">{{ $property->description }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Current Tenants Card -->
                    @if($currentTenants && $currentTenants->count() > 0)
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">
                                    Current Tenants ({{ $currentTenants->count() }})
                                </h3>
                                <div class="space-y-3">
                                    @foreach($currentTenants->take(8) as $tenant)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-medium text-white">
                                                        {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3 min-w-0 flex-1">
                                                <a href="{{ route('tenants.show', $tenant->id) }}" 
                                                   class="text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                                                    {{ $tenant->name }}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($currentTenants->count() > 8)
                                        <div class="pt-2 border-t border-gray-100">
                                            <p class="text-xs text-gray-500">
                                                +{{ $currentTenants->count() - 8 }} more tenants
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions Card -->
                    @if(App\Services\RoleService::canCreateLeases(auth()->user()->role))
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Quick Actions</h3>
                                <div class="space-y-3">
                                    <a href="{{ route('leases.create') }}" 
                                       class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Create New Lease
                                    </a>
                                    <a href="{{ route('tenants.create') }}" 
                                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Add New Tenant
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>