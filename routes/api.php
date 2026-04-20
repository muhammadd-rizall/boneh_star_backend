<?php

use App\Http\Controllers\Api\Admin\ActivityTypeController;
use App\Http\Controllers\Api\Admin\BookingController;
use App\Http\Controllers\Api\Admin\BusCategoryController;
use App\Http\Controllers\Api\Admin\BusesController;
use App\Http\Controllers\Api\Admin\BusFacilitiesController;
use App\Http\Controllers\Api\Admin\BusImagesController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\DriverController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\PaymentMethodController;
use App\Http\Controllers\Api\Admin\QuotationController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\RouteTypeController;
use App\Http\Controllers\Api\Admin\TransactionController;
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
            Route::get('/permissions', [RoleController::class, 'allPermissions'])->name('permissions.all');
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

        // Bus Categories management routes
        Route::prefix('bus-categories')->name('bus-categories.')->group(function () {
            Route::get('/trashed', [BusCategoryController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [BusCategoryController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [BusCategoryController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('bus-categories', BusCategoryController::class);

        // Bus Facilities management routes
        Route::prefix('bus-facilities')->name('bus-facilities.')->group(function () {
            Route::get('/trashed', [BusFacilitiesController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [BusFacilitiesController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [BusFacilitiesController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('bus-facilities', BusFacilitiesController::class);

        // Buses management routes
        Route::prefix('buses')->name('buses.')->group(function () {
            Route::get('/trashed', [BusesController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [BusesController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [BusesController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('buses', BusesController::class);

        // Bus Images management routes
        Route::prefix('bus-images')->name('bus-images.')->group(function () {
            Route::post('/', [BusImagesController::class, 'store'])->name('store');
            Route::patch('/{id}', [BusImagesController::class, 'update'])->name('update');
            Route::delete('/{id}', [BusImagesController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/set-primary', [BusImagesController::class, 'setPrimary'])->name('set-primary');
        });

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

        // Activity Type management routes
        Route::prefix('activity-types')->name('activity-types.')->group(function () {
            Route::get('/trashed', [ActivityTypeController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [ActivityTypeController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [ActivityTypeController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('activity-types', ActivityTypeController::class);

        // Route Type management routes
        Route::prefix('route-types')->name('route-types.')->group(function () {
            Route::get('/trashed', [RouteTypeController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [RouteTypeController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [RouteTypeController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('route-types', RouteTypeController::class);

        // Quotation management routes
        Route::prefix('quotations')->name('quotations.')->group(function () {
            Route::get('/trashed', [QuotationController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [QuotationController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [QuotationController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('quotations', QuotationController::class);

        // Transaction management routes
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/trashed', [TransactionController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [TransactionController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [TransactionController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('transactions', TransactionController::class);

        // Payment Method management routes
        Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/active', [PaymentMethodController::class, 'active'])->name('active');
            Route::get('/trashed', [PaymentMethodController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [PaymentMethodController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [PaymentMethodController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('payment-methods', PaymentMethodController::class);

        // Payment management routes
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::post('/{id}/verify', [PaymentController::class, 'verify'])->name('verify');
            Route::get('/trashed', [PaymentController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [PaymentController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [PaymentController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('payments', PaymentController::class);

    });
});
