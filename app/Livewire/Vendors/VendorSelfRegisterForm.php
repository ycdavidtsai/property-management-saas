<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use App\Models\VendorPromotionRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class VendorSelfRegisterForm extends Component
{
    use WithFileUploads;

    // Step tracking
    public int $currentStep = 1;
    public int $totalSteps = 3;

    // Step 1: Business Information
    public string $company_name = '';
    public string $contact_name = '';
    public string $email = '';
    public string $phone = '';
    public array $specialties = [];
    public string $description = '';

    // Step 2: Phone Verification
    public string $otp = '';
    public bool $otpSent = false;
    public int $otpResendCount = 0;
    public ?string $otpError = null;
    public int $otpCooldown = 0;

    // Step 3: Account Setup
    public string $password = '';
    public string $password_confirmation = '';
    public bool $agree_terms = false;

    // State
    public bool $isSubmitting = false;
    public ?Vendor $vendor = null;

    // Available specialties
    public array $availableSpecialties = [
        'plumbing' => 'Plumbing',
        'electrical' => 'Electrical',
        'hvac' => 'HVAC',
        'appliance_repair' => 'Appliance Repair',
        'general_maintenance' => 'General Maintenance',
        'landscaping' => 'Landscaping',
        'cleaning' => 'Cleaning',
        'pest_control' => 'Pest Control',
        'roofing' => 'Roofing',
        'painting' => 'Painting',
        'carpentry' => 'Carpentry',
        'locksmith' => 'Locksmith',
        'flooring' => 'Flooring',
        'windows_doors' => 'Windows & Doors',
        'other' => 'Other',
    ];

    protected function rules()
    {
        $vendorId = $this->vendor?->id;

        return match ($this->currentStep) {
            1 => [
                'company_name' => 'required|string|max:255',
                'contact_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'unique:users,email',
                    // Allow if re-registering same vendor
                    $vendorId
                        ? 'unique:vendors,email,' . $vendorId
                        : 'unique:vendors,email',
                ],
                'phone' => 'required|string|min:10|max:20',
                'specialties' => 'required|array|min:1',
                'description' => 'nullable|string|max:1000',
            ],
            2 => [
                'otp' => 'required|string|size:6',
            ],
            3 => [
                'password' => 'required|string|min:8|confirmed',
                'agree_terms' => 'accepted',
            ],
            default => [],
        };
    }

    protected $messages = [
        'company_name.required' => 'Please enter your business name.',
        'contact_name.required' => 'Please enter your name.',
        'email.required' => 'Please enter your email address.',
        'email.unique' => 'This email is already registered.',
        'phone.required' => 'Please enter your phone number.',
        'specialties.required' => 'Please select at least one specialty.',
        'specialties.min' => 'Please select at least one specialty.',
        'otp.required' => 'Please enter the verification code.',
        'otp.size' => 'The verification code must be 6 digits.',
        'password.required' => 'Please create a password.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Passwords do not match.',
        'agree_terms.accepted' => 'You must agree to the terms and conditions.',
    ];

    public function mount()
    {
        // Check if there's a pending registration in session
        $pendingVendorId = session('pending_vendor_registration');
        if ($pendingVendorId) {
            $this->vendor = Vendor::find($pendingVendorId);
            if ($this->vendor && $this->vendor->phone_verified_at) {
                $this->currentStep = 3;
                $this->otpSent = true;
            } elseif ($this->vendor) {
                $this->currentStep = 2;
                $this->otpSent = true;
                $this->loadVendorData();
            }
        }
    }

    protected function loadVendorData()
    {
        if ($this->vendor) {
            $this->company_name = $this->vendor->name ?? '';  // DB column is 'name'
            $this->contact_name = $this->vendor->contact_name ?? '';
            $this->email = $this->vendor->email ?? '';
            $this->phone = $this->vendor->phone ?? '';
            $this->specialties = $this->vendor->specialties ?? [];
            $this->description = $this->vendor->description ?? '';
        }
    }

    /**
     * Step 1: Submit business information and create pending vendor
     */
    public function submitBusinessInfo()
    {
        // Debug: Log that method was called
        Log::info('submitBusinessInfo called', [
            'company_name' => $this->company_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'specialties' => $this->specialties,
        ]);

        try {
            // Validate first - this will throw and show errors if validation fails
            $this->validate();

            Log::info('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Format phone number
            $formattedPhone = $this->formatPhoneNumber($this->phone);
            Log::info('Phone formatted', ['formatted' => $formattedPhone]);

            // Check if phone already exists
            $existingVendor = Vendor::where('phone', $formattedPhone)->first();
            if ($existingVendor) {
                if ($existingVendor->setup_status === 'active') {
                    $this->addError('phone', 'This phone number is already registered.');
                    DB::rollBack();
                    return;
                }
                // Allow re-registration if rejected or pending
                $this->vendor = $existingVendor;
            }

            // Check if email already exists in vendors (not caught by unique rule in edge cases)
            $existingByEmail = Vendor::where('email', $this->email)->first();
            if ($existingByEmail && (!$this->vendor || $existingByEmail->id !== $this->vendor->id)) {
                if ($existingByEmail->setup_status === 'active') {
                    $this->addError('email', 'This email is already registered.');
                    DB::rollBack();
                    return;
                }
                // Use existing vendor record
                $this->vendor = $existingByEmail;
            }

            if (!$this->vendor) {
                // Create new vendor record
                $this->vendor = new Vendor();
            }

            $this->vendor->fill([
                'name' => $this->company_name,
                'contact_name' => $this->contact_name,
                'email' => $this->email,
                'phone' => $formattedPhone,
                'specialties' => $this->specialties,
                'description' => $this->description,
                'setup_status' => 'pending_setup',
                'registration_source' => 'self_registered',
                'vendor_type' => 'private',
                'invitation_token' => Str::random(64),
            ]);
            $this->vendor->save();

            Log::info('Vendor saved', ['vendor_id' => $this->vendor->id]);

            // Store in session for recovery
            session(['pending_vendor_registration' => $this->vendor->id]);

            DB::commit();

            // Send OTP
            $this->sendOtp();

            $this->currentStep = 2;

            Log::info('Moving to step 2');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor self-registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $this->email,
            ]);
            session()->flash('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Send OTP to phone
     */
    public function sendOtp()
    {
        if (!$this->vendor) {
            $this->otpError = 'Session expired. Please start over.';
            return;
        }

        if ($this->otpResendCount >= 3) {
            $this->otpError = 'Maximum OTP attempts reached. Please try again later.';
            return;
        }

        try {
            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store hashed OTP with 10-minute expiry
            $this->vendor->update([
                'phone_verification_code' => Hash::make($otp),
                'phone_verification_expires_at' => now()->addMinutes(10),
            ]);

            // Send SMS
            $notificationService = app(NotificationService::class);
            $message = "Your verification code is: {$otp}\n\nThis code expires in 10 minutes.";

            $notificationService->sendSmsDirectly($this->vendor->phone, $message);

            $this->otpSent = true;
            $this->otpResendCount++;
            $this->otpError = null;
            $this->otpCooldown = 60; // 60 second cooldown

            Log::info('OTP sent for vendor self-registration', [
                'vendor_id' => $this->vendor->id,
                'phone' => $this->vendor->phone,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'vendor_id' => $this->vendor->id,
                'error' => $e->getMessage(),
            ]);
            $this->otpError = 'Failed to send verification code. Please try again.';
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        if ($this->otpCooldown > 0) {
            return;
        }
        $this->sendOtp();
    }

    /**
     * Verify OTP
     */
    public function verifyOtp()
    {
        $this->validate();

        if (!$this->vendor) {
            $this->otpError = 'Session expired. Please start over.';
            return;
        }

        // Check if OTP expired
        if ($this->vendor->phone_verification_expires_at < now()) {
            $this->otpError = 'Verification code has expired. Please request a new one.';
            return;
        }

        // Verify OTP
        if (!Hash::check($this->otp, $this->vendor->phone_verification_code)) {
            $this->otpError = 'Invalid verification code. Please try again.';
            return;
        }

        // Mark phone as verified
        $this->vendor->update([
            'phone_verified_at' => now(),
            'phone_verification_code' => null,
        ]);

        $this->otpError = null;
        $this->currentStep = 3;

        Log::info('Phone verified for vendor self-registration', [
            'vendor_id' => $this->vendor->id,
        ]);
    }

    /**
     * Complete registration
     */
    public function completeRegistration()
    {
        $this->validate();

        if (!$this->vendor) {
            session()->flash('error', 'Session expired. Please start over.');
            return redirect()->route('vendor.register');
        }

        $this->isSubmitting = true;

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => $this->contact_name,
                'email' => $this->email,
                'phone' => $this->vendor->phone,
                'password' => Hash::make($this->password),
                'role' => 'vendor',
                'email_verified_at' => now(), // Phone verified = email trusted
            ]);

            // Link user to vendor and update status
            // Self-registered vendors are pending approval to become GLOBAL vendors
            $this->vendor->update([
                'user_id' => $user->id,
                'setup_status' => 'pending_approval',
                'registration_source' => 'self_registered',
                'vendor_type' => 'private',  // Will be changed to 'global' when approved
            ]);

            // Create promotion request record for tracking
            VendorPromotionRequest::create([
                'vendor_id' => $this->vendor->id,
                'request_type' => 'registration',  // 'registration' vs 'promotion'
                'requested_by_user_id' => $user->id,
                'request_message' => 'Self-registered vendor application for global marketplace access.',
                'requested_at' => now(),
                'status' => 'pending',
            ]);

            // Clear session
            session()->forget('pending_vendor_registration');

            DB::commit();

            // Send notification to admins
            $this->notifyAdminsOfNewRegistration();

            // Log in the user
            Auth::login($user);

            Log::info('Vendor self-registration completed', [
                'vendor_id' => $this->vendor->id,
                'user_id' => $user->id,
            ]);

            // Redirect to pending approval page
            return redirect()->route('vendor.register.pending');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->isSubmitting = false;

            Log::error('Vendor registration completion failed', [
                'vendor_id' => $this->vendor->id ?? null,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error', 'Failed to complete registration. Please try again.');
        }
    }

    /**
     * Notify admins of new vendor registration
     */
    protected function notifyAdminsOfNewRegistration()
    {
        try {
            $admins = User::where('role', 'admin')->get();
            $notificationService = app(NotificationService::class);

            foreach ($admins as $admin) {
                $notificationService->sendEmail(
                    $admin,
                    'New Vendor Registration Pending Approval',
                    "A new vendor has registered and is awaiting approval.\n\n" .
                    "Company: {$this->vendor->name}\n" .
                    "Contact: {$this->vendor->contact_name}\n" .
                    "Email: {$this->vendor->email}\n" .
                    "Phone: {$this->vendor->phone}\n\n" .
                    "Please review and approve/reject in the admin dashboard.",
                    'general'
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins of new vendor registration', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Go back to previous step
     */
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Format phone number to E.164 format
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($digits) === 10) {
            return '+1' . $digits;
        }

        if (strlen($digits) === 11 && $digits[0] === '1') {
            return '+' . $digits;
        }

        return '+' . $digits;
    }

    /**
     * Decrement OTP cooldown timer
     */
    public function decrementCooldown()
    {
        if ($this->otpCooldown > 0) {
            $this->otpCooldown--;
        }
    }

    public function render()
    {
        return view('livewire.vendors.vendor-self-register-form');
    }
}
