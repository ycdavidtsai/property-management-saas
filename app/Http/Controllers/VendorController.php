<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MaintenanceRequest;


class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of vendors
     */
    public function index()
    {
        $this->authorize('viewAny', Vendor::class);

        return view('vendors.index');
    }

    /**
     * Show the form for creating a new vendor
     */
    public function create()
    {
        $this->authorize('create', Vendor::class);

        return view('vendors.create');
    }

    /**
     * Display the specified vendor
     */
    public function show(Vendor $vendor)
    {
        $this->authorize('view', $vendor);

        // Don't eager load - let Livewire handle pagination
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified vendor
     */
    public function edit(Vendor $vendor)
    {
        $this->authorize('update', $vendor);

        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Remove the specified vendor from storage
     */
    public function destroy(Vendor $vendor)
    {
        $this->authorize('delete', $vendor);

        // Check if vendor has any maintenance requests
        if ($vendor->maintenanceRequests()->count() > 0) {
            return back()->with('error', 'Cannot delete vendor with existing maintenance requests. Deactivate the vendor instead.');
        }

        $vendorName = $vendor->name;
        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('message', "Vendor '{$vendorName}' has been permanently deleted.");
    }

    /**
     * Display the vendor dashboard
     */
    public function dashboard()
    {
        // Get vendor record linked to current user
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(403, 'No vendor profile associated with this account.');
        }

        // Get request counts for metrics
        $assignedCount = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
            ->where('status', 'assigned')
            ->count();

        $inProgressCount = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
            ->where('status', 'in_progress')
            ->count();

        $completedCount = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
            ->where('status', 'completed')
            ->count();

        return view('vendors.dashboard', compact(
            'vendor',
            'assignedCount',
            'inProgressCount',
            'completedCount'
        ));
    }

    /**
     * Display list of assigned maintenance requests
     */
    public function requests()
    {
        return view('vendors.requests.index');
    }

    /**
     * Display a specific maintenance request for vendor's view
     */
    public function vendorShow(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('viewAsVendor', $maintenanceRequest);

        return view('vendors.vendorShow', compact('maintenanceRequest'));
    }
}
