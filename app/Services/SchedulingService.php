<?php

namespace App\Services;

use App\Models\MaintenanceRequest;
use App\Models\Vendor;
use Carbon\Carbon;

class SchedulingService
{
    /**
     * Get available time slots for a vendor on a specific date
     * Takes into account: weekly availability, exceptions, and existing bookings
     */
    public function getAvailableSlots(
        Vendor $vendor,
        string $date,
        int $slotDurationMinutes = 60,
        ?string $excludeRequestId = null
    ): array {
        $dateObj = Carbon::parse($date);

        // Check if date is in the past
        if ($dateObj->isPast() && !$dateObj->isToday()) {
            return [
                'available' => false,
                'reason' => 'Cannot schedule in the past',
                'slots' => [],
            ];
        }

        // Get vendor's base availability for this day
        $baseSlots = $this->getVendorBaseSlotsForDay($vendor, $dateObj, $slotDurationMinutes);

        if (empty($baseSlots)) {
            return [
                'available' => false,
                'reason' => 'Vendor not available on ' . $dateObj->format('l') . 's',
                'slots' => [],
            ];
        }

        // Get existing bookings for this date
        $existingBookings = $this->getExistingBookings($vendor, $date, $excludeRequestId);

        // Filter out conflicting slots
        $availableSlots = $this->filterConflictingSlots($baseSlots, $existingBookings);

        // If today, filter out past time slots
        if ($dateObj->isToday()) {
            $availableSlots = $this->filterPastSlots($availableSlots);
        }

        return [
            'available' => true,
            'date' => $date,
            'day_name' => $dateObj->format('l'),
            'formatted_date' => $dateObj->format('D, M j, Y'),
            'slots' => $availableSlots,
            'booked_slots' => $existingBookings,
            'total_slots' => count($baseSlots),
            'available_count' => count($availableSlots),
        ];
    }

    /**
     * Filter out time slots that have already passed today
     */
    protected function filterPastSlots(array $slots): array
    {
        $currentTime = now()->format('H:i');

        return array_values(array_filter($slots, function ($slot) use ($currentTime) {
            return $slot['start'] > $currentTime;
        }));
    }

    /**
     * Get vendor's base time slots for a day based on their availability schedule
     */
    protected function getVendorBaseSlotsForDay(
        Vendor $vendor,
        Carbon $date,
        int $slotDurationMinutes
    ): array {
        $dayOfWeek = strtolower($date->format('l'));
        $dateString = $date->format('Y-m-d');

        $schedule = $vendor->availability_schedule ?? [];

        // Check exceptions first (day off, special hours)
        $exceptions = $schedule['exceptions'] ?? [];
        foreach ($exceptions as $exception) {
            if (($exception['date'] ?? '') === $dateString) {
                if (!($exception['available'] ?? false)) {
                    return []; // Day off
                }
                // Has special hours for this day
                return $this->generateTimeSlots(
                    $dateString,
                    $exception['start'] ?? '08:00',
                    $exception['end'] ?? '17:00',
                    $slotDurationMinutes
                );
            }
        }

        // Check weekly schedule
        $weekly = $schedule['weekly'] ?? [];
        if (!isset($weekly[$dayOfWeek]) || !($weekly[$dayOfWeek]['available'] ?? false)) {
            return []; // Not available this day
        }

        $daySchedule = $weekly[$dayOfWeek];
        return $this->generateTimeSlots(
            $dateString,
            $daySchedule['start'] ?? '08:00',
            $daySchedule['end'] ?? '17:00',
            $slotDurationMinutes
        );
    }

    /**
     * Generate time slots between start and end times
     */
    protected function generateTimeSlots(
        string $date,
        string $startTime,
        string $endTime,
        int $durationMinutes
    ): array {
        $slots = [];
        $current = Carbon::parse($date . ' ' . $startTime);
        $end = Carbon::parse($date . ' ' . $endTime);

        while ($current->copy()->addMinutes($durationMinutes) <= $end) {
            $slotEnd = $current->copy()->addMinutes($durationMinutes);

            $slots[] = [
                'start' => $current->format('H:i'),
                'end' => $slotEnd->format('H:i'),
                'display' => $current->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                'start_display' => $current->format('g:i A'),
                'value' => $current->format('H:i') . '-' . $slotEnd->format('H:i'),
            ];

            $current->addMinutes($durationMinutes);
        }

        return $slots;
    }

