@component('emails.layouts.base', [
    'previewText' => 'New inquiry from ' . $name,
    'headerSubtitle' => 'Website Inquiry'
])

<!-- Greeting -->
<h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 20px; font-weight: 600;">
    New Contact Inquiry
</h2>

<p style="margin: 0 0 20px 0; color: #4b5563; font-size: 15px; line-height: 1.6;">
    You've received a new inquiry from the registration page.
</p>

<!-- Contact Details -->
@include('emails.components.info-box', ['items' => [
    'Name' => $name,
    'Email' => $email,
    'Received' => now()->format('M j, Y \a\t g:i A'),
]])

<!-- Message -->
<div style="margin: 25px 0; padding: 20px; background-color: #f9fafb; border-left: 4px solid #3b82f6; border-radius: 0 8px 8px 0;">
    <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
        Message
    </p>
    <p style="margin: 0; color: #1f2937; font-size: 15px; line-height: 1.7; white-space: pre-wrap;">{{ $messageContent }}</p>
</div>

<!-- Reply Button -->
@include('emails.components.button', [
    'url' => 'mailto:' . $email . '?subject=Re: Your inquiry about ' . config('app.name'),
    'text' => 'Reply to ' . $name,
    'color' => 'blue'
])

<p style="margin: 30px 0 0 0; color: #6b7280; font-size: 13px;">
    You can reply directly to this email - it will be sent to {{ $email }}.
</p>

@endcomponent
