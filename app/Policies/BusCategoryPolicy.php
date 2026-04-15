<?php

namespace App\Policies;

use App\Models\BusCategory;
use App\Models\User;

class BusCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('categories.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BusCategory $busCategory): bool
    {
        return $user->hasPermissionTo('categories.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('categories.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BusCategory $busCategory): bool
    {
        return $user->hasPermissionTo('categories.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BusCategory $busCategory): bool
    {
        return $user->hasPermissionTo('categories.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BusCategory $busCategory): bool
    {
        return $user->hasPermissionTo('categories.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BusCategory $busCategory): bool
    {
        return $user->hasPermissionTo('categories.forceDelete');
    }
}