    /**
     * Get existing bookings for vendor on a date
     */
    protected function getExistingBookings(
        Vendor $vendor,
        string $date,
        ?string $excludeRequestId = null
    ): array {
        $query = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
            ->whereDate('scheduled_date', $date)
            ->whereNotNull('scheduled_start_time')
            ->whereIn('status', ['pending_acceptance', 'assigned', 'in_progress']);

        if ($excludeRequestId) {
            $query->where('id', '!=', $excludeRequestId);
        }

        return $query->get()->map(function ($request) {
            // Handle null end times - default to 1 hour after start
            $endTime = $request->scheduled_end_time;
            if (!$endTime && $request->scheduled_start_time) {
                $startCarbon = Carbon::parse($request->scheduled_start_time);
                $endTime = $startCarbon->addHour()->format('H:i');
            }

            return [
                'id' => $request->id,
                'start' => $request->scheduled_start_time,
                'end' => $endTime,
                'title' => $request->title ?? $request->category,
                'status' => $request->status,
            ];
        })->filter(function ($booking) {
            // Filter out any bookings that still have null start or end
            return $booking['start'] && $booking['end'];
        })->values()->toArray();
    }

    /**
     * Filter out slots that conflict with existing bookings
     */
    protected function filterConflictingSlots(array $slots, array $bookings): array
    {
        if (empty($bookings)) {
            return $slots;
        }

        return array_values(array_filter($slots, function ($slot) use ($bookings) {
            foreach ($bookings as $booking) {
                if ($this->timesOverlap(
                    $slot['start'], $slot['end'],
                    $booking['start'], $booking['end']
                )) {
                    return false;
                }
            }
            return true;
        }));
    }

    /**
     * Check if two time ranges overlap
     */
    protected function timesOverlap(
        ?string $start1, ?string $end1,
        ?string $start2, ?string $end2
    ): bool {
        // If any value is null, can't determine overlap - assume no conflict
        if (!$start1 || !$end1 || !$start2 || !$end2) {
            return false;
        }

        $s1 = $this->timeToMinutes($start1);
        $e1 = $this->timeToMinutes($end1);
        $s2 = $this->timeToMinutes($start2);
        $e2 = $this->timeToMinutes($end2);

        return $s1 < $e2 && $e1 > $s2;
    }

