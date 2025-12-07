{{-- Email Alert Component --}}
{{-- Usage: @include('emails.components.alert', ['type' => 'info', 'message' => '...']) --}}

@php
    $styles = [
        'info' => ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'text' => '#1e40af', 'icon' => 'ℹ️'],
        'success' => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#166534', 'icon' => '✓'],
        'warning' => ['bg' => '#fffbeb', 'border' => '#fde68a', 'text' => '#92400e', 'icon' => '⚠️'],
        'error' => ['bg' => '#fef2f2', 'border' => '#fecaca', 'text' => '#991b1b', 'icon' => '✕'],
    ];
    $style = $styles[$type ?? 'info'];
@endphp

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0;">
    <tr>
        <td style="background-color: {{ $style['bg'] }}; border: 1px solid {{ $style['border'] }}; border-radius: 8px; padding: 16px 20px;">
            <p style="margin: 0; color: {{ $style['text'] }}; font-size: 14px; line-height: 1.5;">
                {{ $message }}
            </p>
        </td>
    </tr>
</table>
