<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $organizationId = $request->route('organization') ?? session('current_organization_id') ?? $user->organization_id;

        if (!$user->belongsToOrganization($organizationId)) {
            abort(403, 'Unauthorized access to organization.');
        }

        session(['current_organization_id' => $organizationId]);

        // Debug log
        logger('Organization middleware - setting session org ID: ' . $organizationId);

        return $next($request);
    }
}
