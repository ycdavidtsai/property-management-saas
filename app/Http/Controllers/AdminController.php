<?php

namespace App\Http\Controllers;

use App\Models\BroadcastMessage;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Organization;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorPromotionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Admin Dashboard - Platform Overview
     */
    public function dashboard()
    {
        // Organization metrics
        $orgMetrics = [
            'total' => Organization::count(),
            'active' => Organization::where('subscription_status', 'active')->count(),
            'trial' => Organization::where('subscription_status', 'trialing')
                        ->orWhereNotNull('trial_ends_at')
                        ->where('trial_ends_at', '>', now())
                        ->count(),
            'inactive' => Organization::where('subscription_status', 'inactive')->count(),
        ];

        // User metrics by role
        $usersByRole = User::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        $userMetrics = [
            'total' => User::count(),
            'admins' => $usersByRole['admin'] ?? 0,
            'managers' => $usersByRole['manager'] ?? 0,
            'landlords' => $usersByRole['landlord'] ?? 0,
            'tenants' => $usersByRole['tenant'] ?? 0,
            'vendors' => $usersByRole['vendor'] ?? 0,
        ];

        // Property metrics
        $propertyMetrics = [
            'total_properties' => Property::count(),
            'total_units' => Unit::count(),
            'occupied_units' => Unit::where('status', 'occupied')->count(),
            'vacant_units' => Unit::where('status', 'vacant')->count(),
        ];

        // Lease metrics
        $leaseMetrics = [
            'active_leases' => Lease::where('status', 'active')->count(),
            'expiring_soon' => Lease::where('status', 'active')
                ->where('end_date', '<=', now()->addDays(30))
                ->where('end_date', '>', now())
                ->count(),
        ];

        // Maintenance metrics
        $maintenanceMetrics = [
            'open' => MaintenanceRequest::where('status', 'open')->count(),
            'in_progress' => MaintenanceRequest::whereIn('status', ['assigned', 'in_progress', 'pending_acceptance'])->count(),
            'completed_this_month' => MaintenanceRequest::where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count(),
        ];

        // Vendor metrics
        $vendorMetrics = [
            'total' => Vendor::count(),
            'global' => Vendor::where('vendor_type', 'global')->count(),
            'private' => Vendor::where('vendor_type', 'private')->count(),
            'pending_promotions' => VendorPromotionRequest::pending()->count(),
        ];

        // SMS/Communication metrics (this month)
        $smsMetrics = [
            'broadcasts_this_month' => BroadcastMessage::whereMonth('sent_at', now()->month)
                ->whereYear('sent_at', now()->year)
                ->count(),
            'sms_segments_this_month' => BroadcastMessage::whereMonth('sent_at', now()->month)
                ->whereYear('sent_at', now()->year)
                ->sum('sms_segments_total') ?? 0,
            'emails_sent_this_month' => BroadcastMessage::whereMonth('sent_at', now()->month)
                ->whereYear('sent_at', now()->year)
                ->sum('emails_sent') ?? 0,
        ];

        // System health
        $systemHealth = [
            'failed_jobs' => DB::table('failed_jobs')->count(),
            'queued_jobs' => DB::table('jobs')->count(),
        ];

        // Recent activity (placeholder - will be enhanced later)
        $recentActivity = $this->getRecentActivity();

        // Recent organizations
        $recentOrganizations = Organization::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'orgMetrics',
            'userMetrics',
            'propertyMetrics',
            'leaseMetrics',
            'maintenanceMetrics',
            'vendorMetrics',
            'smsMetrics',
            'systemHealth',
            'recentActivity',
            'recentOrganizations'
        ));
    }

    /**
     * List all organizations
     */
    public function organizations()
    {
        return view('admin.organizations.index');
    }

    /**
     * Show organization details
     */
    public function showOrganization(Organization $organization)
    {
        // Load related data
        $organization->loadCount(['users', 'properties']);

        $stats = [
            'users' => $organization->users()->count(),
            'properties' => $organization->properties()->count(),
            'units' => Unit::whereHas('property', fn($q) => $q->where('organization_id', $organization->id))->count(),
            'active_leases' => Lease::whereHas('unit.property', fn($q) => $q->where('organization_id', $organization->id))
                ->where('status', 'active')
                ->count(),
            'maintenance_requests' => MaintenanceRequest::where('organization_id', $organization->id)->count(),
            'open_maintenance' => MaintenanceRequest::where('organization_id', $organization->id)
                ->whereIn('status', ['open', 'assigned', 'in_progress'])
                ->count(),
        ];

        $users = $organization->users()
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        $properties = $organization->properties()
            ->withCount('units')
            ->get();

        return view('admin.organizations.show', compact('organization', 'stats', 'users', 'properties'));
    }

    /**
     * Toggle organization active status
     */
    public function toggleOrganization(Request $request, Organization $organization)
    {
        $newStatus = $organization->subscription_status === 'active' ? 'inactive' : 'active';

        $organization->update([
            'subscription_status' => $newStatus
        ]);

        return back()->with('success', "Organization {$organization->name} has been " . ($newStatus === 'active' ? 'activated' : 'deactivated') . ".");
    }

    /**
     * List all users across organizations
     */
    public function users()
    {
        return view('admin.users.index');
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user->load('organization');

        $stats = [];

        if ($user->role === 'tenant') {
            $stats['active_lease'] = $user->activeLease();
            $stats['maintenance_requests'] = $user->maintenanceRequests()->count();
        } elseif ($user->role === 'vendor') {
            $stats['vendor_profile'] = $user->vendor;
            $stats['assigned_requests'] = MaintenanceRequest::where('assigned_vendor_id', $user->vendor?->id)->count();
            $stats['completed_requests'] = MaintenanceRequest::where('assigned_vendor_id', $user->vendor?->id)
                ->where('status', 'completed')
                ->count();
        } elseif (in_array($user->role, ['admin', 'manager', 'landlord'])) {
            if ($user->organization) {
                $stats['org_properties'] = Property::where('organization_id', $user->organization_id)->count();
                $stats['org_tenants'] = User::where('organization_id', $user->organization_id)
                    ->where('role', 'tenant')
                    ->count();
            }
        }

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Toggle user active status
     */
    public function toggleUser(Request $request, User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return back()->with('success', "User {$user->name} has been " . ($user->is_active ? 'activated' : 'deactivated') . ".");
    }

    /**
     * Vendor oversight page
     */
    public function vendors()
    {
        return view('admin.vendors.index');
    }

    /**
     * System health monitoring
     */
    public function system()
    {
        $failedJobs = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->limit(50)
            ->get();

        $queuedJobs = DB::table('jobs')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
        ];

        // Database stats
        $dbStats = [
            'total_organizations' => Organization::count(),
            'total_users' => User::count(),
            'total_properties' => Property::count(),
            'total_units' => Unit::count(),
            'total_leases' => Lease::count(),
            'total_maintenance' => MaintenanceRequest::count(),
            'total_broadcasts' => BroadcastMessage::count(),
        ];

        return view('admin.system.index', compact('failedJobs', 'queuedJobs', 'systemInfo', 'dbStats'));
    }

    /**
     * Retry a failed job
     */
    public function retryJob(Request $request, $jobId)
    {
        $job = DB::table('failed_jobs')->where('id', $jobId)->first();

        if (!$job) {
            return back()->with('error', 'Failed job not found.');
        }

        // Use artisan to retry
        \Artisan::call('queue:retry', ['id' => [$job->uuid]]);

        return back()->with('success', 'Job has been queued for retry.');
    }

    /**
     * Delete a failed job
     */
    public function deleteJob(Request $request, $jobId)
    {
        DB::table('failed_jobs')->where('id', $jobId)->delete();

        return back()->with('success', 'Failed job has been deleted.');
    }

    /**
     * Flush all failed jobs
     */
    public function flushJobs(Request $request)
    {
        DB::table('failed_jobs')->truncate();

        return back()->with('success', 'All failed jobs have been deleted.');
    }

    // =====================
    // Existing Promotion Methods (keep these)
    // =====================

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

        $promotionRequest->update([
            'status' => 'approved',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'] ?? null,
            'payment_completed_at' => now(),
            'request_type' => 'admin_approved', //DT
        ]);

        $promotionRequest->vendor->update([
            'vendor_type' => 'global',
            'promoted_at' => now(),
            'promotion_fee_paid' => 0,
            'setup_status' => 'active', //DT
        ]);

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

        $promotionRequest->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'],
        ]);

        return back()->with('success', 'Promotion request rejected.');
    }

    // =====================
    // Helper Methods
    // =====================

    /**
     * Get recent platform activity (placeholder)
     */
    protected function getRecentActivity(): array
    {
        $activities = [];

        // Recent organization registrations
        $recentOrgs = Organization::latest()->take(3)->get();
        foreach ($recentOrgs as $org) {
            $activities[] = [
                'type' => 'organization',
                'icon' => 'building',
                'color' => 'blue',
                'message' => "New organization \"{$org->name}\" registered",
                'time' => $org->created_at,
            ];
        }

        // Recent vendor promotions
        $recentPromotions = VendorPromotionRequest::where('status', 'approved')
            ->with('vendor')
            ->latest('reviewed_at')
            ->take(3)
            ->get();
        foreach ($recentPromotions as $promo) {
            $activities[] = [
                'type' => 'promotion',
                'icon' => 'badge-check',
                'color' => 'green',
                'message' => "Vendor \"{$promo->vendor->name}\" promoted to global",
                'time' => $promo->reviewed_at,
            ];
        }

        // Recent maintenance completions
        $recentMaintenance = MaintenanceRequest::where('status', 'completed')
            ->latest('completed_at')
            ->take(3)
            ->get();
        foreach ($recentMaintenance as $mr) {
            $activities[] = [
                'type' => 'maintenance',
                'icon' => 'wrench',
                'color' => 'yellow',
                'message' => "Maintenance request completed",
                'time' => $mr->completed_at,
            ];
        }

        // Sort by time descending and take top 10
        usort($activities, fn($a, $b) => $b['time'] <=> $a['time']);

        return array_slice($activities, 0, 10);
    }
}
