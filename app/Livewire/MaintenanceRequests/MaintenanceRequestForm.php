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

        // Additional validation for tenants - ensure they can only create requests for their units
        if ($roleService->isTenant($user)) {
            $tenantPropertyIds = $this->getTenantPropertyIds($user);
            $tenantUnitIds = $this->getTenantUnitIds($user);

            if ($tenantPropertyIds) {
                $rules['property_id'] .= '|in:' . $tenantPropertyIds;
            }

            if ($this->unit_id && $tenantUnitIds) {
                $rules['unit_id'] = 'nullable|exists:units,id|in:' . $tenantUnitIds;
            }
        }

        // Additional rules for management users
        if (!$roleService->isTenant($user) && $this->isEditing) {
            $rules['status'] = 'required|in:submitted,assigned,in_progress,completed,closed';
            $rules['assigned_vendor_id'] = 'nullable|exists:vendors,id';
            $rules['estimated_cost'] = 'nullable|numeric|min:0';
            $rules['completion_notes'] = 'nullable|string';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'property_id.required' => 'Please select a property.',
            'property_id.exists' => 'The selected property is invalid.',
            'title.required' => 'Please enter a title for your maintenance request.',
            'title.max' => 'The title must not exceed 255 characters.',
            'description.required' => 'Please describe the maintenance issue in detail.',
            'priority.required' => 'Please select a priority level.',
            'preferred_date.after' => 'The preferred date must be in the future.',
            'newPhotos.*.image' => 'All uploaded files must be images (JPG, PNG, GIF, etc.)',
            'newPhotos.*.max' => 'Each image must not exceed 5MB. Please compress or resize your images.',
        ];
    }

    private function getTenantPropertyIds($user)
    {
        return DB::table('lease_tenant')
            ->join('leases', 'lease_tenant.lease_id', '=', 'leases.id')
            ->join('units', 'leases.unit_id', '=', 'units.id')
            ->where('lease_tenant.tenant_id', $user->id)
            ->where('leases.status', 'active')
            ->pluck('units.property_id')
            ->implode(',');
    }

    private function getTenantUnitIds($user)
    {
        return DB::table('lease_tenant')
            ->join('leases', 'lease_tenant.lease_id', '=', 'leases.id')
            ->where('lease_tenant.tenant_id', $user->id)
            ->where('leases.status', 'active')
            ->pluck('leases.unit_id')
            ->implode(',');
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

            // Auto-populate for tenants
            $user = Auth::user();
            $roleService = app(RoleService::class);

            if ($roleService->isTenant($user)) {
                // Get tenant's current active lease info
                $activeLease = DB::table('lease_tenant')
                    ->join('leases', 'lease_tenant.lease_id', '=', 'leases.id')
                    ->join('units', 'leases.unit_id', '=', 'units.id')
                    ->where('lease_tenant.tenant_id', $user->id)
                    ->where('leases.status', 'active')
                    ->select('units.property_id', 'units.id as unit_id')
                    ->first();

                if ($activeLease) {
                    $this->property_id = $activeLease->property_id;
                    $this->unit_id = $activeLease->unit_id;
                }
            }
        }
    }

    public function updatedPropertyId()
    {
        // Only reset unit_id for management users who can change properties
        $user = Auth::user();
        $roleService = app(RoleService::class);

        if (!$roleService->isTenant($user)) {
            $this->unit_id = '';
        }
    }

    public function removePhoto($index)
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos);
        }
    }

    public function updatedNewPhotos()
    {
        // Debug logging for file upload validation
        \Log::info('File Upload Attempt:', [
            'files_count' => count($this->newPhotos),
            'files_details' => collect($this->newPhotos)->map(function($file) {
                return [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'size_mb' => round($file->getSize() / 1024 / 1024, 2),
                    'mime' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                ];
            })->toArray()
        ]);

        try {
            $this->validate([
                'newPhotos.*' => 'nullable|image|max:5120', // 5MB max
            ], [
                'newPhotos.*.image' => 'The file must be an image (JPG, PNG, GIF, etc.)',
                'newPhotos.*.max' => 'Each image must not be larger than 5MB. Please reduce the file size or choose a smaller image.',
            ]);
            \Log::info('File validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('File validation failed:', [
                'error' => $e->getMessage(),
                'validation_errors' => $e->errors()
            ]);

            // Re-throw to show user the error
            throw $e;
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        $roleService = app(RoleService::class);

        // Debug: Check what files we have before processing
        \Log::info('Save method - File debugging:', [
            'newPhotos_count' => count($this->newPhotos),
            'newPhotos_types' => collect($this->newPhotos)->map(fn($photo) => get_class($photo))->toArray(),
            'existing_photos_count' => count($this->photos),
        ]);

        // Handle photo uploads
        $uploadedPhotos = [];
        foreach ($this->newPhotos as $photo) {
            try {
                \Log::info('Processing file:', [
                    'name' => $photo->getClientOriginalName(),
                    'size' => $photo->getSize(),
                    'temp_path' => $photo->getPathname(),
                ]);

                $path = $photo->store('maintenance-photos', 'public');

                \Log::info('File stored successfully:', [
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path),
                    'file_exists' => file_exists(storage_path('app/public/' . $path)),
                ]);

                $uploadedPhotos[] = $path;
            } catch (\Exception $e) {
                \Log::error('File upload failed:', [
                    'error' => $e->getMessage(),
                    'file' => $photo->getClientOriginalName(),
                ]);
            }
        }

        \Log::info('Upload summary:', [
            'uploaded_photos' => $uploadedPhotos,
            'total_uploaded' => count($uploadedPhotos),
        ]);

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

        // Get units for selected property (management users)
        $units = collect();
        if ($this->property_id && !$roleService->isTenant($user)) {
            $units = Unit::where('property_id', $this->property_id)->get();
        }

        // Get selected property and unit for display (tenants)
        $selectedProperty = null;
        $selectedUnit = null;
        if ($this->property_id) {
            $selectedProperty = Property::find($this->property_id);
        }
        if ($this->unit_id) {
            $selectedUnit = Unit::find($this->unit_id);
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
            'selectedProperty' => $selectedProperty,
            'selectedUnit' => $selectedUnit,
            'canManage' => !$roleService->isTenant($user),
        ]);
    }
}
