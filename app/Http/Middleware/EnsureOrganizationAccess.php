<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
//use App\Services\RoleService;

class EnsureOrganizationAccess
{
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Organization Access Check (existing logic)
            $organizationId = $user->organization_id ?? session('current_organization_id');

            logger()->info("User {$user->id} accessing organization {$organizationId}");

            if (!$user->belongsToOrganization($organizationId)) {
                abort(403, 'Access denied to this organization.');
            }

            session(['current_organization_id' => $organizationId]);
            
        // 2. User Role Check (found role= tenant, redirect, by pass default to dashboard), 
        // check Auth/AuthenticatedSessionController.php store() method for login redirect logic

            // if ($user->role === 'tenant' && !$request->routeIs('tenant.portal')) {
            //     return redirect()->route('tenant.portal');
            // }

        return $next($request);
    }
}
