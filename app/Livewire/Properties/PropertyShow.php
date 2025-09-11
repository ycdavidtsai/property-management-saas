<?php

namespace App\Livewire\Properties;

use App\Models\Property;
use Livewire\Component;

class PropertyShow extends Component
{
    public Property $property;
    public $units;
    public $metrics = [];

    public function mount(Property $property)
    {
        $this->property = $property;
        $this->loadData();
    }

    public function loadData()
    {
        // Load units with eager loading
        $this->units = $this->property->units()
            ->orderBy('unit_number')
            ->get();

        $this->calculateMetrics();
    }

    protected function calculateMetrics()
    {
        $totalUnits = $this->units->count();
        $occupiedUnits = $this->units->where('status', 'occupied')->count();
        $vacantUnits = $this->units->where('status', 'vacant')->count();
        $maintenanceUnits = $this->units->where('status', 'maintenance')->count();

        $totalRent = $this->units->where('status', 'occupied')->sum('rent_amount');
        $potentialRent = $this->units->sum('rent_amount');

        $this->metrics = [
            'totalUnits' => $totalUnits,
            'occupiedUnits' => $occupiedUnits,
            'vacantUnits' => $vacantUnits,
            'maintenanceUnits' => $maintenanceUnits,
            'currentRevenue' => $totalRent,
            'potentialRevenue' => $potentialRent,
            'occupancyRate' => $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0,
        ];
    }

    public function updateUnitStatus($unitId, $status)
    {
        $unit = $this->units->find($unitId);
        if ($unit) {
            $unit->update(['status' => $status]);
            $this->loadData(); // Refresh data
            session()->flash('message', 'Unit status updated successfully!');
        }
    }

    public function render()
    {
        return view('livewire.properties.property-show');
    }
}
