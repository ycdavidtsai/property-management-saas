<div class="max-w-full mx-auto">
    <div class="bg-white rounded-lg shadow-sm border">
        <!-- Header -->
        <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Broadcast History</h2>
                    <p class="text-sm text-gray-600 mt-1">View past broadcast messages and delivery statistics</p>
                </div>
                <a href="{{ route('communications.compose') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    New Broadcast
                </a>
            </div>
        </div>

        <!-- SMS Usage Summary (Current Month) -->
        @if($currentMonthUsage['total_segments'] > 0)
            <div class="px-6 py-3 bg-blue-50 border-b border-blue-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <span class="text-sm font-medium text-blue-800">
                            This Month's SMS Usage:
                        </span>
                    </div>
                    <div class="flex items-center space-x-4 text-sm">
                        <span class="text-blue-700">
                            <strong>{{ number_format($currentMonthUsage['total_segments']) }}</strong> segments
                        </span>
                        <span class="text-blue-400">|</span>
                        <span class="text-blue-700">
                            <strong>{{ number_format($currentMonthUsage['total_sms_sent']) }}</strong> SMS sent
                        </span>
                        <span class="text-blue-400">|</span>
                        <span class="text-blue-700">
                            <strong>{{ $currentMonthUsage['total_broadcasts'] }}</strong> broadcasts
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="px-6 py-4 border-b bg-gray-50">
            <div class="flex flex-wrap gap-4 items-center">
                <!-- Channel Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Channel</label>
                    <select wire:model.live="filterChannel" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                        <option value="">All Channels</option>
                        <option value="email">Email Only</option>
                        <option value="sms">SMS Only</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select wire:model.live="filterStatus" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                        <option value="">All Status</option>
                        <option value="sent">Sent</option>
                        <option value="sending">Sending</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="failed">Failed</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" wire:model.live="filterDateFrom" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                    <input type="date" wire:model.live="filterDateTo" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                </div>

                <!-- Reset Filters -->
                @if($filterChannel || $filterStatus || $filterDateFrom || $filterDateTo)
                    <div class="flex items-end">
                        <button wire:click="resetFilters" class="text-sm text-gray-500 hover:text-gray-700 underline">
                            Reset
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Broadcasts Table -->
        <div class="overflow-x-visible sm:overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Broadcast
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Channel
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Recipients
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Delivery
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            SMS Segments
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sent
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($broadcasts as $broadcast)
                        <tr class="hover:bg-gray-50">
                            <!-- Title & Preview -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $broadcast->title }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">
                                    {{ Str::limit($broadcast->message, 50) }}
                                </div>
                            </td>

                            <!-- Channel -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($broadcast->is_sms)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                        </svg>
                                        SMS
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Email
                                    </span>
                                @endif
                            </td>

                            <!-- Recipients -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $broadcast->recipient_count }}
                            </td>

                            <!-- Delivery Stats -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    @if($broadcast->total_sent > 0)
                                        <span class="text-green-600">{{ $broadcast->total_delivered }} delivered</span>
                                        @if($broadcast->total_failed > 0)
                                            <span class="text-red-600 ml-1">({{ $broadcast->total_failed }} failed)</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                                @if($broadcast->total_sent > 0)
                                    <div class="text-xs text-gray-500">
                                        {{ $broadcast->delivery_rate }}% delivery rate
                                    </div>
                                @endif
                            </td>

                            <!-- SMS Segments -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($broadcast->has_segment_data)
                                    <div class="text-sm text-gray-900">
                                        {{ number_format($broadcast->sms_segments_total) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $broadcast->sms_segments_per_message }} per msg
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($broadcast->status)
                                    @case('sent')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sent
                                        </span>
                                        @break
                                    @case('sending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="animate-spin -ml-0.5 mr-1.5 h-3 w-3" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Sending
                                        </span>
                                        @break
                                    @case('scheduled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Scheduled
                                        </span>
                                        @break
                                    @case('failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Failed
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($broadcast->status) }}
                                        </span>
                                @endswitch
                            </td>

                            <!-- Sent Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($broadcast->sent_at)
                                    <div>{{ $broadcast->sent_at->format('M j, Y') }}</div>
                                    <div class="text-xs">{{ $broadcast->sent_at->format('g:i A') }}</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="viewDetails('{{ $broadcast->id }}')"
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    View
                                </button>
                                @if(in_array($broadcast->status, ['draft', 'failed']))
                                    <button wire:click="deleteBroadcast('{{ $broadcast->id }}')"
                                            wire:confirm="Are you sure you want to delete this broadcast?"
                                            class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-4 text-lg font-medium">No broadcasts yet</p>
                                <p class="mt-2">Send your first broadcast message to tenants.</p>
                                <a href="{{ route('communications.compose') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Create Broadcast
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($broadcasts->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $broadcasts->links() }}
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    @if($selectedBroadcast)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0" wire:click="closeDetails"></div>

                <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-auto z-10 max-h-[90vh] overflow-y-auto">
                    <!-- Modal Header -->
                    <div class="sticky top-0 bg-white px-6 py-4 border-b flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Broadcast Details</h3>
                        <button wire:click="closeDetails" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4 space-y-6">
                        <!-- Title & Message -->
                        <div>
                            <h4 class="text-xl font-medium text-gray-900">{{ $selectedBroadcast->title }}</h4>
                            <p class="mt-2 text-gray-600 whitespace-pre-wrap">{{ $selectedBroadcast->message }}</p>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500">Channel</div>
                                <div class="text-lg font-semibold">{{ $selectedBroadcast->channel_label }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500">Recipients</div>
                                <div class="text-lg font-semibold">{{ $selectedBroadcast->recipient_count }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500">Delivered</div>
                                <div class="text-lg font-semibold text-green-600">{{ $selectedBroadcast->total_delivered }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500">Failed</div>
                                <div class="text-lg font-semibold {{ $selectedBroadcast->total_failed > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $selectedBroadcast->total_failed }}
                                </div>
                            </div>
                        </div>

                        <!-- SMS Segments Section (only for SMS broadcasts) -->
                        @if($selectedBroadcast->has_segment_data)
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h5 class="font-medium text-green-800 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                    SMS Segment Details
                                </h5>
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-green-600">Segments per Message:</span>
                                        <span class="font-semibold ml-1">{{ $selectedBroadcast->sms_segments_per_message }}</span>
                                    </div>
                                    <div>
                                        <span class="text-green-600">Total Segments:</span>
                                        <span class="font-semibold ml-1">{{ number_format($selectedBroadcast->sms_segments_total) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-green-600">Est. Cost:</span>
                                        <span class="font-semibold ml-1">{{ $selectedBroadcast->estimated_cost_display ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <p class="text-xs text-green-600 mt-2">
                                    * Cost estimate based on $0.0079/segment. Actual cost may vary.
                                </p>
                            </div>
                        @endif

                        <!-- Delivery Breakdown -->
                        @if($selectedBroadcast->is_email)
                            <div>
                                <h5 class="font-medium text-gray-700 mb-2">Email Delivery</h5>
                                <div class="flex space-x-4 text-sm">
                                    <span class="text-gray-600">Sent: {{ $selectedBroadcast->emails_sent }}</span>
                                    <span class="text-green-600">Delivered: {{ $selectedBroadcast->emails_delivered }}</span>
                                    <span class="text-red-600">Failed: {{ $selectedBroadcast->emails_failed }}</span>
                                </div>
                            </div>
                        @endif

                        @if($selectedBroadcast->is_sms)
                            <div>
                                <h5 class="font-medium text-gray-700 mb-2">SMS Delivery</h5>
                                <div class="flex space-x-4 text-sm">
                                    <span class="text-gray-600">Sent: {{ $selectedBroadcast->sms_sent }}</span>
                                    <span class="text-green-600">Delivered: {{ $selectedBroadcast->sms_delivered }}</span>
                                    <span class="text-red-600">Failed: {{ $selectedBroadcast->sms_failed }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Meta Info -->
                        <div class="text-sm text-gray-500 border-t pt-4">
                            <div class="flex justify-between">
                                <span>Sent by: {{ $selectedBroadcast->sender?->name ?? 'Unknown' }}</span>
                                <span>{{ $selectedBroadcast->sent_at?->format('M j, Y g:i A') ?? 'Not sent' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t rounded-b-lg">
                        <button wire:click="closeDetails" class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
