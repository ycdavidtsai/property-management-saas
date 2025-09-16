<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('organization');
    }

    /**
     * Display the specified unit
     */
    public function show(Unit $unit)
    {
        // Ensure the unit belongs to the current organization
        if ($unit->property->organization_id !== Auth::user()->organization_id) {
            abort(403, 'Unauthorized access to unit.');
        }

        // Check permissions
        if (!RoleService::roleHasPermission(Auth::user()->role, 'units.view')) {
            abort(403, 'You do not have permission to view units.');
        }

        // Load relationships
        $unit->load([
            'property',
            'leases' => function ($query) {
                $query->with('tenants')->orderBy('created_at', 'desc');
            }
        ]);


    // Add this debug line
    //dd('Controller reached', $unit->toArray());

        return view('units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified unit
     */
    public function edit(Unit $unit)
    {
        // Ensure the unit belongs to the current organization
        if ($unit->property->organization_id !== Auth::user()->organization_id) {
            abort(403, 'Unauthorized access to unit.');
        }

        // Check permissions
        if (!RoleService::roleHasPermission(Auth::user()->role, 'units.edit')) {
            abort(403, 'You do not have permission to edit units.');
        }

        $unit->load('property');

        return view('units.edit', compact('unit'));
    }
}