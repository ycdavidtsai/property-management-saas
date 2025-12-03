<x-admin-layout>
    <x-slot name="header">Vendor Promotion Requests</x-slot>

    <div class="mb-6">
        <p class="text-gray-600">Review and manage vendor requests to be promoted to global listing status.</p>
    </div>

    {{-- Pending Requests --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <h3 class="text-lg font-semibold text-gray-800">Pending Requests</h3>
                @if($pendingRequests->count() > 0)
                    <span class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                        {{ $pendingRequests->count() }}
                    </span>
                @endif
            </div>
        </div>

        @if($pendingRequests->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($pendingRequests as $request)
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $request->vendor->name }}</h4>
                                    @if($request->vendor->company_name)
                                        <span class="ml-2 text-gray-500">({{ $request->vendor->company_name }})</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    Requested by {{ $request->requestedBy->name }}
                                    on {{ $request->requested_at->format('M j, Y \a\t g:i A') }}
                                </p>

                                @if($request->request_notes)
                                    <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-600">{{ $request->request_notes }}</p>
                                    </div>
                                @endif

                                {{-- Vendor Details --}}
                                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Email</p>
                                        <p class="text-gray-900">{{ $request->vendor->email ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Phone</p>
                                        <p class="text-gray-900">{{ $request->vendor->phone ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Organization</p>
                                        <p class="text-gray-900">{{ $request->vendor->organization?->name ?? 'None' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Specialties</p>
                                        <p class="text-gray-900">
                                            @if($request->vendor->specialties && count($request->vendor->specialties) > 0)
                                                {{ implode(', ', array_slice($request->vendor->specialties, 0, 3)) }}
                                                @if(count($request->vendor->specialties) > 3)
                                                    +{{ count($request->vendor->specialties) - 3 }} more
                                                @endif
                                            @else
                                                None listed
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="ml-6 flex flex-col space-y-2">
                                <button
                                    type="button"
                                    onclick="document.getElementById('approve-modal-{{ $request->id }}').classList.remove('hidden')"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Approve
                                </button>
                                <button
                                    type="button"
                                    onclick="document.getElementById('reject-modal-{{ $request->id }}').classList.remove('hidden')"
                                    class="inline-flex items-center px-4 py-2 border border-red-300 text-red-700 text-sm font-medium rounded-lg hover:bg-red-50"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Approve Modal --}}
                    <div id="approve-modal-{{ $request->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('approve-modal-{{ $request->id }}').classList.add('hidden')"></div>
                            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Approve Promotion Request</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    You are about to promote <strong>{{ $request->vendor->name }}</strong> to global vendor status.
                                </p>
                                <form action="{{ route('admin.approve-promotion', $request) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                                        <textarea
                                            name="review_notes"
                                            rows="3"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Add any notes about this approval..."
                                        ></textarea>
                                    </div>
                                    <div class="flex justify-end space-x-3">
                                        <button
                                            type="button"
                                            onclick="document.getElementById('approve-modal-{{ $request->id }}').classList.add('hidden')"
                                            class="px-4 py-2 text-gray-700 hover:text-gray-900"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                                        >
                                            Approve Promotion
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Reject Modal --}}
                    <div id="reject-modal-{{ $request->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('reject-modal-{{ $request->id }}').classList.add('hidden')"></div>
                            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Promotion Request</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Please provide a reason for rejecting the promotion request from <strong>{{ $request->vendor->name }}</strong>.
                                </p>
                                <form action="{{ route('admin.reject-promotion', $request) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
                                        <textarea
                                            name="review_notes"
                                            rows="3"
                                            required
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Explain why this request is being rejected..."
                                        ></textarea>
                                    </div>
                                    <div class="flex justify-end space-x-3">
                                        <button
                                            type="button"
                                            onclick="document.getElementById('reject-modal-{{ $request->id }}').classList.add('hidden')"
                                            class="px-4 py-2 text-gray-700 hover:text-gray-900"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                        >
                                            Reject Request
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending requests</h3>
                <p class="mt-1 text-sm text-gray-500">All promotion requests have been reviewed.</p>
            </div>
        @endif
    </div>

    {{-- Recently Reviewed --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Recently Reviewed</h3>
        </div>

        @if($reviewedRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vendor
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reviewed By
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reviewed At
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Notes
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reviewedRequests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $request->vendor->name }}</p>
                                        @if($request->vendor->company_name)
                                            <p class="text-sm text-gray-500">{{ $request->vendor->company_name }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $request->reviewedBy?->name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->reviewed_at?->format('M j, Y g:i A') ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    {{ $request->review_notes ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-gray-500">No reviewed requests yet.</p>
            </div>
        @endif
    </div>
</x-admin-layout>
