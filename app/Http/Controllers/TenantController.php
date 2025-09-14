<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function index()
    {
        return view('tenants.index');
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function show(User $tenant)
    {
        // Ensure tenant belongs to current organization
        if ($tenant->organization_id !== session('current_organization_id')) {
            abort(404);
        }

        return view('tenants.show', compact('tenant'));
    }

    public function edit(User $tenant)
    {
        // Ensure tenant belongs to current organization
        if ($tenant->organization_id !== session('current_organization_id')) {
            abort(404);
        }

        return view('tenants.edit', compact('tenant'));
    }

    /**
     * Display tenant portal (tenant's lease information)
     */
    public function portal()
    {
        // Only for tenant users
        if (Auth::user()->role !== 'tenant') {
            abort(403, 'Access denied. This portal is for tenants only.');
        }

        return view('tenants.portal');
    }
}
