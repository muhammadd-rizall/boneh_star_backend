<?php

namespace App\Policies;

use App\Models\Buses;
use App\Models\User;

class BusesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('buses.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Buses $buses): bool
    {
        return $user->hasPermissionTo('buses.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('buses.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Buses $buses): bool
    {
        return $user->hasPermissionTo('buses.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Buses $buses): bool
    {
        return $user->hasPermissionTo('buses.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Buses $buses): bool
    {
        return $user->hasPermissionTo('buses.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Buses $buses): bool
    {
        return $user->hasPermissionTo('buses.forceDelete');
    }
}
