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
    public $photos = [];              // ← Already good
    public $actualCost = null;
    public $showCompleteModal = false;
    public $completionNotes = '';
    public $completionPhotos = [];    // ← Already good

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

        // ✅ ADD THESE to ensure arrays are initialized
        $this->photos = [];
        $this->completionPhotos = [];
    }

    public function addUpdate()
    {
        $this->validate([
            'message' => 'required|string|max:1000',
            'photos.*' => 'nullable|image|max:5120',
        ]);

        $photoUrls = [];
        if ($this->photos && is_array($this->photos)) {  // ← Added is_array check
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
            'is_internal' => false,
            'photos' => $photoUrls,
        ]);

        $this->reset(['message', 'photos']);

        // ✅ Re-initialize after reset
        $this->photos = [];

        session()->flash('success', 'Update added successfully.');
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
        if ($this->completionPhotos && is_array($this->completionPhotos)) {  // ← Added is_array check
            foreach ($this->completionPhotos as $photo) {
                $path = $photo->store('maintenance-updates', 'public');
                $photoUrls[] = $path;
            }
        }

        $this->maintenanceRequest->update([
            'status' => 'completed',
            'actual_cost' => $this->actualCost,
            'completed_at' => now(),
        ]);

        MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'user_id' => Auth::id(),
            'update_type' => 'status_change',
            'message' => "Work completed. " . $this->completionNotes,
            'is_internal' => false,
            'photos' => $photoUrls,
        ]);

        $this->reset(['showCompleteModal', 'completionNotes', 'actualCost', 'completionPhotos']);

        // ✅ Re-initialize after reset
        $this->completionPhotos = [];

        session()->flash('success', 'Work marked as completed.');
        $this->maintenanceRequest->refresh();
    }

    public function render()
    {
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
