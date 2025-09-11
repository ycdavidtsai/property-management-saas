<?php

namespace App\Livewire\Properties;

use App\Models\Property;
use App\Models\Unit;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PropertyForm extends Component
{
    public $name = '';
    public $address = '';
    public $type = '';
    public $total_units = 1;

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string',
        'type' => 'required|in:single_family,multi_family,apartment,commercial',
        'total_units' => 'required|integer|min:1|max:500', // Add reasonable limit
    ];

    public function save()
    {
        logger('PropertyForm save method called');

        $this->validate();

        DB::transaction(function () {
            // Create the property
            $property = Property::create([
                'organization_id' => session('current_organization_id'),
                'name' => $this->name,
                'address' => $this->address,
                'type' => $this->type,
                'total_units' => $this->total_units,
            ]);

            logger('Property created: ' . $property->id);

            // Auto-create unit records
            for ($i = 1; $i <= $this->total_units; $i++) {
                Unit::create([
                    'property_id' => $property->id,
                    'unit_number' => $this->generateUnitNumber($i),
                    'rent_amount' => 0, // User can set this later
                    'status' => 'vacant',
                    // Leave bedrooms, bathrooms, square_feet as null for now
                ]);
            }

            logger('Created ' . $this->total_units . ' units for property ' . $property->id);
        });

        session()->flash('message', 'Property and ' . $this->total_units . ' units created successfully!');
        return redirect()->route('properties.index');
    }

    /**
     * Generate unit number based on property type and index
     */
    private function generateUnitNumber($index)
    {
        // For single family, just use the house number or "Unit 1"
        if ($this->type === 'single_family') {
            return '1';
        }

        // For apartments/multi-family, use sequential numbers
        if ($this->total_units <= 10) {
            return (string) $index;
        }

        // For larger properties, use apartment-style numbering (101, 102, etc.)
        if ($this->total_units <= 100) {
            return '1' . str_pad($index, 2, '0', STR_PAD_LEFT);
        }

        // For very large properties, just use sequential
        return (string) $index;
    }

    public function render()
    {
        return view('livewire.properties.property-form');
    }
}
