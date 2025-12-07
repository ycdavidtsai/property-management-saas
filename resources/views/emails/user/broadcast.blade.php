@component('emails.layouts.base', [
    'previewText' => $previewText ?? '',
    'headerSubtitle' => 'Message from ' . ($organizationName ?? config('app.name'))
])

<!-- Greeting -->
<h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 20px; font-weight: 600;">
    {{ $emailSubject ?? $title ?? 'Message' }}
</h2>

@if(isset($sender) && $sender)
<p style="margin: 0 0 15px 0; color: #6b7280; font-size: 13px;">
    From: {{ $sender->name }} at {{ $organizationName ?? config('app.name') }}
</p>
@endif

<!-- Message Content -->
<div style="margin: 20px 0; color: #374151; font-size: 15px; line-height: 1.7;">
    {!! nl2br(e($messageContent)) !!}
</div>

<!-- Divider -->
<hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">

<!-- Footer Note -->
<p style="margin: 0; color: #9ca3af; font-size: 12px; line-height: 1.6;">
    This message was sent to you because you are a tenant at {{ $organizationName ?? config('app.name') }}. 
    If you have questions, please contact your property manager.
</p>

@endcomponent
