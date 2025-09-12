<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
