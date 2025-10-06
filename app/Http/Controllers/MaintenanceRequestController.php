<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Vendor;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceRequestController extends Controller
{
    public function __construct()
    {
        // Use the full class name instead of alias
        $this->middleware(['auth', \App\Http\Middleware\EnsureOrganizationAccess::class]);
    }

    public function index()
    {
        $user = Auth::user();
        $roleService = app(RoleService::class);

        // Different views based on role
        if ($roleService->isTenant($user)) {
            $requests = MaintenanceRequest::where('organization_id', $user->organization_id)
                ->where('tenant_id', $user->id)
                ->latest()
                ->get();
        } elseif ($roleService->isVendor($user)) {
            abort(403, 'Unauthorized access.'); //DT added to prevent vendor from accessing this route
        } else {
            $requests = MaintenanceRequest::where('organization_id', $user->organization_id)
                ->latest()
                ->get();
        }

        return view('maintenance-requests.index', compact('requests'));
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('view', $maintenanceRequest);

        $maintenanceRequest->load([
            'property',
            'unit',
            'tenant',
            //'assignedVendor',
            'assignedBy',
            //'updates.user'
        ]);

        return view('maintenance-requests.show', [ 'request' => $maintenanceRequest ]);
    }

    public function create()
    {
        $user = Auth::user();
        $roleService = app(RoleService::class);

        // Check if user can create maintenance requests
        $this->authorize('create', MaintenanceRequest::class);

        // Tenants can only create for their units
        if ($roleService->isTenant($user)) {
            // Get properties where this tenant has active leases
            // Step 1: Get all active lease IDs for this tenant
            $activeLeasesForTenant = DB::table('lease_tenant')
                ->join('leases', 'lease_tenant.lease_id', '=', 'leases.id')
                ->where('lease_tenant.tenant_id', $user->id)
                ->where('leases.status', 'active')
                ->pluck('leases.unit_id'); // Get the unit IDs from active leases

            // Step 2: Get properties that have those units
            $properties = Property::where('organization_id', $user->organization_id)
                ->whereHas('units', function ($query) use ($activeLeasesForTenant) {
                    $query->whereIn('id', $activeLeasesForTenant);
                })
                ->with(['units' => function ($query) use ($activeLeasesForTenant) {
                    // Only load units where the tenant has active leases
                    $query->whereIn('id', $activeLeasesForTenant);
                }])
                ->get();
        } else {
            // Management users can see all properties
            $properties = Property::where('organization_id', $user->organization_id)
                ->with('units')
                ->get();
        }

        return view('maintenance-requests.create', compact('properties'));
    }

    public function edit(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('update', $maintenanceRequest);

        $maintenanceRequest->load(['property', 'unit']);

        $vendors = Vendor::where('organization_id', $maintenanceRequest->organization_id)
            ->where('is_active', true)
            ->get();

        return view('maintenance-requests.edit', [
            'request' => $maintenanceRequest,
            'vendors' => $vendors
        ]);
    }
}
