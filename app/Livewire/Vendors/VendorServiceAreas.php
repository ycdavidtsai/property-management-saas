<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class VendorServiceAreas extends Component
{
    // Area type: 'zip_codes' or 'cities'
    public string $areaType = 'zip_codes';

    // List of areas
    public array $areas = [];

    // New area input
    public string $newArea = '';

    // UI state
    public bool $hasChanges = false;

    protected $rules = [
        'areaType' => 'required|in:zip_codes,cities',
        'areas.*' => 'string|max:100',
        'newArea' => 'nullable|string|max:100',
    ];

    public function mount()
    {
        $this->loadServiceAreas();
    }

    /**
     * Load vendor's current service areas from database
     */
    protected function loadServiceAreas(): void
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            Log::warning('VendorServiceAreas: No vendor found', ['user_id' => Auth::id()]);
            return;
        }

        $serviceAreas = $vendor->service_areas;

        if (!$serviceAreas) {
            Log::info('VendorServiceAreas: No service areas in DB, using defaults');
            return;
        }

        // Handle both array and JSON string
        if (is_string($serviceAreas)) {
            $serviceAreas = json_decode($serviceAreas, true) ?? [];
        }

        // Load type - default to zip_codes if not set
        $this->areaType = $serviceAreas['type'] ?? 'zip_codes';
        $this->areas = $serviceAreas['areas'] ?? [];

        Log::info('VendorServiceAreas: Loaded', [
            'type' => $this->areaType,
            'areas_count' => count($this->areas)
        ]);
    }

    protected function getVendor(): ?Vendor
    {
        return Vendor::where('user_id', Auth::id())->first();
    }

    /**
     * Called when areaType changes via wire:model
     */
    public function updatedAreaType($value)
    {
        Log::info('VendorServiceAreas: Type changed', ['new_type' => $value]);
        $this->hasChanges = true;

        // Note: We intentionally do NOT clear areas here
        // User might accidentally click wrong type
    }

    /**
     * Explicitly set area type (called from blade buttons)
     */
    public function setAreaType(string $type): void
    {
        if (!in_array($type, ['zip_codes', 'cities'])) {
            return;
        }

        if ($this->areaType !== $type) {
            $this->areaType = $type;
            $this->hasChanges = true;

            Log::info('VendorServiceAreas: Type set explicitly', ['type' => $type]);
        }
    }

    /**
     * Add a new service area
     */
    public function addArea(): void
    {
        $area = trim($this->newArea);

        if (empty($area)) {
            return;
        }

        // Validate based on current type
        if ($this->areaType === 'zip_codes') {
            if (!preg_match('/^\d{5}(-\d{4})?$/', $area)) {
                $this->addError('newArea', 'Please enter a valid zip code (e.g., 90210)');
                return;
            }
            $area = substr($area, 0, 5);
        } else {
            $area = ucwords(strtolower($area));
        }

        if (in_array($area, $this->areas)) {
            $this->addError('newArea', 'This area is already in your list.');
            return;
        }

        $this->areas[] = $area;

        // Sort
        if ($this->areaType === 'zip_codes') {
            sort($this->areas, SORT_NUMERIC);
        } else {
            sort($this->areas, SORT_STRING);
        }

        $this->newArea = '';
        $this->hasChanges = true;
        $this->resetErrorBag('newArea');

        Log::info('VendorServiceAreas: Area added', [
            'area' => $area,
            'type' => $this->areaType,
            'total' => count($this->areas)
        ]);
    }

    /**
     * Remove an area from the list
     */
    public function removeArea(int $index): void
    {
        if (isset($this->areas[$index])) {
            $removed = $this->areas[$index];
            array_splice($this->areas, $index, 1);
            $this->hasChanges = true;

            Log::info('VendorServiceAreas: Area removed', ['area' => $removed]);
        }
    }

    /**
     * Save service areas to database
     */
    public function save(): void
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            session()->flash('error', 'Vendor profile not found.');
            return;
        }

        // Log what we're about to save
        Log::info('VendorServiceAreas: Saving', [
            'vendor_id' => $vendor->id,
            'type' => $this->areaType,
            'areas' => $this->areas,
        ]);

        try {
            $vendor->service_areas = [
                'type' => $this->areaType,
                'areas' => $this->areas,
            ];
            $vendor->save();

            $this->hasChanges = false;
            session()->flash('success', 'Service areas saved successfully.');

            Log::info('VendorServiceAreas: Save successful');

        } catch (\Exception $e) {
            Log::error('VendorServiceAreas: Save failed', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to save. Please try again.');
        }
    }

    /**
     * Clear all areas
     */
    public function clearAll(): void
    {
        $this->areas = [];
        $this->hasChanges = true;
    }

    public function render()
    {
        return view('livewire.vendors.vendor-service-areas');
    }
}
