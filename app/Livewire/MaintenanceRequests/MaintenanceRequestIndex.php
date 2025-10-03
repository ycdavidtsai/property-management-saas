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

        $query = MaintenanceRequest::where('organization_id', $user->organization_id)
            ->with(['property', 'unit', 'tenant', 'assignedVendor']);

        // Role-based filtering
        if ($roleService->isTenant($user)) {
            $query->where('tenant_id', $user->id);
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
