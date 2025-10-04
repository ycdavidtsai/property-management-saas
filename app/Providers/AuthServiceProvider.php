<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\MaintenanceRequest;
use App\Policies\MaintenanceRequestPolicy;
use App\Models\Vendor;
use App\Policies\VendorPolicy; 

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        MaintenanceRequest::class => MaintenanceRequestPolicy::class,
        \App\Models\MaintenanceRequest::class => \App\Policies\MaintenanceRequestPolicy::class,
        Vendor::class => VendorPolicy::class,
    ];
}
