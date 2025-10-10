<?php

namespace App\Http\Controllers;

use App\Models\VendorPromotionRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display promotion requests
     */
    public function promotionRequests()
    {
        $pendingRequests = VendorPromotionRequest::with(['vendor', 'requestedBy'])
            ->pending()
            ->orderBy('requested_at', 'desc')
            ->get();

        $reviewedRequests = VendorPromotionRequest::with(['vendor', 'requestedBy', 'reviewedBy'])
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('reviewed_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.promotion-requests', compact('pendingRequests', 'reviewedRequests'));
    }

    /**
     * Approve promotion request
     */
    public function approvePromotion(Request $request, VendorPromotionRequest $promotionRequest)
    {
        if (!$promotionRequest->isPending()) {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:1000',
        ]);

        // Update promotion request
        $promotionRequest->update([
            'status' => 'approved',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'] ?? null,
            'payment_completed_at' => now(), // Auto-complete since it's free
        ]);

        // Promote vendor to global
        $promotionRequest->vendor->update([
            'vendor_type' => 'global',
            'promoted_at' => now(),
            'promotion_fee_paid' => 0,
        ]);

        // TODO: Send notification to vendor

        return back()->with('success', 'Vendor promoted to global listing successfully.');
    }

    /**
     * Reject promotion request
     */
    public function rejectPromotion(Request $request, VendorPromotionRequest $promotionRequest)
    {
        if (!$promotionRequest->isPending()) {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'review_notes' => 'required|string|max:1000',
        ]);

        // Update promotion request
        $promotionRequest->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'],
        ]);

        // TODO: Send notification to vendor

        return back()->with('success', 'Promotion request rejected.');
    }
}
