<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaseController extends Controller
{
    /**
     * Display a listing of leases
     */
    public function index()
    {
        // Check permission
        if (!RoleService::canViewLeases(Auth::user()->role)) {
            abort(403, 'You do not have permission to view leases.');
        }

        return view('leases.index');
    }

    /**
     * Show the form for creating a new lease
     */
    public function create()
    {
        // Check permission
        if (!RoleService::canCreateLeases(Auth::user()->role)) {
            abort(403, 'You do not have permission to create leases.');
        }

        return view('leases.create');
    }

    /**
     * Display the specified lease
     */
    public function show($leaseId)
    {
        // Check permission
        if (!RoleService::canViewLeases(Auth::user()->role)) {
            abort(403, 'You do not have permission to view leases.');
        }

        // Verify lease exists and belongs to organization
        $lease = Lease::forOrganization(Auth::user()->organization_id)
            ->findOrFail($leaseId);

        return view('leases.show', compact('lease'));
    }

    /**
     * Show the form for editing the specified lease
     */
    public function edit($leaseId)
    {
        // Check permission
        if (!RoleService::canEditLeases(Auth::user()->role)) {
            abort(403, 'You do not have permission to edit leases.');
        }

        // Verify lease exists and belongs to organization
        $lease = Lease::forOrganization(Auth::user()->organization_id)
            ->findOrFail($leaseId);

        return view('leases.edit', compact('lease'));
    }
}
