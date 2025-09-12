<?php

namespace App\Livewire\Tenants;

use App\Models\User;
use App\Models\TenantProfile;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TenantForm extends Component
{
    public User $tenant;

    // Individual properties for better validation
    public $name = '';
    public $email = '';
    public $phone = '';
    public $is_active = true;
    public $password = '';
    public $password_confirmation = '';

    // Tenant profile properties
    public $date_of_birth = '';
    public $ssn_last_four = '';
    public $employment_status = '';
    public $monthly_income = '';
    public $emergency_contact_name = '';
    public $emergency_contact_phone = '';
    public $emergency_contact_relationship = '';
    public $notes = '';

    public $isEditing = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->tenant->id ?? null)
            ],
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'password' => $this->isEditing ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'date_of_birth' => 'nullable|date|before:today',
            'ssn_last_four' => 'nullable|string|size:4|regex:/^\d{4}$/',
            'employment_status' => 'nullable|string|in:employed,self_employed,unemployed,retired,student',
            'monthly_income' => 'nullable|numeric|min:0|max:999999.99',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'name.required' => 'Full name is required.',
        'email.required' => 'Email address is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email address is already in use.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'The password confirmation does not match.',
        'ssn_last_four.size' => 'SSN must be exactly 4 digits.',
        'ssn_last_four.regex' => 'SSN must contain only numbers.',
        'date_of_birth.before' => 'Date of birth must be in the past.',
        'monthly_income.numeric' => 'Monthly income must be a valid number.',
        'monthly_income.max' => 'Monthly income cannot exceed $999,999.99.',
    ];

    // Real-time validation methods
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount(User $tenant = null)
    {
        if ($tenant && $tenant->exists) {
            // Ensure tenant belongs to current organization
            if ($tenant->organization_id !== session('current_organization_id')) {
                abort(404);
            }

            $this->tenant = $tenant;
            $this->isEditing = true;

            // Populate form fields
            $this->name = $tenant->name;
            $this->email = $tenant->email;
            $this->phone = $tenant->phone;
            $this->is_active = $tenant->is_active;

            // Load tenant profile data if exists
            if ($tenant->tenantProfile) {
                $profile = $tenant->tenantProfile;
                $this->date_of_birth = $profile->date_of_birth?->format('Y-m-d') ?? '';
                $this->ssn_last_four = $profile->ssn_last_four ?? '';
                $this->employment_status = $profile->employment_status ?? '';
                $this->monthly_income = $profile->monthly_income ?? '';
                $this->emergency_contact_name = $profile->emergency_contact_name ?? '';
                $this->emergency_contact_phone = $profile->emergency_contact_phone ?? '';
                $this->emergency_contact_relationship = $profile->emergency_contact_relationship ?? '';
                $this->notes = $profile->notes ?? '';
            }
        } else {
            $this->tenant = new User();
            $this->is_active = true;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            // Set tenant properties
            $this->tenant->name = $this->name;
            $this->tenant->email = $this->email;
            $this->tenant->phone = $this->phone;
            $this->tenant->is_active = $this->is_active;

            if (!$this->isEditing) {
                $this->tenant->organization_id = session('current_organization_id');
                $this->tenant->role = 'tenant';
                $this->tenant->password = Hash::make($this->password);
            } elseif ($this->password) {
                $this->tenant->password = Hash::make($this->password);
            }

            $this->tenant->save();

            // Save tenant profile data
            $profileData = [
                'date_of_birth' => $this->date_of_birth ?: null,
                'ssn_last_four' => $this->ssn_last_four ?: null,
                'employment_status' => $this->employment_status ?: null,
                'monthly_income' => $this->monthly_income ?: null,
                'emergency_contact_name' => $this->emergency_contact_name ?: null,
                'emergency_contact_phone' => $this->emergency_contact_phone ?: null,
                'emergency_contact_relationship' => $this->emergency_contact_relationship ?: null,
                'notes' => $this->notes ?: null,
            ];

            // Remove null values
            $profileData = array_filter($profileData, function($value) {
                return $value !== null && $value !== '';
            });

            if (!empty($profileData)) {
                $this->tenant->tenantProfile()->updateOrCreate(
                    ['user_id' => $this->tenant->id],
                    $profileData
                );
            }

            $message = $this->isEditing ? 'Tenant updated successfully!' : 'Tenant created successfully!';
            session()->flash('message', $message);

            return redirect()->route('tenants.index');

        } catch (\Exception $e) {
            $this->addError('general', 'An error occurred while saving. Please try again.');
            return;
        }
    }

    public function render()
    {
        return view('livewire.tenants.tenant-form');
    }
}
