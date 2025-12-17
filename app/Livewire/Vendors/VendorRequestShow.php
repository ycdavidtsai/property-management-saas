<?php

namespace App\Livewire\Vendors;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceRequestUpdate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class VendorRequestShow extends Component
{
    use WithFileUploads, AuthorizesRequests;

    public MaintenanceRequest $maintenanceRequest;
    public $message = '';
    public $photos = [];
    public $actualCost = null;
    public $showCompleteModal = false;
    public $completionNotes = '';
    public $completionPhotos = [];

    // Acceptance/Rejection properties
    public $showRejectModal = false;
    public $rejectionReason = '';
    public $rejectionNotes = '';

    /**
 * Add these to your VendorRequestShow.php Livewire component
 * File: app/Livewire/Vendors/VendorRequestShow.php
 *
 * This enables vendors to schedule appointments from their portal.
 */

// ADD this property to listen for scheduler events:
protected $listeners = [
    'appointment-scheduled' => 'refreshRequest',
    'appointment-confirmed' => 'refreshRequest',
    'appointment-cleared' => 'refreshRequest',
];

// ADD this method:
/**
 * Refresh the maintenance request after scheduling changes
 */
public function refreshRequest()
{
    $this->maintenanceRequest->refresh();
}

    protected $rules = [
        'message' => 'required|string|max:1000',
        'photos.*' => 'nullable|image|max:5120', // 5MB max
        'actualCost' => 'nullable|numeric|min:0|max:999999.99',
        'completionNotes' => 'required_with:showCompleteModal|string|max:1000',
        'completionPhotos.*' => 'nullable|image|max:5120',
    ];

    public function mount(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('viewAsVendor', $maintenanceRequest);
        $this->maintenanceRequest = $maintenanceRequest;

        // Initialize arrays to prevent null errors
        $this->photos = [];
        $this->completionPhotos = [];
    }

