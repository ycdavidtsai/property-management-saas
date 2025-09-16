<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use App\Services\RoleService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UnitForm extends Component
{
    public $unit;
    public $property;
    
    // Form fields
    public $unit_number;
    public $bedrooms;
    public $bathrooms;
    public $square_feet;
    public $rent_amount;
    public $status;
    public $description;
    public $features;

    // Status options
    public $statusOptions = [
        'vacant' => 'Vacant',
        'occupied' => 'Occupied',
        'for_lease' => 'For Lease',
        'maintenance' => 'Under Maintenance',
    ];

    protected $rules = [
        'unit_number' => 'required|string|max:20',
        'bedrooms' => 'required|integer|min:0|max:10',
        'bathrooms' => 'required|numeric|min:0|max:10',
        'square_feet' => 'nullable|integer|min:100|max:10000',
        'rent_amount' => 'required|numeric|min:0|max:999999.99',
        'status' => 'required|in:vacant,occupied,for_lease,maintenance',
        'description' => 'nullable|string|max:1000',
        'features' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'unit_number.required' => 'Unit number is required.',
        'unit_number.unique' => 'This unit number already exists for this property.',
        'bedrooms.required' => 'Number of bedrooms is required.',
        'bathrooms.required' => 'Number of bathrooms is required.',
        'rent_amount.required' => 'Rent amount is required.',
        'status.required' => 'Unit status is required.',
    ];

    public function mount($unit = null)
    {
        if ($unit) {
            $this->unit = $unit;
            $this->property = $unit->property;
            $this->fill([
                'unit_number' => $unit->unit_number,
                'bedrooms' => $unit->bedrooms,
                'bathrooms' => $unit->bathrooms,
                'square_feet' => $unit->square_feet,
                'rent_amount' => $unit->rent_amount,
                'status' => $unit->status,
                'description' => $unit->description ?? '',
                'features' => $unit->features ?? '',
            ]);
        } else {
            abort(404, 'Unit not found');
        }
    }

    public function save()
    {
        // Check permissions
        if (!RoleService::roleHasPermission(Auth::user()->role, 'units.edit')) {
            session()->flash('error', 'You do not have permission to edit units.');
            return;
        }

        // Add unique validation for unit_number within the property
        $this->rules['unit_number'] = [
            'required',
            'string',
            'max:20',
            'unique:units,unit_number,' . $this->unit->id . ',id,property_id,' . $this->property->id
        ];

        $this->validate();

        try {
            // Prepare data for update
            $updateData = [
                'unit_number' => $this->unit_number,
                'bedrooms' => (int) $this->bedrooms,
                'bathrooms' => (float) $this->bathrooms,
                'square_feet' => $this->square_feet ? (int) $this->square_feet : null,
                'rent_amount' => (float) $this->rent_amount,
                'status' => $this->status,
                'description' => $this->description,
                'features' => $this->features,
            ];

            // Update the unit
            $this->unit->update($updateData);

            // Success message
            session()->flash('success', 'Unit updated successfully!');
            
            // Redirect to unit show page
            return redirect()->route('units.show', $this->unit->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update unit. Please try again.');
            Log::error('Unit update failed: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('units.show', $this->unit->id);
    }

    public function render()
    {
        return view('livewire.units.unit-form');
    }
}