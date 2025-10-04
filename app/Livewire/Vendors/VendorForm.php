<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VendorForm extends Component
{
    use AuthorizesRequests;

    public ?Vendor $vendor = null;
    public $name;
    public $email;
    public $phone;
    public $business_type;
    public $specialties = [];
    public $hourly_rate;
    public $notes;
    public $is_active = true;

    // Available business types
    public $businessTypeOptions = [
        'Plumbing' => 'Plumbing',
        'Electrical' => 'Electrical',
        'HVAC' => 'HVAC',
        'General Maintenance' => 'General Maintenance',
        'Appliance Repair' => 'Appliance Repair',
        'Carpentry' => 'Carpentry',
        'Painting' => 'Painting',
        'Roofing' => 'Roofing',
        'Landscaping' => 'Landscaping',
        'Pest Control' => 'Pest Control',
        'Other' => 'Other',
    ];

    // Available specialties
    public $specialtyOptions = [
        'Emergency Services',
        '24/7 Available',
        'Licensed & Insured',
        'Commercial',
        'Residential',
        'New Installation',
        'Repairs',
        'Preventive Maintenance',
        'Inspections',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'business_type' => 'required|string',
            'specialties' => 'nullable|array',
            'hourly_rate' => 'nullable|numeric|min:0|max:99999.99',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    public function mount(?Vendor $vendor = null)
    {
        if ($vendor && $vendor->exists) {
            $this->authorize('update', $vendor);
            $this->vendor = $vendor;
            $this->fill([
                'name' => $vendor->name,
                'email' => $vendor->email,
                'phone' => $vendor->phone,
                'business_type' => $vendor->business_type,
                'specialties' => is_string($vendor->specialties)
                    ? json_decode($vendor->specialties, true) ?? []
                    : ($vendor->specialties ?? []),
                'hourly_rate' => $vendor->hourly_rate,
                'notes' => $vendor->notes,
                'is_active' => $vendor->is_active,
            ]);
        } else {
            $this->authorize('create', Vendor::class);
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'business_type' => $this->business_type,
            'specialties' => json_encode($this->specialties),
            'hourly_rate' => $this->hourly_rate,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'organization_id' => Auth::user()->organization_id,
        ];

        if ($this->vendor && $this->vendor->exists) {
            // Update existing vendor
            $this->authorize('update', $this->vendor);
            $this->vendor->update($data);
            session()->flash('message', 'Vendor updated successfully.');
        } else {
            // Create new vendor
            $this->authorize('create', Vendor::class);
            $this->vendor = Vendor::create($data);
            session()->flash('message', 'Vendor created successfully.');
        }

        return redirect()->route('vendors.show', $this->vendor);
    }

    public function render()
    {
        return view('livewire.vendors.vendor-form');
    }
}
