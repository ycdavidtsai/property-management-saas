<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Availability Schedule</h2>
        <p class="text-sm text-gray-600 mt-1">Set your weekly working hours and days off</p>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <span class="text-red-800">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border">
        {{-- Weekly Schedule Section --}}
        <div class="p-4 sm:p-6 border-b">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Weekly Hours</h3>

            <div class="space-y-3">
                {{-- Monday --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 p-3 rounded-lg {{ $mondayAvailable ? 'bg-green-50' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between sm:w-32">
                        <span class="font-medium text-gray-700">Monday</span>
                        <button
                            type="button"
                            wire:click="toggleDay('monday')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $mondayAvailable ? 'bg-green-600' : 'bg-gray-200' }}"
                        >
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $mondayAvailable ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>
                    @if($mondayAvailable)
                        <div class="flex items-center gap-2 flex-1">
                            <select wire:model.live="mondayStart" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="text-gray-500">to</span>
                            <select wire:model.live="mondayEnd" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <span class="text-sm text-gray-500 italic">Not available</span>
                    @endif
                </div>

                {{-- Tuesday --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 p-3 rounded-lg {{ $tuesdayAvailable ? 'bg-green-50' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between sm:w-32">
                        <span class="font-medium text-gray-700">Tuesday</span>
                        <button
                            type="button"
                            wire:click="toggleDay('tuesday')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $tuesdayAvailable ? 'bg-green-600' : 'bg-gray-200' }}"
                        >
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $tuesdayAvailable ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>
                    @if($tuesdayAvailable)
                        <div class="flex items-center gap-2 flex-1">
                            <select wire:model.live="tuesdayStart" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="text-gray-500">to</span>
                            <select wire:model.live="tuesdayEnd" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <span class="text-sm text-gray-500 italic">Not available</span>
                    @endif
                </div>

                {{-- Wednesday --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 p-3 rounded-lg {{ $wednesdayAvailable ? 'bg-green-50' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between sm:w-32">
                        <span class="font-medium text-gray-700">Wednesday</span>
                        <button
                            type="button"
                            wire:click="toggleDay('wednesday')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $wednesdayAvailable ? 'bg-green-600' : 'bg-gray-200' }}"
                        >
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $wednesdayAvailable ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>
                    @if($wednesdayAvailable)
                        <div class="flex items-center gap-2 flex-1">
                            <select wire:model.live="wednesdayStart" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="text-gray-500">to</span>
                            <select wire:model.live="wednesdayEnd" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <span class="text-sm text-gray-500 italic">Not available</span>
                    @endif
                </div>

                {{-- Thursday --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 p-3 rounded-lg {{ $thursdayAvailable ? 'bg-green-50' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between sm:w-32">
                        <span class="font-medium text-gray-700">Thursday</span>
                        <button
                            type="button"
                            wire:click="toggleDay('thursday')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $thursdayAvailable ? 'bg-green-600' : 'bg-gray-200' }}"
                        >
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $thursdayAvailable ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>
                    @if($thursdayAvailable)
                        <div class="flex items-center gap-2 flex-1">
                            <select wire:model.live="thursdayStart" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="text-gray-500">to</span>
                            <select wire:model.live="thursdayEnd" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <span class="text-sm text-gray-500 italic">Not available</span>
                    @endif
                </div>

                {{-- Friday --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 p-3 rounded-lg {{ $fridayAvailable ? 'bg-green-50' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between sm:w-32">
                        <span class="font-medium text-gray-700">Friday</span>
                        <button
                            type="button"
                            wire:click="toggleDay('friday')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $fridayAvailable ? 'bg-green-600' : 'bg-gray-200' }}"
                        >
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $fridayAvailable ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>
                    @if($fridayAvailable)
                        <div class="flex items-center gap-2 flex-1">
                            <select wire:model.live="fridayStart" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="text-gray-500">to</span>
                            <select wire:model.live="fridayEnd" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <span class="text-sm text-gray-500 italic">Not available</span>
                    @endif
                </div>

                {{-- Saturday --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 p-3 rounded-lg {{ $saturdayAvailable ? 'bg-green-50' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between sm:w-32">
                        <span class="font-medium text-gray-700">Saturday</span>
                        <button
                            type="button"
                            wire:click="toggleDay('saturday')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $saturdayAvailable ? 'bg-green-600' : 'bg-gray-200' }}"
                        >
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $saturdayAvailable ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>
                    @if($saturdayAvailable)
                        <div class="flex items-center gap-2 flex-1">
                            <select wire:model.live="saturdayStart" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="text-gray-500">to</span>
                            <select wire:model.live="saturdayEnd" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <span class="text-sm text-gray-500 italic">Not available</span>
                    @endif
                </div>

                {{-- Sunday --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 p-3 rounded-lg {{ $sundayAvailable ? 'bg-green-50' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between sm:w-32">
                        <span class="font-medium text-gray-700">Sunday</span>
                        <button
                            type="button"
                            wire:click="toggleDay('sunday')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $sundayAvailable ? 'bg-green-600' : 'bg-gray-200' }}"
                        >
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $sundayAvailable ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>
                    @if($sundayAvailable)
                        <div class="flex items-center gap-2 flex-1">
                            <select wire:model.live="sundayStart" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="text-gray-500">to</span>
                            <select wire:model.live="sundayEnd" class="block w-full sm:w-auto rounded-md border-gray-300 text-sm">
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <span class="text-sm text-gray-500 italic">Not available</span>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap gap-3">
                <button
                    type="button"
                    wire:click="resetToDefaults"
                    class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset to Mon-Fri 8-5
                </button>

                <button
                    type="button"
                    wire:click="copyWeekdayToWeekend"
                    class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copy Monday to weekend
                </button>

                <button
                    type="button"
                    wire:click="applyTimeToAll"
                    class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Apply Monday times to all
                </button>
            </div>
        </div>

        {{-- Exceptions Section --}}
        <div class="p-4 sm:p-6 border-b">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Days Off / Exceptions</h3>
            <p class="text-sm text-gray-600 mb-4">Add specific dates when you're unavailable</p>

            @if(count($exceptions) > 0)
                <div class="space-y-2 mb-4">
                    @foreach($exceptions as $index => $exception)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg" wire:key="exception-{{ $index }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <div>
                                    <span class="font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($exception['date'])->format('D, M j, Y') }}
                                    </span>
                                    @if(!empty($exception['reason']))
                                        <span class="text-sm text-gray-600 ml-2">- {{ $exception['reason'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <button
                                type="button"
                                wire:click="removeException({{ $index }})"
                                class="text-red-600 hover:text-red-800 p-1"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 bg-gray-50 rounded-lg mb-4">
                    <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No days off scheduled</p>
                </div>
            @endif

            <form wire:submit="addException" class="flex flex-col sm:flex-row gap-2">
                <div class="flex-1">
                    <input
                        type="date"
                        wire:model="newExceptionDate"
                        min="{{ now()->toDateString() }}"
                        class="w-full rounded-md border-gray-300 text-sm"
                    >
                    @error('newExceptionDate')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1">
                    <input
                        type="text"
                        wire:model="newExceptionReason"
                        class="w-full rounded-md border-gray-300 text-sm"
                        placeholder="Reason (e.g., Vacation)"
                        maxlength="100"
                    >
                </div>

                <button
                    type="submit"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm font-medium"
                >
                    + Add Day Off
                </button>
            </form>
        </div>

        {{-- Save Button --}}
        <div class="p-4 sm:p-6 bg-gray-50 rounded-b-lg">
            <div class="flex items-center justify-between">
                <div>
                    @if($hasChanges)
                        <span class="text-sm text-amber-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Unsaved changes
                        </span>
                    @else
                        <span class="text-sm text-gray-500">All changes saved</span>
                    @endif
                </div>

                <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 font-medium"
                >
                    <span wire:loading.remove wire:target="save">Save Schedule</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Help Text --}}
    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
        <h4 class="text-sm font-medium text-blue-800 mb-1">How this works</h4>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Tenants will see your availability when scheduling appointments</li>
            <li>• Add specific days off for holidays, vacations, etc.</li>
            <li>• You'll always have the final say on confirming appointments</li>
        </ul>
    </div>
</div>
