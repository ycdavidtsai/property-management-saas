<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow-sm border">
        <!-- Header -->
        <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h2 class="text-xl font-semibold text-gray-800">Notification Preferences</h2>
            <p class="text-sm text-gray-600 mt-1">Choose how you'd like to receive notifications</p>
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

        <!-- Error Message -->
        @if (session()->has('error'))
            <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="save" class="p-6">
            <!-- Quick Actions -->
            <div class="mb-6 flex gap-3">
                <button
                    type="button"
                    wire:click="enableAll"
                    class="px-4 py-2 text-sm font-medium text-green-600 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors border border-green-300">
                    Enable All
                </button>
                <button
                    type="button"
                    wire:click="disableAll"
                    class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors border border-red-300">
                    Disable All
                </button>
            </div>

            <!-- Debug Info (remove after testing) -->
            {{-- <div class="mb-4 p-3 bg-gray-100 rounded text-xs font-mono">
                <strong>Debug:</strong> {{ json_encode($preferences) }}
            </div> --}}

            <!-- Notification Channels -->
            <div class="space-y-6">

                <!-- Maintenance Notifications -->
                <div class="p-4 border rounded-lg">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 flex items-center">
                                <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Maintenance Requests
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Updates about your maintenance requests</p>
                        </div>
                    </div>
                    <div class="flex gap-6 mt-3">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="preferences.maintenance.email"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Email</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="preferences.maintenance.sms"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">SMS</span>
                        </label>
                    </div>
                </div>

                <!-- Broadcast Messages -->
                <div class="p-4 border rounded-lg">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 flex items-center">
                                <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                                Broadcast Messages
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Important announcements from your landlord</p>
                        </div>
                    </div>
                    <div class="flex gap-6 mt-3">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="preferences.broadcast.email"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Email</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="preferences.broadcast.sms"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">SMS</span>
                        </label>
                    </div>
                </div>

                <!-- Payment Notifications -->
                <div class="p-4 border rounded-lg">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Payment Reminders
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Rent and payment notifications</p>
                        </div>
                    </div>
                    <div class="flex gap-6 mt-3">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="preferences.payment.email"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Email</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="preferences.payment.sms"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">SMS</span>
                        </label>
                    </div>
                </div>

                <!-- General Notifications -->
                <div class="p-4 border rounded-lg">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                General Notifications
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">System updates and general information</p>
                        </div>
                    </div>
                    <div class="flex gap-6 mt-3">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="preferences.general.email"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Email</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="preferences.general.sms"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">SMS</span>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Info Box -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">About notification preferences:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Email notifications are always recommended for important updates</li>
                            <li>SMS notifications may incur charges from your carrier</li>
                            <li>You can change these preferences at any time</li>
                            <li>Critical security notifications will always be sent via email</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="save">Save Preferences</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>
