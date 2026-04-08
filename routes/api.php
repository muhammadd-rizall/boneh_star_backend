<?php

use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {

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
        
    });

});
