<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use App\Services\BroadcastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestNotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected BroadcastService $broadcastService
    ) {}

    /**
     * Test sending a single notification
     */
    public function testSingle()
    {
        $user = Auth::user();

        $this->notificationService->send(
            $user,
            'Test Notification',
            'This is a test notification sent from the system.',
            ['email'],
            'general'
        );

        return response()->json([
            'success' => true,
            'message' => 'Test notification sent to ' . $user->email
        ]);
    }

    /**
     * Test broadcast to all tenants
     */
    public function testBroadcast()
    {
        $user = Auth::user();

        $broadcast = $this->broadcastService->createBroadcast(
            $user,
            'System Test Broadcast',
            'This is a test broadcast message to all tenants in your organization.',
            ['email'],
            'all_tenants'
        );

        return response()->json([
            'success' => true,
            'message' => 'Broadcast sent to ' . $broadcast->recipient_count . ' recipients',
            'broadcast_id' => $broadcast->id
        ]);
    }

    /**
     * Preview recipients
     */
    public function previewRecipients(Request $request)
    {
        $preview = $this->broadcastService->previewRecipients(
            Auth::user()->organization_id,
            $request->input('recipient_type', 'all_tenants'),
            $request->input('filters')
        );

        return response()->json($preview);
    }

    /**
     * Test sending SMS
     */
    public function testSms()
    {
        $user = Auth::user();

        if (!$user->phone) {
            return response()->json([
                'success' => false,
                'message' => 'Your user account does not have a phone number set.'
            ]);
        }

        $this->notificationService->send(
            $user,
            'Test SMS',
            'This is a test SMS message from your Property Management system.',
            ['sms'], // Only SMS
            'general'
        );

        return response()->json([
            'success' => true,
            'message' => 'Test SMS sent to ' . $user->phone
        ]);
    }

}
