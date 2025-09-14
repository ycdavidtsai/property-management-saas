<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'organization']);
    }

    public function index(): View
    {
        return view('dashboard', [
            // Your existing data...
            'showLeaseMetrics' => RoleService::canViewLeases(Auth::user()->role),
        ]);
    }

    public function home(): View
    {
        return view('welcome');
    }
}
