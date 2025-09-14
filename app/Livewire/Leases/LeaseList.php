<?php
namespace App\Livewire\Leases;

use App\Models\Lease;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class LeaseList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortBy = 'start_date';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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

        // Only for admin, manager, landlord users
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'landlord'])) {
            abort(403, 'Access denied. This portal is for authorized users only.');
        }

        $leases = Lease::with(['unit.property', 'tenants'])
            ->forOrganization(Auth::user()->organization_id)
            ->when($this->search, function ($query) {
                $query->whereHas('unit.property', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('unit', function ($q) {
                    $q->where('unit_number', 'like', '%' . $this->search . '%');
                })->orWhereHas('tenants', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.leases.lease-list', compact('leases'));
    }
}
