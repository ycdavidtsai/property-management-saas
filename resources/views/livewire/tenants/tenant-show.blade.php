<div>
    <!-- Tenant Header -->
    <div class="bg-white shadow">
        <div class="px-6 py-4">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center">
                                <span class="text-xl font-medium text-white">
                                    {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold leading-7 text-gray-900">
                                {{ $tenant->name }}
                            </h1>
                            <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    {{ $tenant->email }}
                                </div>
                                @if($tenant->phone)
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                        </svg>
                                        {{ $tenant->phone }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex space-x-3 sm:mt-0">
                    <span class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md
                        {{ $tenant->is_active ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }}">
                        {{ $tenant->is_active ? 'Active Tenant' : 'Inactive Tenant' }}
                    </span>
                    @if(App\Services\RoleService::roleHasPermission(auth()->user()->role, 'tenants.edit'))
                        <a href="{{ route('tenants.edit', $tenant->id) }}" 
                           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                            </svg>
                            Edit Tenant
                        </a>
                    @endif
                    <a href="{{ route('tenants.index') }}" 
                       class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.793 2.232a.75.75 0 01-.025 1.06L3.622 7.25h10.003a5.375 5.375 0 010 10.75H10.75a.75.75 0 010-1.5h2.875a3.875 3.875 0 000-7.75H3.622l4.146 3.957a.75.75 0 01-1.036 1.085l-5.5-5.25a.75.75 0 010-1.085l5.5-5.25a.75.75 0 011.06.025z" clip-rule="evenodd" />
                        </svg>
                        Back to Tenants
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Current Housing Status -->
        @if($activeLease && $currentProperty)
            <!-- Housed Status -->
            <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 mb-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h2 class="text-lg font-semibold text-green-900">Currently Housed</h2>
                        <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <dt class="text-sm font-medium text-green-700">Property</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('properties.show', $currentProperty->id) }}" 
                                       class="text-base font-semibold text-green-900 hover:text-green-700 transition-colors">
                                        {{ $currentProperty->name }} →
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-green-700">Unit</dt>
                                <dd class="mt-1 text-base font-semibold text-green-900">Unit {{ $currentUnit->unit_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-green-700">Monthly Rent</dt>
                                <dd class="mt-1 text-base font-semibold text-green-900">${{ number_format($activeLease->rent_amount, 0) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-green-700">Lease Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $activeLease->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $activeLease->status === 'expiring_soon' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $activeLease->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $activeLease->status)) }}
                                    </span>
                                </dd>
                            </div>
                        </div>
                        
                        <!-- Lease Details -->
                        <div class="mt-4 pt-4 border-t border-green-200">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <dt class="text-sm font-medium text-green-700">Lease Period</dt>
                                    <dd class="mt-1 text-sm text-green-900">
                                        {{ $activeLease->start_date->format('M j, Y') }} - {{ $activeLease->end_date->format('M j, Y') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-green-700">Address</dt>
                                    <dd class="mt-1 text-sm text-green-900">{{ $currentProperty->address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-green-700">Unit Details</dt>
                                    <dd class="mt-1 text-sm text-green-900">{{ $currentUnit->bedrooms }}BR / {{ $currentUnit->bathrooms }}BA</dd>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="mt-4 flex space-x-3">
                                <a href="{{ route('leases.show', $activeLease->id) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-green-600 text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50 transition-colors">
                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h4.125M8.25 8.25h7.5V12M8.25 8.25V12"></path>
                                    </svg>
                                    View Lease Details
                                </a>
                                <a href="{{ route('properties.show', $currentProperty->id) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-green-600 text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50 transition-colors">
                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    View Property
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Not Housed Status -->
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6 mb-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h2 class="text-lg font-semibold text-yellow-900">No Current Property Assignment</h2>
                        <p class="mt-2 text-sm text-yellow-700">
                            This tenant is not currently assigned to any property or unit. Create a lease to assign them to a property.
                        </p>
                        @if(App\Services\RoleService::canCreateLeases(auth()->user()->role))
                            <div class="mt-4">
                                <a href="{{ route('leases.create') }}?tenant={{ $tenant->id }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 transition-colors">
                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Create New Lease
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Content Grid -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            
            <!-- Main Content -->
            <div class="lg:col-span-8">
                
                <!-- Tenant Information -->
                <div class="bg-white shadow rounded-lg mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Tenant Information</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $tenant->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $tenant->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $tenant->phone ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>
                            </div>
                            @if($tenant->tenantProfile)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Employment Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $tenant->tenantProfile->employment_status ?? 'Not provided' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Monthly Income</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
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

                <!-- Lease History -->
                @if($leaseHistory->count() > 0)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Lease History ({{ $leaseHistory->count() }})</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @foreach($leaseHistory as $lease)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex items-start space-x-3">
                                                    <div class="relative">
                                                        <div class="h-10 w-10 rounded-full flex items-center justify-center ring-8 ring-white
                                                            {{ $lease->status === 'active' ? 'bg-green-500' : '' }}
                                                            {{ $lease->status === 'expiring_soon' ? 'bg-yellow-500' : '' }}
                                                            {{ $lease->status === 'expired' ? 'bg-red-500' : '' }}
                                                            {{ $lease->status === 'terminated' ? 'bg-gray-500' : '' }}">
                                                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        @if($lease->status === 'active')
                                                            <span class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px">
                                                                <svg class="h-3 w-3 text-green-500" fill="currentColor" viewBox="0 0 12 12">
                                                                    <circle cx="6" cy="6" r="6" />
                                                                </svg>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-sm">
                                                            <div class="font-medium text-gray-900">
                                                                <a href="{{ route('properties.show', $lease->unit->property->id) }}" 
                                                                   class="hover:text-indigo-600 transition-colors">
                                                                    {{ $lease->unit->property->name }}
                                                                </a>
                                                                - Unit {{ $lease->unit->unit_number }}
                                                            </div>
                                                            <div class="mt-0.5 text-gray-500">
                                                                {{ $lease->start_date->format('M j, Y') }} - {{ $lease->end_date->format('M j, Y') }} 
                                                                • ${{ number_format($lease->rent_amount, 0) }}/month
                                                            </div>
                                                        </div>
                                                        <div class="mt-2 flex items-center space-x-2">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                {{ $lease->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                                                {{ $lease->status === 'expiring_soon' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                                {{ $lease->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                                                {{ $lease->status === 'terminated' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                                {{ ucfirst(str_replace('_', ' ', $lease->status)) }}
                                                            </span>
                                                            <a href="{{ route('leases.show', $lease->id) }}" 
                                                               class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">
                                                                View Lease →
                                                            </a>
                                                        </div>
                                                        
                                                        <!-- Co-tenants for this lease -->
                                                        @php
                                                            $otherTenants = $lease->tenants->where('id', '!=', $tenant->id);
                                                        @endphp
                                                        @if($otherTenants->count() > 0)
                                                            <div class="mt-2">
                                                                <div class="text-xs text-gray-500">
                                                                    Co-tenant{{ $otherTenants->count() > 1 ? 's' : '' }}: 
                                                                    @foreach($otherTenants as $coTenant)
                                                                        <a href="{{ route('tenants.show', $coTenant->id) }}" 
                                                                           class="text-indigo-600 hover:text-indigo-900">{{ $coTenant->name }}</a>{{ !$loop->last ? ', ' : '' }}
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4">
                <div class="space-y-6">
                    
                    <!-- Quick Summary -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Quick Summary</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($activeLease)
                                            <span class="text-green-600 font-medium">Housed</span>
                                        @else
                                            <span class="text-yellow-600 font-medium">Needs Housing</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Current Property</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($currentProperty)
                                            <a href="{{ route('properties.show', $currentProperty->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                {{ $currentProperty->name }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">None</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Leases</dt>
                                    <dd class="text-sm text-gray-900">{{ $leaseHistory->count() }}</dd>
                                </div>
                                @if($activeLease)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Current Rent</dt>
                                        <dd class="text-sm text-gray-900">${{ number_format($activeLease->rent_amount, 0) }}/month</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Co-tenants (if any) -->
                    @if($coTenants && $coTenants->count() > 0)
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">
                                    Current Co-tenant{{ $coTenants->count() > 1 ? 's' : '' }}
                                </h3>
                                <div class="space-y-3">
                                    @foreach($coTenants as $coTenant)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-medium text-white">
                                                        {{ strtoupper(substr($coTenant->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <a href="{{ route('tenants.show', $coTenant->id) }}" 
                                                   class="text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                                                    {{ $coTenant->name }}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                @if(!$activeLease && App\Services\RoleService::canCreateLeases(auth()->user()->role))
                                    <a href="{{ route('leases.create') }}?tenant={{ $tenant->id }}" 
                                       class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Create New Lease
                                    </a>
                                @endif
                                
                                @if($activeLease)
                                    <a href="{{ route('leases.show', $activeLease->id) }}" 
                                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h4.125M8.25 8.25h7.5V12M8.25 8.25V12"></path>
                                        </svg>
                                        View Current Lease
                                    </a>
                                    
                                    <a href="{{ route('properties.show', $currentProperty->id) }}" 
                                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        View Property
                                    </a>
                                @endif
                                
                                <a href="{{ route('leases.index') }}?search={{ urlencode($tenant->name) }}" 
                                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    View All Leases
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>