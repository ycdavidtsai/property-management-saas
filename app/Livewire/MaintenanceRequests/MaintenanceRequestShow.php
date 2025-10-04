<?php

namespace App\Livewire\MaintenanceRequests;

use App\Models\MaintenanceRequest;
use App\Models\Vendor;
use App\Models\MaintenanceRequestUpdate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MaintenanceRequestShow extends Component
{
    use WithPagination, AuthorizesRequests;

    public MaintenanceRequest $request;
    public $activeTab = 'details';

    // Vendor Assignment Modal
    public $showAssignModal = false;
    public $selectedVendorId = null;
    public $assignmentNotes = '';

    protected $queryString = [
        'activeTab' => ['except' => 'details'],
    ];

    public function mount(MaintenanceRequest $request)
    {
        $this->authorize('view', $request);
        $this->request = $request;

        // Pre-select current vendor if assigned
        if ($request->assigned_vendor_id) {
            $this->selectedVendorId = $request->assigned_vendor_id;
            $this->assignmentNotes = $request->assignment_notes ?? '';
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    /**
     * Open the vendor assignment modal
     */
    public function openAssignModal()
    {
        $this->authorize('update', $this->request);

        // Reset or pre-populate
        if (!$this->request->assigned_vendor_id) {
            $this->selectedVendorId = null;
            $this->assignmentNotes = '';
        }

        $this->showAssignModal = true;
    }

    /**
     * Close the vendor assignment modal
     */
    public function closeAssignModal()
    {
        $this->showAssignModal = false;
        $this->resetValidation();
    }

    /**
     * Assign vendor to the maintenance request
     */
    public function assignVendor()
    {
        $this->authorize('update', $this->request);

        $this->validate([
            'selectedVendorId' => 'required|exists:vendors,id',
            'assignmentNotes' => 'nullable|string|max:1000',
        ]);

        // Get the vendor
        $vendor = Vendor::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($this->selectedVendorId);

        $previousVendor = $this->request->vendor;
        $isReassignment = $this->request->assigned_vendor_id !== null;
        $isNewAssignment = $this->request->assigned_vendor_id === null;

        // Update the maintenance request
        $this->request->update([
            'assigned_vendor_id' => $vendor->id,
            'assigned_at' => now(),
            'assigned_by' => Auth::id(),
            'assignment_notes' => $this->assignmentNotes,
            'status' => $isNewAssignment && $this->request->status === 'submitted'
                ? 'assigned'
                : $this->request->status,
        ]);

        // Create update record for timeline
        $updateMessage = $isReassignment
            ? "Vendor reassigned from {$previousVendor->name} to {$vendor->name}"
            : "Vendor {$vendor->name} assigned to this request";

        if ($this->assignmentNotes) {
            $updateMessage .= "\n\nAssignment Notes: {$this->assignmentNotes}";
        }

        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->request->id,
            'user_id' => Auth::id(),
            'update_type' => 'assignment',
            'message' => $updateMessage,
            'is_internal' => false,
        ]);

        // If status changed, create status change update
        if ($isNewAssignment && $this->request->status === 'assigned') {
            MaintenanceRequestUpdate::create([
                'maintenance_request_id' => $this->request->id,
                'user_id' => Auth::id(),
                'update_type' => 'status_change',
                'message' => "Status changed from submitted to assigned",
                'is_internal' => false,
            ]);
        }

        // Refresh the request to get updated relationships
        $this->request->refresh();

        // Close modal and show success
        $this->showAssignModal = false;
        $this->assignmentNotes = '';

        session()->flash('message', "Vendor successfully assigned to this maintenance request.");
    }

    /**
     * Remove vendor assignment
     */
    public function unassignVendor()
    {
        $this->authorize('update', $this->request);

        if (!$this->request->assigned_vendor_id) {
            return;
        }

        $vendorName = $this->request->vendor->name;

        // Update the request
        $this->request->update([
            'assigned_vendor_id' => null,
            'assigned_at' => null,
            'assigned_by' => null,
            'assignment_notes' => null,
            'status' => $this->request->status === 'assigned' ? 'submitted' : $this->request->status,
        ]);

        // Create update record
        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->request->id,
            'user_id' => Auth::id(),
            'update_type' => 'assignment',
            'message' => "Vendor {$vendorName} unassigned from this request",
            'is_internal' => false,
        ]);

        // If status changed back to submitted
        if ($this->request->status === 'submitted') {
            MaintenanceRequestUpdate::create([
                'maintenance_request_id' => $this->request->id,
                'user_id' => Auth::id(),
                'update_type' => 'status_change',
                'message' => "Status changed from assigned to submitted",
                'is_internal' => false,
            ]);
        }

        $this->request->refresh();

        session()->flash('message', "Vendor unassigned from this maintenance request.");
    }

    public function render()
    {
        // Get active vendors for assignment dropdown
        $activeVendors = Vendor::where('organization_id', Auth::user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get updates/timeline
        $updates = $this->request->updates()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.maintenance-requests.maintenance-request-show', [
            'activeVendors' => $activeVendors,
            'updates' => $updates,
        ]);
    }
}
