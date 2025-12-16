<?php

namespace App\Livewire\Vendors;

use App\Models\MaintenanceRequest;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VendorCalendar extends Component
{
    // Current month/year being displayed
    public int $currentYear;
    public int $currentMonth;

    // Selected date for detail view
    public ?string $selectedDate = null;

    // Jobs for selected date
    public array $selectedDateJobs = [];

    // Vendor info
    public ?Vendor $vendor = null;

    public function mount()
    {
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;

        // Get the vendor for the current logged-in user
        $this->vendor = Vendor::where('user_id', Auth::id())->first();
    }

    /**
     * Navigate to previous month
     */
    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
        $this->selectedDate = null;
        $this->selectedDateJobs = [];
    }

    /**
     * Navigate to next month
     */
    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
        $this->selectedDate = null;
        $this->selectedDateJobs = [];
    }

    /**
     * Go to today's month
     */
    public function goToToday()
    {
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;
        $this->selectedDate = now()->format('Y-m-d');
        $this->loadJobsForDate($this->selectedDate);
    }

    /**
     * Select a date to view jobs
     */
    public function selectDate(string $date)
    {
        $this->selectedDate = $date;
        $this->loadJobsForDate($date);
    }

    /**
     * Close the date detail panel
     */
    public function closeDetail()
    {
        $this->selectedDate = null;
        $this->selectedDateJobs = [];
    }

    /**
     * Load jobs for a specific date
     */
    protected function loadJobsForDate(string $date)
    {
        if (!$this->vendor) {
            $this->selectedDateJobs = [];
            return;
        }

        $jobs = MaintenanceRequest::where('assigned_vendor_id', $this->vendor->id)
            ->whereDate('scheduled_date', $date)
            ->with(['property', 'unit'])
            ->orderBy('scheduled_start_time')
            ->get();

        $this->selectedDateJobs = $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title ?? $job->category,
                'category' => $job->category,
                'property' => $job->property->name ?? 'Unknown',
                'unit' => $job->unit->unit_number ?? null,
                'status' => $job->status,
                'status_label' => $job->status_label,
                'status_color' => $job->status_color,
                'priority' => $job->priority,
                'priority_color' => $job->priority_color,
                'time' => $this->formatJobTime($job),
                'scheduled_start_time' => $job->scheduled_start_time,
                'scheduled_end_time' => $job->scheduled_end_time,
            ];
        })->toArray();
    }

    /**
     * Format job time for display
     */
    protected function formatJobTime($job): string
    {
        if ($job->scheduled_start_time && $job->scheduled_end_time) {
            $start = Carbon::parse($job->scheduled_start_time)->format('g:i A');
            $end = Carbon::parse($job->scheduled_end_time)->format('g:i A');
            return "{$start} - {$end}";
        }

        if ($job->scheduled_start_time) {
            return Carbon::parse($job->scheduled_start_time)->format('g:i A');
        }

        return 'Time TBD';
    }

    /**
     * Get calendar data for the current month
     */
    protected function getCalendarData(): array
    {
        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Get first day of calendar (may include days from previous month)
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);

        // Get last day of calendar (may include days from next month)
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        // Fetch all jobs for this vendor in the date range
        $jobs = collect([]);
        if ($this->vendor) {
            $jobs = MaintenanceRequest::where('assigned_vendor_id', $this->vendor->id)
                ->whereNotNull('scheduled_date')
                ->whereBetween('scheduled_date', [$startOfCalendar->format('Y-m-d'), $endOfCalendar->format('Y-m-d')])
                ->whereIn('status', ['pending_acceptance', 'assigned', 'in_progress', 'completed'])
                ->get()
                ->groupBy(fn($job) => $job->scheduled_date->format('Y-m-d'));
        }

        // Build weeks array
        $weeks = [];
        $currentDate = $startOfCalendar->copy();

        while ($currentDate <= $endOfCalendar) {
            $week = [];

            for ($i = 0; $i < 7; $i++) {
                $dateKey = $currentDate->format('Y-m-d');
                $dayJobs = $jobs->get($dateKey, collect([]));

                $week[] = [
                    'date' => $dateKey,
                    'day' => $currentDate->day,
                    'isCurrentMonth' => $currentDate->month === $this->currentMonth,
                    'isToday' => $currentDate->isToday(),
                    'isPast' => $currentDate->isPast() && !$currentDate->isToday(),
                    'jobs' => $dayJobs->map(fn($job) => [
                        'id' => $job->id,
                        'status' => $job->status,
                        'status_color' => $job->status_color,
                        'priority' => $job->priority,
                        'category' => $job->category,
                    ])->toArray(),
                    'jobCount' => $dayJobs->count(),
                ];

                $currentDate->addDay();
            }

            $weeks[] = $week;
        }

        return [
            'monthName' => $startOfMonth->format('F Y'),
            'weeks' => $weeks,
        ];
    }

    /**
     * Get status color classes for Tailwind
     */
    public function getStatusClasses(string $status): string
    {
        return match($status) {
            'pending_acceptance' => 'bg-yellow-400',
            'assigned' => 'bg-blue-400',
            'in_progress' => 'bg-indigo-500',
            'completed' => 'bg-green-400',
            default => 'bg-gray-400',
        };
    }

    public function render()
    {
        $calendarData = $this->getCalendarData();

        return view('livewire.vendors.vendor-calendar', [
            'monthName' => $calendarData['monthName'],
            'weeks' => $calendarData['weeks'],
        ]);
    }
}
