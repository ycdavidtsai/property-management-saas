<?php

namespace App\Livewire\MaintenanceRequests;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceRequestUpdate;
use App\Services\NotificationService;
use App\Services\SchedulingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AppointmentScheduler extends Component
{
    // The maintenance request being scheduled
    public MaintenanceRequest $request;

    // Who is scheduling: 'tenant' or 'vendor'
    // Note: Landlords can VIEW but scheduling is between tenant & vendor
    public string $userRole = 'tenant';

    // Selected date and time
    public ?string $selectedDate = null;
    public ?string $selectedSlot = null;

    // Available slots for selected date
    public array $availableSlots = [];
    public array $bookedSlots = [];

    // Available dates (next 14 days)
    public array $availableDates = [];

    // Vendor availability summary
    public array $vendorAvailability = [];
    public bool $vendorHasAvailability = false;

    // UI state
    public bool $showScheduler = false;
    public bool $isLoading = false;

    // Scheduling notes
    public string $schedulingNotes = '';

    // View-only mode (for landlords)
    public bool $viewOnly = false;

    protected SchedulingService $schedulingService;

    public function boot(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    public function mount(MaintenanceRequest $request, ?string $userRole = null, bool $viewOnly = false)
    {
        $this->request = $request;
        $this->viewOnly = $viewOnly;

        // Auto-detect role if not provided
        if ($userRole) {
            $this->userRole = $userRole;
        } else {
            $currentUser = Auth::user();
            if ($currentUser->role === 'vendor') {
                $this->userRole = 'vendor';
            } elseif ($currentUser->role === 'tenant') {
                $this->userRole = 'tenant';
            } else {
                // Landlord/Manager/Admin - view only by default
                $this->userRole = 'landlord';
                $this->viewOnly = true;
            }
        }

        // Load vendor availability info
        $this->loadVendorAvailability();

        // If already scheduled, pre-select
        if ($request->scheduled_date) {
            $this->selectedDate = $request->scheduled_date->format('Y-m-d');
            if ($request->scheduled_start_time && $request->scheduled_end_time) {
                $this->selectedSlot = $request->scheduled_start_time . '-' . $request->scheduled_end_time;
            }
        }
    }

    /**
     * Load vendor's availability summary and dates
     */
    protected function loadVendorAvailability()
    {
        $vendor = $this->request->assignedVendor;

        if (!$vendor) {
            $this->vendorHasAvailability = false;
            return;
        }

        $this->vendorHasAvailability = $this->schedulingService->vendorHasAvailability($vendor);
        $this->vendorAvailability = $this->schedulingService->getVendorAvailabilitySummary($vendor);
        $this->availableDates = $this->schedulingService->getAvailableDates($vendor, 14, $this->request->id);
    }

    /**
     * Open the scheduler panel
     */
    public function openScheduler()
    {
        $this->showScheduler = true;
        $this->loadVendorAvailability();

        // If date already selected, load slots
        if ($this->selectedDate) {
            $this->loadSlotsForDate($this->selectedDate);
        }
    }

    /**
     * Close the scheduler panel
     */
    public function closeScheduler()
    {
        $this->showScheduler = false;
        $this->resetValidation();
    }

    /**
     * Select a date and load available slots
     */
    public function selectDate(string $date)
    {
        $this->selectedDate = $date;
        $this->selectedSlot = null;
        $this->loadSlotsForDate($date);
    }

    /**
     * Load available slots for a specific date
     */
    protected function loadSlotsForDate(string $date)
    {
        $vendor = $this->request->assignedVendor;

        if (!$vendor) {
            $this->availableSlots = [];
            $this->bookedSlots = [];
            return;
        }

        $result = $this->schedulingService->getAvailableSlots(
            $vendor,
            $date,
            60,
            $this->request->id
        );

        $this->availableSlots = $result['slots'] ?? [];
        $this->bookedSlots = $result['booked_slots'] ?? [];
    }

    /**
     * Select a time slot
     */
    public function selectSlot(string $slot)
    {
        $this->selectedSlot = $slot;
    }

    /**
     * Schedule the appointment
     */
    public function scheduleAppointment()
    {
        if ($this->viewOnly) {
            session()->flash('error', 'You can only view appointments, not schedule them.');
            return;
        }

        if (!$this->selectedDate || !$this->selectedSlot) {
            session()->flash('error', 'Please select a date and time slot.');
            return;
        }

        // Parse the slot value (format: "HH:MM-HH:MM")
        [$startTime, $endTime] = explode('-', $this->selectedSlot);

        $result = $this->schedulingService->scheduleAppointment(
            $this->request,
            $this->selectedDate,
            $startTime,
            $endTime,
            $this->userRole
        );

        if (!$result['success']) {
            session()->flash('error', $result['message']);
            return;
        }

        // Create timeline entry
        $dateFormatted = Carbon::parse($this->selectedDate)->format('D, M j, Y');
        $timeFormatted = date('g:i A', strtotime($startTime)) . ' - ' . date('g:i A', strtotime($endTime));

        $schedulerName = Auth::user()->name;
        $otherParty = $this->userRole === 'vendor' ? 'tenant' : 'vendor';

        $message = match($result['scheduling_status']) {
            'confirmed' => "{$schedulerName} confirmed appointment for {$dateFormatted} at {$timeFormatted}",
            'pending_tenant_confirmation' => "Vendor proposed appointment for {$dateFormatted} at {$timeFormatted}. Awaiting tenant confirmation.",
            'pending_vendor_confirmation' => "Tenant proposed appointment for {$dateFormatted} at {$timeFormatted}. Awaiting vendor confirmation.",
            default => "Appointment scheduled for {$dateFormatted} at {$timeFormatted}",
        };

        if ($this->schedulingNotes) {
            $message .= "\n\nNotes: {$this->schedulingNotes}";
        }

        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->request->id,
            'user_id' => Auth::id(),
            'update_type' => 'scheduling',
            'message' => $message,
            'is_internal' => false,
        ]);

        // Refresh the request
        $this->request->refresh();

        // Close scheduler and show success
        $this->showScheduler = false;
        $this->schedulingNotes = '';

        session()->flash('message', $result['message']);

        // Dispatch event for parent components
        $this->dispatch('appointment-scheduled');
    }

    /**
     * Confirm a proposed appointment (tenant confirms vendor's proposal or vice versa)
     */
    public function confirmAppointment()
    {
        if ($this->viewOnly) {
            return;
        }

        $result = $this->schedulingService->confirmAppointment($this->request);

        if (!$result['success']) {
            session()->flash('error', $result['message']);
            return;
        }

        // Create timeline entry
        $dateFormatted = $this->request->scheduled_date->format('D, M j, Y');
        $timeFormatted = date('g:i A', strtotime($this->request->scheduled_start_time))
            . ' - ' . date('g:i A', strtotime($this->request->scheduled_end_time));

        $confirmerRole = $this->userRole === 'vendor' ? 'Vendor' : 'Tenant';

        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->request->id,
            'user_id' => Auth::id(),
            'update_type' => 'scheduling',
            'message' => "{$confirmerRole} confirmed appointment for {$dateFormatted} at {$timeFormatted}",
            'is_internal' => false,
        ]);

        // Notify landlord that appointment is confirmed
        $this->notifyLandlordOfConfirmation();

        $this->request->refresh();

        session()->flash('message', 'Appointment confirmed!');
        $this->dispatch('appointment-confirmed');
    }

    /**
     * Notify landlord when appointment is confirmed by both parties
     */
    protected function notifyLandlordOfConfirmation(): void
    {
        try {
            // Get the property owner/landlord
            $property = $this->request->property;
            if (!$property) return;

            // Get organization managers/landlords who should be notified
            $landlords = \App\Models\User::where('organization_id', $this->request->organization_id)
                ->whereIn('role', ['landlord', 'manager', 'admin'])
                ->get();

            if ($landlords->isEmpty()) return;

            $dateFormatted = $this->request->scheduled_date->format('D, M j, Y');
            $timeFormatted = date('g:i A', strtotime($this->request->scheduled_start_time));
            $vendorName = $this->request->assignedVendor?->name ?? 'Vendor';
            $tenantName = $this->request->tenant?->name ?? 'Tenant';
            $propertyName = $property->name ?? 'Property';
            $unitNumber = $this->request->unit?->unit_number ?? '';

            $subject = "Appointment Confirmed - {$this->request->title}";
            $content = "An appointment has been confirmed between {$tenantName} and {$vendorName}.\n\n";
            $content .= "Property: {$propertyName}" . ($unitNumber ? " - Unit {$unitNumber}" : "") . "\n";
            $content .= "Date: {$dateFormatted}\n";
            $content .= "Time: {$timeFormatted}\n";
            $content .= "Request: {$this->request->title}";

            $notificationService = app(NotificationService::class);

            foreach ($landlords as $landlord) {
                $notificationService->send(
                    $landlord,
                    $subject,
                    $content,
                    ['email'], // Email notification to landlord
                    'maintenance',
                    $this->request
                );
            }
        } catch (\Exception $e) {
            // Log but don't fail the confirmation
            \Log::warning('Failed to notify landlord of appointment confirmation', [
                'request_id' => $this->request->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear/cancel the scheduled appointment
     */
    public function clearAppointment()
    {
        if ($this->viewOnly) {
            return;
        }

        $oldDate = $this->request->scheduled_date?->format('D, M j, Y');

        $result = $this->schedulingService->clearAppointment($this->request);

        if ($result['success'] && $oldDate) {
            $cancellerRole = $this->userRole === 'vendor' ? 'Vendor' : 'Tenant';

            MaintenanceRequestUpdate::create([
                'maintenance_request_id' => $this->request->id,
                'user_id' => Auth::id(),
                'update_type' => 'scheduling',
                'message' => "{$cancellerRole} cancelled the scheduled appointment for {$oldDate}",
                'is_internal' => false,
            ]);
        }

        $this->request->refresh();
        $this->selectedDate = null;
        $this->selectedSlot = null;
        $this->availableSlots = [];

        session()->flash('message', 'Appointment cleared.');
        $this->dispatch('appointment-cleared');
    }

    public function render()
    {
        return view('livewire.maintenance-requests.appointment-scheduler');
    }
}
