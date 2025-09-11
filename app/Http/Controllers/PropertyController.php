<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'organization']);
    }

    public function index(): View
    {
        return view('properties.index');
    }

    public function create(): View
    {
        return view('properties.create');
    }

    public function show(Property $property): View
    {
        // Ensure the property belongs to the current organization
        if ($property->organization_id !== session('current_organization_id')) {
            abort(403);
        }

        return view('properties.show', compact('property'));
    }

    public function edit(Property $property): View
    {
        // Ensure the property belongs to the current organization
        if ($property->organization_id !== session('current_organization_id')) {
            abort(403);
        }

        return view('properties.edit', compact('property'));
    }

    public function store(Request $request)
    {
        // This method can be used as fallback for regular form submission
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'type' => 'required|in:single_family,multi_family,apartment,commercial',
            'total_units' => 'required|integer|min:1',
        ]);

        Property::create([
            'organization_id' => session('current_organization_id'),
            'name' => $request->name,
            'address' => $request->address,
            'type' => $request->type,
            'total_units' => $request->total_units,
        ]);

        return redirect()->route('properties.index')->with('message', 'Property created successfully!');
    }
}
