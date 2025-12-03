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
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;



Route::get('/', function () {
    // return redirect()->route('dashboard');
    return view('welcome');
});

Route::get('/home', function () {
    return view('welcome');
});

// =====================
// ADMIN ROUTES (Site Administrator)
// =====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Organization Management
    Route::get('/organizations', [AdminController::class, 'organizations'])->name('organizations.index');
    Route::get('/organizations/{organization}', [AdminController::class, 'showOrganization'])->name('organizations.show');
    Route::post('/organizations/{organization}/toggle', [AdminController::class, 'toggleOrganization'])->name('organizations.toggle');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');

    // Vendor Oversight
    Route::get('/vendors', [AdminController::class, 'vendors'])->name('vendors.index');

    // Promotion Requests (existing)
    Route::get('/promotion-requests', [AdminController::class, 'promotionRequests'])->name('promotion-requests');
    Route::post('/promotion-requests/{promotionRequest}/approve', [AdminController::class, 'approvePromotion'])->name('approve-promotion');
    Route::post('/promotion-requests/{promotionRequest}/reject', [AdminController::class, 'rejectPromotion'])->name('reject-promotion');

    // System Health
    Route::get('/system', [AdminController::class, 'system'])->name('system');
    Route::post('/system/jobs/{jobId}/retry', [AdminController::class, 'retryJob'])->name('system.retry-job');
    Route::delete('/system/jobs/{jobId}', [AdminController::class, 'deleteJob'])->name('system.delete-job');
    Route::post('/system/jobs/flush', [AdminController::class, 'flushJobs'])->name('system.flush-jobs');
});

// =====================
// MAIN APP ROUTES (Organization Users)
// =====================
Route::middleware(['auth', 'verified', 'organization'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notification Preferences
    Route::get('/profile/notification-preferences', [ProfileController::class, 'editNotificationPreferences'])->name('profile.notification-preferences');

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

    // Vendor Management Routes
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('index');
        Route::get('/create', [VendorController::class, 'create'])->name('create');

        // Browse Global Vendors - BEFORE {vendor} wildcard
        Route::get('/browse-global', [VendorController::class, 'browseGlobal'])->name('browse-global');
        Route::post('/{vendor}/add-to-my-vendors', [VendorController::class, 'addToMyVendors'])->name('add-to-my-vendors');
        Route::delete('/{vendor}/remove-from-my-vendors', [VendorController::class, 'removeFromMyVendors'])->name('remove-from-my-vendors');

        // Wildcard routes LAST
        Route::get('/{vendor}', [VendorController::class, 'show'])->name('show');
        Route::get('/{vendor}/edit', [VendorController::class, 'edit'])->name('edit');
        Route::delete('/{vendor}', [VendorController::class, 'destroy'])->name('destroy');
    });

    // Communication Routes (NEW)
    Route::prefix('communications')->name('communications.')->group(function () {
        Route::get('/', [CommunicationController::class, 'index'])->name('index');

        // Broadcast routes (landlord/manager only)
        Route::middleware('role:landlord,manager,admin')->group(function () {
            Route::get('/compose', [CommunicationController::class, 'compose'])->name('compose');
            Route::get('/history', [CommunicationController::class, 'history'])->name('history');
        });

        // Notification routes (all authenticated users)
        Route::get('/notifications', [CommunicationController::class, 'notifications'])->name('notifications');
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
    Route::get('/notification/sms', [App\Http\Controllers\TestNotificationController::class, 'testSms']);
    Route::post('/notification/preview', [App\Http\Controllers\TestNotificationController::class, 'previewRecipients']);
});

// Webhook Routes (must be outside auth middleware)
// Route::post('/webhooks/twilio/status', [WebhookController::class, 'twilioStatus'])
//     ->name('webhooks.twilio.status')
//     ->withoutMiddleware([
//         \App\Http\Middleware\VerifyCsrfToken::class,
//         \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
//     ]);

require __DIR__.'/auth.php';
