<div class="space-y-6">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <span class="text-red-800">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Weekly Schedule Card --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b">
            <h3 class="font-semibold text-gray-900">Weekly Schedule</h3>
            <p class="text-sm text-gray-600">Set your regular working hours for each day</p>
        </div>

        <div class="p-4 space-y-3">
            {{-- Quick Actions --}}
            <div class="flex flex-wrap gap-2 mb-4">
                <button type="button" wire:click="applyToAllWeekdays"
                        class="px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Apply Mon to All Weekdays
                </button>
                <button type="button" wire:click="copyWeekdayToWeekend"
                        class="px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Copy to Weekend
                </button>
            </div>

            {{-- Days of Week --}}
            @php
                $days = [
                    'monday' => 'Monday',
                    'tuesday' => 'Tuesday',
                    'wednesday' => 'Wednesday',
                    'thursday' => 'Thursday',
                    'friday' => 'Friday',
                    'saturday' => 'Saturday',
                    'sunday' => 'Sunday',
                ];
            @endphp

            @foreach($days as $dayKey => $dayName)
                @php
                    $availProp = "{$dayKey}Available";
                    $startProp = "{$dayKey}Start";
                    $endProp = "{$dayKey}End";
                    $isAvailable = $this->$availProp;
                    $isWeekend = in_array($dayKey, ['saturday', 'sunday']);
                @endphp

                <div class="flex items-center gap-3 py-3 border-b last:border-b-0 {{ $isWeekend ? 'bg-gray-50/50 -mx-4 px-4' : '' }}">
                    {{-- Day Name --}}
                    <div class="w-24 flex-shrink-0">
                        <span class="font-medium text-gray-900 text-sm">{{ $dayName }}</span>
                        @if($isWeekend)
                            <span class="text-xs text-gray-500 block">Weekend</span>
                        @endif
                    </div>

                    {{-- Toggle --}}
                    <div class="flex-shrink-0">
                        <button type="button"
                                wire:click="toggleDay('{{ $dayKey }}')"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $isAvailable ? 'bg-blue-600' : 'bg-gray-200' }}">
                            <span class="sr-only">Toggle {{ $dayName }}</span>
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $isAvailable ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                    {{-- Time Selects --}}
                    <div class="flex-1 flex items-center gap-2 {{ !$isAvailable ? 'opacity-40' : '' }}">
                        <select wire:model.live="{{ $startProp }}"
                                {{ !$isAvailable ? 'disabled' : '' }}
                                class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                            @foreach($timeOptions as $time => $display)
                                <option value="{{ $time }}">{{ $display }}</option>
                            @endforeach
                        </select>

                        <span class="text-gray-500 text-sm">to</span>

                        <select wire:model.live="{{ $endProp }}"
                                {{ !$isAvailable ? 'disabled' : '' }}
                                class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                            @foreach($timeOptions as $time => $display)
                                <option value="{{ $time }}">{{ $display }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Badge --}}
                    <div class="flex-shrink-0 w-20 text-right">
                        @if($isAvailable)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                Available
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                Off
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Days Off / Exceptions Card --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 py-3 bg-gradient-to-r from-amber-50 to-yellow-50 border-b">
            <h3 class="font-semibold text-gray-900">Days Off & Holidays</h3>
            <p class="text-sm text-gray-600">Mark specific dates when you're unavailable</p>
        </div>

        <div class="p-4">
            {{-- Add Exception Form --}}
            <div class="flex flex-col sm:flex-row gap-2 mb-4">
                <div class="flex-1">
                    <input type="date"
                           wire:model="newExceptionDate"
                           min="{{ date('Y-m-d') }}"
                           class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Select date">
                </div>
                <div class="flex-1">
                    <input type="text"
                           wire:model="newExceptionReason"
                           class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Reason (optional)">
                </div>
                <button type="button"
                        wire:click="addException"
                        class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-md hover:bg-amber-700">
                    Add Day Off
                </button>
            </div>

            @error('newExceptionDate')
                <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-700 whitespace-pre-line">{{ $message }}</p>
                </div>
            @enderror

            @if (session()->has('exception-success'))
                <div class="mb-3 p-2 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700">{{ session('exception-success') }}</p>
                </div>
            @endif

            {{-- Exceptions List --}}
            @if(count($exceptions) > 0)
                <div class="space-y-2">
                    @foreach($exceptions as $index => $exception)
                        <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($exception['date'])->format('l, M j, Y') }}
                                    </span>
                                    @if(isset($exception['reason']) && $exception['reason'])
                                        <span class="text-sm text-gray-600 block">{{ $exception['reason'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <button type="button"
                                    wire:click="removeException({{ $index }})"
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">No days off scheduled</p>
                    <p class="text-xs text-gray-400 mt-1">Add vacation days, holidays, or other days you won't be available</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Blocked Time Slots Card --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 py-3 bg-gradient-to-r from-red-50 to-pink-50 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900">Blocked Time Slots</h3>
                    <p class="text-sm text-gray-600">Block specific hours for appointments or personal time</p>
                </div>
                <button type="button"
                        wire:click="openBlockSlotModal"
                        class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                    + Block Time
                </button>
            </div>
        </div>

        <div class="p-4">
            @if (session()->has('block-success'))
                <div class="mb-3 p-2 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700">{{ session('block-success') }}</p>
                </div>
            @endif

            @if(count($blockedSlots) > 0)
                <div class="space-y-2">
                    @foreach($blockedSlots as $index => $block)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($block['date'])->format('D, M j') }}
                                    </span>
                                    <span class="text-gray-600 ml-2">
                                        {{ date('g:i A', strtotime($block['start'])) }} - {{ date('g:i A', strtotime($block['end'])) }}
                                    </span>
                                    @if(isset($block['reason']) && $block['reason'] && $block['reason'] !== 'Blocked')
                                        <span class="text-sm text-gray-500 block">{{ $block['reason'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <button type="button"
                                    wire:click="removeBlockedSlot({{ $index }})"
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">No blocked time slots</p>
                    <p class="text-xs text-gray-400 mt-1">Block specific hours when you have personal appointments</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Block Slot Modal --}}
    @if($showBlockSlotModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     wire:click="closeBlockSlotModal"></div>

                {{-- Modal panel --}}
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Block Time Slot
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Block a specific time period when you're not available for appointments.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        {{-- Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date"
                                   wire:model="newBlockDate"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('newBlockDate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Time Range --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                <select wire:model="newBlockStart"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                    <option value="">Select...</option>
                                    @foreach($timeOptions as $time => $display)
                                        <option value="{{ $time }}">{{ $display }}</option>
                                    @endforeach
                                </select>
                                @error('newBlockStart')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                <select wire:model="newBlockEnd"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                    <option value="">Select...</option>
                                    @foreach($timeOptions as $time => $display)
                                        <option value="{{ $time }}">{{ $display }}</option>
                                    @endforeach
                                </select>
                                @error('newBlockEnd')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Reason --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason (optional)</label>
                            <input type="text"
                                   wire:model="newBlockReason"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                   placeholder="e.g., Doctor appointment">
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="button"
                                wire:click="addBlockedSlot"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                            Block This Time
                        </button>
                        <button type="button"
                                wire:click="closeBlockSlotModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Save Button --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                @if($hasUnsavedChanges)
                    <span class="flex items-center text-amber-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Unsaved changes
                    </span>
                @else
                    <span class="text-sm text-gray-500">All changes saved</span>
                @endif
            </div>

            <button type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Save Schedule</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </div>

    {{-- Help Text --}}
    <div class="p-4 bg-blue-50 rounded-lg">
        <h4 class="text-sm font-medium text-blue-800 mb-2">How this works</h4>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• <strong>Weekly Schedule:</strong> Set your regular working hours for each day</li>
            <li>• <strong>Days Off:</strong> Mark entire days when you're unavailable (vacations, holidays)</li>
            <li>• <strong>Blocked Time:</strong> Block specific hours for personal appointments</li>
            <li>• Tenants will see your availability when scheduling appointments</li>
            <li>• You can't mark a day off if you have existing appointments - reschedule them first</li>
        </ul>
    </div>
</div>
