<div>
    <!-- Property Header -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $property->name }}</h1>
                <p class="text-gray-600 mb-4">{{ $property->address }}</p>
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        {{ ucfirst(str_replace('_', ' ', $property->type)) }}
                    </span>
                    <span class="text-gray-500">{{ $property->total_units }} Total Units</span>
                    <span class="text-gray-500">Created {{ $property->created_at->format('M j, Y') }}</span>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('properties.edit', $property) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200">
                    Edit Property
                </a>
                <a href="{{ route('properties.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Back to Properties
                </a>
            </div>
        </div>
    </div>

    <!-- Property Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Units</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $metrics['totalUnits'] }}</p>
                </div>
                <div class="p-3 bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Occupied</p>
                    <p class="text-2xl font-bold text-green-600">{{ $metrics['occupiedUnits'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Vacant</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $metrics['vacantUnits'] }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
                    <p class="text-2xl font-bold text-purple-600">${{ number_format($metrics['currentRevenue']) }}</p>
                    <p class="text-xs text-gray-500">of ${{ number_format($metrics['potentialRevenue']) }} potential</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Units Section -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Units</h2>
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add Unit
                </button>
            </div>
        </div>

        <div class="p-6">
            @if($units->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($units as $unit)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow
                            @if($unit->status === 'occupied') border-green-200 bg-green-50
                            @elseif($unit->status === 'maintenance') border-red-200 bg-red-50
                            @else border-gray-200 bg-gray-50
                            @endif">

                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-semibold text-gray-900">Unit {{ $unit->unit_number }}</h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($unit->status === 'occupied') bg-green-100 text-green-800
                                    @elseif($unit->status === 'maintenance') bg-red-100 text-red-800
                                    @else bg-orange-100 text-orange-800
                                    @endif">
                                    {{ ucfirst($unit->status) }}
                                </span>
                            </div>

                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                @if($unit->bedrooms)
                                    <p>{{ $unit->bedrooms }} bed, {{ $unit->bathrooms }} bath</p>
                                @endif
                                @if($unit->square_feet)
                                    <p>{{ number_format($unit->square_feet) }} sq ft</p>
                                @endif
                                <p class="font-semibold text-gray-900">${{ number_format($unit->rent_amount) }}/month</p>
                            </div>

                            <div class="flex space-x-2">
                                <select wire:change="updateUnitStatus({{ $unit->id }}, $event.target.value)"
                                        class="text-xs border-gray-300 rounded px-2 py-1 flex-1">
                                    <option value="vacant" @if($unit->status === 'vacant') selected @endif>Vacant</option>
                                    <option value="occupied" @if($unit->status === 'occupied') selected @endif>Occupied</option>
                                    <option value="maintenance" @if($unit->status === 'maintenance') selected @endif>Maintenance</option>
                                </select>
                                <button class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded hover:bg-gray-200">
                                    Edit
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No units found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding your first unit.</p>
                    <div class="mt-6">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Add Unit
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
