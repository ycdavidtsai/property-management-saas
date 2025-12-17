<?php

namespace App\Livewire\Vendors;

use App\Models\MaintenanceRequest;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class VendorAvailabilitySettings extends Component
{
    // Individual day properties for Livewire 3 reactivity
    public bool $mondayAvailable = false;
    public string $mondayStart = '08:00';
    public string $mondayEnd = '17:00';

    public bool $tuesdayAvailable = false;
    public string $tuesdayStart = '08:00';
    public string $tuesdayEnd = '17:00';

    public bool $wednesdayAvailable = false;
    public string $wednesdayStart = '08:00';
    public string $wednesdayEnd = '17:00';

    public bool $thursdayAvailable = false;
    public string $thursdayStart = '08:00';
    public string $thursdayEnd = '17:00';

    public bool $fridayAvailable = false;
    public string $fridayStart = '08:00';
    public string $fridayEnd = '17:00';

    public bool $saturdayAvailable = false;
    public string $saturdayStart = '09:00';
    public string $saturdayEnd = '14:00';

    public bool $sundayAvailable = false;
    public string $sundayStart = '09:00';
    public string $sundayEnd = '14:00';

    // Exceptions (days off)
    public array $exceptions = [];
    public string $newExceptionDate = '';
    public string $newExceptionReason = '';

    // Blocked time slots
    public array $blockedSlots = [];
    public string $newBlockDate = '';
    public string $newBlockStart = '';
    public string $newBlockEnd = '';
    public string $newBlockReason = '';
    public bool $showBlockSlotModal = false;

    // UI State
    public array $timeOptions = [];
    public bool $hasUnsavedChanges = false;

    protected $listeners = ['refreshAvailability' => '$refresh'];

    public function mount()
    {
        $this->generateTimeOptions();
        $this->loadAvailability();
    }

    /**
     * Generate time options from 6am to 10pm in 30-min increments
     */
    protected function generateTimeOptions(): void
    {
        $this->timeOptions = [];

        for ($hour = 6; $hour <= 22; $hour++) {
            foreach (['00', '30'] as $minute) {
                $time = sprintf('%02d:%s', $hour, $minute);
                $display = date('g:i A', strtotime($time));
                $this->timeOptions[$time] = $display;
            }
        }
    }

    /**
     * Get current vendor
     */
    protected function getVendor(): ?Vendor
    {
        return Vendor::where('user_id', Auth::id())->first();
    }

    /**
     * Load vendor's current availability from database
     */
    protected function loadAvailability(): void
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            Log::warning('VendorAvailability: No vendor found', ['user_id' => Auth::id()]);
            $this->initializeDefaults();
            return;
        }

        $schedule = $vendor->availability_schedule;

        // Handle JSON string vs array
        if (is_string($schedule)) {
            $schedule = json_decode($schedule, true);
        }

        if ($schedule && isset($schedule['weekly'])) {
            $this->loadWeeklyFromSchedule($schedule['weekly']);
            $this->exceptions = $schedule['exceptions'] ?? [];
            $this->blockedSlots = $schedule['blocked_slots'] ?? [];
        } else {
            $this->initializeDefaults();
        }
    }

    /**
     * Load weekly schedule into individual properties
     */
    protected function loadWeeklyFromSchedule(array $weekly): void
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            $availProp = "{$day}Available";
            $startProp = "{$day}Start";
            $endProp = "{$day}End";

            if (isset($weekly[$day])) {
                $this->$availProp = (bool) ($weekly[$day]['available'] ?? false);
                $this->$startProp = $weekly[$day]['start'] ?? '08:00';
                $this->$endProp = $weekly[$day]['end'] ?? '17:00';
            }
        }
    }

    /**
     * Initialize with sensible defaults
     */
    protected function initializeDefaults(): void
    {
        // Weekdays available by default
        $this->mondayAvailable = true;
        $this->tuesdayAvailable = true;
        $this->wednesdayAvailable = true;
        $this->thursdayAvailable = true;
        $this->fridayAvailable = true;
        $this->saturdayAvailable = false;
        $this->sundayAvailable = false;

        $this->exceptions = [];
        $this->blockedSlots = [];
    }

    /**
     * Build schedule array from individual properties
     */
    protected function getScheduleArray(): array
    {
        return [
            'weekly' => [
                'monday' => [
                    'available' => $this->mondayAvailable,
                    'start' => $this->mondayStart,
                    'end' => $this->mondayEnd,
                ],
                'tuesday' => [
                    'available' => $this->tuesdayAvailable,
                    'start' => $this->tuesdayStart,
                    'end' => $this->tuesdayEnd,
                ],
                'wednesday' => [
                    'available' => $this->wednesdayAvailable,
                    'start' => $this->wednesdayStart,
                    'end' => $this->wednesdayEnd,
                ],
                'thursday' => [
                    'available' => $this->thursdayAvailable,
                    'start' => $this->thursdayStart,
                    'end' => $this->thursdayEnd,
                ],
                'friday' => [
                    'available' => $this->fridayAvailable,
                    'start' => $this->fridayStart,
                    'end' => $this->fridayEnd,
                ],
                'saturday' => [
                    'available' => $this->saturdayAvailable,
                    'start' => $this->saturdayStart,
                    'end' => $this->saturdayEnd,
                ],
                'sunday' => [
                    'available' => $this->sundayAvailable,
                    'start' => $this->sundayStart,
                    'end' => $this->sundayEnd,
                ],
            ],
            'exceptions' => $this->exceptions,
            'blocked_slots' => $this->blockedSlots,
        ];
    }

    /**
     * Toggle a day's availability
     */
    public function toggleDay(string $day): void
    {
        $prop = "{$day}Available";
        if (property_exists($this, $prop)) {
            $this->$prop = !$this->$prop;
            $this->hasUnsavedChanges = true;
        }
    }

    /**
     * Add an exception (day off) - WITH CONFLICT CHECK
     */
    public function addException(): void
    {
        $this->validate([
            'newExceptionDate' => 'required|date|after_or_equal:today',
        ], [
            'newExceptionDate.required' => 'Please select a date.',
            'newExceptionDate.after_or_equal' => 'Date must be today or in the future.',
        ]);

        $vendor = $this->getVendor();

        if ($vendor) {
            // Check for existing scheduled appointments on this date
            $conflictingAppointments = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
                ->whereDate('scheduled_date', $this->newExceptionDate)
                ->whereIn('status', ['pending_acceptance', 'assigned', 'in_progress'])
                ->whereIn('scheduling_status', ['confirmed', 'pending_vendor_confirmation', 'pending_tenant_confirmation'])
                ->with(['property', 'unit'])
                ->get();

            if ($conflictingAppointments->count() > 0) {
                $appointmentList = $conflictingAppointments->map(function ($appt) {
                    $time = $appt->scheduled_start_time
                        ? date('g:i A', strtotime($appt->scheduled_start_time))
                        : 'Time TBD';
                    $location = $appt->property->name ?? 'Property';
                    if ($appt->unit) {
                        $location .= " - Unit {$appt->unit->unit_number}";
                    }
                    return "• {$location} at {$time}";
                })->join("\n");

                $this->addError('newExceptionDate',
                    "Cannot mark this day off. You have {$conflictingAppointments->count()} scheduled appointment(s):\n{$appointmentList}\n\nPlease reschedule these appointments first.");
                return;
            }
        }

        // Check if date already exists in exceptions
        $dateExists = collect($this->exceptions)->contains('date', $this->newExceptionDate);
        if ($dateExists) {
            $this->addError('newExceptionDate', 'This date is already marked as a day off.');
            return;
        }

        $this->exceptions[] = [
            'date' => $this->newExceptionDate,
            'reason' => $this->newExceptionReason ?: 'Day Off',
            'available' => false,
        ];

        // Sort by date
        usort($this->exceptions, fn($a, $b) => $a['date'] <=> $b['date']);

        $this->newExceptionDate = '';
        $this->newExceptionReason = '';
        $this->hasUnsavedChanges = true;

        session()->flash('exception-success', 'Day off added. Remember to save your changes.');
    }

    /**
     * Remove an exception
     */
    public function removeException(int $index): void
    {
        if (isset($this->exceptions[$index])) {
            unset($this->exceptions[$index]);
            $this->exceptions = array_values($this->exceptions);
            $this->hasUnsavedChanges = true;
        }
    }

    /**
     * Open block slot modal
     */
    public function openBlockSlotModal(): void
    {
        $this->newBlockDate = '';
        $this->newBlockStart = '';
        $this->newBlockEnd = '';
        $this->newBlockReason = '';
        $this->resetErrorBag(['newBlockDate', 'newBlockStart', 'newBlockEnd']);
        $this->showBlockSlotModal = true;
    }

    /**
     * Close block slot modal
     */
    public function closeBlockSlotModal(): void
    {
        $this->showBlockSlotModal = false;
    }

    /**
     * Add a blocked time slot - WITH CONFLICT CHECK
     */
    public function addBlockedSlot(): void
    {
        $this->validate([
            'newBlockDate' => 'required|date|after_or_equal:today',
            'newBlockStart' => 'required',
            'newBlockEnd' => 'required',
        ], [
            'newBlockDate.required' => 'Please select a date.',
            'newBlockStart.required' => 'Please select a start time.',
            'newBlockEnd.required' => 'Please select an end time.',
        ]);

        // Validate end time is after start time
        if ($this->newBlockStart >= $this->newBlockEnd) {
            $this->addError('newBlockEnd', 'End time must be after start time.');
            return;
        }

        $vendor = $this->getVendor();

        if ($vendor) {
            // Check if this conflicts with an existing appointment
            $conflicts = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
                ->whereDate('scheduled_date', $this->newBlockDate)
                ->whereIn('status', ['pending_acceptance', 'assigned', 'in_progress'])
                ->whereNotNull('scheduled_start_time')
                ->get()
                ->filter(function ($appt) {
                    // Check time overlap
                    $apptStart = $appt->scheduled_start_time;
                    $apptEnd = $appt->scheduled_end_time ?? date('H:i', strtotime($apptStart) + 3600);

                    return $this->timesOverlap(
                        $this->newBlockStart,
                        $this->newBlockEnd,
                        $apptStart,
                        $apptEnd
                    );
                });

            if ($conflicts->count() > 0) {
                $conflict = $conflicts->first();
                $location = $conflict->property->name ?? 'Property';
                if ($conflict->unit) {
                    $location .= " - Unit {$conflict->unit->unit_number}";
                }
                $time = date('g:i A', strtotime($conflict->scheduled_start_time));

                $this->addError('newBlockStart',
                    "This time slot conflicts with an existing appointment at {$location} ({$time}). Please reschedule that appointment first.");
                return;
            }
        }

        // Check for duplicate blocked slots
        $isDuplicate = collect($this->blockedSlots)->contains(function ($slot) {
            return $slot['date'] === $this->newBlockDate
                && $slot['start'] === $this->newBlockStart
                && $slot['end'] === $this->newBlockEnd;
        });

        if ($isDuplicate) {
            $this->addError('newBlockStart', 'This time slot is already blocked.');
            return;
        }

        $this->blockedSlots[] = [
            'date' => $this->newBlockDate,
            'start' => $this->newBlockStart,
            'end' => $this->newBlockEnd,
            'reason' => $this->newBlockReason ?: 'Blocked',
        ];

        // Sort by date then start time
        usort($this->blockedSlots, function ($a, $b) {
            $dateCompare = $a['date'] <=> $b['date'];
            return $dateCompare !== 0 ? $dateCompare : $a['start'] <=> $b['start'];
        });

        $this->showBlockSlotModal = false;
        $this->hasUnsavedChanges = true;

        session()->flash('block-success', 'Time slot blocked. Remember to save your changes.');
    }

    /**
     * Remove a blocked slot
     */
    public function removeBlockedSlot(int $index): void
    {
        if (isset($this->blockedSlots[$index])) {
            unset($this->blockedSlots[$index]);
            $this->blockedSlots = array_values($this->blockedSlots);
            $this->hasUnsavedChanges = true;
        }
    }

    /**
     * Check if two time ranges overlap
     */
    protected function timesOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        $s1 = strtotime($start1);
        $e1 = strtotime($end1);
        $s2 = strtotime($start2);
        $e2 = strtotime($end2);

        return $s1 < $e2 && $e1 > $s2;
    }

    /**
     * Copy weekday hours to weekend
     */
    public function copyWeekdayToWeekend(): void
    {
        // Use Monday as reference
        $this->saturdayAvailable = $this->mondayAvailable;
        $this->saturdayStart = $this->mondayStart;
        $this->saturdayEnd = $this->mondayEnd;

        $this->sundayAvailable = $this->mondayAvailable;
        $this->sundayStart = $this->mondayStart;
        $this->sundayEnd = $this->mondayEnd;

        $this->hasUnsavedChanges = true;
    }

    /**
     * Set all weekdays to same hours
     */
    public function applyToAllWeekdays(): void
    {
        // Use Monday as reference
        $weekdays = ['tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($weekdays as $day) {
            $availProp = "{$day}Available";
            $startProp = "{$day}Start";
            $endProp = "{$day}End";

            $this->$availProp = $this->mondayAvailable;
            $this->$startProp = $this->mondayStart;
            $this->$endProp = $this->mondayEnd;
        }

        $this->hasUnsavedChanges = true;
    }

    /**
     * Save availability to database
     */
    public function save(): void
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            session()->flash('error', 'Vendor profile not found.');
            return;
        }

        $schedule = $this->getScheduleArray();

        $vendor->update([
            'availability_schedule' => $schedule,
        ]);

        $this->hasUnsavedChanges = false;
        session()->flash('success', 'Availability schedule saved successfully!');
    }

    /**
     * Track changes for unsaved indicator
     */
    public function updated($propertyName): void
    {
        // Track changes for any property except UI state
        if (!in_array($propertyName, ['hasUnsavedChanges', 'showBlockSlotModal', 'newBlockDate', 'newBlockStart', 'newBlockEnd', 'newBlockReason', 'newExceptionDate', 'newExceptionReason'])) {
            $this->hasUnsavedChanges = true;
        }
    }

    /**
     * Get upcoming blocked slots (next 30 days)
     */
    public function getUpcomingBlockedSlotsProperty(): array
    {
        $today = date('Y-m-d');
        $thirtyDaysFromNow = date('Y-m-d', strtotime('+30 days'));

        return array_filter($this->blockedSlots, function ($slot) use ($today, $thirtyDaysFromNow) {
            return $slot['date'] >= $today && $slot['date'] <= $thirtyDaysFromNow;
        });
    }

    /**
     * Get upcoming exceptions (next 30 days)
     */
    public function getUpcomingExceptionsProperty(): array
    {
        $today = date('Y-m-d');
        $thirtyDaysFromNow = date('Y-m-d', strtotime('+30 days'));

        return array_filter($this->exceptions, function ($exc) use ($today, $thirtyDaysFromNow) {
            return $exc['date'] >= $today && $exc['date'] <= $thirtyDaysFromNow;
        });
    }

    public function render()
    {
        return view('livewire.vendors.vendor-availability-settings');
    }
}
