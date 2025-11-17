<div>
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Broadcast History</h2>
        <p class="text-gray-600 mt-1">View and manage your past broadcast messages</p>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-800 font-medium">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="sending">Sending</option>
                    <option value="sent">Sent</option>
                    <option value="failed">Failed</option>
                </select>
            </div>

            <!-- Channel Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Channel</label>
                <select wire:model.live="filters.channel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Channels</option>
                    <option value="email">Email</option>
                    <option value="sms">SMS</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" wire:model.live="filters.date_from" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" wire:model.live="filters.date_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <!-- Reset Filters Button -->
        <div class="mt-4">
            <button wire:click="resetFilters" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                Reset Filters
            </button>
        </div>
    </div>

    <!-- Broadcasts List -->
    <div class="space-y-4">
        @forelse($broadcasts as $broadcast)
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <!-- Title and Status -->
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $broadcast->title }}</h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $broadcast->status === 'sent' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $broadcast->status === 'sending' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $broadcast->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $broadcast->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $broadcast->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($broadcast->status) }}
                                </span>
                            </div>

                            <!-- Message Preview -->
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $broadcast->message }}</p>

                            <!-- Metadata -->
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $broadcast->sender->name }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    {{ $broadcast->recipient_count }} {{ Str::plural('recipient', $broadcast->recipient_count) }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $broadcast->created_at->format('M j, Y g:i A') }}
                                </div>
                                <div class="flex items-center gap-1">
                                    @foreach($broadcast->channels as $channel)
                                        @if($channel === 'email')
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded">
                                                <svg class="inline w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                Email
                                            </span>
                                        @endif
                                        @if($channel === 'sms')
                                            <span class="px-2 py-1 bg-green-50 text-green-700 text-xs rounded">
                                                <svg class="inline w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                                </svg>
                                                SMS
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Delivery Statistics (for sent broadcasts) -->
                            @if($broadcast->status === 'sent')
                                <div class="mt-4 pt-4 border-t grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ $broadcast->total_sent }}</div>
                                        <div class="text-xs text-gray-500">Sent</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600">{{ $broadcast->total_delivered }}</div>
                                        <div class="text-xs text-gray-500">Delivered</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-red-600">{{ $broadcast->total_failed }}</div>
                                        <div class="text-xs text-gray-500">Failed</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-purple-600">{{ $broadcast->delivery_rate }}%</div>
                                        <div class="text-xs text-gray-500">Delivery Rate</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Button -->
                        <div class="ml-4">
                            <button wire:click="viewDetails('{{ $broadcast->id }}')" class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No broadcasts found</h3>
                <p class="text-gray-600">Try adjusting your filters or create a new broadcast message.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $broadcasts->links() }}
    </div>

    <!-- Details Modal -->
    @if($showDetails && $selectedBroadcast)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDetails') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Overlay -->
                <div x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                     @click="$wire.closeDetails()"></div>

                <!-- Modal -->
                <div x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">

                    <!-- Header -->
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Broadcast Details</h3>
                            <button wire:click="closeDetails" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                        <!-- Basic Info -->
                        <div class="mb-6">
                            <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $selectedBroadcast->title }}</h4>
                            <p class="text-gray-600">{{ $selectedBroadcast->message }}</p>
                        </div>

                        <!-- Metadata -->
                        <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <span class="text-sm text-gray-500">Sent by:</span>
                                <p class="font-medium">{{ $selectedBroadcast->sender->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Sent at:</span>
                                <p class="font-medium">{{ $selectedBroadcast->sent_at ? $selectedBroadcast->sent_at->format('M j, Y g:i A') : 'Not sent' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Channels:</span>
                                <div class="flex gap-2 mt-1">
                                    @foreach($selectedBroadcast->channels as $channel)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $channel === 'email' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($channel) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Status:</span>
                                <p class="font-medium">{{ ucfirst($selectedBroadcast->status) }}</p>
                            </div>
                        </div>

                        <!-- Statistics -->
                        @if($selectedBroadcast->status === 'sent')
                            <div class="mb-6">
                                <h5 class="font-semibold text-gray-900 mb-3">Delivery Statistics</h5>
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Email Stats -->
                                    @if(in_array('email', $selectedBroadcast->channels))
                                        <div class="p-4 bg-blue-50 rounded-lg">
                                            <h6 class="text-sm font-medium text-blue-900 mb-2">Email</h6>
                                            <div class="space-y-1 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-blue-700">Sent:</span>
                                                    <span class="font-medium">{{ $selectedBroadcast->emails_sent }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-green-700">Delivered:</span>
                                                    <span class="font-medium">{{ $selectedBroadcast->emails_delivered }}</span>
                                                </div>
                                                @if($selectedBroadcast->emails_failed > 0)
                                                    <div class="flex justify-between">
                                                        <span class="text-red-700">Failed:</span>
                                                        <span class="font-medium">{{ $selectedBroadcast->emails_failed }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- SMS Stats -->
                                    @if(in_array('sms', $selectedBroadcast->channels))
                                        <div class="p-4 bg-green-50 rounded-lg">
                                            <h6 class="text-sm font-medium text-green-900 mb-2">SMS</h6>
                                            <div class="space-y-1 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-green-700">Sent:</span>
                                                    <span class="font-medium">{{ $selectedBroadcast->sms_sent }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-green-700">Delivered:</span>
                                                    <span class="font-medium">{{ $selectedBroadcast->sms_delivered }}</span>
                                                </div>
                                                @if($selectedBroadcast->sms_failed > 0)
                                                    <div class="flex justify-between">
                                                        <span class="text-red-700">Failed:</span>
                                                        <span class="font-medium">{{ $selectedBroadcast->sms_failed }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Recipients List -->
                        <div class="mb-6">
                            <h5 class="font-semibold text-gray-900 mb-3">
                                Recipients ({{ $selectedBroadcast->notifications->count() }})
                            </h5>
                            <div class="border rounded-lg overflow-hidden">
                                <div class="max-h-64 overflow-y-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @php
                                                // Group notifications by recipient to avoid duplicates
                                                $recipientNotifications = $selectedBroadcast->notifications->groupBy('to_user_id');
                                            @endphp
                                            @foreach($recipientNotifications as $userId => $userNotifications)
                                                @php
                                                    $recipient = $userNotifications->first()->toUser;
                                                    // Get status for each channel
                                                    $emailNotification = $userNotifications->where('type', 'email')->first();
                                                    $smsNotification = $userNotifications->where('type', 'sms')->first();
                                                @endphp
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 font-medium text-gray-900">
                                                        {{ $recipient->name }}
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-600">
                                                        <div class="flex items-center gap-2">
                                                            {{ $recipient->email }}
                                                            @if($emailNotification)
                                                                @if($emailNotification->status === 'delivered')
                                                                    <span class="text-green-600" title="Delivered">✓</span>
                                                                @elseif($emailNotification->status === 'failed')
                                                                    <span class="text-red-600" title="Failed">✗</span>
                                                                @elseif($emailNotification->status === 'sent')
                                                                    <span class="text-blue-600" title="Sent">→</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-600">
                                                        <div class="flex items-center gap-2">
                                                            {{ $recipient->phone ?? '-' }}
                                                            @if($smsNotification)
                                                                @if($smsNotification->status === 'delivered')
                                                                    <span class="text-green-600" title="Delivered">✓</span>
                                                                @elseif($smsNotification->status === 'failed')
                                                                    <span class="text-red-600" title="Failed">✗</span>
                                                                @elseif($smsNotification->status === 'sent')
                                                                    <span class="text-blue-600" title="Sent">→</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @php
                                                            $allDelivered = $userNotifications->every(fn($n) => $n->status === 'delivered');
                                                            $anyFailed = $userNotifications->contains(fn($n) => $n->status === 'failed');
                                                        @endphp
                                                        @if($allDelivered)
                                                            <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">Delivered</span>
                                                        @elseif($anyFailed)
                                                            <span class="text-xs px-2 py-1 bg-red-100 text-red-800 rounded-full">Failed</span>
                                                        @else
                                                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">Sent</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t flex justify-between">
                        @if(in_array($selectedBroadcast->status, ['draft', 'failed']))
                            <button wire:click="deleteBroadcast('{{ $selectedBroadcast->id }}')"
                                    onclick="return confirm('Are you sure you want to delete this broadcast?')"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Delete Broadcast
                            </button>
                        @else
                            <div></div>
                        @endif
                        <button wire:click="closeDetails"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
