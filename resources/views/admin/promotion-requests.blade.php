<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Vendor Promotion Requests
        </h2>
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

            <!-- Pending Requests -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        Pending Requests
                        @if($pendingRequests->count() > 0)
                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                {{ $pendingRequests->count() }}
                            </span>
                        @endif
                    </h3>

                    @forelse($pendingRequests as $request)
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h4 class="text-lg font-medium text-gray-900">
                                        {{ $request->vendor->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600">{{ $request->vendor->business_type }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Requested by {{ $request->requestedBy->name }} on {{ $request->requested_at->format('M d, Y \a\t g:i A') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Email:</span>
                                    <span class="text-sm text-gray-900">{{ $request->vendor->email }}</span>
                                </div>
                                @if($request->vendor->phone)
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                                        <span class="text-sm text-gray-900">{{ $request->vendor->phone }}</span>
                                    </div>
                                @endif
                                @if($request->vendor->hourly_rate)
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Rate:</span>
                                        <span class="text-sm text-gray-900">${{ number_format($request->vendor->hourly_rate, 2) }}/hr</span>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Created by:</span>
                                    <span class="text-sm text-gray-900">{{ $request->vendor->creator->name ?? 'N/A' }}</span>
                                </div>
                            </div>

                            @if($request->request_message)
                                <div class="bg-gray-50 rounded p-3 mb-3">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Vendor's Message:</p>
                                    <p class="text-sm text-gray-600">{{ $request->request_message }}</p>
                                </div>
                            @endif

                            <!-- Review Actions -->
                            <div class="flex gap-3">
                                <!-- Approve Form -->
                                <form action="{{ route('admin.approve-promotion', $request) }}" method="POST" class="flex-1">
                                    @csrf
                                    <div class="mb-2">
                                        <input
                                            type="text"
                                            name="review_notes"
                                            placeholder="Optional approval notes..."
                                            class="w-full rounded-md border-gray-300 text-sm"
                                        >
                                    </div>
                                    <button
                                        type="submit"
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium"
                                        onclick="return confirm('Approve this vendor for global listing?')"
                                    >
                                        ✓ Approve
                                    </button>
                                </form>

                                <!-- Reject Form -->
                                <form action="{{ route('admin.reject-promotion', $request) }}" method="POST" class="flex-1">
                                    @csrf
                                    <div class="mb-2">
                                        <input
                                            type="text"
                                            name="review_notes"
                                            placeholder="Reason for rejection (required)..."
                                            required
                                            class="w-full rounded-md border-gray-300 text-sm"
                                        >
                                    </div>
                                    <button
                                        type="submit"
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium"
                                        onclick="return confirm('Reject this promotion request?')"
                                    >
                                        ✗ Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No pending promotion requests.</p>
                    @endforelse
                </div>
            </div>

            <!-- Reviewed Requests -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Recently Reviewed</h3>

                    @forelse($reviewedRequests as $request)
                        <div class="border border-gray-200 rounded-lg p-4 mb-3">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="text-md font-medium text-gray-900">
                                        {{ $request->vendor->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Reviewed by {{ $request->reviewedBy->name ?? 'N/A' }} on {{ $request->reviewed_at->format('M d, Y') }}
                                    </p>
                                    @if($request->review_notes)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <strong>Notes:</strong> {{ $request->review_notes }}
                                        </p>
                                    @endif
                                </div>
                                @if($request->isApproved())
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No reviewed requests yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
