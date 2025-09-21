<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\MaintenanceRequest;
use App\Policies\MaintenanceRequestPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        MaintenanceRequest::class => MaintenanceRequestPolicy::class,
    ];
}
