<!-- resources/views/livewire/properties/dashboard.blade.php -->
<div>
        <!-- Properties Grid -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Properties</h3>
            <a href="{{ route('properties.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Add Property
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($properties as $property)
                <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">{{ $property->name }}</h4>
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $property->type)) }}
                            </span>
                        </div>

                        <p class="text-gray-600 text-sm mb-4">{{ $property->address }}</p>

                        <div class="flex justify-between text-sm mb-4">
                            <span class="text-gray-500">Units: {{ $property->units_count ?? 0 }}</span>
                            <span class="text-gray-500">Occupied: {{ $property->occupied_units_count ?? 0 }}</span>
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ route('properties.show', $property) }}"
                            class="flex-1 bg-blue-600 text-white text-sm py-2 px-3 rounded hover:bg-blue-700 text-center">
                                View Details
                            </a>
                            <a href="{{ route('properties.edit', $property) }}"
                            class="text-gray-600 hover:text-gray-800 text-sm py-2 px-3 border border-gray-300 rounded hover:bg-gray-50">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <p class="text-gray-500">No properties found. Add your first property to get started.</p>
                        <a href="{{ route('properties.create') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Add First Property
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Properties Overview</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Units</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $metrics['totalUnits'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Occupied Units</p>
                    <p class="text-2xl font-bold text-green-600">{{ $metrics['occupiedUnits'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Vacant Units</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $metrics['vacantUnits'] ?? ($metrics['totalUnits'] - $metrics['occupiedUnits']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Occupancy Rate</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $metrics['occupancyRate'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
                    <p class="text-2xl font-bold text-purple-600">${{ number_format($metrics['monthlyRevenue']) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
