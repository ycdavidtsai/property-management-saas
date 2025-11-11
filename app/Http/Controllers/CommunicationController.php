<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunicationController extends Controller
{
    /**
     * Show the communication index page
     */
    public function index()
    {
        return view('communications.index');
    }

    /**
     * Show the broadcast composer page
     */
    public function compose()
    {
        // Only landlords and managers can send broadcasts
        $user = Auth::user();

        if (!in_array($user->role, ['landlord', 'manager', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        return view('communications.compose');
    }

    /**
     * Show broadcast history
     */
    public function history()
    {
        // Only landlords and managers can view broadcast history
        $user = Auth::user();

        if (!in_array($user->role, ['landlord', 'manager', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        return view('communications.history');
    }

    /**
     * Show notification center (for all users)
     */
    public function notifications()
    {
        return view('communications.notifications');
    }
}
