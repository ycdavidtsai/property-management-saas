<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\RegistrationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    /**
     * Display the registration view.
     */
    // public function create(): View
    // {
    //     return view('auth.register');
    // }
    public function create(): View
    {
        return view('auth.register-closed');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function store(RegisterRequest $request): RedirectResponse
    // {
    //     $user = $this->registrationService->registerWithOrganization($request->validated());

    //     event(new Registered($user));

    //     Auth::login($user);

    //     return redirect(route('dashboard', absolute: false));
    // }
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10|max:1000',
        ]);

        try {
            Mail::raw(
                "New inquiry from registration page:\n\n" .
                "Name: {$validated['name']}\n" .
                "Email: {$validated['email']}\n\n" .
                "Message:\n{$validated['message']}",
                function ($mail) use ($validated) {
                    $mail->to(config('mail.admin_email', 'admin@example.com'))
                        ->replyTo($validated['email'], $validated['name'])
                        ->subject('MyRentals - New Inquiry from ' . $validated['name']);
                }
            );

            return back()->with('success', 'Thank you! Your message has been sent. We will contact you soon.');
        } catch (\Exception $e) {
            Log::error('Failed to send contact email: ' . $e->getMessage());
            return back()->with('error', 'Sorry, there was an issue sending your message. Please try again later.');
        }
    }
}
