{{-- Email Info Box Component --}}
{{-- Usage: @include('emails.components.info-box', ['items' => ['Label' => 'Value', ...]]) --}}

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0; background-color: #f8fafc; border-radius: 8px; border: 1px solid #e5e7eb;">
    @foreach($items as $label => $value)
        <tr>
            <td style="padding: 12px 16px; {{ !$loop->last ? 'border-bottom: 1px solid #e5e7eb;' : '' }}">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td width="40%" style="color: #6b7280; font-size: 13px; font-weight: 500;">
                            {{ $label }}
                        </td>
                        <td width="60%" style="color: #1f2937; font-size: 14px; font-weight: 600; text-align: right;">
                            {{ $value }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @endforeach
</table>
