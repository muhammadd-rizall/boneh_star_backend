<?php

use App\Http\Controllers\Api\Admin\ActivityTypeController;
use App\Http\Controllers\Api\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Api\Admin\BookingReportController;
use App\Http\Controllers\Api\Admin\BusAvailabilityReportController;
use App\Http\Controllers\Api\Admin\BusCategoryController;
use App\Http\Controllers\Api\Admin\BusesController;
use App\Http\Controllers\Api\Admin\BusFacilitiesController;
use App\Http\Controllers\Api\Admin\BusImagesController;
use App\Http\Controllers\Api\Admin\CompanyProfileController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\DriverController;
use App\Http\Controllers\Api\Admin\IncomeReportController;
use App\Http\Controllers\Api\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\PaymentMethodController;
use App\Http\Controllers\Api\Admin\QuotationController as AdminQuotationController;
use App\Http\Controllers\Api\Admin\ReviewReportController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\RouteTypeController;
use App\Http\Controllers\Api\Admin\TransactionController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Auth\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Api\Auth\Admin\LogoutController as AdminLogoutController;
use App\Http\Controllers\Api\Auth\Public\LoginController as PublicLoginController;
use App\Http\Controllers\Api\Auth\Public\LogoutController as PublicLogoutController;
use App\Http\Controllers\Api\Auth\Public\RegisterController as PublicRegisterController;
use App\Http\Controllers\Api\Customer\BookingController;
use App\Http\Controllers\Api\Customer\HistoryController;
use App\Http\Controllers\Api\Customer\NotificationController;
use App\Http\Controllers\Api\Customer\QuotationController;
use App\Http\Controllers\Api\Customer\ReviewController;
use App\Http\Controllers\Api\Customer\SettingController;
use App\Http\Controllers\Api\Public\PublicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Data routes
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/company-profile', [PublicController::class, 'companyProfile'])->name('company-profile');
    Route::get('/buses', [PublicController::class, 'buses'])->name('buses.index');
    Route::get('/buses/{id}', [PublicController::class, 'busDetail'])->name('buses.show');
    Route::get('/facilities', [PublicController::class, 'facilities'])->name('facilities');
    Route::get('/reviews', [PublicController::class, 'reviews'])->name('reviews');
    Route::get('/activity-types', [PublicController::class, 'activityTypes'])->name('activity-types');
    Route::get('/route-types', [PublicController::class, 'routeTypes'])->name('route-types');
    Route::get('/bus-categories', [PublicController::class, 'busCategories'])->name('bus-categories');
});

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

    // Customer routes
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/profile', [SettingController::class, 'show'])->name('profile.show');
        Route::patch('/profile', [SettingController::class, 'update'])->name('profile.update');
        Route::post('/profile/picture', [SettingController::class, 'uploadProfilePicture'])->name('profile.picture');
        Route::get('/statistics', [SettingController::class, 'statistics'])->name('statistics');

        // Booking routes
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::post('/', [BookingController::class, 'store'])->name('store');
            Route::get('/payment-methods', [BookingController::class, 'paymentMethods'])->name('payment-methods');
            Route::get('/{id}', [BookingController::class, 'show'])->name('show');
            Route::post('/{id}/upload-payment', [BookingController::class, 'uploadPayment'])->name('upload-payment');
            Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        });

        // Quotation routes
        Route::prefix('quotations')->name('quotations.')->group(function () {
            Route::get('/{id}', [QuotationController::class, 'show'])->name('show');
            Route::post('/{id}/accept', [QuotationController::class, 'accept'])->name('accept');
            Route::post('/{id}/request-revision', [QuotationController::class, 'requestRevision'])->name('request-revision');
        });

        // History and Tracking routes
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('/', [HistoryController::class, 'index'])->name('index');
            Route::get('/{id}', [HistoryController::class, 'show'])->name('show');
            Route::get('/{id}/payment-status', [HistoryController::class, 'paymentStatus'])->name('payment-status');
            Route::get('/{id}/invoice', [HistoryController::class, 'downloadInvoice'])->name('invoice');
        });

        // Notification routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
            Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        });

        // Review routes
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::post('/', [ReviewController::class, 'store'])->name('store');
            Route::get('/{id}', [ReviewController::class, 'show'])->name('show');
            Route::patch('/{id}', [ReviewController::class, 'update'])->name('update');
        });
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

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
            Route::get('/trashed', [AdminBookingController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [AdminBookingController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [AdminBookingController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('bookings', AdminBookingController::class);

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
            Route::get('/trashed', [AdminQuotationController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [AdminQuotationController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [AdminQuotationController::class, 'forceDelete'])->name('force-delete');
        });
        Route::apiResource('quotations', AdminQuotationController::class);

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

        // Notification management routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::post('/mark-all-as-read', [AdminNotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
            Route::post('/{id}/mark-as-read', [AdminNotificationController::class, 'markAsRead'])->name('mark-as-read');
        });
        Route::apiResource('notifications', AdminNotificationController::class)->only(['index', 'destroy']);

        // Report routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/bookings', [BookingReportController::class, 'index'])->name('bookings');
            Route::get('/bookings/excel', [BookingReportController::class, 'exportExcel'])->name('bookings.excel');
            Route::get('/bookings/pdf', [BookingReportController::class, 'exportPdf'])->name('bookings.pdf');

            Route::get('/income', [IncomeReportController::class, 'index'])->name('income');
            Route::get('/income/excel', [IncomeReportController::class, 'exportExcel'])->name('income.excel');
            Route::get('/income/pdf', [IncomeReportController::class, 'exportPdf'])->name('income.pdf');

            Route::get('/bus-availability', [BusAvailabilityReportController::class, 'index'])->name('bus-availability');

            // Consolidated Review routes in reports
            Route::prefix('reviews')->name('reviews.')->group(function () {
                Route::get('/', [ReviewReportController::class, 'index'])->name('index');
                Route::post('/{id}/toggle-publish', [ReviewReportController::class, 'togglePublish'])->name('toggle-publish');
                Route::delete('/{id}', [ReviewReportController::class, 'destroy'])->name('destroy');
            });
        });

        // Company Profile routes
        Route::get('/company-profile', [CompanyProfileController::class, 'index'])->name('company-profile.index');
        Route::post('/company-profile', [CompanyProfileController::class, 'store'])->name('company-profile.store');
        Route::delete('/company-profile', [CompanyProfileController::class, 'destroy'])->name('company-profile.destroy');

    });
});
