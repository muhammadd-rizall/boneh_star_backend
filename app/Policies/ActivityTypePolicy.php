<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ActivityType;

class ActivityTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('activities.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ?ActivityType $activityType = null): bool
    {
        return $user->hasPermissionTo('activities.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('activities.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ?ActivityType $activityType = null): bool
    {
        return $user->hasPermissionTo('activities.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ?ActivityType $activityType = null): bool
    {
        return $user->hasPermissionTo('activities.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ?ActivityType $activityType = null): bool
    {
        return $user->hasPermissionTo('activities.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ?ActivityType $activityType = null): bool
    {
        return $user->hasPermissionTo('activities.forceDelete');
    }
}
