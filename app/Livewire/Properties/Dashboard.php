<?php

namespace App\Livewire\Properties;

use App\Models\Property;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $properties;
    public $metrics = [];
    public $selectedProperty = null;

    public function mount()
    {
        // Only for admin, manager, landlord users
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'landlord'])) {
            abort(403, 'Access denied. This portal is for authorized users only.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $orgId = session('current_organization_id');

        $this->properties = Property::where('organization_id', $orgId)
            ->withCount(['units', 'occupiedUnits', 'vacantUnits'])
            ->withSum('units', 'rent_amount')
            ->get();

        $this->calculateMetrics();
    }

    public function selectProperty($propertyId)
    {
        $this->selectedProperty = $this->properties->find($propertyId);
        $this->dispatch('property-selected', property: $this->selectedProperty);
    }

    protected function calculateMetrics()
    {
        $totalUnits = $this->properties->sum('units_count');
        $occupiedUnits = $this->properties->sum('occupied_units_count');
        $vacantUnits = $this->properties->sum('vacant_units_count');

        $this->metrics = [
            'totalUnits' => $totalUnits,
            'occupiedUnits' => $occupiedUnits,
            'vacantUnits' => $vacantUnits,
            'monthlyRevenue' => $this->properties->sum('units_sum_rent_amount') ?? 0,
            'occupancyRate' => $totalUnits > 0
                ? round(($occupiedUnits / $totalUnits) * 100, 1)
                : 0
        ];
    }

    public function render()
    {
        return view('livewire.properties.dashboard');
    }
}
