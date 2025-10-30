<?php

namespace App\Livewire\Vendors;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceRequestUpdate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
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
    }

    public function addUpdate()
    {
        $this->validate([
            'message' => 'required|string|max:1000',
            'photos.*' => 'nullable|image|max:5120',
        ]);

        $photoUrls = [];
        if ($this->photos) {
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

        // Update status
        $this->maintenanceRequest->update(['status' => $newStatus]);

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
        if ($this->completionPhotos) {
            foreach ($this->completionPhotos as $photo) {
                $path = $photo->store('maintenance-updates', 'public');
                $photoUrls[] = $path;
            }
        }

        // Update request
        $this->maintenanceRequest->update([
            'status' => 'completed',
            'actual_cost' => $this->actualCost,
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
