<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Supports: middleware('role:admin,manager,landlord')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized access.');
        }

        // Flatten all roles into a single array
        $allowedRoles = [];
        foreach ($roles as $role) {
            if (is_string($role)) {
                // Split comma-separated roles
                foreach (explode(',', $role) as $r) {
                    $allowedRoles[] = trim($r);
                }
            }
        }

        // logger()->info('CheckRole middleware', [
        //     'user_role' => $request->user()->role,
        //     'allowed_roles' => $allowedRoles,
        // ]);

        if (!in_array($request->user()->role, $allowedRoles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
