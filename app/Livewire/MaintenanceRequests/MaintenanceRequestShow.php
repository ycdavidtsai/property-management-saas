<?php

namespace App\Livewire\MaintenanceRequests;

use App\Models\MaintenanceRequest;
use App\Models\Vendor;
use App\Models\MaintenanceRequestUpdate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

class MaintenanceRequestShow extends Component
{
    use WithPagination, AuthorizesRequests, WithFileUploads;

    public MaintenanceRequest $request;
    public $activeTab = 'details';

    // Vendor Assignment Modal
    public $showAssignModal = false;
    public $selectedVendorId = null;
    public $assignmentNotes = '';

    // Add Comment/Update - ADD THESE LINES
    public $newComment = '';
    public $newCommentPhotos = [];
    public $isInternalComment = false;

    // Editing update properties
    public $editingUpdateId = null;
    public $editingMessage = '';

    // Toggle for showing internal updates
    public $showInternalUpdates = false;

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

    // ✅ NEW: Get vendor and verify it's in organization's "My Vendors" list
    $orgId = Auth::user()->organization_id;

    $vendor = Vendor::whereHas('organizations', function($q) use ($orgId) {
            $q->where('organization_id', $orgId)
              ->where('organization_vendor.is_active', true);
        })
        ->find($this->selectedVendorId);

    if (!$vendor) {
        session()->flash('error', 'Selected vendor is not available in your vendor list.');
        return;
    }

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

    /**
     * Add a new comment/update
     */
    public function addComment()
    {
        $this->authorize('view', $this->request);

        $this->validate([
            'newComment' => 'required|string|max:2000',
            'newCommentPhotos.*' => 'nullable|image|max:5120', // 5MB max
        ]);

        // Handle photo uploads
        $photoPaths = [];
        if ($this->newCommentPhotos) {
            foreach ($this->newCommentPhotos as $photo) {
                $path = $photo->store('maintenance-updates', 'public');
                $photoPaths[] = $path;
            }
        }

        // Tenants can only create public comments
        $isInternal = Auth::user()->role === 'tenant' ? false : $this->isInternalComment;

        // DEBUG - Remove this after testing
        Log::info('Adding comment', [
            'user_role' => Auth::user()->role,
            'isInternalComment_property' => $this->isInternalComment,
            'final_isInternal' => $isInternal
        ]);

        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->request->id,
            'user_id' => Auth::id(),
            'update_type' => 'comment',
            'message' => $this->newComment,
            'is_internal' => $isInternal,
            'photos' => $photoPaths,
        ]);

        // Reset form
        $this->reset(['newComment', 'newCommentPhotos', 'isInternalComment']);

        session()->flash('message', 'Comment added successfully.');
    }

    /**
     * Start editing an update
     */
    public function editUpdate($updateId)
    {
        $update = MaintenanceRequestUpdate::findOrFail($updateId);

        if (!$update->canBeEditedBy(Auth::user())) {
            session()->flash('error', 'You cannot edit this update.');
            return;
        }

        $this->editingUpdateId = $updateId;
        $this->editingMessage = $update->message;
    }

    /**
     * Cancel editing
     */
    public function cancelEdit()
    {
        $this->reset(['editingUpdateId', 'editingMessage']);
    }

    /**
     * Save edited update
     */
    public function saveEdit()
    {
        $update = MaintenanceRequestUpdate::findOrFail($this->editingUpdateId);

        if (!$update->canBeEditedBy(Auth::user())) {
            session()->flash('error', 'You cannot edit this update.');
            return;
        }

        $this->validate([
            'editingMessage' => 'required|string|max:2000',
        ]);

        $update->update(['message' => $this->editingMessage]);

        $this->reset(['editingUpdateId', 'editingMessage']);
        session()->flash('message', 'Update edited successfully.');
    }

    /**
     * Delete an update
     */
    public function deleteUpdate($updateId)
    {
        $update = MaintenanceRequestUpdate::findOrFail($updateId);

        if (!$update->canBeDeletedBy(Auth::user())) {
            session()->flash('error', 'You cannot delete this update.');
            return;
        }

        // Delete associated photos
        if ($update->photos) {
            foreach ($update->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $update->delete();
        session()->flash('message', 'Update deleted successfully.');
    }

    /**
     * Toggle internal updates visibility
     */
    public function toggleInternalUpdates()
    {
        $this->showInternalUpdates = !$this->showInternalUpdates;
    }

    public function render()
    {
        // Get active vendors for assignment dropdown
        // $activeVendors = Vendor::where('created_by_organization_id', Auth::user()->organization_id)
        //     ->where('is_active', true)
        //     ->orderBy('name')
        //     ->get();

        $orgId = Auth::user()->organization_id;
        $activeVendors = Vendor::whereHas('organizations', function($q) use ($orgId) {
            $q->where('organization_id', $orgId)
            ->where('organization_vendor.is_active', true);  // ✅ CORRECT - use table.column
        })
        ->orderBy('vendor_type', 'desc')
        ->orderBy('name')
        ->get();
        // Get updates/timeline with filtering
        $updatesQuery = $this->request->updates()->with('user');

        // Tenants only see public updates
        if (Auth::user()->role === 'tenant') {
            $updatesQuery->public();
        } else {
            // Managers see all by default, but can toggle
            if (!$this->showInternalUpdates) {
                $updatesQuery->public();
            }
        }

        $updates = $updatesQuery->latest()->paginate(25);

        // Check if user can add internal notes
        $canAddInternalNotes = in_array(Auth::user()->role, ['admin', 'manager', 'landlord']);

        return view('livewire.maintenance-requests.maintenance-request-show', [
            'activeVendors' => $activeVendors,
            'updates' => $updates,
            'canAddInternalNotes' => $canAddInternalNotes,  // ← Make sure this line is here
        ]);
    }
}
