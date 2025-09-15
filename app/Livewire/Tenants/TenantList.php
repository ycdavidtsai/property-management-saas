<?php
namespace App\Livewire\Tenants;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TenantList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $propertyFilter = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'propertyFilter' => ['except' => ''],
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
        $tenants = User::with(['leases.unit.property', 'tenantProfile'])
            ->where('organization_id', Auth::user()->organization_id)
            ->where('role', 'tenant')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'active_lease') {
                    $query->whereHas('leases', function ($q) {
                        $q->where('status', 'active');
                    });
                } elseif ($this->statusFilter === 'no_lease') {
                    $query->whereDoesntHave('leases', function ($q) {
                        $q->where('status', 'active');
                    });
                } else {
                    $query->where('is_active', $this->statusFilter === 'active');
                }
            })
            ->when($this->propertyFilter, function ($query) {
                $query->whereHas('leases.unit.property', function ($q) {
                    $q->where('id', $this->propertyFilter)
                      ->where('leases.status', 'active');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        // Get properties for filter dropdown
        $properties = \App\Models\Property::where('organization_id', auth()->user()->organization_id)
            ->orderBy('name')
            ->get();

        return view('livewire.tenants.tenant-list', compact('tenants', 'properties'));
    }
}