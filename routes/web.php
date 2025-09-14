<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LeaseController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/home', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'organization'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Property Management Routes
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/', [PropertyController::class, 'index'])->name('index');
        Route::get('/create', [PropertyController::class, 'create'])->name('create');
        Route::get('/{property}', [PropertyController::class, 'show'])->name('show');
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('edit');
        Route::post('/', [PropertyController::class, 'store'])->name('store');
    });

    // Tenant Management Routes
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [TenantController::class, 'index'])->name('index');
        Route::get('/create', [TenantController::class, 'create'])->name('create');
        Route::get('/{tenant}', [TenantController::class, 'show'])->name('show');
        Route::get('/{tenant}/edit', [TenantController::class, 'edit'])->name('edit');
    });

    // Tenant Portal Route (optional - add to tenant routes group if you prefer)
    Route::get('/tenant/portal', [TenantController::class, 'portal'])->name('tenant.portal');

    // Lease Management Routes (following your exact pattern)
    Route::prefix('leases')->name('leases.')->group(function () {
        Route::get('/', [LeaseController::class, 'index'])->name('index');
        Route::get('/create', [LeaseController::class, 'create'])->name('create');
        Route::get('/{lease}', [LeaseController::class, 'show'])->name('show');
        Route::get('/{lease}/edit', [LeaseController::class, 'edit'])->name('edit');
    });


    });

require __DIR__.'/auth.php';
