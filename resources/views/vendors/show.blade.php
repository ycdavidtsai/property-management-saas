<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $vendor->company_name }}
                    </h2>

                    <div class="flex gap-2">
                        @if($vendor->isManagedByUser())
                            <!-- ✨ NEW: Show indicator for user-managed vendors -->
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                                Managed by User Account
                            </span>
                        @else
                            <!-- Show edit button only if no user account -->
                            <a href="{{ route('vendors.edit', $vendor) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Edit Vendor
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Vendor Details -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contact Name</label>
                        <p class="mt-1 text-gray-900">{{ $vendor->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-gray-900">{{ $vendor->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <p class="mt-1 text-gray-900">{{ $vendor->phone ?? 'N/A' }}</p>
                    </div>

                    <!-- ... other fields ... -->
                </div>

                @if($vendor->isManagedByUser())
                    <!-- ✨ NEW: Info box for user-managed vendors -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-blue-900">User-Managed Profile</h4>
                                <p class="mt-1 text-sm text-blue-700">
                                    This vendor has a user account and manages their own profile information.
                                    Contact information shown here is managed by the vendor user.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
