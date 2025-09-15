<?php
namespace App\Livewire\Properties;

use App\Models\Property;
use Livewire\Component;

class PropertyShow extends Component
{
    public $property;
    public $showUnitDetails = [];

    public function mount($property = null)
    {
        if ($property) {
            // Property passed from controller
            $this->property = $property;
            
            // Load units with basic ordering
            $this->property->load([
                'units' => function ($query) {
                    $query->orderBy('unit_number');
                }
            ]);
        } else {
            abort(404, 'Property not found');
        }
    }

    public function toggleUnitDetails($unitId)
    {
        $this->showUnitDetails[$unitId] = !($this->showUnitDetails[$unitId] ?? false);
    }

    public function render()
    {
        // Get occupancy summary
        $occupancySummary = $this->property->occupancy_summary;
        
        // Get current tenants using the simplified method
        $currentTenants = $this->property->currentTenants(); // This now returns a collection directly
        
        // Get units with their lease information
        $unitsWithLeases = $this->getUnitsWithActiveLeases();

        return view('livewire.properties.property-show', compact('occupancySummary', 'currentTenants', 'unitsWithLeases'));
    }

    /**
     * Get units with their active lease information
     */
    private function getUnitsWithActiveLeases()
    {
        $units = $this->property->units;
        
        // Load active leases for each unit
        foreach ($units as $unit) {
            $activeLease = \App\Models\Lease::where('unit_id', $unit->id)
                              ->where('status', 'active')
                              ->with('tenants')
                              ->first();
            
            // Add lease and tenants as properties to the unit
            $unit->activeLease = $activeLease;
            $unit->currentTenants = $activeLease ? $activeLease->tenants : collect();
        }
        
        return $units;
    }
}