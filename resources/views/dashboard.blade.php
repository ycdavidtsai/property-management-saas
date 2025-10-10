<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}here
        </h2>
    </x-slot>

    <div style="margin: 15px;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Properties & Units -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Properties & Units</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Total Properties -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Properties</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $metrics['properties']['total'] }}</p>
                                    {{-- <p class="mt-1 text-sm text-gray-600">{{ $metrics['properties']['active'] }} active</p> --}}
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('properties.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                    View all properties →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Total Units -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Units</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $metrics['units']['total'] }}</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $metrics['units']['occupied'] }} occupied</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Occupancy Rate -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Occupancy Rate</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $metrics['units']['occupancy_rate'] }}%</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $metrics['units']['total'] - $metrics['units']['occupied'] }} vacant</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Tenants -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Tenants</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $metrics['tenants']['total'] }}</p>
                                </div>
                                <div class="p-3 bg-indigo-100 rounded-full">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('tenants.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                    View all tenants →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Leases -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Leases</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Active Leases -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Active Leases</p>
                                    <p class="mt-2 text-3xl font-bold text-green-600">{{ $metrics['leases']['active'] }}</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expiring Soon -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Expiring Soon</p>
                                    <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $metrics['leases']['expiring_soon'] }}</p>
                                    <p class="mt-1 text-xs text-gray-500">Next 30 days</p>
                                </div>
                                <div class="p-3 bg-yellow-100 rounded-full">
                                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expired Leases -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Expired</p>
                                    <p class="mt-2 text-3xl font-bold text-red-600">{{ $metrics['leases']['expired'] }}</p>
                                </div>
                                <div class="p-3 bg-red-100 rounded-full">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('leases.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        View all leases →
                    </a>
                </div>
            </div>

            <!-- Maintenance & Vendors -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Maintenance & Vendors</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Submitted Requests -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-500">Submitted</p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $metrics['maintenance']['submitted'] }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gray-600 h-2 rounded-full" style="width: {{ $metrics['maintenance']['total'] > 0 ? ($metrics['maintenance']['submitted'] / $metrics['maintenance']['total'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Requests -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-500">Assigned</p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ $metrics['maintenance']['assigned'] }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $metrics['maintenance']['total'] > 0 ? ($metrics['maintenance']['assigned'] / $metrics['maintenance']['total'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- In Progress -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-500">In Progress</p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $metrics['maintenance']['in_progress'] }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $metrics['maintenance']['total'] > 0 ? ($metrics['maintenance']['in_progress'] / $metrics['maintenance']['total'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Requests -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-500">Completed</p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $metrics['maintenance']['completed'] }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $metrics['maintenance']['total'] > 0 ? ($metrics['maintenance']['completed'] / $metrics['maintenance']['total'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Maintenance -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Requests</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $metrics['maintenance']['total'] }}</p>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Vendors -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Active Vendors</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $metrics['vendors']['active'] }}</p>
                                    <p class="mt-1 text-sm text-gray-600">of {{ $metrics['vendors']['total'] }} total</p>
                                </div>
                                <div class="p-3 bg-teal-100 rounded-full">
                                    <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex space-x-4">
                    <a href="{{ route('maintenance-requests.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-800">
                        View maintenance requests →
                    </a>
                    <a href="{{ route('vendors.index') }}" class="text-sm font-medium text-teal-600 hover:text-teal-800">
                        View vendors →
                    </a>
                </div>
            </div>

            <!-- Recent Maintenance Requests -->
            @if($recentMaintenanceRequests->count() > 0)
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Maintenance Requests</h3>
                        <a href="{{ route('maintenance-requests.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            View all →
                        </a>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="divide-y divide-gray-200">
                            @foreach($recentMaintenanceRequests as $request)
                                <a href="{{ route('maintenance-requests.show', $request) }}" class="block hover:bg-gray-50 transition-colors">
                                    <div class="p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $request->title }}</p>
                                                <div class="mt-1 flex items-center space-x-3 text-xs text-gray-500">
                                                    <span>{{ $request->property->name }}</span>
                                                    @if($request->unit)
                                                        <span>Unit {{ $request->unit->unit_number }}</span>
                                                    @endif
                                                    <span>{{ $request->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <span class="ml-4 px-2 py-1 text-xs font-semibold rounded-full
                                                @if($request->status === 'completed') bg-green-100 text-green-800
                                                @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                                @elseif($request->status === 'assigned') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
</x-app-layout>
