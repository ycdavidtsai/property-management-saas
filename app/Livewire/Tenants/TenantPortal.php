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

    public function render()
    {
        return view('livewire.tenants.tenant-portal');
    }
}
