<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;



Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/home', function () {
    return view('welcome');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/promotion-requests', [AdminController::class, 'promotionRequests'])->name('promotion-requests');
        Route::post('/promotion-requests/{promotionRequest}/approve', [AdminController::class, 'approvePromotion'])->name('approve-promotion');
        Route::post('/promotion-requests/{promotionRequest}/reject', [AdminController::class, 'rejectPromotion'])->name('reject-promotion');
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

    // Unit Management Routes (nested under properties for context)
    Route::prefix('units')->name('units.')->group(function () {
        Route::get('/{unit}', [UnitController::class, 'show'])->name('show');
        Route::get('/{unit}/edit', [UnitController::class, 'edit'])->name('edit');
    });

    // Route::middleware(['auth', 'organization'])->group(function () {
    //     Route::resource('maintenance-requests', MaintenanceRequestController::class);
    // });

    // Vendor Management Routes, note form AI: The /{vendor} route is catching /browse-global and treating "browse-global" as a vendor ID!

    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('index');
        Route::get('/create', [VendorController::class, 'create'])->name('create');

        // ✅ Browse Global Vendors - BEFORE {vendor} wildcard
        Route::get('/browse-global', [VendorController::class, 'browseGlobal'])->name('browse-global');
        Route::post('/{vendor}/add-to-my-vendors', [VendorController::class, 'addToMyVendors'])->name('add-to-my-vendors');
        Route::delete('/{vendor}/remove-from-my-vendors', [VendorController::class, 'removeFromMyVendors'])->name('remove-from-my-vendors');

        // ✅ Wildcard routes LAST
        Route::get('/{vendor}', [VendorController::class, 'show'])->name('show');
        Route::get('/{vendor}/edit', [VendorController::class, 'edit'])->name('edit');
        Route::delete('/{vendor}', [VendorController::class, 'destroy'])->name('destroy');
    });

});

Route::middleware(['auth', 'verified'])->group(function () {
    // Maintenance Request Routes
    Route::prefix('maintenance-requests')->name('maintenance-requests.')->group(function () {
        Route::get('/', [MaintenanceRequestController::class, 'index'])->name('index');
        Route::get('/create', [MaintenanceRequestController::class, 'create'])->name('create');
        Route::get('/{maintenanceRequest}', [MaintenanceRequestController::class, 'show'])->name('show');
        Route::get('/{maintenanceRequest}/edit', [MaintenanceRequestController::class, 'edit'])->name('edit');
        Route::put('/{maintenanceRequest}', [MaintenanceRequestController::class, 'update'])->name('update');
        Route::delete('/{maintenanceRequest}', [MaintenanceRequestController::class, 'destroy'])->name('destroy');
    });
});

// Vendor Portal Routes
Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'verified'])->group(function () {
        // Check if user has vendor role
        Route::middleware('role:vendor')->group(function () {
            Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
            Route::get('/requests', [VendorController::class, 'requests'])->name('requests.index');
            Route::get('/requests/{maintenanceRequest}', [VendorController::class, 'vendorShow'])->name('requests.show');

            // Vendor Profile & Promotion
            Route::get('/profile', [VendorController::class, 'profile'])->name('profile');
            Route::post('/request-promotion', [VendorController::class, 'requestPromotion'])->name('request-promotion');
        });
});

// Temporary test routes - remove in production
Route::middleware(['auth'])->prefix('test')->group(function () {
    Route::get('/notification/single', [App\Http\Controllers\TestNotificationController::class, 'testSingle']);
    Route::get('/notification/broadcast', [App\Http\Controllers\TestNotificationController::class, 'testBroadcast']);
    Route::post('/notification/preview', [App\Http\Controllers\TestNotificationController::class, 'previewRecipients']);
});

require __DIR__.'/auth.php';
