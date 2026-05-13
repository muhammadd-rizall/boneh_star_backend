<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user instanceof User) {
                if ($user->hasRole('super-admin')) {
                    return true;
                }

                if ($user->hasPermissionTo($ability)) {
                    return true;
                }
            }

            return null;
        });

        // Custom password reset URL for customer
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            if ($notifiable instanceof Customer) {
                return env('FRONTEND_URL').'/auth/reset-password?token='.$token.'&email='.$notifiable->getEmailForPasswordReset();
            }

            // Default behavior for other models if needed
            return env('FRONTEND_URL').'/auth/reset-password?token='.$token.'&email='.$notifiable->getEmailForPasswordReset();
        });
    }
}
