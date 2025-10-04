<div>
    <!-- Vendor Header Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-bold text-xl">
                            {{ strtoupper(substr($vendor->name, 0, 2)) }}
                        </span>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $vendor->name }}</h3>
                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $vendor->email }}
                            </span>
                            @if($vendor->phone)
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $vendor->phone }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <button
                    wire:click="toggleVendorStatus"
                    class="px-4 py-2 rounded-md text-sm font-medium {{ $vendor->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}"
                >
                    {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button
                    wire:click="setActiveTab('details')"
                    class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'details' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Details
                </button>
                <button
                    wire:click="setActiveTab('requests')"
                    class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'requests' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    Maintenance Requests
                    <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $activeTab === 'requests' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $stats['total'] }}
                    </span>
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'details')
                <!-- Details Tab -->
                <div class="space-y-6">
                    <!-- Business Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Business Type</dt>
                                <dd class="mt-1">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $vendor->business_type }}
                                    </span>
                                </dd>
                            </div>
                            @if($vendor->hourly_rate)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Hourly Rate</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${{ number_format($vendor->hourly_rate, 2) }}/hour</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Specialties -->
                    @if($vendor->specialties && count(json_decode($vendor->specialties, true) ?? []) > 0)
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Specialties</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach(json_decode($vendor->specialties, true) ?? [] as $specialty)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">
                                        {{ $specialty }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Internal Notes -->
                    @if($vendor->notes)
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Internal Notes</h4>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $vendor->notes }}</p>
                        </div>
                    @endif

                    <!-- Statistics -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Request Statistics</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                                <div class="text-sm text-gray-500">Total Requests</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-900">{{ $stats['assigned'] }}</div>
                                <div class="text-sm text-yellow-700">Assigned</div>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-900">{{ $stats['in_progress'] }}</div>
                                <div class="text-sm text-blue-700">In Progress</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-900">{{ $stats['completed'] }}</div>
                                <div class="text-sm text-green-700">Completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Maintenance Requests Tab -->
                <div>
                    <!-- Status Filter -->
                    <div class="mb-4">
                        <select
                            wire:model.live="requestStatusFilter"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">All Requests</option>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Requests List -->
                    @if($maintenanceRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($maintenanceRequests as $request)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h5 class="text-lg font-medium text-gray-900">{{ $request->title }}</h5>
                                            <p class="mt-1 text-sm text-gray-600">{{ Str::limit($request->description, 150) }}</p>
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                                <span>{{ $request->property->name }}</span>
                                                @if($request->unit)
                                                    <span>{{ $request->unit->unit_number }}</span>
                                                @endif
                                                <span>{{ $request->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex flex-col items-end space-y-2">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                @if($request->status === 'completed') bg-green-100 text-green-800
                                                @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                                @elseif($request->status === 'assigned') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                            <a href="{{ route('maintenance-requests.show', $request) }}"
                                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                View Details →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($maintenanceRequests->hasPages())
                            <div class="mt-6">
                                {{ $maintenanceRequests->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No maintenance requests found for this vendor.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
