<?php

namespace App\Livewire\Admin;

use App\Models\Organization;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $organizationFilter = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'organizationFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingOrganizationFilter()
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

    public function toggleStatus(User $user)
    {
        // Prevent toggling own account
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot deactivate your own account.');
            return;
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        session()->flash('message', "User \"{$user->name}\" has been " . ($user->is_active ? 'activated' : 'deactivated') . ".");
    }

    public function render()
    {
        $query = User::query()->with('organization');

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Role filter
        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        // Organization filter
        if ($this->organizationFilter) {
            $query->where('organization_id', $this->organizationFilter);
        }

        // Status filter
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $users = $query->paginate(20);

        // Get organizations for filter dropdown
        $organizations = Organization::orderBy('name')->get(['id', 'name']);

        return view('livewire.admin.user-list', [
            'users' => $users,
            'organizations' => $organizations,
        ]);
    }
}
