<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;  // ➕ ADD THIS LINE
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
    public $vendor_type = 'private'; // Always private for non-admins
    public $is_admin = false;
    public $selected_organizations = [];

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

    // protected function rules()
    // {
    //     return [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255',
    //         'phone' => 'nullable|string|max:20',
    //         'business_type' => 'required|string',
    //         'specialties' => 'nullable|array',
    //         'hourly_rate' => 'nullable|numeric|min:0|max:99999.99',
    //         'notes' => 'nullable|string|max:1000',
    //         'is_active' => 'boolean',
    //         'create_user_account' => 'boolean',
    //     ];
    // }

    public function mount(?Vendor $vendor = null)
    {
        $this->is_admin = Auth::user()->role === 'admin';

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
                'vendor_type' => $vendor->vendor_type,
                //'selected_organizations' => $vendor->organizations->pluck('id')->toArray(),
                'selected_organizations' => $vendor->organizations ? $vendor->organizations->pluck('id')->toArray() : [],
            ]);
        } else {
            $this->authorize('create', Vendor::class);

            // Default vendor type based on user role
            $this->vendor_type = $this->is_admin ? 'global' : 'private';

            // Default to current user's organization for private vendors
            if (!$this->is_admin) {
                $this->selected_organizations = [Auth::user()->organization_id];
            }
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                // Force unique email globally
                Rule::unique('vendors', 'email')->ignore($this->vendor?->id),
            ],
            'phone' => 'nullable|string|max:20',
            'business_type' => 'required|string',
            'specialties' => 'nullable|array',
            'hourly_rate' => 'nullable|numeric|min:0|max:99999.99',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'create_user_account' => 'boolean',
            'vendor_type' => 'required|in:global,private',
            'selected_organizations' => 'required_if:vendor_type,private|array|min:1',
            'selected_organizations.*' => 'exists:organizations,id',
        ];
    }

    public function save()
    {
        $this->validate();

        // Validate user permissions
        if ($this->vendor_type === 'global' && !$this->is_admin) {
            $this->addError('vendor_type', 'Only administrators can create global vendors.');
            return;
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'business_type' => $this->business_type,
            'specialties' => $this->specialties,
            'hourly_rate' => $this->hourly_rate,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'vendor_type' => $this->vendor_type,
            'created_by_organization_id' => $this->vendor_type === 'private'
                ? Auth::user()->organization_id
                : null,
        ];

        if ($this->vendor && $this->vendor->exists) {
            // Update existing vendor
            $this->authorize('update', $this->vendor);

            // Only allow editing if user has permission
            if (!$this->vendor->canBeEditedBy(Auth::user())) {
                session()->flash('error', 'You do not have permission to edit this vendor.');
                return redirect()->route('vendors.index');
            }

            $this->vendor->update($data);

            // Sync organizations (only for private vendors)
            if ($this->vendor->isPrivate()) {
                $this->vendor->organizations()->sync($this->selected_organizations);
            }

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

            // Attach organizations (only for private vendors or if admin selected orgs)
            if ($this->vendor_type === 'private') {
                $this->vendor->organizations()->attach($this->selected_organizations);
            } elseif ($this->vendor_type === 'global' && !empty($this->selected_organizations)) {
                // For global vendors, admin can optionally pre-add them to organizations
                $this->vendor->organizations()->attach($this->selected_organizations);
            }

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
        // Get all organizations (for admin) or just current user's organization
        $user = Auth::user();
        $availableOrganizations = $user->role === 'admin'
            ? Organization::orderBy('name')->get()
            : Organization::where('id', $user->organization_id)->get();

        return view('livewire.vendors.vendor-form', [
            'availableOrganizations' => $availableOrganizations,
        ]);
    }
}
