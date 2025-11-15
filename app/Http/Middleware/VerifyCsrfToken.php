<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhooks/*', // Exclude all webhook routes from CSRF
        'webhooks/twilio/status',
        'webhooks/twilio/*',
        'webhooks/postmark/*',
    ];
}
