<?php

namespace App\Livewire\MaintenanceRequests;

use App\Models\MaintenanceRequest;
use App\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class MaintenanceRequestIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';

    public $propertyFilter = '';

    public $property_id;


    protected $queryString = ['search', 'statusFilter', 'priorityFilter', 'propertyFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatingPropertyFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $roleService = app(RoleService::class);

        $query = MaintenanceRequest::where('organization_id', $user->organization_id)
            ->with(['property', 'unit', 'tenant', 'assignedVendor']);

        // Role-based filtering
        if ($roleService->isTenant($user)) {
            $query->where('tenant_id', $user->id);

            // Get tenant's current active lease info to filter properties
            $activeLease = DB::table('lease_tenant')
                ->join('leases', 'lease_tenant.lease_id', '=', 'leases.id')
                ->join('units', 'leases.unit_id', '=', 'units.id')
                ->where('lease_tenant.tenant_id', $user->id)
                ->where('leases.status', 'active')
                ->select('units.property_id', 'units.id as unit_id')
                ->first();

            if ($activeLease) {
                //from active lease to get property id
                $properties = \App\Models\Property::where('id', $activeLease->property_id)->get();
            } else {
                $properties = []; // Tenants don't need property filter options, in case they have no active lease
            }

        } else {
            // Fetch all properties for the user's organization
            $properties = \App\Models\Property::where('organization_id', $user->organization_id)->get();
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('property', function ($prop) {
                      $prop->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filters
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        if ($this->propertyFilter) {
            $query->where('property_id', $this->propertyFilter);
        }

        $requests = $query->latest()->paginate(15);

        return view('livewire.maintenance-requests.maintenance-request-index', [
            'requests' => $requests,
            'properties' => $properties, // Pass to view
            'canCreate' => $roleService->isTenant($user) || $roleService->roleHasPermission($user->role, 'maintenance.create'),
        ]);
    }
}
