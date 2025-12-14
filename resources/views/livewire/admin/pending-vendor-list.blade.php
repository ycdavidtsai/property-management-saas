<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Vendor Requests</h2>
            <p class="text-gray-600">Manage vendor registrations and promotion requests</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="text-red-800">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Search --}}
            <div class="flex-1">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by vendor name, email, phone..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            {{-- Status Filter --}}
            <div class="flex gap-2">
                <button
                    wire:click="$set('filterStatus', 'pending')"
                    class="px-4 py-2 rounded-lg font-medium transition-colors {{ $filterStatus === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    Pending
                    @if($statusCounts['pending'] > 0)
                        <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-amber-500 text-white">{{ $statusCounts['pending'] }}</span>
                    @endif
                </button>
                <button
                    wire:click="$set('filterStatus', 'approved')"
                    class="px-4 py-2 rounded-lg font-medium transition-colors {{ $filterStatus === 'approved' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    Approved
                </button>
                <button
                    wire:click="$set('filterStatus', 'rejected')"
                    class="px-4 py-2 rounded-lg font-medium transition-colors {{ $filterStatus === 'rejected' ? 'bg-red-100 text-red-800 border border-red-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    Rejected
                </button>
                <button
                    wire:click="$set('filterStatus', '')"
                    class="px-4 py-2 rounded-lg font-medium transition-colors {{ $filterStatus === '' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    All
                </button>
            </div>
        </div>

        {{-- Type Filter (sub-filter for pending) --}}
        @if($filterStatus === 'pending' && ($typeCounts['registration'] > 0 || $typeCounts['promotion'] > 0))
            <div class="flex gap-2 mt-3 pt-3 border-t">
                <span class="text-sm text-gray-500 py-1">Type:</span>
                <button
                    wire:click="$set('filterType', '')"
                    class="px-3 py-1 text-sm rounded-lg transition-colors {{ $filterType === '' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    All
                </button>
                <button
                    wire:click="$set('filterType', 'registration')"
                    class="px-3 py-1 text-sm rounded-lg transition-colors {{ $filterType === 'registration' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    New Registrations
                    @if($typeCounts['registration'] > 0)
                        <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full bg-purple-200 text-purple-800">{{ $typeCounts['registration'] }}</span>
                    @endif
                </button>
                <button
                    wire:click="$set('filterType', 'promotion')"
                    class="px-3 py-1 text-sm rounded-lg transition-colors {{ $filterType === 'promotion' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    Promotion Requests
                    @if($typeCounts['promotion'] > 0)
                        <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full bg-blue-200 text-blue-800">{{ $typeCounts['promotion'] }}</span>
                    @endif
                </button>
            </div>
        @endif
    </div>

    {{-- Requests List --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        @forelse($requests as $request)
            <div class="p-4 border-b last:border-b-0 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    {{-- Vendor Info --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-xl font-bold text-gray-600">
                                    {{ strtoupper(substr($request->vendor->name ?? 'V', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-semibold text-gray-900">{{ $request->vendor->name ?? 'Unknown Vendor' }}</h3>

                                    {{-- Request Type Badge --}}
                                    @if($request->request_type === 'registration')
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                            New Registration
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            Promotion Request
                                        </span>
                                    @endif

                                    {{-- Status Badge --}}
                                    @if($request->status === 'pending')
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">
                                            Pending
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @elseif($request->status === 'rejected')
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $request->vendor->email ?? 'No email' }}
                                    @if($request->vendor->phone)
                                        <span class="mx-1">•</span>
                                        {{ $request->vendor->phone }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Additional Info --}}
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                            <div>
                                <span class="text-gray-500">Specialties:</span>
                                <span class="text-gray-900">
                                    {{ is_array($request->vendor->specialties) ? implode(', ', $request->vendor->specialties) : ($request->vendor->specialties ?? 'Not specified') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Submitted:</span>
                                <span class="text-gray-900">{{ $request->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @if($request->request_message)
                                <div class="sm:col-span-3">
                                    <span class="text-gray-500">Message:</span>
                                    <span class="text-gray-900">{{ Str::limit($request->request_message, 100) }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Review Notes (for approved/rejected) --}}
                        @if($request->status !== 'pending' && $request->review_notes)
                            <div class="mt-2 p-2 rounded bg-gray-50 text-sm">
                                <span class="text-gray-500">Review Notes:</span>
                                <span class="text-gray-700">{{ $request->review_notes }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">
                        @if($request->status === 'pending')
                            <button
                                wire:click="confirmApprove('{{ $request->id }}')"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-1"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Approve
                            </button>
                            <button
                                wire:click="confirmReject('{{ $request->id }}')"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-1"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reject
                            </button>
                        @else
                            <span class="text-sm text-gray-500">
                                {{ $request->status === 'approved' ? 'Approved' : 'Rejected' }}
                                @if($request->reviewed_at)
                                    on {{ $request->reviewed_at->format('M j, Y') }}
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No Requests Found</h3>
                <p class="text-gray-500">
                    @if($filterStatus === 'pending')
                        There are no pending vendor requests at this time.
                    @else
                        No vendor requests match your current filters.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($requests->hasPages())
        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    @endif

    {{-- Approve Modal --}}
    @if($showApproveModal && $this->selectedRequest)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50" wire:click="closeModals"></div>

                <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 z-10">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Approval</h3>

                    <p class="text-gray-600 mb-4">
                        Are you sure you want to approve
                        <strong>{{ $this->selectedRequest->vendor->name }}</strong>?
                    </p>

                    @if($this->selectedRequest->request_type === 'registration')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                            <p class="text-sm text-green-800">
                                <strong>This is a new vendor registration.</strong><br>
                                Approving will make them a <strong>Global Vendor</strong>, visible to all landlords on the platform.
                            </p>
                        </div>
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                            <p class="text-sm text-blue-800">
                                <strong>This is a promotion request.</strong><br>
                                Approving will upgrade them from Private to <strong>Global Vendor</strong>.
                            </p>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3">
                        <button
                            wire:click="closeModals"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            wire:click="approveRequest"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="approveRequest">Approve</span>
                            <span wire:loading wire:target="approveRequest">Approving...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Reject Modal --}}
    @if($showRejectModal && $this->selectedRequest)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50" wire:click="closeModals"></div>

                <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 z-10">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Request</h3>

                    <p class="text-gray-600 mb-4">
                        Please provide a reason for rejecting
                        <strong>{{ $this->selectedRequest->vendor->name }}</strong>.
                    </p>

                    @if($this->selectedRequest->request_type === 'registration')
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                            <p class="text-sm text-amber-800">
                                <strong>This is a new vendor registration.</strong><br>
                                The vendor will be notified and will not be able to access the platform.
                            </p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason</label>
                        <textarea
                            wire:model="rejectionReason"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Please explain why this request is being rejected..."
                        ></textarea>
                        @error('rejectionReason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            wire:click="closeModals"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            wire:click="rejectRequest"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="rejectRequest">Reject</span>
                            <span wire:loading wire:target="rejectRequest">Rejecting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
