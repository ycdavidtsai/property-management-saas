<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MaintenanceRequest;
use App\Models\VendorPromotionRequest;
use App\Models\VendorInvoice;


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
        // ✨ NEW: Check if vendor can be edited
        if ($vendor->isManagedByUser()) {
            return redirect()->route('vendors.show', $vendor)
                ->with('warning', 'This vendor has a user account and manages their own profile. You can only view their information.');
        }

        $this->authorize('update', $vendor);

        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        // ✨ NEW: Prevent editing vendor with user account
        if ($vendor->isManagedByUser()) {
            return redirect()->route('vendors.show', $vendor)
                ->with('error', 'Cannot edit vendor with a user account. They manage their own profile.');
        }

        // Existing update logic
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'services' => 'nullable|array',
            // ... other fields
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.show', $vendor)
            ->with('success', 'Vendor updated successfully!');
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
     * Display list of all vendors
     */
    public function requests()
    {
        $this->authorize('viewAny', Vendor::class);
        return view('vendors.index');
    }

    /**
     * Display a specific maintenance request for vendor's view
     */
    public function vendorShow(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('viewAsVendor', $maintenanceRequest);

        return view('vendors.vendorShow', compact('maintenanceRequest'));
    }

    /**
     * Display vendor profile
     */
    public function profile()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(403, 'No vendor profile associated with this account.');
        }

        // Get any pending promotion request
        $pendingRequest = $vendor->promotionRequests()
            ->where('status', 'pending')
            ->first();

        return view('vendors.profile', compact('vendor', 'pendingRequest'));
    }

    /**
     * Request promotion to global
     */
    public function requestPromotion(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(403, 'No vendor profile associated with this account.');
        }

        // Validate
        if ($vendor->isGlobal()) {
            return back()->with('error', 'Your vendor profile is already listed globally.');
        }

        // Check for existing pending request
        $existingRequest = $vendor->promotionRequests()
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('error', 'You already have a pending promotion request.');
        }

        // Validate request
        $validated = $request->validate([
            'request_message' => 'nullable|string|max:1000',
        ]);

        // Create promotion request
        VendorPromotionRequest::create([
            'vendor_id' => $vendor->id,
            'requested_by_user_id' => Auth::user()->id,
            'request_message' => $validated['request_message'] ?? null,
            'requested_at' => now(),
            'status' => 'pending',
            'fee_amount' => 0, // Free for now
            'payment_status' => 'waived',
        ]);

        // TODO: Notify admin

        return back()->with('success', 'Your promotion request has been submitted. An administrator will review it shortly.');
    }

    public function browseGlobal()
    {
        $user = Auth::user();

        // Get global vendors NOT yet in this organization's list
        $availableVendors = Vendor::global()
            ->whereDoesntHave('organizations', function($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })
            ->orderBy('name')
            ->paginate(20);

        // Get global vendors ALREADY in this organization's list
        $myGlobalVendors = Vendor::global()
            ->whereHas('organizations', function($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })
            ->orderBy('name')
            ->get();

        return view('vendors.browse-global', compact('availableVendors', 'myGlobalVendors'));
    }

    public function addToMyVendors(Vendor $vendor)
    {
        $user = Auth::user();

        // Verify it's a global vendor
        if (!$vendor->isGlobal()) {
            return back()->with('error', 'Only global vendors can be added.');
        }

        // Add to organization's vendor list
        $vendor->organizations()->syncWithoutDetaching([$user->organization_id => [
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]]);

        return back()->with('success', "{$vendor->name} has been added to your vendors.");
    }

    public function removeFromMyVendors(Vendor $vendor)
    {
        $user = Auth::user();

        // Check if vendor has any active maintenance requests
        $activeRequests = $vendor->maintenanceRequests()
            ->where('organization_id', $user->organization_id)
            ->whereIn('status', ['pending', 'assigned', 'in_progress'])
            ->count();

        if ($activeRequests > 0) {
            return back()->with('error', "Cannot remove {$vendor->name} - they have {$activeRequests} active maintenance request(s).");
        }

        // Remove from organization's vendor list
        $vendor->organizations()->detach($user->organization_id);

        return back()->with('success', "{$vendor->name} has been removed from your vendors.");
    }

    // Vendor Additional Methods, phase 4
    /**
     * Display the vendor calendar view
     */
    public function calendar()
    {
        $vendor = Vendor::where('user_id', Auth::id())->first();

        if (!$vendor) {
            return redirect()->route('vendor.dashboard')
                ->with('error', 'Vendor profile not found.');
        }

        // Only active vendors can access calendar
        if ($vendor->setup_status !== 'active') {
            return redirect()->route('vendor.dashboard')
                ->with('error', 'Your account must be active to access the calendar.');
        }

        return view('vendors.calendar', [
            'vendor' => $vendor
        ]);
    }

    /**
     * Show vendor earnings summary
     */
    public function earnings()
    {
        return view('vendors.earnings');
    }

    /**
     * Show vendor invoices list
     */
    public function invoices()
    {
        return view('vendors.invoices.index');
    }

    /**
     * Show single invoice detail
     */
    public function showInvoice(VendorInvoice $invoice)
    {
        // Verify vendor owns this invoice
        $vendor = Vendor::where('user_id', Auth::id())->firstOrFail();

        if ($invoice->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized');
        }

        return view('vendors.invoices.show', compact('invoice'));
    }

    /**
     * Show vendor portfolio page
     */
    public function portfolio()
    {
        return view('vendors.portfolio');
    }

    /**
     * Show availability settings page
     */
    public function availability()
    {
        return view('vendors.settings.availability');
    }

    /**
     * Show service areas settings page
     */
    public function serviceAreas()
    {
        return view('vendors.settings.service-areas');
    }
}
