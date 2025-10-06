<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
    public $create_user_account = true; // For new vendors

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
            'create_user_account' => 'boolean',
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

        // Check if email is already used by another vendor in this organization
        $existingVendor = Vendor::where('organization_id', Auth::user()->organization_id)
            ->where('email', $this->email)
            ->when($this->vendor, fn($q) => $q->where('id', '!=', $this->vendor->id))
            ->first();

        if ($existingVendor) {
            $this->addError('email', 'A vendor with this email already exists in your organization.');
            return;
        }

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

            // Update linked user account if exists
            if ($this->vendor->user) {
                $this->vendor->user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                ]);
            }

            session()->flash('message', 'Vendor updated successfully.');
        } else {
            // Create new vendor
            $this->authorize('create', Vendor::class);
            $this->vendor = Vendor::create($data);

            // Create user account if requested
            if ($this->create_user_account) {
                $this->createUserAccountForVendor($this->vendor);
            }

            session()->flash('message', 'Vendor created successfully.' .
                ($this->create_user_account ? ' User account created - please send password reset link to the vendor.' : ''));
        }

        return redirect()->route('vendors.show', $this->vendor);
    }

    public function createUserAccount()
    {
        // Check if vendor already has a user account
        if ($this->vendor && $this->vendor->user) {
            session()->flash('error', 'This vendor already has a user account.');
            return;
        }

        if (!$this->vendor) {
            session()->flash('error', 'No vendor record found.');
            return;
        }

        // Check if email is already taken
        $existingUser = User::where('email', $this->vendor->email)->first();
        if ($existingUser) {
            session()->flash('error', 'This email is already registered as a user account.');
            return;
        }

        $this->createUserAccountForVendor($this->vendor);

        $this->vendor->refresh();
        session()->flash('message', 'User account created successfully. Please send password reset link to the vendor.');
    }

    private function createUserAccountForVendor(Vendor $vendor)
    {
        // Check if email is already taken
        $existingUser = User::where('email', $vendor->email)->first();
        if ($existingUser) {
            return; // Silently fail if user exists
        }

        //$temporaryPassword = Str::random(16);
        $temporaryPassword = 'pigu38man'; //temporary fixed password for testing

        $user = User::create([
            'name' => $vendor->name,
            'email' => $vendor->email,
            'password' => Hash::make($temporaryPassword),
            'organization_id' => $vendor->organization_id,
            'role' => 'vendor',
        ]);

        // Link vendor to user
        $vendor->update(['user_id' => $user->id]);
    }

    public function render()
    {
        return view('livewire.vendors.vendor-form');
    }
}