    /**
     * Convert time string to minutes since midnight
     */
    protected function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);
        return (int)$parts[0] * 60 + (int)($parts[1] ?? 0);
    }

    /**
     * Get available dates for the next N days with slot counts
     */
    public function getAvailableDates(
        Vendor $vendor,
        int $daysAhead = 14,
        ?string $excludeRequestId = null
    ): array {
        $dates = [];
        $today = Carbon::today();

        for ($i = 0; $i <= $daysAhead; $i++) {
            $date = $today->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');

            $slots = $this->getAvailableSlots($vendor, $dateString, 60, $excludeRequestId);

            $dates[] = [
                'date' => $dateString,
                'day_name' => $date->format('l'),
                'day_short' => $date->format('D'),
                'day_number' => $date->format('j'),
                'month_day' => $date->format('M j'),
                'is_today' => $date->isToday(),
                'is_available' => $slots['available'] && count($slots['slots']) > 0,
                'available_slots' => count($slots['slots'] ?? []),
                'is_weekend' => $date->isWeekend(),
            ];
        }

        return $dates;
    }

    /**
     * Schedule an appointment (set date/time on request)
     *
     * Scheduling is between TENANT and VENDOR:
     * - Tenant proposes → pending_vendor_confirmation
     * - Vendor proposes → pending_tenant_confirmation
     * - When other party confirms → confirmed
     * - Landlord is notified after confirmation
     */
    public function scheduleAppointment(
        MaintenanceRequest $request,
        string $date,
        string $startTime,
        string $endTime,
        string $scheduledBy = 'tenant'
    ): array {
        $vendor = $request->assignedVendor;

        if (!$vendor) {
            return ['success' => false, 'message' => 'No vendor assigned to this request.'];
        }

        // Validate date is not in past
        $dateObj = Carbon::parse($date);
        if ($dateObj->isPast() && !$dateObj->isToday()) {
            return ['success' => false, 'message' => 'Cannot schedule in the past.'];
        }

        // If today, check time is not in past
        if ($dateObj->isToday() && $startTime <= now()->format('H:i')) {
            return ['success' => false, 'message' => 'Cannot schedule a time that has already passed.'];
        }

        // Check for conflicts
        $slots = $this->getAvailableSlots($vendor, $date, 60, $request->id);

        if (!$slots['available']) {
            return ['success' => false, 'message' => $slots['reason'] ?? 'Vendor not available on this date.'];
        }

        // Check if the specific slot conflicts
        $hasConflict = false;
        foreach ($slots['booked_slots'] as $booking) {
            if ($this->timesOverlap($startTime, $endTime, $booking['start'], $booking['end'])) {
                $hasConflict = true;
                break;
            }
        }

        if ($hasConflict) {
            return ['success' => false, 'message' => 'This time slot conflicts with another appointment.'];
        }

        // Determine scheduling status based on who scheduled
        // Tenant proposes → needs vendor confirmation
        // Vendor proposes → needs tenant confirmation
        $schedulingStatus = match($scheduledBy) {
            'vendor' => 'pending_tenant_confirmation',
            'tenant' => 'pending_vendor_confirmation',
            default => 'pending_confirmation',
        };

        // If this is a response to existing proposal (confirming the other party's time)
        // and the date/time matches, mark as confirmed
        if ($request->scheduled_date && $request->scheduled_start_time) {
            $existingDate = $request->scheduled_date->format('Y-m-d');
            $existingStart = $request->scheduled_start_time;
            $existingEnd = $request->scheduled_end_time;

            // Check if confirming the same slot
            if ($existingDate === $date && $existingStart === $startTime && $existingEnd === $endTime) {
                // This is a confirmation, not a new proposal
                if (($scheduledBy === 'vendor' && $request->scheduling_status === 'pending_vendor_confirmation') ||
                    ($scheduledBy === 'tenant' && $request->scheduling_status === 'pending_tenant_confirmation')) {
                    $schedulingStatus = 'confirmed';
                }
            }
        }

        $request->update([
            'scheduled_date' => $date,
            'scheduled_start_time' => $startTime,
            'scheduled_end_time' => $endTime,
            'scheduling_status' => $schedulingStatus,
            'appointment_confirmed_at' => $schedulingStatus === 'confirmed' ? now() : null,
        ]);

        // Build response message
        $message = match($schedulingStatus) {
            'confirmed' => 'Appointment confirmed!',
            'pending_vendor_confirmation' => 'Appointment proposed. Awaiting vendor confirmation.',
            'pending_tenant_confirmation' => 'Appointment proposed. Awaiting tenant confirmation.',
            default => 'Appointment scheduled.',
        };

        return [
            'success' => true,
            'message' => $message,
            'scheduling_status' => $schedulingStatus,
        ];
    }

    /**
     * Vendor confirms a proposed appointment
     */
    public function confirmAppointment(MaintenanceRequest $request): array
    {
        if (!$request->scheduled_date || !$request->scheduled_start_time) {
            return ['success' => false, 'message' => 'No appointment to confirm.'];
        }

        $request->update([
            'scheduling_status' => 'confirmed',
            'appointment_confirmed_at' => now(),
        ]);

        return ['success' => true, 'message' => 'Appointment confirmed.'];
    }

    /**
     * Clear/cancel scheduled appointment
     */
    public function clearAppointment(MaintenanceRequest $request): array
    {
        $request->update([
            'scheduled_date' => null,
            'scheduled_start_time' => null,
            'scheduled_end_time' => null,
            'scheduling_status' => null,
            'appointment_confirmed_at' => null,
        ]);

        return ['success' => true, 'message' => 'Appointment cleared.'];
    }

    /**
     * Get vendor's weekly availability summary for display
     */
    public function getVendorAvailabilitySummary(Vendor $vendor): array
    {
        $schedule = $vendor->availability_schedule ?? [];
        $weekly = $schedule['weekly'] ?? [];

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $summary = [];

        foreach ($days as $day) {
            $daySchedule = $weekly[$day] ?? ['available' => false];
            $summary[$day] = [
                'available' => $daySchedule['available'] ?? false,
                'start' => $daySchedule['start'] ?? null,
                'end' => $daySchedule['end'] ?? null,
                'display' => ($daySchedule['available'] ?? false)
                    ? date('g:i A', strtotime($daySchedule['start'] ?? '08:00')) . ' - ' . date('g:i A', strtotime($daySchedule['end'] ?? '17:00'))
                    : 'Unavailable',
            ];
        }

        return $summary;
    }

    /**
     * Check if vendor has availability set up
     */
    public function vendorHasAvailability(Vendor $vendor): bool
    {
        $schedule = $vendor->availability_schedule ?? [];
        $weekly = $schedule['weekly'] ?? [];

        foreach ($weekly as $day => $config) {
            if ($config['available'] ?? false) {
                return true;
            }
        }

        return false;
    }
}
