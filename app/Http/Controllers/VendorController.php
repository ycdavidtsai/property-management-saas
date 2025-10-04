<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

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
}
