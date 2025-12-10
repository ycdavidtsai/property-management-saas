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
     * Supports multiple roles: middleware('role:admin,manager,landlord')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $roles  Comma-separated list of allowed roles
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized access.');
        }

        // Split comma-separated roles into array
        $allowedRoles = array_map('trim', explode(',', $roles));

        // Check if user has any of the allowed roles
        if (!in_array($request->user()->role, $allowedRoles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}

// Register this middleware in bootstrap/app.php:
// ->withMiddleware(function (Middleware $middleware) {
//     $middleware->alias([
//         'role' => \App\Http\Middleware\CheckRole::class,
//     ]);
// })
