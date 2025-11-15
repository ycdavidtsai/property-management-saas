<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border">
        <!-- Header -->
        <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h2 class="text-xl font-semibold text-gray-800">Send Broadcast Message</h2>
            <p class="text-sm text-gray-600 mt-1">Send email or SMS notifications to your tenants</p>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-800 font-medium">{{ session('message') }}</p>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="send" class="p-6 space-y-6">

            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Message Title <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model.defer="title"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., Important Community Update"
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message Content -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Message Content <span class="text-red-500">*</span>
                </label>
                <textarea
                    wire:model.defer="message"
                    rows="6"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Type your message here..."
                ></textarea>
                <p class="mt-1 text-xs text-gray-500">Minimum 10 characters</p>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Delivery Channels -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Delivery Channels <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="channels"
                            value="email"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Email
                        </span>
                    </label>
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="channels"
                            value="sms"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            SMS
                        </span>
                    </label>
                </div>
                @error('channels')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Recipient Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Recipients <span class="text-red-500">*</span>
                </label>

                <!-- Recipient Type Tabs -->
                <div class="flex gap-2 mb-4">
                    <button
                        type="button"
                        wire:click="$set('recipientType', 'all_tenants')"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $recipientType === 'all_tenants' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        All Tenants
                    </button>
                    <button
                        type="button"
                        wire:click="$set('recipientType', 'property')"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $recipientType === 'property' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        By Property
                    </button>
                    <button
                        type="button"
                        wire:click="$set('recipientType', 'unit')"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $recipientType === 'unit' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        By Unit
                    </button>
                    <button
                        type="button"
                        wire:click="$set('recipientType', 'specific_users')"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $recipientType === 'specific_users' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        Specific Tenants
                    </button>
                </div>

                <!-- Property Selection -->
                @if($recipientType === 'property')
                    <div class="space-y-2">
                        <label class="block text-sm text-gray-600 mb-2">Select Properties:</label>
                        <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-3 space-y-2">
                            @forelse($properties as $property)
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedProperties"
                                        value="{{ $property->id }}"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">{{ $property->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500">No properties found</p>
                            @endforelse
                        </div>
                    </div>
                @endif

                <!-- Unit Selection -->
                @if($recipientType === 'unit')
                    <div class="space-y-2">
                        <label class="block text-sm text-gray-600 mb-2">Select Units:</label>
                        <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-3 space-y-2">
                            @forelse($units as $unit)
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedUnits"
                                        value="{{ $unit->id }}"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">
                                        {{ $unit->property->name }} - Unit {{ $unit->unit_number }}
                                    </span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500">No units found</p>
                            @endforelse
                        </div>
                    </div>
                @endif

                <!-- Specific Users Selection -->
                @if($recipientType === 'specific_users')
                    <div class="space-y-2">
                        <label class="block text-sm text-gray-600 mb-2">Select Tenants:</label>
                        <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-3 space-y-2">
                            @forelse($tenants as $tenant)
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedUsers"
                                        value="{{ $tenant->id }}"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">
                                        {{ $tenant->name }} ({{ $tenant->email }}) ({{ $tenant->phone }})
                                    </span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500">No active tenants found</p>
                            @endforelse
                        </div>
                    </div>
                @endif

                <!-- All Tenants Info -->
                @if($recipientType === 'all_tenants')
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            This will send the message to all tenants with active leases in your organization.
                        </p>
                    </div>
                @endif

                @error('recipientType')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview Recipients Button -->
            <div>
                <button
                    type="button"
                    wire:click="previewRecipientsList"
                    wire:loading.attr="disabled"
                    class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="previewRecipientsList">
                        Preview Recipients ({{ $recipientCount }})
                    </span>
                    <span wire:loading wire:target="previewRecipientsList">
                        Loading...
                    </span>
                </button>
            </div>

            <!-- Recipients Preview -->
            @if($showPreview && $recipientCount > 0)
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-medium text-green-800 mb-2">
                        {{ $recipientCount }} {{ Str::plural('Recipient', $recipientCount) }} Selected
                    </h4>
                    <div class="max-h-32 overflow-y-auto space-y-1">
                        @foreach($previewRecipients as $recipient)
                            <div class="text-sm text-green-700">
                                <strong>{{ $recipient['name'] }}</strong> - {{ $recipient['email'] }}/ {{ $recipient['phone'] }}
                                @if($recipient['unit'])
                                    <span class="text-green-600">( {{ $recipient['property'] }}/ Unit: {{ $recipient['unit'] }})</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="send">
                        <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Send Broadcast
                    </span>
                    <span wire:loading wire:target="send">
                        Sending...
                    </span>
                </button>
            </div>

        </form>
    </div>
</div>
