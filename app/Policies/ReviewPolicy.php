<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(config('reviews.view'));
    }

    public function view(User $user, Review $review): bool
    {
        return $user->hasPermissionTo(config('reviews.view'));
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(config('reviews.create'));
    }

    public function update(User $user, Review $review): bool
    {
        return $user->hasPermissionTo(config('reviews.update'));
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->hasPermissionTo(config('reviews.delete'));
    }

    public function restore(User $user): bool
    {
        return $user->hasPermissionTo(config('reviews.restore'));
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo(config('reviews.forceDelete'));
    }

    public function publish(User $user): bool
    {
        return $user->hasPermissionTo(config('reviews.publish'));
    }
}
