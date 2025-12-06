<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\RegistrationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    /**
     * Check if registration is open
     */
    protected function isRegistrationOpen(): bool
    {
        return config('app.registration_open', false);
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        if (!$this->isRegistrationOpen()) {
            return view('auth.register-closed');
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // If registration is closed, handle as contact form
        if (!$this->isRegistrationOpen()) {
            return $this->handleContactForm($request);
        }

        // Normal registration flow
        return $this->handleRegistration($request);
    }

    /**
     * Handle contact form submission (when registration is closed)
     */
    protected function handleContactForm(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10|max:1000',
        ]);

        $adminEmail = config('mail.admin_email');

        if (!$adminEmail) {
            Log::warning('Admin email not configured for contact form.');
            return back()->with('error', 'Sorry, there was an issue. Please try again later.');
        }

        try {
            Mail::raw(
                "New Inquiry from Registration Page\n" .
                "===================================\n\n" .
                "Name: {$validated['name']}\n" .
                "Email: {$validated['email']}\n\n" .
                "Message:\n{$validated['message']}\n\n" .
                "---\n" .
                "Sent: " . now()->format('M j, Y \a\t g:i A'),
                function ($mail) use ($adminEmail, $validated) {
                    $mail->to($adminEmail)
                         ->replyTo($validated['email'], $validated['name'])
                         ->subject('MyRentals Inquiry: ' . $validated['name']);
                }
            );

            return back()->with('success', 'Thank you! Your message has been sent. We will contact you soon.');

        } catch (\Exception $e) {
            Log::error('Failed to send contact email: ' . $e->getMessage());
            return back()->with('error', 'Sorry, there was an issue sending your message. Please try again later.');
        }
    }

    /**
     * Handle normal registration (when registration is open)
     */
    protected function handleRegistration(Request $request): RedirectResponse
    {
        // Use the custom RegisterRequest for validation
        $validated = app(RegisterRequest::class)->validated();

        $user = $this->registrationService->registerWithOrganization($validated);

        // Notify admin of new registration
        $this->notifyAdminOfNewRegistration($user, $validated);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to verification notice
        return redirect()->route('verification.notice');
    }

    /**
     * Send notification to admin about new registration
     */
    protected function notifyAdminOfNewRegistration($user, array $registrationData): void
    {
        $adminEmail = config('mail.admin_email');
        
        if (!$adminEmail) {
            Log::warning('Admin email not configured. Skipping new registration notification.');
            return;
        }

        try {
            $organizationName = $registrationData['organization_name'] ?? 'N/A';
            
            Mail::raw(
                "New User Registration\n" .
                "=====================\n\n" .
                "A new user has registered on the platform:\n\n" .
                "Name: {$user->name}\n" .
                "Email: {$user->email}\n" .
                "Organization: {$organizationName}\n" .
                "Role: {$user->role}\n" .
                "Registered: " . now()->format('M j, Y \a\t g:i A') . "\n\n" .
                "Email Verified: " . ($user->hasVerifiedEmail() ? 'Yes' : 'Pending') . "\n\n" .
                "---\n" .
                "View all users: " . route('admin.users.index'),
                function ($mail) use ($adminEmail, $user) {
                    $mail->to($adminEmail)
                         ->subject('New Registration: ' . $user->name . ' (' . $user->email . ')');
                }
            );

            Log::info('Admin notified of new registration', ['user_id' => $user->id, 'email' => $user->email]);

        } catch (\Exception $e) {
            Log::error('Failed to notify admin of new registration: ' . $e->getMessage());
        }
    }
}