<div>
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Notifications</h2>
            <p class="text-gray-600 mt-1">
                @if($this->unreadCount > 0)
                    You have {{ $this->unreadCount }} unread {{ Str::plural('notification', $this->unreadCount) }}
                @else
                    You're all caught up!
                @endif
            </p>
        </div>

        @if($this->unreadCount > 0)
            <button wire:click="markAllAsRead" class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                Mark All as Read
            </button>
        @endif
    </div>

    <!-- Success Message -->
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Unread Only Toggle -->
            <div>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.live="filters.unread_only" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Unread Only</span>
                </label>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select wire:model.live="filters.type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Types</option>
                    <option value="email">Email</option>
                    <option value="sms">SMS</option>
                </select>
            </div>

            <!-- Channel Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Channel</label>
                <select wire:model.live="filters.channel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Channels</option>
                    <option value="general">General</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="broadcast">Broadcast</option>
                    <option value="payment">Payment</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Statuses</option>
                    <option value="sent">Sent</option>
                    <option value="delivered">Delivered</option>
                    <option value="failed">Failed</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div class="flex items-end">
                <button wire:click="resetFilters" class="w-full px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="space-y-3">
        @forelse($notifications as $notification)
            <div wire:key="notification-{{ $notification->id }}"
                 class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-all cursor-pointer
                        {{ $notification->read_at ? 'opacity-75' : 'border-l-4 border-l-blue-500' }}"
                 wire:click="viewNotification('{{ $notification->id }}')">
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <!-- Header -->
                            <div class="flex items-center gap-2 mb-2">
                                @if(!$notification->read_at)
                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                @endif

                                <!-- Channel Badge -->
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $notification->channel === 'maintenance' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $notification->channel === 'broadcast' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $notification->channel === 'payment' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $notification->channel === 'general' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($notification->channel) }}
                                </span>

                                <!-- Type Badge -->
                                @if($notification->type === 'email')
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                @elseif($notification->type === 'sms')
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                @endif

                                <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>

                            <!-- Subject/Content -->
                            @if($notification->subject)
                                <h4 class="font-semibold text-gray-800 mb-1">{{ $notification->subject }}</h4>
                            @endif

                            <p class="text-sm text-gray-600 line-clamp-2">{{ $notification->content }}</p>

                            <!-- From User -->
                            @if($notification->fromUser)
                                <div class="mt-2 flex items-center text-xs text-gray-500">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    From: {{ $notification->fromUser->name }}
                                </div>
                            @endif
                        </div>

                        <!-- Status Icon -->
                        <div class="ml-4">
                            @if($notification->status === 'sent' || $notification->status === 'delivered')
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($notification->status === 'failed')
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                <p class="text-gray-600">You don't have any notifications matching your filters.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>

    <!-- Notification Details Modal -->
    @if($showDetails && $selectedNotification)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showDetails') }" x-show="open" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="$wire.closeDetails()"></div>

                <!-- Modal panel -->
                <div class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-800">Notification Details</h3>
                            <button wire:click="closeDetails" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-6">
                        <!-- Metadata -->
                        <div class="mb-6 flex items-center gap-4 text-sm text-gray-500">
                            <div class="flex items-center">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $selectedNotification->channel === 'maintenance' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $selectedNotification->channel === 'broadcast' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $selectedNotification->channel === 'payment' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $selectedNotification->channel === 'general' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($selectedNotification->channel) }}
                                </span>
                            </div>
                            <span>•</span>
                            <span>{{ $selectedNotification->created_at->format('M j, Y g:i A') }}</span>
                            @if($selectedNotification->fromUser)
                                <span>•</span>
                                <span>From: {{ $selectedNotification->fromUser->name }}</span>
                            @endif
                        </div>

                        <!-- Subject -->
                        @if($selectedNotification->subject)
                            <h4 class="text-xl font-bold text-gray-800 mb-4">{{ $selectedNotification->subject }}</h4>
                        @endif

                        <!-- Content -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-800 whitespace-pre-wrap">{{ $selectedNotification->content }}</p>
                        </div>

                        <!-- Delivery Status -->
                        <div class="mb-6">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Delivery Information</h5>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 border rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">Type</div>
                                    <div class="font-medium">{{ ucfirst($selectedNotification->type) }}</div>
                                </div>
                                <div class="p-3 border rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">Status</div>
                                    <div class="flex items-center">
                                        @if($selectedNotification->status === 'sent' || $selectedNotification->status === 'delivered')
                                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        <span class="font-medium">{{ ucfirst($selectedNotification->status) }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($selectedNotification->sent_at)
                                <div class="mt-3 p-3 bg-blue-50 rounded text-sm">
                                    <strong>Sent:</strong> {{ $selectedNotification->sent_at->format('M j, Y g:i A') }}
                                </div>
                            @endif

                            @if($selectedNotification->error_message)
                                <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-800">
                                    <strong>Error:</strong> {{ $selectedNotification->error_message }}
                                </div>
                            @endif
                        </div>

                        <!-- Related Item (if exists) -->
                        @if($selectedNotification->notifiable)
                            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                <h5 class="text-sm font-medium text-purple-800 mb-1">Related To</h5>
                                <p class="text-sm text-purple-700">
                                    {{ class_basename($selectedNotification->notifiable_type) }}
                                    @if(method_exists($selectedNotification->notifiable, 'getRouteKey'))
                                        #{{ substr($selectedNotification->notifiable->getRouteKey(), 0, 8) }}
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t bg-gray-50 flex justify-between">
                        <button
                            wire:click="deleteNotification('{{ $selectedNotification->id }}')"
                            wire:confirm="Are you sure you want to delete this notification?"
                            class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                            Delete
                        </button>
                        <button wire:click="closeDetails" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
