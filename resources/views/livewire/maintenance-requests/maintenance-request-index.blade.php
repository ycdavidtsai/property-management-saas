<div>
    <!-- Search and Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search requests..."
                       class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Statuses</option>
                    <option value="submitted">Submitted</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select wire:model.live="priorityFilter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Priorities</option>
                    <option value="emergency">Emergency</option>
                    <option value="high">High</option>
                    <option value="normal">Normal</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div class="flex items-end">
                @if($canCreate)
                    <a href="{{ route('maintenance-requests.create') }}"
                       class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                        New Request
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Requests List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($requests->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($requests as $request)
                    <div class="p-6 hover:bg-gray-50 transition duration-150">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <a href="{{ route('maintenance-requests.show', $request) }}"
                                           class="hover:text-blue-600">
                                            {{ $request->title }}
                                        </a>
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $request->priority_color }}-100 text-{{ $request->priority_color }}-800">
                                        {{ ucfirst($request->priority) }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $request->status_color }}-100 text-{{ $request->status_color }}-800">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </div>
                                <p class="text-gray-600 mb-2">{{ Str::limit($request->description, 150) }}</p>
                                <div class="flex items-center text-sm text-gray-500 space-x-4">
                                    <span>{{ $request->property->name }}</span>
                                    @if($request->unit)
                                        <span>Unit {{ $request->unit->unit_number }}</span>
                                    @endif
                                    <span>{{ $request->tenant->name }}</span>
                                    @if($request->assignedVendor)
                                        <span>Assigned to {{ $request->assignedVendor->name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right text-sm text-gray-500">
                                <div>{{ $request->created_at->format('M d, Y') }}</div>
                                <div>{{ $request->created_at->format('g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m-16-4c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No maintenance requests</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new maintenance request.</p>
                @if($canCreate)
                    <div class="mt-6">
                        <a href="{{ route('maintenance-requests.create') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            New Request
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
