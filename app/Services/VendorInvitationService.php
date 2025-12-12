<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\User;
use App\Models\ShortUrl;
use App\Jobs\SendVendorInvitationJob;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VendorInvitationService
{
    protected NotificationService $notificationService;

    // Configuration
    const INVITATION_EXPIRY_DAYS = 7;
    const OTP_EXPIRY_MINUTES = 10;
    const MAX_RESENDS_PER_DAY = 3;
    const OTP_LENGTH = 6;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a vendor with pending setup status and send invitation
     */
    public function createAndInvite(array $vendorData, User $invitedBy): array
    {
        // Validate phone number
        if (empty($vendorData['phone'])) {
            return [
                'success' => false,
                'error' => 'Phone number is required to send an invitation.',
            ];
        }

        // Generate invitation token
        $token = $this->generateInvitationToken();

        // Create vendor with pending status
        $vendor = Vendor::create([
            'name' => $vendorData['name'],
            'email' => $vendorData['email'],
            'phone' => $this->formatPhoneNumber($vendorData['phone']),
            'business_type' => $vendorData['business_type'],
            'specialties' => $vendorData['specialties'] ?? [],
            'hourly_rate' => $vendorData['hourly_rate'] ?? null,
            'notes' => $vendorData['notes'] ?? null,
            'is_active' => false, // Not active until setup complete
            'setup_status' => 'pending_setup',
            'vendor_type' => $vendorData['vendor_type'] ?? 'private',
            'created_by_organization_id' => $invitedBy->organization_id,
            'invitation_token' => $token,
            'invitation_expires_at' => now()->addDays(self::INVITATION_EXPIRY_DAYS),
        ]);

        // Attach to organization
        if ($vendor->isPrivate()) {
            $vendor->organizations()->attach($invitedBy->organization_id);
        }

        // Send invitation
        $result = $this->sendInvitation($vendor, $invitedBy);

        return [
            'success' => true,
            'vendor' => $vendor,
            'invitation_sent' => $result['success'],
            'message' => $result['success']
                ? "Vendor created and invitation sent to {$vendor->phone}"
                : "Vendor created but invitation failed: {$result['error']}",
        ];
    }

    /**
     * Send or resend invitation SMS
     */
    public function sendInvitation(Vendor $vendor, ?User $sentBy = null): array
    {
        // Check resend limits
        if ($vendor->invitation_resend_count >= self::MAX_RESENDS_PER_DAY) {
            $lastSent = $vendor->last_invitation_sent_at;
            if ($lastSent && $lastSent->isToday()) {
                return [
                    'success' => false,
                    'error' => 'Maximum resend limit reached for today. Please try again tomorrow.',
                ];
            }
            // Reset counter if last send was not today
            $vendor->invitation_resend_count = 0;
        }

        // Generate new token if expired or first time
        if (!$vendor->invitation_token || $this->isTokenExpired($vendor)) {
            $vendor->invitation_token = $this->generateInvitationToken();
            $vendor->invitation_expires_at = now()->addDays(self::INVITATION_EXPIRY_DAYS);
        }

        // Create short URL for SMS
        $fullUrl = route('vendor.setup', ['token' => $vendor->invitation_token]);
        $shortUrl = ShortUrl::shorten(
            $fullUrl,
            'vendor_invitation',
            $vendor,
            $vendor->invitation_expires_at
        );

        // Get organization name
        $orgName = $sentBy?->organization?->name ?? 'A property management company';

        // Compose SMS message
        $message = "{$orgName} has invited you to join as a service vendor. Complete your registration: {$shortUrl->short_url}";

        // Send via job (queued)
        SendVendorInvitationJob::dispatch($vendor, $message, $sentBy);

        // Update tracking fields
        $vendor->update([
            'invitation_sent_at' => now(),
            'last_invitation_sent_at' => now(),
            'invitation_resend_count' => $vendor->invitation_resend_count + 1,
        ]);

        Log::info('Vendor invitation sent', [
            'vendor_id' => $vendor->id,
            'phone' => $vendor->phone,
            'sent_by' => $sentBy?->id,
        ]);

        return [
            'success' => true,
            'short_url' => $shortUrl->short_url,
        ];
    }

    /**
     * Validate invitation token
     */
    public function validateToken(string $token): ?Vendor
    {
        $vendor = Vendor::where('invitation_token', $token)
            ->where('setup_status', 'pending_setup')
            ->first();

        if (!$vendor) {
            return null;
        }

        if ($this->isTokenExpired($vendor)) {
            return null;
        }

        return $vendor;
    }

    /**
     * Generate and send OTP for phone verification
     */
    public function sendPhoneOtp(Vendor $vendor): array
    {
        if (!$vendor->phone) {
            return [
                'success' => false,
                'error' => 'No phone number on file.',
            ];
        }

        // Generate 6-digit OTP
        $otp = $this->generateOtp();

        // Store OTP with expiry
        $vendor->update([
            'phone_verification_code' => Hash::make($otp), // Store hashed
            'phone_verification_expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ]);

        // Send OTP via SMS
        $message = "Your verification code is: {$otp}. This code expires in " . self::OTP_EXPIRY_MINUTES . " minutes.";

        try {
            $this->notificationService->sendSmsDirectly(
                $vendor->phone,
                $message
            );

            return [
                'success' => true,
                'expires_in' => self::OTP_EXPIRY_MINUTES,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send verification code. Please try again.',
            ];
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Vendor $vendor, string $otp): bool
    {
        // Check if OTP exists and not expired
        if (!$vendor->phone_verification_code || !$vendor->phone_verification_expires_at) {
            return false;
        }

        if ($vendor->phone_verification_expires_at->isPast()) {
            return false;
        }

        // Verify OTP hash
        if (!Hash::check($otp, $vendor->phone_verification_code)) {
            return false;
        }

        // Mark phone as verified
        $vendor->update([
            'phone_verified_at' => now(),
            'phone_verification_code' => null,
            'phone_verification_expires_at' => null,
        ]);

        return true;
    }

    /**
     * Complete vendor setup - create user account and activate
     */
    public function completeSetup(Vendor $vendor, array $data): array
    {
        // Validate phone is verified
        if (!$vendor->phone_verified_at) {
            return [
                'success' => false,
                'error' => 'Phone number must be verified first.',
            ];
        }

        // Check if email is already taken
        if (User::where('email', $data['email'])->exists()) {
            return [
                'success' => false,
                'error' => 'This email is already registered. Please use a different email or contact support.',
            ];
        }

        // Create user account
        $user = User::create([
            'name' => $data['name'] ?? $vendor->name,
            'email' => $data['email'] ?? $vendor->email,
            'phone' => $vendor->phone,
            'password' => Hash::make($data['password']),
            'role' => 'vendor',
            'organization_id' => $vendor->created_by_organization_id,
            'email_verified_at' => now(), // Auto-verify since phone was verified
        ]);

        // Update vendor record
        $vendor->update([
            'user_id' => $user->id,
            'name' => $data['name'] ?? $vendor->name,
            'email' => $data['email'] ?? $vendor->email,
            'setup_status' => 'active',
            'is_active' => true,
            'invitation_token' => null, // Clear token
        ]);

        // Notify the landlord who invited this vendor
        $this->notifyInviterOfCompletion($vendor);

        Log::info('Vendor setup completed', [
            'vendor_id' => $vendor->id,
            'user_id' => $user->id,
        ]);

        return [
            'success' => true,
            'user' => $user,
            'vendor' => $vendor,
        ];
    }

    /**
     * Notify the inviting landlord that vendor completed setup
     */
    protected function notifyInviterOfCompletion(Vendor $vendor): void
    {
        // Find users who should be notified (admins/managers/landlords of the creating org)
        $organization = $vendor->creator;
        if (!$organization) {
            return;
        }

        $managers = $organization->users()
            ->whereIn('role', ['admin', 'manager', 'landlord'])
            ->get();

        foreach ($managers as $manager) {
            try {
                $this->notificationService->send(
                    $manager,
                    'Vendor Registration Complete',
                    "{$vendor->name} has completed their registration and is now available for work assignments.",
                    ['email'],
                    'general',
                    $vendor
                );
            } catch (\Exception $e) {
                Log::error('Failed to notify manager of vendor completion', [
                    'error' => $e->getMessage(),
                    'manager_id' => $manager->id,
                ]);
            }
        }
    }

    /**
     * Check if invitation token is expired
     */
    protected function isTokenExpired(Vendor $vendor): bool
    {
        return !$vendor->invitation_expires_at || $vendor->invitation_expires_at->isPast();
    }

    /**
     * Generate secure invitation token
     */
    protected function generateInvitationToken(): string
    {
        return Str::random(64);
    }

    /**
     * Generate numeric OTP
     */
    protected function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Format phone number to E.164 format
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $digits = preg_replace('/[^0-9]/', '', $phone);

        // If 10 digits, assume US number
        if (strlen($digits) === 10) {
            return '+1' . $digits;
        }

        // If 11 digits starting with 1, assume US number
        if (strlen($digits) === 11 && $digits[0] === '1') {
            return '+' . $digits;
        }

        // If already has +, return as-is
        if (str_starts_with($phone, '+')) {
            return '+' . $digits;
        }

        // Default: add + prefix
        return '+' . $digits;
    }

    /**
     * Get invitation status info for display
     */
    public function getInvitationStatus(Vendor $vendor): array
    {
        if ($vendor->setup_status !== 'pending_setup') {
            return [
                'status' => $vendor->setup_status,
                'can_resend' => false,
                'message' => 'Vendor has already completed setup.',
            ];
        }

        $isExpired = $this->isTokenExpired($vendor);
        $canResend = !$isExpired || $vendor->invitation_resend_count < self::MAX_RESENDS_PER_DAY;

        return [
            'status' => 'pending_setup',
            'is_expired' => $isExpired,
            'can_resend' => $canResend,
            'resend_count' => $vendor->invitation_resend_count,
            'max_resends' => self::MAX_RESENDS_PER_DAY,
            'expires_at' => $vendor->invitation_expires_at,
            'sent_at' => $vendor->invitation_sent_at,
            'message' => $isExpired
                ? 'Invitation has expired. Resend to generate a new link.'
                : 'Invitation pending. Vendor has not completed registration.',
        ];
    }
}
