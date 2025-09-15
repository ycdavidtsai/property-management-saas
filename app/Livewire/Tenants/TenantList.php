<?php
namespace App\Livewire\Tenants;

use App\Models\User;
use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TenantList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $propertyFilter = '';
    public $leaseStatusFilter = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'propertyFilter' => ['except' => ''],
        'leaseStatusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPropertyFilter()
    {
        $this->resetPage();
    }

    public function updatingLeaseStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortBy = $field;
    }

    public function render()
    {
        $organizationId = Auth::user()->organization_id;

        // Load tenants with their lease and property information
        $tenants = User::with([
            'leases' => function ($query) {
                $query->where('status', 'active')->with(['unit.property']);
            },
            'tenantProfile'
        ])
        ->where('organization_id', $organizationId)
        ->where('role', 'tenant')
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        })
        ->when($this->statusFilter, function ($query) {
            if ($this->statusFilter === 'active') {
                $query->where('is_active', true);
            } elseif ($this->statusFilter === 'inactive') {
                $query->where('is_active', false);
            }
        })
        ->when($this->leaseStatusFilter, function ($query) {
            if ($this->leaseStatusFilter === 'has_lease') {
                $query->whereHas('leases', function ($q) {
                    $q->where('status', 'active');
                });
            } elseif ($this->leaseStatusFilter === 'no_lease') {
                $query->whereDoesntHave('leases', function ($q) {
                    $q->where('status', 'active');
                });
            } elseif (in_array($this->leaseStatusFilter, ['active', 'expiring_soon', 'expired'])) {
                $query->whereHas('leases', function ($q) {
                    $q->where('status', $this->leaseStatusFilter);
                });
            }
        })
        ->when($this->propertyFilter, function ($query) {
            $query->whereHas('leases', function ($q) {
                $q->where('status', 'active')
                  ->whereHas('unit.property', function ($propertyQuery) {
                      $propertyQuery->where('id', $this->propertyFilter);
                  });
            });
        })
        ->orderBy($this->sortBy, $this->sortDirection)
        ->paginate(12);

        // Get properties for filter dropdown
        $properties = Property::where('organization_id', $organizationId)
            ->orderBy('name')
            ->get();

        // Get tenant statistics
        $stats = $this->getTenantStatistics($organizationId);

        return view('livewire.tenants.tenant-list', compact('tenants', 'properties', 'stats'));
    }

    private function getTenantStatistics($organizationId)
    {
        $totalTenants = User::where('organization_id', $organizationId)
            ->where('role', 'tenant')
            ->count();

        $activeTenants = User::where('organization_id', $organizationId)
            ->where('role', 'tenant')
            ->where('is_active', true)
            ->count();

        $tenantsWithLeases = User::where('organization_id', $organizationId)
            ->where('role', 'tenant')
            ->whereHas('leases', function ($q) {
                $q->where('status', 'active');
            })
            ->count();

        $tenantsWithoutLeases = $activeTenants - $tenantsWithLeases;

        return [
            'total' => $totalTenants,
            'active' => $activeTenants,
            'with_leases' => $tenantsWithLeases,
            'without_leases' => $tenantsWithoutLeases,
            'housed_percentage' => $activeTenants > 0 ? round(($tenantsWithLeases / $activeTenants) * 100, 1) : 0
        ];
    }
}