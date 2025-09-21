<?php

namespace App\Livewire\MaintenanceRequests;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Vendor;
use App\Services\RoleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class MaintenanceRequestForm extends Component
{
    use WithFileUploads, AuthorizesRequests;

    public ?MaintenanceRequest $maintenanceRequest = null;
    public $isEditing = false;

    // Form fields
    public $property_id = '';
    public $unit_id = '';
    public $title = '';
    public $description = '';
    public $priority = 'normal';
    public $category = '';
    public $preferred_date = '';
    public $photos = [];
    public $newPhotos = [];

    // For management users
    public $status = 'submitted';
    public $assigned_vendor_id = '';
    public $estimated_cost = '';
    public $completion_notes = '';

    protected function rules()
    {
        $user = Auth::user();
        $roleService = app(RoleService::class);

        $rules = [
            'property_id' => 'required|exists:properties,id',
            'unit_id' => 'nullable|exists:units,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:emergency,high,normal,low',
            'category' => 'nullable|string|max:100',
            'preferred_date' => 'nullable|date|after:today',
            'newPhotos.*' => 'nullable|image|max:5120', // 5MB max
        ];

        // Additional rules for management users
        if (!$roleService->isTenant($user) && $this->isEditing) {
            $rules['status'] = 'required|in:submitted,assigned,in_progress,completed,closed';
            $rules['assigned_vendor_id'] = 'nullable|exists:vendors,id';
            $rules['estimated_cost'] = 'nullable|numeric|min:0';
            $rules['completion_notes'] = 'nullable|string';
        }

        return $rules;
    }

    public function mount(?MaintenanceRequest $maintenanceRequest = null)
    {
        if ($maintenanceRequest && $maintenanceRequest->exists) {
            $this->authorize('update', $maintenanceRequest);
            $this->maintenanceRequest = $maintenanceRequest;
            $this->isEditing = true;
            $this->fill([
                'property_id' => $maintenanceRequest->property_id,
                'unit_id' => $maintenanceRequest->unit_id,
                'title' => $maintenanceRequest->title,
                'description' => $maintenanceRequest->description,
                'priority' => $maintenanceRequest->priority,
                'category' => $maintenanceRequest->category,
                'preferred_date' => $maintenanceRequest->preferred_date?->format('Y-m-d'),
                'photos' => $maintenanceRequest->photos ?? [],
                'status' => $maintenanceRequest->status,
                'assigned_vendor_id' => $maintenanceRequest->assigned_vendor_id,
                'estimated_cost' => $maintenanceRequest->estimated_cost,
                'completion_notes' => $maintenanceRequest->completion_notes,
            ]);
        } else {
            // Check if user can create maintenance requests
            $this->authorize('create', MaintenanceRequest::class);
        }
    }

    public function updatedPropertyId()
    {
        $this->unit_id = '';
    }

    public function removePhoto($index)
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos);
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        $roleService = app(RoleService::class);

        // Handle photo uploads
        $uploadedPhotos = [];
        foreach ($this->newPhotos as $photo) {
            $path = $photo->store('maintenance-photos', 'public');
            $uploadedPhotos[] = $path;
        }

        $allPhotos = array_merge($this->photos, $uploadedPhotos);

        $data = [
            'organization_id' => $user->organization_id,
            'property_id' => $this->property_id,
            'unit_id' => $this->unit_id ?: null,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'category' => $this->category,
            'preferred_date' => $this->preferred_date ? now()->parse($this->preferred_date) : null,
            'photos' => $allPhotos,
        ];

        if ($this->isEditing) {
            // Management users can update additional fields
            if (!$roleService->isTenant($user)) {
                $data['status'] = $this->status;
                $data['assigned_vendor_id'] = $this->assigned_vendor_id ?: null;
                $data['estimated_cost'] = $this->estimated_cost ?: null;
                $data['completion_notes'] = $this->completion_notes;

                // Track status changes
                if ($this->maintenanceRequest->status !== $this->status) {
                    $data['assigned_by'] = $user->id;

                    if ($this->status === 'assigned') {
                        $data['assigned_at'] = now();
                    } elseif ($this->status === 'in_progress') {
                        $data['started_at'] = now();
                    } elseif ($this->status === 'completed') {
                        $data['completed_at'] = now();
                    } elseif ($this->status === 'closed') {
                        $data['closed_at'] = now();
                    }
                }
            }

            $this->maintenanceRequest->update($data);
            session()->flash('message', 'Maintenance request updated successfully.');
        } else {
            $data['tenant_id'] = $user->id;
            MaintenanceRequest::create($data);
            session()->flash('message', 'Maintenance request submitted successfully.');
        }

        return redirect()->route('maintenance-requests.index');
    }

    public function render()
    {
        $user = Auth::user();
        $roleService = app(RoleService::class);

        // Get properties based on user role
        if ($roleService->isTenant($user)) {
            // Get properties where this tenant has active leases
            // Step 1: Get all active lease unit IDs for this tenant
            $activeLeasesForTenant = DB::table('lease_tenant')
                ->join('leases', 'lease_tenant.lease_id', '=', 'leases.id')
                ->where('lease_tenant.tenant_id', $user->id)
                ->where('leases.status', 'active')
                ->pluck('leases.unit_id'); // Get the unit IDs from active leases

            // Step 2: Get properties that have those units
            $properties = Property::where('organization_id', $user->organization_id)
                ->whereHas('units', function ($query) use ($activeLeasesForTenant) {
                    $query->whereIn('id', $activeLeasesForTenant);
                })
                ->with(['units' => function ($query) use ($activeLeasesForTenant) {
                    // Only load units where the tenant has active leases
                    $query->whereIn('id', $activeLeasesForTenant);
                }])
                ->get();
        } else {
            $properties = Property::where('organization_id', $user->organization_id)
                ->with('units')
                ->get();
        }

        // Get units for selected property
        $units = collect();
        if ($this->property_id) {
            $units = Unit::where('property_id', $this->property_id)->get();
        }

        // Get vendors for management users
        $vendors = collect();
        if (!$roleService->isTenant($user)) {
            $vendors = Vendor::where('organization_id', $user->organization_id)
                ->where('is_active', true)
                ->get();
        }

        return view('livewire.maintenance-requests.maintenance-request-form', [
            'properties' => $properties,
            'units' => $units,
            'vendors' => $vendors,
            'canManage' => !$roleService->isTenant($user),
        ]);
    }
}
