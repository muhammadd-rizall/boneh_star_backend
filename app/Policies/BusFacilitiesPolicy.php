<?php

namespace App\Policies;

use App\Models\BusFacilities;
use App\Models\User;

class BusFacilitiesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('facilities.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ?BusFacilities $busFacilities = null): bool
    {
        return $user->hasPermissionTo('facilities.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('facilities.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ?BusFacilities $busFacilities = null): bool
    {
        return $user->hasPermissionTo('facilities.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ?BusFacilities $busFacilities = null): bool
    {
        return $user->hasPermissionTo('facilities.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ?BusFacilities $busFacilities = null): bool
    {
        return $user->hasPermissionTo('facilities.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ?BusFacilities $busFacilities = null): bool
    {
        return $user->hasPermissionTo('facilities.forceDelete');
    }
}
