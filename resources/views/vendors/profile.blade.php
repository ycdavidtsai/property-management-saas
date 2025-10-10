<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Vendor Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Vendor Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $vendor->name }}</h3>
                            <p class="text-gray-600 mt-1">{{ $vendor->business_type }}</p>
                        </div>
                        @if($vendor->isGlobal())
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                🌐 Global Listing
                            </span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-300">
                                🔒 Private Listing
                            </span>
                        @endif
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900">{{ $vendor->email }}</p>
                        </div>
                        @if($vendor->phone)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Phone</label>
                                <p class="text-gray-900">{{ $vendor->phone }}</p>
                            </div>
                        @endif
                        @if($vendor->hourly_rate)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Hourly Rate</label>
                                <p class="text-gray-900">${{ number_format($vendor->hourly_rate, 2) }}/hr</p>
                            </div>
                        @endif
                    </div>

                    {{-- @if($vendor->specialties && count($vendor->specialties) > 0) --}}
                    {{-- @if($vendor->specialties && is_array($vendor->specialties) && count($vendor->specialties) > 0) --}}
                    @if(!empty($vendor->specialties) && is_array($vendor->specialties))
                        <div class="mt-4">
                            <label class="text-sm font-medium text-gray-500">Specialties</label>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($vendor->specialties as $specialty)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">
                                        {{ $specialty }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Global Listing Promotion -->
            @if($vendor->isPrivate())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4">Expand Your Reach</h4>

                        @if($pendingRequest)
                            <!-- Pending Request Status -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="h-6 w-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h5 class="font-medium text-yellow-900">Promotion Request Pending</h5>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            Your request to be listed globally was submitted on {{ $pendingRequest->requested_at->format('M d, Y') }}.
                                            An administrator will review your request soon.
                                        </p>
                                        @if($pendingRequest->request_message)
                                            <div class="mt-2 text-sm text-yellow-800">
                                                <strong>Your message:</strong> {{ $pendingRequest->request_message }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Request Promotion Form -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <h5 class="font-medium text-blue-900 mb-2">✨ Get Listed Globally</h5>
                                <p class="text-sm text-blue-700 mb-4">
                                    Request to be listed in the global vendor directory and become visible to all organizations on the platform.
                                    This will help you get more work opportunities across multiple property management companies.
                                </p>
                                <ul class="text-sm text-blue-700 space-y-1 mb-4">
                                    <li>✓ Increased visibility to all organizations</li>
                                    <li>✓ More work opportunities</li>
                                    <li>✓ Build your reputation across the platform</li>
                                    <li>✓ Currently <strong>FREE</strong> to list globally</li>
                                </ul>
                            </div>

                            <form action="{{ route('vendor.request-promotion') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="request_message" class="block text-sm font-medium text-gray-700 mb-2">
                                        Message to Administrator (Optional)
                                    </label>
                                    <textarea
                                        id="request_message"
                                        name="request_message"
                                        rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Tell us why you'd like to be listed globally or any additional information..."
                                    ></textarea>
                                    @error('request_message')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button
                                    type="submit"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium"
                                >
                                    Request Global Listing
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @else
                <!-- Already Global -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h5 class="font-medium text-green-900">You're Listed Globally</h5>
                                    <p class="text-sm text-green-700 mt-1">
                                        Your vendor profile is visible to all organizations on the platform.
                                        @if($vendor->promoted_at)
                                            Listed since {{ $vendor->promoted_at->format('M d, Y') }}.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
