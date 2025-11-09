<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->subject }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            margin: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
            background-color: #ffffff;
        }
        .content p {
            margin: 0 0 15px 0;
            white-space: pre-line;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            font-size: 12px;
            color: #6b7280;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            margin-top: 10px;
        }
        .badge-maintenance {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-broadcast {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-general {
            background-color: #e5e7eb;
            color: #374151;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
            }
            .header h1 {
                font-size: 20px;
            }
            .content {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            @if($notification->channel)
                <span class="badge badge-{{ $notification->channel }}">
                    {{ ucfirst($notification->channel) }}
                </span>
            @endif
        </div>

        <div class="content">
            <p>{!! nl2br(e($emailContent)) !!}</p>

            @if($notification->notifiable_type === 'App\Models\MaintenanceRequest' && $notification->notifiable)
                <div style="margin-top: 20px; padding: 15px; background-color: #f9fafb; border-radius: 6px; border-left: 4px solid #667eea;">
                    <p style="margin: 0; font-weight: 600; color: #374151;">Request Details:</p>
                    <p style="margin: 5px 0 0 0; font-size: 14px; color: #6b7280;">
                        <strong>ID:</strong> {{ substr($notification->notifiable->id, 0, 8) }}<br>
                        <strong>Category:</strong> {{ ucfirst($notification->notifiable->category ?? 'N/A') }}<br>
                        <strong>Priority:</strong> {{ ucfirst($notification->notifiable->priority ?? 'N/A') }}<br>
                        <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $notification->notifiable->status ?? 'N/A')) }}
                    </p>
                </div>
            @endif
        </div>

        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p style="margin-top: 15px; color: #9ca3af;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
