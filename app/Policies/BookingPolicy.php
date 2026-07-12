<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('bookings.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ?Booking $booking = null): bool
    {
        return $user->hasPermissionTo('bookings.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('bookings.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ?Booking $booking = null): bool
    {
        return $user->hasPermissionTo('bookings.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ?Booking $booking = null): bool
    {
        return $user->hasPermissionTo('bookings.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ?Booking $booking = null): bool
    {
        return $user->hasPermissionTo('bookings.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ?Booking $booking = null): bool
    {
        return $user->hasPermissionTo('bookings.forceDelete');
    }
}
