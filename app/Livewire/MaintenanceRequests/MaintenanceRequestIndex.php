<?php

namespace App\Livewire\MaintenanceRequests;

use App\Models\MaintenanceRequest;
use App\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MaintenanceRequestIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';

    protected $queryString = ['search', 'statusFilter', 'priorityFilter'];

    public function render()
    {
        $user = Auth::user();
        $roleService = app(RoleService::class);

        // $query = MaintenanceRequest::where('organization_id', $user->organization_id)
        //     ->with(['property', 'unit', 'tenant', 'assignedVendor']);

        // // Role-based filtering
        // if ($roleService->isTenant($user)) {
        //     $query->where('tenant_id', $user->id);
        // }

        // revise to use single query variable with conditional clauses, for differenrt roles

        $query = MaintenanceRequest::query();

        if ($roleService->isTenant($user)) {
            $query->where('organization_id', $user->organization_id)
                ->where('tenant_id', $user->id);
        } elseif ($roleService->isVendor($user)) {
            $vendor = $user->vendor;
            if ($vendor) {
                $query->where('assigned_vendor_id', $vendor->id);
            } else {
                $query->whereRaw('1 = 0'); // Return nothing if no vendor
            }
        } else {
            $query->where('organization_id', $user->organization_id);
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

        $requests = $query->latest()->paginate(15);

        return view('livewire.maintenance-requests.maintenance-request-index', [
            'requests' => $requests,
            'canCreate' => $roleService->hasPermission($user, 'maintenance.create'),
        ]);
    }
}
