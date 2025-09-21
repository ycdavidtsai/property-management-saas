<?php

namespace App\Livewire\MaintenanceRequests;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceRequestUpdate;
use App\Models\Vendor;
use App\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MaintenanceRequestShow extends Component
{
    use WithFileUploads;

    public MaintenanceRequest $maintenanceRequest;
    public $newUpdate = '';
    public $updatePhotos = [];
    public $isInternal = false;

    // Quick actions
    public $showAssignModal = false;
    public $selectedVendorId = '';
    public $showStatusModal = false;
    public $newStatus = '';
    public $statusNotes = '';

    protected $rules = [
        'newUpdate' => 'required|string|min:3',
        'updatePhotos.*' => 'nullable|image|max:5120',
        'selectedVendorId' => 'required|exists:vendors,id',
        'newStatus' => 'required|string',
        'statusNotes' => 'nullable|string',
    ];

    public function mount(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('view', $maintenanceRequest);
        $this->maintenanceRequest = $maintenanceRequest;
    }

    public function addUpdate()
    {
        $this->validate(['newUpdate' => 'required|string|min:3']);

        $user = Auth::user();
        $roleService = app(RoleService::class);

        // Handle photo uploads
        $uploadedPhotos = [];
        foreach ($this->updatePhotos as $photo) {
            $path = $photo->store('maintenance-updates', 'public');
            $uploadedPhotos[] = $path;
        }

        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'user_id' => $user->id,
            'message' => $this->newUpdate,
            'photos' => $uploadedPhotos,
            'type' => 'comment',
            'is_internal' => $this->isInternal && !$roleService->isTenant($user),
        ]);

        $this->reset(['newUpdate', 'updatePhotos', 'isInternal']);
        $this->maintenanceRequest->refresh();
        $this->maintenanceRequest->load('updates.user');

        session()->flash('message', 'Update added successfully.');
    }

    public function assignVendor()
    {
        $this->validate(['selectedVendorId' => 'required|exists:vendors,id']);

        $user = Auth::user();
        $vendor = Vendor::find($this->selectedVendorId);

        $this->maintenanceRequest->update([
            'assigned_vendor_id' => $this->selectedVendorId,
            'assigned_by' => $user->id,
            'assigned_at' => now(),
            'status' => 'assigned',
        ]);

        // Add update
        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'user_id' => $user->id,
            'message' => "Assigned to vendor: {$vendor->name}",
            'type' => 'assignment',
            'metadata' => ['vendor_id' => $vendor->id, 'vendor_name' => $vendor->name],
        ]);

        $this->reset(['showAssignModal', 'selectedVendorId']);
        $this->maintenanceRequest->refresh();

        session()->flash('message', 'Vendor assigned successfully.');
    }

    public function updateStatus()
    {
        $this->validate(['newStatus' => 'required|string']);

        $user = Auth::user();
        $oldStatus = $this->maintenanceRequest->status;

        $updateData = ['status' => $this->newStatus];

        // Set appropriate timestamps
        if ($this->newStatus === 'in_progress' && !$this->maintenanceRequest->started_at) {
            $updateData['started_at'] = now();
        } elseif ($this->newStatus === 'completed' && !$this->maintenanceRequest->completed_at) {
            $updateData['completed_at'] = now();
        } elseif ($this->newStatus === 'closed' && !$this->maintenanceRequest->closed_at) {
            $updateData['closed_at'] = now();
        }

        $this->maintenanceRequest->update($updateData);

        // Add update
        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'user_id' => $user->id,
            'message' => $this->statusNotes ?: "Status changed from {$oldStatus} to {$this->newStatus}",
            'type' => 'status_change',
            'metadata' => ['old_status' => $oldStatus, 'new_status' => $this->newStatus],
        ]);

        $this->reset(['showStatusModal', 'newStatus', 'statusNotes']);
        $this->maintenanceRequest->refresh();

        session()->flash('message', 'Status updated successfully.');
    }

    public function render()
    {
        $user = Auth::user();
        $roleService = app(RoleService::class);

        $vendors = Vendor::where('organization_id', $this->maintenanceRequest->organization_id)
            ->where('is_active', true)
            ->get();

        return view('livewire.maintenance-requests.maintenance-request-show', [
            'canManage' => !$roleService->isTenant($user),
            'vendors' => $vendors,
        ]);
    }
}
