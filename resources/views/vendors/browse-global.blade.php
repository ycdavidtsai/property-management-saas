<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Browse Global Vendor Directory
            </h2>
            <a href="{{ route('vendors.index') }}" class="text-blue-600 hover:text-blue-900">
                ← Back to My Vendors
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- My Global Vendors -->
            @if($myGlobalVendors->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Global Vendors in My List ({{ $myGlobalVendors->count() }})</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($myGlobalVendors as $vendor)
                                <div class="border border-green-200 bg-green-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $vendor->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $vendor->business_type }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            ✓ Added
                                        </span>
                                    </div>
                                    <form action="{{ route('vendors.remove-from-my-vendors', $vendor) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-900"
                                                onclick="return confirm('Remove {{ $vendor->name }} from your vendors?')">
                                            Remove from My Vendors
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Available Global Vendors -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Available Global Vendors ({{ $availableVendors->total() }})</h3>

                    @if($availableVendors->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($availableVendors as $vendor)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $vendor->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $vendor->business_type }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            🌐 Global
                                        </span>
                                    </div>
                                    @if($vendor->hourly_rate)
                                        <p class="text-sm text-gray-600 mb-2">${{ number_format($vendor->hourly_rate, 2) }}/hr</p>
                                    @endif
                                    @if($vendor->specialties && count($vendor->specialties) > 0)
                                        <div class="flex flex-wrap gap-1 mb-3">
                                            @foreach(array_slice($vendor->specialties, 0, 3) as $specialty)
                                                <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                                                    {{ $specialty }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <form action="{{ route('vendors.add-to-my-vendors', $vendor) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                                            + Add to My Vendors
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $availableVendors->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            No additional global vendors available. You've added all global vendors to your list!
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
