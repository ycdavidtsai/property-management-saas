<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use App\Services\VendorInvitationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VendorSetupWizard extends Component
{
    // Token and vendor
    public string $token = '';
    public ?Vendor $vendor = null;
    public string $organizationName = '';

    // Current step: verify_phone, enter_otp, complete_profile
    public string $currentStep = 'verify_phone';

    // OTP fields
    public string $otp = '';
    public bool $otpSent = false;
    public int $resendCountdown = 0;

    // Profile fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // UI state
    public bool $isLoading = false;
    public string $errorMessage = '';
    public string $successMessage = '';

    protected VendorInvitationService $invitationService;

    protected $rules = [
        'otp' => 'required|string|size:6',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:8',
        'password_confirmation' => 'required|same:password',
    ];

    protected $messages = [
        'otp.required' => 'Please enter the verification code.',
        'otp.size' => 'The verification code must be 6 digits.',
        'name.required' => 'Please enter your name.',
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
        'password.required' => 'Please create a password.',
        'password.min' => 'Password must be at least 8 characters.',
        'password_confirmation.same' => 'Passwords do not match.',
    ];

    public function boot(VendorInvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    public function mount(string $token, Vendor $vendor, string $organizationName)
    {
        $this->token = $token;
        $this->vendor = $vendor;
        $this->organizationName = $organizationName;

        // Pre-fill known info
        $this->name = $vendor->name ?? '';
        $this->email = $vendor->email ?? '';

        // Check if phone already verified
        if ($vendor->phone_verified_at) {
            $this->currentStep = 'complete_profile';
        }
    }

    /**
     * Send OTP to phone
     */

public function sendOtp()
{
    try {
        logger()->info('VendorSetupWizard: sendOtp called', ['token' => $this->token]);

        $service = app(VendorInvitationService::class);

        // validateToken returns Vendor or null, not an array
        $vendor = $service->validateToken($this->token);

        if (!$vendor) {
            $this->addError('otp', 'Invalid or expired invitation');
            return;
        }

        logger()->info('VendorSetupWizard: calling sendPhoneOtp', ['vendor_id' => $vendor->id]);

        $result = $service->sendPhoneOtp($vendor);

        logger()->info('VendorSetupWizard: OTP result', ['result' => $result]);

        if ($result['success']) {
            $this->currentStep = 'enter_otp';  // Changed from $this->step
            $this->otpSent = true;
            $this->resendCountdown = 60;

            logger()->info('VendorSetupWizard: step changed to enter_otp');
            $this->dispatch('otp-sent');
        } else {
            $this->addError('otp', $result['message'] ?? 'Failed to send code');
        }
    } catch (\Exception $e) {
        logger()->error('VendorSetupWizard: sendOtp error', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        $this->addError('otp', 'Failed to send verification code. Please try again.');
    }
}
    /**
     * Verify OTP
     */
    public function verifyOtp()
    {
        $this->validateOnly('otp');

        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            $verified = $this->invitationService->verifyOtp($this->vendor, $this->otp);

            if ($verified) {
                $this->vendor->refresh();
                $this->currentStep = 'complete_profile';
                $this->successMessage = 'Phone verified! Now complete your profile.';
            } else {
                $this->errorMessage = 'Invalid or expired code. Please try again.';
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Verification failed. Please try again.';
        }

        $this->isLoading = false;
    }

    /**
     * Complete profile and create account
     */
    public function completeProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            $result = $this->invitationService->completeSetup($this->vendor, [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]);

            if ($result['success']) {
                // Log in the user
                Auth::login($result['user']);

                // Redirect to vendor dashboard
                session()->flash('success', 'Welcome! Your account has been created successfully.');
                return redirect()->route('vendor.dashboard');
            } else {
                $this->errorMessage = $result['error'];
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Setup failed: ' . $e->getMessage();
        }

        $this->isLoading = false;
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        if ($this->resendCountdown > 0) {
            return;
        }

        $this->sendOtp();
    }

    /**
     * Go back to previous step
     */
    public function goBack()
    {
        if ($this->currentStep === 'enter_otp') {
            $this->currentStep = 'verify_phone';
            $this->otp = '';
        }
    }

    /**
     * Mask phone number for display
     */
    protected function maskPhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        $length = strlen($digits);

        if ($length < 4) {
            return $phone;
        }

        return '***-***-' . substr($digits, -4);
    }

    public function render()
    {
        return view('livewire.vendors.vendor-setup-wizard')
            ->layout('layouts.guest'); // Use guest layout (no navigation)
    }
}
