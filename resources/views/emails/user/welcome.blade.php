@component('emails.layouts.base', [
    'previewText' => 'Welcome aboard! Your account is ready.',
    'headerSubtitle' => 'Welcome to the Platform'
])

<!-- Greeting -->
<h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 20px; font-weight: 600;">
    Welcome, {{ $user->name }}! 🎉
</h2>

<p style="margin: 0 0 20px 0; color: #4b5563; font-size: 15px; line-height: 1.6;">
    Thank you for joining {{ config('app.name') }}. Your email has been verified and your account is now fully activated.
</p>

<p style="margin: 0 0 25px 0; color: #4b5563; font-size: 15px; line-height: 1.6;">
    You can now access all the features of your property management dashboard.
</p>

<!-- Quick Start Section -->
<div style="margin: 30px 0; padding: 25px; background-color: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;">
    <h3 style="margin: 0 0 15px 0; color: #0c4a6e; font-size: 16px; font-weight: 600;">
        🚀 Quick Start Guide
    </h3>
    <ul style="margin: 0; padding: 0 0 0 20px; color: #0369a1; font-size: 14px; line-height: 1.8;">
        <li>Add your first property</li>
        <li>Create units for your property</li>
        <li>Add tenants and create leases</li>
        <li>Set up maintenance categories</li>
        <li>Invite vendors to handle maintenance</li>
    </ul>
</div>

<!-- Get Started Button -->
@include('emails.components.button', [
    'url' => route('dashboard'),
    'text' => 'Go to Dashboard',
    'color' => 'blue'
])

<!-- Support Section -->
<div style="margin: 30px 0 0 0; padding-top: 25px; border-top: 1px solid #e5e7eb;">
    <p style="margin: 0 0 10px 0; color: #4b5563; font-size: 14px; font-weight: 600;">
        Need Help?
    </p>
    <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
        If you have any questions or need assistance getting started, don't hesitate to reach out to our support team.
    </p>
</div>

@endcomponent
