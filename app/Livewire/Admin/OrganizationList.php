<?php

namespace App\Livewire\Admin;

use App\Models\Organization;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
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
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStatus(Organization $organization)
    {
        $newStatus = $organization->subscription_status === 'active' ? 'inactive' : 'active';

        $organization->update([
            'subscription_status' => $newStatus
        ]);

        session()->flash('message', "Organization \"{$organization->name}\" has been " . ($newStatus === 'active' ? 'activated' : 'deactivated') . ".");
    }

    public function render()
    {
        $query = Organization::query()
            ->withCount(['users', 'properties']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('users', function ($userQuery) {
                      $userQuery->where('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('subscription_status', $this->statusFilter);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $organizations = $query->paginate(15);

        // Get stats for each organization
        $orgIds = $organizations->pluck('id');

        $unitCounts = Unit::whereHas('property', function ($q) use ($orgIds) {
            $q->whereIn('organization_id', $orgIds);
        })
        ->selectRaw('properties.organization_id, count(*) as count')
        ->join('properties', 'units.property_id', '=', 'properties.id')
        ->groupBy('properties.organization_id')
        ->pluck('count', 'organization_id');

        return view('livewire.admin.organization-list', [
            'organizations' => $organizations,
            'unitCounts' => $unitCounts,
        ]);
    }
}
