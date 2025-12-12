<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Twilio SMS status webhook
     */
    public function twilioStatus(Request $request)
    {
        // Log the webhook data
        Log::info('Twilio webhook received', $request->all());

        $messageSid = $request->input('MessageSid');
        $status = $request->input('MessageStatus'); // queued, sent, delivered, failed, etc.

        if (!$messageSid) {
            return response()->json(['error' => 'Missing MessageSid'], 400);
        }

        // Find notification by provider_id (Twilio SID)
        $notification = Notification::where('provider_id', $messageSid)->first();

        if (!$notification) {
            Log::warning('Notification not found for Twilio webhook', [
                'message_sid' => $messageSid
            ]);
            //return response()->json(['message' => 'Notification not found'], 404);
            // When Twilio calls back with the status update, your controller looks for a Notification record with that SID. It doesn't find one (because one was never created), so it returns 404. Twilio sees the 404 and flags it as an error (11200).
            // Return 200 OK so Twilio stops complaining/retrying
            return response()->json(['message' => 'Notification not found, but webhook received'], 200);
        }

        // Update notification based on status
        $updateData = [
            'provider_response' => array_merge(
                $notification->provider_response ?? [],
                [
                    'last_webhook_status' => $status,
                    'last_webhook_at' => now()->toDateTimeString(),
                ]
            )
        ];

        // Map Twilio status to our status
        switch ($status) {
            case 'delivered':
                $updateData['status'] = 'delivered';
                $updateData['delivered_at'] = now();
                break;

            case 'failed':
            case 'undelivered':
                $updateData['status'] = 'failed';
                $updateData['error_message'] = $request->input('ErrorMessage', 'Delivery failed');
                break;

            case 'sent':
                // Keep as 'sent', don't change to delivered yet
                break;
        }

        $notification->update($updateData);

        Log::info('Notification updated from Twilio webhook', [
            'notification_id' => $notification->id,
            'status' => $status
        ]);

        return response()->json(['message' => 'Webhook processed'], 200);
    }
}
