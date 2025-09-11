<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'organization']);
    }

    public function index(): View
    {
        return view('dashboard');
    }

    public function home(): View
    {
        return view('welcome');
    }
}
