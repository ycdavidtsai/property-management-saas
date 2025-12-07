@component('emails.layouts.base', [
    'previewText' => 'A new user has registered: ' . $user->name,
    'headerSubtitle' => 'Admin Notification'
])

<!-- Greeting -->
<h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 20px; font-weight: 600;">
    New User Registration
</h2>

<p style="margin: 0 0 20px 0; color: #4b5563; font-size: 15px; line-height: 1.6;">
    A new user has registered on the platform and is pending email verification.
</p>

<!-- User Details -->
@include('emails.components.info-box', ['items' => [
    'Name' => $user->name,
    'Email' => $user->email,
    'Organization' => $organizationName,
    'Role' => ucfirst($user->role),
    'Registered' => $user->created_at->format('M j, Y \a\t g:i A'),
    'Email Verified' => $user->hasVerifiedEmail() ? 'Yes' : 'Pending',
]])

<!-- Alert -->
@include('emails.components.alert', [
    'type' => 'info',
    'message' => 'The user will need to verify their email address before they can access the dashboard.'
])

<!-- Action Button -->
@include('emails.components.button', [
    'url' => route('admin.users.index'),
    'text' => 'View All Users',
    'color' => 'blue'
])

<p style="margin: 30px 0 0 0; color: #6b7280; font-size: 13px;">
    This is an automated notification from your property management platform.
</p>

@endcomponent
