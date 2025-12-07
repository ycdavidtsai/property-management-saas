{{-- Email Button Component --}}
{{-- Usage: @include('emails.components.button', ['url' => '...', 'text' => '...', 'color' => 'blue']) --}}

@php
    $colors = [
        'blue' => ['bg' => '#3b82f6', 'hover' => '#2563eb'],
        'green' => ['bg' => '#10b981', 'hover' => '#059669'],
        'red' => ['bg' => '#ef4444', 'hover' => '#dc2626'],
        'gray' => ['bg' => '#6b7280', 'hover' => '#4b5563'],
    ];
    $colorScheme = $colors[$color ?? 'blue'];
@endphp

<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 25px 0;">
    <tr>
        <td style="border-radius: 6px; background-color: {{ $colorScheme['bg'] }};">
            <a href="{{ $url }}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 6px;">
                {{ $text }}
            </a>
        </td>
    </tr>
</table>
