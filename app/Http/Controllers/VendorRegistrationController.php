<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorRegistrationController extends Controller
{
    /**
     * Show the vendor self-registration form
     */
    public function showRegistrationForm()
    {
        return view('vendor-register.register');
    }

    /**
     * Show registration success/pending approval page
     */
    public function pending()
    {
        return view('vendor-register.pending');
    }

    /**
     * Show rejection page (when vendor clicks link after rejection)
     */
    public function rejected(Request $request)
    {
        $token = $request->query('token');
        $vendor = null;

        if ($token) {
            $vendor = Vendor::where('invitation_token', $token)
                ->where('setup_status', 'rejected')
                ->first();
        }

        return view('vendor-register.rejected', compact('vendor'));
    }
}
