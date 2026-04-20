<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(config('payments.view'));
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo(config('payments.view'));
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(config('payments.create'));
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo(config('payments.update'));
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasPermissionTo(config('payments.delete'));
    }

    public function verify(User $user): bool
    {
        return $user->hasPermissionTo(config('payments.verify'));
    }

    public function restore(User $user): bool
    {
        return $user->hasPermissionTo(config('payments.restore'));
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo(config('payments.forceDelete'));
    }
}
