<?php

namespace App\Livewire\Properties;

use App\Models\Property;
use App\Models\Unit;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PropertyEdit extends Component
{
    public Property $property;
    public $name;
    public $address;
    public $type;
    public $total_units;
    public $current_unit_count;

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string',
        'type' => 'required|in:single_family,multi_family,apartment,commercial',
        'total_units' => 'required|integer|min:1|max:500',
    ];

    public function mount(Property $property)
    {
        $this->property = $property;
        $this->name = $property->name;
        $this->address = $property->address;
        $this->type = $property->type;
        $this->total_units = $property->total_units;
        $this->current_unit_count = $property->units()->count();
    }

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // First check if unit count change is possible
                $unitChangeResult = $this->handleUnitCountChange();

                if (!$unitChangeResult['success']) {
                    // Add validation error for total_units field
                    $this->addError('total_units', $unitChangeResult['message']);
                    throw new \Exception('Unit count change failed');
                }

                // Only update property if unit changes were successful
                $this->property->update([
                    'name' => $this->name,
                    'address' => $this->address,
                    'type' => $this->type,
                    'total_units' => $this->total_units,
                ]);
            });

            session()->flash('message', 'Property updated successfully!');
            return redirect()->route('properties.show', $this->property);

        } catch (\Exception $e) {
            // Stay on the edit page, error messages are already set
            logger('Property update failed: ' . $e->getMessage());
        }
    }

    private function handleUnitCountChange(): array
    {
        $currentCount = $this->current_unit_count;
        $newCount = $this->total_units;

        if ($newCount > $currentCount) {
            // Add new units
            for ($i = $currentCount + 1; $i <= $newCount; $i++) {
                Unit::create([
                    'property_id' => $this->property->id,
                    'unit_number' => $this->generateUnitNumber($i),
                    'rent_amount' => 0,
                    'status' => 'vacant',
                ]);
            }
            logger("Added " . ($newCount - $currentCount) . " units");
            return ['success' => true, 'message' => 'Units added successfully'];

        } elseif ($newCount < $currentCount) {
            // Check if we can remove the required number of units
            $unitsToRemoveCount = $currentCount - $newCount;
            $vacantUnits = $this->property->units()
                ->where('status', 'vacant')
                ->orderByDesc('unit_number')
                ->get();

            if ($vacantUnits->count() < $unitsToRemoveCount) {
                $occupiedCount = $this->property->units()->where('status', '!=', 'vacant')->count();
                $availableToRemove = $vacantUnits->count();

                return [
                    'success' => false,
                    'message' => "Cannot reduce to {$newCount} units. You can only remove {$availableToRemove} vacant units. {$occupiedCount} units are occupied or in maintenance."
                ];
            }

            // Remove the excess vacant units
            $unitsToRemove = $vacantUnits->take($unitsToRemoveCount);
            foreach ($unitsToRemove as $unit) {
                $unit->delete();
            }

            logger("Removed " . $unitsToRemove->count() . " vacant units");
            return ['success' => true, 'message' => 'Vacant units removed successfully'];

        } else {
            // No unit count change
            return ['success' => true, 'message' => 'No unit count change'];
        }
    }

    private function generateUnitNumber($index)
    {
        if ($this->type === 'single_family') {
            return '1';
        }

        if ($this->total_units <= 10) {
            return (string) $index;
        }

        if ($this->total_units <= 100) {
            return '1' . str_pad($index, 2, '0', STR_PAD_LEFT);
        }

        return (string) $index;
    }

    public function render()
    {
        return view('livewire.properties.property-edit');
    }
}
