<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use App\Models\Organization;
use App\Services\VendorInvitationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
    public $vendor_type = 'private';
    public $is_admin = false;
    public $selected_organizations = [];

    // NEW: Invitation mode
    public bool $sendInvitation = true; // Default to sending invitation

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
                'selected_organizations' => $vendor->organizations ? $vendor->organizations->pluck('id')->toArray() : [],
            ]);
            // Don't show invitation option when editing
            $this->sendInvitation = false;
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
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('vendors', 'email')->ignore($this->vendor?->id),
            ],
            'phone' => [
                $this->sendInvitation && !$this->vendor?->exists ? 'required' : 'nullable',
                'string',
                'max:20',
            ],
            'business_type' => 'required|string',
            'specialties' => 'nullable|array',
            'hourly_rate' => 'nullable|numeric|min:0|max:99999.99',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'vendor_type' => 'required|in:global,private',
            'selected_organizations' => 'required_if:vendor_type,private|array|min:1',
            'selected_organizations.*' => 'exists:organizations,id',
        ];

        return $rules;
    }

    protected $messages = [
        'phone.required' => 'Phone number is required to send an invitation.',
    ];

    public function save()
    {
        $this->validate();

        // Validate user permissions
        if ($this->vendor_type === 'global' && !$this->is_admin) {
            $this->addError('vendor_type', 'Only administrators can create global vendors.');
            return;
        }

        if ($this->vendor && $this->vendor->exists) {
            // UPDATE EXISTING VENDOR
            return $this->updateVendor();
        } else {
            // CREATE NEW VENDOR
            return $this->createVendor();
        }
    }

    protected function createVendor()
    {
        $this->authorize('create', Vendor::class);

        if ($this->sendInvitation) {
            // Use invitation service
            $invitationService = app(VendorInvitationService::class);

            $result = $invitationService->createAndInvite([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'business_type' => $this->business_type,
                'specialties' => $this->specialties,
                'hourly_rate' => $this->hourly_rate,
                'notes' => $this->notes,
                'vendor_type' => $this->vendor_type,
            ], Auth::user());

            if (!$result['success']) {
                $this->addError('phone', $result['error'] ?? 'Failed to create vendor.');
                return;
            }

            $this->vendor = $result['vendor'];
            session()->flash('message', $result['message']);

        } else {
            // Create vendor without invitation (no user account)
            $this->vendor = Vendor::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'business_type' => $this->business_type,
                'specialties' => $this->specialties,
                'hourly_rate' => $this->hourly_rate,
                'notes' => $this->notes,
                'is_active' => $this->is_active,
                'vendor_type' => $this->vendor_type,
                'created_by_organization_id' => Auth::user()->organization_id,
                'setup_status' => 'active', // Active but no user account
            ]);

            // Attach organizations
            if ($this->vendor_type === 'private') {
                $this->vendor->organizations()->attach($this->selected_organizations);
            }

            session()->flash('message', 'Vendor created successfully. Note: No user account was created - the vendor cannot log in.');
        }

        return redirect()->route('vendors.show', $this->vendor);
    }

    protected function updateVendor()
    {
        $this->authorize('update', $this->vendor);

        // Only allow editing if user has permission
        if (!$this->vendor->canBeEditedBy(Auth::user())) {
            session()->flash('error', 'You do not have permission to edit this vendor.');
            return redirect()->route('vendors.index');
        }

        // Check if vendor has user account (managed by user)
        if ($this->vendor->isManagedByUser()) {
            // Only allow limited updates
            $this->vendor->update([
                'business_type' => $this->business_type,
                'specialties' => $this->specialties,
                'hourly_rate' => $this->hourly_rate,
                'notes' => $this->notes,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Vendor updated. Note: Name, email, and phone are managed by the vendor.');
        } else {
            // Full update allowed
            $this->vendor->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'business_type' => $this->business_type,
                'specialties' => $this->specialties,
                'hourly_rate' => $this->hourly_rate,
                'notes' => $this->notes,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Vendor updated successfully.');
        }

        // Sync organizations (only for private vendors)
        if ($this->vendor->isPrivate()) {
            $this->vendor->organizations()->sync($this->selected_organizations);
        }

        return redirect()->route('vendors.show', $this->vendor);
    }

    /**
     * Resend invitation to pending vendor
     */
    public function resendInvitation()
    {
        if (!$this->vendor || $this->vendor->setup_status !== 'pending_setup') {
            session()->flash('error', 'Cannot resend invitation to this vendor.');
            return;
        }

        $invitationService = app(VendorInvitationService::class);
        $result = $invitationService->sendInvitation($this->vendor, Auth::user());

        if ($result['success']) {
            session()->flash('message', 'Invitation resent successfully.');
        } else {
            session()->flash('error', $result['error']);
        }

        $this->vendor->refresh();
    }

    public function render()
    {
        $user = Auth::user();
        $availableOrganizations = $user->role === 'admin'
            ? Organization::orderBy('name')->get()
            : Organization::where('id', $user->organization_id)->get();

        // Get invitation status if editing a pending vendor
        $invitationStatus = null;
        if ($this->vendor && $this->vendor->setup_status === 'pending_setup') {
            $invitationService = app(VendorInvitationService::class);
            $invitationStatus = $invitationService->getInvitationStatus($this->vendor);
        }

        $hasUserAccount = false;
        $isPendingSetup = false;
        $canResendInvitation = false;

        if ($this->vendor && $this->vendor->exists) {
            $hasUserAccount = $this->vendor->user_id !== null;
            $isPendingSetup = method_exists($this->vendor, 'isPendingSetup') && $this->vendor->isPendingSetup();
            $canResendInvitation = $isPendingSetup && !$this->vendor->isInvitationExpired();
        }

        return view('livewire.vendors.vendor-form', [
            'availableOrganizations' => $availableOrganizations,
            'invitationStatus' => $invitationStatus,
            'hasUserAccount' => $hasUserAccount,
            'isPendingSetup' => $isPendingSetup,
            'canResendInvitation' => $canResendInvitation,
            'isEditing' => $this->vendor && $this->vendor->exists,
        ]);
    }
}