    /**
     * Vendor accepts the assignment
     * NOTE: Notifications are handled by MaintenanceRequestObserver when status changes
     */
    public function acceptAssignment()
    {
        // Verify authorization
        $this->authorize('updateStatus', $this->maintenanceRequest);

        // Verify status is pending_acceptance
        if ($this->maintenanceRequest->status !== 'pending_acceptance') {
            session()->flash('error', 'This request is no longer pending acceptance.');
            return;
        }

        // Update status to assigned
        // This triggers MaintenanceRequestObserver which sends notifications to:
        // - Tenant (email + SMS with vendor info)
        // - Landlord/Manager (email + SMS)
        $this->maintenanceRequest->update([
            'status' => 'assigned',
            'accepted_at' => now(),
        ]);

        // Create timeline entry (public - visible to everyone)
        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'user_id' => Auth::id(),
            'update_type' => 'status_change',
            'message' => Auth::user()->name . ' accepted the assignment',
            'is_internal' => false,
        ]);

        session()->flash('success', 'Assignment accepted! You can now start work when ready.');
        $this->maintenanceRequest->refresh();
    }

    /**
     * Confirm rejection with reason
     * NOTE: Rejection notifications are sent directly here since there's no Observer event for rejection
     */
    public function confirmRejection()
    {
        $this->validate([
            'rejectionReason' => 'required|string',
            'rejectionNotes' => 'nullable|string|max:500',
        ], [
            'rejectionReason.required' => 'Please select a reason for rejection.',
        ]);

        // Verify authorization
        $this->authorize('updateStatus', $this->maintenanceRequest);

        // Verify status
        if ($this->maintenanceRequest->status !== 'pending_acceptance') {
            session()->flash('error', 'This request is no longer pending acceptance.');
            return;
        }

        $vendorName = Auth::user()->name;

        // Store rejection info and revert to submitted status
        $this->maintenanceRequest->update([
            'status' => 'submitted', // Back to unassigned
            'assigned_vendor_id' => null,
            'assigned_at' => null,
            'rejection_reason' => $this->rejectionReason,
            'rejection_notes' => $this->rejectionNotes,
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
        ]);

        // Build rejection description
        $reasonMap = [
            'too_busy' => 'Currently too busy / Fully booked',
            'out_of_area' => 'Property location outside service area',
            'lacks_expertise' => 'Requires specialized expertise',
            'emergency_unavailable' => 'Cannot handle emergency priority at this time',
            'insufficient_info' => 'Insufficient information to assess job',
            'other' => 'Other reason',
        ];

        $reasonText = $reasonMap[$this->rejectionReason] ?? $this->rejectionReason;
        $description = "{$vendorName} rejected the assignment\nReason: {$reasonText}";

        if ($this->rejectionNotes) {
            $description .= "\nNotes: {$this->rejectionNotes}";
        }

        // Create timeline entry (PRIVATE - only visible to managers/landlords)
        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'user_id' => Auth::id(),
            'update_type' => 'status_change',
            'message' => $description,
            'is_internal' => true, // Private note
        ]);

        // Notify property manager/landlord about rejection
        // This is done here because 'submitted' status doesn't trigger Observer notifications
        $managers = $this->maintenanceRequest->organization->users()
            ->whereIn('role', ['admin', 'manager', 'landlord'])
            ->get();

        foreach ($managers as $manager) {
            try {
                app(\App\Services\NotificationService::class)->send(
                    $manager,
                    'Vendor Rejected Assignment',
                    "{$vendorName} declined the maintenance request for {$this->maintenanceRequest->property->name}.\n\n" .
                    "Request: {$this->maintenanceRequest->title}\n" .
                    "Reason: {$reasonText}" .
                    ($this->rejectionNotes ? "\nNotes: {$this->rejectionNotes}" : ""),
                    ['email', 'sms'],
                    'maintenance',
                    $this->maintenanceRequest
                );
            } catch (\Exception $e) {
                Log::error('Failed to send rejection notification to manager', [
                    'error' => $e->getMessage(),
                    'manager_id' => $manager->id,
                ]);
            }
        }

        session()->flash('success', 'Assignment rejected. The property manager has been notified and will assign another vendor.');

        // Redirect back to vendor dashboard
        return redirect()->route('vendor.dashboard');
    }

    public function addUpdate()
    {
        $this->validate([
            'message' => 'required|string|max:1000',
            'photos.*' => 'nullable|image|max:5120',
        ]);

        $photoUrls = [];
        if ($this->photos && is_array($this->photos)) {
            foreach ($this->photos as $photo) {
                $path = $photo->store('maintenance-updates', 'public');
                $photoUrls[] = $path;
            }
        }

        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'user_id' => Auth::id(),
            'update_type' => 'comment',
            'message' => $this->message,
            'is_internal' => false, // Vendor updates are always public
            'photos' => $photoUrls,
        ]);

        $this->reset(['message', 'photos']);
        $this->photos = []; // Re-initialize after reset

        session()->flash('success', 'Update added successfully.');

        // Refresh the request to get updated timeline
        $this->maintenanceRequest->refresh();
    }

    public function updateStatus($newStatus)
    {
        $this->authorize('updateStatus', $this->maintenanceRequest);

        $allowedTransitions = [
            'assigned' => ['in_progress'],
            'in_progress' => ['completed'],
            'completed' => [], // Cannot change from completed
        ];

        $currentStatus = $this->maintenanceRequest->status;

        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            session()->flash('error', 'Invalid status transition.');
            return;
        }

        // If transitioning to completed, show modal for completion details
        if ($newStatus === 'completed') {
            $this->showCompleteModal = true;
            return;
        }

        // Update status - Observer handles notifications
        $this->maintenanceRequest->update(['status' => $newStatus]);

        // For in_progress, set started_at timestamp
        if ($newStatus === 'in_progress') {
            $this->maintenanceRequest->update(['started_at' => now()]);
        }

        // Create timeline entry
        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'user_id' => Auth::id(),
            'update_type' => 'status_change',
            'message' => "Status changed from {$currentStatus} to {$newStatus}",
            'is_internal' => false,
        ]);

        session()->flash('success', 'Status updated successfully.');
        $this->maintenanceRequest->refresh();
    }

    public function completeWork()
    {
        $this->validate([
            'completionNotes' => 'required|string|max:1000',
            'actualCost' => 'nullable|numeric|min:0|max:999999.99',
            'completionPhotos.*' => 'nullable|image|max:5120',
        ]);

        $photoUrls = [];
        if ($this->completionPhotos && is_array($this->completionPhotos)) {
            foreach ($this->completionPhotos as $photo) {
                $path = $photo->store('maintenance-updates', 'public');
                $photoUrls[] = $path;
            }
        }

        // Update status to completed - Observer handles notifications
        $this->maintenanceRequest->update([
            'status' => 'completed',
            'actual_cost' => $this->actualCost,
            'completion_notes' => $this->completionNotes,
            'completed_at' => now(),
        ]);

        // Create completion timeline entry
        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'user_id' => Auth::id(),
            'update_type' => 'status_change',
            'message' => "Work completed. " . $this->completionNotes,
            'is_internal' => false,
            'photos' => $photoUrls,
        ]);

        $this->reset(['showCompleteModal', 'completionNotes', 'actualCost', 'completionPhotos']);
        $this->completionPhotos = []; // Re-initialize after reset

        session()->flash('success', 'Work marked as completed.');
        $this->maintenanceRequest->refresh();
    }

    public function render()
    {
        // Load public updates only (not internal notes)
        $updates = $this->maintenanceRequest->updates()
            ->with('user')
            ->where('is_internal', false)
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('livewire.vendors.vendor-request-show', [
            'updates' => $updates,
        ]);
    }
}
