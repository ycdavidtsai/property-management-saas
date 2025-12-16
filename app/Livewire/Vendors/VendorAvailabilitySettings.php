<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class VendorAvailabilitySettings extends Component
{
    // Individual day properties - avoids nested array reactivity issues in Livewire 3
    public bool $mondayAvailable = true;
    public string $mondayStart = '08:00';
    public string $mondayEnd = '17:00';

    public bool $tuesdayAvailable = true;
    public string $tuesdayStart = '08:00';
    public string $tuesdayEnd = '17:00';

    public bool $wednesdayAvailable = true;
    public string $wednesdayStart = '08:00';
    public string $wednesdayEnd = '17:00';

    public bool $thursdayAvailable = true;
    public string $thursdayStart = '08:00';
    public string $thursdayEnd = '17:00';

    public bool $fridayAvailable = true;
    public string $fridayStart = '08:00';
    public string $fridayEnd = '17:00';

    public bool $saturdayAvailable = false;
    public string $saturdayStart = '08:00';
    public string $saturdayEnd = '17:00';

    public bool $sundayAvailable = false;
    public string $sundayStart = '08:00';
    public string $sundayEnd = '17:00';

    // Exceptions (days off, holidays)
    public array $exceptions = [];

    // New exception form
    public string $newExceptionDate = '';
    public string $newExceptionReason = '';

    // UI state
    public bool $hasChanges = false;

    // Days config for iteration in blade
    public array $daysConfig = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];

    // Time options
    public array $timeOptions = [];

    public function mount()
    {
        $this->generateTimeOptions();
        $this->loadAvailability();
    }

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

    protected function loadAvailability(): void
    {
        $vendor = $this->getVendor();

        if (!$vendor || !$vendor->availability_schedule) {
            return;
        }

        $schedule = $vendor->availability_schedule;

        if (is_string($schedule)) {
            $schedule = json_decode($schedule, true);
        }

        if (!isset($schedule['weekly'])) {
            return;
        }

        $weekly = $schedule['weekly'];

        // Load each day
        foreach ($this->daysConfig as $day => $label) {
            if (isset($weekly[$day])) {
                $this->{$day . 'Available'} = (bool) ($weekly[$day]['available'] ?? false);
                $this->{$day . 'Start'} = $weekly[$day]['start'] ?? '08:00';
                $this->{$day . 'End'} = $weekly[$day]['end'] ?? '17:00';
            }
        }

        $this->exceptions = $schedule['exceptions'] ?? [];
    }

    protected function getVendor(): ?Vendor
    {
        return Vendor::where('user_id', Auth::id())->first();
    }

    public function updated($property)
    {
        $this->hasChanges = true;
    }

    /**
     * Toggle day availability - called from blade
     */
    public function toggleDay(string $day): void
    {
        $prop = $day . 'Available';

        if (property_exists($this, $prop)) {
            $this->$prop = !$this->$prop;
            $this->hasChanges = true;
        }
    }

    public function addException(): void
    {
        $this->validate([
            'newExceptionDate' => 'required|date|after_or_equal:today',
            'newExceptionReason' => 'nullable|string|max:100',
        ]);

        foreach ($this->exceptions as $exception) {
            if ($exception['date'] === $this->newExceptionDate) {
                $this->addError('newExceptionDate', 'This date is already added.');
                return;
            }
        }

        $this->exceptions[] = [
            'date' => $this->newExceptionDate,
            'reason' => $this->newExceptionReason ?: 'Day off',
            'available' => false,
        ];

        usort($this->exceptions, fn($a, $b) => $a['date'] <=> $b['date']);

        $this->newExceptionDate = '';
        $this->newExceptionReason = '';
        $this->hasChanges = true;
    }

    public function removeException(int $index): void
    {
        if (isset($this->exceptions[$index])) {
            array_splice($this->exceptions, $index, 1);
            $this->hasChanges = true;
        }
    }

    protected function buildWeeklyArray(): array
    {
        $weekly = [];

        foreach ($this->daysConfig as $day => $label) {
            $weekly[$day] = [
                'available' => $this->{$day . 'Available'},
                'start' => $this->{$day . 'Start'},
                'end' => $this->{$day . 'End'},
            ];
        }

        return $weekly;
    }

    public function save(): void
    {
        $weekly = $this->buildWeeklyArray();

        foreach ($weekly as $day => $schedule) {
            if ($schedule['available']) {
                if (empty($schedule['start']) || empty($schedule['end'])) {
                    session()->flash('error', "Start and end times required for {$day}.");
                    return;
                }
                if ($schedule['start'] >= $schedule['end']) {
                    session()->flash('error', "End time must be after start time for {$day}.");
                    return;
                }
            }
        }

        $vendor = $this->getVendor();

        if (!$vendor) {
            session()->flash('error', 'Vendor profile not found.');
            return;
        }

        try {
            $this->exceptions = array_values(array_filter(
                $this->exceptions,
                fn($e) => $e['date'] >= now()->toDateString()
            ));

            $vendor->availability_schedule = [
                'weekly' => $weekly,
                'exceptions' => $this->exceptions,
            ];
            $vendor->save();

            $this->hasChanges = false;
            session()->flash('success', 'Availability schedule saved successfully.');

        } catch (\Exception $e) {
            Log::error('VendorAvailability: Save failed', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to save. Please try again.');
        }
    }

    public function resetToDefaults(): void
    {
        $this->mondayAvailable = true;
        $this->mondayStart = '08:00';
        $this->mondayEnd = '17:00';

        $this->tuesdayAvailable = true;
        $this->tuesdayStart = '08:00';
        $this->tuesdayEnd = '17:00';

        $this->wednesdayAvailable = true;
        $this->wednesdayStart = '08:00';
        $this->wednesdayEnd = '17:00';

        $this->thursdayAvailable = true;
        $this->thursdayStart = '08:00';
        $this->thursdayEnd = '17:00';

        $this->fridayAvailable = true;
        $this->fridayStart = '08:00';
        $this->fridayEnd = '17:00';

        $this->saturdayAvailable = false;
        $this->saturdayStart = '08:00';
        $this->saturdayEnd = '17:00';

        $this->sundayAvailable = false;
        $this->sundayStart = '08:00';
        $this->sundayEnd = '17:00';

        $this->hasChanges = true;
    }

    public function copyWeekdayToWeekend(): void
    {
        $this->saturdayAvailable = $this->mondayAvailable;
        $this->saturdayStart = $this->mondayStart;
        $this->saturdayEnd = $this->mondayEnd;

        $this->sundayAvailable = $this->mondayAvailable;
        $this->sundayStart = $this->mondayStart;
        $this->sundayEnd = $this->mondayEnd;

        $this->hasChanges = true;
    }

    public function applyTimeToAll(): void
    {
        $startTime = $this->mondayStart;
        $endTime = $this->mondayEnd;

        foreach ($this->daysConfig as $day => $label) {
            if ($this->{$day . 'Available'}) {
                $this->{$day . 'Start'} = $startTime;
                $this->{$day . 'End'} = $endTime;
            }
        }

        $this->hasChanges = true;
    }

    public function render()
    {
        return view('livewire.vendors.vendor-availability-settings');
    }
}
