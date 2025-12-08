<?php

namespace App\Observers;

use App\Models\MaintenanceRequest;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class MaintenanceRequestObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the MaintenanceRequest "created" event.
     */
    public function created(MaintenanceRequest $maintenanceRequest): void
    {
        // Send "created" notification to tenant
        try {
            $this->notificationService->sendMaintenanceNotification(
                $maintenanceRequest,
                'created'
            );

            Log::info('Maintenance request created notification sent', [
                'maintenance_request_id' => $maintenanceRequest->id,
                'tenant_id' => $maintenanceRequest->tenant_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send maintenance request created notification', [
                'maintenance_request_id' => $maintenanceRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the MaintenanceRequest "updated" event.
     */
    public function updated(MaintenanceRequest $maintenanceRequest): void
    {
        // Only trigger notification if status changed
        if (!$maintenanceRequest->wasChanged('status')) {
            return;
        }

        // Map status to notification event
        // pending_acceptance = landlord assigned vendor, notify vendor
        // assigned = vendor accepted, notify tenant
        // in_progress = work started, notify tenant
        // completed = work done, notify tenant + landlord
        $statusEvents = [
            'pending_acceptance' => 'pending_acceptance',  // NEW: Notify vendor
            'assigned' => 'assigned',                      // Notify tenant (vendor accepted)
            'in_progress' => 'in_progress',
            'completed' => 'completed',
        ];

        $newStatus = $maintenanceRequest->status;

        // Check if this status change should trigger a notification
        if (!isset($statusEvents[$newStatus])) {
            return;
        }

        $event = $statusEvents[$newStatus];

        try {
            $this->notificationService->sendMaintenanceNotification(
                $maintenanceRequest,
                $event
            );

            Log::info('Maintenance request status change notification sent', [
                'maintenance_request_id' => $maintenanceRequest->id,
                'old_status' => $maintenanceRequest->getOriginal('status'),
                'new_status' => $newStatus,
                'event' => $event,
                'tenant_id' => $maintenanceRequest->tenant_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send maintenance request status change notification', [
                'maintenance_request_id' => $maintenanceRequest->id,
                'status' => $newStatus,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }
}