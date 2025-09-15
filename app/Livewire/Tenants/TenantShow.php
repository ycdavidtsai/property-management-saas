<?php
namespace App\Livewire\Tenants;

use App\Models\User;
use Livewire\Component;

class TenantShow extends Component
{
    public $tenant;
    public $activeLease;
    public $currentProperty;
    public $currentUnit;
    public $leaseHistory;
    public $coTenants;

    public function mount($tenant = null)
    {
        if ($tenant) {
            // Tenant passed from controller
            $this->tenant = $tenant;
            $this->loadTenantData();
        } else {
            abort(404, 'Tenant not found');
        }
    }

    private function loadTenantData()
    {
        // Load tenant with relationships
        $this->tenant->load(['tenantProfile', 'leases.unit.property', 'leases.tenants']);
        
        // Get active lease
        $this->activeLease = $this->tenant->leases->where('status', 'active')->first();
        
        // Get current property and unit
        if ($this->activeLease) {
            $this->currentProperty = $this->activeLease->unit->property;
            $this->currentUnit = $this->activeLease->unit;
            
            // Get co-tenants (other tenants on the same lease)
            $this->coTenants = $this->activeLease->tenants->where('id', '!=', $this->tenant->id);
        }
        
        // Get lease history (all leases, ordered by date)
        $this->leaseHistory = $this->tenant->leases()
            ->with(['unit.property', 'tenants'])
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.tenants.tenant-show');
    }
}