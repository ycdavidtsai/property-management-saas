<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use App\Services\RoleService;
use Livewire\Component;

class UnitShow extends Component
{
    public $unit;
    public $showLeaseHistory = false;
    public $activeTab = 'overview';

    public function mount($unit = null)
    {
        if ($unit) {
            $this->unit = $unit;
        } else {
            abort(404, 'Unit not found');
        }
    }

    public function toggleLeaseHistory()
    {
        $this->showLeaseHistory = !$this->showLeaseHistory;
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        // Get current and historical lease data
        $currentLease = $this->unit->activeLease();
        $leaseHistory = $this->unit->leases()
            ->with('tenants')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get unit specifications and details
        $unitSpecs = [
            'bedrooms' => $this->unit->bedrooms,
            'bathrooms' => $this->unit->bathrooms,
            'square_feet' => $this->unit->square_feet,
            'rent_amount' => $this->unit->rent_amount,
        ];

        // Calculate unit metrics
        $metrics = $this->calculateUnitMetrics();

        return view('livewire.units.unit-show', compact(
            'currentLease', 
            'leaseHistory', 
            'unitSpecs', 
            'metrics'
        ));
    }

    private function calculateUnitMetrics()
    {
        $totalLeases = $this->unit->leases()->count();
        $totalDaysOccupied = 0;
        $totalRevenue = 0;

        // Calculate occupancy and revenue metrics
        foreach ($this->unit->leases as $lease) {
            if ($lease->status === 'active') {
                $daysActive = $lease->start_date->diffInDays(now());
                $totalDaysOccupied += $daysActive;
                $totalRevenue += ($daysActive / 30) * $this->unit->rent_amount;
            } elseif ($lease->status === 'terminated' || $lease->status === 'expired') {
                $daysActive = $lease->start_date->diffInDays($lease->end_date);
                $totalDaysOccupied += $daysActive;
                $totalRevenue += ($daysActive / 30) * $this->unit->rent_amount;
            }
        }

        $daysSinceCreated = $this->unit->created_at->diffInDays(now());
        $occupancyRate = $daysSinceCreated > 0 ? round(($totalDaysOccupied / $daysSinceCreated) * 100, 1) : 0;

        return [
            'total_leases' => $totalLeases,
            'occupancy_rate' => $occupancyRate,
            'estimated_revenue' => $totalRevenue,
            'days_occupied' => $totalDaysOccupied,
        ];
    }
}