<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\MaintenanceRequestController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;



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

    // Unit Management Routes (nested under properties for context)
    Route::prefix('units')->name('units.')->group(function () {
        Route::get('/{unit}', [UnitController::class, 'show'])->name('show');
        Route::get('/{unit}/edit', [UnitController::class, 'edit'])->name('edit');
    });

Route::middleware(['auth', 'organization'])->group(function () {
    Route::resource('maintenance-requests', MaintenanceRequestController::class);
});

    });
// Add to web.php temporarily
Route::get('/test-storage', function() {
    try {
        Storage::disk('public')->put('test-file.txt', 'test content');
        return [
            'storage_works' => true,
            'file_exists' => Storage::disk('public')->exists('test-file.txt'),
            'file_url' => asset('storage/test-file.txt'),
            'full_path' => storage_path('app/public/test-file.txt'),
            'directory_writable' => is_writable(storage_path('app/public')),
        ];
    } catch (\Exception $e) {
        return [
            'storage_works' => false,
            'error' => $e->getMessage(),
            'storage_path' => storage_path('app/public'),
            'exists' => is_dir(storage_path('app/public')),
            'writable' => is_writable(storage_path('app/public')),
        ];
    }
})->middleware('auth');

Route::get('/test-upload', \App\Livewire\TestUpload::class)->middleware('auth');

require __DIR__.'/auth.php';
