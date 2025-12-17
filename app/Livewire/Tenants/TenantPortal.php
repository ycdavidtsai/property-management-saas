<?php
namespace App\Livewire\Tenants;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TenantPortal extends Component
{
    public $activeLease;
    public $unit;
    public $property;

    public function mount()
    {
        // Only for tenant users

        if (Auth::user()->role !== 'tenant') {
            abort(403, 'Access denied. This portal is for tenants only.');
        }

        $this->activeLease = Auth::user()->activeLease(); //ignore the complaints for now
        //$this->activeLease = auth()->user()->activeLease(); // Alternative way

        if ($this->activeLease) {
            $this->unit = $this->activeLease->unit;
            $this->property = $this->unit->property;
        }
    }

    /**
 * Add these to your Tenant Portal Livewire component
 * File: app/Livewire/Tenants/TenantPortal.php (or similar)
 *
 * This enables tenants to schedule appointments from their portal.
 */

// ADD this property to listen for scheduler events:
protected $listeners = [
    'appointment-scheduled' => 'refreshData',
    'appointment-confirmed' => 'refreshData',
    'appointment-cleared' => 'refreshData',
];

// ADD this method:
/**
 * Refresh maintenance request data after scheduling changes
 */
public function refreshData()
{
    // Refresh whatever maintenance request data you're displaying
    // This depends on how your TenantPortal is structured

    // Example if you have a single request:
    // $this->maintenanceRequest->refresh();

    // Example if you have a collection:
    // $this->loadMaintenanceRequests();
}

    public function render()
    {
        return view('livewire.tenants.tenant-portal');
    }
}
