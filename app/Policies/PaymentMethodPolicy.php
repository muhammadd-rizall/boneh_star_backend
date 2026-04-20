<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(config('payment_methods.view'));
    }

    public function view(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->hasPermissionTo(config('payment_methods.view'));
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(config('payment_methods.create'));
    }

    public function update(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->hasPermissionTo(config('payment_methods.update'));
    }

    public function delete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->hasPermissionTo(config('payment_methods.delete'));
    }

    public function restore(User $user): bool
    {
        return $user->hasPermissionTo(config('payment_methods.restore'));
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo(config('payment_methods.forceDelete'));
    }
}
