@component('emails.layouts.base', [
    'previewText' => $previewText ?? '',
    'headerSubtitle' => 'Notification'
])

<!-- Subject as Title -->
<h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 20px; font-weight: 600;">
    {{ $emailSubject ?? $subject ?? 'Notification' }}
</h2>

<!-- Message Content -->
<div style="margin: 20px 0; color: #374151; font-size: 15px; line-height: 1.7;">
    {!! nl2br(e($messageContent ?? $content ?? '')) !!}
</div>

<!-- Divider -->
<hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">

<!-- Footer Note -->
<p style="margin: 0; color: #9ca3af; font-size: 12px; line-height: 1.6;">
    This is an automated notification from {{ config('app.name') }}.
</p>

@endcomponent
