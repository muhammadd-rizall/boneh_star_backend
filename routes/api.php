<?php

use App\Http\Controllers\Api\Admin\BookingController;
use App\Http\Controllers\Api\Admin\BusesController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\DriverController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Auth\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Api\Auth\Admin\LogoutController as AdminLogoutController;
use App\Http\Controllers\Api\Auth\Public\LoginController as PublicLoginController;
use App\Http\Controllers\Api\Auth\Public\LogoutController as PublicLogoutController;
use App\Http\Controllers\Api\Auth\Public\RegisterController as PublicRegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Public routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [PublicLoginController::class, 'login'])->name('login');
    Route::post('/register', [PublicRegisterController::class, 'register'])->name('register');
});

// Auth Admin routes
Route::prefix('admin/auth')->name('admin.auth.')->group(function () {
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login');
});

Route::middleware(['auth:sanctum'])->group(function () {

    // Auth Logout routes
    Route::post('/auth/logout', [PublicLogoutController::class, 'logout'])->name('auth.logout');
    Route::post('/admin/auth/logout', [AdminLogoutController::class, 'logout'])->name('admin.auth.logout');

    // Get current authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles.permissions');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {

        // User management routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/trashed', [UserController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [UserController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('users', UserController::class);

        // Role management routes
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/trashed', [RoleController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [RoleController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [RoleController::class, 'forceDelete'])->name('force-delete');

            // Permission management for roles
            Route::get('/{role}/permissions', [RoleController::class, 'permissions'])->name('permissions');
            Route::post('/{role}/sync-permissions', [RoleController::class, 'syncPermissions'])->name('sync-permissions');
            Route::post('/{role}/permissions/{permission}/attach', [RoleController::class, 'attachPermission'])->name('attach-permission');
            Route::post('/{role}/permissions/{permission}/detach', [RoleController::class, 'detachPermission'])->name('detach-permission');
        });
        Route::apiResource('roles', RoleController::class);

        // Customer management routes
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/trashed', [CustomerController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [CustomerController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [CustomerController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('customers', CustomerController::class);

        // Buses management routes
        Route::prefix('buses')->name('buses.')->group(function () {
            Route::get('/trashed', [BusesController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [BusesController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [BusesController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('buses', BusesController::class);

        // Driver management routes
        Route::prefix('drivers')->name('drivers.')->group(function () {
            Route::get('/trashed', [DriverController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [DriverController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [DriverController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('drivers', DriverController::class);

        // Booking management routes
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/trashed', [BookingController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [BookingController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [BookingController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('bookings', BookingController::class);

    });
});
