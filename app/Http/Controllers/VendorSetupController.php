<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\ShortUrl;
use App\Services\VendorInvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorSetupController extends Controller
{
    protected VendorInvitationService $invitationService;

    public function __construct(VendorInvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    /**
     * Handle short URL redirect
     */
    public function handleShortUrl(string $code)
    {
        $shortUrl = ShortUrl::findValidByCode($code);

        if (!$shortUrl) {
            return view('vendor-setup.expired', [
                'message' => 'This link has expired or is invalid.',
            ]);
        }

        // Record the click
        $shortUrl->recordClick();

        // Redirect to the actual URL
        return redirect($shortUrl->url);
    }

    /**
     * Display vendor setup form
     */
    public function show(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return view('vendor-setup.expired', [
                'message' => 'Invalid invitation link.',
            ]);
        }

        $vendor = $this->invitationService->validateToken($token);

        if (!$vendor) {
            return view('vendor-setup.expired', [
                'message' => 'This invitation has expired or has already been used. Please contact the property manager for a new invitation.',
            ]);
        }

        // Get the organization name
        $organizationName = $vendor->creator?->name ?? 'Property Management';

        return view('vendor-setup.setup', [
            'vendor' => $vendor,
            'token' => $token,
            'organizationName' => $organizationName,
            'step' => 'verify_phone', // Initial step
        ]);
    }

    /**
     * Send OTP for phone verification
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $vendor = $this->invitationService->validateToken($request->token);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired invitation.',
            ], 400);
        }

        $result = $this->invitationService->sendPhoneOtp($vendor);

        return response()->json($result);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $vendor = $this->invitationService->validateToken($request->token);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired invitation.',
            ], 400);
        }

        $verified = $this->invitationService->verifyOtp($vendor, $request->otp);

        if (!$verified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code. Please try again.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Phone verified successfully!',
        ]);
    }

    /**
     * Complete vendor setup
     */
    public function complete(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $vendor = $this->invitationService->validateToken($request->token);

        if (!$vendor) {
            return back()->withErrors(['token' => 'Invalid or expired invitation.']);
        }

        // Check phone is verified
        if (!$vendor->phone_verified_at) {
            return back()->withErrors(['otp' => 'Please verify your phone number first.']);
        }

        $result = $this->invitationService->completeSetup($vendor, [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if (!$result['success']) {
            return back()->withErrors(['email' => $result['error']]);
        }

        // Log in the user
        Auth::login($result['user']);

        return redirect()->route('vendor.dashboard')
            ->with('success', 'Welcome! Your account has been created successfully.');
    }
}
