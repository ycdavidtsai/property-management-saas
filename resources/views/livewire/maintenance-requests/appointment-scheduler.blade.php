<div>
    {{-- Current Appointment Status --}}
    @if($request->scheduled_date)
        <div class="mb-4 p-4 rounded-lg border
            {{ $request->scheduling_status === 'confirmed' ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-5 h-5 {{ $request->scheduling_status === 'confirmed' ? 'text-green-600' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium {{ $request->scheduling_status === 'confirmed' ? 'text-green-800' : 'text-yellow-800' }}">
                            @if($request->scheduling_status === 'confirmed')
                                Appointment Confirmed
                            @elseif($request->scheduling_status === 'pending_vendor_confirmation')
                                Awaiting Vendor Confirmation
                            @elseif($request->scheduling_status === 'pending_tenant_confirmation')
                                Awaiting Tenant Confirmation
                            @else
                                Appointment Pending
                            @endif
                        </span>
                    </div>
                    <p class="text-sm {{ $request->scheduling_status === 'confirmed' ? 'text-green-700' : 'text-yellow-700' }}">
                        {{ $request->scheduled_date->format('l, F j, Y') }}
                        @if($request->scheduled_start_time)
                            at {{ date('g:i A', strtotime($request->scheduled_start_time)) }}
                            @if($request->scheduled_end_time)
                                - {{ date('g:i A', strtotime($request->scheduled_end_time)) }}
                            @endif
                        @endif
                    </p>

                    {{-- Show who needs to confirm --}}
                    @if($request->scheduling_status === 'pending_vendor_confirmation' && $userRole === 'tenant')
                        <p class="text-xs text-yellow-600 mt-1">Waiting for vendor to confirm this time</p>
                    @elseif($request->scheduling_status === 'pending_tenant_confirmation' && $userRole === 'vendor')
                        <p class="text-xs text-yellow-600 mt-1">Waiting for tenant to confirm this time</p>
                    @endif
                </div>

                @if(!$viewOnly)
                    <div class="flex gap-2">
                        {{-- Confirm button for the party that needs to confirm --}}
                        @if(($userRole === 'vendor' && $request->scheduling_status === 'pending_vendor_confirmation') ||
                            ($userRole === 'tenant' && $request->scheduling_status === 'pending_tenant_confirmation'))
                            <button
                                wire:click="confirmAppointment"
                                wire:loading.attr="disabled"
                                class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700"
                            >
                                Confirm
                            </button>
                        @endif

                        {{-- Reschedule button (propose different time) --}}
                        <button
                            wire:click="openScheduler"
                            class="px-3 py-1.5 bg-white border text-gray-700 text-sm rounded-lg hover:bg-gray-50"
                        >
                            @if($request->scheduling_status === 'confirmed')
                                Reschedule
                            @else
                                Propose Different Time
                            @endif
                        </button>

                        {{-- Cancel appointment --}}
                        <button
                            wire:click="clearAppointment"
                            wire:confirm="Are you sure you want to cancel this appointment?"
                            class="px-3 py-1.5 text-red-600 text-sm hover:text-red-800"
                        >
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- No appointment scheduled yet --}}
        @if($request->assignedVendor && in_array($request->status, ['assigned', 'pending_acceptance']))
            @if($viewOnly)
                {{-- Landlord view - just show status --}}
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm">Appointment not yet scheduled. Tenant and vendor will coordinate.</span>
                    </div>
                </div>
            @else
                {{-- Tenant/Vendor can schedule --}}
                <button
                    wire:click="openScheduler"
                    class="w-full p-4 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-colors"
                >
                    <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium">
                        @if($userRole === 'tenant')
                            Propose Appointment Time
                        @else
                            Schedule Appointment
                        @endif
                    </span>
                </button>
            @endif
        @endif
    @endif

    {{-- Scheduler Modal/Panel --}}
    @if($showScheduler && !$viewOnly)
        @teleport('body')
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
                <div class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    {{-- Backdrop --}}
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeScheduler"></div>

                    {{-- Modal Content --}}
                    <div class="relative bg-white rounded-xl shadow-xl transform transition-all w-full max-w-2xl">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $request->scheduled_date ? 'Reschedule Appointment' : 'Schedule Appointment' }}
                            </h3>
                            <button wire:click="closeScheduler" class="p-1 hover:bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="p-6">
                            {{-- Vendor Availability Notice --}}
                            @if(!$vendorHasAvailability)
                                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-800">
                                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Vendor has not set up their availability schedule. All time slots are shown.
                                    </p>
                                </div>
                            @endif

                            {{-- Date Selection --}}
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Select Date</label>
                                <div class="grid grid-cols-7 gap-1 sm:gap-2">
                                    @foreach($availableDates as $dateInfo)
                                        <button
                                            type="button"
                                            wire:click="selectDate('{{ $dateInfo['date'] }}')"
                                            @if(!$dateInfo['is_available'] && $vendorHasAvailability) disabled @endif
                                            class="p-2 text-center rounded-lg border transition-colors
                                                {{ $selectedDate === $dateInfo['date']
                                                    ? 'bg-blue-600 text-white border-blue-600'
                                                    : ($dateInfo['is_available'] || !$vendorHasAvailability
                                                        ? 'bg-white hover:bg-gray-50 border-gray-200 text-gray-900'
                                                        : 'bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed')
                                                }}
                                                {{ $dateInfo['is_today'] ? 'ring-2 ring-blue-300' : '' }}"
                                        >
                                            <div class="text-[10px] uppercase {{ $selectedDate === $dateInfo['date'] ? 'text-blue-100' : 'text-gray-500' }}">
                                                {{ $dateInfo['day_short'] }}
                                            </div>
                                            <div class="text-sm font-semibold">{{ $dateInfo['day_number'] }}</div>
                                            @if($dateInfo['is_available'] || !$vendorHasAvailability)
                                                <div class="text-[9px] {{ $selectedDate === $dateInfo['date'] ? 'text-blue-200' : 'text-green-600' }}">
                                                    @if($vendorHasAvailability)
                                                        {{ $dateInfo['available_slots'] }} slots
                                                    @else
                                                        Open
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-[9px] text-gray-400">N/A</div>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Time Slot Selection --}}
                            @if($selectedDate)
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Select Time for {{ \Carbon\Carbon::parse($selectedDate)->format('D, M j') }}
                                    </label>

                                    @if(count($availableSlots) > 0)
                                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                            @foreach($availableSlots as $slot)
                                                <button
                                                    type="button"
                                                    wire:click="selectSlot('{{ $slot['value'] }}')"
                                                    class="p-2 text-sm text-center rounded-lg border transition-colors
                                                        {{ $selectedSlot === $slot['value']
                                                            ? 'bg-blue-600 text-white border-blue-600'
                                                            : 'bg-white hover:bg-blue-50 border-gray-200 text-gray-700' }}"
                                                >
                                                    {{ $slot['start_display'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-6 text-gray-500">
                                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p class="text-sm">No available time slots for this date.</p>
                                            <p class="text-xs text-gray-400 mt-1">Please select a different date.</p>
                                        </div>
                                    @endif

                                    {{-- Show booked slots --}}
                                    @if(count($bookedSlots) > 0)
                                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                            <p class="text-xs text-gray-600 font-medium mb-2">Already booked:</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($bookedSlots as $booked)
                                                    <span class="text-xs px-2 py-1 bg-gray-200 text-gray-600 rounded">
                                                        {{ date('g:i A', strtotime($booked['start'])) }} - {{ date('g:i A', strtotime($booked['end'])) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Notes (optional) --}}
                            @if($selectedDate && $selectedSlot)
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                                    <textarea
                                        wire:model="schedulingNotes"
                                        rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Any additional notes about the appointment..."
                                    ></textarea>
                                </div>
                            @endif

                            {{-- Vendor Weekly Availability Summary --}}
                            @if($vendorHasAvailability)
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-600 mb-2">
                                        {{ $request->assignedVendor->name }}'s Weekly Availability:
                                    </p>
                                    <div class="grid grid-cols-7 gap-1 text-[10px]">
                                        @foreach(['Mon' => 'monday', 'Tue' => 'tuesday', 'Wed' => 'wednesday', 'Thu' => 'thursday', 'Fri' => 'friday', 'Sat' => 'saturday', 'Sun' => 'sunday'] as $short => $day)
                                            <div class="text-center p-1 rounded {{ $vendorAvailability[$day]['available'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                                                <div class="font-medium">{{ $short }}</div>
                                                @if($vendorAvailability[$day]['available'])
                                                    <div class="truncate">{{ date('ga', strtotime($vendorAvailability[$day]['start'])) }}</div>
                                                @else
                                                    <div>Off</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-xl">
                            <button
                                wire:click="closeScheduler"
                                class="px-4 py-2 text-gray-700 bg-white border rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                wire:click="scheduleAppointment"
                                wire:loading.attr="disabled"
                                @if(!$selectedDate || !$selectedSlot) disabled @endif
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span wire:loading.remove wire:target="scheduleAppointment">
                                    @if($userRole === 'tenant')
                                        Propose This Time
                                    @else
                                        {{ $request->scheduling_status === 'pending_vendor_confirmation' ? 'Confirm & Schedule' : 'Propose This Time' }}
                                    @endif
                                </span>
                                <span wire:loading wire:target="scheduleAppointment">
                                    Scheduling...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endteleport
    @endif
</div>
