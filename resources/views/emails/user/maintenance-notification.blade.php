@component('emails.layouts.base', [
    'previewText' => $previewText,
    'headerSubtitle' => 'Maintenance Update'
])

<!-- Status Badge -->
<div style="margin-bottom: 25px;">
    <span style="display: inline-block; padding: 6px 14px; background-color: {{ $statusColor }}; color: #ffffff; font-size: 12px; font-weight: 600; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
        {{ ucfirst(str_replace('_', ' ', $event)) }}
    </span>
</div>

<!-- Greeting -->
<h2 style="margin: 0 0 15px 0; color: #1f2937; font-size: 20px; font-weight: 600;">
    {{ $emailSubject }}
</h2>

<p style="margin: 0 0 25px 0; color: #4b5563; font-size: 15px; line-height: 1.6;">
    Hi {{ $recipient->name }}, {{ strtolower($statusMessage) }}
</p>

<!-- Request Details -->
@include('emails.components.info-box', ['items' => [
    'Request ID' => '#' . substr($maintenanceRequest->id, 0, 8),
    'Category' => ucfirst($maintenanceRequest->category ?? 'General'),
    'Priority' => ucfirst($maintenanceRequest->priority ?? 'Normal'),
    'Property' => $maintenanceRequest->unit?->property?->name ?? 'N/A',
    'Unit' => $maintenanceRequest->unit?->unit_number ?? 'N/A',
    'Submitted' => $maintenanceRequest->created_at->format('M j, Y'),
]])

<!-- Description -->
<div style="margin: 25px 0; padding: 20px; background-color: #f9fafb; border-radius: 8px;">
    <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
        Issue Description
    </p>
    <p style="margin: 0; color: #374151; font-size: 14px; line-height: 1.6;">
        {{ $maintenanceRequest->description }}
    </p>
</div>

@if($event === 'assigned' && $maintenanceRequest->assignedVendor)
<!-- Vendor Info -->
<div style="margin: 25px 0; padding: 20px; background-color: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
    <p style="margin: 0 0 8px 0; color: #166534; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
        Assigned Vendor
    </p>
    <p style="margin: 0 0 5px 0; color: #15803d; font-size: 16px; font-weight: 600;">
        {{ $maintenanceRequest->assignedVendor->company_name }}
    </p>
    @if($maintenanceRequest->scheduled_date)
    <p style="margin: 0; color: #166534; font-size: 14px;">
        Scheduled: {{ $maintenanceRequest->scheduled_date->format('l, M j, Y') }}
    </p>
    @endif
</div>
@endif

@if($event === 'completed')
<!-- Completion Info -->
@include('emails.components.alert', [
    'type' => 'success',
    'message' => 'Your maintenance request has been resolved. If you have any concerns about the work performed, please contact your property manager.'
])
@endif

<!-- View Request Button -->
@include('emails.components.button', [
    'url' => route('maintenance-requests.show', $maintenanceRequest),
    'text' => 'View Request Details',
    'color' => 'blue'
])

<p style="margin: 30px 0 0 0; color: #9ca3af; font-size: 12px;">
    If you have questions about this maintenance request, please contact your property manager.
</p>

@endcomponent
