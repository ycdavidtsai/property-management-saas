<?php
namespace App\Livewire\Dashboard;

use App\Models\Lease;
use App\Models\Unit;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaseMetrics extends Component
{
    public $activeLeases;
    public $expiringSoonLeases;
    public $expiredLeases;
    public $terminatedLeases;
    public $occupancyRate;
    public $totalMonthlyRent;

    public function mount()
    {
        $organizationId = Auth::user()->organization_id;

        // Get lease counts by status
        $this->activeLeases = Lease::forOrganization($organizationId)
            ->where('status', 'active')
            ->count();

        $this->expiringSoonLeases = Lease::forOrganization($organizationId)
            ->where('status', 'expiring_soon')
            ->orWhere(function($q) use ($organizationId) {
                $q->forOrganization($organizationId)
                  ->where('status', 'active')
                  ->where('end_date', '<=', Carbon::now()->addDays(60));
            })
            ->count();

        $this->expiredLeases = Lease::forOrganization($organizationId)
            ->where('status', 'expired')
            ->count();

        $this->terminatedLeases = Lease::forOrganization($organizationId)
            ->where('status', 'terminated')
            ->count();

        // Calculate occupancy rate
        $totalUnits = Unit::whereHas('property', function ($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->count();

        $occupiedUnits = Unit::whereHas('property', function ($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->where('status', 'occupied')->count();

        $this->occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;

        // Calculate total monthly rent from active leases
        $this->totalMonthlyRent = Lease::forOrganization($organizationId)
            ->where('status', 'active')
            ->sum('rent_amount');
    }

    public function render()
    {
        return view('livewire.dashboard.lease-metrics');
    }
}
