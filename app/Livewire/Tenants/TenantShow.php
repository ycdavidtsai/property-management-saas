<?php

namespace App\Livewire\Tenants;

use App\Models\User;
use Livewire\Component;

class TenantShow extends Component
{
    public User $tenant;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public function mount(User $tenant)
    {
        // Ensure tenant belongs to current organization
        if ($tenant->organization_id !== session('current_organization_id')) {
            abort(404);
        }

        if ($tenant->role !== 'tenant') {
            abort(404);
        }

        $this->tenant = $tenant->load('tenantProfile');
    }

    public function toggleActiveStatus()
    {
        $this->tenant->update([
            'is_active' => !$this->tenant->is_active
        ]);

        $status = $this->tenant->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Tenant account has been {$status}.");
    }

    public function confirmDelete()
    {
        $this->showDeleteModal = true;
    }

    public function deleteTenant()
    {
        $tenantName = $this->tenant->name;

        // Delete tenant profile first (due to foreign key)
        $this->tenant->tenantProfile()?->delete();

        // Delete tenant
        $this->tenant->delete();

        session()->flash('message', "Tenant '{$tenantName}' has been deleted.");

        return redirect()->route('tenants.index');
    }

    public function render()
    {
        return view('livewire.tenants.tenant-show');
    }
}
