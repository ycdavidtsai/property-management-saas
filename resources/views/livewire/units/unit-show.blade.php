<div>
    <!-- Unit Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate">
                        Unit {{ $unit->unit_number }}
                    </h1>
                    <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 16.5v-13h-.25a.75.75 0 010-1.5h12.5a.75.75 0 010 1.5H16v13h.25a.75.75 0 010 1.5H3.75a.75.75 0 010-1.5H4zm1.5 0v-2.5h9v2.5h-9zm9-4v-2.5h-9v2.5h9zm-9-4v-2.5h9v2.5h-9z" clip-rule="evenodd" />
                            </svg>
                            {{ $unit->property->name }}
                        </div>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            {{ $unit->property->address }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex space-x-3 sm:mt-0">
                    @if(\App\Services\RoleService::roleHasPermission(auth()->user()->role, 'units.edit'))
                        <a href="{{ route('units.edit', $unit->id) }}" 
                           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                            </svg>
                            Edit Unit
                        </a>
                    @endif
                    <a href="{{ route('properties.show', $unit->property->id) }}" 
                       class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.793 2.232a.75.75 0 01-.025 1.06L3.622 7.25h10.003a5.375 5.375 0 010 10.75H10.75a.75.75 0 010-1.5h2.875a3.875 3.875 0 000-7.75H3.622l4.146 3.957a.75.75 0 01-1.036 1.085l-5.5-5.25a.75.75 0 010-1.085l5.5-5.25a.75.75 0 011.06.025z" clip-rule="evenodd" />
                        </svg>
                        Back to Property
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Unit Status & Metrics -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4 mb-8">
            
            <!-- Status Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $unit->status === 'occupied' ? 'bg-green-100' : ($unit->status === 'vacant' ? 'bg-gray-100' : ($unit->status === 'for_lease' ? 'bg-blue-100' : 'bg-yellow-100')) }} rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 {{ $unit->status === 'occupied' ? 'text-green-600' : ($unit->status === 'vacant' ? 'text-gray-600' : ($unit->status === 'for_lease' ? 'text-blue-600' : 'text-yellow-600')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Status</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $unit->status)) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rent Amount Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Monthly Rent</dt>
                                <dd class="text-lg font-medium text-gray-900">${{ number_format($unit->rent_amount, 0) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Occupancy Rate Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Occupancy Rate</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $metrics['occupancy_rate'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Leases Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Leases</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $metrics['total_leases'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="switchTab('overview')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Overview
                </button>
                <button wire:click="switchTab('current')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'current' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Current Lease
                </button>
                <button wire:click="switchTab('history')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'history' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Lease History
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            
            <!-- Main Content Area -->
            <div class="lg:col-span-8">
                
                <!-- Overview Tab -->
                @if($activeTab === 'overview')
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Unit Specifications</h3>
                            
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Bedrooms</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $unitSpecs['bedrooms'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Bathrooms</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $unitSpecs['bathrooms'] }}</dd>
                                </div>
                                @if($unitSpecs['square_feet'])
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Square Feet</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($unitSpecs['square_feet']) }} sq ft</dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Monthly Rent</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${{ number_format($unitSpecs['rent_amount'], 2) }}</dd>
                                </div>
                                @if($unit->description)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $unit->description }}</dd>
                                    </div>
                                @endif
                                @if($unit->features)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Features</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $unit->features }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                @endif

                <!-- Current Lease Tab -->
                @if($activeTab === 'current')
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            @if($currentLease)
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Current Lease</h3>
                                    <a href="{{ route('leases.show', $currentLease->id) }}" 
                                       class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                        View Full Details →
                                    </a>
                                </div>

                                <!-- Lease Details -->
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $currentLease->start_date->format('M j, Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">End Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $currentLease->end_date->format('M j, Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Monthly Rent</dt>
                                        <dd class="mt-1 text-sm text-gray-900">${{ number_format($currentLease->monthly_rent, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Security Deposit</dt>
                                        <dd class="mt-1 text-sm text-gray-900">${{ number_format($currentLease->security_deposit, 2) }}</dd>
                                    </div>
                                </dl>

                                <!-- Current Tenants -->
                                <div class="mt-6 border-t border-gray-200 pt-6">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">
                                        Current Tenant{{ $currentLease->tenants->count() > 1 ? 's' : '' }} ({{ $currentLease->tenants->count() }})
                                    </h4>
                                    <div class="space-y-3">
                                        @foreach($currentLease->tenants as $tenant)
                                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                                <div class="flex-shrink-0">
                                                    <div class="h-10 w-10 bg-indigo-600 rounded-full flex items-center justify-center">
                                                        <span class="text-sm font-medium text-white">
                                                            {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <a href="{{ route('tenants.show', $tenant->id) }}" 
                                                       class="block">
                                                        <p class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $tenant->name }}</p>
                                                        <p class="text-sm text-gray-500">{{ $tenant->email }}</p>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Active Lease</h3>
                                    <p class="mt-1 text-sm text-gray-500">This unit does not currently have an active lease.</p>
                                    @if(\App\Services\RoleService::roleHasPermission(auth()->user()->role, 'leases.create'))
                                        <div class="mt-6">
                                            <a href="{{ route('leases.create') }}?unit={{ $unit->id }}" 
                                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Create New Lease
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Lease History Tab -->
                @if($activeTab === 'history')
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Lease History</h3>
                            
                            @if($leaseHistory->count() > 0)
                                <div class="flow-root">
                                    <ul class="-mb-8">
                                        @foreach($leaseHistory as $index => $lease)
                                            <li>
                                                <div class="relative pb-8">
                                                    @if(!$loop->last)
                                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                    @endif
                                                    <div class="relative flex space-x-3">
                                                        <div>
                                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                                {{ $lease->status === 'active' ? 'bg-green-500' : ($lease->status === 'expired' ? 'bg-gray-500' : 'bg-red-500') }}">
                                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                            <div>
                                                                <p class="text-sm text-gray-500">
                                                                    <a href="{{ route('leases.show', $lease->id) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                                                        Lease #{{ $lease->id }}
                                                                    </a>
                                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                        {{ $lease->status === 'active' ? 'bg-green-100 text-green-800' : ($lease->status === 'expired' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                                                        {{ ucfirst($lease->status) }}
                                                                    </span>
                                                                </p>
                                                                <p class="text-sm text-gray-900">
                                                                    {{ $lease->start_date->format('M j, Y') }} - {{ $lease->end_date->format('M j, Y') }}
                                                                </p>
                                                                <p class="text-sm text-gray-500">
                                                                    Tenants: {{ $lease->tenants->pluck('name')->join(', ') }}
                                                                </p>
                                                            </div>
                                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                                ${{ number_format($lease->monthly_rent, 0) }}/mo
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Lease History</h3>
                                    <p class="mt-1 text-sm text-gray-500">This unit has not had any leases yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4">
                <div class="space-y-6">
                    
                    <!-- Property Card -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Property Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('properties.show', $unit->property->id) }}" 
                                           class="hover:text-indigo-600 transition-colors">
                                            {{ $unit->property->name }}
                                        </a>
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $unit->property->address }}</p>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Type:</span> {{ ucfirst($unit->property->type) }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Total Units:</span> {{ $unit->property->total_units }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @if(\App\Services\RoleService::roleHasPermission(auth()->user()->role, 'leases.create'))
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Quick Actions</h3>
                                <div class="space-y-3">
                                    @if(!$currentLease)
                                        <a href="{{ route('leases.create') }}?unit={{ $unit->id }}" 
                                           class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Create New Lease
                                        </a>
                                    @endif
                                    <a href="{{ route('tenants.create') }}" 
                                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Add New Tenant
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Unit Performance -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Performance Metrics</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Occupancy Rate</dt>
                                    <dd class="text-sm text-gray-900">{{ $metrics['occupancy_rate'] }}%</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Leases</dt>
                                    <dd class="text-sm text-gray-900">{{ $metrics['total_leases'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Estimated Revenue</dt>
                                    <dd class="text-sm text-gray-900">${{ number_format($metrics['estimated_revenue'], 0) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Days Occupied</dt>
                                    <dd class="text-sm text-gray-900">{{ $metrics['days_occupied'] }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>