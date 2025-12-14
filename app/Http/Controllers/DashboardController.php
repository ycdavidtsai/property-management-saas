<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Vendor;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->middleware(['auth', 'verified']);
        $this->roleService = $roleService;
    }

    public function index()
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;

        // Tenant gets redirected to their portal
        if ($this->roleService->isTenant($user)) {
            return redirect()->route('tenant.portal');
        }

        //Vendor portal
        if ($user->role === 'vendor') {
            return redirect()->route('vendor.dashboard');
        }

        // Get metrics for management users
        $metrics = [
            'properties' => [
                'total' => Property::where('organization_id', $organizationId)->count(),
                // 'active' => Property::where('organization_id', $organizationId)
                //     ->where('status', 'active')->count(),
            ],
            'units' => [
                'total' => Unit::whereHas('property', function($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                })->count(),
                'occupied' => Unit::whereHas('property', function($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                })->whereHas('leases', function($q) {
                    $q->where('status', 'active');
                })->count(),
            ],
            'leases' => [
                'active' => Lease::where('organization_id', $organizationId)
                    ->where('status', 'active')->count(),
                'expiring_soon' => Lease::where('organization_id', $organizationId)
                    ->where('status', 'active')
                    ->whereBetween('end_date', [now(), now()->addDays(30)])
                    ->count(),
                'expired' => Lease::where('organization_id', $organizationId)
                    ->where('status', 'expired')->count(),
            ],
            'tenants' => [
                'total' => User::where('organization_id', $organizationId)
                    ->where('role', 'tenant')
                    ->count(),
            ],
            'maintenance' => [
                'total' => MaintenanceRequest::where('organization_id', $organizationId)->count(),
                'submitted' => MaintenanceRequest::where('organization_id', $organizationId)
                    ->where('status', 'submitted')->count(),
                'assigned' => MaintenanceRequest::where('organization_id', $organizationId)
                    ->where('status', 'assigned')->count(),
                'in_progress' => MaintenanceRequest::where('organization_id', $organizationId)
                    ->where('status', 'in_progress')->count(),
                'completed' => MaintenanceRequest::where('organization_id', $organizationId)
                    ->where('status', 'completed')->count(),
            ],
            'vendors' => [
                // 'total' => Vendor::where('organization_id', $organizationId)->count(),
                // 'active' => Vendor::where('organization_id', $organizationId)
                //     ->where('is_active', true)->count(),
                'total' => Vendor::whereHas('organizations', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                })->count(),
                'active' => Vendor::whereHas('organizations', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId)
                      ->where('organization_vendor.is_active', true);
                })->count(),
            ],
        ];

        // Calculate occupancy rate
        $metrics['units']['occupancy_rate'] = $metrics['units']['total'] > 0
            ? round(($metrics['units']['occupied'] / $metrics['units']['total']) * 100, 1)
            : 0;

        // Get recent maintenance requests
        $recentMaintenanceRequests = MaintenanceRequest::where('organization_id', $organizationId)
            ->with(['property', 'unit', 'tenant'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('metrics', 'recentMaintenanceRequests'));
    }

    public function home(): View
    {
        return view('welcome');
    }
}



