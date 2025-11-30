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

            <!-- Delivery Channel Selection (Either/Or) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Delivery Channel <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Email Option -->
                    <label class="relative cursor-pointer">
                        <input type="radio"
                               wire:model.live="channel"
                               value="email"
                               class="sr-only peer">
                        <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 p-2 rounded-full bg-blue-100 peer-checked:bg-blue-200">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-900">Email</p>
                                    <p class="text-xs text-gray-500">No character limit</p>
                                </div>
                            </div>
                            @if($channel === 'email')
                                <div class="absolute top-2 right-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </label>

                    <!-- SMS Option -->
                    <label class="relative cursor-pointer">
                        <input type="radio"
                               wire:model.live="channel"
                               value="sms"
                               class="sr-only peer">
                        <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 p-2 rounded-full bg-green-100 peer-checked:bg-green-200">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-900">SMS</p>
                                    <p class="text-xs text-gray-500">Max {{ $smsLimit }} characters</p>
                                </div>
                            </div>
                            @if($channel === 'sms')
                                <div class="absolute top-2 right-2">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </label>
                </div>
                @error('channel')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- SMS Info Box -->
                @if($channel === 'sms')
                    <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-amber-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-amber-800">
                                <p class="font-medium">SMS Messaging Guidelines:</p>
                                <ul class="mt-1 list-disc list-inside text-xs space-y-0.5">
                                    <li>Keep messages under {{ $smsLimit }} characters for best delivery</li>
                                    <li>Messages over 160 characters are split into multiple segments</li>
                                    <li>Each segment is billed separately by Twilio</li>
                                    <li>Avoid special characters/emojis (they reduce character limit)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Message Title <span class="text-red-500">*</span>
                    @if($channel === 'sms')
                        <span class="text-xs text-gray-500 font-normal">(Used for tracking, not sent in SMS)</span>
                    @endif
                </label>
                <input
                    type="text"
                    wire:model="title"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., Important Community Update"
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message Content -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Message Content <span class="text-red-500">*</span>
                    </label>

                    <!-- Character Counter -->
                    @if($channel === 'sms')
                        <div class="flex items-center space-x-3 text-sm">
                            <!-- Character Count -->
                            <span class="{{ $characterCount > $smsLimit ? 'text-red-600 font-semibold' : ($characterCount > $smsLimit * 0.9 ? 'text-amber-600' : 'text-gray-500') }}">
                                {{ $characterCount }} / {{ $smsLimit }}
                            </span>

                            <!-- Segment Count -->
                            <span class="text-gray-400">|</span>
                            <span class="{{ $segmentCount > 2 ? 'text-amber-600' : 'text-gray-500' }}" title="SMS Segments (billed separately)">
                                {{ $segmentCount }} {{ Str::plural('segment', $segmentCount) }}
                            </span>
                        </div>
                    @endif
                </div>

                <textarea
                    wire:model.live="message"
                    rows="{{ $channel === 'sms' ? 4 : 6 }}"
                    maxlength="{{ $channel === 'sms' ? $smsLimit : 10000 }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:border-blue-500 {{ $channel === 'sms' && $characterCount > $smsLimit ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500' }}"
                    placeholder="{{ $channel === 'sms' ? 'Keep it short and concise for SMS...' : 'Type your message here...' }}"
                ></textarea>

                <!-- SMS Character Progress Bar -->
                @if($channel === 'sms')
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = min(100, ($characterCount / $smsLimit) * 100);
                                $colorClass = $characterCount > $smsLimit ? 'bg-red-500' : ($percentage > 90 ? 'bg-amber-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-green-500'));
                            @endphp
                            <div class="{{ $colorClass }} h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>

                        @if($characterCount > $smsLimit)
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Message too long! Please remove {{ $characterCount - $smsLimit }} characters or switch to Email.
                            </p>
                        @elseif($characterCount > 160 && $characterCount <= $smsLimit)
                            <p class="mt-1 text-xs text-amber-600">
                                ⚠️ This message will be sent as {{ $segmentCount }} SMS segments (each billed separately).
                            </p>
                        @endif
                    </div>
                @else
                    <p class="mt-1 text-xs text-gray-500">Minimum 10 characters</p>
                @endif

                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Recipient Selection -->
            <div wire:key="recipient-selection-{{ $channel }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Recipients <span class="text-red-500">*</span>
                </label>

                <!-- Recipient Type Tabs -->
                <div class="flex flex-wrap gap-2 mb-4">
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
                                        wire:model.live="selectedProperties"
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
                                        wire:model.live="selectedUnits"
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
                    <div class="space-y-2" wire:key="specific-users-{{ $channel }}">
                        <label class="block text-sm text-gray-600 mb-2">
                            Select Tenants:
                            <span class="text-xs text-gray-400 ml-1">
                                ({{ $channel === 'sms' ? 'Phone numbers shown' : 'Email addresses shown' }})
                            </span>
                        </label>
                        <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-3 space-y-2">
                            @forelse($tenants as $tenant)
                                <label class="flex items-center py-1 hover:bg-gray-50 rounded px-1 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedUsers"
                                        value="{{ $tenant->id }}"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 flex-1">
                                        <span class="font-medium">{{ $tenant->name }}</span>
                                        @if($channel === 'sms')
                                            {{-- SMS Mode: Show phone number --}}
                                            @if($tenant->phone)
                                                <span class="text-gray-500 ml-1">{{ $tenant->phone }}</span>
                                            @else
                                                <span class="text-red-500 ml-1 text-xs">(No phone - will be skipped)</span>
                                            @endif
                                        @else
                                            {{-- Email Mode: Show email --}}
                                            <span class="text-gray-500 ml-1">{{ $tenant->email }}</span>
                                        @endif
                                    </span>
                                    {{-- Show secondary contact info as small text --}}
                                    <span class="text-xs text-gray-400 ml-2">
                                        @if($channel === 'sms')
                                            {{ $tenant->email }}
                                        @else
                                            {{ $tenant->phone ?? 'No phone' }}
                                        @endif
                                    </span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500">No active tenants found</p>
                            @endforelse
                        </div>

                        {{-- SMS Warning about missing phone numbers --}}
                        @if($channel === 'sms')
                            @php
                                $tenantsWithoutPhone = $tenants->filter(fn($t) => empty($t->phone))->count();
                            @endphp
                            @if($tenantsWithoutPhone > 0)
                                <p class="text-xs text-amber-600 mt-2">
                                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $tenantsWithoutPhone }} {{ Str::plural('tenant', $tenantsWithoutPhone) }} without phone numbers will not receive SMS.
                                </p>
                            @endif
                        @endif
                    </div>
                @endif

                <!-- All Tenants Info -->
                @if($recipientType === 'all_tenants')
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            This will send the {{ $channel === 'sms' ? 'SMS' : 'email' }} to all tenants with active leases in your organization.
                            @if($channel === 'sms')
                                <br><span class="text-xs mt-1 block">Note: Only tenants with phone numbers on file will receive SMS.</span>
                            @endif
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
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg" wire:key="preview-{{ $channel }}">
                    <h4 class="font-medium text-green-800 mb-2">
                        {{ $recipientCount }} {{ Str::plural('Recipient', $recipientCount) }} Selected
                    </h4>
                    <div class="max-h-32 overflow-y-auto space-y-1">
                        @foreach($previewRecipients as $recipient)
                            <div class="text-sm text-green-700">
                                <strong>{{ $recipient['name'] }}</strong>
                                - {{ $channel === 'sms' ? ($recipient['phone'] ?? 'No phone') : $recipient['email'] }}
                                @if($recipient['unit'])
                                    <span class="text-green-600">(Unit {{ $recipient['unit'] }})</span>
                                @endif
                                @if($channel === 'sms' && !$recipient['phone'])
                                    <span class="text-red-500 text-xs">(will be skipped)</span>
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
                    @if($channel === 'sms' && $characterCount > $smsLimit) disabled @endif
                    class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="send">
                        @if($channel === 'email')
                            <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Send Email Broadcast
                        @else
                            <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            Send SMS Broadcast
                        @endif
                    </span>
                    <span wire:loading wire:target="send">
                        Sending...
                    </span>
                </button>
            </div>

        </form>
    </div>
</div>
